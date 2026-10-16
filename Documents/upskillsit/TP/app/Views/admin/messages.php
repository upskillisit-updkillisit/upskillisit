<?php use App\Core\CSRF; ?>
<section class="admin-command">
    <div>
        <span class="badge green">Messages contact</span>
        <h1>Demandes recues</h1>
        <p>Messages envoyes depuis le formulaire public et stockes en base de donnees.</p>
    </div>
    <div class="admin-health"><strong><?= (int) ($messageStats['new_count'] ?? 0) ?></strong><span>Nouveaux</span></div>
</section>

<section class="stats-grid admin-stats">
    <article class="stat"><span><?= (int) ($messageStats['total'] ?? 0) ?></span><p>Total messages</p></article>
    <article class="stat"><span><?= (int) ($messageStats['new_count'] ?? 0) ?></span><p>Nouveaux</p></article>
    <article class="stat"><span><?= (int) ($messageStats['read_count'] ?? 0) ?></span><p>Lus</p></article>
    <article class="stat"><span><?= (int) ($messageStats['archived_count'] ?? 0) ?></span><p>Archives</p></article>
</section>

<form class="filters" method="get">
    <input name="q" value="<?= e($_GET['q'] ?? '') ?>" placeholder="Nom, email, sujet ou message">
    <select name="status">
        <option value="">Tous les statuts</option>
        <option value="new"<?= selected($_GET['status'] ?? '', 'new') ?>>Nouveau</option>
        <option value="read"<?= selected($_GET['status'] ?? '', 'read') ?>>Lu</option>
        <option value="archived"<?= selected($_GET['status'] ?? '', 'archived') ?>>Archive</option>
    </select>
    <button class="btn btn-primary">Filtrer</button>
</form>

<section class="panel table-wrap">
    <table>
        <thead><tr><th>Contact</th><th>Sujet</th><th>Message</th><th>Statut</th><th>Date</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($messages as $message): ?>
            <tr>
                <td><strong><?= e($message['name']) ?></strong><br><small><a href="mailto:<?= e($message['email']) ?>"><?= e($message['email']) ?></a></small></td>
                <td><?= e($message['subject']) ?></td>
                <td><?= e(mb_strimwidth($message['message'], 0, 140, '...')) ?></td>
                <td><span class="badge <?= $message['status'] === 'new' ? 'green' : '' ?>"><?= e($message['status']) ?></span></td>
                <td><?= e(date('d/m/Y H:i', strtotime($message['created_at']))) ?></td>
                <td>
                    <form class="inline-actions" method="post" action="<?= url('/admin/messages/' . (int) $message['id'] . '/status') ?>">
                        <?= CSRF::field() ?>
                        <select name="status">
                            <option value="read"<?= selected($message['status'], 'read') ?>>Lu</option>
                            <option value="new"<?= selected($message['status'], 'new') ?>>Nouveau</option>
                            <option value="archived"<?= selected($message['status'], 'archived') ?>>Archive</option>
                        </select>
                        <button class="btn btn-light">Mettre a jour</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($messages)): ?><tr><td colspan="6">Aucun message trouve.</td></tr><?php endif; ?>
        </tbody>
    </table>
</section>
