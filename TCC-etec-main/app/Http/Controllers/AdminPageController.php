<?php

namespace App\Http\Controllers;

use App\Core\Controller;
use App\Support\Auth;

class AdminPageController extends Controller
{
    public function __invoke(): void
    {
        Auth::start();
        Auth::requireAuth();
        Auth::requireRole('admin');

        $usuario = Auth::user() ?? [];
        $usuarioNome = (string)($usuario['nome'] ?? $usuario['nome_completo'] ?? 'Administrador');

        // Entrada estável do /admin sem alterar a lógica existente de negócio
        // dos demais controllers (livros, notícias, painel completo etc.).
        echo $this->view('admin/painel', [
            'usuario_nome' => $usuarioNome,
            'funcionario' => null,
            'contadores' => [
                'total_livros' => 0,
                'total_noticias' => 0,
                'solicitacoes_pendentes' => 0,
                'emprestimos_atrasados' => 0,
            ],
            'estatisticas' => [],
        ]);
    }
}
