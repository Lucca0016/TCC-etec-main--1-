<?php


namespace App\Api\Controllers;

use App\Services\AuthService;
use App\Api\Services\JwtService;

class ApiAuthController
{
    private AuthService $auth;

    public function __construct()
    {
        $this->auth = new AuthService();
    }

    public function login()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $email = $data['email'] ?? '';
        $senha = $data['senha'] ?? '';
        $perfil = $data['perfil'] ?? null;

        $fingerprint = AuthService::fingerprintFromGlobals();

        $result = $this->auth->attempt($email, $senha, $perfil, $fingerprint);

        if (!$result['ok']) {
            http_response_code(401);
            echo json_encode([
                'error' => $result['message']
            ]);
            return;
        }

        // 🔐 AQUI entra o JWT
        $token = JwtService::generate($result['user']);

        echo json_encode([
            'token' => $token,
            'user' => $result['user']
        ]);
    }
}