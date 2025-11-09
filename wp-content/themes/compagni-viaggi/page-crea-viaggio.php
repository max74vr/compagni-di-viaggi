<?php
/**
 * Template Name: Crea Viaggio
 * Description: Form per creare un nuovo viaggio
 */

// Check if user is logged in
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

// Check if user has capability to create travels
$user_id = get_current_user_id();
if (!current_user_can('create_viaggi')) {
    wp_redirect(home_url('/dashboard'));
    exit;
}

get_header();
?>

<main class="site-main">
    <div class="create-travel-page">
        <div class="container">
            <div class="create-travel-wrapper">
                <div class="page-header">
                    <h1>Crea un Nuovo Viaggio</h1>
                    <p>Compila il form per proporre il tuo viaggio e trovare compagni di avventura!</p>
                </div>

                <form id="create-travel-form" class="travel-form">
                    <div class="form-section">
                        <h3>Informazioni Generali</h3>

                        <div class="form-group">
                            <label for="travel_title">Titolo del Viaggio <span class="required">*</span></label>
                            <input type="text" id="travel_title" name="travel_title" required placeholder="Es: Weekend a Venezia, Road Trip in Toscana">
                        </div>

                        <div class="form-group">
                            <label for="travel_description">Descrizione <span class="required">*</span></label>
                            <textarea id="travel_description" name="travel_description" rows="6" required placeholder="Descrivi il tuo viaggio: destinazioni, attività previste, cosa rende speciale questa esperienza..."></textarea>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Destinazione</h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="travel_destination">Destinazione <span class="required">*</span></label>
                                <input type="text" id="travel_destination" name="travel_destination" required placeholder="Es: Venezia, Toscana">
                            </div>

                            <div class="form-group">
                                <label for="travel_country">Paese <span class="required">*</span></label>
                                <input type="text" id="travel_country" name="travel_country" required placeholder="Es: Italia, Francia">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Date e Budget</h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="travel_start_date">Data Inizio <span class="required">*</span></label>
                                <input type="date" id="travel_start_date" name="travel_start_date" required min="<?php echo date('Y-m-d'); ?>">
                            </div>

                            <div class="form-group">
                                <label for="travel_end_date">Data Fine <span class="required">*</span></label>
                                <input type="date" id="travel_end_date" name="travel_end_date" required min="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="travel_budget">Budget per Persona (€) <span class="required">*</span></label>
                                <input type="number" id="travel_budget" name="travel_budget" min="0" required placeholder="500">
                            </div>

                            <div class="form-group">
                                <label for="travel_max_participants">Max Partecipanti <span class="required">*</span></label>
                                <input type="number" id="travel_max_participants" name="travel_max_participants" min="2" max="50" value="5" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Tipo di Viaggio</h3>
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
                        <a href="<?php echo esc_url(home_url('/dashboard')); ?>" class="btn-secondary">Annulla</a>
                        <button type="submit" class="btn-primary btn-large">Crea Annuncio 🚀</button>
                    </div>

                    <div id="form-messages" style="margin-top: 20px;"></div>
                </form>
            </div>
        </div>
    </div>
</main>

<style>
.create-travel-page {
    padding: calc(var(--spacing-unit) * 6) 0;
    background: var(--bg-light);
    min-height: 80vh;
}

.create-travel-wrapper {
    max-width: 900px;
    margin: 0 auto;
    background: white;
    padding: calc(var(--spacing-unit) * 6);
    border-radius: 12px;
    box-shadow: 0 2px 20px rgba(0,0,0,0.08);
}

.page-header {
    text-align: center;
    margin-bottom: calc(var(--spacing-unit) * 6);
}

.page-header h1 {
    margin-bottom: calc(var(--spacing-unit) * 2);
    color: var(--primary-color);
}

.page-header p {
    font-size: 1.1rem;
    color: var(--text-medium);
}

.travel-form .form-section {
    margin-bottom: calc(var(--spacing-unit) * 5);
    padding-bottom: calc(var(--spacing-unit) * 5);
    border-bottom: 1px solid var(--border-color);
}

.travel-form .form-section:last-of-type {
    border-bottom: none;
    padding-bottom: 0;
}

.travel-form .form-section h3 {
    color: var(--text-dark);
    margin-bottom: calc(var(--spacing-unit) * 3);
    font-size: 1.3rem;
}

.required {
    color: var(--error-color);
}

.checkbox-group {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: calc(var(--spacing-unit) * 2);
}

.checkbox-group label {
    display: flex;
    align-items: center;
    gap: calc(var(--spacing-unit) * 1);
    padding: calc(var(--spacing-unit) * 1.5);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s;
}

.checkbox-group label:hover {
    border-color: var(--primary-color);
    background: rgba(var(--primary-rgb), 0.05);
}

.checkbox-group input[type="checkbox"] {
    cursor: pointer;
}

.form-actions {
    display: flex;
    gap: calc(var(--spacing-unit) * 2);
    justify-content: center;
    margin-top: calc(var(--spacing-unit) * 4);
}

.success-message {
    background: var(--success-color);
    color: white;
    padding: calc(var(--spacing-unit) * 3);
    border-radius: 8px;
    text-align: center;
}

.error-message {
    background: var(--error-color);
    color: white;
    padding: calc(var(--spacing-unit) * 3);
    border-radius: 8px;
    text-align: center;
}

@media (max-width: 768px) {
    .create-travel-wrapper {
        padding: calc(var(--spacing-unit) * 4);
    }

    .checkbox-group {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    $('#create-travel-form').on('submit', function(e) {
        e.preventDefault();

        const $form = $(this);
        const $submitBtn = $form.find('button[type="submit"]');
        const $messages = $('#form-messages');

        // Validate dates
        const startDate = new Date($('#travel_start_date').val());
        const endDate = new Date($('#travel_end_date').val());

        if (endDate <= startDate) {
            $messages.html('<div class="error-message">La data di fine deve essere successiva alla data di inizio.</div>');
            return;
        }

        // Get travel types
        const travelTypes = [];
        $('input[name="travel_types[]"]:checked').each(function() {
            travelTypes.push($(this).val());
        });

        // Disable submit button
        $submitBtn.prop('disabled', true).text('Creazione in corso...');
        $messages.empty();

        $.ajax({
            url: cdvAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'cdv_create_travel',
                nonce: cdvAjax.nonce,
                title: $('#travel_title').val(),
                description: $('#travel_description').val(),
                destination: $('#travel_destination').val(),
                country: $('#travel_country').val(),
                start_date: $('#travel_start_date').val(),
                end_date: $('#travel_end_date').val(),
                budget: $('#travel_budget').val(),
                max_participants: $('#travel_max_participants').val(),
                travel_types: travelTypes
            },
            success: function(response) {
                if (response.success) {
                    $messages.html('<div class="success-message">' + response.data.message + '</div>');

                    // Redirect to the travel page after 1.5 seconds
                    setTimeout(function() {
                        window.location.href = response.data.redirect_url;
                    }, 1500);
                } else {
                    $messages.html('<div class="error-message">' + response.data.message + '</div>');
                    $submitBtn.prop('disabled', false).text('Crea Annuncio 🚀');
                }
            },
            error: function() {
                $messages.html('<div class="error-message">Si è verificato un errore. Riprova più tardi.</div>');
                $submitBtn.prop('disabled', false).text('Crea Annuncio 🚀');
            }
        });
    });

    // Update end date min when start date changes
    $('#travel_start_date').on('change', function() {
        const startDate = $(this).val();
        $('#travel_end_date').attr('min', startDate);
    });
});
</script>

<?php
get_footer();
