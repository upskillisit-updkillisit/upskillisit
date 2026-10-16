<section class="admin-command">
    <div>
        <span class="badge green">Notifications admin</span>
        <h1>Signaux importants</h1>
        <p>Synthese des points qui demandent une attention administrative.</p>
    </div>
</section>

<section class="dashboard-columns">
    <article class="panel">
        <h2>Messages contact</h2>
        <div class="list-row"><span>Nouveaux messages</span><strong><?= (int) ($messageStats['new_count'] ?? 0) ?></strong></div>
        <div class="list-row"><span>Total messages</span><strong><?= (int) ($messageStats['total'] ?? 0) ?></strong></div>
        <a class="btn btn-primary" href="<?= url('/admin/messages') ?>">Ouvrir les messages</a>
    </article>
    <article class="panel">
        <h2>Contenus a piloter</h2>
        <div class="list-row"><span>Opportunites</span><strong><?= (int) ($contentStats['opportunities'] ?? 0) ?></strong></div>
        <div class="list-row"><span>Evenements</span><strong><?= (int) ($contentStats['events'] ?? 0) ?></strong></div>
        <div class="list-row"><span>Articles</span><strong><?= (int) ($contentStats['articles'] ?? 0) ?></strong></div>
    </article>
    <article class="panel">
        <h2>Suivi demo</h2>
        <p>Les notifications exploitent les donnees operationnelles du projet et orientent vers les actions admin utiles.</p>
    </article>
</section>
