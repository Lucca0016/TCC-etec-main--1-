<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Emprestimo
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::connection();
    }

    public function solicitarEmprestimo(int $usuarioId, int $livroId): bool
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO biblioteca_emprestimos
            (usuario_id, livro_id, emprestado_em, vencimento_em, status)
            VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 14 DAY), "solicitado")
        ');
        return $stmt->execute([$usuarioId, $livroId]);
    }

    public function listarDoUsuario(int $usuarioId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT e.id, e.livro_id, l.titulo, e.emprestado_em, e.status
            FROM biblioteca_emprestimos e
            LEFT JOIN biblioteca_livros l ON l.id = e.livro_id
            WHERE e.usuario_id = ?
            ORDER BY e.emprestado_em DESC
        ');
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function obterPorId(int $emprestimoId): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT *
            FROM biblioteca_emprestimos
            WHERE id = ?
        ');
        $stmt->execute([$emprestimoId]);
        $emprestimo = $stmt->fetch(PDO::FETCH_ASSOC);
        return $emprestimo ?: null;
    }

    public function atualizarStatus(int $emprestimoId, string $novoStatus): bool
    {
        $stmt = $this->pdo->prepare('
            UPDATE biblioteca_emprestimos
            SET status = ?, atualizado_em = NOW()
            WHERE id = ?
        ');
        return $stmt->execute([$novoStatus, $emprestimoId]);
    }

    public function registrarDevolucao(int $emprestimoId, bool $emAtraso = false): bool
    {
        $stmt = $this->pdo->prepare('
            UPDATE biblioteca_emprestimos
            SET status = ?, devolvido_em = NOW(), atualizado_em = NOW()
            WHERE id = ?
        ');
        $status = $emAtraso ? 'devolvido_atrasado' : 'devolvido';
        return $stmt->execute([$status, $emprestimoId]);
    }

    public function buscarAtivos(int $usuarioId): array
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
                END as status_real,
                GREATEST(DATEDIFF(DATE(e.vencimento_em), CURDATE()), 0) as dias_restantes,
                GREATEST(DATEDIFF(CURDATE(), DATE(e.vencimento_em)), 0) as dias_atraso
            FROM biblioteca_emprestimos e
            INNER JOIN biblioteca_livros l ON e.livro_id = l.id
            WHERE e.usuario_id = ? AND e.status IN ("solicitado","reservado","emprestado","renovado")
            ORDER BY e.vencimento_em ASC, e.id DESC
        ');
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function buscarHistorico(int $usuarioId, int $limite = 20): array
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
        $stmt->bindValue(1, $usuarioId);
        $stmt->bindValue(2, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function buscarAtrasados(int $limite = 50): array
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

    public function renovar(int $emprestimoId): bool
    {
        $stmt = $this->pdo->prepare('
            UPDATE biblioteca_emprestimos
            SET vencimento_em = DATE_ADD(vencimento_em, INTERVAL 14 DAY),
                status = "renovado",
                atualizado_em = NOW()
            WHERE id = ? AND status = "emprestado"
        ');
        return $stmt->execute([$emprestimoId]);
    }

    public function contarAtivos(int $usuarioId): int
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

    public function contarAtrasados(): int
    {
        $stmt = $this->pdo->query('
            SELECT COUNT(*) as total
            FROM biblioteca_emprestimos
            WHERE status = "emprestado" AND vencimento_em < NOW()
        ');
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($resultado['total'] ?? 0);
    }
}
