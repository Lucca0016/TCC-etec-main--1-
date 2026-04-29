<?php

namespace App\Http\Controllers;

use App\Core\Controller;
use App\Models\Vestibular;

class VestibularController extends Controller
{
    public function index(): void
    {
        $model = new Vestibular();
        $cursos = $model->listarCursos();
        echo $this->view('vestibular/index', [
            'cursos' => $cursos,
            'errors' => [],
            'old' => [],
        ]);
    }

    public function inscrever(): void
    {
        $dados = [
            'nome' => trim($_POST['nome'] ?? ''),
            'cpf' => trim($_POST['cpf'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'telefone' => trim($_POST['telefone'] ?? ''),
            'curso' => trim($_POST['curso'] ?? ''),
            'turno' => trim($_POST['turno'] ?? ''),
            'modalidade' => trim($_POST['modalidade'] ?? ''),
            'lgpd' => isset($_POST['lgpd']) ? '1' : '0',
        ];

        $errors = $this->validate($dados);
        if (!empty($errors)) {
            $model = new Vestibular();
            echo $this->view('vestibular/index', [
                'cursos' => $model->listarCursos(),
                'errors' => $errors,
                'old' => $dados,
            ]);
            return;
        }

        $model = new Vestibular();
        $model->salvarInscricao($dados);

        header('Location: /TCC-etec/vestibular/sucesso');
        exit;
    }

    private function validate(array $dados): array
    {
        $errors = [];

        if ($dados['nome'] === '') {
            $errors['nome'] = 'O nome completo é obrigatório';
        }

        $cpfApenasDigitos = preg_replace('/\D/', '', $dados['cpf']);
        if ($cpfApenasDigitos === '' || strlen($cpfApenasDigitos) !== 11) {
            $errors['cpf'] = 'CPF inválido. Informe 11 dígitos.';
        }

        if ($dados['email'] === '' || !filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'E-mail inválido.';
        }

        if ($dados['telefone'] === '' || !preg_match('/^[\d\(\)\s\-\+]{10,}$/', $dados['telefone'])) {
            $errors['telefone'] = 'Telefone inválido.';
        }

        if ($dados['curso'] === '') {
            $errors['curso'] = 'Selecione o curso desejado.';
        }

        if ($dados['turno'] === '') {
            $errors['turno'] = 'Selecione o turno.';
        }

        if ($dados['modalidade'] === '') {
            $errors['modalidade'] = 'Selecione a modalidade.';
        }

        if ($dados['lgpd'] !== '1') {
            $errors['lgpd'] = 'É necessário concordar com a LGPD.';
        }

        return $errors;
    }
}
