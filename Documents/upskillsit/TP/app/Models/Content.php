<?php

namespace App\Models;

use App\Core\Model;

final class Content extends Model
{
    public function opportunities(array $filters = []): array
    {
        [$where, $params] = $this->filters($filters, ['type','status','location'], ['title','organism','description','skills']);
        if (!array_key_exists('admin', $filters)) {
            $where .= ($where ? ' AND ' : 'WHERE ') . '(deadline IS NULL OR deadline >= CURDATE())';
        }
        return $this->all('SELECT * FROM opportunities ' . $where . ' ORDER BY published_at DESC, created_at DESC LIMIT 60', $params);
    }

    public function opportunity(int $id): ?array
    {
        return $this->one('SELECT * FROM opportunities WHERE id=?', [$id]);
    }

    public function saveOpportunity(array $data, ?int $id = null): void
    {
        $this->ensureCompanyOpportunityColumn();
        $fields = ['title','type','organism','description','target_profile','skills','location','deadline','application_method','application_link','status','published_at','company_user_id'];
        $this->save('opportunities', $fields, $data, $id);
    }

    public function companyOpportunities(int $companyUserId): array
    {
        $this->ensureCompanyOpportunityColumn();
        return $this->all(
            'SELECT o.*, (SELECT COUNT(*) FROM applications a WHERE a.opportunity_id=o.id) applications_count
             FROM opportunities o
             WHERE o.company_user_id=?
             ORDER BY o.created_at DESC',
            [$companyUserId]
        );
    }

    public function companyOpportunity(int $companyUserId, int $id): ?array
    {
        $this->ensureCompanyOpportunityColumn();
        return $this->one(
            'SELECT * FROM opportunities WHERE company_user_id=? AND id=?',
            [$companyUserId, $id]
        );
    }

    public function resources(array $filters = []): array
    {
        [$where, $params] = $this->filters($filters, ['category'], ['title','description','content','author']);
        return $this->all('SELECT * FROM resources ' . $where . ' ORDER BY published_at DESC, created_at DESC LIMIT 60', $params);
    }

    public function resource(int $id): ?array
    {
        return $this->one('SELECT * FROM resources WHERE id=?', [$id]);
    }

    public function saveResource(array $data, ?int $id = null): void
    {
        $fields = ['title','description','category','author','source','content','link','document_path','status','published_at'];
        $this->save('resources', $fields, $data, $id);
    }

    public function events(array $filters = []): array
    {
        [$where, $params] = $this->filters($filters, ['status'], ['title','description','location','speaker'], 'e.');
        if (!array_key_exists('admin', $filters)) {
            $where .= ($where ? ' AND ' : 'WHERE ') . 'e.event_date >= NOW()';
        }
        return $this->all('SELECT e.*, (SELECT COUNT(*) FROM registrations r WHERE r.event_id=e.id AND r.status="confirmed") confirmed_count FROM events e ' . $where . ' ORDER BY e.event_date ASC LIMIT 60', $params);
    }

    public function event(int $id): ?array
    {
        return $this->one('SELECT e.*, (SELECT COUNT(*) FROM registrations r WHERE r.event_id=e.id AND r.status="confirmed") confirmed_count FROM events e WHERE e.id=?', [$id]);
    }

    public function saveEvent(array $data, ?int $id = null): void
    {
        $fields = ['title','description','event_date','location','capacity','speaker','status'];
        $this->save('events', $fields, $data, $id);
    }

    public function articles(array $filters = []): array
    {
        [$where, $params] = $this->filters($filters, ['status'], ['title','excerpt','content']);
        return $this->all('SELECT * FROM articles ' . $where . ' ORDER BY published_at DESC, created_at DESC LIMIT 60', $params);
    }

    public function article(int $id): ?array
    {
        return $this->one('SELECT * FROM articles WHERE id=?', [$id]);
    }

    public function saveArticle(array $data, ?int $id = null): void
    {
        if (empty($data['slug']) && !empty($data['title'])) {
            $data['slug'] = $this->slug((string) $data['title']);
        }
        $fields = ['title','slug','excerpt','content','status','published_at'];
        $this->save('articles', $fields, $data, $id);
    }

    public function delete(string $table, int $id): void
    {
        if (in_array($table, ['opportunities','resources','events','articles'], true)) {
            $this->exec("DELETE FROM {$table} WHERE id=?", [$id]);
        }
    }

    public function setStatus(string $table, int $id, string $status): void
    {
        if (!in_array($table, ['opportunities','resources','events','articles'], true)) {
            return;
        }
        if ($status === 'published' && in_array($table, ['opportunities','resources','articles'], true)) {
            $this->exec("UPDATE {$table} SET status=?, published_at=COALESCE(published_at, NOW()) WHERE id=?", [$status, $id]);
            return;
        }
        $this->exec("UPDATE {$table} SET status=? WHERE id=?", [$status, $id]);
    }

    public function registerEvent(int $eventId, int $userId): array
    {
        try {
            $this->db->beginTransaction();
            $event = $this->one('SELECT * FROM events WHERE id=? FOR UPDATE', [$eventId]);
            if (!$event) {
                $this->db->rollBack();
                return ['ok' => false, 'message' => "L'événement est introuvable."];
            }
            if ($event['status'] !== 'published' || strtotime($event['event_date']) < time()) {
                $this->db->rollBack();
                return ['ok' => false, 'message' => "Les inscriptions ne sont plus ouvertes."];
            }
            $existing = $this->one('SELECT id FROM registrations WHERE user_id=? AND event_id=?', [$userId, $eventId]);
            if ($existing) {
                $this->db->rollBack();
                return ['ok' => false, 'message' => 'Vous êtes déjà inscrit à cet événement.'];
            }
            $count = (int) (($this->one('SELECT COUNT(*) c FROM registrations WHERE event_id=? AND status="confirmed"', [$eventId])['c'] ?? 0));
            $status = $count < (int) $event['capacity'] ? 'confirmed' : 'waiting_list';
            $this->exec('INSERT INTO registrations (user_id,event_id,status) VALUES (?,?,?)', [$userId, $eventId, $status]);
            $this->db->commit();
            return ['ok' => true, 'status' => $status, 'message' => $status === 'confirmed' ? 'Inscription confirmée.' : "Capacité atteinte : vous êtes sur liste d'attente."];
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log($e->getMessage());
            return ['ok' => false, 'message' => 'Une erreur est survenue.'];
        }
    }

    public function dashboardStats(): array
    {
        return [
            'opportunities' => $this->one('SELECT COUNT(*) c FROM opportunities')['c'] ?? 0,
            'published_opportunities' => $this->one("SELECT COUNT(*) c FROM opportunities WHERE status='published'")['c'] ?? 0,
            'resources' => $this->one('SELECT COUNT(*) c FROM resources')['c'] ?? 0,
            'published_resources' => $this->one("SELECT COUNT(*) c FROM resources WHERE status='published'")['c'] ?? 0,
            'events' => $this->one('SELECT COUNT(*) c FROM events')['c'] ?? 0,
            'published_events' => $this->one("SELECT COUNT(*) c FROM events WHERE status='published'")['c'] ?? 0,
            'registrations' => $this->one('SELECT COUNT(*) c FROM registrations')['c'] ?? 0,
            'articles' => $this->one('SELECT COUNT(*) c FROM articles')['c'] ?? 0,
            'published_articles' => $this->one("SELECT COUNT(*) c FROM articles WHERE status='published'")['c'] ?? 0,
        ];
    }

    public function applicationStats(): array
    {
        $this->ensureApplicationsTable();
        return $this->one(
            "SELECT COUNT(*) total,
                    SUM(status='new') new_count,
                    SUM(status='reviewed') reviewed_count,
                    SUM(status='shortlisted') shortlisted_count,
                    SUM(status='rejected') rejected_count
             FROM applications"
        ) ?? ['total' => 0, 'new_count' => 0, 'reviewed_count' => 0, 'shortlisted_count' => 0, 'rejected_count' => 0];
    }

    public function contentByStatus(): array
    {
        return [
            'opportunities' => $this->all("SELECT status, COUNT(*) total FROM opportunities GROUP BY status"),
            'resources' => $this->all("SELECT status, COUNT(*) total FROM resources GROUP BY status"),
            'events' => $this->all("SELECT status, COUNT(*) total FROM events GROUP BY status"),
            'articles' => $this->all("SELECT status, COUNT(*) total FROM articles GROUP BY status"),
        ];
    }

    private function save(string $table, array $fields, array $data, ?int $id): void
    {
        $values = array_map(fn($f) => $data[$f] ?? null, $fields);
        if ($id) {
            $sets = implode(',', array_map(fn($f) => "{$f}=?", $fields));
            $this->exec("UPDATE {$table} SET {$sets} WHERE id=?", array_merge($values, [$id]));
            return;
        }
        $this->exec("INSERT INTO {$table} (" . implode(',', $fields) . ') VALUES (' . rtrim(str_repeat('?,', count($fields)), ',') . ')', $values);
    }

    private function filters(array $filters, array $exact, array $search, string $prefix = ''): array
    {
        $where = [];
        $params = [];
        foreach ($exact as $field) {
            if (!empty($filters[$field])) {
                $where[] = "{$prefix}{$field}=?";
                $params[] = $filters[$field];
            }
        }
        if (!empty($filters['q'])) {
            $where[] = '(' . implode(' OR ', array_map(fn($f) => "{$prefix}{$f} LIKE ?", $search)) . ')';
            foreach ($search as $_) {
                $params[] = '%' . $filters['q'] . '%';
            }
        }
        if (!array_key_exists('admin', $filters)) {
            $where[] = "{$prefix}status='published'";
        }
        return [$where ? 'WHERE ' . implode(' AND ', $where) : '', $params];
    }

    private function slug(string $value): string
    {
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: $value;
        $value = strtolower((string) preg_replace('/[^a-zA-Z0-9]+/', '-', $value));
        return trim($value, '-') ?: 'article-' . date('YmdHis');
    }

    private function ensureCompanyOpportunityColumn(): void
    {
        $column = $this->one("SHOW COLUMNS FROM opportunities LIKE 'company_user_id'");
        if (!$column) {
            $this->exec('ALTER TABLE opportunities ADD company_user_id BIGINT UNSIGNED NULL AFTER application_link');
            $this->exec('ALTER TABLE opportunities ADD INDEX idx_opportunities_company (company_user_id)');
        }
    }

    private function ensureApplicationsTable(): void
    {
        $this->exec("CREATE TABLE IF NOT EXISTS applications (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            opportunity_id BIGINT UNSIGNED NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            message TEXT,
            status ENUM('new','reviewed','shortlisted','rejected') NOT NULL DEFAULT 'new',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uq_applications_opportunity_user (opportunity_id,user_id),
            INDEX idx_applications_status (status),
            FOREIGN KEY(opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB");
    }
}
