<?php

$env = static function (string $name, string $default): string {
    $value = getenv($name);
    return $value === false ? $default : $value;
};

return [
    'app_name' => 'UpSkillsIT',
    'base_url' => $env('BASE_URL', ''),
    'env' => $env('APP_ENV', 'local'),
    'db' => [
        'host' => $env('DB_HOST', '127.0.0.1'),
        'port' => $env('DB_PORT', '3306'),
        'name' => $env('DB_NAME', 'upskillsit'),
        'user' => $env('DB_USER', 'root'),
        'pass' => $env('DB_PASSWORD', ''),
        'charset' => 'utf8mb4',
    ],
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
