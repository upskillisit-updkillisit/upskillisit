<?php use App\Core\CSRF; $old = $old ?? []; $errors = $errors ?? []; ?>
<section class="auth-page">
    <aside class="auth-side">
        <span class="eyebrow"><?= e(t('auth.join')) ?></span>
        <h1><?= e(t('auth.register_title_side')) ?></h1>
        <p><?= e(t('auth.register_intro')) ?></p>
    </aside>
    <form class="form auth-card wide" method="post">
        <?= CSRF::field() ?>
        <h1><?= e(t('nav.register')) ?></h1>
        <div class="grid-2">
            <label><?= e(t('ui.first_name')) ?><input name="first_name" value="<?= e($old['first_name'] ?? '') ?>" autocomplete="given-name" required></label>
            <label><?= e(t('ui.name')) ?><input name="last_name" value="<?= e($old['last_name'] ?? '') ?>" autocomplete="family-name" required></label>
        </div>
        <label><?= e(t('ui.email')) ?><input type="email" name="email" value="<?= e($old['email'] ?? '') ?>" autocomplete="email" required></label>
        <div class="grid-2">
            <label><?= e(t('ui.password')) ?><input type="password" name="password" minlength="8" autocomplete="new-password" required></label>
            <label><?= e(t('ui.password_confirmation')) ?><input type="password" name="password_confirmation" minlength="8" autocomplete="new-password" required></label>
        </div>
        <label><?= e(t('auth.profile_type')) ?>
            <select name="role" required>
                <option value="student"<?= selected($old['role'] ?? '', 'student') ?>><?= e(t('auth.student')) ?></option>
                <option value="professional"<?= selected($old['role'] ?? '', 'professional') ?>><?= e(t('auth.professional')) ?></option>
                <option value="company"<?= selected($old['role'] ?? '', 'company') ?>><?= e(t('auth.company')) ?></option>
            </select>
        </label>
        <label class="check"><input type="checkbox" name="terms" value="1" required> <?= e(t('auth.terms')) ?></label>
        <label class="check"><input type="checkbox" name="consent" value="1" required> <?= e(t('auth.consent')) ?></label>
        <?php if ($errors): ?><div class="alert error"><?= e(implode(' ', $errors)) ?></div><?php endif; ?>
        <button class="btn btn-primary"><?= e(t('nav.register')) ?></button>
        <p><?= e(t('auth.already_member')) ?> <a href="<?= url('/connexion') ?>"><?= e(t('auth.login_button')) ?></a></p>
    </form>
</section>
