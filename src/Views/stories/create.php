<?php
$pageTitle = 'Racconta il Tuo Viaggio';
include BASE_PATH . '/src/Views/layouts/header.php';

$errors = $_SESSION['errors'] ?? [];
$oldInput = $_SESSION['old_input'] ?? [];
unset($_SESSION['errors'], $_SESSION['old_input']);
?>

<div class="container" style="max-width: 900px; margin: 4rem auto;">
    <div style="background: white; padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--shadow-lg);">
        <h1 class="text-center mb-4">✈️ Racconta il Tuo Viaggio</h1>
        <p class="text-center mb-4" style="color: var(--text-secondary);">Condividi la tua esperienza e ispira altri viaggiatori!</p>

        <?php if (!empty($errors)): ?>
        <div class="alert alert-error mb-3" style="background: #fed7d7; color: #742a2a; padding: 1rem; border-radius: var(--border-radius);">
            <ul style="margin: 0; padding-left: 1.5rem;">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= SITE_URL ?>/create-story.php" enctype="multipart/form-data">
            <!-- Cover Image -->
            <div class="form-group">
                <label for="cover_image">📷 Foto di copertina</label>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div id="cover-preview" style="width: 200px; height: 150px; border-radius: var(--border-radius); background: var(--bg-light); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <span style="color: var(--text-secondary);">Anteprima foto</span>
                    </div>
                    <div style="flex: 1;">
                        <label for="cover_image" class="btn-secondary" style="cursor: pointer; display: inline-block;">
                            📁 Scegli foto di copertina
                        </label>
                        <input type="file" id="cover_image" name="cover_image" accept="image/*" style="display: none;">
                        <p style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.5rem;">JPG, PNG - Max 5MB</p>
                    </div>
                </div>
            </div>

            <!-- Title -->
            <div class="form-group">
                <label for="title">Titolo del racconto *</label>
                <input type="text" id="title" name="title" class="form-control" value="<?= htmlspecialchars($oldInput['title'] ?? '') ?>" placeholder="Il mio incredibile viaggio in..." required>
            </div>

            <!-- Destination & Country -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="destination">Destinazione *</label>
                    <input type="text" id="destination" name="destination" class="form-control" value="<?= htmlspecialchars($oldInput['destination'] ?? '') ?>" placeholder="es: Roma, Parigi, Tokyo" required>
                </div>

                <div class="form-group">
                    <label for="country">Paese *</label>
                    <input type="text" id="country" name="country" class="form-control" value="<?= htmlspecialchars($oldInput['country'] ?? '') ?>" placeholder="es: Italia, Francia, Giappone" required>
                </div>
            </div>

            <!-- Travel Date & Duration -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="travel_date">Data del viaggio</label>
                    <input type="date" id="travel_date" name="travel_date" class="form-control" value="<?= htmlspecialchars($oldInput['travel_date'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="travel_duration">Durata</label>
                    <input type="text" id="travel_duration" name="travel_duration" class="form-control" value="<?= htmlspecialchars($oldInput['travel_duration'] ?? '') ?>" placeholder="es: 1 settimana, 10 giorni">
                </div>
            </div>

            <!-- Travel Type -->
            <div class="form-group">
                <label for="travel_type">Tipo di viaggio</label>
                <select id="travel_type" name="travel_type" class="form-control">
                    <option value="">Seleziona...</option>
                    <option value="avventura">🏔️ Avventura</option>
                    <option value="mare">🏖️ Mare</option>
                    <option value="città">🏙️ Città</option>
                    <option value="smart-working">💻 Smart Working</option>
                    <option value="relax">🧘 Relax</option>
                    <option value="party">🎉 Party</option>
                    <option value="cultura">🎭 Cultura</option>
                    <option value="natura">🌲 Natura</option>
                </select>
            </div>

            <!-- Story Content -->
            <div class="form-group">
                <label for="story_content">Il tuo racconto * (min. 100 caratteri)</label>
                <textarea id="story_content" name="story_content" class="form-control" rows="10" placeholder="Racconta la tua esperienza... Cosa hai visto? Cosa hai fatto? Cosa ti ha colpito di più?" required><?= htmlspecialchars($oldInput['story_content'] ?? '') ?></textarea>
            </div>

            <!-- Highlights -->
            <div class="form-group">
                <label for="highlights">🌟 Punti salienti del viaggio</label>
                <textarea id="highlights" name="highlights" class="form-control" rows="3" placeholder="I momenti migliori, le esperienze uniche..."><?= htmlspecialchars($oldInput['highlights'] ?? '') ?></textarea>
            </div>

            <!-- Tips -->
            <div class="form-group">
                <label for="tips">💡 Consigli per chi vuole visitare</label>
                <textarea id="tips" name="tips" class="form-control" rows="4" placeholder="Consigli utili, cosa portare, dove andare, cosa evitare..."><?= htmlspecialchars($oldInput['tips'] ?? '') ?></textarea>
            </div>

            <!-- Budget Info -->
            <div class="form-group">
                <label for="budget_info">💰 Informazioni sul budget</label>
                <textarea id="budget_info" name="budget_info" class="form-control" rows="3" placeholder="Quanto hai speso? Consigli per risparmiare?"><?= htmlspecialchars($oldInput['budget_info'] ?? '') ?></textarea>
            </div>

            <!-- Overall Rating -->
            <div class="form-group">
                <label for="overall_rating">Valutazione generale</label>
                <select id="overall_rating" name="overall_rating" class="form-control">
                    <option value="">Non specificato</option>
                    <option value="5">⭐⭐⭐⭐⭐ Fantastico!</option>
                    <option value="4">⭐⭐⭐⭐ Molto bello</option>
                    <option value="3">⭐⭐⭐ Buono</option>
                    <option value="2">⭐⭐ Nella media</option>
                    <option value="1">⭐ Deludente</option>
                </select>
            </div>

            <!-- Would Return -->
            <div class="form-group">
                <label style="display: flex; align-items: center; padding: 0.75rem; background: var(--bg-light); border-radius: var(--border-radius); cursor: pointer;">
                    <input type="checkbox" name="would_return" value="1" checked style="margin-right: 0.5rem;">
                    🔄 Ci tornerei volentieri!
                </label>
            </div>

            <!-- Publish -->
            <div class="form-group">
                <label style="display: flex; align-items: center; padding: 0.75rem; background: var(--bg-light); border-radius: var(--border-radius); cursor: pointer;">
                    <input type="checkbox" name="is_published" value="1" checked style="margin-right: 0.5rem;">
                    ✅ Pubblica il racconto (sarà visibile a tutti)
                </label>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn-primary" style="flex: 1;">Pubblica Racconto</button>
                <a href="<?= SITE_URL ?>/dashboard.php" class="btn-secondary" style="flex: 1; text-align: center; line-height: 2.5;">Annulla</a>
            </div>
        </form>
    </div>
</div>

<script>
// Image preview
document.getElementById('cover_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('cover-preview');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" style="width: 100%; height: 100%; object-fit: cover;">';
        };
        reader.readAsDataURL(file);
    }
});
</script>

<?php include BASE_PATH . '/src/Views/layouts/footer.php'; ?>
