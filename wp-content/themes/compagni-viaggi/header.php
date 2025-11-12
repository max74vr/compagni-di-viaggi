<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="container">
        <div class="site-logo">
            <a href="<?php echo esc_url(home_url('/')); ?>">
                <?php
                if (has_custom_logo()) {
                    the_custom_logo();
                } else {
                    echo '<span class="logo-icon">✈️</span>';
                    echo '<span class="site-name">' . get_bloginfo('name') . '</span>';
                }
                ?>
            </a>
        </div>

        <button class="mobile-menu-toggle" aria-label="Toggle menu">☰</button>

        <nav class="main-nav desktop-only">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_class'     => 'nav-menu',
                'container'      => false,
                'fallback_cb'    => 'cdv_fallback_menu',
            ));
            ?>
        </nav>

        <nav class="mobile-nav">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'mobile',
                'menu_class'     => 'mobile-menu',
                'container'      => false,
                'fallback_cb'    => 'cdv_fallback_mobile_menu',
            ));
            ?>
        </nav>

        <div class="header-actions desktop-only">
            <?php if (is_user_logged_in()) : ?>
                <a href="<?php echo esc_url(home_url('/dashboard?tab=notifications')); ?>" class="btn-header btn-header-icon notifications-btn" title="Notifiche">
                    🔔
                    <?php
                    if (class_exists('CDV_Notifications')) {
                        $unread_count = CDV_Notifications::get_unread_count(get_current_user_id());
                        if ($unread_count > 0) :
                        ?>
                            <span class="notifications-badge"><?php echo $unread_count; ?></span>
                        <?php
                        endif;
                    }
                    ?>
                </a>
                <a href="<?php echo esc_url(home_url('/dashboard')); ?>" class="btn-header btn-header-secondary">
                    Dashboard
                </a>
                <a href="<?php echo esc_url(home_url('/crea-viaggio')); ?>" class="btn-header btn-header-primary">
                    Crea Annuncio
                </a>
                <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="btn-header btn-header-ghost">
                    Esci
                </a>
            <?php else : ?>
                <a href="<?php echo esc_url(home_url('/accedi')); ?>" class="btn-header btn-header-secondary">
                    Accedi
                </a>
                <a href="<?php echo esc_url(home_url('/registrazione')); ?>" class="btn-header btn-header-primary">
                    Registrati
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>

<?php
/**
 * Fallback menu if no menu is set
 */
function cdv_fallback_menu() {
    echo '<ul class="nav-menu">';
    echo '<li><a href="' . esc_url(home_url('/')) . '">Home</a></li>';
    echo '<li><a href="' . esc_url(home_url('/viaggi')) . '">Viaggi</a></li>';
    if (is_user_logged_in()) {
        echo '<li><a href="' . esc_url(home_url('/dashboard')) . '">Dashboard</a></li>';
    }
    echo '</ul>';
}

/**
 * Fallback mobile menu if no menu is set
 */
function cdv_fallback_mobile_menu() {
    echo '<ul class="mobile-menu">';
    echo '<li><a href="' . esc_url(home_url('/')) . '">🏠 Home</a></li>';
    echo '<li><a href="' . esc_url(home_url('/viaggi')) . '">✈️ Viaggi</a></li>';
    echo '<li><a href="' . esc_url(home_url('/racconti')) . '">📖 Racconti</a></li>';
    if (is_user_logged_in()) {
        echo '<li><a href="' . esc_url(home_url('/dashboard')) . '">👤 Dashboard</a></li>';
        echo '<li><a href="' . esc_url(home_url('/crea-viaggio')) . '">➕ Crea Annuncio</a></li>';
        echo '<li><a href="' . esc_url(wp_logout_url(home_url())) . '" style="background: rgba(220, 53, 69, 0.2); color: #ff6b6b;">Esci</a></li>';
    } else {
        echo '<li><a href="' . esc_url(home_url('/accedi')) . '">🔐 Accedi</a></li>';
        echo '<li><a href="' . esc_url(home_url('/registrazione')) . '" style="background: var(--primary-color); color: white;">✨ Registrati</a></li>';
    }
    echo '</ul>';
}
?>
