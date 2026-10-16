<?php use App\Core\Session; $success = Session::flash('success'); $error = Session::flash('error'); ?>
<?php if ($success): ?><div class="alert success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
