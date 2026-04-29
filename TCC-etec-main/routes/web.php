<?php

use App\Core\Application;
use App\Support\Frontend;

$app = new Application();
$router = $app->router();

$router->get('/', function () {
    echo Frontend::renderLanding();
});

$router->get('/index.html', function () {
    header('Location: /TCC-etec/');
    exit;
});

$router->get('/login', 'AuthController@showForm');
$router->post('/login', 'AuthController@authenticate');
$router->get('/logout', 'AuthController@logout');
$router->get('/admin', 'AdminPageController@__invoke');

// Módulos de CMS/Gestão
$router->get('/admin/cms/usuarios', 'AdminController@gerenciarUsuarios');
$router->post('/admin/cms/usuario/criar', 'AdminController@criarUsuario');
$router->post('/admin/cms/usuario/{id}/excluir', 'AdminController@excluirUsuario');
$router->get('/admin/cms/noticias', 'AdminController@gerenciarNoticias');
$router->post('/admin/cms/noticia/criar', 'AdminController@criarNoticia');
$router->post('/admin/cms/noticia/{id}/excluir', 'AdminController@excluirNoticia');

$router->get('/aluno', 'DashboardAlunoController@index');
$router->get('/aluno/biblioteca', 'DashboardAlunoController@biblioteca');
$router->post('/aluno/emprestimo/solicitar', 'DashboardAlunoController@solicitarEmprestimo');
$router->get('/aluno/perfil', 'DashboardAlunoController@meusPerfis');
$router->get('/aluno/solicitacoes', 'DashboardAlunoController@minhasSolicitacoes');
$router->post('/aluno/solicitacao/criar', 'DashboardAlunoController@criarSolicitacao');

$router->get('/biblioteca', 'BibliotecaController@index');
$router->get('/biblioteca/catalogo', 'BibliotecaController@catalogo');
$router->get('/biblioteca/livro/{id}', 'BibliotecaController@detalhes');
$router->get('/biblioteca/detalhes/{id}', 'BibliotecaController@detalhes');
$router->post('/biblioteca/solicitar', 'BibliotecaController@solicitarEmprestimo');
$router->get('/biblioteca/buscar', 'BibliotecaController@buscar');

$router->get('/contato', function () {
    echo view('contato/index');
});

$router->get('/faleconosco', 'FaleConoscoController@index');
$router->post('/faleconosco', 'FaleConoscoController@index');

$router->get('/cursos', 'CursosController@index');
$router->get('/vestibular', 'VestibularController@index');
$router->post('/vestibular/inscricao', 'VestibularController@inscrever');
$router->get('/vestibular/sucesso', function () {
    echo view('vestibular/sucesso');
});
$router->get('/cursos.html', function () {
    header('Location: /TCC-etec/cursos');
    exit;
});

$router->get('/emprestimos', 'EmprestimoController@index');
$router->post('/emprestimo/criar', 'EmprestimoController@criar');
$router->post('/emprestimo/renovar', 'EmprestimoController@renovar');
$router->get('/emprestimo/{id}', 'EmprestimoController@detalhes');

$router->get('/admin/livros', 'AdminController@gerenciarLivros');
$router->post('/admin/livro/criar', 'AdminController@criarLivro');
$router->post('/admin/livro/{id}/editar', 'AdminController@editarLivro');
$router->post('/admin/livro/{id}/deletar', 'AdminController@deletarLivro');
$router->get('/admin/noticias', 'AdminController@gerenciarNoticias');
$router->post('/admin/noticia/criar', 'AdminController@criarNoticia');
$router->post('/admin/noticia/{id}/editar', 'AdminController@editarNoticia');
$router->post('/admin/noticia/{id}/deletar', 'AdminController@deletarNoticia');

$router->get('/secretaria', 'SecretariaController@painel');
$router->get('/secretaria/solicitacoes', 'SecretariaController@gerenciarSolicitacoes');
$router->post('/secretaria/solicitacao/{id}/atualizar', 'SecretariaController@atualizarSolicitacao');
$router->get('/secretaria/emprestimos', 'SecretariaController@gerenciarEmprestimos');
$router->post('/secretaria/emprestimo/{id}/devolver', 'SecretariaController@registrarDevolucao');
$router->get('/secretaria/alunos', 'SecretariaController@gerenciarAlunos');
$router->get('/secretaria/aluno/{id}', 'SecretariaController@visualizarAluno');
$router->get('/secretaria/auditoria', 'SecretariaController@auditoria');
$router->get('/secretaria/relatorios', 'SecretariaController@relatorios');

$router->post('/api/login', 'ApiAuthController@login');
$router->post('/api/refresh', 'ApiAuthController@refresh');
$router->post('/api/logout', 'ApiAuthController@logout', ['auth']);
$router->get('/api/auth/check', 'ApiAuthController@check', ['auth']);

$router->get('/api/usuarios', 'ApiAdminController@listarUsuarios', ['auth', 'role:admin']);
$router->post('/api/usuarios', 'ApiAdminController@criarUsuario', ['auth', 'role:admin']);
$router->post('/api/usuarios/{usuarioId}/deletar', 'ApiAdminController@deletarUsuario', ['auth', 'role:admin']);

$router->get('/api/livros', 'ApiAdminController@listarLivros', ['auth', 'role:admin']);
$router->post('/api/livros', 'ApiAdminController@criarLivro', ['auth', 'role:admin']);
$router->post('/api/livros/{livroId}/deletar', 'ApiAdminController@deletarLivro', ['auth', 'role:admin']);

$router->get('/api/noticias', 'ApiAdminController@listarNoticias', ['auth', 'role:admin']);
$router->post('/api/noticias', 'ApiAdminController@criarNoticia', ['auth', 'role:admin']);
$router->post('/api/noticias/{noticiaId}/deletar', 'ApiAdminController@deletarNoticia', ['auth', 'role:admin']);

$router->get('/api/auditoria', 'ApiAdminController@auditoria', ['auth', 'role:admin']);

// Migrações API
$router->get('/api/migrations/status', 'AdminController@verificarMigracoes');
$router->post('/api/migrations/run', 'AdminController@executarMigracoes');

return $app;
