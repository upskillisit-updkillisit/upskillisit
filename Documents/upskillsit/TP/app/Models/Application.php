<?php

namespace App\Models;

use App\Core\Model;

final class Application extends Model
{
    public function ensureSchema(): void
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
        $this->ensureUniqueApplicationIndex();
    }

    public function companyApplications(int $companyUserId): array
    {
        $this->ensureSchema();
        return $this->all(
            "SELECT a.*, o.title opportunity_title, o.type opportunity_type,
                    u.first_name, u.last_name, u.email,
                    sp.school, sp.study_level, sp.major,
                    pp.function_title, pp.expertise_domain, pp.experience_years,
                    pp.skills, pp.location
             FROM applications a
             JOIN opportunities o ON o.id=a.opportunity_id
             JOIN users u ON u.id=a.user_id
             LEFT JOIN student_profiles sp ON sp.user_id=u.id
             LEFT JOIN professional_profiles pp ON pp.user_id=u.id
             WHERE o.company_user_id=?
             ORDER BY a.created_at DESC",
            [$companyUserId]
        );
    }

    public function myApplications(int $userId): array
    {
        $this->ensureSchema();
        return $this->all(
            "SELECT a.*, o.title opportunity_title, o.type opportunity_type, o.organism, o.location, o.deadline,
                    o.company_user_id, c.company_name
             FROM applications a
             JOIN opportunities o ON o.id=a.opportunity_id
             LEFT JOIN company_profiles c ON c.user_id=o.company_user_id
             WHERE a.user_id=?
             ORDER BY a.created_at DESC",
            [$userId]
        );
    }

    public function appliedOpportunityIds(int $userId): array
    {
        $this->ensureSchema();
        return array_map(
            'intval',
            array_column(
                $this->all('SELECT opportunity_id FROM applications WHERE user_id=?', [$userId]),
                'opportunity_id'
            )
        );
    }

    public function apply(int $userId, int $opportunityId, ?string $message = null): array
    {
        $this->ensureSchema();
        $user = (new User())->find($userId);
        if (!$user || ($user['status'] ?? '') !== 'active') {
            return ['ok' => false, 'message' => 'Votre compte doit etre actif pour postuler.'];
        }
        if (($user['role'] ?? '') !== 'professional') {
            return ['ok' => false, 'message' => 'Seuls les professionnels peuvent postuler depuis cet espace.'];
        }

        $opportunity = (new Content())->opportunity($opportunityId);
        if (!$opportunity) {
            return ['ok' => false, 'message' => 'Cette opportunite est introuvable.'];
        }
        if (($opportunity['status'] ?? '') !== 'published') {
            return ['ok' => false, 'message' => 'Cette opportunite n est plus ouverte aux candidatures.'];
        }
        if (!empty($opportunity['deadline']) && strtotime((string) $opportunity['deadline']) < strtotime(date('Y-m-d'))) {
            return ['ok' => false, 'message' => 'La date limite de cette opportunite est depassee.'];
        }

        try {
            $this->db->beginTransaction();
            $exists = $this->one('SELECT id FROM applications WHERE user_id=? AND opportunity_id=? FOR UPDATE', [$userId, $opportunityId]);
            if ($exists) {
                $this->db->rollBack();
                return ['ok' => false, 'message' => 'Vous avez deja candidate a cette opportunite.'];
            }

            $this->exec(
                'INSERT INTO applications (opportunity_id,user_id,message,status) VALUES (?,?,?,?)',
                [$opportunityId, $userId, $message ? trim($message) : null, 'new']
            );
            $applicationId = (int) $this->db->lastInsertId();
            $this->db->commit();
        } catch (\PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            if ($e->getCode() === '23000') {
                return ['ok' => false, 'message' => 'Vous avez deja candidate a cette opportunite.'];
            }
            error_log($e->getMessage());
            return ['ok' => false, 'message' => 'Impossible d enregistrer la candidature pour le moment.'];
        }

        try {
            $notifications = new Notification();
            $notifications->create($userId, 'Candidature envoyee', 'Votre candidature pour "' . $opportunity['title'] . '" a ete enregistree.');
            if (!empty($opportunity['company_user_id'])) {
                $notifications->create((int) $opportunity['company_user_id'], 'Nouvelle candidature', 'Une candidature a ete recue pour "' . $opportunity['title'] . '".');
            }
        } catch (\Throwable $e) {
            error_log($e->getMessage());
        }

        return ['ok' => true, 'message' => 'Votre candidature a bien ete enregistree.', 'id' => $applicationId];
    }

    public function cancel(int $userId, int $applicationId): bool
    {
        $this->ensureSchema();
        $stmt = $this->db->prepare('DELETE FROM applications WHERE id=? AND user_id=?');
        $stmt->execute([$applicationId, $userId]);
        return $stmt->rowCount() > 0;
    }

    public function updateStatus(int $id, int $companyUserId, string $status): bool
    {
        $this->ensureSchema();
        if (!in_array($status, ['new','reviewed','shortlisted','rejected'], true)) {
            $status = 'reviewed';
        }
        $application = $this->one(
            'SELECT a.user_id, a.status, o.title
             FROM applications a
             JOIN opportunities o ON o.id=a.opportunity_id
             WHERE a.id=? AND o.company_user_id=?',
            [$id, $companyUserId]
        );
        if (!$application) {
            return false;
        }

        $this->exec('UPDATE applications SET status=? WHERE id=?', [$status, $id]);
        if (($application['status'] ?? '') !== $status) {
            (new Notification())->create(
                (int) $application['user_id'],
                'Statut de candidature mis a jour',
                'Votre candidature pour "' . $application['title'] . '" est maintenant: ' . $status . '.'
            );
        }
        return true;
    }

    public function statsForCompany(int $companyUserId): array
    {
        $this->ensureSchema();
        return $this->one(
            "SELECT COUNT(*) total,
                    SUM(a.status='new') new_count,
                    SUM(a.status='shortlisted') shortlisted_count,
                    SUM(a.status='rejected') rejected_count
             FROM applications a
             JOIN opportunities o ON o.id=a.opportunity_id
             WHERE o.company_user_id=?",
            [$companyUserId]
        ) ?? ['total' => 0, 'new_count' => 0, 'shortlisted_count' => 0, 'rejected_count' => 0];
    }

    public function statsForUser(int $userId): array
    {
        $this->ensureSchema();
        return $this->one(
            "SELECT COUNT(*) total,
                    SUM(a.status='new') new_count,
                    SUM(a.status='shortlisted') shortlisted_count,
                    SUM(a.status='rejected') rejected_count
             FROM applications a
             WHERE a.user_id=?",
            [$userId]
        ) ?? ['total' => 0, 'new_count' => 0, 'shortlisted_count' => 0, 'rejected_count' => 0];
    }

    private function ensureUniqueApplicationIndex(): void
    {
        $index = $this->one("SHOW INDEX FROM applications WHERE Key_name='uq_applications_opportunity_user'");
        if (!$index) {
            $this->exec('ALTER TABLE applications ADD UNIQUE KEY uq_applications_opportunity_user (opportunity_id,user_id)');
        }
    }
}
