<?php

namespace App\Http\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Domain\FaleConosco\Contato;
use App\Domain\FaleConosco\DadosPessoais;
use App\Domain\FaleConosco\Duvida;
use App\Domain\FaleConosco\Feedback;
use App\Domain\FaleConosco\ProblemaComLogin;

class FaleConoscoController extends Controller
{
    public function index(): void
    {
        $resultado = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['_csrf'] ?? null;
            if (!Csrf::validateToken($token)) {
                $resultado = 'Requisição inválida (CSRF).';
            } else {
                $resultado = $this->processarContato(
                    (string)($_POST['tipo'] ?? ''),
                    trim((string)($_POST['nome'] ?? '')),
                    trim((string)($_POST['email'] ?? '')),
                    trim((string)($_POST['mensagem'] ?? ''))
                );
            }
        }

        $csrf = Csrf::generateToken();

        echo $this->view('FaleConosco/faleConosco', [
            'resultado' => $resultado,
            'csrf' => $csrf,
        ]);
    }

    private function processarContato(string $tipo, string $nome, string $email, string $mensagem): string
    {
        $contato = $this->criarContato($tipo, $nome, $email, $mensagem);
        if ($contato === null) {
            return 'Tipo de contato inválido.';
        }

        return $contato->processar();
    }

    private function criarContato(string $tipo, string $nome, string $email, string $mensagem): ?Contato
    {
        if ($tipo === 'duvida') {
            return new Duvida($nome, $email, $mensagem);
        }

        if ($tipo === 'reclamacao') {
            return new Feedback($nome, $email, $mensagem, 'Reclamação');
        }

        if ($tipo === 'elogio') {
            return new Feedback($nome, $email, $mensagem, 'Elogio');
        }

        if ($tipo === 'login') {
            return new ProblemaComLogin($nome, $email, $mensagem);
        }

        if ($tipo === 'dados') {
            return new DadosPessoais($nome, $email, $mensagem);
        }

        return null;
    }
}
