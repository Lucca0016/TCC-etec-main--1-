<?php

namespace App\Http\Controllers;

use App\Core\Controller;

class NoticiasController extends Controller
{
    public function index(): void
    {
        echo $this->view('Noticias/noticias');
    }

    public function show(string $slug): void
    {
        $safeSlug = preg_replace('/[^a-z0-9\-]/i', '', $slug) ?: 'noticias';
        $view = 'Noticias/' . $safeSlug;

        $viewPath = resource_path('views/' . $view . '.php');
        if (!file_exists($viewPath)) {
            http_response_code(404);
            echo 'Notícia não encontrada';
            return;
        }

        echo $this->view($view);
    }
}
