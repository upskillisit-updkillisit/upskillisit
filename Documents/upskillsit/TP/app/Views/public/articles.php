<section class="page-hero">
    <h1><?= e(t('pages.news_title')) ?></h1>
    <p><?= e(t('pages.news_intro')) ?></p>
</section>
<section class="section">
    <div class="grid-3">
        <?php foreach ($articles as $article): ?>
            <article class="card">
                <span class="badge green"><?= e(date('d/m/Y', strtotime($article['published_at']))) ?></span>
                <h2><?= e($article['title']) ?></h2>
                <p><?= e($article['excerpt']) ?></p>
                <a href="<?= url('/actualite/' . $article['id']) ?>"><?= e(t('ui.read_article')) ?></a>
            </article>
        <?php endforeach; ?>
    </div>
</section>
