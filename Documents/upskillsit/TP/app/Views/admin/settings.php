<section class="admin-command">
    <div>
        <span class="badge green">Parametres</span>
        <h1>Configuration administrative</h1>
        <p>Vue de controle pour verifier les modules actifs et guider les operations de demonstration.</p>
    </div>
</section>

<section class="dashboard-columns">
    <article class="panel">
        <h2>Modules connectes</h2>
        <div class="list-row"><span>Utilisateurs</span><strong><?= (int) ($userStats['total_users'] ?? 0) ?></strong></div>
        <div class="list-row"><span>Opportunites</span><strong><?= (int) ($contentStats['opportunities'] ?? 0) ?></strong></div>
        <div class="list-row"><span>Ressources</span><strong><?= (int) ($contentStats['resources'] ?? 0) ?></strong></div>
        <div class="list-row"><span>Evenements</span><strong><?= (int) ($contentStats['events'] ?? 0) ?></strong></div>
    </article>
    <article class="panel">
        <h2>Actions rapides</h2>
        <a class="list-row" href="<?= url('/admin/articles') ?>"><span>Publier une actualite</span><small>Articles</small></a>
        <a class="list-row" href="<?= url('/admin/opportunites') ?>"><span>Creer une opportunite</span><small>Recrutement</small></a>
        <a class="list-row" href="<?= url('/admin/users') ?>"><span>Auditer les comptes</span><small>Utilisateurs</small></a>
    </article>
    <article class="panel">
        <h2>Etat demo</h2>
        <p>Les tableaux de bord consomment les donnees MySQL existantes et restent compatibles avec l'architecture MVC du projet.</p>
    </article>
</section>
