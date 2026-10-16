<?php use App\Core\CSRF; ?>
<section class="auth-page">
    <form class="form auth-card" method="post">
        <?= CSRF::field() ?>
        <h1><?= e(t('auth.reset_title')) ?></h1>
        <?php if (!$valid): ?>
            <div class="alert error"><?= e(t('auth.reset_invalid')) ?></div>
            <a class="btn btn-light" href="<?= url('/mot-de-passe-oublie') ?>"><?= e(t('auth.request_new_link')) ?></a>
        <?php else: ?>
            <?php if (!empty($error)): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
            <label><?= e(t('auth.new_password')) ?><input type="password" name="password" minlength="8" required></label>
            <label><?= e(t('ui.password_confirmation')) ?><input type="password" name="password_confirmation" minlength="8" required></label>
            <button class="btn btn-primary"><?= e(t('ui.update')) ?></button>
        <?php endif; ?>
    </form>
</section>
