<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Application;

$app = new Application();
$router = $app->getRouter();

$router->post('/api/login', 'ApiAuthController@login');
$router->post('/api/refresh', 'ApiAuthController@refresh');
$router->post('/api/logout', 'ApiAuthController@logout');
$router->get('/api/check', 'ApiAuthController@check');

$router->get('/api/admin/usuarios', 'ApiAdminController@listarUsuarios');
$router->post('/api/admin/usuarios', 'ApiAdminController@criarUsuario');
$router->post('/api/admin/usuarios/deletar', 'ApiAdminController@deletarUsuario');

$router->get('/api/admin/livros', 'ApiAdminController@listarLivros');
$router->post('/api/admin/livros', 'ApiAdminController@criarLivro');
$router->post('/api/admin/livros/deletar', 'ApiAdminController@deletarLivro');

$router->get('/api/admin/noticias', 'ApiAdminController@listarNoticias');
$router->post('/api/admin/noticias', 'ApiAdminController@criarNoticia');
$router->post('/api/admin/noticias/deletar', 'ApiAdminController@deletarNoticia');


$router->get('/api/teste', function () {
    header('Content-Type: application/json');
    echo json_encode(['ok' => true, 'msg' => 'API funcionando']);
});


$app->run();