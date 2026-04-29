<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Student
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::connection();
    }

    public function fullProfile(int $userId): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT 
                a.*,
                u.email,
                u.nome_completo,
                u.telefone,
                c.nome AS nome_curso,
                c.codigo AS codigo_curso
            FROM alunos a
            INNER JOIN usuarios u ON a.usuario_id = u.id
            LEFT JOIN cursos c ON a.curso_id = c.id
            WHERE a.usuario_id = ?
        ');
        $stmt->execute([$userId]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        return $student ?: null;
    }

    public function activeLoans(int $userId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT 
                e.id,
                e.livro_id,
                e.emprestado_em,
                e.vencimento_em,
                e.status,
                e.multa_centavos,
                l.titulo,
                l.autor,
                l.editora,
                CASE 
                    WHEN e.status IN ("solicitado","reservado") THEN e.status
                    WHEN e.vencimento_em < NOW() AND e.status = "emprestado" THEN "atrasado"
                    ELSE e.status
                END AS status_real,
                GREATEST(DATEDIFF(DATE(e.vencimento_em), CURDATE()), 0) AS dias_restantes,
                GREATEST(DATEDIFF(CURDATE(), DATE(e.vencimento_em)), 0) AS dias_atraso
            FROM biblioteca_emprestimos e
            INNER JOIN biblioteca_livros l ON e.livro_id = l.id
            WHERE e.usuario_id = ? AND e.status IN ("solicitado","reservado","emprestado","renovado")
            ORDER BY e.vencimento_em ASC, e.id DESC
        ');
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function loanHistory(int $userId, int $limit = 10): array
    {
        $stmt = $this->pdo->prepare('
            SELECT 
                e.id,
                e.livro_id,
                e.emprestado_em,
                e.vencimento_em,
                e.devolvido_em,
                e.status,
                l.titulo,
                l.autor
            FROM biblioteca_emprestimos e
            INNER JOIN biblioteca_livros l ON e.livro_id = l.id
            WHERE e.usuario_id = ?
            ORDER BY e.emprestado_em DESC
            LIMIT ?
        ');
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function solicitations(int $userId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM solicitacoes_secretaria
            WHERE usuario_id = ?
            ORDER BY criado_em DESC
            LIMIT 10
        ');
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function buscarTurmas(int $usuarioId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT
                t.*,
                c.nome as nome_curso,
                m.status as status_matricula,
                m.matriculado_em
            FROM matriculas m
            INNER JOIN turmas t ON m.turma_id = t.id
            INNER JOIN cursos c ON t.curso_id = c.id
            WHERE m.aluno_id = (
                SELECT id FROM alunos WHERE usuario_id = ?
            ) AND m.status = "ativo"
        ');
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function buscarLivrosDisponiveis(int $limite = 20): array
    {
        $stmt = $this->pdo->prepare('
            SELECT *
            FROM biblioteca_livros
            WHERE disponivel = 1 OR copias_disponiveis > 0
            ORDER BY titulo ASC
            LIMIT ?
        ');
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function obterPerfil(int $usuarioId): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT *
            FROM alunos
            WHERE usuario_id = ?
        ');
        $stmt->execute([$usuarioId]);
        $aluno = $stmt->fetch(PDO::FETCH_ASSOC);
        return $aluno ?: null;
    }

    public function criarSolicitacao(int $usuarioId, string $tipo, string $descricao): bool
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO solicitacoes_secretaria
            (usuario_id, tipo, descricao, status, criado_em)
            VALUES (?, ?, ?, "pendente", NOW())
        ');
        return $stmt->execute([$usuarioId, $tipo, $descricao]);
    }

    public function buscarNoticias(int $limite = 5): array
    {
        $stmt = $this->pdo->prepare('
            SELECT *
            FROM noticias
            WHERE publicada = 1 AND (perfil_destino = "aluno" OR perfil_destino = "publico")
            ORDER BY data_publicacao DESC
            LIMIT ?
        ');
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function contarEmprestimosAtivos(int $usuarioId): int
    {
        $stmt = $this->pdo->prepare('
            SELECT COUNT(*) as total
            FROM biblioteca_emprestimos
            WHERE usuario_id = ? AND status IN ("solicitado","reservado","emprestado","renovado")
        ');
        $stmt->execute([$usuarioId]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($resultado['total'] ?? 0);
    }

    public function contarSolicitacoesPendentes(int $usuarioId): int
    {
        $stmt = $this->pdo->prepare('
            SELECT COUNT(*) as total
            FROM solicitacoes_secretaria
            WHERE usuario_id = ? AND status = "pendente"
        ');
        $stmt->execute([$usuarioId]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($resultado['total'] ?? 0);
    }
}
