<?php

namespace App\Http\Controllers;

use App\Core\Controller;
use App\Models\Student;
use App\Models\Livro;
use App\Models\Emprestimo;
use App\Support\Auth;

class DashboardAlunoController extends Controller
{
    private Student $studentModel;
    private Livro $livroModel;
    private Emprestimo $emprestimoModel;

    public function __construct()
    {
        $this->studentModel = new Student();
        $this->livroModel = new Livro();
        $this->emprestimoModel = new Emprestimo();
    }

    public function index(): void
    {
        Auth::start();
        Auth::requireRole('aluno');

        $usuario = Auth::user();
        $usuarioId = $usuario['id'];
        $usuarioEmail = $_SESSION['usuario_email'] ?? '';
        $usuarioNome = $usuario['nome'];

        try {
            $perfil = $this->studentModel->fullProfile($usuarioId);

            if (!$perfil) {
                header('Location: /TCC-etec/login?error=' . urlencode('Perfil de aluno não encontrado.'));
                exit;
            }

            $emprestimosAtivos = $this->emprestimoModel->buscarAtivos($usuarioId);
            $historicoEmprestimos = $this->emprestimoModel->buscarHistorico($usuarioId, 5);
            $noticias = $this->studentModel->buscarNoticias(5);
            $turmas = $this->studentModel->buscarTurmas($usuarioId);
            $solicitacoes = $this->studentModel->solicitations($usuarioId);

            $contadores = [
                'emprestimos_ativos' => count($emprestimosAtivos),
                'solicitacoes_pendentes' => $this->studentModel->contarSolicitacoesPendentes($usuarioId)
            ];

            echo $this->view('aluno/dashboard', [
                'usuario_nome' => $usuarioNome,
                'usuario_email' => $usuarioEmail,
                'perfil' => $perfil,
                'emprestimos_ativos' => $emprestimosAtivos,
                'historico_emprestimos' => $historicoEmprestimos,
                'noticias' => $noticias,
                'turmas' => $turmas,
                'solicitacoes' => $solicitacoes,
                'contadores' => $contadores
            ]);
        } catch (\Throwable $e) {
            error_log('Erro no DashboardAlunoController: ' . $e->getMessage());
            header('Location: /TCC-etec/login?error=' . urlencode('Erro ao carregar dashboard.'));
            exit;
        }
    }

    public function biblioteca(): void
    {
        Auth::start();
        Auth::requireRole('aluno');

        $usuario = Auth::user();
        $usuarioId = $usuario['id'];

        try {
            $pagina = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $limite = 20;
            $offset = ($pagina - 1) * $limite;

            $livros = $this->livroModel->listarComPaginacao($limite, $offset);
            $totalLivros = $this->livroModel->contar();
            $totalPaginas = ceil($totalLivros / $limite);

            $emprestimosAtivos = $this->emprestimoModel->buscarAtivos($usuarioId);
            $livrosEmprestados = array_column($emprestimosAtivos, 'livro_id');

            foreach ($livros as &$livro) {
                $livro['ja_emprestado'] = in_array($livro['id'], $livrosEmprestados);
            }

            echo $this->view('aluno/biblioteca', [
                'livros' => $livros,
                'total_livros' => $totalLivros,
                'pagina_atual' => $pagina,
                'total_paginas' => $totalPaginas,
                'emprestimos_ativos' => $emprestimosAtivos
            ]);
        } catch (\Throwable $e) {
            error_log('Erro na biblioteca: ' . $e->getMessage());
            header('Location: /TCC-etec/aluno?error=' . urlencode('Erro ao carregar biblioteca.'));
            exit;
        }
    }

    public function solicitarEmprestimo(): void
    {
        Auth::start();
        Auth::requireRole('aluno');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /TCC-etec/aluno/biblioteca');
            exit;
        }

        $usuario = Auth::user();
        $usuarioId = $usuario['id'];
        $livroId = isset($_POST['livro_id']) ? (int)$_POST['livro_id'] : 0;

        if ($livroId <= 0) {
            header('Location: /TCC-etec/aluno/biblioteca?error=' . urlencode('Livro inválido.'));
            exit;
        }

        $livro = $this->livroModel->obterPorId($livroId);
        if (!$livro) {
            header('Location: /TCC-etec/aluno/biblioteca?error=' . urlencode('Livro não encontrado.'));
            exit;
        }

        if (!$livro['disponivel'] && $livro['copias_disponiveis'] <= 0) {
            header('Location: /TCC-etec/aluno/biblioteca?error=' . urlencode('Livro não está disponível.'));
            exit;
        }

        try {
            if ($this->emprestimoModel->solicitarEmprestimo($usuarioId, $livroId)) {
                header('Location: /TCC-etec/aluno?success=' . urlencode('Empréstimo solicitado com sucesso!'));
            } else {
                header('Location: /TCC-etec/aluno/biblioteca?error=' . urlencode('Erro ao solicitar empréstimo.'));
            }
        } catch (\Throwable $e) {
            error_log('Erro ao solicitar empréstimo: ' . $e->getMessage());
            header('Location: /TCC-etec/aluno/biblioteca?error=' . urlencode('Erro ao processar solicitação.'));
        }
        exit;
    }

    public function meusPerfis(): void
    {
        Auth::start();
        Auth::requireRole('aluno');

        $usuario = Auth::user();
        $usuarioId = $usuario['id'];

        try {
            $perfil = $this->studentModel->obterPerfil($usuarioId);
            $turmas = $this->studentModel->buscarTurmas($usuarioId);

            echo $this->view('aluno/perfil', [
                'perfil' => $perfil,
                'turmas' => $turmas
            ]);
        } catch (\Throwable $e) {
            error_log('Erro ao buscar perfil: ' . $e->getMessage());
            header('Location: /TCC-etec/aluno?error=' . urlencode('Erro ao carregar perfil.'));
            exit;
        }
    }

    public function minhasSolicitacoes(): void
    {
        Auth::start();
        Auth::requireRole('aluno');

        $usuario = Auth::user();
        $usuarioId = $usuario['id'];

        try {
            $solicitacoes = $this->studentModel->solicitations($usuarioId);

            echo $this->view('aluno/solicitacoes', [
                'solicitacoes' => $solicitacoes
            ]);
        } catch (\Throwable $e) {
            error_log('Erro ao buscar solicitações: ' . $e->getMessage());
            header('Location: /TCC-etec/aluno?error=' . urlencode('Erro ao carregar solicitações.'));
            exit;
        }
    }

    public function criarSolicitacao(): void
    {
        Auth::start();
        Auth::requireRole('aluno');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /TCC-etec/aluno/solicitacoes');
            exit;
        }

        $usuario = Auth::user();
        $usuarioId = $usuario['id'];
        $tipo = isset($_POST['tipo']) ? trim((string)$_POST['tipo']) : '';
        $descricao = isset($_POST['descricao']) ? trim((string)$_POST['descricao']) : '';

        if (empty($tipo) || empty($descricao)) {
            header('Location: /TCC-etec/aluno/solicitacoes?error=' . urlencode('Preencha todos os campos.'));
            exit;
        }

        try {
            if ($this->studentModel->criarSolicitacao($usuarioId, $tipo, $descricao)) {
                header('Location: /TCC-etec/aluno/solicitacoes?success=' . urlencode('Solicitação criada com sucesso!'));
            } else {
                header('Location: /TCC-etec/aluno/solicitacoes?error=' . urlencode('Erro ao criar solicitação.'));
            }
        } catch (\Throwable $e) {
            error_log('Erro ao criar solicitação: ' . $e->getMessage());
            header('Location: /TCC-etec/aluno/solicitacoes?error=' . urlencode('Erro ao processar solicitação.'));
        }
        exit;
    }
}
