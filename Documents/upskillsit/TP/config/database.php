<?php

$env = static function (string $name, string $default): string {
    $value = function_exists('getenv') ? getenv($name) : false;
    if ($value !== false && $value !== '') {
        return $value;
    }

    $value = $_ENV[$name] ?? $_SERVER[$name] ?? null;
    return is_string($value) && $value !== '' ? $value : $default;
};

return [
    'host' => $env('DB_HOST', '127.0.0.1'),
    'port' => $env('DB_PORT', '3306'),
    'name' => $env('DB_NAME', 'upskillsit'),
    'user' => $env('DB_USER', 'root'),
    'pass' => $env('DB_PASSWORD', ''),
    'charset' => 'utf8mb4',
];
