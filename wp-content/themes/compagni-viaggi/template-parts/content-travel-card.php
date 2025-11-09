<?php
/**
 * Template part for displaying travel cards
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('card'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <div class="card-image">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('travel-card'); ?>
            </a>
        </div>
    <?php else : ?>
        <div class="card-image card-image-placeholder">
            <a href="<?php the_permalink(); ?>">
                <div class="placeholder-content">
                    <span class="placeholder-icon">✈️</span>
                    <span class="placeholder-text">Nessuna immagine</span>
                </div>
            </a>
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

            <a href="<?php the_permalink(); ?>" class="btn-primary btn-sm">
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
</style>
