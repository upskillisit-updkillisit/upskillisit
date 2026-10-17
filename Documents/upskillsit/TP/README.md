# UpSkillsIT

UpSkillsIT est une plateforme PHP/MySQL native dédiée aux étudiants IT, jeunes diplômés, professionnels et administrateurs. Elle propose un site public, une authentification par sessions, des profils, opportunités, ressources, événements avec inscription, tableau de bord apprenant et administration.

## Technologies

- PHP 8.x natif en MVC
- MySQL 8.x avec PDO et requêtes préparées
- HTML5, CSS3, JavaScript vanilla, Fetch API
- Apache avec `.htaccess`
- Sessions PHP, `password_hash()`, `password_verify()`, CSRF, RBAC

## Installation XAMPP

1. Copier le dossier `TP` dans `htdocs/upskillsit` ou pointer un VirtualHost vers `TP/public`.
2. Démarrer Apache et MySQL.
3. Créer/importer la base :
   - importer `database/schema.sql`
   - importer `database/seed.sql`
4. Vérifier la configuration dans `config/config.php`.
5. Ouvrir `http://localhost/upskillsit/TP/public` ou le VirtualHost configuré.

## Déploiement sur Render (Docker)

Le `Dockerfile` de ce dépôt utilise PHP 8.2 avec Apache, `pdo_mysql` et `mbstring`. Le DocumentRoot Apache est `public/`; `.htaccess` y est activé pour transmettre les routes MVC à `index.php`. Le conteneur écoute sur `0.0.0.0:$PORT` (10000 par défaut sur Render).

### Variables d'environnement du Web Service

Créer un service Render de type **Web Service**, connecter le dépôt `upskillisit-updkillisit/UPSKILLSIT` et choisir **Docker**. Pour ce dépôt seul, garder **Root Directory** vide. Si vous déployez plutôt depuis un monorepo qui contient le projet dans `TP/`, renseigner `TP` comme **Root Directory**. Ajouter ces variables dans Render (les secrets restent dans le tableau de bord, jamais dans Git) :

| Variable | Valeur |
| --- | --- |
| `APP_ENV` | `production` |
| `DB_HOST` | hôte privé du serveur MySQL |
| `DB_PORT` | port MySQL, généralement `3306` |
| `DB_NAME` | `upskillsit` |
| `DB_USER` | utilisateur de l'application |
| `DB_PASSWORD` | mot de passe défini dans Render |
| `BASE_URL` | laisser vide pour le domaine racine, sinon URL publique sans `/` final |

Render fournit `PORT` au conteneur. Ne pas remplacer cette variable par un port fixe. Les valeurs de secours dans `config/config.php` restent celles de XAMPP; les variables d'environnement les remplacent en production.

### Base MySQL

Render fournit PostgreSQL managé, pas MySQL managé. Pour cette application PDO/MySQL, créer un service MySQL compatible (par exemple le service privé MySQL décrit dans la documentation Render) ou utiliser un fournisseur MySQL externe. Garder le Web Service et le MySQL Render dans la même région et utiliser l'adresse privée du service lorsque c'est possible. Ajouter un disque persistant au service MySQL si vous l'hébergez sur Render; sa documentation spécifie `/var/lib/mysql` comme chemin de montage.

Créer une base vide appelée `upskillsit` et un utilisateur autorisé à créer/modifier ses tables. Depuis une machine ayant accès à l'hôte MySQL, initialiser une seule fois avec les identifiants de l'administrateur de base :

```sh
mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p upskillsit < database/schema.sql
mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p upskillsit < database/seed.sql
```

`schema.sql` crée la base `upskillsit`, sélectionne cette base puis supprime et recrée toutes les tables. Il convient à l'initialisation d'une base vierge, mais **ne pas le réexécuter sur une base contenant des données à conserver**. `seed.sql` ajoute les comptes et contenus de démonstration avec mots de passe connus pour le développement : ne l'exécuter en production que si ces données sont souhaitées, puis supprimer ou sécuriser immédiatement les comptes de démonstration, notamment l'administrateur. Faire une sauvegarde avant toute opération SQL.

### Fichiers téléversés

Les photos et CV sont enregistrés dans `public/uploads/`. Le système de fichiers d'un service Render est éphémère par défaut; ajouter un disque persistant au Web Service monté sur `/var/www/html/public/uploads` pour préserver ces fichiers après un redéploiement. La fonctionnalité d'upload fonctionne aussi sans disque, mais les fichiers peuvent disparaître au redéploiement.

Après avoir renseigné les variables et initialisé MySQL, lancer le déploiement depuis Render. Vérifier l'URL publique, la page d'accueil, une route directe telle que `/opportunites`, le CSS, la connexion et les téléversements. Les sessions PHP utilisent le stockage local du conteneur; les utilisateurs devront probablement se reconnecter après un redémarrage ou redéploiement.

## Déploiement InfinityFree (PHP/MySQL mutualisé)

Le projet est déjà en PHP natif : il n'utilise ni Laravel, ni Blade, ni Eloquent, ni Composer. Il conserve son routeur PHP léger, ses contrôleurs et ses vues PHP. `index.php` à la racine du projet appelle `public/index.php`; le `.htaccess` du projet réécrit les routes et sert les assets depuis `public/`, tout en bloquant l'accès HTTP à `app/`, `config/`, `database/`, `storage/` et aux fichiers cachés.

1. Dans le panneau InfinityFree, créez une base MySQL et copiez **l'hôte, le nom de base, l'utilisateur et le mot de passe exactement comme affichés**. L'hôte `sql200.infinityfree.com`, la base `if0_43196352_upskillsit` et l'utilisateur `if0_43196352` fournis pour ce déploiement sont des valeurs d'exemple pour le compte indiqué; confirmez-les dans le panneau.
2. Dans phpMyAdmin, sélectionnez la base concernée, puis importez `database/schema.sql`. Ce script crée les tables manquantes et ne supprime ni ne vide les tables existantes. Choisissez `utf8mb4` si le panneau vous demande l'interclassement.
3. N'importez `database/seed.sql` que si vous voulez les données de démonstration dans une base vide. Il n'est pas idempotent et crée des comptes de démonstration avec des mots de passe connus; ne l'importez pas dans une base de production contenant des utilisateurs.
4. Copiez **le contenu de ce dossier `TP/`** directement dans `htdocs` via FTP ou le gestionnaire de fichiers. Ne téléversez pas `UPSKILLSIITT1/` ni un dossier `.git`.
5. Sur le serveur, copiez `.env.example` vers `.env` à la racine de `htdocs`, puis complétez `DB_PASSWORD` avec le mot de passe de base. `.env` est ignoré par Git et bloqué par `.htaccess`; ne le publiez jamais. Le chargeur lit les vraies variables de processus quand elles existent, puis `.env` pour les hébergements mutualisés qui ne permettent pas de les configurer dans le panneau. Les hébergements gratuits InfinityFree ne fournissent pas de variables PHP personnalisées via le panneau.
6. Ouvrez le domaine en HTTPS et vérifiez `/`, `/opportunites`, `/connexion`, les assets, les rôles et les téléversements. L'application n'utilise ni Node.js ni commande de terminal sur le serveur.

`DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `APP_ENV` et `BASE_URL` sont lus depuis l'environnement. Sans `.env` et sans variables, les valeurs de repli XAMPP (`127.0.0.1`, `upskillsit`, `root`, mot de passe vide) restent inchangées. Laissez `BASE_URL` vide pour que les liens se basent sur le chemin d'installation détecté; renseignez-le seulement si le site est installé sous un préfixe inhabituel.

Les tables SQL utilisent des types, index et clés étrangères pris en charge par MySQL/MariaDB courants. L'import doit se faire dans la base sélectionnée de phpMyAdmin : les scripts n'imposent plus le nom local `upskillsit` ni `CREATE DATABASE`. Une table déjà existante n'est pas modifiée par `CREATE TABLE IF NOT EXISTS`; une évolution ultérieure de schéma devra donc être appliquée avec une migration SQL dédiée et sauvegardée au préalable.

Le formulaire de contact enregistre les messages dans la base, sans dépendre de l'envoi d'e-mail. Le flux de réinitialisation de mot de passe n'affiche son lien qu'en mode `APP_ENV=local`; en production il faut le relier à un fournisseur SMTP externe avant d'activer la récupération par e-mail. InfinityFree restreint `mail()` sur son hébergement gratuit et recommande un SMTP externe. Les fichiers de `storage/sessions/` sont des données temporaires locales : ne les téléversez pas; laissez PHP créer les sessions sur le serveur.

## Comptes de démonstration

- Admin : `admin@example.com` / `AdminDemo!2026`
- Étudiant : `student@example.com` / `StudentDemo!2026`
- Professionnel : `professional@example.com` / `ProDemo!2026`

Ces mots de passe sont destinés au développement local uniquement.

## Structure

- `public/` : front controller, assets, uploads, SEO
- `app/Core/` : routeur, contrôleur, modèle, session, auth, CSRF, validation
- `app/Controllers/` : contrôleurs HTTP
- `app/Models/` : accès données et logique métier
- `app/Views/` : vues et layouts
- `database/` : schéma et données de démo
- `docs/` : architecture, base, sécurité, tests

## Fonctionnalités

- Site public : accueil, services, profils cibles, opportunités, ressources, actualités, événements, FAQ, contact, pages légales
- Inscription, connexion, déconnexion, mot de passe oublié, modification et suppression du compte
- Dashboard utilisateur avec progression du profil
- Profil étudiant avec CV/photo et compétences détaillées
- Profil professionnel
- Opportunités avec recherche et filtres
- Ressources avec catégories
- Événements avec inscription AJAX, transactions et liste d'attente
- Admin : statistiques, utilisateurs, opportunités, ressources, événements

## Tests manuels rapides

1. Créer un compte étudiant.
2. Se connecter, compléter le profil, envoyer une photo valide et un PDF.
3. Filtrer opportunités et ressources.
4. S'inscrire à un événement depuis sa fiche.
5. Réessayer l'inscription au même événement pour vérifier l'unicité.
6. Se connecter comme admin et gérer utilisateurs/contenus.
7. Tester une route admin sans session pour vérifier la redirection/refus.

## Évolution

Le MVP est extensible vers candidatures avancées, modération fine, notifications enrichies, matching ou intégrations externes, sans changer la base MVC native.
