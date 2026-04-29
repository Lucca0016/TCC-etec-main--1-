<?php

namespace App\Http\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Funcionario;
use App\Models\Emprestimo;
use App\Models\Student;
use App\Support\Auth;

class SecretariaController extends Controller
{
    private Funcionario $funcionarioModel;
    private Emprestimo $emprestimoModel;
    private Student $studentModel;

    public function __construct()
    {
        $this->funcionarioModel = new Funcionario();
        $this->emprestimoModel = new Emprestimo();
        $this->studentModel = new Student();
    }

    public function painel(): void
    {
        Auth::start();
        Auth::requireRole('secretaria');

        $usuario = Auth::user();
        $usuarioId = $usuario['id'];
        $usuarioNome = $usuario['nome'];

        try {
            $funcionario = $this->funcionarioModel->obterCompleto($usuarioId);

            if (!$funcionario) {
                header('Location: /TCC-etec/login?error=' . urlencode('Acesso de secretaria não autorizado.'));
                exit;
            }

            $solicitacoesPendentes = $this->funcionarioModel->buscarSolicitacoesPorStatus('pendente', 10);
            $emprestimosAtrasados = $this->funcionarioModel->buscarEmprestimosAtrasados();
            $estatisticas = $this->funcionarioModel->obterEstatisticasBiblioteca();

            $contadores = [
                'solicitacoes_pendentes' => count($solicitacoesPendentes),
                'emprestimos_atrasados' => count($emprestimosAtrasados),
                'alunos_ativos' => count($this->funcionarioModel->buscarAlunos())
            ];

            echo $this->view('secretaria/painel', [
                'usuario_nome' => $usuarioNome,
                'funcionario' => $funcionario,
                'contadores' => $contadores,
                'solicitacoes_pendentes' => $solicitacoesPendentes,
                'emprestimos_atrasados' => $emprestimosAtrasados,
                'estatisticas' => $estatisticas
            ]);
        } catch (\Throwable $e) {
            error_log('Erro no painel de secretaria: ' . $e->getMessage());
            header('Location: /TCC-etec/login?error=' . urlencode('Erro ao carregar painel.'));
            exit;
        }
    }

    public function gerenciarSolicitacoes(): void
    {
        Auth::start();
        Auth::requireRole('secretaria');

        try {
            $pagina = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $status = isset($_GET['status']) ? trim((string)$_GET['status']) : 'pendente';
            $limite = 20;
            $offset = ($pagina - 1) * $limite;

            $solicitacoes = $this->funcionarioModel->buscarSolicitacoesPorStatus($status, $limite);
            $totalSolicitacoes = count($this->funcionarioModel->buscarSolicitacoes($limite));
            $totalPaginas = ceil($totalSolicitacoes / $limite);

            echo $this->view('secretaria/solicitacoes', [
                'solicitacoes' => $solicitacoes,
                'pagina_atual' => $pagina,
                'total_paginas' => $totalPaginas,
                'status_filtro' => $status
            ]);
        } catch (\Throwable $e) {
            error_log('Erro ao listar solicitações: ' . $e->getMessage());
            header('Location: /TCC-etec/secretaria?error=' . urlencode('Erro ao carregar solicitações.'));
            exit;
        }
    }

    public function atualizarSolicitacao(int $solicitacaoId): void
    {
        Auth::start();
        Auth::requireRole('secretaria');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /TCC-etec/secretaria/solicitacoes');
            exit;
        }

        $novoStatus = isset($_POST['status']) ? trim((string)$_POST['status']) : '';
        $statusValidos = ['pendente', 'deferida', 'indeferida'];

        if (empty($novoStatus) || !in_array($novoStatus, $statusValidos)) {
            header('Location: /TCC-etec/secretaria/solicitacoes?error=' . urlencode('Status inválido.'));
            exit;
        }

        try {
            if ($this->funcionarioModel->atualizarStatusSolicitacao($solicitacaoId, $novoStatus)) {
                header('Location: /TCC-etec/secretaria/solicitacoes?success=' . urlencode('Solicitação atualizada.'));
            } else {
                header('Location: /TCC-etec/secretaria/solicitacoes?error=' . urlencode('Erro ao atualizar.'));
            }
        } catch (\Throwable $e) {
            error_log('Erro ao atualizar solicitação: ' . $e->getMessage());
            header('Location: /TCC-etec/secretaria/solicitacoes?error=' . urlencode('Erro ao processar.'));
        }
        exit;
    }

    public function gerenciarEmprestimos(): void
    {
        Auth::start();
        Auth::requireRole('secretaria');

        try {
            $pagina = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $limite = 25;
            $offset = ($pagina - 1) * $limite;

            $emprestimos = Database::connection()->prepare('
                SELECT
                    e.*,
                    u.nome_completo,
                    u.email,
                    l.titulo
                FROM biblioteca_emprestimos e
                INNER JOIN usuarios u ON e.usuario_id = u.id
                INNER JOIN biblioteca_livros l ON e.livro_id = l.id
                ORDER BY e.data_emprestimo DESC
                LIMIT ? OFFSET ?
            ');

            $emprestimos->bindValue(1, $limite, \PDO::PARAM_INT);
            $emprestimos->bindValue(2, $offset, \PDO::PARAM_INT);
            $emprestimos->execute();
            $listaEmprestimos = $emprestimos->fetchAll(\PDO::FETCH_ASSOC) ?: [];

            $totalStmt = Database::connection()->query('SELECT COUNT(*) as total FROM biblioteca_emprestimos');
            $totalEmprestimos = (int)($totalStmt->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0);
            $totalPaginas = ceil($totalEmprestimos / $limite);

            echo $this->view('secretaria/emprestimos', [
                'emprestimos' => $listaEmprestimos,
                'pagina_atual' => $pagina,
                'total_paginas' => $totalPaginas,
                'total_emprestimos' => $totalEmprestimos
            ]);
        } catch (\Throwable $e) {
            error_log('Erro ao listar empréstimos: ' . $e->getMessage());
            header('Location: /TCC-etec/secretaria?error=' . urlencode('Erro ao carregar empréstimos.'));
            exit;
        }
    }

    public function registrarDevolucao(int $emprestimoId): void
    {
        Auth::start();
        Auth::requireRole('secretaria');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /TCC-etec/secretaria/emprestimos');
            exit;
        }

        try {
            $emprestimo = Database::connection()->prepare('
                SELECT * FROM biblioteca_emprestimos WHERE id = ?
            ');
            $emprestimo->execute([$emprestimoId]);
            $dados = $emprestimo->fetch(\PDO::FETCH_ASSOC);

            if (!$dados) {
                header('Location: /TCC-etec/secretaria/emprestimos?error=' . urlencode('Empréstimo não encontrado.'));
                exit;
            }

            if ($this->emprestimoModel->registrarDevolucao($emprestimoId)) {
                header('Location: /TCC-etec/secretaria/emprestimos?success=' . urlencode('Devolução registrada.'));
            } else {
                header('Location: /TCC-etec/secretaria/emprestimos?error=' . urlencode('Erro ao registrar devolução.'));
            }
        } catch (\Throwable $e) {
            error_log('Erro ao registrar devolução: ' . $e->getMessage());
            header('Location: /TCC-etec/secretaria/emprestimos?error=' . urlencode('Erro ao processar.'));
        }
        exit;
    }

    public function gerenciarAlunos(): void
    {
        Auth::start();
        Auth::requireRole('secretaria');

        try {
            $pagina = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $limite = 30;
            $offset = ($pagina - 1) * $limite;

            $alunos = $this->funcionarioModel->buscarAlunos();
            $totalAlunos = count($alunos);
            $totalPaginas = ceil($totalAlunos / $limite);
            $alunosPaginados = array_slice($alunos, $offset, $limite);

            echo $this->view('secretaria/alunos', [
                'alunos' => $alunosPaginados,
                'pagina_atual' => $pagina,
                'total_paginas' => $totalPaginas,
                'total_alunos' => $totalAlunos
            ]);
        } catch (\Throwable $e) {
            error_log('Erro ao listar alunos: ' . $e->getMessage());
            header('Location: /TCC-etec/secretaria?error=' . urlencode('Erro ao carregar alunos.'));
            exit;
        }
    }

    public function visualizarAluno(int $alunoId): void
    {
        Auth::start();
        Auth::requireRole('secretaria');

        try {
            $perfil = $this->studentModel->fullProfile($alunoId);

            if (!$perfil) {
                header('Location: /TCC-etec/secretaria/alunos?error=' . urlencode('Aluno não encontrado.'));
                exit;
            }

            $emprestimos = $this->studentModel->activeLoans($alunoId);
            $historico = $this->studentModel->loanHistory($alunoId);
            $solicitacoes = $this->studentModel->solicitations($alunoId);

            echo $this->view('secretaria/aluno-detalhes', [
                'perfil' => $perfil,
                'emprestimos' => $emprestimos,
                'historico' => $historico,
                'solicitacoes' => $solicitacoes
            ]);
        } catch (\Throwable $e) {
            error_log('Erro ao visualizar aluno: ' . $e->getMessage());
            header('Location: /TCC-etec/secretaria/alunos?error=' . urlencode('Erro ao carregar aluno.'));
            exit;
        }
    }

    public function auditoria(): void
    {
        Auth::start();
        Auth::requireRole('secretaria');

        try {
            $pagina = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $limite = 50;
            $offset = ($pagina - 1) * $limite;

            $auditoria = Database::connection()->prepare('
                SELECT
                    *
                FROM auditoria
                ORDER BY data_hora DESC
                LIMIT ? OFFSET ?
            ');

            $auditoria->bindValue(1, $limite, \PDO::PARAM_INT);
            $auditoria->bindValue(2, $offset, \PDO::PARAM_INT);
            $auditoria->execute();
            $registros = $auditoria->fetchAll(\PDO::FETCH_ASSOC) ?: [];

            $totalStmt = Database::connection()->query('SELECT COUNT(*) as total FROM auditoria');
            $totalRegistros = (int)($totalStmt->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0);
            $totalPaginas = ceil($totalRegistros / $limite);

            echo $this->view('secretaria/auditoria', [
                'registros' => $registros,
                'pagina_atual' => $pagina,
                'total_paginas' => $totalPaginas,
                'total_registros' => $totalRegistros
            ]);
        } catch (\Throwable $e) {
            error_log('Erro ao carregar auditoria: ' . $e->getMessage());
            header('Location: /TCC-etec/secretaria?error=' . urlencode('Erro ao carregar auditoria.'));
            exit;
        }
    }

    public function relatorios(): void
    {
        Auth::start();
        Auth::requireRole('secretaria');

        try {
            $estatisticas = $this->funcionarioModel->obterEstatisticasBiblioteca();
            $emprestimosAtrasados = $this->funcionarioModel->buscarEmprestimosAtrasados();

            echo $this->view('secretaria/relatorios', [
                'estatisticas' => $estatisticas,
                'emprestimos_atrasados' => $emprestimosAtrasados
            ]);
        } catch (\Throwable $e) {
            error_log('Erro ao carregar relatórios: ' . $e->getMessage());
            header('Location: /TCC-etec/secretaria?error=' . urlencode('Erro ao carregar relatórios.'));
            exit;
        }
    }
}
