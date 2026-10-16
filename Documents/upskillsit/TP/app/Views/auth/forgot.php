<?php use App\Core\CSRF; ?>
<section class="auth-page">
    <form class="form auth-card" method="post">
        <?= CSRF::field() ?>
        <h1><?= e(t('auth.forgot_title')) ?></h1>
        <p><?= e(t('auth.forgot_intro')) ?></p>
        <label><?= e(t('ui.email')) ?><input type="email" name="email" required></label>
        <button class="btn btn-primary"><?= e(t('auth.send')) ?></button>
    </form>
</section>
