<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class User
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::connection();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM usuarios WHERE email = ? AND ativo = 1 LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM usuarios WHERE id = ? AND ativo = 1 LIMIT 1');
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function isBlocked(int $userId, int $limit = 5): bool
    {
        $stmt = $this->pdo->prepare('SELECT tentativas FROM tentativas_login WHERE usuario_id = ? AND data = CURDATE()');
        $stmt->execute([$userId]);
        $attempts = (int)($stmt->fetchColumn() ?? 0);
        return $attempts >= $limit;
    }

    public function recordAttempt(int $userId): void
    {
        if ($userId <= 0) {
            return;
        }

        $stmt = $this->pdo->prepare('INSERT INTO tentativas_login (usuario_id, data, tentativas, ultimo_tentativa)
            VALUES (?, CURDATE(), 1, NOW())
            ON DUPLICATE KEY UPDATE tentativas = tentativas + 1, ultimo_tentativa = NOW()');
        $stmt->execute([$userId]);
    }

    public function clearAttempts(int $userId): void
    {
        if ($userId <= 0) {
            return;
        }

        $stmt = $this->pdo->prepare('DELETE FROM tentativas_login WHERE usuario_id = ? AND data = CURDATE()');
        $stmt->execute([$userId]);
    }

    public function validatePassword(string $password, string $hash): bool
    {
        if ($hash === '') {
            return false;
        }
        return password_verify($password, $hash);
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public function recordAudit(int $userId, string $action, ?array $meta = null): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO registro_auditoria (usuario_id, acao, meta, criado_em)
            VALUES (?, ?, ?, NOW())');
        $stmt->execute([
            $userId,
            $action,
            $meta ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null,
        ]);
    }

    public function fetchNews(int $limit = 5): array
    {
        $stmt = $this->pdo->prepare('SELECT n.*, u.nome_completo AS autor_nome
            FROM noticias n
            LEFT JOIN usuarios u ON n.autor_id = u.id
            WHERE n.publicado = 1
            ORDER BY n.publicado_em DESC
            LIMIT ?');
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function paginate(int $offset, int $limit): array
    {
        $stmt = $this->pdo->prepare('SELECT id, email, nome_completo, papel, ativo, criado_em FROM usuarios LIMIT ? OFFSET ?');
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function count(): int
    {
        $stmt = $this->pdo->query('SELECT COUNT(*) FROM usuarios');
        return (int)$stmt->fetchColumn();
    }

    public function create(array $dados): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO usuarios (email, nome_completo, papel, senha_hash, ativo, criado_em)
            VALUES (?, ?, ?, ?, ?, NOW())');
        $stmt->execute([
            $dados['email'],
            $dados['nome_completo'],
            $dados['papel'],
            $dados['senha_hash'],
            $dados['ativo'] ?? 1,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM usuarios WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function fetchAuditLog(int $limit = 50): array
    {
        $stmt = $this->pdo->prepare('SELECT ra.*, u.nome_completo AS usuario_nome
            FROM registro_auditoria ra
            LEFT JOIN usuarios u ON ra.usuario_id = u.id
            ORDER BY ra.criado_em DESC
            LIMIT ?');
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
