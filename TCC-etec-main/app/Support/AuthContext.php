<?php

namespace App\Support;

class AuthContext
{
    public function user(): ?array
    {
        if (!empty($_REQUEST['auth_user']) && is_array($_REQUEST['auth_user'])) {
            return $_REQUEST['auth_user'];
        }

        if (Auth::check()) {
            return Auth::user();
        }

        return null;
    }

    public function id(): ?int
    {
        $user = $this->user();
        return isset($user['id']) ? (int)$user['id'] : null;
    }

    public function role(): ?string
    {
        $user = $this->user();
        return isset($user['papel']) ? strtolower((string)$user['papel']) : (isset($user['role']) ? strtolower((string)$user['role']) : null);
    }
}
