<?php use App\Core\CSRF; ?>
<section class="auth-page">
    <aside class="auth-side">
        <span class="eyebrow"><?= e(t('auth.member_area')) ?></span>
        <h1><?= e(t('auth.login_title_side')) ?></h1>
        <p><?= e(t('auth.login_intro')) ?></p>
    </aside>
    <form class="form auth-card" method="post">
        <?= CSRF::field() ?>
        <h1><?= e(t('auth.login_title')) ?></h1>
        <label><?= e(t('ui.email')) ?><input type="email" name="email" autocomplete="email" required></label>
        <label><?= e(t('ui.password')) ?><input type="password" name="password" autocomplete="current-password" required></label>
        <button class="btn btn-primary"><?= e(t('auth.login_button')) ?></button>
        <a href="<?= url('/mot-de-passe-oublie') ?>"><?= e(t('auth.forgot_password')) ?></a>
        <p><?= e(t('auth.not_member')) ?> <a href="<?= url('/inscription') ?>"><?= e(t('auth.create_account_link')) ?></a></p>
    </form>
</section>
