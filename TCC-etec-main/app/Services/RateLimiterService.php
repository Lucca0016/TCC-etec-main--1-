<?php

namespace App\Services;

use App\Core\Database;
use PDO;

/**
 * ✅ Serviço centralizado de rate limiting
 * 
 * Consolida dois sistemas de rate limiting em um único e consistente:
 * - Antes: LoginRateLimiter (por fingerprint/IP+UA) vs User.recordAttempt (por user_id)
 * - Agora: Sistema unificado que rastreia ambos para melhor segurança
 */
class RateLimiterService
{
    private PDO $pdo;
    private int $limiteDiariaoPorFingerprint = 10;  // Limite por IP/UA por dia
    private int $limiteDiarioPorUsuario = 20;       // Limite por usuario_id por dia
    private int $limiteHorario = 5;                 // Limite por hora

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::connection();
    }

    /**
     * ✅ Verifica se uma tentativa é permitida (por fingerprint OU usuario_id)
     * 
     * Bloqueia se:
     * - Mesmo fingerprint tem >= 10 tentativas no dia
     * - Mesmo user_id tem >= 20 tentativas no dia
     * - Mesmo fingerprint tem >= 5 tentativas na última hora
     */
    public function isBlocked(?string $fingerprint = null, ?int $userId = null): bool
    {
        // ✅ Bloqueia por fingerprint (previne ataque por força bruta de mesmo IP/UA)
        if ($fingerprint !== null && $this->isBlockedByFingerprint($fingerprint)) {
            return true;
        }

        // ✅ Bloqueia por usuario_id (previne ataque direcionado a usuario específico)
        if ($userId !== null && $userId > 0 && $this->isBlockedByUserId($userId)) {
            return true;
        }

        // ✅ Bloqueia por limite horário (mais agressivo para proteção aguda)
        if ($fingerprint !== null && $this->isBlockedByHourlyLimit($fingerprint)) {
            return true;
        }

        return false;
    }

    /**
     * Verifica se bloqueado por fingerprint (limite diário)
     */
    private function isBlockedByFingerprint(string $fingerprint): bool
    {
        $stmt = $this->pdo->prepare('
            SELECT tentativas FROM tentativas_login 
            WHERE fingerprint = ? AND data = CURDATE() AND tipo = ?
        ');
        $stmt->execute([$fingerprint, 'fingerprint']);
        $attempts = (int)($stmt->fetchColumn() ?? 0);
        
        return $attempts >= $this->limiteDiariaoPorFingerprint;
    }

    /**
     * Verifica se bloqueado por usuario_id (limite diário)
     */
    private function isBlockedByUserId(int $userId): bool
    {
        $stmt = $this->pdo->prepare('
            SELECT tentativas FROM tentativas_login 
            WHERE usuario_id = ? AND data = CURDATE() AND tipo = ?
        ');
        $stmt->execute([$userId, 'usuario_id']);
        $attempts = (int)($stmt->fetchColumn() ?? 0);
        
        return $attempts >= $this->limiteDiarioPorUsuario;
    }

    /**
     * Verifica limite horário (proteção aguda contra ataque)
     */
    private function isBlockedByHourlyLimit(string $fingerprint): bool
    {
        $stmt = $this->pdo->prepare('
            SELECT COUNT(*) as total FROM tentativas_login 
            WHERE fingerprint = ? AND tipo = ? AND ultimo_tentativa > DATE_SUB(NOW(), INTERVAL 1 HOUR)
        ');
        $stmt->execute([$fingerprint, 'fingerprint']);
        $recentAttempts = (int)($stmt->fetchColumn() ?? 0);
        
        return $recentAttempts >= $this->limiteHorario;
    }

    /**
     * ✅ Registra falha de login (por fingerprint E usuario_id)
     * 
     * Registra o evento em ambas as dimensões para rastreamento completo
     */
    public function recordFailure(?string $fingerprint = null, ?int $userId = null, array $meta = []): array
    {
        // Registra por fingerprint
        if ($fingerprint !== null) {
            $this->pdo->prepare('
                INSERT INTO tentativas_login (usuario_id, fingerprint, data, tentativas, ultimo_tentativa, tipo)
                VALUES (NULL, ?, CURDATE(), 1, NOW(), ?)
                ON DUPLICATE KEY UPDATE tentativas = tentativas + 1, ultimo_tentativa = NOW()
            ')->execute([$fingerprint, 'fingerprint']);
        }

        // Registra por usuario_id
        if ($userId !== null && $userId > 0) {
            $this->pdo->prepare('
                INSERT INTO tentativas_login (usuario_id, fingerprint, data, tentativas, ultimo_tentativa, tipo)
                VALUES (?, NULL, CURDATE(), 1, NOW(), ?)
                ON DUPLICATE KEY UPDATE tentativas = tentativas + 1, ultimo_tentativa = NOW()
            ')->execute([$userId, 'usuario_id']);
        }

        return [
            'remaining_by_fingerprint' => $this->getRemainingAttemptsByFingerprint($fingerprint),
            'remaining_by_userid' => $this->getRemainingAttemptsById($userId),
        ];
    }

    /**
     * Retorna tentativas restantes por fingerprint
     */
    public function getRemainingAttemptsByFingerprint(?string $fingerprint): int
    {
        if ($fingerprint === null) {
            return $this->limiteDiariaoPorFingerprint;
        }

        $stmt = $this->pdo->prepare('
            SELECT tentativas FROM tentativas_login 
            WHERE fingerprint = ? AND data = CURDATE() AND tipo = ?
        ');
        $stmt->execute([$fingerprint, 'fingerprint']);
        $attempts = (int)($stmt->fetchColumn() ?? 0);
        
        return max(0, $this->limiteDiariaoPorFingerprint - $attempts);
    }

    /**
     * Retorna tentativas restantes por usuario_id
     */
    public function getRemainingAttemptsById(?int $userId): int
    {
        if ($userId === null || $userId <= 0) {
            return $this->limiteDiarioPorUsuario;
        }

        $stmt = $this->pdo->prepare('
            SELECT tentativas FROM tentativas_login 
            WHERE usuario_id = ? AND data = CURDATE() AND tipo = ?
        ');
        $stmt->execute([$userId, 'usuario_id']);
        $attempts = (int)($stmt->fetchColumn() ?? 0);
        
        return max(0, $this->limiteDiarioPorUsuario - $attempts);
    }

    /**
     * ✅ Reseta tentativas (após login bem-sucedido)
     * 
     * Limpa ambos os registros para permitir novo início
     */
    public function reset(?string $fingerprint = null, ?int $userId = null): void
    {
        if ($fingerprint !== null) {
            $this->pdo->prepare('
                DELETE FROM tentativas_login 
                WHERE fingerprint = ? AND data = CURDATE() AND tipo = ?
            ')->execute([$fingerprint, 'fingerprint']);
        }

        if ($userId !== null && $userId > 0) {
            $this->pdo->prepare('
                DELETE FROM tentativas_login 
                WHERE usuario_id = ? AND data = CURDATE() AND tipo = ?
            ')->execute([$userId, 'usuario_id']);
        }
    }

    /**
     * Obtém histórico de tentativas para análise/auditoria
     */
    public function getHistory(?string $fingerprint = null, ?int $userId = null, int $limit = 50): array
    {
        $query = 'SELECT * FROM tentativas_login WHERE 1=1';
        $params = [];

        if ($fingerprint !== null) {
            $query .= ' AND fingerprint = ?';
            $params[] = $fingerprint;
        }

        if ($userId !== null && $userId > 0) {
            $query .= ' AND usuario_id = ?';
            $params[] = $userId;
        }

        $query .= ' ORDER BY ultimo_tentativa DESC LIMIT ?';
        $params[] = $limit;

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
