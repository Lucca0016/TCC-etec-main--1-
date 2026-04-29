<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Vestibular
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::connection();
        $this->ensureTableExists();
    }

    private function ensureTableExists(): void
    {
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS vestibular_inscricoes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(150) NOT NULL,
            cpf VARCHAR(20) NOT NULL,
            email VARCHAR(150) NOT NULL,
            telefone VARCHAR(20) NOT NULL,
            curso VARCHAR(100) NOT NULL,
            turno VARCHAR(50) NOT NULL,
            modalidade VARCHAR(50) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;');
    }

    public function listarCursos(): array
    {
        try {
            $stmt = $this->pdo->query('SELECT nome FROM cursos ORDER BY nome ASC');
            $result = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
            return !empty($result) ? $result : $this->defaultCourses();
        } catch (\Throwable $e) {
            return $this->defaultCourses();
        }
    }

    public function salvarInscricao(array $dados): bool
    {
        $stmt = $this->pdo->prepare('INSERT INTO vestibular_inscricoes (nome, cpf, email, telefone, curso, turno, modalidade) VALUES (?, ?, ?, ?, ?, ?, ?)');
        return $stmt->execute([
            $dados['nome'],
            preg_replace('/\D/', '', $dados['cpf']),
            $dados['email'],
            $dados['telefone'],
            $dados['curso'],
            $dados['turno'],
            $dados['modalidade'],
        ]);
    }

    private function defaultCourses(): array
    {
        return [
            'Desenvolvimento de Sistemas',
            'Administração',
            'Enfermagem',
            'Redes',
            'Design',
            'Mobile',
        ];
    }
}
