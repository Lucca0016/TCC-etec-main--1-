<?php

namespace App\Http\Controllers;

use App\Core\Controller;
use App\Models\Livro;
use App\Models\Emprestimo;
use App\Services\LibraryService;
use App\Support\Auth;

class BibliotecaController extends Controller
{
    private Livro $livroModel;
    private Emprestimo $emprestimoModel;
    private LibraryService $libraryService;

    public function __construct()
    {
        $this->livroModel = new Livro();
        $this->emprestimoModel = new Emprestimo();
        $this->libraryService = new LibraryService();
    }

    public function index(): void
    {
        Auth::start();

        $pagina = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $filtros = [
            'search' => trim((string)($_GET['search'] ?? '')),
            'autor' => trim((string)($_GET['autor'] ?? '')),
            'categoria' => trim((string)($_GET['categoria'] ?? '')),
            'curso' => trim((string)($_GET['curso'] ?? '')),
            'ano' => trim((string)($_GET['ano'] ?? '')),
            'disponibilidade' => trim((string)($_GET['disponibilidade'] ?? '')),
        ];
        $limite = 18;
        $offset = ($pagina - 1) * $limite;

        try {
            $resultado = $this->livroModel->listarFiltrado($filtros, $limite, $offset);
            $livros = $resultado['dados'] ?? [];
            $totalLivros = $resultado['total'] ?? 0;
            $totalPaginas = max(1, (int)ceil($totalLivros / $limite));

            $categorias = $this->livroModel->obterCategorias();
            $cursos = $this->livroModel->obterCursos();
            $anos = $this->livroModel->obterAnos();
            $livrosDestaque = array_slice($livros, 0, 6);

            $usuario = null;
            $emprestimosAtivos = [];
            $emprestimosAtivosCount = 0;
            if (Auth::check()) {
                $usuario = Auth::user();
                $emprestimosAtivos = $this->emprestimoModel->buscarAtivos($usuario['id']);
                $emprestimosAtivosCount = $this->emprestimoModel->contarAtivos($usuario['id']);
            }

            echo $this->view('biblioteca/index', [
                'livros' => $livros,
                'livros_destaque' => $livrosDestaque,
                'total_livros' => $totalLivros,
                'total_disponiveis' => $this->livroModel->contarDisponiveis(),
                'emprestimos_ativos_count' => $emprestimosAtivosCount,
                'categorias_count' => count($categorias),
                'pagina_atual' => $pagina,
                'total_paginas' => $totalPaginas,
                'busca' => $filtros['search'],
                'filtros' => $filtros,
                'categorias' => $categorias,
                'cursos' => $cursos,
                'anos' => $anos,
                'autenticado' => Auth::check(),
                'usuario' => $usuario,
                'erro' => $_GET['error'] ?? null,
            ]);
        } catch (\Throwable $e) {
            error_log('Erro na biblioteca: ' . $e->getMessage());
            echo $this->view('biblioteca/index', [
                'livros' => [],
                'livros_destaque' => [],
                'total_livros' => 0,
                'total_disponiveis' => 0,
                'emprestimos_ativos_count' => 0,
                'categorias_count' => 0,
                'pagina_atual' => 1,
                'total_paginas' => 0,
                'busca' => $filtros['search'],
                'filtros' => $filtros,
                'categorias' => [],
                'cursos' => [],
                'anos' => [],
                'autenticado' => Auth::check(),
                'usuario' => null,
                'erro' => 'Erro ao carregar livros',
            ]);
        }
    }

    public function buscar(): void
    {
        Auth::start();

        $termo = trim((string)($_GET['q'] ?? ''));
        if (empty($termo)) {
            $this->json(['error' => 'Nenhum termo de busca informado.'], 400);
            return;
        }

        try {
            $livros = $this->livroModel->buscarPorTermo($termo, 100);
            $this->json(['livros' => $livros]);
        } catch (\Throwable $e) {
            error_log('Erro na busca: ' . $e->getMessage());
            $this->json(['error' => 'Erro ao buscar livros.'], 500);
        }
    }

    public function detalhes(int $livroId): void
    {
        Auth::start();

        try {
            $livro = $this->livroModel->obterPorId($livroId);
            if (!$livro) {
                $this->json(['error' => 'Livro não encontrado.'], 404);
                return;
            }

            $livro['disponivel'] = (isset($livro['disponivel']) && (int)$livro['disponivel'] === 1)
                || (isset($livro['copias_disponiveis']) && (int)$livro['copias_disponiveis'] > 0);

            $jaEmprestado = false;
            if (Auth::check()) {
                $usuario = Auth::user();
                $emprestimosAtivos = $this->emprestimoModel->buscarAtivos($usuario['id']);
                $jaEmprestado = in_array($livroId, array_column($emprestimosAtivos, 'livro_id'));
            }

            $this->json([
                'livro' => $livro,
                'autenticado' => Auth::check(),
                'ja_emprestado' => $jaEmprestado,
            ]);
        } catch (\Throwable $e) {
            error_log('Erro ao carregar detalhes do livro: ' . $e->getMessage());
            $this->json(['error' => 'Erro ao carregar dados do livro.'], 500);
        }
    }

    public function solicitarEmprestimo(): void
    {
        Auth::start();

        if (!Auth::check()) {
            $this->json(['error' => 'Você precisa estar logado para solicitar um empréstimo.'], 401);
            return;
        }

        $livroId = isset($_POST['livro_id']) ? (int)$_POST['livro_id'] : 0;
        if ($livroId <= 0) {
            $this->json(['error' => 'Livro inválido para solicitação.'], 400);
            return;
        }

        try {
            $usuario = Auth::user();
            $resultado = $this->libraryService->requestLoan($usuario['id'], $livroId);

            $this->json([
                'success' => true,
                'message' => 'Empréstimo solicitado com sucesso. Aguarde a aprovação da secretaria.',
                'emprestimo' => $resultado,
            ]);
        } catch (\Throwable $e) {
            $mensagem = $e->getMessage();
            $this->json(['error' => $mensagem], 500);
        }
    }
}
