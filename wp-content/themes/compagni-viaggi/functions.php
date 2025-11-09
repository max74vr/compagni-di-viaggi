<?php
/**
 * Compagni di Viaggi Theme Functions
 */

if (!defined('ABSPATH')) {
    exit;
}

// Theme version
define('CDV_THEME_VERSION', '1.0.0');

/**
 * Theme setup
 */
function cdv_theme_setup() {
    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails on posts and pages
    add_theme_support('post-thumbnails');
    set_post_thumbnail_size(800, 450, true);
    add_image_size('travel-card', 400, 300, true);
    add_image_size('travel-hero', 1200, 600, true);

    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Menu Principale', 'compagni-viaggi'),
        'mobile' => __('Menu Mobile', 'compagni-viaggi'),
        'footer' => __('Menu Footer', 'compagni-viaggi'),
    ));

    // Switch default core markup to output valid HTML5
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));

    // Add theme support for selective refresh for widgets
    add_theme_support('customize-selective-refresh-widgets');

    // Add support for editor styles
    add_theme_support('editor-styles');

    // Add support for responsive embeds
    add_theme_support('responsive-embeds');

    // Add support for custom logo
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
        'header-text' => array('site-name', 'site-description'),
    ));
}
add_action('after_setup_theme', 'cdv_theme_setup');

/**
 * Enqueue scripts and styles
 */
function cdv_enqueue_scripts() {
    // Google Fonts
    wp_enqueue_style('cdv-google-fonts', 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap', array(), null);

    // Theme stylesheet
    wp_enqueue_style('cdv-style', get_stylesheet_uri(), array(), CDV_THEME_VERSION);

    // Main JavaScript
    wp_enqueue_script('cdv-main', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), CDV_THEME_VERSION, true);

    // Localize script for AJAX
    wp_localize_script('cdv-main', 'cdvAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('cdv_ajax_nonce'),
        'restUrl' => rest_url('cdv/v1/'),
    ));

    // Comment reply script
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'cdv_enqueue_scripts');

/**
 * Register widget areas
 */
function cdv_widgets_init() {
    register_sidebar(array(
        'name'          => __('Sidebar', 'compagni-viaggi'),
        'id'            => 'sidebar-1',
        'description'   => __('Aggiungi widget qui per apparire nella sidebar.', 'compagni-viaggi'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    register_sidebar(array(
        'name'          => __('Footer 1', 'compagni-viaggi'),
        'id'            => 'footer-1',
        'description'   => __('Prima colonna del footer.', 'compagni-viaggi'),
        'before_widget' => '<div class="footer-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>',
    ));

    register_sidebar(array(
        'name'          => __('Footer 2', 'compagni-viaggi'),
        'id'            => 'footer-2',
        'description'   => __('Seconda colonna del footer.', 'compagni-viaggi'),
        'before_widget' => '<div class="footer-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>',
    ));

    register_sidebar(array(
        'name'          => __('Footer 3', 'compagni-viaggi'),
        'id'            => 'footer-3',
        'description'   => __('Terza colonna del footer.', 'compagni-viaggi'),
        'before_widget' => '<div class="footer-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'cdv_widgets_init');

/**
 * Custom template tags
 */

/**
 * Display travel meta information
 */
function cdv_travel_meta($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $start_date = get_post_meta($post_id, 'cdv_start_date', true);
    $end_date = get_post_meta($post_id, 'cdv_end_date', true);
    $destination = get_post_meta($post_id, 'cdv_destination', true);
    $country = get_post_meta($post_id, 'cdv_country', true);
    $budget = get_post_meta($post_id, 'cdv_budget', true);
    $max_participants = get_post_meta($post_id, 'cdv_max_participants', true);
    $current_participants = CDV_Participants::get_participant_count($post_id);

    ?>
    <div class="travel-meta">
        <?php if ($destination) : ?>
            <span class="meta-item">
                <span class="icon">📍</span>
                <?php echo esc_html($destination); ?><?php echo $country ? ', ' . esc_html($country) : ''; ?>
            </span>
        <?php endif; ?>

        <?php if ($start_date) : ?>
            <span class="meta-item">
                <span class="icon">📅</span>
                <?php echo date_i18n('d/m/Y', strtotime($start_date)); ?>
                <?php if ($end_date) echo ' - ' . date_i18n('d/m/Y', strtotime($end_date)); ?>
            </span>
        <?php endif; ?>

        <?php if ($budget) : ?>
            <span class="meta-item">
                <span class="icon">💰</span>
                €<?php echo number_format($budget, 0, ',', '.'); ?>
            </span>
        <?php endif; ?>

        <?php if ($max_participants) : ?>
            <span class="meta-item">
                <span class="icon">👥</span>
                <?php echo $current_participants; ?>/<?php echo $max_participants; ?> partecipanti
            </span>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Display organizer info
 */
function cdv_organizer_info($author_id = null) {
    if (!$author_id) {
        $author_id = get_the_author_meta('ID');
    }

    $author = get_user_by('id', $author_id);
    $reputation = get_user_meta($author_id, 'cdv_reputation_score', true);
    $verified = get_user_meta($author_id, 'cdv_verified', true);

    $profile_url = CDV_User_Profiles::get_profile_url($author_id);
    ?>
    <a href="<?php echo esc_url($profile_url); ?>" class="organizer-info">
        <?php echo get_avatar($author_id, 40, '', '', array('class' => 'organizer-avatar')); ?>
        <div class="organizer-details">
            <div class="organizer-name">
                <?php echo esc_html($author->user_login); ?>
                <?php if ($verified === '1') : ?>
                    <span class="verified-badge" title="Verificato">✓</span>
                <?php endif; ?>
            </div>
            <?php if ($reputation) : ?>
                <div class="organizer-reputation">
                    <?php cdv_display_stars($reputation); ?>
                </div>
            <?php endif; ?>
        </div>
    </a>
    <?php
}

/**
 * Display star rating
 */
function cdv_display_stars($rating, $max = 5) {
    $rating = round($rating * 2) / 2; // Round to nearest 0.5
    $full_stars = floor($rating);
    $half_star = ($rating - $full_stars) >= 0.5;
    $empty_stars = $max - $full_stars - ($half_star ? 1 : 0);

    echo '<div class="star-rating">';

    for ($i = 0; $i < $full_stars; $i++) {
        echo '<span class="star full">★</span>';
    }

    if ($half_star) {
        echo '<span class="star half">★</span>';
    }

    for ($i = 0; $i < $empty_stars; $i++) {
        echo '<span class="star empty">☆</span>';
    }

    echo '<span class="rating-value">(' . number_format($rating, 1) . ')</span>';
    echo '</div>';
}

/**
 * Display travel type badges
 */
function cdv_travel_type_badges($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $types = wp_get_post_terms($post_id, 'tipo_viaggio');

    if (empty($types) || is_wp_error($types)) {
        return;
    }

    echo '<div class="travel-types">';
    foreach ($types as $type) {
        echo '<span class="badge badge-primary">' . esc_html($type->name) . '</span>';
    }
    echo '</div>';
}

/**
 * Get travel status label
 */
function cdv_get_travel_status_label($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $status = get_post_meta($post_id, 'cdv_travel_status', true);

    $labels = array(
        'open' => array('label' => 'Aperto', 'class' => 'success'),
        'full' => array('label' => 'Completo', 'class' => 'warning'),
        'in_progress' => array('label' => 'In Corso', 'class' => 'info'),
        'completed' => array('label' => 'Completato', 'class' => 'secondary'),
        'cancelled' => array('label' => 'Annullato', 'class' => 'error'),
    );

    if (empty($status)) {
        $status = 'open';
    }

    if (isset($labels[$status])) {
        return '<span class="badge badge-' . $labels[$status]['class'] . '">' . $labels[$status]['label'] . '</span>';
    }

    return '';
}

/**
 * Pagination
 */
function cdv_pagination() {
    the_posts_pagination(array(
        'mid_size' => 2,
        'prev_text' => __('« Precedente', 'compagni-viaggi'),
        'next_text' => __('Successivo »', 'compagni-viaggi'),
    ));
}

/**
 * Custom excerpt length
 */
function cdv_excerpt_length($length) {
    return 30;
}
add_filter('excerpt_length', 'cdv_excerpt_length');

/**
 * Custom excerpt more
 */
function cdv_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'cdv_excerpt_more');

/**
 * Add body classes
 */
function cdv_body_classes($classes) {
    if (is_user_logged_in()) {
        $classes[] = 'logged-in';
    } else {
        $classes[] = 'logged-out';
    }

    if (is_post_type_archive('viaggio') || is_singular('viaggio')) {
        $classes[] = 'viaggio-page';
    }

    return $classes;
}
add_filter('body_class', 'cdv_body_classes');

/**
 * Calculate reading time for post
 */
function cdv_reading_time($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $content = get_post_field('post_content', $post_id);
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // Average reading speed: 200 words per minute

    return max($reading_time, 1); // Minimum 1 minute
}

/**
 * Include Theme Customizer
 */
require_once get_template_directory() . '/inc/customizer.php';

/**
 * Add Custom CSS meta box for pages
 */
function cdv_add_custom_css_meta_box() {
    add_meta_box(
        'cdv_custom_css',
        'CSS Personalizzato',
        'cdv_custom_css_meta_box_callback',
        array('page', 'post', 'viaggio'),
        'normal',
        'low'
    );
}
add_action('add_meta_boxes', 'cdv_add_custom_css_meta_box');

/**
 * Custom CSS meta box callback
 */
function cdv_custom_css_meta_box_callback($post) {
    wp_nonce_field('cdv_custom_css_nonce', 'cdv_custom_css_nonce_field');
    $custom_css = get_post_meta($post->ID, '_cdv_custom_css', true);
    ?>
    <p>
        <label for="cdv_custom_css"><strong>CSS per questa pagina:</strong></label>
        <small style="display: block; margin-top: 5px; color: #666;">
            Non è necessario aggiungere i tag &lt;style&gt;. Il CSS verrà applicato solo a questa pagina.
        </small>
    </p>
    <textarea id="cdv_custom_css" name="cdv_custom_css" rows="10" style="width: 100%; font-family: monospace; font-size: 13px;"><?php echo esc_textarea($custom_css); ?></textarea>
    <?php
}

/**
 * Save Custom CSS meta box
 */
function cdv_save_custom_css_meta_box($post_id) {
    // Check nonce
    if (!isset($_POST['cdv_custom_css_nonce_field']) ||
        !wp_verify_nonce($_POST['cdv_custom_css_nonce_field'], 'cdv_custom_css_nonce')) {
        return;
    }

    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save CSS
    if (isset($_POST['cdv_custom_css'])) {
        update_post_meta($post_id, '_cdv_custom_css', wp_kses_post($_POST['cdv_custom_css']));
    }
}
add_action('save_post', 'cdv_save_custom_css_meta_box');

/**
 * Output custom CSS for current page
 */
function cdv_output_page_custom_css() {
    if (is_singular()) {
        $custom_css = get_post_meta(get_the_ID(), '_cdv_custom_css', true);
        if (!empty($custom_css)) {
            echo '<style type="text/css" id="page-custom-css">' . wp_kses_post($custom_css) . '</style>';
        }
    }
}
add_action('wp_head', 'cdv_output_page_custom_css', 100);
