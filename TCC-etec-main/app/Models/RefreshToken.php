<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class RefreshToken
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::connection();
        $this->ensureTableExists();
    }

    private function ensureTableExists(): void
    {
        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS refresh_tokens (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                usuario_id INT NOT NULL,
                token_hash CHAR(64) NOT NULL,
                expires_at DATETIME NOT NULL,
                revoked_at DATETIME NULL,
                ip VARCHAR(45) NULL,
                user_agent VARCHAR(255) NULL,
                created_at DATETIME NOT NULL,
                UNIQUE KEY token_hash (token_hash),
                INDEX indice_usuario_id (usuario_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'
        );
    }

    public function store(int $userId, string $token, int $ttlSeconds, ?string $ip = null, ?string $userAgent = null): bool
    {
        $hash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', time() + $ttlSeconds);
        $createdAt = date('Y-m-d H:i:s');

        $stmt = $this->pdo->prepare(
            'INSERT INTO refresh_tokens (usuario_id, token_hash, expires_at, ip, user_agent, created_at)
             VALUES (?, ?, ?, ?, ?, ?)'
        );

        return $stmt->execute([
            $userId,
            $hash,
            $expiresAt,
            $ip,
            $userAgent,
            $createdAt,
        ]);
    }

    public function findValidByToken(string $token): ?array
    {
        $hash = hash('sha256', $token);

        $stmt = $this->pdo->prepare(
            'SELECT rt.usuario_id AS id, u.email, u.papel AS role, rt.expires_at, rt.revoked_at
             FROM refresh_tokens rt
             INNER JOIN usuarios u ON u.id = rt.usuario_id
             WHERE rt.token_hash = ?
             LIMIT 1'
        );
        $stmt->execute([$hash]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        if (!empty($row['revoked_at']) || strtotime($row['expires_at']) < time()) {
            return null;
        }

        return [
            'id' => (int)$row['id'],
            'email' => $row['email'],
            'role' => strtolower((string)$row['role']),
        ];
    }

    public function revokeToken(string $token): void
    {
        $hash = hash('sha256', $token);
        $stmt = $this->pdo->prepare('UPDATE refresh_tokens SET revoked_at = NOW() WHERE token_hash = ?');
        $stmt->execute([$hash]);
    }

    public function revokeByUser(int $userId): void
    {
        $stmt = $this->pdo->prepare('UPDATE refresh_tokens SET revoked_at = NOW() WHERE usuario_id = ?');
        $stmt->execute([$userId]);
    }
}
