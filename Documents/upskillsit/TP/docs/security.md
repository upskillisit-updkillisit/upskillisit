# Sécurité

Mesures implémentées :

- Mots de passe hachés avec `password_hash()`
- Vérification avec `password_verify()`
- PDO et statements préparés
- Jetons CSRF sur formulaires et requêtes AJAX sensibles
- Sessions HTTP-only avec `SameSite=Lax`
- Contrôles RBAC via `Auth::requireRole()`
- Protection contre les tentatives de connexion répétées
- Échappement HTML centralisé avec `e()`
- Validation serveur des formulaires critiques
- Uploads limités par MIME, taille et noms aléatoires
- Pages d'erreur sans exposition SQL

En production, activer HTTPS, configurer `base_url`, restreindre les permissions des dossiers d'upload et externaliser les secrets.
