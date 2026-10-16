<?php use App\Core\Auth; use App\Core\CSRF; ?>
<?php
$role = strtolower((string) Auth::role());
$spacePath = match ($role) {
    'admin' => '/admin',
    'professional', 'professionnel' => '/professionnel',
    'company', 'entreprise' => '/entreprise',
    default => '/dashboard',
};
$socialLinks = [
    'facebook' => ['label' => 'Facebook', 'href' => (string) config('contact.social.facebook', '')],
    'linkedin' => ['label' => 'LinkedIn', 'href' => (string) config('contact.social.linkedin', '')],
    'instagram' => ['label' => 'Instagram', 'href' => (string) config('contact.social.instagram', '')],
];
?>
<!doctype html>
<html lang="<?= e(current_lang()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'UpSkillsIT') ?> | UpSkillsIT</title>
    <meta name="description" content="<?= e(t('meta.description')) ?>">
    <meta property="og:title" content="<?= e($title ?? 'UpSkillsIT') ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="UpSkillsIT">
    <script>document.documentElement.dataset.theme=localStorage.getItem('upskillsit-theme')||'light';</script>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <script defer src="<?= asset('js/app.js') ?>"></script>
    <script>window.UPSKILLSIT_CSRF="<?= e(CSRF::token()) ?>";</script>
</head>
<body>
<header class="site-header">
    <a class="brand" href="<?= url('/') ?>"><span>Up</span>SkillsIT</a>
    <button class="nav-toggle" data-nav-toggle aria-label="<?= e(t('ui.open_menu')) ?>" aria-expanded="false"><?= e(t('ui.menu')) ?></button>
    <nav class="site-nav" data-nav>
        <div class="nav-links">
            <a href="<?= url('/') ?>"><?= e(t('nav.home')) ?></a>
            <a href="<?= url('/services') ?>"><?= e(t('nav.services')) ?></a>
            <a href="<?= url('/opportunites') ?>"><?= e(t('nav.opportunities')) ?></a>
            <a href="<?= url('/ressources') ?>"><?= e(t('nav.resources')) ?></a>
            <a href="<?= url('/evenements') ?>"><?= e(t('nav.events')) ?></a>
            <a href="<?= url('/actualites') ?>"><?= e(t('nav.news')) ?></a>
            <a href="<?= url('/contact') ?>"><?= e(t('nav.contact')) ?></a>
        </div>
        <div class="nav-actions">
            <div class="header-tools">
                <button class="theme-toggle" type="button" data-theme-toggle aria-label="<?= e(t('ui.theme_toggle')) ?>" title="<?= e(t('ui.theme_toggle')) ?>"><span class="theme-icon theme-icon--moon" aria-hidden="true"></span><span class="theme-icon theme-icon--sun" aria-hidden="true"></span></button>
                <form class="lang-switch" method="get" action="<?= e(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/') ?>">
                    <?= preserve_query_fields(['lang']) ?>
                    <label class="sr-only" for="lang-public"><?= e(t('ui.language')) ?></label>
                    <select id="lang-public" name="lang" aria-label="<?= e(t('ui.language')) ?>" title="<?= e(t('ui.language')) ?>">
                        <option value="fr"<?= selected(current_lang(), 'fr') ?>>FR</option>
                        <option value="en"<?= selected(current_lang(), 'en') ?>>EN</option>
                    </select>
                </form>
            </div>
            <?php if (Auth::check()): ?>
                <a class="btn btn-light" href="<?= url($spacePath) ?>"><?= e(t('nav.space')) ?></a>
                <form method="post" action="<?= url('/deconnexion') ?>"><?= CSRF::field() ?><button class="btn btn-primary"><?= e(t('nav.logout')) ?></button></form>
            <?php else: ?>
                <a class="btn btn-light" href="<?= url('/connexion') ?>"><?= e(t('nav.login')) ?></a>
                <a class="btn btn-primary" href="<?= url('/inscription') ?>"><?= e(t('nav.register')) ?></a>
            <?php endif; ?>
        </div>
    </nav>
</header>
<?php require APP_PATH . '/Views/partials/alerts.php'; ?>
<main><?= $content ?></main>
<footer class="site-footer">
    <section class="footer-main">
        <div>
            <a class="brand" href="<?= url('/') ?>"><span>Up</span>SkillsIT</a>
            <p><?= e(t('footer.description')) ?></p>
            <h4><?= e(t('footer.follow')) ?></h4>
            <div class="social-links" aria-label="<?= e(t('footer.social_networks')) ?>">
                <?php foreach ($socialLinks as $name => $social): ?>
                    <?php if ($social['href'] !== '' && $social['href'] !== '#'): ?>
                        <a class="social-link social-link--<?= e($name) ?>" href="<?= e($social['href']) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= e($social['label']) ?>" title="<?= e($social['label']) ?>">
                            <?php if ($name === 'facebook'): ?>
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15.1 8.2h2V4.7c-.3 0-1.5-.1-2.9-.1-2.9 0-4.8 1.8-4.8 5.1v2.8H6.2v3.9h3.2v8h3.9v-8h3.1l.5-3.9h-3.6v-2.4c0-1.1.3-1.9 1.8-1.9z"></path></svg>
                            <?php elseif ($name === 'linkedin'): ?>
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4.8 8.8h3.8V21H4.8zM6.7 3a2.2 2.2 0 1 1 0 4.4A2.2 2.2 0 0 1 6.7 3zM11 8.8h3.6v1.7h.1c.5-1 1.8-2 3.7-2 3.9 0 4.6 2.6 4.6 5.9V21h-3.8v-6c0-1.4 0-3.2-2-3.2s-2.3 1.5-2.3 3.1V21H11z"></path></svg>
                            <?php else: ?>
                                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.4" y="3.4" width="17.2" height="17.2" rx="5.1"></rect><circle cx="12" cy="12" r="4.1"></circle><circle cx="17.3" cy="6.7" r="1.2"></circle></svg>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <nav aria-label="<?= e(t('footer.quick_nav')) ?>">
            <h4><?= e(t('footer.quick_nav')) ?></h4>
            <a href="<?= url('/') ?>"><?= e(t('nav.home')) ?></a>
            <a href="<?= url('/services') ?>"><?= e(t('nav.services')) ?></a>
            <a href="<?= url('/opportunites') ?>"><?= e(t('nav.opportunities')) ?></a>
            <a href="<?= url('/ressources') ?>"><?= e(t('nav.resources')) ?></a>
            <a href="<?= url('/evenements') ?>"><?= e(t('nav.events')) ?></a>
        </nav>
        <nav aria-label="<?= e(t('footer.communities')) ?>">
            <h4><?= e(t('footer.communities')) ?></h4>
            <a href="<?= url('/etudiants') ?>"><?= e(t('footer.students')) ?></a>
            <a href="<?= url('/professionnels-it') ?>"><?= e(t('footer.professionals')) ?></a>
            <a href="<?= url('/entreprises') ?>"><?= e(t('footer.companies')) ?></a>
            <a href="<?= url('/contact') ?>"><?= e(t('footer.contact_us')) ?></a>
        </nav>
        <nav aria-label="<?= e(t('footer.information')) ?>">
            <h4><?= e(t('footer.information')) ?></h4>
            <a href="<?= url('/faq') ?>">FAQ</a>
            <a href="<?= url('/mentions-legales') ?>"><?= e(t('footer.legal')) ?></a>
            <a href="<?= url('/politique-confidentialite') ?>"><?= e(t('footer.privacy')) ?></a>
            <a href="<?= url('/actualites') ?>"><?= e(t('nav.news')) ?></a>
        </nav>
        <div>
            <h4><?= e(t('footer.contact_us')) ?></h4>
            <a class="btn btn-light" href="<?= url('/contact') ?>"><?= e(t('footer.contact_us')) ?></a>
        </div>
    </section>
    <section class="footer-bottom">
        <span>&copy; <?= date('Y') ?> UpSkillsIT. <?= e(t('footer.rights')) ?></span>
        <span><?= e(t('footer.tagline')) ?></span>
    </section>
</footer>
</body>
</html>
