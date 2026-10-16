<?php use App\Core\CSRF; ?>
<!doctype html>
<html lang="<?= e(current_lang()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Admin') ?> | UpSkillsIT</title>
    <script>document.documentElement.dataset.theme=localStorage.getItem('upskillsit-theme')||'light';</script>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <script defer src="<?= asset('js/app.js') ?>"></script>
</head>
<body class="app-shell admin-shell">
<aside class="sidebar">
    <a class="brand" href="<?= url('/admin') ?>"><span>Up</span>SkillsIT</a>
    <nav>
        <a href="<?= url('/admin') ?>"><?= e(t('nav.dashboard')) ?></a>
        <a href="<?= url('/admin/users') ?>"><?= e(t('nav.users')) ?></a>
        <a href="<?= url('/admin/opportunites') ?>"><?= e(t('nav.opportunities')) ?></a>
        <a href="<?= url('/admin/evenements') ?>"><?= e(t('nav.events')) ?></a>
        <a href="<?= url('/admin/ressources') ?>"><?= e(t('nav.resources')) ?></a>
        <a href="<?= url('/admin/articles') ?>"><?= e(t('nav.articles')) ?></a>
        <a href="<?= url('/admin/messages') ?>"><?= e(t('nav.messages')) ?></a>
        <a href="<?= url('/admin/notifications') ?>"><?= e(t('nav.notifications')) ?></a>
        <a href="<?= url('/admin/statistiques') ?>"><?= e(t('nav.statistics')) ?></a>
        <a href="<?= url('/admin/parametres') ?>"><?= e(t('nav.settings')) ?></a>
        <a href="<?= url('/') ?>"><?= e(t('nav.public_site')) ?></a>
        <form method="post" action="<?= url('/deconnexion') ?>"><?= CSRF::field() ?><button><?= e(t('nav.logout')) ?></button></form>
    </nav>
</aside>
<div class="workspace">
    <header class="topbar">
        <strong><?= e($title ?? '') ?></strong>
        <div class="topbar-actions">
            <button class="theme-toggle" type="button" data-theme-toggle aria-label="<?= e(t('ui.theme_toggle')) ?>" title="<?= e(t('ui.theme_toggle')) ?>"><span class="theme-icon theme-icon--moon" aria-hidden="true"></span><span class="theme-icon theme-icon--sun" aria-hidden="true"></span></button>
            <form class="lang-switch" method="get" action="<?= e(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/') ?>">
                <?= preserve_query_fields(['lang']) ?>
                <label class="sr-only" for="lang-admin"><?= e(t('ui.language')) ?></label>
                <select id="lang-admin" name="lang" aria-label="<?= e(t('ui.language')) ?>" title="<?= e(t('ui.language')) ?>">
                    <option value="fr"<?= selected(current_lang(), 'fr') ?>>FR</option>
                    <option value="en"<?= selected(current_lang(), 'en') ?>>EN</option>
                </select>
            </form>
            <span><?= e(t('nav.administration')) ?></span>
        </div>
    </header>
    <?php require APP_PATH . '/Views/partials/alerts.php'; ?>
    <main class="workspace-main"><?= $content ?></main>
</div>
</body>
</html>
