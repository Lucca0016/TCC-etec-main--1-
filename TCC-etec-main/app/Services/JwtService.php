<?php

namespace App\Services;

use App\Models\RefreshToken;

class JwtService
{
    private string $secret;
    private string $issuer;
    private RefreshToken $refreshTokens;
    private int $accessTtl;
    private int $refreshTtl;

    public function __construct(?string $secret = null, ?RefreshToken $refreshTokens = null)
    {
        $this->secret = $secret ?? config('jwt.secret', env('APP_KEY', 'fallback-secret'));
        $this->issuer = config('jwt.issuer', env('APP_URL', 'http://localhost'));
        $this->accessTtl = (int)config('jwt.access_ttl', 900);
        $this->refreshTtl = (int)config('jwt.refresh_ttl', 604800);
        $this->refreshTokens = $refreshTokens ?? new RefreshToken();
    }

    public function generateAccessToken(array $user): string
    {
        $payload = [
            'iss' => $this->issuer,
            'iat' => time(),
            'exp' => time() + $this->accessTtl,
            'sub' => (int)($user['id'] ?? $user['sub'] ?? 0),
            'id' => (int)($user['id'] ?? $user['sub'] ?? 0),
            'email' => $user['email'] ?? '',
            'role' => strtolower((string)($user['papel'] ?? $user['role'] ?? '')),
        ];

        return $this->encode($payload);
    }

    public function generateRefreshToken(array $user): string
    {
        $token = bin2hex(random_bytes(40));
        $this->refreshTokens->store(
            (int)$user['id'],
            $token,
            $this->refreshTtl,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        return $token;
    }

    public function validateAccessToken(string $token): ?array
    {
        $payload = $this->decode($token);
        if ($payload === null || empty($payload['exp']) || (int)$payload['exp'] < time()) {
            return null;
        }

        return $payload;
    }

    public function validateRefreshToken(string $token): ?array
    {
        if ($token === '') {
            return null;
        }

        return $this->refreshTokens->findValidByToken($token);
    }

    public function invalidateRefreshToken(string $token): void
    {
        if ($token === '') {
            return;
        }

        $this->refreshTokens->revokeToken($token);
    }

    public function invalidateUserRefreshTokens(int $userId): void
    {
        $this->refreshTokens->revokeByUser($userId);
    }

    private function encode(array $payload): string
    {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $segments = [
            $this->base64UrlEncode(json_encode($header, JSON_UNESCAPED_UNICODE)),
            $this->base64UrlEncode(json_encode($payload, JSON_UNESCAPED_UNICODE)),
        ];
        $segments[] = $this->base64UrlEncode(hash_hmac('sha256', implode('.', $segments), $this->secret, true));

        return implode('.', $segments);
    }

    private function decode(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$header, $payload, $signature] = $parts;
        $signed = $this->base64UrlEncode(hash_hmac('sha256', $header . '.' . $payload, $this->secret, true));
        if (!hash_equals($signed, $signature)) {
            return null;
        }

        $decoded = json_decode($this->base64UrlDecode($payload), true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            return null;
        }

        return $decoded;
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $data): string
    {
        $padding = 4 - (strlen($data) % 4);
        if ($padding < 4) {
            $data .= str_repeat('=', $padding);
        }

        return base64_decode(strtr($data, '-_', '+/')) ?: '';
    }
}
