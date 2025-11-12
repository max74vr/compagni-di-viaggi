<?php
/**
 * Template part for displaying travel cards
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('card'); ?>>
    <?php
    $show_image = !is_front_page(); // Non mostrare immagine in home
    $has_thumbnail = has_post_thumbnail();
    $taxonomy_image_url = false;

    // PRIORITÀ: Prima cerca l'immagine del tipo di viaggio (taxonomy)
    if ($show_image && class_exists('CDV_Taxonomy_Images')) {
        $travel_types = wp_get_post_terms(get_the_ID(), 'tipo_viaggio', array('fields' => 'ids'));
        if (!empty($travel_types)) {
            // Ottieni immagine random se ci sono più tipi
            $taxonomy_image_url = CDV_Taxonomy_Images::get_random_term_image($travel_types, 'travel-card');
        }
    }
    ?>

    <?php if ($show_image && $taxonomy_image_url) : ?>
        <!-- Immagine dalla tassonomia tipo_viaggio (PRIORITARIA) -->
        <div class="card-image">
            <a href="<?php the_permalink(); ?>">
                <img src="<?php echo esc_url($taxonomy_image_url); ?>" alt="<?php the_title_attribute(); ?>" />
            </a>
            <?php if (is_user_logged_in()) : ?>
                <button class="wishlist-btn <?php echo CDV_Wishlist::is_in_wishlist(get_current_user_id(), get_the_ID()) ? 'active' : ''; ?>"
                        data-travel-id="<?php the_ID(); ?>"
                        title="<?php echo CDV_Wishlist::is_in_wishlist(get_current_user_id(), get_the_ID()) ? 'Rimuovi dalla wishlist' : 'Aggiungi alla wishlist'; ?>">
                    <span class="wishlist-icon"><?php echo CDV_Wishlist::is_in_wishlist(get_current_user_id(), get_the_ID()) ? '❤️' : '🤍'; ?></span>
                </button>
            <?php endif; ?>
        </div>
    <?php elseif ($show_image && $has_thumbnail) : ?>
        <!-- Fallback: Immagine caricata dall'utente (featured image) -->
        <div class="card-image">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('travel-card'); ?>
            </a>
            <?php if (is_user_logged_in()) : ?>
                <button class="wishlist-btn <?php echo CDV_Wishlist::is_in_wishlist(get_current_user_id(), get_the_ID()) ? 'active' : ''; ?>"
                        data-travel-id="<?php the_ID(); ?>"
                        title="<?php echo CDV_Wishlist::is_in_wishlist(get_current_user_id(), get_the_ID()) ? 'Rimuovi dalla wishlist' : 'Aggiungi alla wishlist'; ?>">
                    <span class="wishlist-icon"><?php echo CDV_Wishlist::is_in_wishlist(get_current_user_id(), get_the_ID()) ? '❤️' : '🤍'; ?></span>
                </button>
            <?php endif; ?>
        </div>
    <?php elseif ($show_image) : ?>
        <!-- Nessuna immagine disponibile -->
        <div class="card-image card-image-placeholder">
            <a href="<?php the_permalink(); ?>">
                <div class="placeholder-content">
                    <span class="placeholder-icon">✈️</span>
                    <span class="placeholder-text">Nessuna immagine</span>
                </div>
            </a>
            <?php if (is_user_logged_in()) : ?>
                <button class="wishlist-btn <?php echo CDV_Wishlist::is_in_wishlist(get_current_user_id(), get_the_ID()) ? 'active' : ''; ?>"
                        data-travel-id="<?php the_ID(); ?>"
                        title="<?php echo CDV_Wishlist::is_in_wishlist(get_current_user_id(), get_the_ID()) ? 'Rimuovi dalla wishlist' : 'Aggiungi alla wishlist'; ?>">
                    <span class="wishlist-icon"><?php echo CDV_Wishlist::is_in_wishlist(get_current_user_id(), get_the_ID()) ? '❤️' : '🤍'; ?></span>
                </button>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="card-content">
        <div class="card-header">
            <?php
            $is_expired = get_query_var('is_expired', false);
            if ($is_expired) :
            ?>
                <span class="badge badge-expired" style="background: #dc3545; color: white; padding: calc(var(--spacing-unit) * 0.5) calc(var(--spacing-unit) * 1.5); border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">
                    Scaduto
                </span>
            <?php endif; ?>
            <?php cdv_travel_type_badges(); ?>
            <?php if (!$is_expired) echo cdv_get_travel_status_label(); ?>
        </div>

        <h3 class="card-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>

        <?php cdv_travel_meta(); ?>

        <div class="card-excerpt">
            <?php the_excerpt(); ?>
        </div>

        <div class="card-footer">
            <?php cdv_organizer_info(get_the_author_meta('ID')); ?>

            <a href="<?php the_permalink(); ?>" class="travel-details-link">
                Vedi Dettagli →
            </a>
        </div>
    </div>
</article>

<style>
.card-image {
    width: 100%;
    aspect-ratio: 4/3;
    overflow: hidden;
    border-radius: 8px 8px 0 0;
    position: relative;
}

.card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.card:hover .card-image img {
    transform: scale(1.05);
}

.wishlist-btn {
    position: absolute;
    top: 12px;
    right: 12px;
    background: white;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
    z-index: 10;
}

.wishlist-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(0,0,0,0.25);
}

.wishlist-icon {
    font-size: 1.3rem;
    line-height: 1;
    transition: transform 0.2s ease;
}

.wishlist-btn:active .wishlist-icon {
    transform: scale(0.9);
}

.wishlist-btn.active .wishlist-icon {
    animation: heartBeat 0.5s ease;
}

@keyframes heartBeat {
    0%, 100% { transform: scale(1); }
    25% { transform: scale(1.3); }
    50% { transform: scale(1.1); }
    75% { transform: scale(1.2); }
}

.card-image-placeholder {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.card-image-placeholder a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    text-decoration: none;
}

.placeholder-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: calc(var(--spacing-unit) * 1);
    color: white;
    text-align: center;
}

.placeholder-icon {
    font-size: 4rem;
    opacity: 0.8;
}

.placeholder-text {
    font-size: 0.9rem;
    font-weight: 500;
    opacity: 0.9;
}

.card-header {
    display: flex;
    gap: calc(var(--spacing-unit) * 1);
    margin-bottom: calc(var(--spacing-unit) * 2);
    flex-wrap: wrap;
}

.travel-meta {
    display: flex;
    flex-direction: column;
    gap: calc(var(--spacing-unit) * 1);
    margin-bottom: calc(var(--spacing-unit) * 2);
    font-size: 0.9rem;
    color: var(--text-medium);
}

.meta-item {
    display: flex;
    align-items: center;
    gap: calc(var(--spacing-unit) * 1);
}

.meta-item .icon {
    font-size: 1.1rem;
}

.travel-types {
    display: flex;
    gap: calc(var(--spacing-unit) * 1);
    flex-wrap: wrap;
}

.btn-sm {
    padding: calc(var(--spacing-unit) * 1) calc(var(--spacing-unit) * 2);
    font-size: 0.9rem;
}

.verified-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 18px;
    height: 18px;
    background-color: var(--success-color);
    color: white;
    border-radius: 50%;
    font-size: 0.7rem;
    margin-left: calc(var(--spacing-unit) * 0.5);
}

.star-rating {
    display: flex;
    align-items: center;
    gap: 2px;
    font-size: 0.9rem;
}

.star {
    color: var(--warning-color);
}

.star.empty {
    color: var(--border-color);
}

.rating-value {
    margin-left: calc(var(--spacing-unit) * 0.5);
    color: var(--text-light);
    font-size: 0.85rem;
}

.organizer-details {
    display: flex;
    flex-direction: column;
    gap: calc(var(--spacing-unit) * 0.5);
}

.organizer-name {
    display: flex;
    align-items: center;
}

.organizer-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    text-decoration: none;
    color: inherit;
    transition: opacity 0.2s;
}

.organizer-info:hover {
    opacity: 0.8;
}

.organizer-info:hover .organizer-name {
    color: var(--primary-color);
}

.travel-details-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.travel-details-link:hover {
    color: var(--secondary-color);
    text-decoration: underline;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Wishlist toggle - delegate to handle dynamically loaded cards
    $(document).on('click', '.wishlist-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const $btn = $(this);
        const travelId = $btn.data('travel-id');
        const isActive = $btn.hasClass('active');

        $.ajax({
            url: cdvAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'cdv_toggle_wishlist',
                nonce: cdvAjax.nonce,
                travel_id: travelId
            },
            beforeSend: function() {
                $btn.prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    // Toggle icon and class
                    if (isActive) {
                        $btn.removeClass('active');
                        $btn.find('.wishlist-icon').text('🤍');
                        $btn.attr('title', 'Aggiungi alla wishlist');
                    } else {
                        $btn.addClass('active');
                        $btn.find('.wishlist-icon').text('❤️');
                        $btn.attr('title', 'Rimuovi dalla wishlist');
                    }

                    // Show brief feedback
                    const message = isActive ? 'Rimosso dalla wishlist' : 'Aggiunto alla wishlist';
                    if (typeof cdv_show_notification === 'function') {
                        cdv_show_notification(message, 'success');
                    }
                }
            },
            error: function() {
                alert('Errore durante l\'operazione. Riprova.');
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    });
});
</script>
