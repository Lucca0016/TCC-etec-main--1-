<?php

namespace App\Core;

class View
{
    public static function render(string $view, array $data = [], ?string $extension = null): string
    {
        $view = str_replace(['::', '.'], ['/', '/'], $view);
        $ext = $extension ? '.' . ltrim($extension, '.') : '.php';
        if (str_contains($view, '.')) {
            $ext = '';
        }
        $path = resource_path('views/' . $view . $ext);
        if (!file_exists($path)) {
            throw new \RuntimeException("View '{$view}' não encontrada em {$path}");
        }
        extract($data, EXTR_SKIP);
        ob_start();
        include $path;
        return ob_get_clean() ?: '';
    }
}
