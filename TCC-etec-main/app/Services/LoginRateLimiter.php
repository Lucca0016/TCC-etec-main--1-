<?php

namespace App\Services;

use App\Core\Database;
use PDO;

class LoginRateLimiter
{
    private PDO $pdo;
    private int $limiteDiario = 10;
    private bool $hasFingerprintColumn = false;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::connection();
        $this->hasFingerprintColumn = $this->detectFingerprintColumn();
    }

    public function isBlocked(string $fingerprint): bool
    {
        if ($this->hasFingerprintColumn) {
            $stmt = $this->pdo->prepare('SELECT tentativas FROM tentativas_login WHERE fingerprint = ? AND data = CURDATE()');
            $stmt->execute([$fingerprint]);
        } else {
            // Compatibilidade: schema antigo sem coluna fingerprint
            $stmt = $this->pdo->prepare('SELECT tentativas FROM tentativas_login WHERE usuario_id = ? AND data = CURDATE()');
            $stmt->execute([0]);
        }

        $tentativas = (int)($stmt->fetchColumn() ?? 0);
        return $tentativas >= $this->limiteDiario;
    }

    public function registerFailure(string $fingerprint, array $meta = []): array
    {
        if ($this->hasFingerprintColumn) {
            $stmt = $this->pdo->prepare('INSERT INTO tentativas_login (usuario_id, fingerprint, data, tentativas, ultimo_tentativa)
                VALUES (?, ?, CURDATE(), 1, NOW())
                ON DUPLICATE KEY UPDATE tentativas = tentativas + 1, ultimo_tentativa = NOW()');
            $stmt->execute([0, $fingerprint]);
        } else {
            // Compatibilidade: schema antigo sem coluna fingerprint
            $stmt = $this->pdo->prepare('INSERT INTO tentativas_login (usuario_id, data, tentativas, ultimo_tentativa)
                VALUES (?, CURDATE(), 1, NOW())
                ON DUPLICATE KEY UPDATE tentativas = tentativas + 1, ultimo_tentativa = NOW()');
            $stmt->execute([0]);
        }

        $remaining = $this->remainingAttempts($fingerprint);
        return ['remaining' => $remaining];
    }

    public function remainingAttempts(string $fingerprint): int
    {
        if ($this->hasFingerprintColumn) {
            $stmt = $this->pdo->prepare('SELECT tentativas FROM tentativas_login WHERE fingerprint = ? AND data = CURDATE()');
            $stmt->execute([$fingerprint]);
        } else {
            // Compatibilidade: schema antigo sem coluna fingerprint
            $stmt = $this->pdo->prepare('SELECT tentativas FROM tentativas_login WHERE usuario_id = ? AND data = CURDATE()');
            $stmt->execute([0]);
        }

        $tentativas = (int)($stmt->fetchColumn() ?? 0);
        return max(0, $this->limiteDiario - $tentativas);
    }

    public function reset(string $fingerprint): void
    {
        if ($this->hasFingerprintColumn) {
            $stmt = $this->pdo->prepare('DELETE FROM tentativas_login WHERE fingerprint = ? AND data = CURDATE()');
            $stmt->execute([$fingerprint]);
            return;
        }

        // Compatibilidade: schema antigo sem coluna fingerprint
        $stmt = $this->pdo->prepare('DELETE FROM tentativas_login WHERE usuario_id = ? AND data = CURDATE()');
        $stmt->execute([0]);
    }

    private function detectFingerprintColumn(): bool
    {
        try {
            $stmt = $this->pdo->query("SHOW COLUMNS FROM tentativas_login LIKE 'fingerprint'");
            return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            // Em caso de erro de introspecção, usa modo compatível sem fingerprint.
            return false;
        }
    }
}
