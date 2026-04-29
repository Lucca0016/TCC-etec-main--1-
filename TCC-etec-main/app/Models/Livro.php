<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Livro
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::connection();
    }

    public function listar(int $limite = 200): array
    {
        $stmt = $this->pdo->prepare('
            SELECT id, titulo, autor, ano, disponivel, copias_disponiveis
            FROM biblioteca_livros
            WHERE disponivel = 1 OR copias_disponiveis > 0
            ORDER BY titulo ASC
            LIMIT ?
        ');
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function listarComPaginacao(int $limite = 50, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare('
            SELECT *
            FROM biblioteca_livros
            ORDER BY titulo ASC
            LIMIT ? OFFSET ?
        ');
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function listarFiltrado(array $filtros = [], int $limite = 20, int $offset = 0): array
    {
        $sql = 'SELECT * FROM biblioteca_livros WHERE 1=1';
        $countSql = 'SELECT COUNT(*) AS total FROM biblioteca_livros WHERE 1=1';
        $params = [];

        if (!empty($filtros['search'])) {
            $params[':search'] = '%' . $filtros['search'] . '%';
            $sql .= ' AND (titulo LIKE :search OR autor LIKE :search OR categoria LIKE :search OR curso LIKE :search OR genero LIKE :search)';
            $countSql .= ' AND (titulo LIKE :search OR autor LIKE :search OR categoria LIKE :search OR curso LIKE :search OR genero LIKE :search)';
        }

        if (!empty($filtros['autor'])) {
            $params[':autor'] = '%' . $filtros['autor'] . '%';
            $sql .= ' AND autor LIKE :autor';
            $countSql .= ' AND autor LIKE :autor';
        }

        if (!empty($filtros['categoria'])) {
            $params[':categoria'] = $filtros['categoria'];
            $sql .= ' AND categoria = :categoria';
            $countSql .= ' AND categoria = :categoria';
        }

        if (!empty($filtros['curso'])) {
            $params[':curso'] = $filtros['curso'];
            $sql .= ' AND curso = :curso';
            $countSql .= ' AND curso = :curso';
        }

        if (!empty($filtros['ano'])) {
            $params[':ano'] = (int)$filtros['ano'];
            $sql .= ' AND (ano_publicacao = :ano OR ano = :ano)';
            $countSql .= ' AND (ano_publicacao = :ano OR ano = :ano)';
        }

        if (!empty($filtros['disponibilidade'])) {
            if ($filtros['disponibilidade'] === 'disponiveis') {
                $sql .= ' AND (disponivel = 1 OR copias_disponiveis > 0)';
                $countSql .= ' AND (disponivel = 1 OR copias_disponiveis > 0)';
            } elseif ($filtros['disponibilidade'] === 'indisponiveis') {
                $sql .= ' AND (disponivel = 0 AND copias_disponiveis = 0)';
                $countSql .= ' AND (disponivel = 0 AND copias_disponiveis = 0)';
            }
        }

        $sql .= ' ORDER BY titulo ASC LIMIT :limit OFFSET :offset';

        $countStmt = $this->pdo->prepare($countSql);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $total = (int)($countStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'dados' => $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [],
            'total' => $total,
            'pagina' => (int)floor($offset / $limite) + 1,
            'total_paginas' => $limite > 0 ? (int)ceil($total / $limite) : 0,
        ];
    }

    public function obterPorId(int $id): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT *
            FROM biblioteca_livros
            WHERE id = ?
        ');
        $stmt->execute([$id]);
        $livro = $stmt->fetch(PDO::FETCH_ASSOC);
        return $livro ?: null;
    }

    public function buscarPorTitulo(string $titulo, int $limite = 50): array
    {
        $busca = '%' . $titulo . '%';
        $stmt = $this->pdo->prepare('
            SELECT *
            FROM biblioteca_livros
            WHERE titulo LIKE ?
            ORDER BY titulo ASC
            LIMIT ?
        ');
        $stmt->bindValue(1, $busca);
        $stmt->bindValue(2, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function buscarPorAutor(string $autor, int $limite = 50): array
    {
        $busca = '%' . $autor . '%';
        $stmt = $this->pdo->prepare('
            SELECT *
            FROM biblioteca_livros
            WHERE autor LIKE ?
            ORDER BY titulo ASC
            LIMIT ?
        ');
        $stmt->bindValue(1, $busca);
        $stmt->bindValue(2, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function buscarPorTermo(string $termo, int $limite = 50): array
    {
        $busca = '%' . $termo . '%';
        $stmt = $this->pdo->prepare('
            SELECT *
            FROM biblioteca_livros
            WHERE titulo LIKE ?
               OR autor LIKE ?
               OR categoria LIKE ?
               OR curso LIKE ?
               OR genero LIKE ?
            ORDER BY titulo ASC
            LIMIT ?
        ');
        $stmt->bindValue(1, $busca);
        $stmt->bindValue(2, $busca);
        $stmt->bindValue(3, $busca);
        $stmt->bindValue(4, $busca);
        $stmt->bindValue(5, $busca);
        $stmt->bindValue(6, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function obterCategorias(): array
    {
        $stmt = $this->pdo->query('
            SELECT DISTINCT categoria
            FROM biblioteca_livros
            WHERE categoria IS NOT NULL AND categoria <> ""
            ORDER BY categoria ASC
        ');
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    public function obterCursos(): array
    {
        $stmt = $this->pdo->query('
            SELECT DISTINCT curso
            FROM biblioteca_livros
            WHERE curso IS NOT NULL AND curso <> ""
            ORDER BY curso ASC
        ');
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    public function obterAnos(): array
    {
        $stmt = $this->pdo->query('
            SELECT DISTINCT COALESCE(ano_publicacao, ano) AS ano
            FROM biblioteca_livros
            WHERE ano_publicacao IS NOT NULL OR ano IS NOT NULL
            ORDER BY ano DESC
        ');
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    public function contar(): int
    {
        $stmt = $this->pdo->query('
            SELECT COUNT(*) as total
            FROM biblioteca_livros
        ');
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($resultado['total'] ?? 0);
    }

    public function contarDisponiveis(): int
    {
        $stmt = $this->pdo->query('
            SELECT COUNT(*) as total
            FROM biblioteca_livros
            WHERE disponivel = 1 OR copias_disponiveis > 0
        ');
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($resultado['total'] ?? 0);
    }

    public function incrementarVisualizacoes(int $livroId): bool
    {
        $stmt = $this->pdo->prepare('
            UPDATE biblioteca_livros
            SET visualizacoes = visualizacoes + 1
            WHERE id = ?
        ');
        return $stmt->execute([$livroId]);
    }

    public function all(): array
    {
        return $this->listar();
    }

    public function findById(int $id): ?array
    {
        return $this->obterPorId($id);
    }

    public function create(array $dados): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO biblioteca_livros (titulo, autor, isbn, quantidade, quantidade_disponivel, criado_em)
            VALUES (?, ?, ?, ?, ?, NOW())');
        $stmt->execute([
            $dados['titulo'],
            $dados['autor'],
            $dados['isbn'],
            $dados['quantidade'] ?? 0,
            $dados['quantidade_disponivel'] ?? 0,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM biblioteca_livros WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function recordAudit(int $usuarioId, string $acao, ?array $meta = null): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO registro_auditoria (usuario_id, acao, meta, criado_em)
            VALUES (?, ?, ?, NOW())');
        $stmt->execute([
            $usuarioId,
            $acao,
            $meta ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null,
        ]);
    }
}
