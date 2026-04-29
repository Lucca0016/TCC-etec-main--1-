<?php

namespace App\Controllers;

use App\Models\Livro;
use App\Config\Database;

class CatalogoController {
    private $livroModel;

    public function __construct() {
        $db = Database::getInstance()->getConnection();
        $this->livroModel = new Livro($db);
    }

    public function listarLivros() {
        $filtros = [
            'titulo' => $_GET['titulo'] ?? '',
            'autor' => $_GET['autor'] ?? '',
            'categoria' => $_GET['categoria'] ?? '',
            'ano' => $_GET['ano'] ?? '',
            'disponibilidade' => $_GET['disponibilidade'] ?? 'todos',
            'ordenacao' => $_GET['ordenacao'] ?? 'titulo',
            'pagina' => $_GET['pagina'] ?? 1
        ];

        return $this->livroModel->buscar($filtros);
    }

    public function obterMetadadosCatalogo() {
        return [
            'categorias' => $this->livroModel->obterCategorias(),
            'anos' => $this->livroModel->obterAnos()
        ];
    }

    public function obterDetalhe($id) {
        return $this->livroModel->obterPorId($id);
    }
}
?>
