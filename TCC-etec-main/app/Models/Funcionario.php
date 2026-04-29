<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Funcionario
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::connection();
    }

    public function obterCompleto(int $usuarioId): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT
                f.*,
                u.email,
                u.nome_completo,
                u.telefone,
                u.papel,
                u.criado_em
            FROM funcionarios f
            INNER JOIN usuarios u ON f.usuario_id = u.id
            WHERE f.usuario_id = ?
        ');
        $stmt->execute([$usuarioId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function buscarSolicitacoes(int $limite = 50, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare('
            SELECT
                s.*,
                u.nome_completo,
                u.email
            FROM solicitacoes_secretaria s
            INNER JOIN usuarios u ON s.usuario_id = u.id
            ORDER BY s.criado_em DESC
            LIMIT ? OFFSET ?
        ');
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function buscarSolicitacoesPorStatus(string $status, int $limite = 50): array
    {
        $stmt = $this->pdo->prepare('
            SELECT
                s.*,
                u.nome_completo,
                u.email
            FROM solicitacoes_secretaria s
            INNER JOIN usuarios u ON s.usuario_id = u.id
            WHERE s.status = ?
            ORDER BY s.criado_em DESC
            LIMIT ?
        ');
        $stmt->bindValue(1, $status);
        $stmt->bindValue(2, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function atualizarStatusSolicitacao(int $solicitacaoId, string $novoStatus): bool
    {
        $stmt = $this->pdo->prepare('
            UPDATE solicitacoes_secretaria
            SET status = ?, atualizado_em = NOW()
            WHERE id = ?
        ');
        return $stmt->execute([$novoStatus, $solicitacaoId]);
    }

    public function buscarEmprestimosAtrasados(int $limite = 50): array
    {
        $stmt = $this->pdo->prepare('
            SELECT
                e.*,
                u.nome_completo,
                u.email,
                l.titulo,
                l.autor,
                DATEDIFF(CURDATE(), DATE(e.vencimento_em)) as dias_atraso,
                CASE
                    WHEN DATEDIFF(CURDATE(), DATE(e.vencimento_em)) <= 7 THEN "alerta"
                    WHEN DATEDIFF(CURDATE(), DATE(e.vencimento_em)) <= 30 THEN "aviso"
                    ELSE "critico"
                END as prioridade
            FROM biblioteca_emprestimos e
            INNER JOIN usuarios u ON e.usuario_id = u.id
            INNER JOIN biblioteca_livros l ON e.livro_id = l.id
            WHERE e.status = "emprestado" AND e.vencimento_em < NOW()
            ORDER BY dias_atraso DESC
            LIMIT ?
        ');
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function obterEstatisticasBiblioteca(): array
    {
        $stats = [];

        $stmt = $this->pdo->query('
            SELECT COUNT(*) as total, SUM(copias_disponiveis) as disponiveis
            FROM biblioteca_livros
        ');
        $stats['livros'] = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $this->pdo->query('
            SELECT COUNT(*) as total FROM biblioteca_emprestimos WHERE status = "emprestado"
        ');
        $stats['emprestimos_ativos'] = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $this->pdo->query('
            SELECT COUNT(*) as total FROM biblioteca_emprestimos
            WHERE status = "emprestado" AND vencimento_em < NOW()
        ');
        $stats['emprestimos_atrasados'] = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $this->pdo->query('
            SELECT COUNT(*) as total FROM solicitacoes_secretaria WHERE status = "pendente"
        ');
        $stats['solicitacoes_pendentes'] = $stmt->fetch(PDO::FETCH_ASSOC);

        return $stats;
    }

    public function buscarAlunos(int $limite = 50, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare('
            SELECT
                u.id,
                u.nome_completo,
                u.email,
                a.matricula,
                a.curso_id
            FROM alunos a
            INNER JOIN usuarios u ON a.usuario_id = u.id
            ORDER BY u.nome_completo ASC
            LIMIT ? OFFSET ?
        ');
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function registrarAuditoria(int $usuarioId, string $acao, array $dados = []): bool
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO auditoria (usuario_id, acao, dados, criado_em)
            VALUES (?, ?, ?, NOW())
        ');
        return $stmt->execute([
            $usuarioId,
            $acao,
            json_encode($dados)
        ]);
    }
}
