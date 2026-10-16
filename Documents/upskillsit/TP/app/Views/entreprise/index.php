<?php use App\Core\CSRF; ?>
<?php
$active = fn(string $name): string => $section === $name ? ' is-active' : '';
$edit = $editingOpportunity ?? [];
$published = count(array_filter($opportunities, fn($o) => ($o['status'] ?? '') === 'published'));
$waiting = (int) ($applicationStats['new_count'] ?? 0);
?>
<section class="company-hero">
    <div>
        <span class="badge green">Espace recruteur</span>
        <h1><?= e($profile['company_name'] ?: 'Dashboard entreprise') ?></h1>
        <p><?= e($profile['description'] ?: 'Pilotez vos recrutements, publiez vos opportunites et suivez chaque candidature depuis un espace dedie aux entreprises.') ?></p>
        <div class="hero-meta">
            <span><?= e($profile['sector'] ?: 'Secteur a renseigner') ?></span>
            <span><?= e($profile['location'] ?: 'Localisation a renseigner') ?></span>
            <span><?= e($profile['contact_person'] ?: 'Contact RH a renseigner') ?></span>
        </div>
    </div>
    <div class="profile-score"><strong><?= (int) $completion ?>%</strong><span>Profil complet</span></div>
</section>

<nav class="tabs company-tabs">
    <a class="<?= $active('dashboard') ?>" href="<?= url('/entreprise') ?>">Dashboard</a>
    <a class="<?= $active('profile') ?>" href="<?= url('/entreprise/profil') ?>">Profil entreprise</a>
    <a class="<?= $active('opportunities') ?>" href="<?= url('/entreprise/opportunites') ?>">Mes opportunites</a>
    <a class="<?= $active('applications') ?>" href="<?= url('/entreprise/candidatures') ?>">Candidatures</a>
    <a class="<?= $active('talents') ?>" href="<?= url('/entreprise/profils') ?>">Talents</a>
    <a class="<?= $active('events') ?>" href="<?= url('/entreprise/evenements') ?>">Evenements</a>
</nav>

<?php if ($section === 'profile'): ?>
<form class="form panel" method="post" action="<?= url('/entreprise/profil') ?>">
    <?= CSRF::field() ?>
    <h2>Profil entreprise</h2>
    <div class="grid-2">
        <label>Nom entreprise<input name="company_name" value="<?= e($profile['company_name'] ?? '') ?>" required></label>
        <label>Secteur<input name="sector" value="<?= e($profile['sector'] ?? '') ?>"></label>
        <label>Site web<input type="url" name="website" value="<?= e($profile['website'] ?? '') ?>"></label>
        <label>Localisation<input name="location" value="<?= e($profile['location'] ?? '') ?>"></label>
        <label>Contact RH<input name="contact_person" value="<?= e($profile['contact_person'] ?? '') ?>"></label>
        <label>Liens publics<input name="public_links" value="<?= e($profile['public_links'] ?? '') ?>"></label>
    </div>
    <label>Description<textarea name="description"><?= e($profile['description'] ?? '') ?></textarea></label>
    <label>Besoins de recrutement<textarea name="hiring_needs"><?= e($profile['hiring_needs'] ?? '') ?></textarea></label>
    <button class="btn btn-primary">Enregistrer le profil</button>
</form>

<?php elseif ($section === 'opportunities'): ?>
<section class="admin-grid">
    <form class="form panel" method="post" action="<?= url('/entreprise/opportunites') ?>">
        <?= CSRF::field() ?>
        <input type="hidden" name="id" value="<?= e($edit['id'] ?? '') ?>">
        <h2><?= !empty($edit['id']) ? 'Modifier une opportunite' : 'Creer une opportunite' ?></h2>
        <label>Titre<input name="title" value="<?= e($edit['title'] ?? '') ?>" required></label>
        <label>Type<select name="type"><option value="Stage"<?= selected($edit['type'] ?? '', 'Stage') ?>>Stage</option><option value="Emploi junior"<?= selected($edit['type'] ?? '', 'Emploi junior') ?>>Emploi junior</option><option value="Alternance"<?= selected($edit['type'] ?? '', 'Alternance') ?>>Alternance</option><option value="Projet"<?= selected($edit['type'] ?? '', 'Projet') ?>>Projet</option><option value="Formation"<?= selected($edit['type'] ?? '', 'Formation') ?>>Formation</option></select></label>
        <label>Organisme<input name="organism" value="<?= e($edit['organism'] ?? ($profile['company_name'] ?? '')) ?>" required></label>
        <label>Description<textarea name="description" required><?= e($edit['description'] ?? '') ?></textarea></label>
        <label>Profil cible<textarea name="target_profile"><?= e($edit['target_profile'] ?? '') ?></textarea></label>
        <label>Competences<textarea name="skills"><?= e($edit['skills'] ?? '') ?></textarea></label>
        <label>Localisation<input name="location" value="<?= e($edit['location'] ?? ($profile['location'] ?? '')) ?>"></label>
        <label>Date limite<input type="date" name="deadline" value="<?= e($edit['deadline'] ?? '') ?>"></label>
        <label>Modalites<textarea name="application_method"><?= e($edit['application_method'] ?? '') ?></textarea></label>
        <label>Lien candidature<input type="url" name="application_link" value="<?= e($edit['application_link'] ?? '') ?>"></label>
        <label>Statut<select name="status"><option value="draft"<?= selected($edit['status'] ?? '', 'draft') ?>>Brouillon</option><option value="published"<?= selected($edit['status'] ?? '', 'published') ?>>Publie</option><option value="closed"<?= selected($edit['status'] ?? '', 'closed') ?>>Ferme</option></select></label>
        <button class="btn btn-primary">Enregistrer</button>
    </form>
    <div class="panel table-wrap"><table><thead><tr><th>Opportunite</th><th>Statut</th><th>Candidatures</th><th>Date limite</th><th>Actions</th></tr></thead><tbody><?php foreach ($opportunities as $item): ?><tr><td><strong><?= e($item['title']) ?></strong><br><small><?= e($item['type']) ?></small></td><td><span class="badge"><?= e($item['status']) ?></span></td><td><?= (int) ($item['applications_count'] ?? 0) ?></td><td><?= e($item['deadline'] ?? '-') ?></td><td><div class="inline-actions"><a class="btn btn-light" href="<?= url('/entreprise/opportunites?edit=' . (int) $item['id']) ?>">Modifier</a><form method="post" action="<?= url('/entreprise/opportunites/' . (int) $item['id'] . '/status') ?>"><?= CSRF::field() ?><input type="hidden" name="status" value="<?= $item['status'] === 'published' ? 'draft' : 'published' ?>"><button class="btn btn-primary"><?= $item['status'] === 'published' ? 'Depublier' : 'Publier' ?></button></form><form method="post" action="<?= url('/entreprise/opportunites/' . (int) $item['id'] . '/delete') ?>"><?= CSRF::field() ?><button class="btn btn-danger">Supprimer</button></form></div></td></tr><?php endforeach; ?></tbody></table></div>
</section>

<?php elseif ($section === 'applications'): ?>
<section class="panel table-wrap"><table><thead><tr><th>Candidat</th><th>Opportunite</th><th>Profil</th><th>Statut</th><th>Decision</th></tr></thead><tbody><?php foreach ($applications as $item): ?><tr><td><strong><?= e($item['first_name'] . ' ' . $item['last_name']) ?></strong><br><small><?= e($item['email']) ?></small></td><td><?= e($item['opportunity_title']) ?><br><small><?= e($item['opportunity_type']) ?></small></td><td><?= e($item['function_title'] ?: trim(($item['study_level'] ?? '') . ' ' . ($item['major'] ?? 'Profil IT'))) ?><br><small><?= e($item['skills'] ?: $item['location'] ?: '') ?></small></td><td><span class="badge"><?= e($item['status']) ?></span></td><td><form class="inline-actions" method="post" action="<?= url('/entreprise/candidatures/' . (int) $item['id'] . '/status') ?>"><?= CSRF::field() ?><select name="status"><option value="new"<?= selected($item['status'] ?? '', 'new') ?>>Nouveau</option><option value="reviewed"<?= selected($item['status'] ?? '', 'reviewed') ?>>Vu</option><option value="shortlisted"<?= selected($item['status'] ?? '', 'shortlisted') ?>>Retenu</option><option value="rejected"<?= selected($item['status'] ?? '', 'rejected') ?>>Refuse</option></select><button class="btn btn-primary">Mettre a jour</button></form></td></tr><?php endforeach; ?></tbody></table></section>

<?php elseif ($section === 'talents'): ?>
<form class="filters" method="get" action="<?= url('/entreprise/profils') ?>"><input name="q" value="<?= e($_GET['q'] ?? '') ?>" placeholder="Rechercher PHP, data, cloud..."><select name="role"><option value="">Tous les profils</option></select><button class="btn btn-primary">Rechercher</button></form>
<section class="grid-3"><?php foreach ($talents as $talent): ?><article class="card talent-card"><span class="badge"><?= e($talent['role'] === 'professional' ? 'Professionnel' : 'Etudiant') ?></span><h3><?= e($talent['first_name'] . ' ' . $talent['last_name']) ?></h3><p><?= e($talent['function_title'] ?: trim(($talent['study_level'] ?? '') . ' - ' . ($talent['major'] ?? 'Profil IT'))) ?></p><small><?= e($talent['skills'] ?: trim(($talent['programming_languages'] ?? '') . ' ' . ($talent['frameworks'] ?? ''))) ?></small></article><?php endforeach; ?></section>

<?php elseif ($section === 'events'): ?>
<section class="grid-3"><?php foreach ($events as $event): ?><article class="card"><span class="badge"><?= e(date('d/m/Y H:i', strtotime($event['event_date']))) ?></span><h3><?= e($event['title']) ?></h3><p><?= e($event['description']) ?></p><small><?= e($event['location'] ?? '') ?></small></article><?php endforeach; ?></section>

<?php else: ?>
<section class="stats-grid company-stats">
    <article class="stat"><span><?= count($opportunities) ?></span><p>Opportunites publiees/creees</p></article>
    <article class="stat"><span><?= (int) ($applicationStats['total'] ?? 0) ?></span><p>Candidatures recues</p></article>
    <article class="stat"><span><?= $waiting ?></span><p>En attente</p></article>
    <article class="stat"><span><?= count($talents) ?></span><p>Talents disponibles</p></article>
</section>
<section class="dashboard-grid">
    <article class="panel">
        <h2>Pipeline recrutement</h2>
        <div class="list-row"><span>Opportunites publiees</span><strong><?= $published ?></strong></div>
        <div class="list-row"><span>Nouvelles candidatures</span><strong><?= $waiting ?></strong></div>
        <div class="list-row"><span>Profils retenus</span><strong><?= (int) ($applicationStats['shortlisted_count'] ?? 0) ?></strong></div>
        <a class="btn btn-primary" href="<?= url('/entreprise/opportunites') ?>">Creer une opportunite</a>
    </article>
    <article class="panel">
        <h2>Dernieres candidatures</h2>
        <?php foreach (array_slice($applications, 0, 5) as $item): ?><a class="list-row" href="<?= url('/entreprise/candidatures') ?>"><span><?= e($item['first_name'] . ' ' . $item['last_name']) ?></span><small><?= e($item['status']) ?></small></a><?php endforeach; ?>
        <?php if (empty($applications)): ?><p>Aucune candidature recue.</p><?php endif; ?>
    </article>
</section>
<section class="dashboard-columns">
    <article class="panel"><h2>Dernieres opportunites</h2><?php foreach (array_slice($opportunities, 0, 5) as $o): ?><a class="list-row" href="<?= url('/entreprise/opportunites?edit=' . (int) $o['id']) ?>"><span><?= e($o['title']) ?></span><small><?= e($o['status']) ?></small></a><?php endforeach; ?></article>
    <article class="panel"><h2>Talents recents</h2><?php foreach (array_slice($talents, 0, 5) as $talent): ?><a class="list-row" href="<?= url('/entreprise/profils?q=' . urlencode($talent['first_name'])) ?>"><span><?= e($talent['first_name'] . ' ' . $talent['last_name']) ?></span><small><?= e($talent['role']) ?></small></a><?php endforeach; ?></article>
    <article class="panel"><h2>Evenements</h2><?php foreach ($events as $event): ?><a class="list-row" href="<?= url('/evenement/' . (int) $event['id']) ?>"><span><?= e($event['title']) ?></span><small><?= e(date('d/m', strtotime($event['event_date']))) ?></small></a><?php endforeach; ?></article>
</section>
<?php endif; ?>
