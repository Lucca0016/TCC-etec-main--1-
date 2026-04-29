<?php
namespace App\Core;

class SessionManager
{
    private static ?SessionManager $instance = null;

    private function __construct() {}

    public static function getInstance(): SessionManager
    {
        if (self::$instance === null) self::$instance = new SessionManager();
        return self::$instance;
    }

    public function start(bool $remember = false): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
            $lifetime = 0;
            if ($remember && !empty($_COOKIE['remember_me'])) {
                $days = intval($_COOKIE['remember_me']);
                if ($days > 0) $lifetime = $days * 24 * 60 * 60;
            }
            session_set_cookie_params([
                'lifetime' => $lifetime,
                'path' => '/',
                'secure' => $secure,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            ini_set('session.use_strict_mode', '1');
            if ($lifetime > 0) ini_set('session.gc_maxlifetime', (string)$lifetime);
            session_start();

            // Enforce inactivity timeout: 30 minutes
            $timeout = 30 * 60;
            $last = $_SESSION['last_activity'] ?? null;
            if ($last !== null && (time() - (int)$last) > $timeout) {
                // destroy old session and start fresh
                $this->destroy();
                session_start();
            }
            $_SESSION['last_activity'] = time();
        }
    }

    public function regenerate(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) $this->start();
        session_regenerate_id(true);
        // update last activity time on regenerate
        $_SESSION['last_activity'] = time();
    }

    public function destroy(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) $this->start();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'] ?? '/',
                $params['domain'] ?? '',
                $params['secure'] ?? false,
                $params['httponly'] ?? true
            );
        }
        session_destroy();
    }

    public function set(string $key, $value): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) $this->start();
        $_SESSION[$key] = $value;
    }

    public function get(string $key, $default = null)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) $this->start();
        return $_SESSION[$key] ?? $default;
    }

    public function getSessionInfo(): array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) $this->start();
        $logado = !empty($_SESSION['usuario_id']);
        return [
            'logado' => $logado,
            'papel' => $_SESSION['usuario_papel'] ?? null,
            'nome' => $_SESSION['usuario_nome'] ?? null,
        ];
    }
}


