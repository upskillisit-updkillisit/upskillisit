<?php use App\Core\CSRF; ?>
<?php
$skills = array_values(array_filter(array_map('trim', explode(',', (string) ($profile['skills'] ?? '')))));
$active = fn(string $name): string => $section === $name ? ' is-active' : '';
$fullName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
$initials = strtoupper(substr($user['first_name'] ?? 'U', 0, 1) . substr($user['last_name'] ?? 'P', 0, 1));
$editProject = !empty($_GET['edit_project']) ? array_values(array_filter($projects, fn($p) => (int) $p['id'] === (int) $_GET['edit_project']))[0] ?? [] : [];
$editExp = !empty($_GET['edit_exp']) ? array_values(array_filter($experiences, fn($e) => (int) $e['id'] === (int) $_GET['edit_exp']))[0] ?? [] : [];
?>
<section class="pro-hero">
    <div class="avatar-xl"><?= e($initials) ?></div>
    <div>
        <span class="badge green">Talent IT</span>
        <h1><?= e($profile['function_title'] ?: ($fullName ?: 'Profil professionnel')) ?></h1>
        <p><?= e($profile['bio'] ?: 'Construisez un profil clair, valorisez vos competences et postulez aux opportunites publiees sur UpSkillsIT.') ?></p>
        <div class="hero-meta">
            <span><?= e($fullName ?: 'Nom a renseigner') ?></span>
            <span><?= e($profile['location'] ?: 'Localisation a renseigner') ?></span>
            <span><?= (int) ($profile['experience_years'] ?? 0) ?> ans experience</span>
        </div>
    </div>
    <div class="profile-score"><strong><?= (int) $completion ?>%</strong><span>Profil complet</span></div>
</section>

<nav class="tabs professional-tabs">
    <a class="<?= $active('dashboard') ?>" href="<?= url('/professionnel') ?>">Dashboard</a>
    <a class="<?= $active('profile') ?>" href="<?= url('/professionnel/profil') ?>">Mon profil</a>
    <a class="<?= $active('skills') ?>" href="<?= url('/professionnel/competences') ?>">Competences</a>
    <a class="<?= $active('experiences') ?>" href="<?= url('/professionnel/experiences') ?>">Experiences</a>
    <a class="<?= $active('projects') ?>" href="<?= url('/professionnel/projets') ?>">Projets</a>
    <a class="<?= $active('opportunities') ?>" href="<?= url('/professionnel/opportunites') ?>">Opportunites</a>
    <a class="<?= $active('applications') ?>" href="<?= url('/professionnel/candidatures') ?>">Candidatures</a>
    <a class="<?= $active('resources') ?>" href="<?= url('/professionnel/ressources') ?>">Ressources</a>
    <a class="<?= $active('events') ?>" href="<?= url('/professionnel/evenements') ?>">Evenements</a>
    <a class="<?= $active('notifications') ?>" href="<?= url('/professionnel/notifications') ?>">Notifications</a>
</nav>

<?php if ($section === 'profile'): ?>
<form class="form panel" method="post" action="<?= url('/professionnel/profil') ?>">
    <?= CSRF::field() ?>
    <h2>Profil professionnel</h2>
    <div class="grid-2">
        <label>Metier<input name="function_title" value="<?= e($profile['function_title'] ?? '') ?>" required></label>
        <label>Entreprise actuelle<input name="company" value="<?= e($profile['company'] ?? '') ?>"></label>
        <label>Domaine d'expertise<input name="expertise_domain" value="<?= e($profile['expertise_domain'] ?? '') ?>"></label>
        <label>Annees d'experience<input type="number" min="0" name="experience_years" value="<?= e($profile['experience_years'] ?? '') ?>"></label>
        <label>Localisation<input name="location" value="<?= e($profile['location'] ?? '') ?>"></label>
        <label>Liens publics<input name="public_links" value="<?= e($profile['public_links'] ?? '') ?>"></label>
    </div>
    <label>Competences<textarea name="skills" placeholder="PHP, Laravel, MySQL, React, Cloud"><?= e($profile['skills'] ?? '') ?></textarea></label>
    <label>Parcours<textarea name="career_path"><?= e($profile['career_path'] ?? '') ?></textarea></label>
    <label>Centres d'interet<textarea name="interests"><?= e($profile['interests'] ?? '') ?></textarea></label>
    <label>Bio<textarea name="bio"><?= e($profile['bio'] ?? '') ?></textarea></label>
    <button class="btn btn-primary">Enregistrer le profil</button>
</form>

<?php elseif ($section === 'skills'): ?>
<section class="dashboard-grid">
    <article class="panel">
        <h2>Stack principale</h2>
        <?php foreach ($skills ?: ['PHP', 'MySQL', 'Architecture web'] as $skill): ?><span class="skill-chip"><?= e($skill) ?></span><?php endforeach; ?>
    </article>
    <article class="panel">
        <h2>Positionnement</h2>
        <div class="list-row"><span>Domaine</span><strong><?= e($profile['expertise_domain'] ?: 'A definir') ?></strong></div>
        <div class="list-row"><span>Experience</span><strong><?= (int) ($profile['experience_years'] ?? 0) ?> ans</strong></div>
        <div class="list-row"><span>Projets</span><strong><?= count($projects) ?></strong></div>
    </article>
</section>

<?php elseif ($section === 'experiences'): ?>
<section class="dashboard-grid">
    <article class="panel">
        <h2>Parcours</h2>
        <?php foreach ($experiences as $experience): ?>
            <div class="list-row">
                <span><strong><?= e($experience['title']) ?></strong><br><small><?= e($experience['organization'] ?: 'Organisation') ?> - <?= e($experience['type']) ?></small></span>
                <span class="inline-actions"><a class="btn btn-light" href="<?= url('/professionnel/experiences?edit_exp=' . (int) $experience['id']) ?>">Modifier</a><form method="post" action="<?= url('/professionnel/experiences/' . (int) $experience['id'] . '/delete') ?>"><?= CSRF::field() ?><button class="btn btn-danger">Supprimer</button></form></span>
            </div>
        <?php endforeach; ?>
        <?php if (empty($experiences)): ?><p>Aucune experience enregistree.</p><?php endif; ?>
    </article>
    <form class="form panel" method="post" action="<?= url('/professionnel/experiences') ?>">
        <?= CSRF::field() ?>
        <input type="hidden" name="id" value="<?= e($editExp['id'] ?? '') ?>">
        <h2><?= $editExp ? 'Modifier une experience' : 'Ajouter une experience' ?></h2>
        <label>Type<select name="type"><option>Stage</option><option>Emploi</option><option>Freelance</option><option>Projet</option></select></label>
        <label>Titre<input name="title" value="<?= e($editExp['title'] ?? '') ?>" required></label>
        <label>Organisation<input name="organization" value="<?= e($editExp['organization'] ?? '') ?>"></label>
        <div class="grid-2"><label>Debut<input type="date" name="started_at" value="<?= e($editExp['started_at'] ?? '') ?>"></label><label>Fin<input type="date" name="ended_at" value="<?= e($editExp['ended_at'] ?? '') ?>"></label></div>
        <label>Description<textarea name="description"><?= e($editExp['description'] ?? '') ?></textarea></label>
        <button class="btn btn-primary">Enregistrer</button>
    </form>
</section>

<?php elseif ($section === 'projects'): ?>
<section class="dashboard-grid">
    <article class="panel">
        <h2>Portfolio projets</h2>
        <?php foreach ($projects as $project): ?>
            <div class="list-row">
                <span><strong><?= e($project['name']) ?></strong><br><small><?= e($project['technologies'] ?: 'Technologies a preciser') ?></small></span>
                <span class="inline-actions"><a class="btn btn-light" href="<?= url('/professionnel/projets?edit_project=' . (int) $project['id']) ?>">Modifier</a><form method="post" action="<?= url('/professionnel/projets/' . (int) $project['id'] . '/delete') ?>"><?= CSRF::field() ?><button class="btn btn-danger">Supprimer</button></form></span>
            </div>
        <?php endforeach; ?>
        <?php if (empty($projects)): ?><p>Aucun projet enregistre.</p><?php endif; ?>
    </article>
    <form class="form panel" method="post" action="<?= url('/professionnel/projets') ?>">
        <?= CSRF::field() ?>
        <input type="hidden" name="id" value="<?= e($editProject['id'] ?? '') ?>">
        <h2><?= $editProject ? 'Modifier un projet' : 'Ajouter un projet' ?></h2>
        <label>Nom<input name="name" value="<?= e($editProject['name'] ?? '') ?>" required></label>
        <label>Role<input name="role" value="<?= e($editProject['role'] ?? '') ?>"></label>
        <label>Technologies<input name="technologies" value="<?= e($editProject['technologies'] ?? '') ?>"></label>
        <label>Lien public<input type="url" name="public_link" value="<?= e($editProject['public_link'] ?? '') ?>"></label>
        <label>Description<textarea name="description"><?= e($editProject['description'] ?? '') ?></textarea></label>
        <button class="btn btn-primary">Enregistrer</button>
    </form>
</section>

<?php elseif ($section === 'opportunities'): ?>
<section class="grid-3">
    <?php foreach ($opportunities as $item): ?>
        <?php $alreadyApplied = in_array((int) $item['id'], $appliedOpportunityIds ?? [], true); ?>
        <article class="card opportunity-card"><span class="badge"><?= e($item['type']) ?></span><h3><?= e($item['title']) ?></h3><p><?= e($item['location'] ?: 'Remote') ?> - <?= e($item['organism']) ?></p><small><?= e($item['skills'] ?: 'Competences ouvertes') ?></small><?php if ($alreadyApplied): ?><a class="btn btn-light" href="<?= url('/professionnel/candidatures') ?>">Candidature envoyee</a><?php else: ?><form method="post" action="<?= url('/professionnel/opportunites/' . (int) $item['id'] . '/candidater') ?>"><?= CSRF::field() ?><button class="btn btn-primary">Postuler</button></form><?php endif; ?></article>
    <?php endforeach; ?>
    <?php if (empty($opportunities)): ?><article class="panel"><p>Aucune opportunite ouverte actuellement.</p></article><?php endif; ?>
</section>

<?php elseif ($section === 'applications'): ?>
<section class="panel table-wrap"><table><thead><tr><th>Opportunite</th><th>Entreprise</th><th>Statut</th><th>Date</th></tr></thead><tbody><?php foreach ($applications as $application): ?><tr><td><?= e($application['opportunity_title']) ?><br><small><?= e($application['opportunity_type']) ?></small></td><td><?= e($application['organism'] ?: $application['company_name'] ?: '-') ?></td><td><span class="badge"><?= e($application['status']) ?></span></td><td><?= e(date('d/m/Y', strtotime($application['created_at']))) ?></td></tr><?php endforeach; ?></tbody></table></section>

<?php elseif ($section === 'resources'): ?>
<section class="grid-3"><?php foreach ($resources as $item): ?><article class="card"><span class="badge green"><?= e($item['category']) ?></span><h3><?= e($item['title']) ?></h3><p><?= e($item['description']) ?></p><a href="<?= url('/ressource/' . (int) $item['id']) ?>">Consulter</a></article><?php endforeach; ?></section>

<?php elseif ($section === 'events'): ?>
<section class="grid-3"><?php foreach ($events as $item): ?><article class="card"><span class="badge"><?= e(date('d/m/Y H:i', strtotime($item['event_date']))) ?></span><h3><?= e($item['title']) ?></h3><p><?= e($item['description']) ?></p><small><?= e($item['location'] ?? '') ?></small></article><?php endforeach; ?></section>

<?php elseif ($section === 'notifications'): ?>
<section class="panel">
    <h2>Notifications</h2>
    <?php foreach ($notifications as $notification): ?><div class="list-row"><span><strong><?= e($notification['title']) ?></strong><br><small><?= e($notification['body'] ?? '') ?></small></span><small><?= e(date('d/m/Y', strtotime($notification['created_at']))) ?></small></div><?php endforeach; ?>
    <?php if (empty($notifications)): ?><p>Aucune notification pour le moment.</p><?php endif; ?>
</section>

<?php else: ?>
<section class="stats-grid">
    <article class="stat"><span><?= (int) $completion ?>%</span><p>Profil complete</p></article>
    <article class="stat"><span><?= count($projects) ?></span><p>Projets</p></article>
    <article class="stat"><span><?= count($experiences) ?></span><p>Experiences</p></article>
    <article class="stat"><span><?= (int) ($applicationStats['total'] ?? 0) ?></span><p>Candidatures</p></article>
</section>
<section class="dashboard-grid">
    <article class="panel"><h2>Opportunites recommandees</h2><?php foreach ($opportunities as $item): ?><a class="list-row" href="<?= url('/opportunite/' . (int) $item['id']) ?>"><span><?= e($item['title']) ?></span><small><?= e($item['type']) ?></small></a><?php endforeach; ?></article>
    <article class="panel"><h2>Priorites profil</h2><div class="list-row"><span>Competences</span><strong><?= count($skills) ?></strong></div><div class="list-row"><span>Ressources utiles</span><strong><?= count($resources) ?></strong></div><div class="list-row"><span>Evenements</span><strong><?= count($events) ?></strong></div><a class="btn btn-primary" href="<?= url('/professionnel/profil') ?>">Completer mon profil</a></article>
</section>
<section class="dashboard-columns">
    <article class="panel"><h2>Dernieres candidatures</h2><?php foreach (array_slice($applications, 0, 4) as $a): ?><div class="list-row"><span><?= e($a['opportunity_title']) ?></span><small><?= e($a['status']) ?></small></div><?php endforeach; ?></article>
    <article class="panel"><h2>Ressources</h2><?php foreach ($resources as $r): ?><a class="list-row" href="<?= url('/ressource/' . (int) $r['id']) ?>"><span><?= e($r['title']) ?></span><small><?= e($r['category']) ?></small></a><?php endforeach; ?></article>
    <article class="panel"><h2>Agenda</h2><?php foreach ($events as $event): ?><a class="list-row" href="<?= url('/evenement/' . (int) $event['id']) ?>"><span><?= e($event['title']) ?></span><small><?= e(date('d/m', strtotime($event['event_date']))) ?></small></a><?php endforeach; ?></article>
</section>
<?php endif; ?>
