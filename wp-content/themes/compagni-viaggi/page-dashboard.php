<?php
/**
 * Template Name: Dashboard
 * Template Gestione Dashboard Viaggiatore
 */

if (!is_user_logged_in()) {
    wp_redirect(home_url('/registrazione/'));
    exit;
}

get_header();

$current_user = wp_get_current_user();
$user_approved = get_user_meta($current_user->ID, 'cdv_user_approved', true);

// Users are now auto-approved (value is '1'), no need to check
// if ($user_approved !== '1' && $user_approved !== 'approved') {
//     wp_redirect(home_url('/profilo-in-attesa/'));
//     exit;
// }

// Query viaggi organizzati dall'utente
$my_travels = new WP_Query(array(
    'post_type' => 'viaggio',
    'author' => $current_user->ID,
    'post_status' => array('publish', 'pending', 'draft'),
    'posts_per_page' => -1,
    'orderby' => 'date',
    'order' => 'DESC',
));

// Query viaggi a cui partecipo
global $wpdb;
$participants_table = $wpdb->prefix . 'cdv_participants';
$participated_ids = $wpdb->get_col($wpdb->prepare(
    "SELECT travel_id FROM $participants_table WHERE user_id = %d AND status = 'accepted'",
    $current_user->ID
));

$participated_travels = null;
if (!empty($participated_ids)) {
    $participated_travels = new WP_Query(array(
        'post_type' => 'viaggio',
        'post__in' => $participated_ids,
        'post_status' => 'publish',
        'posts_per_page' => -1,
    ));
}

// Conta richieste pendenti
$pending_requests = $wpdb->get_results($wpdb->prepare(
    "SELECT p.*, t.post_title, u.display_name, u.user_login
    FROM $participants_table p
    LEFT JOIN {$wpdb->posts} t ON p.travel_id = t.ID
    LEFT JOIN {$wpdb->users} u ON p.user_id = u.ID
    WHERE t.post_author = %d AND p.status = 'pending'
    ORDER BY p.created_at DESC",
    $current_user->ID
));
?>

<main class="site-main dashboard">
    <div class="container">
        <div class="dashboard-header">
            <div>
                <h1>Benvenuto, <?php echo esc_html($current_user->display_name); ?>!</h1>
                <p>Gestisci i tuoi viaggi e le richieste di partecipazione</p>
            </div>
            <a href="<?php echo CDV_User_Profiles::get_profile_url($current_user->ID); ?>" class="btn btn-secondary">
                Vedi Profilo Pubblico
            </a>
        </div>

        <!-- Tab Navigation -->
        <div class="dashboard-tabs">
            <button class="tab-button active" data-tab="my-travels">
                I Miei Viaggi (<?php echo $my_travels->post_count; ?>)
            </button>
            <button class="tab-button" data-tab="requests">
                Richieste di Partecipazione
                <?php if (count($pending_requests) > 0) : ?>
                    <span class="badge-count"><?php echo count($pending_requests); ?></span>
                <?php endif; ?>
            </button>
            <button class="tab-button" data-tab="participating">
                Viaggi a cui Partecipo
                <?php if ($participated_travels) : ?>
                    (<?php echo $participated_travels->post_count; ?>)
                <?php endif; ?>
            </button>
            <button class="tab-button" data-tab="settings">Impostazioni</button>
        </div>

        <!-- Tab: I Miei Viaggi -->
        <div class="tab-content active" id="tab-my-travels">
            <div class="section-header">
                <h2>I Miei Viaggi</h2>
                <a href="#" class="btn btn-primary" id="btn-new-travel">
                    <i class="icon-plus"></i> Nuovo Viaggio
                </a>
            </div>

            <?php if ($my_travels->have_posts()) : ?>
                <div class="travels-list">
                    <?php while ($my_travels->have_posts()) : $my_travels->the_post(); ?>
                        <?php
                        $travel_id = get_the_ID();
                        $participants = CDV_Participants::get_participants($travel_id, 'accepted');
                        $pending = CDV_Participants::get_participants($travel_id, 'pending');
                        $max_participants = get_post_meta($travel_id, 'cdv_max_participants', true);
                        $travel_status = get_post_meta($travel_id, 'cdv_travel_status', true);
                        $post_status = get_post_status();
                        ?>
                        <div class="travel-item" data-travel-id="<?php echo $travel_id; ?>">
                            <div class="travel-item-header">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="travel-thumb">
                                        <?php the_post_thumbnail('thumbnail'); ?>
                                    </div>
                                <?php endif; ?>

                                <div class="travel-item-info">
                                    <h3>
                                        <a href="<?php the_permalink(); ?>" target="_blank">
                                            <?php the_title(); ?>
                                        </a>
                                    </h3>
                                    <p class="travel-meta">
                                        <span class="status-badge status-<?php echo $post_status; ?>">
                                            <?php
                                            echo $post_status === 'publish' ? 'Pubblicato' :
                                                ($post_status === 'pending' ? 'In Attesa di Approvazione' : 'Bozza');
                                            ?>
                                        </span>
                                        <?php if ($post_status === 'publish') : ?>
                                            <span class="travel-status-badge travel-<?php echo $travel_status; ?>">
                                                <?php echo ucfirst($travel_status); ?>
                                            </span>
                                        <?php endif; ?>
                                        <span><i class="icon-users"></i> <?php echo count($participants); ?>/<?php echo $max_participants; ?> partecipanti</span>
                                        <?php if (count($pending) > 0) : ?>
                                            <span class="pending-requests">
                                                <i class="icon-alert"></i> <?php echo count($pending); ?> richieste
                                            </span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>

                            <div class="travel-item-actions">
                                <a href="<?php the_permalink(); ?>" class="btn btn-sm btn-secondary" target="_blank">
                                    <i class="icon-eye"></i> Visualizza
                                </a>

                                <?php if ($post_status === 'publish') : ?>
                                    <select class="travel-status-select" data-travel-id="<?php echo $travel_id; ?>">
                                        <option value="open" <?php selected($travel_status, 'open'); ?>>Aperto</option>
                                        <option value="full" <?php selected($travel_status, 'full'); ?>>Completo</option>
                                        <option value="closed" <?php selected($travel_status, 'closed'); ?>>Chiuso</option>
                                        <option value="completed" <?php selected($travel_status, 'completed'); ?>>Completato</option>
                                    </select>
                                <?php endif; ?>

                                <button class="btn btn-sm btn-danger delete-travel" data-travel-id="<?php echo $travel_id; ?>">
                                    <i class="icon-trash"></i> Elimina
                                </button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                </div>
            <?php else : ?>
                <p class="no-content">Non hai ancora creato nessun viaggio. <a href="#" id="link-new-travel">Crea il tuo primo viaggio!</a></p>
            <?php endif; ?>
        </div>

        <!-- Tab: Richieste di Partecipazione -->
        <div class="tab-content" id="tab-requests">
            <h2>Richieste di Partecipazione</h2>

            <?php if (!empty($pending_requests)) : ?>
                <div class="requests-list">
                    <?php foreach ($pending_requests as $request) : ?>
                        <div class="request-item" data-request-id="<?php echo $request->id; ?>">
                            <div class="request-user">
                                <?php echo get_avatar($request->user_id, 60); ?>
                                <div class="request-user-info">
                                    <h4>
                                        <a href="<?php echo CDV_User_Profiles::get_profile_url($request->user_id); ?>" target="_blank">
                                            <?php echo esc_html($request->display_name); ?>
                                        </a>
                                    </h4>
                                    <p class="request-travel">Viaggio: <strong><?php echo esc_html($request->post_title); ?></strong></p>
                                    <p class="request-date">
                                        <i class="icon-clock"></i>
                                        <?php echo human_time_diff(strtotime($request->created_at), current_time('timestamp')); ?> fa
                                    </p>
                                    <?php if (!empty($request->message)) : ?>
                                        <p class="request-message">"<?php echo esc_html($request->message); ?>"</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="request-actions">
                                <button class="btn btn-success approve-request" data-request-id="<?php echo $request->id; ?>" data-travel-id="<?php echo $request->travel_id; ?>" data-user-id="<?php echo $request->user_id; ?>">
                                    <i class="icon-check"></i> Approva
                                </button>
                                <button class="btn btn-danger reject-request" data-request-id="<?php echo $request->id; ?>" data-travel-id="<?php echo $request->travel_id; ?>" data-user-id="<?php echo $request->user_id; ?>">
                                    <i class="icon-x"></i> Rifiuta
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <p class="no-content">Nessuna richiesta di partecipazione in attesa.</p>
            <?php endif; ?>
        </div>

        <!-- Tab: Viaggi a cui Partecipo -->
        <div class="tab-content" id="tab-participating">
            <h2>Viaggi a cui Partecipo</h2>

            <?php if ($participated_travels && $participated_travels->have_posts()) : ?>
                <div class="travels-grid">
                    <?php while ($participated_travels->have_posts()) : $participated_travels->the_post(); ?>
                        <?php get_template_part('template-parts/content', 'travel-card'); ?>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                </div>
            <?php else : ?>
                <p class="no-content">Non stai partecipando a nessun viaggio. <a href="<?php echo get_post_type_archive_link('viaggio'); ?>">Cerca un viaggio!</a></p>
            <?php endif; ?>
        </div>

        <!-- Tab: Impostazioni -->
        <div class="tab-content" id="tab-settings">
            <h2>Impostazioni Profilo</h2>

            <!-- Edit Profile Form -->
            <div class="settings-section">
                <h3>Informazioni Personali</h3>
                <form id="edit-profile-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_display_name">Nome e Cognome</label>
                            <input type="text" id="edit_display_name" name="display_name" value="<?php echo esc_attr($current_user->display_name); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_email">Email</label>
                            <input type="email" id="edit_email" name="email" value="<?php echo esc_attr($current_user->user_email); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_city">Città</label>
                            <input type="text" id="edit_city" name="city" value="<?php echo esc_attr(get_user_meta($current_user->ID, 'cdv_city', true)); ?>">
                        </div>
                        <div class="form-group">
                            <label for="edit_phone">Telefono</label>
                            <input type="tel" id="edit_phone" name="phone" value="<?php echo esc_attr(get_user_meta($current_user->ID, 'cdv_phone', true)); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_bio">Bio</label>
                        <textarea id="edit_bio" name="bio" rows="4"><?php echo esc_textarea(get_user_meta($current_user->ID, 'cdv_bio', true)); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Salva Modifiche</button>
                </form>
            </div>

            <!-- Change Password -->
            <div class="settings-section">
                <h3>Cambia Password</h3>
                <form id="change-password-form">
                    <div class="form-group">
                        <label for="current_password">Password Attuale</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">Nuova Password</label>
                        <input type="password" id="new_password" name="new_password" required minlength="8">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Conferma Nuova Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Cambia Password</button>
                </form>
            </div>

            <!-- Delete Account -->
            <div class="settings-section danger-zone">
                <h3>Zona Pericolosa</h3>
                <p><strong>Elimina Account</strong> - Questa azione è irreversibile. Tutti i tuoi dati, viaggi e messaggi saranno eliminati permanentemente.</p>
                <button type="button" class="btn btn-danger" id="delete-account-btn">Elimina Account</button>
            </div>
        </div>
    </div>
</main>

<style>
.dashboard {
    padding: 2rem 0;
    background: #f8f9fa;
    min-height: calc(100vh - 200px);
}

.dashboard-header {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.dashboard-header h1 {
    margin: 0 0 0.5rem 0;
}

.dashboard-header p {
    margin: 0;
    color: #666;
}

.dashboard-tabs {
    display: flex;
    gap: 1rem;
    border-bottom: 2px solid #e0e0e0;
    margin-bottom: 2rem;
    background: white;
    padding: 0 2rem;
    border-radius: 12px 12px 0 0;
}

.tab-button {
    padding: 1rem 1.5rem;
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    font-size: 1rem;
    color: #666;
    transition: all 0.3s;
    position: relative;
}

.tab-button.active {
    color: var(--primary-color);
    border-bottom-color: var(--primary-color);
}

.badge-count {
    background: #dc3545;
    color: white;
    padding: 0.125rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    margin-left: 0.5rem;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.section-header h2 {
    margin: 0;
}

.travels-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.travel-item {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 2rem;
}

.travel-item-header {
    display: flex;
    gap: 1rem;
    flex: 1;
    align-items: center;
}

.travel-thumb img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
}

.travel-item-info {
    flex: 1;
}

.travel-item-info h3 {
    margin: 0 0 0.5rem 0;
}

.travel-item-info h3 a {
    color: #333;
    text-decoration: none;
}

.travel-item-info h3 a:hover {
    color: var(--primary-color);
}

.travel-meta {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    align-items: center;
    margin: 0;
    font-size: 0.875rem;
    color: #666;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: bold;
}

.status-publish {
    background: #d4edda;
    color: #155724;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-draft {
    background: #e2e3e5;
    color: #383d41;
}

.travel-status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: bold;
}

.travel-open {
    background: #d4edda;
    color: #155724;
}

.travel-full {
    background: #fff3cd;
    color: #856404;
}

.travel-closed, .travel-completed {
    background: #e2e3e5;
    color: #383d41;
}

.pending-requests {
    color: #dc3545;
    font-weight: bold;
}

.travel-item-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.travel-status-select {
    padding: 0.5rem 1rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 0.875rem;
    cursor: pointer;
}

.requests-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.request-item {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 2rem;
}

.request-user {
    display: flex;
    gap: 1rem;
    flex: 1;
}

.request-user img {
    border-radius: 50%;
}

.request-user-info h4 {
    margin: 0 0 0.25rem 0;
}

.request-user-info h4 a {
    color: #333;
    text-decoration: none;
}

.request-user-info h4 a:hover {
    color: var(--primary-color);
}

.request-user-info p {
    margin: 0.25rem 0;
    font-size: 0.875rem;
    color: #666;
}

.request-message {
    font-style: italic;
    background: #f8f9fa;
    padding: 0.5rem;
    border-radius: 4px;
    margin-top: 0.5rem !important;
}

.request-actions {
    display: flex;
    gap: 0.5rem;
}

.travels-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
}

.settings-section {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.no-content {
    background: white;
    padding: 3rem;
    border-radius: 12px;
    text-align: center;
    color: #999;
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .dashboard-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .travel-item, .request-item {
        flex-direction: column;
        align-items: flex-start;
    }

    .travel-item-actions, .request-actions {
        width: 100%;
        justify-content: flex-end;
    }
}

/* Settings Forms */
.settings-section {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.settings-section h3 {
    margin-bottom: 1.5rem;
    color: #2c3e50;
}

.settings-section .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.settings-section .form-group {
    margin-bottom: 1.5rem;
}

.settings-section label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #333;
}

.settings-section input[type="text"],
.settings-section input[type="email"],
.settings-section input[type="tel"],
.settings-section input[type="password"],
.settings-section textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
}

.settings-section textarea {
    resize: vertical;
    min-height: 100px;
}

.danger-zone {
    border: 2px solid #dc3545;
}

.danger-zone h3 {
    color: #dc3545;
}

.btn-danger {
    background: #dc3545;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 500;
}

.btn-danger:hover {
    background: #c82333;
}

@media (max-width: 768px) {
    .settings-section .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');

            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));

            this.classList.add('active');
            document.getElementById('tab-' + tabId).classList.add('active');
        });
    });

    // Change travel status
    document.querySelectorAll('.travel-status-select').forEach(select => {
        select.addEventListener('change', function() {
            const travelId = this.dataset.travelId;
            const newStatus = this.value;

            if (!confirm('Vuoi davvero cambiare lo stato di questo viaggio?')) {
                return;
            }

            jQuery.ajax({
                url: cdvAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cdv_change_travel_status',
                    travel_id: travelId,
                    status: newStatus,
                    nonce: cdvAjax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert('Stato del viaggio aggiornato con successo!');
                        location.reload();
                    } else {
                        alert('Errore: ' + response.data);
                    }
                }
            });
        });
    });

    // Delete travel
    document.querySelectorAll('.delete-travel').forEach(button => {
        button.addEventListener('click', function() {
            const travelId = this.dataset.travelId;

            if (!confirm('Sei sicuro di voler eliminare questo viaggio? Questa azione non può essere annullata.')) {
                return;
            }

            jQuery.ajax({
                url: cdvAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cdv_delete_travel',
                    travel_id: travelId,
                    nonce: cdvAjax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert('Viaggio eliminato con successo!');
                        location.reload();
                    } else {
                        alert('Errore: ' + response.data);
                    }
                }
            });
        });
    });

    // Approve request
    document.querySelectorAll('.approve-request').forEach(button => {
        button.addEventListener('click', function() {
            const requestId = this.dataset.requestId;
            const travelId = this.dataset.travelId;
            const userId = this.dataset.userId;

            jQuery.ajax({
                url: cdvAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cdv_approve_participant',
                    travel_id: travelId,
                    user_id: userId,
                    nonce: cdvAjax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert('Richiesta approvata!');
                        location.reload();
                    } else {
                        alert('Errore: ' + response.data);
                    }
                }
            });
        });
    });

    // Reject request
    document.querySelectorAll('.reject-request').forEach(button => {
        button.addEventListener('click', function() {
            const requestId = this.dataset.requestId;
            const travelId = this.dataset.travelId;
            const userId = this.dataset.userId;

            if (!confirm('Sei sicuro di voler rifiutare questa richiesta?')) {
                return;
            }

            jQuery.ajax({
                url: cdvAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cdv_reject_participant',
                    travel_id: travelId,
                    user_id: userId,
                    nonce: cdvAjax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert('Richiesta rifiutata.');
                        location.reload();
                    } else {
                        alert('Errore: ' + response.data);
                    }
                }
            });
        });
    });

    // New travel button
    document.getElementById('btn-new-travel')?.addEventListener('click', function(e) {
        e.preventDefault();
        alert('Funzionalità in arrivo: form per creare nuovo viaggio');
    });

    document.getElementById('link-new-travel')?.addEventListener('click', function(e) {
        e.preventDefault();
        alert('Funzionalità in arrivo: form per creare nuovo viaggio');
    });

    // Edit Profile Form
    document.getElementById('edit-profile-form')?.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('action', 'cdv_update_profile');
        formData.append('nonce', cdvAjax.nonce);

        jQuery.ajax({
            url: cdvAjax.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert('Profilo aggiornato con successo!');
                    location.reload();
                } else {
                    alert(response.data.message || 'Errore durante l\'aggiornamento');
                }
            },
            error: function() {
                alert('Errore di connessione');
            }
        });
    });

    // Change Password Form
    document.getElementById('change-password-form')?.addEventListener('submit', function(e) {
        e.preventDefault();

        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        if (newPassword !== confirmPassword) {
            alert('Le nuove password non coincidono');
            return;
        }

        const formData = new FormData(this);
        formData.append('action', 'cdv_change_password');
        formData.append('nonce', cdvAjax.nonce);

        jQuery.ajax({
            url: cdvAjax.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert('Password cambiata con successo!');
                    document.getElementById('change-password-form').reset();
                } else {
                    alert(response.data.message || 'Errore durante il cambio password');
                }
            },
            error: function() {
                alert('Errore di connessione');
            }
        });
    });

    // Delete Account
    document.getElementById('delete-account-btn')?.addEventListener('click', function() {
        const confirmed = confirm('SEI SICURO? Questa azione è IRREVERSIBILE. Tutti i tuoi dati saranno eliminati permanentemente.');

        if (!confirmed) return;

        const doubleConfirm = prompt('Scrivi "ELIMINA" per confermare:');

        if (doubleConfirm !== 'ELIMINA') {
            alert('Eliminazione annullata');
            return;
        }

        jQuery.ajax({
            url: cdvAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'cdv_delete_account',
                nonce: cdvAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert('Account eliminato. Arrivederci!');
                    window.location.href = '<?php echo home_url(); ?>';
                } else {
                    alert(response.data.message || 'Errore durante l\'eliminazione');
                }
            },
            error: function() {
                alert('Errore di connessione');
            }
        });
    });
});
</script>

<?php get_footer(); ?>
