<?php
$pageTitle = 'Completa il tuo profilo';
include BASE_PATH . '/src/Views/layouts/header.php';

$userId = getCurrentUserId();
$userModel = new User();
$user = $userModel->getById($userId);
?>

<div class="container" style="max-width: 800px; margin: 4rem auto;">
    <div class="auth-card" style="background: white; padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--shadow-lg);">
        <h1 class="text-center mb-4">Completa il tuo profilo</h1>
        <p class="text-center mb-4" style="color: var(--text-secondary);">Raccontaci di più su di te per trovare compagni di viaggio compatibili!</p>

        <form method="POST" action="<?= SITE_URL ?>/complete-profile.php" enctype="multipart/form-data">
            <!-- Profile Photo -->
            <div class="form-group">
                <label for="profile_photo">Foto profilo</label>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div id="photo-preview" style="width: 80px; height: 80px; border-radius: 50%; background: var(--bg-light); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <span style="color: var(--text-secondary);">📷</span>
                    </div>
                    <div style="flex: 1;">
                        <label for="profile_photo" class="btn-secondary" style="cursor: pointer; display: inline-block;">
                            📁 Scegli foto
                        </label>
                        <input type="file" id="profile_photo" name="profile_photo" accept="image/*" style="display: none;" data-preview="photo-preview">
                        <p style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.5rem;">JPG, PNG - Max 5MB</p>
                    </div>
                </div>
            </div>

            <!-- Bio -->
            <div class="form-group">
                <label for="bio">Racconta qualcosa di te</label>
                <textarea id="bio" name="bio" class="form-control" rows="4" placeholder="Ciao! Sono appassionato di viaggi e mi piace..." data-max-length="500"></textarea>
            </div>

            <!-- Travel Styles -->
            <div class="form-group">
                <label>Stili di viaggio preferiti</label>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem;">
                    <label style="display: flex; align-items: center; padding: 0.75rem; background: var(--bg-light); border-radius: var(--border-radius); cursor: pointer;">
                        <input type="checkbox" name="travel_styles[]" value="avventura" style="margin-right: 0.5rem;">
                        🏔️ Avventura
                    </label>
                    <label style="display: flex; align-items: center; padding: 0.75rem; background: var(--bg-light); border-radius: var(--border-radius); cursor: pointer;">
                        <input type="checkbox" name="travel_styles[]" value="mare" style="margin-right: 0.5rem;">
                        🏖️ Mare
                    </label>
                    <label style="display: flex; align-items: center; padding: 0.75rem; background: var(--bg-light); border-radius: var(--border-radius); cursor: pointer;">
                        <input type="checkbox" name="travel_styles[]" value="città" style="margin-right: 0.5rem;">
                        🏙️ Città
                    </label>
                    <label style="display: flex; align-items: center; padding: 0.75rem; background: var(--bg-light); border-radius: var(--border-radius); cursor: pointer;">
                        <input type="checkbox" name="travel_styles[]" value="natura" style="margin-right: 0.5rem;">
                        🌲 Natura
                    </label>
                    <label style="display: flex; align-items: center; padding: 0.75rem; background: var(--bg-light); border-radius: var(--border-radius); cursor: pointer;">
                        <input type="checkbox" name="travel_styles[]" value="cultura" style="margin-right: 0.5rem;">
                        🎭 Cultura
                    </label>
                    <label style="display: flex; align-items: center; padding: 0.75rem; background: var(--bg-light); border-radius: var(--border-radius); cursor: pointer;">
                        <input type="checkbox" name="travel_styles[]" value="relax" style="margin-right: 0.5rem;">
                        🧘 Relax
                    </label>
                </div>
            </div>

            <!-- Budget Level -->
            <div class="form-group">
                <label for="budget_level">Livello di budget preferito</label>
                <select id="budget_level" name="budget_level" class="form-control">
                    <option value="low">Economico</option>
                    <option value="medium" selected>Medio</option>
                    <option value="high">Alto</option>
                </select>
            </div>

            <!-- Accommodation Type -->
            <div class="form-group">
                <label for="accommodation_type">Tipo di alloggio preferito</label>
                <select id="accommodation_type" name="accommodation_type" class="form-control">
                    <option value="">Qualsiasi</option>
                    <option value="hotel">Hotel</option>
                    <option value="hostel">Ostello</option>
                    <option value="airbnb">Airbnb/Casa</option>
                    <option value="camping">Campeggio</option>
                </select>
            </div>

            <!-- Languages -->
            <div class="form-group">
                <label>Lingue parlate</label>
                <div id="languages-container">
                    <div style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <select name="languages[]" class="form-control" style="flex: 1;">
                            <option value="">Seleziona lingua</option>
                            <option value="Italiano:it">🇮🇹 Italiano</option>
                            <option value="Inglese:en">🇬🇧 Inglese</option>
                            <option value="Spagnolo:es">🇪🇸 Spagnolo</option>
                            <option value="Francese:fr">🇫🇷 Francese</option>
                            <option value="Tedesco:de">🇩🇪 Tedesco</option>
                            <option value="Portoghese:pt">🇵🇹 Portoghese</option>
                        </select>
                        <select name="language_proficiency[it]" class="form-control" style="flex: 1;">
                            <option value="basic">Base</option>
                            <option value="intermediate">Intermedio</option>
                            <option value="advanced">Avanzato</option>
                            <option value="native">Madrelingua</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Preferences -->
            <div class="form-group">
                <label>Preferenze</label>
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label style="display: flex; align-items: center; padding: 0.75rem; background: var(--bg-light); border-radius: var(--border-radius); cursor: pointer;">
                        <input type="checkbox" name="smoking" value="1" style="margin-right: 0.5rem;">
                        🚬 Fumatore
                    </label>
                    <label style="display: flex; align-items: center; padding: 0.75rem; background: var(--bg-light); border-radius: var(--border-radius); cursor: pointer;">
                        <input type="checkbox" name="pets" value="1" style="margin-right: 0.5rem;">
                        🐕 Amante degli animali
                    </label>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn-primary" style="flex: 1;">Salva e Continua</button>
                <a href="<?= SITE_URL ?>/dashboard.php" class="btn-secondary" style="flex: 1; text-align: center; line-height: 2.5;">Salta per ora</a>
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
