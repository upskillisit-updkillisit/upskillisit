<?php use App\Core\CSRF; ?>
<section class="section narrow">
    <form class="form card" method="post">
        <?= CSRF::field() ?>
        <h1><?= e(t('auth.account_title')) ?></h1>
        <div class="grid-2">
            <label><?= e(t('ui.first_name')) ?><input name="first_name" value="<?= e($user['first_name']) ?>" required></label>
            <label><?= e(t('ui.name')) ?><input name="last_name" value="<?= e($user['last_name']) ?>" required></label>
        </div>
        <label><?= e(t('ui.email')) ?><input type="email" name="email" value="<?= e($user['email']) ?>" required></label>
        <label><?= e(t('auth.new_password')) ?><input type="password" name="password" minlength="8"></label>
        <button class="btn btn-primary"><?= e(t('ui.save')) ?></button>
    </form>
    <form method="post" action="<?= url('/compte/supprimer') ?>" class="danger-zone">
        <?= CSRF::field() ?>
        <button class="btn btn-danger" onclick="return confirm('<?= e(t('auth.delete_confirm')) ?>')"><?= e(t('auth.delete_account')) ?></button>
    </form>
</section>
