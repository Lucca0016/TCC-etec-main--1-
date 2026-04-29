<?php

namespace App\Config;

use PDO;

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $this->conectar();
    }

    private function conectar() {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $dbname = getenv('DB_DATABASE') ?: getenv('DB_NAME') ?: 'tcc-etec';
        $user = getenv('DB_USERNAME') ?: getenv('DB_USER') ?: 'root';
        $password = getenv('DB_PASSWORD') ?: '';

        try {
            $this->connection = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $user,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (\PDOException $e) {
            throw new \RuntimeException('Falha ao conectar ao banco: ' . $e->getMessage(), 0, $e);
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}
?>
