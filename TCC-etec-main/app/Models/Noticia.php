<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Noticia
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::connection();
    }

    public function listar(int $limite = 20, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare('
            SELECT *
            FROM noticias
            WHERE publicada = 1
            ORDER BY data_publicacao DESC
            LIMIT ? OFFSET ?
        ');
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function obterPorSlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT *
            FROM noticias
            WHERE slug = ? AND publicada = 1
        ');
        $stmt->execute([$slug]);
        $noticia = $stmt->fetch(PDO::FETCH_ASSOC);
        return $noticia ?: null;
    }

    public function obterPorId(int $id): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT *
            FROM noticias
            WHERE id = ?
        ');
        $stmt->execute([$id]);
        $noticia = $stmt->fetch(PDO::FETCH_ASSOC);
        return $noticia ?: null;
    }

    public function listarPorPerfil(string $perfil, int $limite = 10): array
    {
        $stmt = $this->pdo->prepare('
            SELECT *
            FROM noticias
            WHERE perfil_destino = ? AND publicada = 1
            ORDER BY data_publicacao DESC
            LIMIT ?
        ');
        $stmt->bindValue(1, $perfil);
        $stmt->bindValue(2, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function listarParaCarrossel(int $limite = 5): array
    {
        $stmt = $this->pdo->prepare('
            SELECT *
            FROM noticias
            WHERE status_carrossel = 1
            ORDER BY data_publicacao DESC
            LIMIT ?
        ');
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function criar(array $dados): bool
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO noticias
            (titulo, slug, conteudo, perfil_destino, publicada, imagem_capa, status_carrossel, data_publicacao)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ');
        return $stmt->execute([
            $dados['titulo'] ?? '',
            $dados['slug'] ?? '',
            $dados['conteudo'] ?? '',
            $dados['perfil_destino'] ?? 'publico',
            $dados['publicada'] ?? 1,
            $dados['imagem_capa'] ?? null,
            $dados['status_carrossel'] ?? 0
        ]);
    }

    public function atualizar(int $id, array $dados): bool
    {
        $stmt = $this->pdo->prepare('
            UPDATE noticias
            SET titulo = ?, slug = ?, conteudo = ?, perfil_destino = ?, publicada = ?, imagem_capa = COALESCE(?, imagem_capa), status_carrossel = ?, atualizado_em = NOW()
            WHERE id = ?
        ');
        return $stmt->execute([
            $dados['titulo'] ?? '',
            $dados['slug'] ?? '',
            $dados['conteudo'] ?? '',
            $dados['perfil_destino'] ?? 'publico',
            $dados['publicada'] ?? 1,
            $dados['imagem_capa'] ?? null,
            $dados['status_carrossel'] ?? 0,
            $id
        ]);
    }

    public function deletar(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM noticias WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function contar(): int
    {
        $stmt = $this->pdo->query('SELECT COUNT(*) as total FROM noticias');
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($resultado['total'] ?? 0);
    }

    public function contarPublicadas(): int
    {
        $stmt = $this->pdo->query('
            SELECT COUNT(*) as total FROM noticias WHERE publicada = 1
        ');
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($resultado['total'] ?? 0);
    }

    public function incrementarVisualizacoes(int $noticiaId): bool
    {
        $stmt = $this->pdo->prepare('
            UPDATE noticias
            SET visualizacoes = visualizacoes + 1
            WHERE id = ?
        ');
        return $stmt->execute([$noticiaId]);
    }

    public function all(): array
    {
        return $this->listar(1000, 0);
    }

    public function findById(int $id): ?array
    {
        // ✅ Alias para obterPorId (compatibilidade)
        return $this->obterPorId($id);
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
