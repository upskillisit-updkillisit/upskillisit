<section class="page-hero">
    <h1><?= e(t('pages.events_title')) ?></h1>
    <p><?= e(t('pages.events_intro')) ?></p>
</section>
<section class="section">
    <form class="filters">
        <input name="q" value="<?= e($_GET['q'] ?? '') ?>" placeholder="<?= e(t('ui.search')) ?>">
        <button class="btn btn-primary"><?= e(t('ui.filter')) ?></button>
    </form>
    <div class="grid-3">
        <?php foreach ($items as $event): ?>
            <article class="card">
                <span class="badge"><?= e(date('d/m/Y H:i', strtotime($event['event_date']))) ?></span>
                <h2><?= e($event['title']) ?></h2>
                <p><?= e($event['location']) ?> - <?= e($event['speaker']) ?></p>
                <p><?= (int) $event['confirmed_count'] ?>/<?= (int) $event['capacity'] ?> <?= e(t('ui.confirmed_places')) ?></p>
                <a href="<?= url('/evenement/' . $event['id']) ?>"><?= e(t('ui.register_event')) ?></a>
            </article>
        <?php endforeach; ?>
    </div>
</section>
