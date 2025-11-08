<?php
/**
 * Template part for displaying travel cards
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('card'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <a href="<?php the_permalink(); ?>">
            <?php the_post_thumbnail('travel-card', array('class' => 'card-image')); ?>
        </a>
    <?php else : ?>
        <div class="card-image" style="background: linear-gradient(135deg, var(--primary-light), var(--secondary-light)); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
            ✈️
        </div>
    <?php endif; ?>

    <div class="card-content">
        <div class="card-header">
            <?php cdv_travel_type_badges(); ?>
            <?php echo cdv_get_travel_status_label(); ?>
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
</style>
