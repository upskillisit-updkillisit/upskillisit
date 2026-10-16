<?php use App\Core\CSRF; ?>
<section class="page-hero">
    <span class="eyebrow"><?= e(t('pages.contact_eyebrow')) ?></span>
    <h1><?= e(t('pages.contact_title')) ?></h1>
    <p><?= e(t('pages.contact_intro')) ?></p>
</section>

<section class="section">
    <div class="contact-grid">
        <form class="form panel contact-form" method="post" action="<?= url('/contact') ?>">
            <?= CSRF::field() ?>
            <h2><?= e(t('ui.send_message')) ?></h2>
            <div class="grid-2">
                <label><?= e(t('ui.name')) ?><input name="name" required maxlength="150" placeholder="<?= e(t('ui.name')) ?>"></label>
                <label><?= e(t('ui.email')) ?><input type="email" name="email" required maxlength="190" placeholder="vous@example.com"></label>
            </div>
            <label><?= e(t('ui.subject')) ?><input name="subject" required maxlength="190" placeholder="<?= e(t('ui.subject')) ?>"></label>
            <label><?= e(t('ui.message')) ?><textarea name="message" required minlength="10" rows="7" placeholder="<?= e(t('ui.message')) ?>"></textarea></label>
            <button class="btn btn-primary"><?= e(t('ui.send_message')) ?></button>
        </form>
        <div class="grid-2">
            <article class="card">
                <h3><?= e(t('pages.share_opportunity')) ?></h3>
                <p><?= e(t('pages.share_opportunity_text')) ?></p>
                <a href="<?= url('/opportunites') ?>"><?= e(t('nav.opportunities')) ?></a>
            </article>
            <article class="card">
                <h3><?= e(t('pages.professionals_companies')) ?></h3>
                <p><?= e(t('pages.professionals_companies_text')) ?></p>
                <a href="<?= url('/entreprises') ?>"><?= e(t('footer.companies')) ?></a>
            </article>
            <article class="card">
                <h3><?= e(t('nav.events')) ?></h3>
                <p><?= e(t('pages.events_card_text')) ?></p>
                <a href="<?= url('/evenements') ?>"><?= e(t('nav.events')) ?></a>
            </article>
        </div>
    </div>
</section>
