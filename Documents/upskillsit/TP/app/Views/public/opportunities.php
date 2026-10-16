<section class="page-hero">
    <h1><?= e(t('pages.opportunities_title')) ?></h1>
    <p><?= e(t('pages.opportunities_intro')) ?></p>
</section>
<section class="section">
    <form class="filters">
        <input name="q" value="<?= e($_GET['q'] ?? '') ?>" placeholder="<?= e(t('ui.search')) ?>">
        <select name="type">
            <option value=""><?= e(t('ui.all_types')) ?></option>
            <?php foreach (['Stage','Projet academique','Evenement','Concours','Hackathon','Formation','Benevolat IT'] as $type): ?><option<?= selected($_GET['type'] ?? '', $type) ?>><?= e($type) ?></option><?php endforeach; ?>
        </select>
        <button class="btn btn-primary"><?= e(t('ui.filter')) ?></button>
    </form>
    <div class="grid-3">
        <?php foreach ($items as $item): ?>
            <article class="card">
                <span class="badge"><?= e($item['type']) ?></span>
                <h2><?= e($item['title']) ?></h2>
                <p><?= e($item['organism']) ?> - <?= e($item['location']) ?></p>
                <p><?= e(mb_strimwidth($item['description'], 0, 130, '...')) ?></p>
                <a href="<?= url('/opportunite/' . $item['id']) ?>"><?= e(t('ui.view_opportunity')) ?></a>
            </article>
        <?php endforeach; ?>
    </div>
</section>
