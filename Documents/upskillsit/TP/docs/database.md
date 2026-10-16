# Base de données

La base `upskillsit` est normalisée autour des tables principales :

- `users`
- `student_profiles`
- `professional_profiles`
- `skills`, `student_skills`
- `projects`, `experiences`
- `opportunities`
- `resources`
- `events`, `registrations`
- `articles`, `notifications`, `password_resets`, `admin_logs`

Les inscriptions aux événements utilisent la contrainte unique `(user_id, event_id)` et une transaction avec `SELECT ... FOR UPDATE` pour éviter de dépasser la capacité.
