<?php

namespace App\Api\Middleware;

use App\Core\MiddlewareInterface;
use App\Core\Request;
use App\Services\JwtService;

class AuthMiddleware implements MiddlewareInterface
{
    private ?string $parameter;

    public function __construct(?string $parameter = null)
    {
        $this->parameter = $parameter;
    }

    public function handle(Request $request): bool
    {
        $token = $this->resolveToken($request);
        if ($token === null) {
            response_json(['ok' => false, 'message' => 'Token de autenticação não fornecido.'], 401);
            return false;
        }

        $jwt = new JwtService();
        $payload = $jwt->validateAccessToken($token);
        if ($payload === null) {
            response_json(['ok' => false, 'message' => 'Token inválido ou expirado.'], 401);
            return false;
        }

        $request->user = $payload;
        $_REQUEST['auth_user'] = $payload;

        return true;
    }

    private function resolveToken(Request $request): ?string
    {
        $authorization = $request->header('Authorization');
        if ($authorization === null) {
            $authorization = $request->header('authorization');
        }

        if ($authorization !== null) {
            if (str_starts_with($authorization, 'Bearer ')) {
                return trim(substr($authorization, 7));
            }
            return trim($authorization);
        }

        if (!empty($_GET['access_token'])) {
            return trim((string)$_GET['access_token']);
        }

        return null;
    }
}
