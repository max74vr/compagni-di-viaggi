<?php
$pageTitle = htmlspecialchars($story['title']);
include BASE_PATH . '/src/Views/layouts/header.php';
?>

<div class="container" style="max-width: 900px; margin: 2rem auto;">
    <!-- Cover Image -->
    <?php if ($story['cover_image']): ?>
    <div style="height: 400px; background: url('<?= SITE_URL ?>/uploads/stories/<?= htmlspecialchars($story['cover_image']) ?>'); background-size: cover; background-position: center; border-radius: var(--border-radius); margin-bottom: 2rem;"></div>
    <?php endif; ?>

    <!-- Story Header -->
    <div style="background: white; padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--shadow-md); margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.5rem;">
            <div style="flex: 1;">
                <h1 style="margin-bottom: 1rem;"><?= htmlspecialchars($story['title']) ?></h1>

                <p style="color: var(--text-secondary); font-size: 1.125rem; margin-bottom: 1rem;">
                    📍 <?= htmlspecialchars($story['destination']) ?>, <?= htmlspecialchars($story['country']) ?>
                </p>

                <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem;">
                    <?php if ($story['travel_type']): ?>
                    <span style="background: var(--bg-light); padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem;">
                        <?= getTravelTypeIcon($story['travel_type']) ?> <?= htmlspecialchars($story['travel_type']) ?>
                    </span>
                    <?php endif; ?>

                    <?php if ($story['travel_duration']): ?>
                    <span style="background: var(--bg-light); padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem;">
                        ⏱️ <?= htmlspecialchars($story['travel_duration']) ?>
                    </span>
                    <?php endif; ?>

                    <?php if ($story['overall_rating']): ?>
                    <span style="background: var(--bg-light); padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem; color: var(--warning-color);">
                        <?php for ($i = 0; $i < $story['overall_rating']; $i++): ?>⭐<?php endfor; ?>
                    </span>
                    <?php endif; ?>

                    <?php if ($story['would_return']): ?>
                    <span style="background: var(--success-color); color: white; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem;">
                        🔄 Ci tornerei!
                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Author Info -->
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: var(--bg-light); border-radius: var(--border-radius);">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <a href="<?= SITE_URL ?>/profile.php?id=<?= $story['user_id'] ?>">
                    <?php if ($story['profile_photo']): ?>
                    <img src="<?= SITE_URL ?>/uploads/profiles/<?= htmlspecialchars($story['profile_photo']) ?>" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                    <?php else: ?>
                    <div style="width: 50px; height: 50px; border-radius: 50%; background: var(--primary-color); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 600;">
                        <?= strtoupper(substr($story['first_name'], 0, 1)) ?>
                    </div>
                    <?php endif; ?>
                </a>
                <div>
                    <strong><?= htmlspecialchars($story['first_name']) ?> <?= htmlspecialchars($story['last_name']) ?></strong>
                    <p style="font-size: 0.875rem; color: var(--text-secondary); margin: 0;">
                        Pubblicato il <?= formatDate($story['created_at']) ?>
                    </p>
                </div>
            </div>

            <div style="font-size: 0.9rem; color: var(--text-secondary);">
                👁️ <?= number_format($story['views_count']) ?> visualizzazioni &nbsp;
                ❤️ <?= number_format($story['likes_count']) ?> mi piace
            </div>
        </div>
    </div>

    <!-- Story Content -->
    <div style="background: white; padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--shadow-md); margin-bottom: 2rem;">
        <h2 style="margin-bottom: 1.5rem;">📖 Il Racconto</h2>
        <div style="line-height: 1.8; white-space: pre-wrap;"><?= nl2br(htmlspecialchars($story['story_content'])) ?></div>
    </div>

    <!-- Highlights -->
    <?php if ($story['highlights']): ?>
    <div style="background: white; padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--shadow-md); margin-bottom: 2rem;">
        <h2 style="margin-bottom: 1.5rem;">🌟 Punti Salienti</h2>
        <div style="line-height: 1.8; white-space: pre-wrap;"><?= nl2br(htmlspecialchars($story['highlights'])) ?></div>
    </div>
    <?php endif; ?>

    <!-- Tips -->
    <?php if ($story['tips']): ?>
    <div style="background: white; padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--shadow-md); margin-bottom: 2rem;">
        <h2 style="margin-bottom: 1.5rem;">💡 Consigli Utili</h2>
        <div style="line-height: 1.8; white-space: pre-wrap;"><?= nl2br(htmlspecialchars($story['tips'])) ?></div>
    </div>
    <?php endif; ?>

    <!-- Budget Info -->
    <?php if ($story['budget_info']): ?>
    <div style="background: white; padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--shadow-md); margin-bottom: 2rem;">
        <h2 style="margin-bottom: 1.5rem;">💰 Budget e Costi</h2>
        <div style="line-height: 1.8; white-space: pre-wrap;"><?= nl2br(htmlspecialchars($story['budget_info'])) ?></div>
    </div>
    <?php endif; ?>

    <!-- Actions -->
    <div style="text-align: center; margin: 2rem 0;">
        <a href="<?= SITE_URL ?>/stories.php" class="btn-secondary">← Torna ai Racconti</a>
        <?php if (isLoggedIn() && $story['user_id'] == getCurrentUserId()): ?>
        <a href="<?= SITE_URL ?>/edit-story.php?id=<?= $story['id'] ?>" class="btn-primary">Modifica</a>
        <?php endif; ?>
    </div>
</div>

<?php include BASE_PATH . '/src/Views/layouts/footer.php'; ?>
