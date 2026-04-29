<?php

namespace App\Http\Controllers;

use App\Core\Database;

class MigrationController
{
    public function runMigrations()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            return json_encode(['erro' => 'Não autenticado']);
        }
        try {
            $pdo = Database::connection();

            $migrations = [
                "ALTER TABLE biblioteca_livros ADD COLUMN IF NOT EXISTS link_pdf VARCHAR(500) NULL DEFAULT NULL AFTER imagem_capa",
            ];

            $results = [];
            foreach ($migrations as $sql) {
                try {
                    $pdo->exec($sql);
                    $results[] = [
                        'status' => 'sucesso',
                        'sql' => substr($sql, 0, 50) . '...',
                        'mensagem' => 'Migração executada com sucesso'
                    ];
                } catch (\Exception $e) {
                    $results[] = [
                        'status' => 'erro',
                        'sql' => substr($sql, 0, 50) . '...',
                        'mensagem' => $e->getMessage()
                    ];
                }
            }

            header('Content-Type: application/json');
            echo json_encode([
                'sucesso' => true,
                'migrações' => $results
            ]);

        } catch (\Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'sucesso' => false,
                'erro' => $e->getMessage()
            ]);
        }
    }
}
