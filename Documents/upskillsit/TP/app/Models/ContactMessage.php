<?php

namespace App\Models;

use App\Core\Model;

final class ContactMessage extends Model
{
    public function ensureSchema(): void
    {
        $this->exec("CREATE TABLE IF NOT EXISTS contact_messages (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(150) NOT NULL,
            email VARCHAR(190) NOT NULL,
            subject VARCHAR(190) NOT NULL,
            message TEXT NOT NULL,
            status ENUM('new','read','archived') NOT NULL DEFAULT 'new',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_contact_messages_status (status),
            INDEX idx_contact_messages_created (created_at)
        ) ENGINE=InnoDB");
    }

    public function create(array $data): void
    {
        $this->ensureSchema();
        $this->exec(
            'INSERT INTO contact_messages (name,email,subject,message,status) VALUES (?,?,?,?,?)',
            [$data['name'], mb_strtolower($data['email']), $data['subject'], $data['message'], 'new']
        );
    }

    public function list(array $filters = []): array
    {
        $this->ensureSchema();
        $where = [];
        $params = [];
        if (!empty($filters['status'])) {
            $where[] = 'status=?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['q'])) {
            $where[] = '(name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?)';
            $q = '%' . $filters['q'] . '%';
            array_push($params, $q, $q, $q, $q);
        }
        $sql = 'SELECT * FROM contact_messages';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        return $this->all($sql . ' ORDER BY created_at DESC LIMIT 120', $params);
    }

    public function stats(): array
    {
        $this->ensureSchema();
        return $this->one(
            "SELECT COUNT(*) total,
                    SUM(status='new') new_count,
                    SUM(status='read') read_count,
                    SUM(status='archived') archived_count
             FROM contact_messages"
        ) ?? ['total' => 0, 'new_count' => 0, 'read_count' => 0, 'archived_count' => 0];
    }

    public function setStatus(int $id, string $status): void
    {
        $this->ensureSchema();
        if (!in_array($status, ['new', 'read', 'archived'], true)) {
            $status = 'read';
        }
        $this->exec('UPDATE contact_messages SET status=? WHERE id=?', [$status, $id]);
    }
}
