<?php

namespace App\Http\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Livro;
use App\Models\Noticia;
use App\Services\JwtService;

class ApiAdminController extends Controller
{
    private User $usuarioModel;
    private Livro $livroModel;
    private Noticia $noticiaModel;
    private JwtService $jwtService;

    public function __construct()
    {
        $this->usuarioModel = new User();
        $this->livroModel = new Livro();
        $this->noticiaModel = new Noticia();
        $this->jwtService = new JwtService();
    }

    private function requireJwtAuth(): array
    {
        $headers = getallheaders();
        $token = null;

        if (isset($headers['Authorization'])) {
            $auth = $headers['Authorization'];
            if (str_starts_with($auth, 'Bearer ')) {
                $token = trim(substr($auth, 7));
            }
        }

        if (!$token) {
            $this->json(['ok' => false, 'message' => 'Token de autenticação não fornecido.'], 401);
            exit;
        }

        $user = $this->jwtService->validateAccessToken($token);
        if (!$user) {
            $this->json(['ok' => false, 'message' => 'Token inválido ou expirado.'], 401);
            exit;
        }

        if (strtolower((string)$user['role']) !== 'admin') {
            $this->json(['ok' => false, 'message' => 'Acesso negado - admin requerido.'], 403);
            exit;
        }

        return $user;
    }

    public function listarUsuarios(): void
    {
        try {
            $this->requireJwtAuth();

            $pagina = (int)($_GET['pagina'] ?? 1);
            $porPagina = (int)($_GET['por_pagina'] ?? 10);
            $offset = ($pagina - 1) * $porPagina;

            $usuarios = $this->usuarioModel->paginate($offset, $porPagina);
            $total = $this->usuarioModel->count();

            $this->json([
                'ok' => true,
                'data' => $usuarios,
                'pagina' => $pagina,
                'total' => $total,
            ]);
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->json(['ok' => false, 'message' => 'Erro ao listar usuários.'], 500);
        }
    }

    public function criarUsuario(): void
    {
        try {
            $this->requireJwtAuth();

            $dados = json_decode(file_get_contents('php://input'), true) ?? [];

            $email = filter_var((string)($dados['email'] ?? ''), FILTER_VALIDATE_EMAIL);
            $nome = trim((string)($dados['nome_completo'] ?? ''));
            $papel = trim((string)($dados['papel'] ?? ''));

            if (!$email || $nome === '' || $papel === '') {
                $this->json(['ok' => false, 'message' => 'Campos obrigatórios faltando'], 422);
                return;
            }

            if ($this->usuarioModel->findByEmail($email)) {
                $this->json(['ok' => false, 'message' => 'Email já existe'], 409);
                return;
            }

            $senha = $dados['senha'] ?? bin2hex(random_bytes(8));

            $usuarioId = $this->usuarioModel->create([
                'email' => $email,
                'nome_completo' => $nome,
                'papel' => $papel,
                'senha_hash' => User::hashPassword($senha),
                'ativo' => 1,
            ]);

            $this->json([
                'ok' => true,
                'data' => ['id' => $usuarioId],
            ], 201);

        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->json(['ok' => false, 'message' => 'Erro ao criar usuário.'], 500);
        }
    }

    public function deletarUsuario(): void
    {
        try {
            $this->requireJwtAuth();

            $dados = json_decode(file_get_contents('php://input'), true) ?? [];
            $usuarioId = (int)($dados['usuario_id'] ?? 0);

            if ($usuarioId <= 0) {
                $this->json(['ok' => false, 'message' => 'ID de usuário inválido'], 422);
                return;
            }

            $this->usuarioModel->delete($usuarioId);

            $this->json(['ok' => true, 'message' => 'Usuário deletado com sucesso']);

        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->json(['ok' => false, 'message' => 'Erro ao deletar usuário.'], 500);
        }
    }

    public function listarLivros(): void
    {
        try {
            $this->requireJwtAuth();

            $livros = $this->livroModel->all();

            $this->json([
                'ok' => true,
                'data' => $livros
            ]);

        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->json(['ok' => false, 'message' => 'Erro ao listar livros.'], 500);
        }
    }

    public function criarLivro(): void
    {
        try {
            $this->requireJwtAuth();

            $dados = json_decode(file_get_contents('php://input'), true) ?? [];

            $titulo = trim((string)($dados['titulo'] ?? ''));
            $autor = trim((string)($dados['autor'] ?? ''));
            $isbn = trim((string)($dados['isbn'] ?? ''));

            if ($titulo === '' || $autor === '' || $isbn === '') {
                $this->json(['ok' => false, 'message' => 'Campos obrigatórios faltando'], 422);
                return;
            }

            $livroId = $this->livroModel->create([
                'titulo' => $titulo,
                'autor' => $autor,
                'isbn' => $isbn,
                'quantidade' => (int)($dados['quantidade'] ?? 0),
                'quantidade_disponivel' => (int)($dados['quantidade'] ?? 0),
            ]);

            $this->json([
                'ok' => true,
                'data' => ['id' => $livroId]
            ], 201);

        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->json(['ok' => false, 'message' => 'Erro ao criar livro.'], 500);
        }
    }

    public function deletarLivro(): void
    {
        try {
            $this->requireJwtAuth();

            $dados = json_decode(file_get_contents('php://input'), true) ?? [];
            $livroId = (int)($dados['livro_id'] ?? 0);

            if ($livroId <= 0) {
                $this->json(['ok' => false, 'message' => 'ID de livro inválido'], 422);
                return;
            }

            $this->livroModel->delete($livroId);

            $this->json(['ok' => true, 'message' => 'Livro deletado com sucesso']);

        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->json(['ok' => false, 'message' => 'Erro ao deletar livro.'], 500);
        }
    }

    public function listarNoticias(): void
    {
        try {
            $this->requireJwtAuth();

            $noticias = $this->noticiaModel->all();

            $this->json([
                'ok' => true,
                'data' => $noticias
            ]);

        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->json(['ok' => false, 'message' => 'Erro ao listar notícias.'], 500);
        }
    }

    public function criarNoticia(): void
    {
        try {
            $this->requireJwtAuth();

            $dados = json_decode(file_get_contents('php://input'), true) ?? [];

            $titulo = trim((string)($dados['titulo'] ?? ''));
            $conteudo = trim((string)($dados['conteudo'] ?? ''));

            if ($titulo === '' || $conteudo === '') {
                $this->json(['ok' => false, 'message' => 'Campos obrigatórios faltando'], 422);
                return;
            }

            $noticiaId = $this->noticiaModel->create([
                'titulo' => $titulo,
                'conteudo' => $conteudo,
                'autor_id' => auth()->id(),
                'publicado' => 1,
            ]);

            $this->json([
                'ok' => true,
                'data' => ['id' => $noticiaId]
            ], 201);

        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->json(['ok' => false, 'message' => 'Erro ao criar notícia.'], 500);
        }
    }

    public function deletarNoticia(): void
    {
        try {
            $this->requireJwtAuth();

            $dados = json_decode(file_get_contents('php://input'), true) ?? [];
            $noticiaId = (int)($dados['noticia_id'] ?? 0);

            if ($noticiaId <= 0) {
                $this->json(['ok' => false, 'message' => 'ID de notícia inválido'], 422);
                return;
            }

            $this->noticiaModel->delete($noticiaId);

            $this->json(['ok' => true, 'message' => 'Notícia deletada com sucesso']);

        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->json(['ok' => false, 'message' => 'Erro ao deletar notícia.'], 500);
        }
    }

    public function auditoria(): void
    {
        try {
            $this->requireJwtAuth();

            $auditoria = $this->usuarioModel->fetchAuditLog();

            $this->json([
                'ok' => true,
                'data' => $auditoria
            ]);

        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->json(['ok' => false, 'message' => 'Erro ao buscar auditoria.'], 500);
        }
    }
}