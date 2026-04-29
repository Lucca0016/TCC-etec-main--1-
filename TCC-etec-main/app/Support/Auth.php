<?php

namespace App\Support;

use App\Core\SessionManager;

class Auth
{
    // ✅ Roles agora centralizadas em RoleManager

    public static function start(): void
    {
        SessionManager::getInstance()->start();
    }

    public static function check(): bool
    {
        self::start();
        return !empty($_SESSION['usuario_id']);
    }

    public static function role(): string
    {
        self::start();
        return strtolower((string)($_SESSION['usuario_papel'] ?? ''));
    }

    public static function login(array $user, bool $remember = false): void
    {
        $session = SessionManager::getInstance();
        $session->start($remember);
        $session->regenerate();
        $_SESSION['usuario_id'] = $user['id'] ?? null;
        $_SESSION['usuario_email'] = $user['email'] ?? null;
        $_SESSION['usuario_nome'] = $user['nome'] ?? null;
        $_SESSION['usuario_papel'] = $user['papel'] ?? null;
    }

    public static function user(): array
    {
        self::start();
        return [
            'id' => $_SESSION['usuario_id'] ?? null,
            'nome' => $_SESSION['usuario_nome'] ?? null,
            'papel' => $_SESSION['usuario_papel'] ?? null,
        ];
    }

    public static function requireAuth(string $redirect = '/TCC-etec/login'): void
    {
        if (!self::check()) {
            header('Location: ' . $redirect);
            exit;
        }
    }

    public static function logout(): void
    {
        SessionManager::getInstance()->destroy();
    }

public static function requireRole($roles, string $redirect = '/TCC-etec/login'): void    {
        self::requireAuth($redirect);
        $role = self::role();
        $rolesArray = (array)$roles;
        
        // ✅ Usar RoleManager para verificar autorização centralizada
        $isAuthorized = false;
        foreach ($rolesArray as $requiredRole) {
            if (RoleManager::isInGroup($role, $requiredRole)) {
                $isAuthorized = true;
                break;
            }
        }
        
        if (!$isAuthorized) {
            header('HTTP/1.1 403 Forbidden');
            echo 'Acesso negado';
            exit;
        }
    }
}
