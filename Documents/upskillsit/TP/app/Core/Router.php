<?php

namespace App\Core;

final class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, array $handler): void
    {
        $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', rtrim($path, '/') ?: '/');
        $this->routes[] = compact('method', 'path', 'handler') + ['pattern' => '#^' . $pattern . '$#'];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        if ($scriptDir !== '/' && str_starts_with($path, $scriptDir)) {
            $path = substr($path, strlen($scriptDir)) ?: '/';
        }
        $path = rtrim($path, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $path, $matches)) {
                [$class, $action] = $route['handler'];
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                (new $class())->$action(...array_values($params));
                return;
            }
        }

        http_response_code(404);
        require APP_PATH . '/Views/errors/404.php';
    }
}
