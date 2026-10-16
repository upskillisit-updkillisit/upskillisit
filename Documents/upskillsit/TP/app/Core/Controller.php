<?php

namespace App\Core;

abstract class Controller
{
    protected function view(string $view, array $data = [], string $layout = 'main'): void
    {
        extract($data, EXTR_SKIP);
        $viewPath = APP_PATH . '/Views/' . $view . '.php';
        if (!is_file($viewPath)) {
            throw new \RuntimeException("Vue introuvable: {$view}");
        }
        ob_start();
        require $viewPath;
        $content = ob_get_clean();
        require APP_PATH . '/Views/layouts/' . $layout . '.php';
    }

    protected function json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . url($path));
        exit;
    }

    protected function input(): array
    {
        return $this->clean($_POST);
    }

    private function clean(array $input): array
    {
        $clean = [];
        foreach ($input as $key => $value) {
            $clean[$key] = is_array($value) ? $this->clean($value) : (is_string($value) ? trim($value) : $value);
        }
        return $clean;
    }
}
