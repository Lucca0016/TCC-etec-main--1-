<?php

namespace App\Core;

use Dotenv\Dotenv;

class Bootstrap
{
    public static function init(): void
    {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', realpath(__DIR__ . '/../../'));
        }

        if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
            require_once BASE_PATH . '/vendor/autoload.php';
        }

        if (file_exists(BASE_PATH . '/.env')) {
            if (class_exists('Dotenv\\Dotenv')) {
                $dotenv = Dotenv::createImmutable(BASE_PATH);
                $dotenv->safeLoad();
            } else {
                trigger_error('Pacote vlucas/phpdotenv não encontrado; variáveis de ambiente não foram carregadas.', E_USER_WARNING);
            }
        }

        $timezone = config('app.timezone', 'UTC');
        if (!empty($timezone)) {
            date_default_timezone_set($timezone);
        }

        mb_internal_encoding('UTF-8');

        // ✅ Adicionar HTTP security headers
        self::setSecurityHeaders();

        // ✅ Carregar configuração CORS
        self::loadCors();
    }

    /**
     * ✅ Carrega configuração CORS
     */
    private static function loadCors(): void
    {
        $corsPath = BASE_PATH . '/config/cors.php';
        if (file_exists($corsPath)) {
            require_once $corsPath;
        }
    }

    /**
     * ✅ Define headers de segurança HTTP para proteger contra ataques comuns
     */
    private static function setSecurityHeaders(): void
    {
        // Previne clickjacking (XF)
        header('X-Frame-Options: SAMEORIGIN', true);

        // Protege contra MIME type sniffing (XSS)
        header('X-Content-Type-Options: nosniff', true);

        // Protege contra XSS (navegadores modernos)
        header('X-XSS-Protection: 1; mode=block', true);

        // Content Security Policy (CSP) básico
        $csp = "default-src 'self'; "
             . "script-src 'self' 'unsafe-inline'; "
             . "style-src 'self' 'unsafe-inline'; "
             . "img-src 'self' data: https:; "
             . "font-src 'self' data:";
        header('Content-Security-Policy: ' . $csp, true);

        // HSTS (HTTP Strict Transport Security)
        // Apenas ativa em HTTPS
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains', true);
        }

        // Referrer Policy
        header('Referrer-Policy: strict-origin-when-cross-origin', true);

        // Feature Policy / Permissions Policy (navegadores modernos)
        header('Permissions-Policy: accelerometer=(), camera=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=()', true);
    }
}


