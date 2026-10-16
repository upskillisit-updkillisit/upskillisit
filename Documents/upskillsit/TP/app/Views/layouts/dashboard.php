<?php use App\Core\Auth; use App\Core\CSRF; ?>
<?php $role = Auth::role(); ?>
<!doctype html>
<html lang="<?= e(current_lang()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Tableau de bord') ?> | UpSkillsIT</title>
    <script>document.documentElement.dataset.theme=localStorage.getItem('upskillsit-theme')||'light';</script>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <script defer src="<?= asset('js/app.js') ?>"></script>
    <script>window.UPSKILLSIT_CSRF="<?= e(CSRF::token()) ?>";</script>
</head>
<body class="app-shell role-<?= e($role ?? 'student') ?> <?= $role === 'professional' ? 'professional-shell' : ($role === 'company' ? 'company-shell' : 'student-shell') ?>">
<aside class="sidebar">
    <a class="brand" href="<?= url('/') ?>"><span>Up</span>SkillsIT</a>
    <nav>
        <?php if ($role === 'professional'): ?>
            <a href="<?= url('/professionnel') ?>"><?= e(t('nav.dashboard')) ?></a>
            <a href="<?= url('/professionnel/profil') ?>"><?= e(t('nav.professional_profile')) ?></a>
            <a href="<?= url('/professionnel/competences') ?>"><?= e(t('nav.skills')) ?></a>
            <a href="<?= url('/professionnel/experiences') ?>"><?= e(t('nav.experiences')) ?></a>
            <a href="<?= url('/professionnel/projets') ?>"><?= e(t('nav.projects')) ?></a>
            <a href="<?= url('/professionnel/opportunites') ?>"><?= e(t('nav.opportunities')) ?></a>
            <a href="<?= url('/professionnel/candidatures') ?>"><?= e(t('nav.applications')) ?></a>
            <a href="<?= url('/professionnel/ressources') ?>"><?= e(t('nav.resources')) ?></a>
            <a href="<?= url('/professionnel/evenements') ?>"><?= e(t('nav.events')) ?></a>
            <a href="<?= url('/professionnel/notifications') ?>"><?= e(t('nav.notifications')) ?></a>
        <?php elseif ($role === 'company'): ?>
            <a href="<?= url('/entreprise') ?>"><?= e(t('nav.dashboard')) ?></a>
            <a href="<?= url('/entreprise/profil') ?>"><?= e(t('nav.company_profile')) ?></a>
            <a href="<?= url('/entreprise/opportunites') ?>"><?= e(t('nav.opportunities')) ?></a>
            <a href="<?= url('/entreprise/opportunites/creer') ?>"><?= e(t('nav.create_opportunity')) ?></a>
            <a href="<?= url('/entreprise/candidatures') ?>"><?= e(t('nav.applications')) ?></a>
            <a href="<?= url('/entreprise/profils') ?>"><?= e(t('nav.talents')) ?></a>
            <a href="<?= url('/entreprise/evenements') ?>"><?= e(t('nav.events')) ?></a>
        <?php else: ?>
            <a href="<?= url('/dashboard') ?>"><?= e(t('nav.dashboard')) ?></a>
            <a href="<?= url('/profil') ?>"><?= e(t('nav.profile')) ?></a>
            <a href="<?= url('/profil') ?>"><?= e(t('nav.skills')) ?></a>
            <a href="<?= url('/profil') ?>"><?= e(t('nav.projects')) ?></a>
            <a href="<?= url('/profil') ?>"><?= e(t('nav.cv')) ?></a>
            <a href="<?= url('/opportunites') ?>"><?= e(t('nav.opportunities')) ?></a>
            <a href="<?= url('/candidatures') ?>"><?= e(t('nav.applications')) ?></a>
            <a href="<?= url('/ressources') ?>"><?= e(t('nav.resources')) ?></a>
            <a href="<?= url('/evenements') ?>"><?= e(t('nav.events')) ?></a>
            <a href="<?= url('/notifications') ?>"><?= e(t('nav.notifications')) ?></a>
        <?php endif; ?>
        <a href="<?= url($role === 'professional' ? '/professionnel/parametres' : ($role === 'company' ? '/entreprise/parametres' : '/compte')) ?>"><?= e(t('nav.settings')) ?></a>
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
                <label class="sr-only" for="lang-dashboard"><?= e(t('ui.language')) ?></label>
                <select id="lang-dashboard" name="lang" aria-label="<?= e(t('ui.language')) ?>" title="<?= e(t('ui.language')) ?>">
                    <option value="fr"<?= selected(current_lang(), 'fr') ?>>FR</option>
                    <option value="en"<?= selected(current_lang(), 'en') ?>>EN</option>
                </select>
            </form>
            <span><?= e(Auth::user()['first_name'] ?? '') ?></span>
        </div>
    </header>
    <?php require APP_PATH . '/Views/partials/alerts.php'; ?>
    <main class="workspace-main"><?= $content ?></main>
</div>
</body>
</html>
