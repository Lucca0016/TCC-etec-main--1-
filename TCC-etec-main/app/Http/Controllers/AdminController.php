<?php

namespace App\Http\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Livro;
use App\Models\Noticia;
use App\Models\Funcionario;
use App\Support\Auth;
use PDO;

class AdminController extends Controller
{
    private Livro $livroModel;
    private Noticia $noticiaModel;
    private Funcionario $funcionarioModel;
    private \App\Models\User $userModel;

    public function __construct()
    {
        $this->livroModel = new Livro();
        $this->noticiaModel = new Noticia();
        $this->funcionarioModel = new Funcionario();
        $this->userModel = new \App\Models\User();
    }

    public function painel(): void
    {
        Auth::start();
        Auth::requireRole('admin');

        $usuario = Auth::user();
        $usuarioId = $usuario['id'];
        $usuarioNome = $usuario['nome'];

        try {
            $funcionario = $this->funcionarioModel->obterCompleto($usuarioId);

            if (!$funcionario) {
                header('Location: /TCC-etec/login?error=' . urlencode('Acesso administrativo não autorizado.'));
                exit;
            }

            $totalLivros = $this->livroModel->contar();
            $totalNoticias = $this->noticiaModel->contar();
            $solicitacoesPendentes = $this->funcionarioModel->buscarSolicitacoesPorStatus('pendente');
            $estatisticas = $this->funcionarioModel->obterEstatisticasBiblioteca();

            $contadores = [
                'total_livros' => $totalLivros,
                'total_noticias' => $totalNoticias,
                'solicitacoes_pendentes' => count($solicitacoesPendentes),
                'emprestimos_atrasados' => count($this->funcionarioModel->buscarEmprestimosAtrasados())
            ];

            echo $this->view('admin/painel', [
                'usuario_nome' => $usuarioNome,
                'funcionario' => $funcionario,
                'contadores' => $contadores,
                'estatisticas' => $estatisticas
            ]);
        } catch (\Throwable $e) {
            error_log('Erro no painel administrativo: ' . $e->getMessage());
            header('Location: /TCC-etec/login?error=' . urlencode('Erro ao carregar painel.'));
            exit;
        }
    }

    public function gerenciarLivros(): void
    {
        Auth::start();
        Auth::requireRole('admin');

        try {
            $pagina = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $limite = 25;
            $offset = ($pagina - 1) * $limite;

            $livros = $this->livroModel->listarComPaginacao($limite, $offset);
            $totalLivros = $this->livroModel->contar();
            $totalPaginas = ceil($totalLivros / $limite);

            echo $this->view('admin/livros', [
                'livros' => $livros,
                'pagina_atual' => $pagina,
                'total_paginas' => $totalPaginas,
                'total_livros' => $totalLivros
            ]);
        } catch (\Throwable $e) {
            error_log('Erro ao listar livros: ' . $e->getMessage());
            header('Location: /TCC-etec/admin?error=' . urlencode('Erro ao carregar livros.'));
            exit;
        }
    }

    public function criarLivro(): void
    {
        Auth::start();
        Auth::requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /TCC-etec/admin/livros');
            exit;
        }

        $titulo = isset($_POST['titulo']) ? trim((string)$_POST['titulo']) : '';
        $autor = isset($_POST['autor']) ? trim((string)$_POST['autor']) : '';
        $editora = isset($_POST['editora']) ? trim((string)$_POST['editora']) : '';
        $isbn = isset($_POST['isbn']) ? trim((string)$_POST['isbn']) : '';
        $link_pdf = isset($_POST['link_pdf']) ? trim((string)$_POST['link_pdf']) : '';
        $copias = isset($_POST['copias']) ? (int)$_POST['copias'] : 0;

        if (empty($titulo) || empty($autor) || $copias <= 0) {
            header('Location: /TCC-etec/admin/livros?error=' . urlencode('Preencha os campos obrigatórios.'));
            exit;
        }

        $imagem_capa = null;
        if (isset($_FILES['imagem_capa']) && $_FILES['imagem_capa']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['imagem_capa']['name'], PATHINFO_EXTENSION);
            $nomeArquivo = uniqid('capa_livro_') . '.' . $ext;
            $caminho = __DIR__ . '/../../../public/uploads/livros/' . $nomeArquivo;
            if (!is_dir(dirname($caminho))) {
                mkdir(dirname($caminho), 0777, true);
            }
            if (move_uploaded_file($_FILES['imagem_capa']['tmp_name'], $caminho)) {
                $imagem_capa = '/uploads/livros/' . $nomeArquivo;
            }
        }

        try {
            $stmt = Database::connection()->prepare('
                INSERT INTO biblioteca_livros
                (titulo, autor, editora, isbn, copias_totais, copias_disponiveis, disponivel, imagem_capa, link_pdf, criado_em)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ');

            $disponivel = $copias > 0 ? 1 : 0;

            if ($stmt->execute([$titulo, $autor, $editora, $isbn, $copias, $copias, $disponivel, $imagem_capa, $link_pdf])) {
                header('Location: /TCC-etec/admin/livros?success=' . urlencode('Livro criado com sucesso.'));
            } else {
                header('Location: /TCC-etec/admin/livros?error=' . urlencode('Erro ao criar livro.'));
            }
        } catch (\Throwable $e) {
            error_log('Erro ao criar livro: ' . $e->getMessage());
            header('Location: /TCC-etec/admin/livros?error=' . urlencode('Erro ao processar criação.'));
        }
        exit;
    }

    public function editarLivro(int $livroId): void
    {
        Auth::start();
        Auth::requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /TCC-etec/admin/livros');
            exit;
        }

        try {
            $livro = $this->livroModel->obterPorId($livroId);

            if (!$livro) {
                header('Location: /TCC-etec/admin/livros?error=' . urlencode('Livro não encontrado.'));
                exit;
            }

            $titulo = isset($_POST['titulo']) ? trim((string)$_POST['titulo']) : $livro['titulo'];
            $autor = isset($_POST['autor']) ? trim((string)$_POST['autor']) : $livro['autor'];
            $editora = isset($_POST['editora']) ? trim((string)$_POST['editora']) : $livro['editora'];
            $isbn = isset($_POST['isbn']) ? trim((string)$_POST['isbn']) : $livro['isbn'];
            $link_pdf = isset($_POST['link_pdf']) ? trim((string)$_POST['link_pdf']) : ($livro['link_pdf'] ?? '');
            $copias = isset($_POST['copias']) ? (int)$_POST['copias'] : $livro['copias_totais'];
            $copias_disponiveis = isset($_POST['copias_disponiveis']) ? (int)$_POST['copias_disponiveis'] : $livro['copias_disponiveis'];

            $imagem_capa = $livro['imagem_capa'] ?? null;
            if (isset($_FILES['imagem_capa']) && $_FILES['imagem_capa']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['imagem_capa']['name'], PATHINFO_EXTENSION);
                $nomeArquivo = uniqid('capa_livro_') . '.' . $ext;
                $caminho = __DIR__ . '/../../../public/uploads/livros/' . $nomeArquivo;
                if (!is_dir(dirname($caminho))) {
                    mkdir(dirname($caminho), 0777, true);
                }
                if (move_uploaded_file($_FILES['imagem_capa']['tmp_name'], $caminho)) {
                    $imagem_capa = '/uploads/livros/' . $nomeArquivo;
                }
            }

            $stmt = Database::connection()->prepare('
                UPDATE biblioteca_livros
                SET titulo = ?, autor = ?, editora = ?, isbn = ?, copias_totais = ?, copias_disponiveis = ?, disponivel = ?, imagem_capa = ?, link_pdf = ?
                WHERE id = ?
            ');

            $disponivel = $copias > 0 ? 1 : 0;

            if ($stmt->execute([$titulo, $autor, $editora, $isbn, $copias, $copias_disponiveis, $disponivel, $imagem_capa, $link_pdf, $livroId])) {
                header('Location: /TCC-etec/admin/livros?success=' . urlencode('Livro atualizado com sucesso.'));
            } else {
                header('Location: /TCC-etec/admin/livros?error=' . urlencode('Erro ao atualizar livro.'));
            }
        } catch (\Throwable $e) {
            error_log('Erro ao editar livro: ' . $e->getMessage());
            header('Location: /TCC-etec/admin/livros?error=' . urlencode('Erro ao processar edição.'));
        }
        exit;
    }

    public function deletarLivro(int $livroId): void
    {
        Auth::start();
        Auth::requireRole('admin');

        try {
            $livro = $this->livroModel->obterPorId($livroId);

            if (!$livro) {
                header('Location: /TCC-etec/admin/livros?error=' . urlencode('Livro não encontrado.'));
                exit;
            }

            $stmt = Database::connection()->prepare('DELETE FROM biblioteca_livros WHERE id = ?');

            if ($stmt->execute([$livroId])) {
                header('Location: /TCC-etec/admin/livros?success=' . urlencode('Livro removido.'));
            } else {
                header('Location: /TCC-etec/admin/livros?error=' . urlencode('Erro ao remover livro.'));
            }
        } catch (\Throwable $e) {
            error_log('Erro ao deletar livro: ' . $e->getMessage());
            header('Location: /TCC-etec/admin/livros?error=' . urlencode('Erro ao processar exclusão.'));
        }
        exit;
    }

    public function gerenciarUsuarios(): void
    {
        Auth::start();
        Auth::requireRole('admin');
        
        $usuarios = $this->userModel->paginate(0, 1000);
        
        echo $this->view('admin/usuarios', [
            'usuarios' => $usuarios
        ]);
    }

    public function criarUsuario(): void
    {
        Auth::start();
        Auth::requireRole('admin');

        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';
        $nome = $_POST['nome_completo'] ?? '';
        $papel = $_POST['papel'] ?? 'aluno';

        if (!preg_match('/\b[a-zA-Z0-9._%+-]+@(edu\.com\.br|edu\.gov\.br|fatec\.sp\.gov\.br|etec\.sp\.gov\.br)\b/i', $email)) {
            header('Location: /TCC-etec/admin/cms/usuarios?error=' . urlencode('Email fora do domínio educacional permitido.'));
            exit;
        }

        $senhaHash = password_hash($senha, PASSWORD_BCRYPT);
        
        $this->userModel->create([
            'email' => $email,
            'nome_completo' => $nome,
            'papel' => $papel,
            'senha_hash' => $senhaHash,
            'ativo' => 1
        ]);
        
        header('Location: /TCC-etec/admin/cms/usuarios?success=1');
    }

    public function excluirUsuario($id): void
    {
        Auth::start();
        Auth::requireRole('admin');

        $this->userModel->delete($id);
        
        header('Location: /TCC-etec/admin/cms/usuarios?deleted=1');
    }

    public function gerenciarNoticias(): void
    {
        Auth::start();
        Auth::requireRole('admin');
        
        $noticias = $this->noticiaModel->all();
        
        echo $this->view('admin/noticias', [
            'noticias' => $noticias
        ]);
    }

    public function criarNoticia(): void
    {
        Auth::start();
        Auth::requireRole('admin');

        $dados = [
            'titulo' => $_POST['titulo'] ?? '',
            'conteudo' => $_POST['conteudo'] ?? '',
            'slug' => strtolower(str_replace(' ', '-', preg_replace('/[^A-Za-z0-9 ]/', '', $_POST['titulo'] ?? ''))),
            'imagem_capa' => null,
            'status_carrossel' => isset($_POST['status_carrossel']) ? 1 : 0
        ];
        
        if (isset($_FILES['imagem_capa']) && $_FILES['imagem_capa']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['imagem_capa']['name'], PATHINFO_EXTENSION);
            $nomeArquivo = uniqid('capa_') . '.' . $ext;
            $caminho = __DIR__ . '/../../../public/uploads/noticias/' . $nomeArquivo;
            if (!is_dir(dirname($caminho))) {
                mkdir(dirname($caminho), 0777, true);
            }
            if (move_uploaded_file($_FILES['imagem_capa']['tmp_name'], $caminho)) {
                $dados['imagem_capa'] = '/uploads/noticias/' . $nomeArquivo;
            }
        }

        $this->noticiaModel->criar($dados);
        
        header('Location: /TCC-etec/admin/cms/noticias?success=1');
    }

    public function excluirNoticia($id): void
    {
        Auth::start();
        Auth::requireRole('admin');

        $this->noticiaModel->deletar($id);
        
        header('Location: /TCC-etec/admin/cms/noticias?deleted=1');
    }

    /**
     * API: Verificar status das migrações
     */
    public function verificarMigracoes(): void
    {
        header('Content-Type: application/json');
        
        try {
            $pdo = Database::connection();
            
            // Verificar se coluna link_pdf existe na tabela biblioteca_livros
            $result = $pdo->query("
                SELECT COUNT(*) as count 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_NAME = 'biblioteca_livros' 
                AND COLUMN_NAME = 'link_pdf'
            ")->fetch(PDO::FETCH_ASSOC);

            $linkPdfExiste = ($result['count'] ?? 0) > 0;

            echo json_encode([
                'sucesso' => true,
                'link_pdf_existe' => $linkPdfExiste,
                'mensagem' => $linkPdfExiste ? 'Migrações completas' : 'Migrações pendentes'
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'sucesso' => false,
                'erro' => 'Erro ao verificar migrações: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Executar migrações
     */
    public function executarMigracoes(): void
    {
        header('Content-Type: application/json');

        // Não requer autenticação para migração inicial, mas pode adicionar validação
        try {
            $pdo = Database::connection();

            $migrations = [
                [
                    'name' => 'Adicionar coluna link_pdf em biblioteca_livros',
                    'sql' => 'ALTER TABLE biblioteca_livros ADD COLUMN IF NOT EXISTS link_pdf VARCHAR(500) NULL DEFAULT NULL AFTER imagem_capa'
                ],
            ];

            $results = [];
            $successCount = 0;
            $errorCount = 0;

            foreach ($migrations as $migration) {
                try {
                    $pdo->exec($migration['sql']);
                    $results[] = [
                        'name' => $migration['name'],
                        'status' => 'sucesso',
                        'mensagem' => 'Executada com sucesso'
                    ];
                    $successCount++;
                } catch (\Exception $e) {
                    // Se a coluna já existe, é um sucesso
                    if (stripos($e->getMessage(), 'Duplicate column') !== false) {
                        $results[] = [
                            'name' => $migration['name'],
                            'status' => 'sucesso',
                            'mensagem' => 'Coluna já existe'
                        ];
                        $successCount++;
                    } else {
                        $results[] = [
                            'name' => $migration['name'],
                            'status' => 'erro',
                            'mensagem' => $e->getMessage()
                        ];
                        $errorCount++;
                    }
                }
            }

            echo json_encode([
                'sucesso' => $errorCount === 0,
                'migrações' => $results,
                'resumo' => [
                    'total' => count($migrations),
                    'sucesso' => $successCount,
                    'erro' => $errorCount
                ]
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'sucesso' => false,
                'erro' => 'Erro ao executar migrações: ' . $e->getMessage()
            ]);
        }
    }
}
