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
    'app_name' => 'UpSkillsIT',
    'base_url' => $env('BASE_URL', ''),
    'env' => $env('APP_ENV', 'local'),
    'db' => require __DIR__ . '/database.php',
    'security' => [
        'session_name' => 'UPSKILLSITSESSID',
        'csrf_key' => '_csrf_token',
        'max_login_attempts' => 5,
        'lockout_minutes' => 15,
    ],
    'uploads' => [
        'photo_dir' => dirname(__DIR__) . '/public/uploads/photos',
        'cv_dir' => dirname(__DIR__) . '/public/uploads/cv',
        'max_photo_size' => 2097152,
        'max_cv_size' => 5242880,
        'photo_mimes' => ['image/jpeg', 'image/png', 'image/webp'],
        'cv_mimes' => ['application/pdf'],
    ],
    'contact' => [
        'email' => 'contact@upskillsit.local',
        'social' => [
            'facebook' => 'https://www.facebook.com/EURESjobs',
            'linkedin' => 'https://www.linkedin.com/company/eures/',
            'instagram' => 'https://www.instagram.com/euresjobs/',
        ],
    ],
];
