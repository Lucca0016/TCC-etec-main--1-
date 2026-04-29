#!/usr/bin/env php
<?php
/**
 * Script CLI para executar migrações de banco de dados
 * Execute: php migrations.php
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Database;

class MigrationRunner
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::connection();
    }

    public function run()
    {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "🔄 Executando Migrações do Banco de Dados\n";
        echo str_repeat("=", 60) . "\n\n";

        $migrations = [
            [
                'name' => 'Adicionar coluna link_pdf em biblioteca_livros',
                'sql' => 'ALTER TABLE biblioteca_livros ADD COLUMN IF NOT EXISTS link_pdf VARCHAR(500) NULL DEFAULT NULL AFTER imagem_capa'
            ],
        ];

        $successCount = 0;
        $errorCount = 0;

        foreach ($migrations as $migration) {
            echo "📝 {$migration['name']}...\n";
            try {
                $this->pdo->exec($migration['sql']);
                echo "   ✅ Sucesso!\n\n";
                $successCount++;
            } catch (\Exception $e) {
                echo "   ❌ Erro: {$e->getMessage()}\n\n";
                $errorCount++;
            }
        }

        echo str_repeat("=", 60) . "\n";
        echo "📊 Resultado: {$successCount} sucesso(s), {$errorCount} erro(s)\n";
        echo str_repeat("=", 60) . "\n\n";

        return $errorCount === 0;
    }
}

$runner = new MigrationRunner();
$success = $runner->run();
exit($success ? 0 : 1);
