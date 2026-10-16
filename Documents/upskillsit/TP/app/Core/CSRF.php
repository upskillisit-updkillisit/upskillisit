<?php

namespace App\Core;

final class CSRF
{
    public static function token(): string
    {
        $key = config('security.csrf_key');
        if (!Session::get($key)) {
            Session::put($key, bin2hex(random_bytes(32)));
        }
        return Session::get($key);
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(self::token()) . '">';
    }

    public static function verify(?string $token): bool
    {
        return is_string($token) && hash_equals((string) Session::get(config('security.csrf_key'), ''), $token);
    }

    public static function requireValid(): void
    {
        if (!self::verify($_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null)) {
            http_response_code(419);
            exit('Jeton CSRF invalide.');
        }
    }
}
