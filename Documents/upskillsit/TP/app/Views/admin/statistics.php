<?php $maxMonth = max(1, ...array_map(fn($m) => (int) $m['total'], $monthlyUsers ?: [['total' => 1]])); ?>
<section class="admin-command">
    <div>
        <span class="badge green">Statistiques</span>
        <h1>Performance plateforme</h1>
        <p>Indicateurs calcules depuis les tables utilisateurs, contenus, candidatures et inscriptions.</p>
    </div>
</section>

<section class="stats-grid admin-stats">
    <article class="stat"><span><?= (int) ($userStats['active_users'] ?? 0) ?></span><p>Comptes actifs</p></article>
    <article class="stat"><span><?= (int) ($contentStats['published_opportunities'] ?? 0) ?></span><p>Opportunites publiees</p></article>
    <article class="stat"><span><?= (int) ($applicationStats['shortlisted_count'] ?? 0) ?></span><p>Candidats retenus</p></article>
    <article class="stat"><span><?= (int) ($contentStats['registrations'] ?? 0) ?></span><p>Inscriptions evenements</p></article>
</section>

<section class="dashboard-grid">
    <article class="panel">
        <h2>Inscriptions sur 6 mois</h2>
        <div class="bar-list">
            <?php foreach ($monthlyUsers as $month): ?>
                <div><span><?= e($month['month']) ?></span><i style="width:<?= round(((int) $month['total'] / $maxMonth) * 100) ?>%"></i><strong><?= (int) $month['total'] ?></strong></div>
            <?php endforeach; ?>
            <?php if (empty($monthlyUsers)): ?><p>Aucune inscription recente.</p><?php endif; ?>
        </div>
    </article>
    <article class="panel">
        <h2>Candidatures</h2>
        <div class="list-row"><span>Nouvelles</span><strong><?= (int) ($applicationStats['new_count'] ?? 0) ?></strong></div>
        <div class="list-row"><span>Analysees</span><strong><?= (int) ($applicationStats['reviewed_count'] ?? 0) ?></strong></div>
        <div class="list-row"><span>Retenues</span><strong><?= (int) ($applicationStats['shortlisted_count'] ?? 0) ?></strong></div>
        <div class="list-row"><span>Refusees</span><strong><?= (int) ($applicationStats['rejected_count'] ?? 0) ?></strong></div>
    </article>
</section>

<section class="dashboard-columns">
    <?php foreach ($contentByStatus as $label => $rows): ?>
        <article class="panel">
            <h2><?= e(ucfirst($label)) ?></h2>
            <?php foreach ($rows as $row): ?>
                <div class="list-row"><span><?= e($row['status']) ?></span><strong><?= (int) $row['total'] ?></strong></div>
            <?php endforeach; ?>
            <?php if (empty($rows)): ?><p>Aucune donnee.</p><?php endif; ?>
        </article>
    <?php endforeach; ?>
</section>
