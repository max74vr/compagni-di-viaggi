<?php
/**
 * Template Name: Registrazione
 *
 * Multi-step registration form
 */

// Redirect if already logged in AND profile is complete
if (is_user_logged_in()) {
    $user_id = get_current_user_id();
    $profile_completed = get_user_meta($user_id, 'cdv_profile_completed', true);

    // Only redirect if registration is fully complete
    if ($profile_completed === '1') {
        wp_redirect(home_url('/dashboard'));
        exit;
    }
}

get_header();
?>

<main class="site-main registration-page">
    <div class="container">
        <div class="registration-wrapper">
            <div class="registration-header">
                <h1>Unisciti a Compagni di Viaggi</h1>
                <p>Crea il tuo account e inizia a trovare compagni di viaggio</p>
            </div>

            <!-- Progress Steps -->
            <div class="registration-steps">
                <div class="step active" data-step="1">
                    <div class="step-number">1</div>
                    <div class="step-label">Account</div>
                </div>
                <div class="step-connector"></div>
                <div class="step" data-step="2">
                    <div class="step-number">2</div>
                    <div class="step-label">Profilo</div>
                </div>
                <div class="step-connector"></div>
                <div class="step" data-step="3">
                    <div class="step-number">3</div>
                    <div class="step-label">Foto</div>
                </div>
                <div class="step-connector"></div>
                <div class="step" data-step="4">
                    <div class="step-number">4</div>
                    <div class="step-label">Viaggio (Opzionale)</div>
                </div>
            </div>

            <div class="registration-form-container">
                <!-- Step 1: Account Creation -->
                <form id="registration-step-1" class="registration-step active">
                    <h2>Crea il tuo Account</h2>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="display_name">Nome e Cognome <span class="required">*</span></label>
                            <input type="text" id="display_name" name="display_name" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="username">Username <span class="required">*</span></label>
                            <input type="text" id="username" name="username" required>
                            <small>Solo lettere, numeri e underscore</small>
                        </div>

                        <div class="form-group">
                            <label for="email">Email <span class="required">*</span></label>
                            <input type="email" id="email" name="email" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Password <span class="required">*</span></label>
                            <input type="password" id="password" name="password" required minlength="8">
                            <small>Minimo 8 caratteri</small>
                        </div>

                        <div class="form-group">
                            <label for="password_confirm">Conferma Password <span class="required">*</span></label>
                            <input type="password" id="password_confirm" name="password_confirm" required>
                        </div>
                    </div>

                    <div class="form-group checkbox-group">
                        <label>
                            <input type="checkbox" name="terms" required>
                            Accetto i <a href="<?php echo home_url('/termini'); ?>" target="_blank">Termini e Condizioni</a> e la <a href="<?php echo home_url('/privacy'); ?>" target="_blank">Privacy Policy</a>
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary btn-large">Continua →</button>
                    </div>

                    <div class="form-footer">
                        Hai già un account? <a href="<?php echo wp_login_url(); ?>">Accedi</a>
                    </div>
                </form>

                <!-- Step 2: Profile Information -->
                <form id="registration-step-2" class="registration-step">
                    <h2>Completa il tuo Profilo</h2>

                    <div class="form-section">
                        <h3>Informazioni Personali</h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="birth_date">Data di Nascita <span class="required">*</span></label>
                                <input type="date" id="birth_date" name="birth_date" required max="<?php echo date('Y-m-d', strtotime('-18 years')); ?>">
                                <small>Devi avere almeno 18 anni</small>
                            </div>

                            <div class="form-group">
                                <label for="gender">Genere</label>
                                <select id="gender" name="gender">
                                    <option value="">Preferisco non dire</option>
                                    <option value="male">Uomo</option>
                                    <option value="female">Donna</option>
                                    <option value="other">Altro</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="city">Città <span class="required">*</span></label>
                                <input type="text" id="city" name="city" required placeholder="Es: Milano">
                            </div>

                            <div class="form-group">
                                <label for="country">Paese <span class="required">*</span></label>
                                <input type="text" id="country" name="country" required value="Italia">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="phone">Telefono (opzionale)</label>
                            <input type="tel" id="phone" name="phone" placeholder="+39 123 456 7890">
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Parlami di Te</h3>

                        <div class="form-group">
                            <label for="bio">Bio <span class="required">*</span></label>
                            <textarea id="bio" name="bio" rows="5" required placeholder="Raccontaci chi sei, cosa ami dei viaggi, le tue esperienze..."></textarea>
                            <small id="bio-count">0/500 caratteri</small>
                        </div>

                        <div class="form-group">
                            <label for="languages">Lingue Parlate <span class="required">*</span></label>
                            <input type="text" id="languages" name="languages" required placeholder="Es: Italiano, Inglese, Spagnolo">
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Stili di Viaggio</h3>
                        <p>Seleziona i tuoi stili preferiti:</p>

                        <div class="checkbox-grid">
                            <label><input type="checkbox" name="travel_styles[]" value="Avventura"> Avventura</label>
                            <label><input type="checkbox" name="travel_styles[]" value="Mare"> Mare</label>
                            <label><input type="checkbox" name="travel_styles[]" value="Montagna"> Montagna</label>
                            <label><input type="checkbox" name="travel_styles[]" value="Città d'Arte"> Città d'Arte</label>
                            <label><input type="checkbox" name="travel_styles[]" value="Cultura"> Cultura</label>
                            <label><input type="checkbox" name="travel_styles[]" value="Relax"> Relax</label>
                            <label><input type="checkbox" name="travel_styles[]" value="Food & Wine"> Food & Wine</label>
                            <label><input type="checkbox" name="travel_styles[]" value="Sport"> Sport</label>
                            <label><input type="checkbox" name="travel_styles[]" value="Zaino in Spalla"> Zaino in Spalla</label>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Interessi</h3>
                        <p>Cosa ti piace fare in viaggio?</p>

                        <div class="checkbox-grid">
                            <label><input type="checkbox" name="interests[]" value="Fotografia"> Fotografia</label>
                            <label><input type="checkbox" name="interests[]" value="Trekking"> Trekking</label>
                            <label><input type="checkbox" name="interests[]" value="Yoga"> Yoga</label>
                            <label><input type="checkbox" name="interests[]" value="Immersioni"> Immersioni</label>
                            <label><input type="checkbox" name="interests[]" value="Storia"> Storia</label>
                            <label><input type="checkbox" name="interests[]" value="Arte"> Arte</label>
                            <label><input type="checkbox" name="interests[]" value="Cucina Locale"> Cucina Locale</label>
                            <label><input type="checkbox" name="interests[]" value="Vita Notturna"> Vita Notturna</label>
                            <label><input type="checkbox" name="interests[]" value="Volontariato"> Volontariato</label>
                            <label><input type="checkbox" name="interests[]" value="Wildlife"> Wildlife</label>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Preferenze di Viaggio</h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="budget_range">Range di Budget</label>
                                <select id="budget_range" name="budget_range">
                                    <option value="economico">Economico (< 500€)</option>
                                    <option value="medio">Medio (500-1500€)</option>
                                    <option value="comfort">Comfort (1500-3000€)</option>
                                    <option value="lusso">Lusso (> 3000€)</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="travel_frequency">Quanto viaggi?</label>
                                <select id="travel_frequency" name="travel_frequency">
                                    <option value="raro">Raramente (1-2 volte/anno)</option>
                                    <option value="occasionale">Occasionalmente (3-4 volte/anno)</option>
                                    <option value="frequente">Frequentemente (5+ volte/anno)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="accommodation_preference">Preferenza Alloggio</label>
                                <select id="accommodation_preference" name="accommodation_preference">
                                    <option value="hostel">Ostelli</option>
                                    <option value="hotel">Hotel</option>
                                    <option value="bnb">B&B / Airbnb</option>
                                    <option value="camping">Camping</option>
                                    <option value="misto">Misto</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="travel_pace">Ritmo di Viaggio</label>
                                <select id="travel_pace" name="travel_pace">
                                    <option value="rilassato">Rilassato</option>
                                    <option value="moderato">Moderato</option>
                                    <option value="intenso">Intenso</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Social (Opzionale)</h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="instagram">Instagram</label>
                                <input type="text" id="instagram" name="instagram" placeholder="@tuousername">
                            </div>

                            <div class="form-group">
                                <label for="facebook">Facebook</label>
                                <input type="text" id="facebook" name="facebook" placeholder="URL profilo">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Privacy</h3>
                        <p>Scegli cosa mostrare nel tuo profilo pubblico:</p>

                        <div class="checkbox-list">
                            <label><input type="checkbox" name="show_age" checked> Mostra la mia età</label>
                            <label><input type="checkbox" name="show_phone"> Mostra il mio telefono</label>
                            <label><input type="checkbox" name="show_email"> Mostra la mia email</label>
                            <label><input type="checkbox" name="show_social" checked> Mostra i miei social</label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-secondary btn-prev">← Indietro</button>
                        <button type="submit" class="btn-primary btn-large">Continua →</button>
                    </div>
                </form>

                <!-- Step 3: Profile Image -->
                <form id="registration-step-3" class="registration-step">
                    <h2>Aggiungi la tua Foto Profilo</h2>

                    <div class="profile-image-section">
                        <div class="image-preview">
                            <img id="profile-preview" src="" alt="Anteprima" style="display: none;">
                            <div class="placeholder-avatar">
                                <span class="icon">📷</span>
                                <p>Carica una tua foto</p>
                            </div>
                        </div>

                        <div class="image-upload-controls">
                            <input type="file" id="profile_image" name="profile_image" accept="image/jpeg,image/png,image/jpg" style="display: none;">
                            <button type="button" class="btn-secondary" id="upload-btn">Scegli Foto</button>
                            <small>JPG o PNG, max 5MB</small>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-secondary btn-prev">← Indietro</button>
                        <button type="button" class="btn-secondary" id="skip-photo">Salta per ora</button>
                        <button type="button" class="btn-primary btn-large" id="continue-to-travel">Continua →</button>
                    </div>
                </form>

                <!-- Step 4: Create First Travel (Optional) -->
                <form id="registration-step-4" class="registration-step">
                    <h2>Crea il Tuo Primo Viaggio (Opzionale)</h2>
                    <p class="step-intro">Hai già in mente un viaggio? Crealo ora e inizia a trovare compagni!</p>

                    <div class="form-group">
                        <label for="travel_title">Titolo del Viaggio <span class="required">*</span></label>
                        <input type="text" id="travel_title" name="travel_title" placeholder="Es: Weekend a Venezia, Road Trip in Toscana">
                    </div>

                    <div class="form-group">
                        <label for="travel_description">Descrizione <span class="required">*</span></label>
                        <textarea id="travel_description" name="travel_description" rows="5" placeholder="Descrivi il tuo viaggio: destinazioni, attività previste, cosa rende speciale questa esperienza..."></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="travel_destination">Destinazione <span class="required">*</span></label>
                            <input type="text" id="travel_destination" name="travel_destination" placeholder="Es: Venezia, Toscana">
                        </div>

                        <div class="form-group">
                            <label for="travel_country">Paese <span class="required">*</span></label>
                            <input type="text" id="travel_country" name="travel_country" placeholder="Es: Italia, Francia">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="travel_start_date">Data Inizio <span class="required">*</span></label>
                            <input type="date" id="travel_start_date" name="travel_start_date">
                        </div>

                        <div class="form-group">
                            <label for="travel_end_date">Data Fine <span class="required">*</span></label>
                            <input type="date" id="travel_end_date" name="travel_end_date">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="travel_budget">Budget per Persona (€) <span class="required">*</span></label>
                            <input type="number" id="travel_budget" name="travel_budget" min="0" placeholder="500">
                        </div>

                        <div class="form-group">
                            <label for="travel_max_participants">Max Partecipanti <span class="required">*</span></label>
                            <input type="number" id="travel_max_participants" name="travel_max_participants" min="2" max="50" value="5">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Tipo di Viaggio</label>
                        <div class="checkbox-group">
                            <?php
                            $travel_types = get_terms(array(
                                'taxonomy' => 'tipo_viaggio',
                                'hide_empty' => false,
                            ));
                            if (!empty($travel_types) && !is_wp_error($travel_types)) :
                                foreach ($travel_types as $type) :
                            ?>
                                <label>
                                    <input type="checkbox" name="travel_types[]" value="<?php echo esc_attr($type->term_id); ?>">
                                    <?php echo esc_html($type->name); ?>
                                </label>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-secondary btn-prev">← Indietro</button>
                        <button type="button" class="btn-secondary" id="skip-travel">Salta e Completa</button>
                        <button type="submit" class="btn-primary btn-large">Crea Viaggio e Completa ✓</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<style>
.registration-page {
    padding: calc(var(--spacing-unit) * 6) 0;
    background: var(--bg-light);
}

.registration-wrapper {
    max-width: 800px;
    margin: 0 auto;
}

.registration-header {
    text-align: center;
    margin-bottom: calc(var(--spacing-unit) * 6);
}

.registration-header h1 {
    margin-bottom: calc(var(--spacing-unit) * 2);
}

/* Progress Steps */
.registration-steps {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: calc(var(--spacing-unit) * 6);
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: calc(var(--spacing-unit) * 1);
}

.step-number {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--bg-gray);
    color: var(--text-medium);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1.25rem;
    transition: all var(--transition-base);
}

.step.active .step-number,
.step.completed .step-number {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
}

.step-label {
    font-size: 0.9rem;
    color: var(--text-medium);
}

.step-connector {
    width: 80px;
    height: 2px;
    background: var(--bg-gray);
    margin: 0 calc(var(--spacing-unit) * 2);
}

/* Form Container */
.registration-form-container {
    background: white;
    padding: calc(var(--spacing-unit) * 4);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-md);
}

.registration-step {
    display: none;
}

.registration-step.active {
    display: block;
}

.registration-step h2 {
    margin-bottom: calc(var(--spacing-unit) * 4);
    text-align: center;
}

/* Form Sections */
.form-section {
    margin-bottom: calc(var(--spacing-unit) * 4);
    padding-bottom: calc(var(--spacing-unit) * 4);
    border-bottom: 1px solid var(--border-color);
}

.form-section:last-of-type {
    border-bottom: none;
}

.form-section h3 {
    margin-bottom: calc(var(--spacing-unit) * 2);
    color: var(--primary-color);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: calc(var(--spacing-unit) * 2);
}

.form-group {
    margin-bottom: calc(var(--spacing-unit) * 2);
}

.form-group label {
    display: block;
    margin-bottom: calc(var(--spacing-unit) * 1);
    font-weight: 500;
    color: var(--text-dark);
}

.required {
    color: var(--error-color);
}

.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="password"],
.form-group input[type="date"],
.form-group input[type="tel"],
.form-group select,
.form-group textarea {
    width: 100%;
    padding: calc(var(--spacing-unit) * 1.5);
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius-sm);
    font-family: var(--font-primary);
    font-size: 1rem;
    transition: border-color var(--transition-fast);
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary-color);
}

.form-group small {
    display: block;
    margin-top: calc(var(--spacing-unit) * 0.5);
    color: var(--text-light);
    font-size: 0.85rem;
}

/* Checkbox Grid */
.checkbox-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: calc(var(--spacing-unit) * 1.5);
}

.checkbox-grid label,
.checkbox-list label {
    display: flex;
    align-items: center;
    gap: calc(var(--spacing-unit) * 1);
    cursor: pointer;
    padding: calc(var(--spacing-unit) * 1);
    border-radius: var(--border-radius-sm);
    transition: background-color var(--transition-fast);
}

.checkbox-grid label:hover,
.checkbox-list label:hover {
    background-color: var(--bg-light);
}

/* Profile Image Section */
.profile-image-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: calc(var(--spacing-unit) * 3);
    margin: calc(var(--spacing-unit) * 4) 0;
}

.image-preview {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid var(--border-color);
}

.image-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.placeholder-avatar {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: var(--bg-light);
    color: var(--text-light);
}

.placeholder-avatar .icon {
    font-size: 3rem;
    margin-bottom: calc(var(--spacing-unit) * 1);
}

.image-upload-controls {
    text-align: center;
}

/* Form Actions */
.form-actions {
    display: flex;
    gap: calc(var(--spacing-unit) * 2);
    justify-content: center;
    margin-top: calc(var(--spacing-unit) * 4);
    padding-top: calc(var(--spacing-unit) * 4);
    border-top: 1px solid var(--border-color);
}

.btn-large {
    padding: calc(var(--spacing-unit) * 2) calc(var(--spacing-unit) * 4);
    font-size: 1.1rem;
}

.form-footer {
    text-align: center;
    margin-top: calc(var(--spacing-unit) * 3);
    color: var(--text-medium);
}

/* Loading State */
.loading {
    opacity: 0.6;
    pointer-events: none;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }

    .registration-steps {
        scale: 0.8;
    }

    .step-connector {
        width: 40px;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    let currentStep = 1;
    let userId = null;

    // Bio character count
    $('#bio').on('input', function() {
        const count = $(this).val().length;
        $('#bio-count').text(count + '/500 caratteri');

        if (count > 500) {
            $(this).val($(this).val().substring(0, 500));
        }
    });

    // Step 1: Account Creation
    $('#registration-step-1').on('submit', function(e) {
        e.preventDefault();

        const password = $('#password').val();
        const confirm = $('#password_confirm').val();

        if (password !== confirm) {
            alert('Le password non coincidono');
            return;
        }

        const formData = {
            action: 'cdv_register_step1',
            nonce: cdvAjax.nonce,
            username: $('#username').val(),
            email: $('#email').val(),
            password: password,
            display_name: $('#display_name').val(),
        };

        $(this).addClass('loading');

        $.ajax({
            url: cdvAjax.ajaxurl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    userId = response.data.user_id;
                    nextStep();
                } else {
                    alert(response.data.message || 'Errore durante la registrazione');
                }
            },
            error: function() {
                alert('Errore di connessione');
            },
            complete: function() {
                $('#registration-step-1').removeClass('loading');
            }
        });
    });

    // Step 2: Profile Information
    $('#registration-step-2').on('submit', function(e) {
        e.preventDefault();

        // Check at least one travel style selected
        if ($('input[name="travel_styles[]"]:checked').length === 0) {
            alert('Seleziona almeno uno stile di viaggio');
            return;
        }

        const formData = $(this).serializeArray();
        formData.push({ name: 'action', value: 'cdv_register_step2' });
        formData.push({ name: 'nonce', value: cdvAjax.nonce });

        console.log('Step 2 - Sending data:', formData);
        console.log('AJAX URL:', cdvAjax.ajaxurl);

        $(this).addClass('loading');

        $.ajax({
            url: cdvAjax.ajaxurl,
            type: 'POST',
            data: $.param(formData),
            dataType: 'json',
            success: function(response) {
                console.log('Step 2 - Success response:', response);
                if (response.success) {
                    nextStep();
                } else {
                    alert(response.data.message || 'Errore durante il salvataggio');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Step 2 - Error details:', {
                    status: jqXHR.status,
                    statusText: jqXHR.statusText,
                    textStatus: textStatus,
                    errorThrown: errorThrown,
                    responseText: jqXHR.responseText
                });

                let errorMsg = 'Errore di connessione';
                if (jqXHR.status === 500) {
                    errorMsg = 'Errore del server (500). Controlla i log PHP.';
                } else if (jqXHR.status === 403) {
                    errorMsg = 'Accesso negato (403). Problema con il nonce.';
                } else if (jqXHR.status === 404) {
                    errorMsg = 'Endpoint non trovato (404).';
                } else if (jqXHR.responseText) {
                    errorMsg = 'Errore: ' + jqXHR.responseText.substring(0, 100);
                }

                alert(errorMsg);
            },
            complete: function() {
                $('#registration-step-2').removeClass('loading');
            }
        });
    });

    // Step 3: Profile Image
    $('#upload-btn').on('click', function() {
        $('#profile_image').click();
    });

    $('#profile_image').on('change', function(e) {
        const file = e.target.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#profile-preview').attr('src', e.target.result).show();
                $('.placeholder-avatar').hide();
            };
            reader.readAsDataURL(file);
        }
    });

    // Continue to travel step button
    $('#continue-to-travel').on('click', function() {
        const file = $('#profile_image')[0].files[0];

        if (file) {
            // Upload photo first, then go to step 4
            const formData = new FormData();
            formData.append('action', 'cdv_upload_profile_image');
            formData.append('nonce', cdvAjax.nonce);
            formData.append('profile_image', file);

            $('#registration-step-3').addClass('loading');

            $.ajax({
                url: cdvAjax.ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        nextStep(); // Go to step 4
                    } else {
                        alert(response.data.message || 'Errore durante l\'upload');
                    }
                },
                error: function() {
                    alert('Errore di connessione');
                },
                complete: function() {
                    $('#registration-step-3').removeClass('loading');
                }
            });
        } else {
            // Skip photo and go to step 4
            nextStep();
        }
    });

    $('#skip-photo').on('click', function() {
        nextStep(); // Go to step 4
    });

    // Step 4: Create Travel (Optional)
    $('#registration-step-4').on('submit', function(e) {
        e.preventDefault();

        const formData = $(this).serializeArray();
        formData.push({ name: 'action', value: 'cdv_create_first_travel' });
        formData.push({ name: 'nonce', value: cdvAjax.nonce });

        $(this).addClass('loading');

        $.ajax({
            url: cdvAjax.ajaxurl,
            type: 'POST',
            data: $.param(formData),
            success: function(response) {
                if (response.success) {
                    window.location.href = '<?php echo home_url('/profilo-in-attesa'); ?>';
                } else {
                    alert(response.data.message || 'Errore durante la creazione del viaggio');
                }
            },
            error: function() {
                alert('Errore di connessione');
            },
            complete: function() {
                $('#registration-step-4').removeClass('loading');
            }
        });
    });

    $('#skip-travel').on('click', function() {
        window.location.href = '<?php echo home_url('/profilo-in-attesa'); ?>';
    });

    // Previous buttons
    $('.btn-prev').on('click', function() {
        prevStep();
    });

    function nextStep() {
        currentStep++;
        updateSteps();
    }

    function prevStep() {
        if (currentStep > 1) {
            currentStep--;
            updateSteps();
        }
    }

    function updateSteps() {
        // Update step indicators
        $('.step').removeClass('active completed');
        $('.step[data-step="' + currentStep + '"]').addClass('active');
        $('.step[data-step]').each(function() {
            const step = parseInt($(this).data('step'));
            if (step < currentStep) {
                $(this).addClass('completed');
            }
        });

        // Update forms
        $('.registration-step').removeClass('active');
        $('#registration-step-' + currentStep).addClass('active');

        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
});
</script>

<?php
get_footer();
