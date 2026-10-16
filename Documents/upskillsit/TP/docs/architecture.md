# Architecture

UpSkillsIT suit une architecture MVC PHP native.

- `public/index.php` reçoit toutes les requêtes.
- `App\Core\Router` associe les URLs propres aux contrôleurs.
- Les contrôleurs valident la requête, appliquent auth/autorisation, appellent les modèles et retournent vues ou JSON.
- Les modèles centralisent SQL, transactions et accès PDO.
- Les vues ne contiennent pas de SQL et échappent les sorties avec `e()`.

Les layouts `main`, `dashboard` et `admin` séparent l'expérience publique, utilisateur et administration.
