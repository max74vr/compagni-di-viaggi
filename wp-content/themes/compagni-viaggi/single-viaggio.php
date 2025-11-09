<?php
/**
 * Single Viaggio Template
 */

get_header();

while (have_posts()) : the_post();
    $travel_id = get_the_ID();
    $author_id = get_the_author_meta('ID');
    $is_organizer = is_user_logged_in() && get_current_user_id() == $author_id;
    $is_participant = is_user_logged_in() && CDV_Participants::is_participant($travel_id, get_current_user_id(), 'accepted');
    $has_requested = is_user_logged_in() && CDV_Participants::is_participant($travel_id, get_current_user_id(), 'pending');
    $participants = CDV_Participants::get_participants($travel_id, 'accepted');
    $pending_requests = CDV_Participants::get_participants($travel_id, 'pending');
    ?>

    <main class="site-main single-travel">
        <!-- Hero Image -->
        <?php if (has_post_thumbnail()) : ?>
            <div class="travel-hero">
                <?php the_post_thumbnail('travel-hero'); ?>
            </div>
        <?php endif; ?>

        <div class="container">
            <div class="travel-layout">
                <!-- Main Content -->
                <article class="travel-content">
                    <header class="travel-header">
                        <div class="travel-badges">
                            <?php cdv_travel_type_badges(); ?>
                            <?php echo cdv_get_travel_status_label(); ?>
                        </div>

                        <h1><?php the_title(); ?></h1>

                        <?php cdv_travel_meta(); ?>
                    </header>

                    <div class="travel-description">
                        <?php the_content(); ?>
                    </div>

                    <!-- Participants Section -->
                    <?php if (!empty($participants)) : ?>
                        <div class="participants-section">
                            <h3>Partecipanti (<?php echo count($participants); ?>)</h3>
                            <div class="participants-grid">
                                <!-- Organizer First -->
                                <div class="participant-card-wrapper">
                                    <a href="<?php echo esc_url(CDV_User_Profiles::get_profile_url($author_id)); ?>" class="participant-card organizer">
                                        <?php echo get_avatar($author_id, 80); ?>
                                        <div class="participant-info">
                                            <div class="participant-name">
                                                <?php echo esc_html(get_the_author_meta('user_login', $author_id)); ?>
                                                <span class="organizer-badge">Organizzatore</span>
                                            </div>
                                            <?php
                                            $reputation = get_user_meta($author_id, 'cdv_reputation_score', true);
                                            if ($reputation) {
                                                cdv_display_stars($reputation);
                                            }
                                            ?>
                                        </div>
                                    </a>
                                    <?php if (is_user_logged_in() && get_current_user_id() != $author_id && ($is_participant || $is_organizer)) : ?>
                                        <a href="<?php echo home_url('/dashboard?tab=messages&user_id=' . $author_id . '&travel_id=' . $travel_id); ?>" class="btn btn-sm btn-primary participant-message-btn">
                                            Invia Messaggio
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <!-- Other Participants -->
                                <?php foreach ($participants as $participant) :
                                    $user = get_user_by('id', $participant->user_id);
                                    $reputation = get_user_meta($user->ID, 'cdv_reputation_score', true);
                                    ?>
                                    <div class="participant-card-wrapper">
                                        <a href="<?php echo esc_url(CDV_User_Profiles::get_profile_url($user->ID)); ?>" class="participant-card">
                                            <?php echo get_avatar($user->ID, 80); ?>
                                            <div class="participant-info">
                                                <div class="participant-name"><?php echo esc_html($user->user_login); ?></div>
                                                <?php if ($reputation) {
                                                    cdv_display_stars($reputation);
                                                } ?>
                                            </div>
                                        </a>
                                        <?php if (is_user_logged_in() && get_current_user_id() != $user->ID && ($is_participant || $is_organizer)) : ?>
                                            <a href="<?php echo home_url('/dashboard?tab=messages&user_id=' . $user->ID . '&travel_id=' . $travel_id); ?>" class="btn btn-sm btn-primary participant-message-btn">
                                                Invia Messaggio
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Pending Requests (only for organizer) -->
                    <?php if ($is_organizer && !empty($pending_requests)) : ?>
                        <div class="pending-requests-section">
                            <h3>Richieste in Attesa (<?php echo count($pending_requests); ?>)</h3>
                            <div class="requests-list">
                                <?php foreach ($pending_requests as $request) :
                                    $user = get_user_by('id', $request->user_id);
                                    ?>
                                    <div class="request-card" data-user-id="<?php echo $user->ID; ?>">
                                        <?php echo get_avatar($user->ID, 60); ?>
                                        <div class="request-info">
                                            <div class="request-name"><?php echo esc_html($user->user_login); ?></div>
                                            <?php if ($request->message) : ?>
                                                <div class="request-message"><?php echo esc_html($request->message); ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="request-actions">
                                            <button class="btn-success btn-accept" data-travel-id="<?php echo $travel_id; ?>" data-user-id="<?php echo $user->ID; ?>">
                                                Accetta
                                            </button>
                                            <button class="btn-danger btn-reject" data-travel-id="<?php echo $travel_id; ?>" data-user-id="<?php echo $user->ID; ?>">
                                                Rifiuta
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </article>

                <!-- Sidebar -->
                <aside class="travel-sidebar">
                    <!-- Organizer Card -->
                    <div class="sidebar-card organizer-card">
                        <h3>Organizzatore</h3>
                        <?php
                        $verified = get_user_meta($author_id, 'cdv_verified', true);
                        $reputation = get_user_meta($author_id, 'cdv_reputation_score', true);
                        $bio = get_user_meta($author_id, 'cdv_bio', true);
                        ?>
                        <a href="<?php echo esc_url(CDV_User_Profiles::get_profile_url($author_id)); ?>" class="organizer-profile">
                            <?php echo get_avatar($author_id, 100); ?>
                            <div class="organizer-name">
                                <?php echo esc_html(get_the_author_meta('user_login', $author_id)); ?>
                                <?php if ($verified === '1') : ?>
                                    <span class="verified-badge" title="Verificato">✓</span>
                                <?php endif; ?>
                            </div>
                            <?php if ($reputation) {
                                cdv_display_stars($reputation);
                            } ?>
                            <?php if ($bio) : ?>
                                <p class="organizer-bio"><?php echo esc_html($bio); ?></p>
                            <?php endif; ?>
                        </a>
                    </div>

                    <!-- Join Card -->
                    <?php if (is_user_logged_in()) : ?>
                        <?php if ($is_organizer) : ?>
                            <div class="sidebar-card">
                                <p><strong>Questo è il tuo viaggio!</strong></p>
                                <a href="<?php echo get_edit_post_link(); ?>" class="btn-primary" style="width: 100%; text-align: center;">
                                    Modifica Viaggio
                                </a>
                            </div>
                        <?php elseif ($is_participant) : ?>
                            <div class="sidebar-card success-card">
                                <p><strong>✓ Sei un partecipante</strong></p>
                                <p>Hai accesso alla chat di gruppo</p>
                            </div>
                        <?php elseif ($has_requested) : ?>
                            <div class="sidebar-card warning-card">
                                <p><strong>⏳ Richiesta in attesa</strong></p>
                                <p>La tua richiesta è in attesa di approvazione</p>
                            </div>
                        <?php else : ?>
                            <div class="sidebar-card join-card">
                                <h3>Partecipa al Viaggio</h3>
                                <form id="join-travel-form">
                                    <div class="form-group">
                                        <label for="join-message">Messaggio per l'organizzatore</label>
                                        <textarea id="join-message" rows="4" placeholder="Presentati e spiega perché vuoi unirti..."></textarea>
                                    </div>
                                    <button type="submit" class="btn-primary" style="width: 100%;">
                                        Richiedi di Partecipare
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    <?php else : ?>
                        <div class="sidebar-card">
                            <h3>Vuoi partecipare?</h3>
                            <p>Accedi o registrati per unirti a questo viaggio</p>
                            <a href="<?php echo wp_login_url(get_permalink()); ?>" class="btn-primary" style="width: 100%; text-align: center; margin-bottom: 10px;">
                                Accedi
                            </a>
                            <a href="<?php echo wp_registration_url(); ?>" class="btn-secondary" style="width: 100%; text-align: center;">
                                Registrati
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- Travel Details -->
                    <div class="sidebar-card">
                        <h3>Dettagli Viaggio</h3>
                        <div class="travel-details-list">
                            <?php
                            $start_date = get_post_meta($travel_id, 'cdv_start_date', true);
                            $end_date = get_post_meta($travel_id, 'cdv_end_date', true);
                            $destination = get_post_meta($travel_id, 'cdv_destination', true);
                            $country = get_post_meta($travel_id, 'cdv_country', true);
                            $budget = get_post_meta($travel_id, 'cdv_budget', true);
                            $max_participants = get_post_meta($travel_id, 'cdv_max_participants', true);
                            ?>

                            <?php if ($start_date) : ?>
                                <div class="detail-item">
                                    <strong>Data Inizio:</strong>
                                    <span><?php echo date_i18n('d F Y', strtotime($start_date)); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($end_date) : ?>
                                <div class="detail-item">
                                    <strong>Data Fine:</strong>
                                    <span><?php echo date_i18n('d F Y', strtotime($end_date)); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($destination) : ?>
                                <div class="detail-item">
                                    <strong>Destinazione:</strong>
                                    <span><?php echo esc_html($destination); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($country) : ?>
                                <div class="detail-item">
                                    <strong>Paese:</strong>
                                    <span><?php echo esc_html($country); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($budget) : ?>
                                <div class="detail-item">
                                    <strong>Budget Stimato:</strong>
                                    <span>€<?php echo number_format($budget, 0, ',', '.'); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($max_participants) : ?>
                                <div class="detail-item">
                                    <strong>Partecipanti:</strong>
                                    <span><?php echo count($participants); ?>/<?php echo $max_participants; ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </main>

    <style>
        .travel-hero {
            width: 100%;
            height: 400px;
            overflow: hidden;
            margin-bottom: calc(var(--spacing-unit) * 4);
        }
        .travel-hero img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .travel-layout {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: calc(var(--spacing-unit) * 4);
            margin-bottom: calc(var(--spacing-unit) * 6);
        }
        .travel-header {
            margin-bottom: calc(var(--spacing-unit) * 4);
        }
        .travel-badges {
            display: flex;
            gap: calc(var(--spacing-unit) * 1);
            margin-bottom: calc(var(--spacing-unit) * 2);
        }
        .travel-description {
            margin-bottom: calc(var(--spacing-unit) * 4);
            line-height: 1.8;
        }
        .sidebar-card {
            background: white;
            padding: calc(var(--spacing-unit) * 3);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            margin-bottom: calc(var(--spacing-unit) * 3);
        }
        .sidebar-card h3 {
            margin-bottom: calc(var(--spacing-unit) * 2);
            padding-bottom: calc(var(--spacing-unit) * 2);
            border-bottom: 2px solid var(--primary-color);
        }
        .organizer-profile {
            text-align: center;
            display: block;
            text-decoration: none;
            color: inherit;
            transition: opacity 0.2s;
        }
        .organizer-profile:hover {
            opacity: 0.8;
        }
        .organizer-profile:hover .organizer-name {
            color: var(--primary-color);
        }
        .organizer-profile img {
            margin: 0 auto calc(var(--spacing-unit) * 2);
            border-radius: 50%;
        }
        .organizer-bio {
            margin-top: calc(var(--spacing-unit) * 2);
            color: var(--text-medium);
            font-size: 0.9rem;
        }
        .travel-details-list {
            display: flex;
            flex-direction: column;
            gap: calc(var(--spacing-unit) * 2);
        }
        .detail-item {
            display: flex;
            justify-content: space-between;
            padding-bottom: calc(var(--spacing-unit) * 2);
            border-bottom: 1px solid var(--border-color);
        }
        .detail-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .participants-section,
        .pending-requests-section {
            background: white;
            padding: calc(var(--spacing-unit) * 3);
            border-radius: var(--border-radius);
            margin-bottom: calc(var(--spacing-unit) * 3);
        }
        .participants-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: calc(var(--spacing-unit) * 2);
        }
        .participant-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: calc(var(--spacing-unit) * 2);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-sm);
            text-decoration: none;
            color: inherit;
            transition: all 0.2s;
        }
        .participant-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .participant-card:hover .participant-name {
            color: var(--primary-color);
        }
        .participant-card img {
            margin-bottom: calc(var(--spacing-unit) * 1.5);
            border-radius: 50%;
        }
        .organizer-badge {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 0.7rem;
            margin-left: 5px;
        }
        .request-card {
            display: flex;
            align-items: center;
            gap: calc(var(--spacing-unit) * 2);
            padding: calc(var(--spacing-unit) * 2);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-sm);
            margin-bottom: calc(var(--spacing-unit) * 2);
        }
        .request-info {
            flex: 1;
        }
        .request-name {
            font-weight: 600;
            margin-bottom: calc(var(--spacing-unit) * 0.5);
        }
        .request-message {
            color: var(--text-medium);
            font-size: 0.9rem;
        }
        .request-actions {
            display: flex;
            gap: calc(var(--spacing-unit) * 1);
        }
        .btn-success {
            background-color: var(--success-color);
            color: white;
            padding: calc(var(--spacing-unit) * 1) calc(var(--spacing-unit) * 2);
            border: none;
            border-radius: var(--border-radius-sm);
            cursor: pointer;
            font-weight: 500;
        }
        .btn-danger {
            background-color: var(--error-color);
            color: white;
            padding: calc(var(--spacing-unit) * 1) calc(var(--spacing-unit) * 2);
            border: none;
            border-radius: var(--border-radius-sm);
            cursor: pointer;
            font-weight: 500;
        }
        .success-card {
            background-color: #f0fdf4;
            border: 2px solid var(--success-color);
        }
        .warning-card {
            background-color: #fffbeb;
            border: 2px solid var(--warning-color);
        }
        @media (max-width: 768px) {
            .travel-layout {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script>
    jQuery(document).ready(function($) {
        // Join travel form
        $('#join-travel-form').on('submit', function(e) {
            e.preventDefault();

            var message = $('#join-message').val();

            $.ajax({
                url: cdvAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cdv_join_travel',
                    nonce: cdvAjax.nonce,
                    travel_id: <?php echo $travel_id; ?>,
                    message: message
                },
                success: function(response) {
                    if (response.success) {
                        alert('Richiesta inviata con successo!');
                        location.reload();
                    } else {
                        alert(response.data.message || 'Errore durante l\'invio della richiesta');
                    }
                },
                error: function() {
                    alert('Errore di connessione');
                }
            });
        });

        // Accept participant
        $('.btn-accept').on('click', function() {
            var btn = $(this);
            var travelId = btn.data('travel-id');
            var userId = btn.data('user-id');

            if (!confirm('Accettare questo partecipante?')) {
                return;
            }

            $.ajax({
                url: cdvAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cdv_accept_participant',
                    nonce: cdvAjax.nonce,
                    travel_id: travelId,
                    user_id: userId
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message || 'Errore');
                    }
                }
            });
        });

        // Reject participant
        $('.btn-reject').on('click', function() {
            var btn = $(this);
            var travelId = btn.data('travel-id');
            var userId = btn.data('user-id');

            if (!confirm('Rifiutare questo partecipante?')) {
                return;
            }

            $.ajax({
                url: cdvAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cdv_reject_participant',
                    nonce: cdvAjax.nonce,
                    travel_id: travelId,
                    user_id: userId
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message || 'Errore');
                    }
                }
            });
        });
    });
    </script>

    <?php
endwhile;

get_footer();
