<?php
$totalUsers = max(1, (int) ($userStats['total_users'] ?? 0));
$publishedRate = (int) round(((int) ($contentStats['published_opportunities'] ?? 0) / max(1, (int) ($contentStats['opportunities'] ?? 0))) * 100);
?>
<section class="admin-command">
    <div>
        <span class="badge green"><?= e(t('admin.command_center')) ?></span>
        <h1><?= e(t('admin.title')) ?></h1>
        <p><?= e(t('admin.intro')) ?></p>
    </div>
    <div class="admin-health">
        <strong><?= $publishedRate ?>%</strong>
        <span><?= e(t('admin.published_opportunities')) ?></span>
    </div>
</section>

<section class="stats-grid admin-stats">
    <article class="stat"><span><?= (int) ($userStats['total_users'] ?? 0) ?></span><p><?= e(t('admin.users')) ?></p></article>
    <article class="stat"><span><?= (int) ($userStats['students'] ?? 0) ?></span><p><?= e(t('admin.students')) ?></p></article>
    <article class="stat"><span><?= (int) ($userStats['professionals'] ?? 0) ?></span><p><?= e(t('admin.professionals')) ?></p></article>
    <article class="stat"><span><?= (int) ($userStats['companies'] ?? 0) ?></span><p><?= e(t('admin.companies')) ?></p></article>
    <article class="stat"><span><?= (int) ($contentStats['opportunities'] ?? 0) ?></span><p><?= e(t('admin.opportunities')) ?></p></article>
    <article class="stat"><span><?= (int) ($applicationStats['total'] ?? 0) ?></span><p><?= e(t('admin.applications')) ?></p></article>
    <article class="stat"><span><?= (int) ($contentStats['events'] ?? 0) ?></span><p><?= e(t('admin.events')) ?></p></article>
    <article class="stat"><span><?= (int) ($contentStats['resources'] ?? 0) ?></span><p><?= e(t('admin.resources')) ?></p></article>
    <article class="stat"><span><?= (int) ($contentStats['articles'] ?? 0) ?></span><p><?= e(t('admin.articles')) ?></p></article>
    <article class="stat"><span><?= (int) ($messageStats['new_count'] ?? 0) ?></span><p><?= e(t('admin.contact_messages')) ?></p></article>
</section>

<section class="dashboard-grid">
    <article class="panel">
        <h2><?= e(t('admin.user_distribution')) ?></h2>
        <div class="bar-list">
            <div><span><?= e(t('admin.students')) ?></span><i style="width:<?= round(((int) ($userStats['students'] ?? 0) / $totalUsers) * 100) ?>%"></i><strong><?= (int) ($userStats['students'] ?? 0) ?></strong></div>
            <div><span><?= e(t('admin.professionals')) ?></span><i style="width:<?= round(((int) ($userStats['professionals'] ?? 0) / $totalUsers) * 100) ?>%"></i><strong><?= (int) ($userStats['professionals'] ?? 0) ?></strong></div>
            <div><span><?= e(t('admin.companies')) ?></span><i style="width:<?= round(((int) ($userStats['companies'] ?? 0) / $totalUsers) * 100) ?>%"></i><strong><?= (int) ($userStats['companies'] ?? 0) ?></strong></div>
        </div>
    </article>
    <article class="panel">
        <h2><?= e(t('admin.moderation_flow')) ?></h2>
        <div class="list-row"><span><?= e(t('admin.new_applications')) ?></span><strong><?= (int) ($applicationStats['new_count'] ?? 0) ?></strong></div>
        <div class="list-row"><span><?= e(t('admin.new_messages')) ?></span><strong><?= (int) ($messageStats['new_count'] ?? 0) ?></strong></div>
        <div class="list-row"><span><?= e(t('admin.active_accounts')) ?></span><strong><?= (int) ($userStats['active_users'] ?? 0) ?></strong></div>
        <div class="list-row"><span><?= e(t('admin.suspended_accounts')) ?></span><strong><?= (int) ($userStats['suspended_users'] ?? 0) ?></strong></div>
        <a class="btn btn-primary" href="<?= url('/admin/statistiques') ?>"><?= e(t('admin.view_statistics')) ?></a>
    </article>
</section>

<section class="dashboard-columns">
    <article class="panel">
        <h2><?= e(t('admin.latest_users')) ?></h2>
        <?php foreach ($recentUsers as $u): ?>
            <a class="list-row" href="<?= url('/admin/users?q=' . urlencode($u['email'])) ?>"><span><?= e($u['first_name'] . ' ' . $u['last_name']) ?></span><small><?= e($u['role']) ?></small></a>
        <?php endforeach; ?>
    </article>
    <article class="panel">
        <h2><?= e(t('admin.recent_opportunities')) ?></h2>
        <?php foreach ($recentOpportunities as $item): ?>
            <a class="list-row" href="<?= url('/admin/opportunites?edit=' . (int) $item['id']) ?>"><span><?= e($item['title']) ?></span><small><?= e($item['status']) ?></small></a>
        <?php endforeach; ?>
    </article>
    <article class="panel">
        <h2><?= e(t('admin.platform_agenda')) ?></h2>
        <?php foreach ($recentEvents as $item): ?>
            <a class="list-row" href="<?= url('/admin/evenements?edit=' . (int) $item['id']) ?>"><span><?= e($item['title']) ?></span><small><?= e(!empty($item['event_date']) ? date('d/m', strtotime($item['event_date'])) : '-') ?></small></a>
        <?php endforeach; ?>
    </article>
</section>
