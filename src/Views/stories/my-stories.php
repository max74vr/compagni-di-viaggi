<?php
$pageTitle = 'I Miei Racconti';
include BASE_PATH . '/src/Views/layouts/header.php';
?>

<div class="container" style="margin: 4rem auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>📖 I Miei Racconti</h1>
        <a href="<?= SITE_URL ?>/create-story.php" class="btn-primary">✍️ Nuovo Racconto</a>
    </div>

    <?php if (!empty($stories)): ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 2rem;">
        <?php foreach ($stories as $story): ?>
        <div style="background: white; border-radius: var(--border-radius); overflow: hidden; box-shadow: var(--shadow-md);">
            <?php if ($story['cover_image']): ?>
            <div style="height: 150px; background: url('<?= SITE_URL ?>/uploads/stories/<?= htmlspecialchars($story['cover_image']) ?>'); background-size: cover; background-position: center;"></div>
            <?php endif; ?>

            <div style="padding: 1.5rem;">
                <h3 style="margin-bottom: 0.5rem;"><?= htmlspecialchars($story['title']) ?></h3>
                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1rem;">
                    📍 <?= htmlspecialchars($story['destination']) ?>, <?= htmlspecialchars($story['country']) ?>
                </p>

                <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem; font-size: 0.875rem; color: var(--text-secondary);">
                    <span>👁️ <?= number_format($story['views_count']) ?></span>
                    <span>❤️ <?= number_format($story['likes_count']) ?></span>
                    <span><?= $story['is_published'] ? '✅ Pubblicato' : '📝 Bozza' ?></span>
                </div>

                <div style="display: flex; gap: 0.5rem;">
                    <a href="<?= SITE_URL ?>/story.php?id=<?= $story['id'] ?>" class="btn-secondary" style="flex: 1; text-align: center;">Visualizza</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div style="text-align: center; padding: 3rem; background: var(--bg-light); border-radius: var(--border-radius);">
        <h3>Ancora nessun racconto</h3>
        <p style="color: var(--text-secondary); margin-top: 1rem;">
            Inizia a condividere le tue esperienze di viaggio!
        </p>
        <a href="<?= SITE_URL ?>/create-story.php" class="btn-primary" style="margin-top: 1rem;">✍️ Scrivi il Tuo Primo Racconto</a>
    </div>
    <?php endif; ?>
</div>

<?php include BASE_PATH . '/src/Views/layouts/footer.php'; ?>
