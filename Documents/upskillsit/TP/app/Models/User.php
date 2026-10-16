<?php

namespace App\Models;

use App\Core\Model;

final class User extends Model
{
    public function find(int $id): ?array
    {
        return $this->one('SELECT id, first_name, last_name, email, role, status, created_at FROM users WHERE id = ?', [$id]);
    }

    public function findByEmail(string $email): ?array
    {
        return $this->one('SELECT * FROM users WHERE email = ?', [mb_strtolower($email)]);
    }

    public function create(array $data): int
    {
        $this->exec(
            'INSERT INTO users (first_name,last_name,email,password,role,terms_accepted,data_consent,status) VALUES (?,?,?,?,?,?,?,?)',
            [$data['first_name'], $data['last_name'], mb_strtolower($data['email']), password_hash($data['password'], PASSWORD_DEFAULT), $data['role'], 1, 1, 'active']
        );
        return (int) $this->db->lastInsertId();
    }

    public function updateAccount(int $id, array $data): void
    {
        $email = mb_strtolower((string) $data['email']);
        $existing = $this->findByEmail($email);
        if ($existing && (int) $existing['id'] !== $id) {
            throw new \RuntimeException('Email déjà utilisé.');
        }
        $params = [$data['first_name'], $data['last_name'], $email];
        $sql = 'UPDATE users SET first_name=?, last_name=?, email=?';
        if (!empty($data['password'])) {
            $sql .= ', password=?';
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        $params[] = $id;
        $this->exec($sql . ' WHERE id=?', $params);
    }

    public function delete(int $id): void
    {
        $this->exec('DELETE FROM users WHERE id = ?', [$id]);
    }

    public function list(array $filters = []): array
    {
        $where = [];
        $params = [];
        if (!empty($filters['q'])) {
            $where[] = '(first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)';
            $q = '%' . $filters['q'] . '%';
            array_push($params, $q, $q, $q);
        }
        if (!empty($filters['role'])) {
            $where[] = 'role = ?';
            $params[] = $filters['role'];
        }
        $sql = 'SELECT id, first_name, last_name, email, role, status, created_at FROM users';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        return $this->all($sql . ' ORDER BY created_at DESC LIMIT 100', $params);
    }

    public function setStatus(int $id, string $status): void
    {
        $this->exec('UPDATE users SET status=? WHERE id=?', [$status, $id]);
    }

    public function stats(): array
    {
        return $this->one("SELECT COUNT(*) total_users, SUM(role='student') students, SUM(role='professional') professionals, SUM(role='company') companies, SUM(role='admin') admins, SUM(status='active') active_users, SUM(status='suspended') suspended_users FROM users") ?? [];
    }

    public function recent(int $limit = 8): array
    {
        $limit = max(1, min(20, $limit));
        return $this->all("SELECT id, first_name, last_name, email, role, status, created_at FROM users ORDER BY created_at DESC LIMIT {$limit}");
    }

    public function monthlyRegistrations(): array
    {
        return $this->all(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') month, COUNT(*) total
             FROM users
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
             GROUP BY DATE_FORMAT(created_at, '%Y-%m')
             ORDER BY month ASC"
        );
    }

    public function createResetToken(string $email): ?string
    {
        $user = $this->findByEmail($email);
        if (!$user || $user['status'] !== 'active') {
            return null;
        }
        $token = bin2hex(random_bytes(32));
        $this->exec('DELETE FROM password_resets WHERE user_id=? OR expires_at < NOW()', [(int) $user['id']]);
        $this->exec('INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (?,?,DATE_ADD(NOW(), INTERVAL 1 HOUR))', [(int) $user['id'], hash('sha256', $token)]);
        return $token;
    }

    public function findResetToken(string $token): ?array
    {
        return $this->one('SELECT pr.*, u.email FROM password_resets pr JOIN users u ON u.id=pr.user_id WHERE pr.token_hash=? AND pr.expires_at > NOW()', [hash('sha256', $token)]);
    }

    public function resetPassword(string $token, string $password): bool
    {
        $reset = $this->findResetToken($token);
        if (!$reset) {
            return false;
        }
        $this->exec('UPDATE users SET password=? WHERE id=?', [password_hash($password, PASSWORD_DEFAULT), (int) $reset['user_id']]);
        $this->exec('DELETE FROM password_resets WHERE user_id=?', [(int) $reset['user_id']]);
        return true;
    }
}
