<?php

namespace App\Core;

abstract class Controller
{
    protected function view(string $view, array $data = [], ?string $extension = null): string
    {
        return View::render($view, $data, $extension);
    }
    protected function json($payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if (ob_get_level() > 0) {
            ob_end_flush();
        }

        exit;
    }
}
