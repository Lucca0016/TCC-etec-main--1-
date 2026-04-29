<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $config = config('database');
        $driver = $config['default'] ?? 'mysql';
        $settings = $config['connections'][$driver] ?? null;

        if ($settings === null) {
            throw new \RuntimeException("Configuração de banco de dados para '{$driver}' não encontrada.");
        }

        $dsn = sprintf(
            '%s:host=%s;port=%s;dbname=%s;charset=%s',
            $settings['driver'] ?? 'mysql',
            $settings['host'] ?? '127.0.0.1',
            $settings['port'] ?? 3306,
            $settings['database'] ?? 'tcc-etec',
            $settings['charset'] ?? 'utf8mb4'
        );

        $username = $settings['username'] ?? 'root';
        $password = $settings['password'] ?? '';
        $options = $settings['options'] ?? [];

        try {
            self::$connection = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            if ($e->getCode() === '1049' || $e->getCode() === 1049) {
                throw new \RuntimeException('Banco de dados não encontrado. Verifique DB_NAME e a existência do banco: ' . $e->getMessage(), 0, $e);
            }
            throw new \RuntimeException('Falha ao conectar ao banco: ' . $e->getMessage(), 0, $e);
        }

        return self::$connection;
    }

    public static function reset(): void
    {
        self::$connection = null;
    }
}
