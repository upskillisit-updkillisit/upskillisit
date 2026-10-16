<?php
use App\Core\CSRF;

$isOpp = $kind === 'opportunities';
$isRes = $kind === 'resources';
$isEvt = $kind === 'events';
$isArticle = $kind === 'articles';
$adminPaths = ['opportunities' => 'opportunites', 'resources' => 'ressources', 'events' => 'evenements', 'articles' => 'articles'];
$routeKind = $adminPaths[$kind] ?? $kind;
$action = url('/admin/' . $routeKind);
$edit = $edit ?? [];
$status = $edit['status'] ?? 'draft';
?>
<section class="admin-grid">
    <form class="form panel" method="post" action="<?= $action ?>">
        <?= CSRF::field() ?>
        <?php if (!empty($edit['id'])): ?><input type="hidden" name="id" value="<?= (int) $edit['id'] ?>"><?php endif; ?>
        <h2><?= $edit ? 'Modifier' : 'Créer' ?> <?= $isArticle ? 'un article' : 'un contenu' ?></h2>
        <label>Titre<input name="title" value="<?= e($edit['title'] ?? '') ?>" required></label>

        <?php if ($isArticle): ?>
            <label>Slug<input name="slug" value="<?= e($edit['slug'] ?? '') ?>" placeholder="généré automatiquement si vide"></label>
            <label>Résumé<textarea name="excerpt"><?= e($edit['excerpt'] ?? '') ?></textarea></label>
        <?php endif; ?>

        <?php if ($isOpp): ?>
            <label>Type<select name="type"><?php foreach (['Stage','Projet académique','Événement','Concours','Hackathon','Formation','Bénévolat IT'] as $t): ?><option<?= selected($edit['type'] ?? '', $t) ?>><?= e($t) ?></option><?php endforeach; ?></select></label>
            <label>Organisme<input name="organism" value="<?= e($edit['organism'] ?? '') ?>" required></label>
            <label>Profil recherché<textarea name="target_profile"><?= e($edit['target_profile'] ?? '') ?></textarea></label>
            <label>Compétences recherchées<textarea name="skills"><?= e($edit['skills'] ?? '') ?></textarea></label>
            <label>Localisation<input name="location" value="<?= e($edit['location'] ?? '') ?>"></label>
            <label>Date limite<input type="date" name="deadline" value="<?= e($edit['deadline'] ?? '') ?>"></label>
            <label>Modalités de candidature<textarea name="application_method"><?= e($edit['application_method'] ?? '') ?></textarea></label>
            <label>Lien de candidature<input type="url" name="application_link" value="<?= e($edit['application_link'] ?? '') ?>"></label>
        <?php endif; ?>

        <?php if ($isRes): ?>
            <label>Catégorie<input name="category" value="<?= e($edit['category'] ?? '') ?>" placeholder="CV, GitHub, certifications..." required></label>
            <label>Auteur<input name="author" value="<?= e($edit['author'] ?? '') ?>"></label>
            <label>Source<input name="source" value="<?= e($edit['source'] ?? '') ?>"></label>
            <label>Lien<input type="url" name="link" value="<?= e($edit['link'] ?? '') ?>"></label>
            <label>Document<input name="document_path" value="<?= e($edit['document_path'] ?? '') ?>"></label>
        <?php endif; ?>

        <?php if ($isEvt): ?>
            <label>Date<input type="datetime-local" name="event_date" value="<?= !empty($edit['event_date']) ? e(date('Y-m-d\TH:i', strtotime($edit['event_date']))) : '' ?>" required></label>
            <label>Lieu<input name="location" value="<?= e($edit['location'] ?? '') ?>"></label>
            <label>Capacité<input type="number" min="1" name="capacity" value="<?= e($edit['capacity'] ?? 30) ?>"></label>
            <label>Intervenant<input name="speaker" value="<?= e($edit['speaker'] ?? '') ?>"></label>
        <?php endif; ?>

        <?php if (!$isArticle): ?>
            <label>Description<textarea name="description" required><?= e($edit['description'] ?? '') ?></textarea></label>
        <?php endif; ?>
        <?php if ($isRes || $isArticle): ?><label>Contenu<textarea name="content"><?= e($edit['content'] ?? '') ?></textarea></label><?php endif; ?>
        <label>Statut<select name="status"><option value="draft"<?= selected($status, 'draft') ?>>Brouillon</option><option value="published"<?= selected($status, 'published') ?>>Publié</option><?php if (!$isRes && !$isArticle): ?><option value="closed"<?= selected($status, 'closed') ?>>Fermé</option><?php endif; ?></select></label>
        <div class="inline-actions">
            <button class="btn btn-primary">Enregistrer</button>
            <?php if ($edit): ?><a class="btn btn-light" href="<?= $action ?>">Nouveau</a><?php endif; ?>
        </div>
    </form>

    <div class="panel table-wrap">
        <table>
            <thead><tr><th>Titre</th><th>Statut</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= e($item['title']) ?></td>
                    <td><span class="badge"><?= e($item['status']) ?></span></td>
                    <td><?= e($item['published_at'] ?? $item['event_date'] ?? $item['created_at']) ?></td>
                    <td class="inline-actions">
                        <a class="btn btn-light" href="<?= url('/admin/' . $routeKind . '?edit=' . $item['id']) ?>">Modifier</a>
                        <form method="post" action="<?= url('/admin/' . $routeKind . '/' . $item['id'] . '/status') ?>"><?= CSRF::field() ?><input type="hidden" name="status" value="<?= $item['status'] === 'published' ? 'draft' : 'published' ?>"><button><?= $item['status'] === 'published' ? 'Dépublier' : 'Publier' ?></button></form>
                        <form method="post" action="<?= url('/admin/' . $routeKind . '/' . $item['id'] . '/delete') ?>"><?= CSRF::field() ?><button onclick="return confirm('Supprimer ce contenu ?')">Supprimer</button></form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
