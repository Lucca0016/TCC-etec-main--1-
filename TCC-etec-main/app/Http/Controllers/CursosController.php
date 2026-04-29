<?php

namespace App\Http\Controllers;

use App\Core\Controller;
use App\Models\Curso;

class CursosController extends Controller
{
    private Curso $cursoModel;

    public function __construct()
    {
        $this->cursoModel = new Curso();
    }

    public function index(): void
    {
        try {
            $cursos = $this->cursoModel->listar();
        } catch (\Throwable $e) {
            $cursos = [];
        }

        echo $this->view('Cursos', ['cursos' => $cursos], 'html');
    }
}
