<?php

namespace App\Core;

use Closure;
use App\Core\Request;
use App\Core\MiddlewareManager;

class Router
{
    /** @var array<int, array{methods:array<int,string>,pattern:string,handler:mixed,middlewares:array<int,string>}> */
    private array $routes = [];

    public function get(string $uri, $handler, array $middlewares = []): self
    {
        return $this->add(['GET'], $uri, $handler, $middlewares);
    }

    public function post(string $uri, $handler, array $middlewares = []): self
    {
        return $this->add(['POST'], $uri, $handler, $middlewares);
    }

    public function match(array $methods, string $uri, $handler, array $middlewares = []): self
    {
        $upper = array_map('strtoupper', $methods);
        return $this->add($upper, $uri, $handler, $middlewares);
    }

    private function add(array $methods, string $uri, $handler, array $middlewares = []): self
    {
        $normalized = $uri === '/' ? '/' : rtrim($uri, '/');
        $regex = preg_replace_callback('/{([^}]+)}/', function ($matches) {
            $param = trim($matches[1]);
            return '(?P<' . $param . '>[^/]+)';
        }, $normalized);

        if ($regex !== '/') {
            $regex .= '/?';
        }

        $pattern = '#^' . $regex . '$#i';

        $this->routes[] = [
            'methods' => $methods,
            'pattern' => $pattern,
            'handler' => $handler,
            'middlewares' => $middlewares,
        ];

        return $this;
    }

    public function dispatch(string $method, string $uri): bool
    {
        $method = strtoupper($method);
        $uri = rtrim($uri, '/') ?: '/';
        $request = Request::capture();

        foreach ($this->routes as $route) {
            if (!in_array($method, $route['methods'], true)) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                $params = array_filter(
                    $matches,
                    fn($key) => !is_int($key),
                    ARRAY_FILTER_USE_KEY
                );
                $request->routeParams = $params;
                $this->invoke($route['handler'], $params, $request, $route['middlewares']);
                return true;
            }
        }

        return false;
    }

    private function invoke($handler, array $params, Request $request, array $middlewares): void
    {
        if (!empty($middlewares) && !$this->processMiddlewares($middlewares, $request)) {
            return;
        }

        $args = array_merge([$request], array_values($params));

        if ($handler instanceof Closure || is_callable($handler)) {
            call_user_func_array($handler, $args);
            return;
        }

        if (is_string($handler) && str_contains($handler, '@')) {
            [$controller, $method] = explode('@', $handler, 2);
            $controllerClass = $this->resolveController($controller);
            $instance = new $controllerClass();
            call_user_func_array([$instance, $method], $args);
            return;
        }

        throw new \RuntimeException('Handler de rota inválido.');
    }

    private function processMiddlewares(array $middlewares, Request $request): bool
    {
        return (new MiddlewareManager())->handle($middlewares, $request);
    }

    private function resolveController(string $controller): string
    {
        $controller = trim($controller, '\\');
        if (!str_starts_with($controller, 'App\\')) {
            $controller = 'App\\Http\\Controllers\\' . $controller;
        }
        if (!class_exists($controller)) {
            throw new \RuntimeException("Controller {$controller} não encontrado");
        }
        return $controller;
    }
}
