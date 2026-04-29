<?php

namespace App\Api\Middleware;

use App\Core\MiddlewareInterface;
use App\Core\Request;
use App\Support\RoleManager;

class RoleMiddleware implements MiddlewareInterface
{
    // ✅ Roles agora centralizadas em RoleManager

    private ?string $parameter;

    public function __construct(?string $parameter = null)
    {
        $this->parameter = $parameter;
    }

    public function handle(Request $request): bool
    {
        $user = $request->user ?? ($_REQUEST['auth_user'] ?? null);
        if (!is_array($user) || empty($user['role'])) {
            response_json(['ok' => false, 'message' => 'Usuário não autenticado.'], 401);
            return false;
        }

        if ($this->parameter === null || trim($this->parameter) === '') {
            return true;
        }

        $allowedRoles = array_map('trim', explode(',', $this->parameter));
        $role = strtolower((string)$user['role']);

        if (!$this->isRoleAllowed($role, $allowedRoles)) {
            response_json(['ok' => false, 'message' => 'Permissão insuficiente.'], 403);
            return false;
        }

        return true;
    }

    private function isRoleAllowed(string $role, array $allowedRoles): bool
    {
        foreach ($allowedRoles as $allowedRole) {
            $allowedRole = strtolower($allowedRole);
            
            // ✅ Usa RoleManager para checagem centralizada
            if (RoleManager::isInGroup($role, $allowedRole)) {
                return true;
            }
        }

        return false;
    }
}
