<?php

namespace App\Models;

use App\Core\Model;

final class Notification extends Model
{
    public function ensureSchema(): void
    {
        $this->exec("CREATE TABLE IF NOT EXISTS notifications (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            title VARCHAR(190) NOT NULL,
            body TEXT,
            read_at DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB");
    }

    public function create(int $userId, string $title, ?string $body = null): void
    {
        $this->ensureSchema();
        $this->exec(
            'INSERT INTO notifications (user_id,title,body) VALUES (?,?,?)',
            [$userId, $title, $body]
        );
    }

    public function forUser(int $userId, int $limit = 30): array
    {
        $this->ensureSchema();
        $limit = max(1, min(100, $limit));
        return $this->all(
            "SELECT * FROM notifications WHERE user_id=? ORDER BY created_at DESC LIMIT {$limit}",
            [$userId]
        );
    }
}
