<section class="page-hero detail">
    <span class="badge"><?= e($item['type'] ?? $item['category'] ?? (!empty($item['event_date']) ? date('d/m/Y', strtotime($item['event_date'])) : t('detail.article'))) ?></span>
    <h1><?= e($item['title']) ?></h1>
    <p><?= e($item['description'] ?? $item['excerpt'] ?? '') ?></p>
</section>
<section class="section narrow">
    <article class="card detail-card">
        <?php if ($type === 'opportunity'): ?>
            <p><strong><?= e(t('detail.organism')) ?>:</strong> <?= e($item['organism']) ?></p>
            <p><strong><?= e(t('detail.target_profile')) ?>:</strong> <?= e($item['target_profile']) ?></p>
            <p><strong><?= e(t('detail.skills')) ?>:</strong> <?= e($item['skills']) ?></p>
            <p><strong><?= e(t('detail.location')) ?>:</strong> <?= e($item['location']) ?></p>
            <p><strong><?= e(t('detail.deadline')) ?>:</strong> <?= e($item['deadline']) ?></p>
            <?php if (!empty($item['application_method'])): ?><p><strong><?= e(t('detail.application_method')) ?>:</strong> <?= e($item['application_method']) ?></p><?php endif; ?>
            <?php if (!empty($item['application_link'])): ?><p><strong><?= e(t('detail.external_link')) ?>:</strong> <a href="<?= e($item['application_link']) ?>" target="_blank" rel="noopener noreferrer"><?= e($item['application_link']) ?></a></p><?php endif; ?>
            <?php if (App\Core\Auth::check() && in_array(App\Core\Auth::role(), ['student', 'professional'], true)): ?>
                <form class="form" method="post" action="<?= url('/opportunite/' . (int) $item['id'] . '/candidater') ?>">
                    <?= App\Core\CSRF::field() ?>
                    <label><?= e(t('detail.motivation_message')) ?><textarea name="message" rows="4" placeholder="<?= e(t('detail.motivation_placeholder')) ?>"></textarea></label>
                    <button class="btn btn-primary"><?= e(t('ui.submit_application')) ?></button>
                </form>
            <?php elseif (App\Core\Auth::check()): ?>
                <p><?= e(t('detail.application_reserved')) ?></p>
            <?php else: ?>
                <p><a class="btn btn-primary" href="<?= url('/connexion') ?>"><?= e(t('ui.login_to_apply')) ?></a></p>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($type === 'resource'): ?>
            <p><strong><?= e(t('detail.source')) ?>:</strong> <?= e($item['author'] ?: $item['source']) ?></p>
            <div class="richtext"><?= nl2br(e($item['content'])) ?></div>
            <?php if ($item['link']): ?><a class="btn btn-primary" href="<?= e($item['link']) ?>"><?= e(t('ui.opening_link')) ?></a><?php endif; ?>
        <?php endif; ?>

        <?php if ($type === 'article'): ?>
            <p><strong><?= e(t('detail.published_at')) ?>:</strong> <?= e(date('d/m/Y', strtotime($item['published_at'] ?? $item['created_at']))) ?></p>
            <div class="richtext"><?= nl2br(e($item['content'])) ?></div>
        <?php endif; ?>

        <?php if ($type === 'event'): ?>
            <p><strong><?= e(t('detail.date')) ?>:</strong> <?= e(date('d/m/Y H:i', strtotime($item['event_date']))) ?></p>
            <p><strong><?= e(t('detail.place')) ?>:</strong> <?= e($item['location']) ?></p>
            <p><strong><?= e(t('detail.speaker')) ?>:</strong> <?= e($item['speaker']) ?></p>
            <p><strong><?= e(t('detail.capacity')) ?>:</strong> <?= (int) $item['confirmed_count'] ?>/<?= (int) $item['capacity'] ?></p>
            <button
                class="btn btn-primary"
                data-event-register="<?= e(url('/evenement/' . $item['id'] . '/inscription')) ?>"
                data-label-registering="<?= e(t('ui.event_registering')) ?>"
                data-label-waiting="<?= e(t('ui.event_waiting')) ?>"
                data-label-registered="<?= e(t('ui.event_registered')) ?>"
                data-label-default="<?= e(t('ui.register_event')) ?>"
            ><?= e(t('ui.register_event')) ?></button>
            <p class="async-message" data-event-message data-label-error="<?= e(t('ui.event_error')) ?>" data-label-response="<?= e(t('ui.event_response')) ?>"></p>
        <?php endif; ?>
    </article>
</section>
