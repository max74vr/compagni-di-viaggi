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

                    <!-- Photo Gallery -->
                    <?php
                    $gallery_images = CDV_Travel_Gallery::get_gallery_images($travel_id);
                    if (!empty($gallery_images)) :
                    ?>
                        <div class="travel-gallery-section">
                            <h3>📸 Galleria Fotografica (<?php echo count($gallery_images); ?> foto)</h3>
                            <div class="travel-gallery-grid">
                                <?php foreach ($gallery_images as $image) : ?>
                                    <div class="gallery-item" data-image-id="<?php echo $image['id']; ?>">
                                        <img src="<?php echo esc_url($image['medium']); ?>"
                                             alt="<?php echo esc_attr($image['alt'] ?: 'Foto di viaggio'); ?>"
                                             data-full="<?php echo esc_url($image['full']); ?>">
                                        <div class="gallery-item-overlay">
                                            <button class="gallery-view-btn" data-full-url="<?php echo esc_url($image['full']); ?>">
                                                <span>🔍</span> Visualizza
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <?php if ($is_organizer) : ?>
                                <div class="gallery-manage-link">
                                    <a href="#" id="manage-gallery-btn" class="btn btn-secondary">
                                        <span>📷</span> Gestisci Galleria
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php elseif ($is_organizer) : ?>
                        <div class="travel-gallery-section empty">
                            <div class="gallery-empty-state">
                                <p>📷 Nessuna foto ancora. Aggiungi foto per far vedere la bellezza di questo viaggio!</p>
                                <a href="#" id="add-first-photo-btn" class="btn btn-primary">
                                    Aggiungi Prime Foto
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Travel Map -->
                    <div class="travel-map-section">
                        <h3>📍 Posizione</h3>
                        <?php echo CDV_Travel_Maps::get_map_html($travel_id, '450px'); ?>
                        <?php
                        $destination = get_post_meta($travel_id, 'cdv_destination', true);
                        $country = get_post_meta($travel_id, 'cdv_country', true);
                        if ($destination || $country) :
                        ?>
                            <p class="map-location-text">
                                <strong>Destinazione:</strong> <?php echo esc_html($destination); ?><?php echo $country ? ', ' . esc_html($country) : ''; ?>
                            </p>
                        <?php endif; ?>
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

                    <!-- Group Chat (only for participants and organizer) -->
                    <?php if (is_user_logged_in() && ($is_participant || $is_organizer)) : ?>
                        <div class="group-chat-section">
                            <div class="group-chat-header">
                                <h3>Chat di Gruppo</h3>
                                <span class="participants-count" id="chat-participants-count">
                                    <?php echo count($participants) + 1; ?> partecipanti
                                </span>
                            </div>

                            <div class="group-chat-container">
                                <div class="group-chat-messages" id="group-chat-messages">
                                    <div class="loading-indicator">Caricamento messaggi...</div>
                                </div>

                                <div class="group-chat-input">
                                    <textarea
                                        id="group-message-input"
                                        placeholder="Scrivi un messaggio al gruppo..."
                                        rows="2"
                                    ></textarea>
                                    <button id="send-group-message" class="btn btn-primary">
                                        <span class="button-text">Invia</span>
                                        <span class="button-loading" style="display: none;">...</span>
                                    </button>
                                </div>
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

        <!-- Gallery Lightbox -->
        <div id="gallery-lightbox" class="gallery-lightbox">
            <div class="gallery-lightbox-content">
                <button class="gallery-lightbox-close">&times;</button>
                <img id="gallery-lightbox-image" class="gallery-lightbox-image" src="" alt="">
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
        /* Group Chat Styles */
        .group-chat-section {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            margin-top: calc(var(--spacing-unit) * 4);
        }
        .group-chat-header {
            background: var(--primary-color);
            color: white;
            padding: calc(var(--spacing-unit) * 2) calc(var(--spacing-unit) * 3);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .group-chat-header h3 {
            margin: 0;
            font-size: 1.25rem;
        }
        .participants-count {
            font-size: 0.875rem;
            opacity: 0.9;
        }
        .group-chat-container {
            padding: calc(var(--spacing-unit) * 3);
        }
        .group-chat-messages {
            background: #f8f9fa;
            border-radius: var(--border-radius);
            padding: calc(var(--spacing-unit) * 2);
            height: 400px;
            overflow-y: auto;
            margin-bottom: calc(var(--spacing-unit) * 2);
            display: flex;
            flex-direction: column;
            gap: calc(var(--spacing-unit) * 2);
        }
        .loading-indicator {
            text-align: center;
            color: #999;
            padding: calc(var(--spacing-unit) * 4);
        }
        .group-message {
            display: flex;
            gap: calc(var(--spacing-unit) * 1.5);
            animation: fadeInMessage 0.3s ease-in;
        }
        @keyframes fadeInMessage {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .group-message.own-message {
            flex-direction: row-reverse;
        }
        .group-message .avatar {
            flex-shrink: 0;
        }
        .group-message .avatar img {
            border-radius: 50%;
            width: 40px;
            height: 40px;
        }
        .message-bubble {
            background: white;
            padding: calc(var(--spacing-unit) * 1.5);
            border-radius: var(--border-radius);
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            max-width: 70%;
        }
        .group-message.own-message .message-bubble {
            background: var(--primary-color);
            color: white;
        }
        .message-user {
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: calc(var(--spacing-unit) * 0.5);
        }
        .group-message.own-message .message-user {
            text-align: right;
        }
        .message-text {
            margin-bottom: calc(var(--spacing-unit) * 0.5);
            line-height: 1.5;
        }
        .message-time {
            font-size: 0.75rem;
            opacity: 0.7;
        }
        .group-chat-input {
            display: flex;
            gap: calc(var(--spacing-unit) * 2);
        }
        .group-chat-input textarea {
            flex: 1;
            padding: calc(var(--spacing-unit) * 1.5);
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            resize: vertical;
            font-family: inherit;
            font-size: 1rem;
        }
        .group-chat-input textarea:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        .group-chat-input button {
            padding: calc(var(--spacing-unit) * 1.5) calc(var(--spacing-unit) * 3);
            white-space: nowrap;
        }
        .button-loading {
            display: inline-block;
            animation: pulse 1s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        @media (max-width: 768px) {
            .travel-layout {
                grid-template-columns: 1fr;
            }
            .group-chat-messages {
                height: 300px;
            }
            .message-bubble {
                max-width: 85%;
            }
        }

        /* Photo Gallery Styles */
        .travel-gallery-section {
            margin: 40px 0;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .travel-gallery-section h3 {
            margin-bottom: 25px;
            color: #2d3748;
            font-size: 1.5rem;
        }

        .travel-gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .gallery-item {
            position: relative;
            aspect-ratio: 4/3;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .gallery-item:hover {
            transform: scale(1.02);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .gallery-item-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .gallery-item:hover .gallery-item-overlay {
            opacity: 1;
        }

        .gallery-view-btn {
            background: white;
            color: #667eea;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .gallery-view-btn:hover {
            background: #667eea;
            color: white;
        }

        .gallery-empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .gallery-manage-link {
            text-align: center;
            margin-top: 20px;
        }

        /* Gallery Lightbox Modal */
        .gallery-lightbox {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.95);
        }

        .gallery-lightbox-content {
            position: relative;
            width: 90%;
            max-width: 1200px;
            margin: 50px auto;
            text-align: center;
        }

        .gallery-lightbox-image {
            max-width: 100%;
            max-height: 80vh;
            border-radius: 8px;
        }

        .gallery-lightbox-close {
            position: absolute;
            top: -40px;
            right: 0;
            color: white;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
            background: none;
            border: none;
        }

        .gallery-lightbox-close:hover {
            color: #ccc;
        }

        /* Travel Map Section */
        .travel-map-section {
            margin: 40px 0;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .travel-map-section h3 {
            margin-bottom: 20px;
            color: #2d3748;
            font-size: 1.5rem;
        }

        .map-location-text {
            margin-top: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
            color: #4a5568;
        }

        .map-placeholder {
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
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

        // === GROUP CHAT FUNCTIONALITY ===
        const $groupChatMessages = $('#group-chat-messages');
        const $groupMessageInput = $('#group-message-input');
        const $sendGroupMessageBtn = $('#send-group-message');
        const travelId = <?php echo $travel_id; ?>;
        let chatRefreshInterval = null;

        // Load group chat messages on page load
        if ($groupChatMessages.length > 0) {
            loadGroupMessages();

            // Refresh messages every 5 seconds
            chatRefreshInterval = setInterval(function() {
                loadGroupMessages(true); // true = silent refresh (no loading indicator)
            }, 5000);
        }

        // Send message
        $sendGroupMessageBtn.on('click', function() {
            sendGroupMessage();
        });

        // Send on Enter (without Shift)
        $groupMessageInput.on('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendGroupMessage();
            }
        });

        function loadGroupMessages(silent = false) {
            if (!silent) {
                $groupChatMessages.html('<div class="loading-indicator">Caricamento messaggi...</div>');
            }

            $.ajax({
                url: cdvAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cdv_get_group_messages',
                    nonce: cdvAjax.nonce,
                    travel_id: travelId
                },
                success: function(response) {
                    if (response.success) {
                        displayGroupMessages(response.data.messages);

                        // Update participants count
                        if (response.data.participants_count) {
                            $('#chat-participants-count').text(response.data.participants_count + ' partecipanti');
                        }
                    } else {
                        if (!silent) {
                            $groupChatMessages.html('<div class="loading-indicator" style="color: #dc3545;">Errore: ' + (response.data.message || 'Impossibile caricare i messaggi') + '</div>');
                        }
                    }
                },
                error: function() {
                    if (!silent) {
                        $groupChatMessages.html('<div class="loading-indicator" style="color: #dc3545;">Errore di connessione</div>');
                    }
                }
            });
        }

        function displayGroupMessages(messages) {
            if (!messages || messages.length === 0) {
                $groupChatMessages.html('<div class="loading-indicator">Nessun messaggio ancora. Inizia la conversazione!</div>');
                return;
            }

            // Save scroll position
            const wasAtBottom = $groupChatMessages[0].scrollHeight - $groupChatMessages.scrollTop() <= $groupChatMessages.outerHeight() + 50;

            let html = '';
            messages.forEach(function(msg) {
                const ownClass = msg.is_own ? 'own-message' : '';
                html += `
                    <div class="group-message ${ownClass}" data-message-id="${msg.id}">
                        <div class="avatar">${msg.avatar}</div>
                        <div class="message-bubble">
                            <div class="message-user">${msg.user_name}</div>
                            <div class="message-text">${msg.message}</div>
                            <div class="message-time">${msg.time_ago}</div>
                        </div>
                    </div>
                `;
            });

            $groupChatMessages.html(html);

            // Scroll to bottom if was already at bottom or if it's the first load
            if (wasAtBottom || $groupChatMessages.find('.group-message').length === messages.length) {
                scrollToBottom();
            }
        }

        function sendGroupMessage() {
            const message = $groupMessageInput.val().trim();

            if (!message) {
                alert('Scrivi un messaggio prima di inviare');
                return;
            }

            // Show loading state
            $sendGroupMessageBtn.find('.button-text').hide();
            $sendGroupMessageBtn.find('.button-loading').show();
            $sendGroupMessageBtn.prop('disabled', true);

            $.ajax({
                url: cdvAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cdv_send_group_message',
                    nonce: cdvAjax.nonce,
                    travel_id: travelId,
                    message: message
                },
                success: function(response) {
                    if (response.success) {
                        $groupMessageInput.val('');
                        loadGroupMessages();
                    } else {
                        alert('Errore: ' + (response.data.message || 'Impossibile inviare il messaggio'));
                    }
                },
                error: function() {
                    alert('Errore di connessione');
                },
                complete: function() {
                    // Hide loading state
                    $sendGroupMessageBtn.find('.button-text').show();
                    $sendGroupMessageBtn.find('.button-loading').hide();
                    $sendGroupMessageBtn.prop('disabled', false);
                    $groupMessageInput.focus();
                }
            });
        }

        function scrollToBottom() {
            $groupChatMessages.animate({
                scrollTop: $groupChatMessages[0].scrollHeight
            }, 300);
        }

        // Clean up interval on page unload
        $(window).on('beforeunload', function() {
            if (chatRefreshInterval) {
                clearInterval(chatRefreshInterval);
            }
        });

        // Gallery Lightbox
        const lightbox = $('#gallery-lightbox');
        const lightboxImage = $('#gallery-lightbox-image');

        $('.gallery-view-btn').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const fullUrl = $(this).data('full-url');
            lightboxImage.attr('src', fullUrl);
            lightbox.fadeIn(300);
        });

        $('.gallery-lightbox-close').on('click', function() {
            lightbox.fadeOut(300);
        });

        lightbox.on('click', function(e) {
            if (e.target === this) {
                lightbox.fadeOut(300);
            }
        });

        // Close lightbox with ESC key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && lightbox.is(':visible')) {
                lightbox.fadeOut(300);
            }
        });
    });
    </script>

    <?php
endwhile;

get_footer();
