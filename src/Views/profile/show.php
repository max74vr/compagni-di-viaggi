<?php
$pageTitle = htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']);
include BASE_PATH . '/src/Views/layouts/header.php';
?>

<div class="container" style="margin: 2rem auto;">
    <div class="profile-container">
        <!-- Profile Header -->
        <div class="profile-header" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; padding: 3rem 2rem; border-radius: var(--border-radius); margin-bottom: 2rem; text-align: center;">
            <div style="margin-bottom: 1.5rem;">
                <?php if ($user['profile_photo']): ?>
                    <img src="<?= SITE_URL ?>/uploads/profiles/<?= htmlspecialchars($user['profile_photo']) ?>"
                         alt="<?= htmlspecialchars($user['first_name']) ?>"
                         style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid white; box-shadow: var(--shadow-lg);">
                <?php else: ?>
                    <div style="width: 120px; height: 120px; border-radius: 50%; background: white; color: var(--primary-color); display: inline-flex; align-items: center; justify-content: center; font-size: 3rem; font-weight: 700; border: 4px solid white; box-shadow: var(--shadow-lg);">
                        <?= strtoupper(substr($user['first_name'], 0, 1)) ?>
                    </div>
                <?php endif; ?>

                <?php if ($user['is_verified']): ?>
                    <span style="background: var(--success-color); color: white; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.875rem; margin-left: 0.5rem;">✓ Verificato</span>
                <?php endif; ?>
            </div>

            <h1 style="margin-bottom: 0.5rem; font-size: 2rem;">
                <?= htmlspecialchars($user['first_name']) ?> <?= htmlspecialchars($user['last_name']) ?>
            </h1>

            <p style="opacity: 0.9; font-size: 1.125rem;">@<?= htmlspecialchars($user['username']) ?></p>

            <?php if ($user['city'] || $user['country']): ?>
                <p style="margin-top: 1rem; opacity: 0.9;">
                    📍 <?= htmlspecialchars($user['city'] ?? '') ?><?= $user['city'] && $user['country'] ? ', ' : '' ?><?= htmlspecialchars($user['country'] ?? '') ?>
                </p>
            <?php endif; ?>

            <?php if ($user['reputation_score'] > 0): ?>
                <div style="margin-top: 1rem; font-size: 1.25rem;">
                    ⭐ <?= number_format($user['reputation_score'], 1) ?> / 5.0
                    <span style="opacity: 0.8; font-size: 0.9rem;">(<?= $user['total_trips'] ?? 0 ?> viaggi)</span>
                </div>
            <?php endif; ?>

            <?php if ($isOwnProfile): ?>
                <div style="margin-top: 1.5rem;">
                    <a href="<?= SITE_URL ?>/edit-profile.php" class="btn-secondary">Modifica Profilo</a>
                </div>
            <?php endif; ?>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
            <!-- Sidebar -->
            <div>
                <!-- Bio -->
                <?php if ($user['bio']): ?>
                <div style="background: white; padding: 1.5rem; border-radius: var(--border-radius); box-shadow: var(--shadow-sm); margin-bottom: 1.5rem;">
                    <h3 style="margin-bottom: 1rem;">Chi sono</h3>
                    <p style="color: var(--text-secondary); line-height: 1.6;"><?= nl2br(htmlspecialchars($user['bio'])) ?></p>
                </div>
                <?php endif; ?>

                <!-- Languages -->
                <?php if (!empty($languages)): ?>
                <div style="background: white; padding: 1.5rem; border-radius: var(--border-radius); box-shadow: var(--shadow-sm); margin-bottom: 1.5rem;">
                    <h3 style="margin-bottom: 1rem;">🌍 Lingue</h3>
                    <ul style="list-style: none;">
                        <?php foreach ($languages as $lang): ?>
                            <li style="margin-bottom: 0.5rem; color: var(--text-secondary);">
                                <?= htmlspecialchars($lang['language_name']) ?>
                                <span style="font-size: 0.875rem; color: var(--text-secondary);">(<?= htmlspecialchars($lang['proficiency_level']) ?>)</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Badges -->
                <?php if (!empty($badges)): ?>
                <div style="background: white; padding: 1.5rem; border-radius: var(--border-radius); box-shadow: var(--shadow-sm); margin-bottom: 1.5rem;">
                    <h3 style="margin-bottom: 1rem;">🏆 Badge</h3>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                        <?php foreach ($badges as $badge): ?>
                            <span style="background: var(--bg-light); padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.875rem;" title="<?= htmlspecialchars($badge['description']) ?>">
                                <?= $badge['icon'] ?> <?= htmlspecialchars($badge['name']) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Travel Preferences -->
                <?php if (!empty($preferences)): ?>
                <div style="background: white; padding: 1.5rem; border-radius: var(--border-radius); box-shadow: var(--shadow-sm); margin-bottom: 1.5rem;">
                    <h3 style="margin-bottom: 1rem;">✈️ Stile di Viaggio</h3>
                    <?php foreach ($preferences as $pref): ?>
                        <p style="margin-bottom: 0.5rem; color: var(--text-secondary);">
                            <strong><?= htmlspecialchars($pref['travel_style']) ?></strong><br>
                            Budget: <?= htmlspecialchars($pref['budget_level']) ?>
                        </p>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Main Content -->
            <div>
                <!-- Reviews -->
                <?php if (!empty($reviews) && !empty($reviewStats)): ?>
                <div style="background: white; padding: 1.5rem; border-radius: var(--border-radius); box-shadow: var(--shadow-sm); margin-bottom: 1.5rem;">
                    <h3 style="margin-bottom: 1.5rem;">📝 Recensioni (<?= $reviewStats['total_reviews'] ?>)</h3>

                    <!-- Review Stats -->
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-color);">
                        <div>
                            <div style="font-size: 0.875rem; color: var(--text-secondary);">⏰ Puntualità</div>
                            <div style="font-weight: 600; color: var(--warning-color);">⭐ <?= number_format($reviewStats['avg_punctuality'], 1) ?></div>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; color: var(--text-secondary);">🤝 Spirito di gruppo</div>
                            <div style="font-weight: 600; color: var(--warning-color);">⭐ <?= number_format($reviewStats['avg_team_spirit'], 1) ?></div>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; color: var(--text-secondary);">💚 Rispetto</div>
                            <div style="font-weight: 600; color: var(--warning-color);">⭐ <?= number_format($reviewStats['avg_respect'], 1) ?></div>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; color: var(--text-secondary);">🔄 Adattabilità</div>
                            <div style="font-weight: 600; color: var(--warning-color);">⭐ <?= number_format($reviewStats['avg_adaptability'], 1) ?></div>
                        </div>
                    </div>

                    <!-- Recent Reviews -->
                    <?php foreach (array_slice($reviews, 0, 3) as $review): ?>
                        <div style="padding: 1rem 0; border-bottom: 1px solid var(--border-color);">
                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                <strong><?= htmlspecialchars($review['reviewer_first_name']) ?> <?= htmlspecialchars($review['reviewer_last_name']) ?></strong>
                                <span style="color: var(--warning-color);">⭐ <?= number_format($review['average_rating'], 1) ?></span>
                            </div>
                            <?php if ($review['comment']): ?>
                                <p style="color: var(--text-secondary); font-size: 0.9rem;"><?= htmlspecialchars($review['comment']) ?></p>
                            <?php endif; ?>
                            <small style="color: var(--text-secondary);"><?= formatDate($review['created_at']) ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- User Travels -->
                <?php if (!empty($userTravels)): ?>
                <div style="background: white; padding: 1.5rem; border-radius: var(--border-radius); box-shadow: var(--shadow-sm); margin-bottom: 1.5rem;">
                    <h3 style="margin-bottom: 1rem;">🗺️ Viaggi Organizzati</h3>
                    <div class="travel-grid" style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
                        <?php foreach ($userTravels as $travel): ?>
                            <div style="border: 1px solid var(--border-color); border-radius: var(--border-radius); padding: 1rem;">
                                <h4><?= htmlspecialchars($travel['title']) ?></h4>
                                <p style="color: var(--text-secondary); font-size: 0.9rem;">
                                    📍 <?= htmlspecialchars($travel['destination']) ?>, <?= htmlspecialchars($travel['country']) ?><br>
                                    📅 <?= formatDate($travel['start_date']) ?> - <?= formatDate($travel['end_date']) ?><br>
                                    Stato: <span style="color: var(--success-color);"><?= htmlspecialchars($travel['status']) ?></span>
                                </p>
                                <a href="<?= SITE_URL ?>/travel.php?id=<?= $travel['id'] ?>" class="btn-secondary" style="margin-top: 0.5rem; display: inline-block; font-size: 0.9rem;">Vedi dettagli</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Joined Travels -->
                <?php if (!empty($joinedTravels)): ?>
                <div style="background: white; padding: 1.5rem; border-radius: var(--border-radius); box-shadow: var(--shadow-sm);">
                    <h3 style="margin-bottom: 1rem;">🎒 Viaggi Partecipati</h3>
                    <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
                        <?php foreach ($joinedTravels as $travel): ?>
                            <div style="border: 1px solid var(--border-color); border-radius: var(--border-radius); padding: 1rem;">
                                <h4><?= htmlspecialchars($travel['title']) ?></h4>
                                <p style="color: var(--text-secondary); font-size: 0.9rem;">
                                    📍 <?= htmlspecialchars($travel['destination']) ?>, <?= htmlspecialchars($travel['country']) ?><br>
                                    📅 <?= formatDate($travel['start_date']) ?>
                                </p>
                                <a href="<?= SITE_URL ?>/travel.php?id=<?= $travel['id'] ?>" class="btn-secondary" style="margin-top: 0.5rem; display: inline-block; font-size: 0.9rem;">Vedi dettagli</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (empty($userTravels) && empty($joinedTravels)): ?>
                <div style="background: white; padding: 3rem; border-radius: var(--border-radius); box-shadow: var(--shadow-sm); text-align: center;">
                    <p style="color: var(--text-secondary);">Nessun viaggio ancora. <?= $isOwnProfile ? '<a href="' . SITE_URL . '/create-travel.php">Crea il tuo primo viaggio!</a>' : '' ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
@media (max-width: 768px) {
    .profile-container > div {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php include BASE_PATH . '/src/Views/layouts/footer.php'; ?>
