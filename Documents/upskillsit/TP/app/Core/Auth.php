<?php

namespace App\Core;

use App\Models\User;

final class Auth
{
    public static function user(): ?array
    {
        $id = Session::get('user_id');
        return $id ? (new User())->find((int) $id) : null;
    }

    public static function id(): ?int
    {
        return Session::get('user_id');
    }

    public static function check(): bool
    {
        return self::id() !== null;
    }

    public static function login(array $user): void
    {
        Session::regenerate();
        Session::put('user_id', (int) $user['id']);
        Session::put('role', $user['role']);
    }

    public static function logout(): void
    {
        $lang = current_lang();
        Session::destroy();
        Session::start();
        Session::put('lang', $lang);
    }

    public static function role(): ?string
    {
        return Session::get('role');
    }

    public static function requireAuth(): void
    {
        if (!self::check()) {
            Session::flash('error', t('messages.login_to_continue'));
            header('Location: ' . url('/connexion'));
            exit;
        }
    }

    public static function requireRole(array $roles): void
    {
        self::requireAuth();
        if (!in_array((string) self::role(), $roles, true)) {
            http_response_code(403);
            require APP_PATH . '/Views/errors/403.php';
            exit;
        }
    }
}
