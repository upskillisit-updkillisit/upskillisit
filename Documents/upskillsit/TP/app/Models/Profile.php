<?php

namespace App\Models;

use App\Core\Model;

final class Profile extends Model
{
    public function forUser(int $userId, string $role): ?array
    {
        return match ($role) {
            'professional' => $this->professional($userId),
            'company' => $this->company($userId),
            default => $this->student($userId),
        };
    }

    public function student(int $userId): ?array
    {
        return $this->one('SELECT * FROM student_profiles WHERE user_id=?', [$userId]);
    }

    public function professional(int $userId): ?array
    {
        return $this->one('SELECT * FROM professional_profiles WHERE user_id=?', [$userId]);
    }

    public function company(int $userId): ?array
    {
        return $this->one('SELECT * FROM company_profiles WHERE user_id=?', [$userId]);
    }

    public function projects(int $userId): array
    {
        return $this->all('SELECT * FROM projects WHERE user_id=? ORDER BY created_at DESC', [$userId]);
    }

    public function experiences(int $userId): array
    {
        return $this->all('SELECT * FROM experiences WHERE user_id=? ORDER BY started_at DESC, id DESC', [$userId]);
    }

    public function saveStudent(int $userId, array $data): void
    {
        $fields = ['city','country','school','study_level','major','specialty','degree','study_year','certifications','extra_training','programming_languages','frameworks','databases','cloud','cybersecurity','data_ai','networks','systems','tools','other_skills','bio','photo_path','cv_path'];
        $this->upsert('student_profiles', $userId, $fields, $data);
    }

    public function saveProfessional(int $userId, array $data): void
    {
        $fields = ['function_title','company','expertise_domain','experience_years','skills','location','career_path','interests','public_links','bio'];
        $this->upsert('professional_profiles', $userId, $fields, $data);
    }

    public function saveCompany(int $userId, array $data): void
    {
        $fields = ['company_name','sector','website','location','contact_person','description','hiring_needs','public_links'];
        $this->upsert('company_profiles', $userId, $fields, $data);
    }

    public function project(int $userId, int $id): ?array
    {
        return $this->one('SELECT * FROM projects WHERE user_id=? AND id=?', [$userId, $id]);
    }

    public function experience(int $userId, int $id): ?array
    {
        return $this->one('SELECT * FROM experiences WHERE user_id=? AND id=?', [$userId, $id]);
    }

    public function saveProject(int $userId, array $data, ?int $id = null): void
    {
        $project = [
            'name' => $data['name'] ?? '',
            'description' => $data['description'] ?? null,
            'technologies' => $data['technologies'] ?? null,
            'role' => $data['role'] ?? null,
            'public_link' => $data['public_link'] ?? null,
        ];
        if (trim((string) $project['name']) === '') {
            return;
        }

        if ($id) {
            $this->exec(
                'UPDATE projects SET name=?, description=?, technologies=?, role=?, public_link=? WHERE user_id=? AND id=?',
                [$project['name'], $project['description'], $project['technologies'], $project['role'], $project['public_link'], $userId, $id]
            );
            return;
        }

        $this->exec(
            'INSERT INTO projects (user_id,name,description,technologies,role,public_link) VALUES (?,?,?,?,?,?)',
            [$userId, $project['name'], $project['description'], $project['technologies'], $project['role'], $project['public_link']]
        );
    }

    public function deleteProject(int $userId, int $id): void
    {
        $this->exec('DELETE FROM projects WHERE user_id=? AND id=?', [$userId, $id]);
    }

    public function saveExperience(int $userId, array $data, ?int $id = null): void
    {
        $experience = [
            'type' => $data['type'] ?? 'Projet',
            'title' => $data['title'] ?? '',
            'organization' => $data['organization'] ?? null,
            'description' => $data['description'] ?? null,
            'started_at' => $data['started_at'] ?? null,
            'ended_at' => $data['ended_at'] ?? null,
        ];
        if (trim((string) $experience['title']) === '') {
            return;
        }

        if ($id) {
            $this->exec(
                'UPDATE experiences SET type=?, title=?, organization=?, description=?, started_at=?, ended_at=? WHERE user_id=? AND id=?',
                [$experience['type'], $experience['title'], $experience['organization'], $experience['description'], $experience['started_at'] ?: null, $experience['ended_at'] ?: null, $userId, $id]
            );
            return;
        }

        $this->exec(
            'INSERT INTO experiences (user_id,type,title,organization,description,started_at,ended_at) VALUES (?,?,?,?,?,?,?)',
            [$userId, $experience['type'], $experience['title'], $experience['organization'], $experience['description'], $experience['started_at'] ?: null, $experience['ended_at'] ?: null]
        );
    }

    public function deleteExperience(int $userId, int $id): void
    {
        $this->exec('DELETE FROM experiences WHERE user_id=? AND id=?', [$userId, $id]);
    }

    public function saveProjects(int $userId, array $projects): void
    {
        $this->exec('DELETE FROM projects WHERE user_id=?', [$userId]);
        foreach ($projects as $project) {
            if (trim((string) ($project['name'] ?? '')) === '') {
                continue;
            }
            $this->exec(
                'INSERT INTO projects (user_id,name,description,technologies,role,public_link) VALUES (?,?,?,?,?,?)',
                [$userId, $project['name'], $project['description'] ?? null, $project['technologies'] ?? null, $project['role'] ?? null, $project['public_link'] ?? null]
            );
        }
    }

    public function saveExperiences(int $userId, array $experiences): void
    {
        $this->exec('DELETE FROM experiences WHERE user_id=?', [$userId]);
        foreach ($experiences as $experience) {
            if (trim((string) ($experience['title'] ?? '')) === '') {
                continue;
            }
            $this->exec(
                'INSERT INTO experiences (user_id,type,title,organization,description,started_at,ended_at) VALUES (?,?,?,?,?,?,?)',
                [$userId, $experience['type'] ?? null, $experience['title'], $experience['organization'] ?? null, $experience['description'] ?? null, $experience['started_at'] ?: null, $experience['ended_at'] ?: null]
            );
        }
    }

    public function completion(array $profile): int
    {
        if (!$profile) {
            return 0;
        }
        $ignore = ['id','user_id','created_at','updated_at'];
        $fields = array_filter(array_keys($profile), fn($k) => !in_array($k, $ignore, true));
        $filled = 0;
        foreach ($fields as $field) {
            $filled += trim((string) ($profile[$field] ?? '')) !== '' ? 1 : 0;
        }
        return (int) round(($filled / max(1, count($fields))) * 100);
    }

    public function notifications(int $userId): array
    {
        return (new Notification())->forUser($userId);
    }

    private function upsert(string $table, int $userId, array $fields, array $data): void
    {
        $values = array_map(fn($field) => $data[$field] ?? null, $fields);
        $columns = array_map(fn($field) => $this->quoteIdentifier($field), $fields);
        $updates = implode(',', array_map(fn($field) => $this->quoteIdentifier($field) . '=VALUES(' . $this->quoteIdentifier($field) . ')', $fields));
        $this->exec('INSERT INTO ' . $table . ' (user_id,' . implode(',', $columns) . ') VALUES (?,' . rtrim(str_repeat('?,', count($fields)), ',') . ') ON DUPLICATE KEY UPDATE ' . $updates, array_merge([$userId], $values));
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }
}
