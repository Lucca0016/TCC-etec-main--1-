<?php

namespace App\Core;

use App\Api\Middleware\AuthMiddleware;
use App\Api\Middleware\RoleMiddleware;

class MiddlewareManager
{
    private array $map = [
        'auth' => AuthMiddleware::class,
        'role' => RoleMiddleware::class,
    ];

    public function handle(array $definitions, Request $request): bool
    {
        foreach ($definitions as $definition) {
            $middleware = $this->resolve($definition);
            if (!$middleware->handle($request)) {
                return false;
            }
        }

        return true;
    }

    private function resolve(string $definition): MiddlewareInterface
    {
        $parameter = null;
        if (str_contains($definition, ':')) {
            [$name, $parameter] = explode(':', $definition, 2);
            $name = trim($name);
            $parameter = trim($parameter);
        } else {
            $name = trim($definition);
        }

        if ($name === '') {
            throw new \RuntimeException('Nome de middleware inválido.');
        }

        if (!isset($this->map[$name])) {
            throw new \RuntimeException("Middleware '{$name}' não está registrado.");
        }

        $class = $this->map[$name];
        if (!class_exists($class)) {
            throw new \RuntimeException("Classe de middleware '{$class}' não encontrada.");
        }

        return new $class($parameter);
    }
}
