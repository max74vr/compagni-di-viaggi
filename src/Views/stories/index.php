<?php
$pageTitle = 'Racconti di Viaggio';
include BASE_PATH . '/src/Views/layouts/header.php';
?>

<section class="hero" style="padding: 3rem 0;">
    <div class="container">
        <h1 style="font-size: 2.5rem;">📖 Racconti di Viaggio</h1>
        <p class="hero-subtitle">Scopri le avventure di altri viaggiatori e lasciati ispirare!</p>

        <!-- Search & Filters -->
        <div style="background: white; padding: 1.5rem; border-radius: var(--border-radius); box-shadow: var(--shadow-lg); margin-top: 2rem;">
            <form action="<?= SITE_URL ?>/stories.php" method="GET">
                <div style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 1rem;">
                    <input type="text" name="search" placeholder="Cerca per destinazione o parole chiave..." class="form-control" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">

                    <select name="travel_type" class="form-control">
                        <option value="">Tutti i tipi</option>
                        <option value="avventura" <?= ($_GET['travel_type'] ?? '') === 'avventura' ? 'selected' : '' ?>>🏔️ Avventura</option>
                        <option value="mare" <?= ($_GET['travel_type'] ?? '') === 'mare' ? 'selected' : '' ?>>🏖️ Mare</option>
                        <option value="città" <?= ($_GET['travel_type'] ?? '') === 'città' ? 'selected' : '' ?>>🏙️ Città</option>
                        <option value="natura" <?= ($_GET['travel_type'] ?? '') === 'natura' ? 'selected' : '' ?>>🌲 Natura</option>
                        <option value="cultura" <?= ($_GET['travel_type'] ?? '') === 'cultura' ? 'selected' : '' ?>>🎭 Cultura</option>
                    </select>

                    <button type="submit" class="btn-primary">Cerca</button>
                </div>
            </form>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (isLoggedIn()): ?>
        <div style="text-align: center; margin-bottom: 2rem;">
            <a href="<?= SITE_URL ?>/create-story.php" class="btn-primary btn-lg">
                ✍️ Racconta il Tuo Viaggio
            </a>
        </div>
        <?php endif; ?>

        <?php if (!empty($stories)): ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 2rem;">
            <?php foreach ($stories as $story): ?>
            <div style="background: white; border-radius: var(--border-radius); overflow: hidden; box-shadow: var(--shadow-md); transition: var(--transition);" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='var(--shadow-lg)'" onmouseout="this.style.transform=''; this.style.boxShadow='var(--shadow-md)'">
                <!-- Cover Image -->
                <div style="height: 200px; background: <?= $story['cover_image'] ? "url('" . SITE_URL . "/uploads/stories/" . htmlspecialchars($story['cover_image']) . "')" : 'linear-gradient(135deg, #667eea, #764ba2)' ?>; background-size: cover; background-position: center; position: relative;">
                    <?php if ($story['travel_type']): ?>
                    <span style="position: absolute; top: 1rem; right: 1rem; background: rgba(255,255,255,0.95); padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.875rem;">
                        <?= getTravelTypeIcon($story['travel_type']) ?> <?= htmlspecialchars($story['travel_type']) ?>
                    </span>
                    <?php endif; ?>

                    <?php if ($story['overall_rating']): ?>
                    <span style="position: absolute; bottom: 1rem; left: 1rem; background: rgba(255,255,255,0.95); padding: 0.5rem 1rem; border-radius: 20px; color: var(--warning-color); font-weight: 600;">
                        <?php for ($i = 0; $i < $story['overall_rating']; $i++): ?>⭐<?php endfor; ?>
                    </span>
                    <?php endif; ?>
                </div>

                <!-- Content -->
                <div style="padding: 1.5rem;">
                    <h3 style="margin-bottom: 0.5rem; font-size: 1.25rem;">
                        <a href="<?= SITE_URL ?>/story.php?id=<?= $story['id'] ?>" style="color: var(--text-primary); text-decoration: none;">
                            <?= htmlspecialchars($story['title']) ?>
                        </a>
                    </h3>

                    <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1rem;">
                        📍 <?= htmlspecialchars($story['destination']) ?>, <?= htmlspecialchars($story['country']) ?>
                    </p>

                    <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 1rem;">
                        <?= htmlspecialchars(substr($story['story_content'], 0, 150)) ?>...
                    </p>

                    <!-- Footer -->
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <?php if ($story['profile_photo']): ?>
                            <img src="<?= SITE_URL ?>/uploads/profiles/<?= htmlspecialchars($story['profile_photo']) ?>" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;">
                            <?php else: ?>
                            <div style="width: 30px; height: 30px; border-radius: 50%; background: var(--primary-color); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                <?= strtoupper(substr($story['first_name'], 0, 1)) ?>
                            </div>
                            <?php endif; ?>
                            <span style="font-size: 0.875rem;"><?= htmlspecialchars($story['first_name']) ?></span>
                        </div>

                        <div style="font-size: 0.875rem; color: var(--text-secondary);">
                            👁️ <?= number_format($story['views_count']) ?> &nbsp;
                            ❤️ <?= number_format($story['likes_count']) ?>
                        </div>
                    </div>

                    <a href="<?= SITE_URL ?>/story.php?id=<?= $story['id'] ?>" class="btn-secondary btn-block" style="margin-top: 1rem;">Leggi di più</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div style="margin-top: 3rem; text-align: center;">
            <?= generatePagination($page ?? 1, $totalPages, SITE_URL . '/stories.php') ?>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <div style="text-align: center; padding: 3rem; background: var(--bg-light); border-radius: var(--border-radius);">
            <h3>Nessun racconto trovato</h3>
            <p style="color: var(--text-secondary); margin-top: 1rem;">
                <?php if (isLoggedIn()): ?>
                    Sii il primo a condividere la tua esperienza!
                    <br><br>
                    <a href="<?= SITE_URL ?>/create-story.php" class="btn-primary">Racconta il Tuo Viaggio</a>
                <?php else: ?>
                    <a href="<?= SITE_URL ?>/register.php">Registrati</a> per condividere i tuoi racconti di viaggio!
                <?php endif; ?>
            </p>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include BASE_PATH . '/src/Views/layouts/footer.php'; ?>
