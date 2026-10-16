<?php

namespace App\Models;

use App\Core\Model;

final class Talent extends Model
{
    public function profiles(array $filters = []): array
    {
        $where = ["u.status='active'", "u.role IN ('student','professional')"];
        $params = [];
        if (!empty($filters['q'])) {
            $where[] = "(u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ? OR sp.programming_languages LIKE ? OR sp.frameworks LIKE ? OR pp.skills LIKE ? OR pp.expertise_domain LIKE ?)";
            $q = '%' . $filters['q'] . '%';
            array_push($params, $q, $q, $q, $q, $q, $q, $q);
        }

        return $this->all(
            'SELECT u.id, u.first_name, u.last_name, u.email, u.role,
                    sp.school, sp.study_level, sp.major, sp.programming_languages, sp.frameworks, sp.city,
                    pp.function_title, pp.company, pp.expertise_domain, pp.experience_years, pp.skills, pp.location
             FROM users u
             LEFT JOIN student_profiles sp ON sp.user_id=u.id
             LEFT JOIN professional_profiles pp ON pp.user_id=u.id
             WHERE ' . implode(' AND ', $where) . '
             ORDER BY u.created_at DESC
             LIMIT 80',
            $params
        );
    }
}
