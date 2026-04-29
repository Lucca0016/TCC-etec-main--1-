<?php
require_once '../models/Cursos.php';

class CursosController {
    public function index() {
        $cursos = Curso::listar();
        require '../views/cursos.php';
    }
}