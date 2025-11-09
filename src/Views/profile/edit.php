<?php
$pageTitle = 'Modifica Profilo';
include BASE_PATH . '/src/Views/layouts/header.php';
?>

<div class="container" style="max-width: 800px; margin: 4rem auto;">
    <div class="auth-card" style="background: white; padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--shadow-lg);">
        <h1 class="text-center mb-4">Modifica il tuo profilo</h1>

        <form method="POST" action="<?= SITE_URL ?>/edit-profile.php" enctype="multipart/form-data">
            <!-- Profile Photo -->
            <div class="form-group">
                <label for="profile_photo">Foto profilo</label>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div id="photo-preview" style="width: 80px; height: 80px; border-radius: 50%; background: var(--bg-light); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <?php if ($user['profile_photo']): ?>
                            <img src="<?= SITE_URL ?>/uploads/profiles/<?= htmlspecialchars($user['profile_photo']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <span style="color: var(--text-secondary);">📷</span>
                        <?php endif; ?>
                    </div>
                    <div style="flex: 1;">
                        <label for="profile_photo" class="btn-secondary" style="cursor: pointer; display: inline-block;">
                            📁 Scegli nuova foto
                        </label>
                        <input type="file" id="profile_photo" name="profile_photo" accept="image/*" style="display: none;" data-preview="photo-preview">
                        <p style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.5rem;">JPG, PNG - Max 5MB</p>
                    </div>
                </div>
            </div>

            <!-- Personal Info -->
            <div class="form-group">
                <label for="first_name">Nome *</label>
                <input type="text" id="first_name" name="first_name" class="form-control" value="<?= htmlspecialchars($user['first_name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="last_name">Cognome *</label>
                <input type="text" id="last_name" name="last_name" class="form-control" value="<?= htmlspecialchars($user['last_name']) ?>" required>
            </div>

            <!-- Bio -->
            <div class="form-group">
                <label for="bio">Bio</label>
                <textarea id="bio" name="bio" class="form-control" rows="4" data-max-length="500"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
            </div>

            <!-- Location -->
            <div class="form-group">
                <label for="city">Città</label>
                <input type="text" id="city" name="city" class="form-control" value="<?= htmlspecialchars($user['city'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="country">Paese</label>
                <input type="text" id="country" name="country" class="form-control" value="<?= htmlspecialchars($user['country'] ?? '') ?>">
            </div>

            <!-- Birth Date -->
            <div class="form-group">
                <label for="date_of_birth">Data di Nascita</label>
                <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" value="<?= htmlspecialchars($user['date_of_birth'] ?? '') ?>">
            </div>

            <!-- Gender -->
            <div class="form-group">
                <label for="gender">Genere</label>
                <select id="gender" name="gender" class="form-control">
                    <option value="" <?= empty($user['gender']) ? 'selected' : '' ?>>Preferisco non dirlo</option>
                    <option value="male" <?= ($user['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Maschio</option>
                    <option value="female" <?= ($user['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Femmina</option>
                    <option value="other" <?= ($user['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Altro</option>
                </select>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn-primary" style="flex: 1;">Salva Modifiche</button>
                <a href="<?= SITE_URL ?>/profile.php" class="btn-secondary" style="flex: 1; text-align: center; line-height: 2.5;">Annulla</a>
            </div>
        </form>
    </div>
</div>

<script>
// Image preview for profile photo
document.getElementById('profile_photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('photo-preview');

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
