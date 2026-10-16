<?php $section = $section ?? 'dashboard'; ?>

<?php if ($section === 'applications'): ?>
<section class="panel table-wrap">
    <h1><?= e(t('dashboard.applications_title')) ?></h1>
    <table>
        <thead><tr><th><?= e(t('dashboard.opportunity')) ?></th><th><?= e(t('dashboard.company')) ?></th><th><?= e(t('dashboard.status')) ?></th><th><?= e(t('dashboard.date')) ?></th></tr></thead>
        <tbody>
            <?php foreach ($applications as $application): ?>
                <tr>
                    <td><?= e($application['opportunity_title']) ?><br><small><?= e($application['opportunity_type']) ?></small></td>
                    <td><?= e($application['organism'] ?: $application['company_name'] ?: '-') ?></td>
                    <td><span class="badge"><?= e($application['status']) ?></span></td>
                    <td><?= e(date('d/m/Y', strtotime($application['created_at']))) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if (empty($applications)): ?><p><?= e(t('dashboard.no_applications')) ?></p><?php endif; ?>
</section>

<?php elseif ($section === 'notifications'): ?>
<section class="panel">
    <h1><?= e(t('dashboard.notifications_title')) ?></h1>
    <?php foreach ($notifications as $notification): ?>
        <div class="list-row">
            <span><strong><?= e($notification['title']) ?></strong><br><small><?= e($notification['body'] ?? '') ?></small></span>
            <small><?= e(date('d/m/Y', strtotime($notification['created_at']))) ?></small>
        </div>
    <?php endforeach; ?>
    <?php if (empty($notifications)): ?><p><?= e(t('dashboard.no_notifications')) ?></p><?php endif; ?>
</section>

<?php else: ?>
<section class="dashboard-grid">
    <article class="panel hero-panel">
        <h1><?= e(t('dashboard.hello')) ?> <?= e($user['first_name']) ?>, <?= e(t('dashboard.ready')) ?></h1>
        <p><?= e(t('dashboard.intro')) ?></p>
        <div class="progress"><span style="width:<?= (int) $completion ?>%"></span></div>
        <strong><?= (int) $completion ?>% <?= e(t('dashboard.profile_complete')) ?></strong>
    </article>
    <article class="panel">
        <h2><?= e(t('dashboard.quick_actions')) ?></h2>
        <a class="btn btn-primary" href="<?= url('/profil') ?>"><?= e(t('dashboard.complete_profile')) ?></a>
        <a class="btn btn-light" href="<?= url('/opportunites') ?>"><?= e(t('dashboard.view_opportunities')) ?></a>
        <a class="btn btn-light" href="<?= url('/candidatures') ?>"><?= e(t('dashboard.track_applications')) ?></a>
    </article>
</section>

<section class="stats-grid">
    <article class="stat"><span><?= (int) ($applicationStats['total'] ?? 0) ?></span><p><?= e(t('dashboard.applications')) ?></p></article>
    <article class="stat"><span><?= (int) ($applicationStats['shortlisted_count'] ?? 0) ?></span><p><?= e(t('dashboard.shortlisted')) ?></p></article>
    <article class="stat"><span><?= count($notifications ?? []) ?></span><p><?= e(t('dashboard.notifications_title')) ?></p></article>
</section>

<section class="dashboard-columns">
    <div class="panel">
        <h2><?= e(t('dashboard.latest_opportunities')) ?></h2>
        <?php foreach ($opportunities as $item): ?>
            <a class="list-row" href="<?= url('/opportunite/' . $item['id']) ?>"><span><?= e($item['title']) ?></span><small><?= e($item['type']) ?></small></a>
        <?php endforeach; ?>
    </div>
    <div class="panel">
        <h2><?= e(t('dashboard.recommended_resources')) ?></h2>
        <?php foreach ($resources as $item): ?>
            <a class="list-row" href="<?= url('/ressource/' . $item['id']) ?>"><span><?= e($item['title']) ?></span><small><?= e($item['category']) ?></small></a>
        <?php endforeach; ?>
    </div>
    <div class="panel">
        <h2><?= e(t('dashboard.upcoming_events')) ?></h2>
        <?php foreach ($events as $event): ?>
            <a class="list-row" href="<?= url('/evenement/' . $event['id']) ?>"><span><?= e($event['title']) ?></span><small><?= e(date('d/m/Y', strtotime($event['event_date']))) ?></small></a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
