<?php use App\Core\CSRF; ?>
<section class="panel admin-page-head">
    <div>
        <span class="badge">Gestion utilisateurs</span>
        <h1>Comptes et roles</h1>
        <p>Recherche, filtrage, suspension et suppression des comptes depuis la base MySQL.</p>
    </div>
</section>

<form class="filters" method="get">
    <input name="q" value="<?= e($_GET['q'] ?? '') ?>" placeholder="Nom, prenom ou email">
    <select name="role">
        <option value="">Tous les roles</option>
        <option value="student"<?= selected($_GET['role'] ?? '', 'student') ?>>Etudiant</option>
        <option value="professional"<?= selected($_GET['role'] ?? '', 'professional') ?>>Professionnel</option>
        <option value="company"<?= selected($_GET['role'] ?? '', 'company') ?>>Entreprise</option>
        <option value="admin"<?= selected($_GET['role'] ?? '', 'admin') ?>>Admin</option>
    </select>
    <button class="btn btn-primary">Filtrer</button>
</form>

<div class="panel table-wrap">
    <table>
        <thead><tr><th>Utilisateur</th><th>Email</th><th>Role</th><th>Statut</th><th>Inscription</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><strong><?= e($u['first_name'] . ' ' . $u['last_name']) ?></strong></td>
                <td><?= e($u['email']) ?></td>
                <td><span class="badge"><?= e($u['role']) ?></span></td>
                <td><span class="badge <?= $u['status'] === 'active' ? 'green' : '' ?>"><?= e($u['status']) ?></span></td>
                <td><?= e(date('d/m/Y', strtotime($u['created_at']))) ?></td>
                <td class="inline-actions">
                    <form method="post" action="<?= url('/admin/users/' . $u['id'] . '/status') ?>">
                        <?= CSRF::field() ?>
                        <input type="hidden" name="status" value="<?= $u['status'] === 'active' ? 'suspended' : 'active' ?>">
                        <button class="btn btn-light"><?= $u['status'] === 'active' ? 'Suspendre' : 'Reactiver' ?></button>
                    </form>
                    <form method="post" action="<?= url('/admin/users/' . $u['id'] . '/delete') ?>">
                        <?= CSRF::field() ?>
                        <button class="btn btn-danger" onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
