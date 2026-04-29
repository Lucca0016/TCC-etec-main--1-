<?php

namespace App\Http\Controllers;

use App\Core\Controller;
use App\Models\Emprestimo;
use App\Models\Livro;
use App\Support\Auth;

class EmprestimoController extends Controller
{
    private Emprestimo $emprestimoModel;
    private Livro $livroModel;

    public function __construct()
    {
        $this->emprestimoModel = new Emprestimo();
        $this->livroModel = new Livro();
    }

    public function index(): void
    {
        Auth::start();
        Auth::requireRole('aluno');

        $usuario = Auth::user();
        $usuarioId = $usuario['id'];

        try {
            $emprestimosAtivos = $this->emprestimoModel->buscarAtivos($usuarioId);
            $historicoEmprestimos = $this->emprestimoModel->buscarHistorico($usuarioId, 20);

            echo $this->view('emprestimos/index', [
                'emprestimos_ativos' => $emprestimosAtivos,
                'historico' => $historicoEmprestimos
            ]);
        } catch (\Throwable $e) {
            error_log('Erro ao listar empréstimos: ' . $e->getMessage());
            header('Location: /TCC-etec/aluno?error=' . urlencode('Erro ao carregar empréstimos.'));
            exit;
        }
    }

    public function criar(): void
    {
        Auth::start();
        Auth::requireRole('aluno');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /TCC-etec/biblioteca');
            exit;
        }

        $usuario = Auth::user();
        $usuarioId = $usuario['id'];
        $livroId = isset($_POST['livro_id']) ? (int)$_POST['livro_id'] : 0;

        if ($livroId <= 0) {
            header('Location: /TCC-etec/biblioteca?error=' . urlencode('Livro inválido.'));
            exit;
        }

        try {
            $livro = $this->livroModel->obterPorId($livroId);

            if (!$livro) {
                header('Location: /TCC-etec/biblioteca?error=' . urlencode('Livro não encontrado.'));
                exit;
            }

            if (!$livro['disponivel'] && $livro['copias_disponiveis'] <= 0) {
                header('Location: /TCC-etec/biblioteca?error=' . urlencode('Livro não está disponível.'));
                exit;
            }

            if ($this->emprestimoModel->solicitarEmprestimo($usuarioId, $livroId)) {
                header('Location: /TCC-etec/aluno?success=' . urlencode('Empréstimo solicitado com sucesso!'));
            } else {
                header('Location: /TCC-etec/biblioteca?error=' . urlencode('Erro ao solicitar empréstimo.'));
            }
        } catch (\Throwable $e) {
            error_log('Erro ao criar empréstimo: ' . $e->getMessage());
            header('Location: /TCC-etec/biblioteca?error=' . urlencode('Erro ao processar solicitação.'));
        }
        exit;
    }

    public function renovar(): void
    {
        Auth::start();
        Auth::requireRole('aluno');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /TCC-etec/emprestimos');
            exit;
        }

        $usuario = Auth::user();
        $usuarioId = $usuario['id'];
        $emprestimoId = isset($_POST['emprestimo_id']) ? (int)$_POST['emprestimo_id'] : 0;

        if ($emprestimoId <= 0) {
            header('Location: /TCC-etec/emprestimos?error=' . urlencode('Empréstimo inválido.'));
            exit;
        }

        try {
            $emprestimo = $this->emprestimoModel->obterPorId($emprestimoId);

            if (!$emprestimo || $emprestimo['usuario_id'] != $usuarioId) {
                header('Location: /TCC-etec/emprestimos?error=' . urlencode('Empréstimo não encontrado.'));
                exit;
            }

            if ($this->emprestimoModel->renovar($emprestimoId)) {
                header('Location: /TCC-etec/emprestimos?success=' . urlencode('Empréstimo renovado com sucesso!'));
            } else {
                header('Location: /TCC-etec/emprestimos?error=' . urlencode('Não foi possível renovar o empréstimo.'));
            }
        } catch (\Throwable $e) {
            error_log('Erro ao renovar empréstimo: ' . $e->getMessage());
            header('Location: /TCC-etec/emprestimos?error=' . urlencode('Erro ao processar renovação.'));
        }
        exit;
    }

    public function detalhes(int $emprestimoId): void
    {
        Auth::start();
        Auth::requireRole('aluno');

        $usuario = Auth::user();
        $usuarioId = $usuario['id'];

        try {
            $emprestimo = $this->emprestimoModel->obterPorId($emprestimoId);

            if (!$emprestimo || $emprestimo['usuario_id'] != $usuarioId) {
                header('HTTP/1.1 403 Forbidden');
                echo 'Acesso negado';
                exit;
            }

            $livro = $this->livroModel->obterPorId($emprestimo['livro_id']);

            echo $this->view('emprestimos/detalhes', [
                'emprestimo' => $emprestimo,
                'livro' => $livro
            ]);
        } catch (\Throwable $e) {
            error_log('Erro ao carregar detalhes: ' . $e->getMessage());
            header('Location: /TCC-etec/emprestimos?error=' . urlencode('Erro ao carregar detalhes.'));
            exit;
        }
    }
}
