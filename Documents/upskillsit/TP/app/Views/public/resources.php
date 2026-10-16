<section class="page-hero">
    <h1><?= e(t('pages.resources_title')) ?></h1>
    <p><?= e(t('pages.resources_intro')) ?></p>
</section>
<section class="section">
    <form class="filters">
        <input name="q" value="<?= e($_GET['q'] ?? '') ?>" placeholder="<?= e(t('ui.search')) ?>">
        <select name="category">
            <option value=""><?= e(t('ui.all_categories')) ?></option>
            <?php foreach (['Orientation','Recherche de stage','CV','Entretien','LinkedIn','GitHub','Soft skills','Competences techniques','Employabilite','Carriere','Etudes','Certifications','Veille technologique'] as $cat): ?><option<?= selected($_GET['category'] ?? '', $cat) ?>><?= e($cat) ?></option><?php endforeach; ?>
        </select>
        <button class="btn btn-primary"><?= e(t('ui.filter')) ?></button>
    </form>
    <div class="grid-3">
        <?php foreach ($items as $item): ?>
            <article class="card">
                <span class="badge green"><?= e($item['category']) ?></span>
                <h2><?= e($item['title']) ?></h2>
                <p><?= e($item['description']) ?></p>
                <a href="<?= url('/ressource/' . $item['id']) ?>"><?= e(t('ui.read_resource')) ?></a>
            </article>
        <?php endforeach; ?>
    </div>
</section>
