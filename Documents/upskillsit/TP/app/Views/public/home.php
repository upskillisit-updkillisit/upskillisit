<section class="hero">
    <div class="hero-copy">
        <span class="eyebrow"><?= e(t('home.eyebrow')) ?></span>
        <h1><?= e(t('home.title')) ?></h1>
        <p><?= e(t('home.intro')) ?></p>
        <div class="actions">
            <a class="btn btn-primary" href="<?= url('/opportunites') ?>"><?= e(t('home.discover_opportunities')) ?></a>
            <a class="btn btn-ghost" href="<?= url('/services') ?>"><?= e(t('home.discover_services')) ?></a>
        </div>
    </div>
    <div class="hero-visual" aria-hidden="true">
        <div class="hero-stat"><strong><?= count($opportunities) ?></strong><span><?= e(t('home.recent_opportunities')) ?></span></div>
        <div class="hero-stat"><strong><?= count($events) ?></strong><span><?= e(t('home.upcoming_events')) ?></span></div>
        <div class="hero-stat"><strong><?= count($articles) ?></strong><span><?= e(t('home.resources_news')) ?></span></div>
    </div>
</section>

<section class="section">
    <div class="section-head">
        <div>
            <span class="eyebrow"><?= e(t('home.what')) ?></span>
            <h2><?= e(t('home.what_title')) ?></h2>
        </div>
        <p><?= e(t('home.what_text')) ?></p>
    </div>
    <div class="grid-3">
        <article class="card service-card" data-reveal>
            <span class="icon-mark">01</span>
            <h3><?= e(t('home.profile_card')) ?></h3>
            <p><?= e(t('home.profile_card_text')) ?></p>
        </article>
        <article class="card service-card" data-reveal>
            <span class="icon-mark">02</span>
            <h3><?= e(t('home.opportunity_card')) ?></h3>
            <p><?= e(t('home.opportunity_card_text')) ?></p>
        </article>
        <article class="card service-card" data-reveal>
            <span class="icon-mark">03</span>
            <h3><?= e(t('home.progress_card')) ?></h3>
            <p><?= e(t('home.progress_card_text')) ?></p>
        </article>
    </div>
</section>

<section class="section tint">
    <div class="section-head">
        <h2><?= e(t('home.recent_opportunities')) ?></h2>
        <a class="btn btn-light" href="<?= url('/opportunites') ?>"><?= e(t('home.all')) ?></a>
    </div>
    <div class="grid-3">
        <?php foreach ($opportunities as $item): ?>
            <article class="card">
                <span class="badge"><?= e($item['type']) ?></span>
                <h3><?= e($item['title']) ?></h3>
                <p><?= e($item['organism'] ?? '') ?> - <?= e($item['location'] ?? '') ?></p>
                <a href="<?= url('/opportunite/' . (int) $item['id']) ?>"><?= e(t('ui.view_opportunity')) ?></a>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="section">
    <div class="feature-row">
        <article class="split-panel">
            <span class="eyebrow"><?= e(t('home.join_title')) ?></span>
            <h2><?= e(t('home.learn_meet')) ?></h2>
            <p><?= e(t('home.join_text')) ?></p>
            <div class="metric-strip">
                <div class="metric"><strong><?= count($opportunities) ?></strong><span><?= e(t('nav.opportunities')) ?></span></div>
                <div class="metric"><strong><?= count($events) ?></strong><span><?= e(t('nav.events')) ?></span></div>
                <div class="metric"><strong><?= count($articles) ?></strong><span><?= e(t('nav.news')) ?></span></div>
                <div class="metric"><strong>IT</strong><span><?= e(t('footer.communities')) ?></span></div>
            </div>
        </article>
        <div class="timeline">
            <article class="timeline-item"><span>1</span><div><h3><?= e(t('home.students_title')) ?></h3><p><?= e(t('home.students_text')) ?></p></div></article>
            <article class="timeline-item"><span>2</span><div><h3><?= e(t('home.professionals_title')) ?></h3><p><?= e(t('home.professionals_text')) ?></p></div></article>
            <article class="timeline-item"><span>3</span><div><h3><?= e(t('home.prepare_title')) ?></h3><p><?= e(t('home.services_text')) ?></p></div></article>
        </div>
    </div>
</section>

<section class="section alt">
    <div class="section-head">
        <div>
            <span class="eyebrow"><?= e(t('home.career_resources')) ?></span>
            <h2><?= e(t('home.prepare_title')) ?></h2>
        </div>
        <a class="btn btn-light" href="<?= url('/ressources') ?>"><?= e(t('home.explore_resources')) ?></a>
    </div>
    <div class="grid-3">
        <?php foreach (array_slice($articles, 0, 3) as $article): ?>
            <article class="card">
                <span class="badge green"><?= e(date('d/m/Y', strtotime($article['published_at'] ?? $article['created_at'] ?? 'now'))) ?></span>
                <h3><?= e($article['title']) ?></h3>
                <p><?= e($article['excerpt'] ?? '') ?></p>
                <a href="<?= url('/actualite/' . (int) $article['id']) ?>"><?= e(t('ui.read_article')) ?></a>
            </article>
        <?php endforeach; ?>
    </div>
</section>
