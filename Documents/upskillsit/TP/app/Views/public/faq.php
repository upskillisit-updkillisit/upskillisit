<section class="page-hero">
    <h1>FAQ</h1>
    <p><?= e(t('static.faq_intro')) ?></p>
</section>
<section class="section narrow">
    <?php foreach (t_list('static.faq_items') as $index => $item): ?>
        <details<?= $index === 0 ? ' open' : '' ?>>
            <summary><?= e($item['question'] ?? '') ?></summary>
            <p><?= e($item['answer'] ?? '') ?></p>
        </details>
    <?php endforeach; ?>
</section>
