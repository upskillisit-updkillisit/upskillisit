<?php

declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', __DIR__);

$config = require ROOT_PATH . '/config/config.php';
define('CONFIG', $config);

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (str_starts_with($class, $prefix)) {
        $path = APP_PATH . '/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (is_file($path)) {
            require $path;
        }
    }
});

use App\Core\Session;

Session::start();

$requestedLang = $_GET['lang'] ?? null;
if (is_string($requestedLang) && in_array($requestedLang, ['fr', 'en'], true)) {
    Session::put('lang', $requestedLang);
    setcookie('UPSKILLSIT_LANG', $requestedLang, [
        'expires' => time() + 60 * 60 * 24 * 180,
        'path' => '/',
        'httponly' => false,
        'samesite' => 'Lax',
        'secure' => (config('env') === 'production' || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')),
    ]);
}

function config(string $key, mixed $default = null): mixed
{
    $value = CONFIG;
    foreach (explode('.', $key) as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }
        $value = $value[$segment];
    }
    return $value;
}

function url(string $path = ''): string
{
    $base = rtrim((string) config('base_url', ''), '/');
    return $base . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function current_lang(): string
{
    $lang = App\Core\Session::get('lang', $_COOKIE['UPSKILLSIT_LANG'] ?? 'fr');
    return in_array($lang, ['fr', 'en'], true) ? $lang : 'fr';
}

function translations(): array
{
    static $translations = [];
    $lang = current_lang();
    if (!isset($translations[$lang])) {
        $path = APP_PATH . '/lang/' . $lang . '.php';
        $translations[$lang] = is_file($path) ? require $path : [];
    }
    return $translations[$lang];
}

function t(string $key, ?string $fallback = null): string
{
    $value = translations();
    foreach (explode('.', $key) as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $fallback ?? $key;
        }
        $value = $value[$segment];
    }
    return is_scalar($value) ? (string) $value : ($fallback ?? $key);
}

function t_list(string $key): array
{
    $value = translations();
    foreach (explode('.', $key) as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return [];
        }
        $value = $value[$segment];
    }
    return is_array($value) ? $value : [];
}

function t_choice(string $key, string $value): string
{
    $choices = t_list($key);
    return isset($choices[$value]) && is_scalar($choices[$value]) ? (string) $choices[$value] : $value;
}

function preserve_query_fields(array $exclude = []): string
{
    $exclude = array_fill_keys($exclude, true);
    $fields = '';
    foreach ($_GET as $key => $value) {
        if (isset($exclude[$key])) {
            continue;
        }
        foreach ((array) $value as $item) {
            $fields .= '<input type="hidden" name="' . e($key) . '" value="' . e($item) . '">';
        }
    }
    return $fields;
}

function selected(mixed $a, mixed $b): string
{
    return (string) $a === (string) $b ? ' selected' : '';
}

function checked(bool $value): string
{
    return $value ? ' checked' : '';
}
