<?php
/**
 * Theme Customizer
 *
 * Gestisce tutte le personalizzazioni del tema
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register customizer settings
 */
function cdv_customize_register($wp_customize) {

    // ========================================
    // SECTION: Colori
    // ========================================
    $wp_customize->add_section('cdv_colors', array(
        'title'    => 'Colori del Sito',
        'priority' => 30,
    ));

    // Primary Color
    $wp_customize->add_setting('cdv_primary_color', array(
        'default'           => '#667eea',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cdv_primary_color', array(
        'label'    => 'Colore Primario',
        'section'  => 'cdv_colors',
        'settings' => 'cdv_primary_color',
    )));

    // Secondary Color
    $wp_customize->add_setting('cdv_secondary_color', array(
        'default'           => '#764ba2',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cdv_secondary_color', array(
        'label'    => 'Colore Secondario',
        'section'  => 'cdv_colors',
        'settings' => 'cdv_secondary_color',
    )));

    // Text Color
    $wp_customize->add_setting('cdv_text_color', array(
        'default'           => '#2c3e50',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cdv_text_color', array(
        'label'    => 'Colore Testo',
        'section'  => 'cdv_colors',
        'settings' => 'cdv_text_color',
    )));

    // Link Color
    $wp_customize->add_setting('cdv_link_color', array(
        'default'           => '#667eea',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cdv_link_color', array(
        'label'    => 'Colore Link',
        'section'  => 'cdv_colors',
        'settings' => 'cdv_link_color',
    )));

    // ========================================
    // SECTION: Logo & Header
    // ========================================
    $wp_customize->add_section('cdv_header', array(
        'title'    => 'Logo & Intestazione',
        'priority' => 35,
    ));

    // Logo Height
    $wp_customize->add_setting('cdv_logo_height', array(
        'default'           => '50',
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('cdv_logo_height', array(
        'label'       => 'Altezza Logo (px)',
        'description' => 'Imposta l\'altezza del logo in pixel',
        'section'     => 'cdv_header',
        'settings'    => 'cdv_logo_height',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 30,
            'max'  => 150,
            'step' => 5,
        ),
    ));

    // Header Background Color
    $wp_customize->add_setting('cdv_header_bg_color', array(
        'default'           => '#667eea',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cdv_header_bg_color', array(
        'label'    => 'Colore Sfondo Header',
        'section'  => 'cdv_header',
        'settings' => 'cdv_header_bg_color',
    )));

    // ========================================
    // SECTION: Hero Homepage
    // ========================================
    $wp_customize->add_section('cdv_hero', array(
        'title'       => 'Sezione Hero (Homepage)',
        'description' => 'Personalizza la sezione principale della homepage',
        'priority'    => 40,
    ));

    // Hero Title
    $wp_customize->add_setting('cdv_hero_title', array(
        'default'           => 'Trova i Tuoi Compagni di Viaggio',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('cdv_hero_title', array(
        'label'    => 'Titolo Hero',
        'section'  => 'cdv_hero',
        'settings' => 'cdv_hero_title',
        'type'     => 'text',
    ));

    // Hero Subtitle
    $wp_customize->add_setting('cdv_hero_subtitle', array(
        'default'           => 'Connettiti con viaggiatori che condividono le tue passioni. Organizza avventure indimenticabili insieme.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('cdv_hero_subtitle', array(
        'label'    => 'Sottotitolo Hero',
        'section'  => 'cdv_hero',
        'settings' => 'cdv_hero_subtitle',
        'type'     => 'textarea',
    ));

    // Hero Button Text
    $wp_customize->add_setting('cdv_hero_button_text', array(
        'default'           => 'Inserisci il Tuo Annuncio',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('cdv_hero_button_text', array(
        'label'    => 'Testo Pulsante Hero',
        'section'  => 'cdv_hero',
        'settings' => 'cdv_hero_button_text',
        'type'     => 'text',
    ));

    // Hero Button URL
    $wp_customize->add_setting('cdv_hero_button_url', array(
        'default'           => '/crea-viaggio',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('cdv_hero_button_url', array(
        'label'    => 'URL Pulsante Hero',
        'section'  => 'cdv_hero',
        'settings' => 'cdv_hero_button_url',
        'type'     => 'url',
    ));

    // ========================================
    // SECTION: Sezione Viaggi
    // ========================================
    $wp_customize->add_section('cdv_travels_section', array(
        'title'       => 'Sezione Viaggi (Homepage)',
        'description' => 'Personalizza la sezione viaggi in evidenza',
        'priority'    => 45,
    ));

    // Travels Section Title
    $wp_customize->add_setting('cdv_travels_title', array(
        'default'           => 'Proposte di Viaggi',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('cdv_travels_title', array(
        'label'    => 'Titolo Sezione',
        'section'  => 'cdv_travels_section',
        'settings' => 'cdv_travels_title',
        'type'     => 'text',
    ));

    // Travels Section Subtitle
    $wp_customize->add_setting('cdv_travels_subtitle', array(
        'default'           => 'Scopri le prossime avventure e unisciti ai viaggiatori',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('cdv_travels_subtitle', array(
        'label'    => 'Sottotitolo Sezione',
        'section'  => 'cdv_travels_section',
        'settings' => 'cdv_travels_subtitle',
        'type'     => 'text',
    ));

    // Travels Button Text
    $wp_customize->add_setting('cdv_travels_button_text', array(
        'default'           => 'Vedi Tutti i Viaggi',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('cdv_travels_button_text', array(
        'label'    => 'Testo Pulsante',
        'section'  => 'cdv_travels_section',
        'settings' => 'cdv_travels_button_text',
        'type'     => 'text',
    ));

    // ========================================
    // SECTION: Come Funziona
    // ========================================
    $wp_customize->add_section('cdv_how_it_works', array(
        'title'       => 'Sezione "Come Funziona"',
        'description' => 'Personalizza la sezione come funziona',
        'priority'    => 50,
    ));

    // How it Works Title
    $wp_customize->add_setting('cdv_how_title', array(
        'default'           => 'Come Funziona',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('cdv_how_title', array(
        'label'    => 'Titolo Sezione',
        'section'  => 'cdv_how_it_works',
        'settings' => 'cdv_how_title',
        'type'     => 'text',
    ));

    // How it Works Subtitle
    $wp_customize->add_setting('cdv_how_subtitle', array(
        'default'           => 'In pochi semplici passi puoi trovare i tuoi compagni di viaggio',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('cdv_how_subtitle', array(
        'label'    => 'Sottotitolo Sezione',
        'section'  => 'cdv_how_it_works',
        'settings' => 'cdv_how_subtitle',
        'type'     => 'text',
    ));

    // Step 1
    $wp_customize->add_setting('cdv_step1_title', array(
        'default'           => '1. Crea il Tuo Profilo',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('cdv_step1_title', array(
        'label'    => 'Titolo Step 1',
        'section'  => 'cdv_how_it_works',
        'settings' => 'cdv_step1_title',
        'type'     => 'text',
    ));

    $wp_customize->add_setting('cdv_step1_text', array(
        'default'           => 'Registrati e completa il tuo profilo con interessi, lingue parlate e stili di viaggio preferiti.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('cdv_step1_text', array(
        'label'    => 'Testo Step 1',
        'section'  => 'cdv_how_it_works',
        'settings' => 'cdv_step1_text',
        'type'     => 'textarea',
    ));

    // Step 2
    $wp_customize->add_setting('cdv_step2_title', array(
        'default'           => '2. Cerca o Crea un Viaggio',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('cdv_step2_title', array(
        'label'    => 'Titolo Step 2',
        'section'  => 'cdv_how_it_works',
        'settings' => 'cdv_step2_title',
        'type'     => 'text',
    ));

    $wp_customize->add_setting('cdv_step2_text', array(
        'default'           => 'Cerca tra i viaggi disponibili o crea il tuo e aspetta che altri viaggiatori si uniscano.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('cdv_step2_text', array(
        'label'    => 'Testo Step 2',
        'section'  => 'cdv_how_it_works',
        'settings' => 'cdv_step2_text',
        'type'     => 'textarea',
    ));

    // Step 3
    $wp_customize->add_setting('cdv_step3_title', array(
        'default'           => '3. Connettiti e Organizza',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('cdv_step3_title', array(
        'label'    => 'Titolo Step 3',
        'section'  => 'cdv_how_it_works',
        'settings' => 'cdv_step3_title',
        'type'     => 'text',
    ));

    $wp_customize->add_setting('cdv_step3_text', array(
        'default'           => 'Usa la chat di gruppo per conoscere i compagni di viaggio e organizzare i dettagli insieme.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('cdv_step3_text', array(
        'label'    => 'Testo Step 3',
        'section'  => 'cdv_how_it_works',
        'settings' => 'cdv_step3_text',
        'type'     => 'textarea',
    ));

    // ========================================
    // SECTION: Sezione Racconti
    // ========================================
    $wp_customize->add_section('cdv_stories_section', array(
        'title'       => 'Sezione Racconti (Homepage)',
        'description' => 'Personalizza la sezione racconti di viaggio',
        'priority'    => 55,
    ));

    // Stories Section Title
    $wp_customize->add_setting('cdv_stories_title', array(
        'default'           => 'Racconti di Viaggio',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('cdv_stories_title', array(
        'label'    => 'Titolo Sezione',
        'section'  => 'cdv_stories_section',
        'settings' => 'cdv_stories_title',
        'type'     => 'text',
    ));

    // Stories Section Subtitle
    $wp_customize->add_setting('cdv_stories_subtitle', array(
        'default'           => 'Lasciati ispirare dalle esperienze dei nostri viaggiatori',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('cdv_stories_subtitle', array(
        'label'    => 'Sottotitolo Sezione',
        'section'  => 'cdv_stories_section',
        'settings' => 'cdv_stories_subtitle',
        'type'     => 'text',
    ));

    // Stories Button Text
    $wp_customize->add_setting('cdv_stories_button_text', array(
        'default'           => 'Vedi Tutti i Racconti',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('cdv_stories_button_text', array(
        'label'    => 'Testo Pulsante',
        'section'  => 'cdv_stories_section',
        'settings' => 'cdv_stories_button_text',
        'type'     => 'text',
    ));
}
add_action('customize_register', 'cdv_customize_register');

/**
 * Output custom CSS
 */
function cdv_customizer_css() {
    $primary_color = get_theme_mod('cdv_primary_color', '#667eea');
    $secondary_color = get_theme_mod('cdv_secondary_color', '#764ba2');
    $text_color = get_theme_mod('cdv_text_color', '#2c3e50');
    $link_color = get_theme_mod('cdv_link_color', '#667eea');
    $logo_height = get_theme_mod('cdv_logo_height', '50');
    $header_bg_color = get_theme_mod('cdv_header_bg_color', '#667eea');
    ?>
    <style type="text/css">
        :root {
            --primary-color: <?php echo esc_attr($primary_color); ?>;
            --secondary-color: <?php echo esc_attr($secondary_color); ?>;
            --text-dark: <?php echo esc_attr($text_color); ?>;
            --link-color: <?php echo esc_attr($link_color); ?>;
        }

        .site-header {
            background: linear-gradient(135deg, <?php echo esc_attr($header_bg_color); ?> 0%, <?php echo esc_attr($secondary_color); ?> 100%);
        }

        .custom-logo {
            max-height: <?php echo esc_attr($logo_height); ?>px;
            width: auto;
        }

        a {
            color: <?php echo esc_attr($link_color); ?>;
        }

        .btn-primary,
        .btn-header-primary {
            background: <?php echo esc_attr($primary_color); ?>;
        }

        .btn-primary:hover,
        .btn-header-primary:hover {
            background: <?php echo esc_attr($secondary_color); ?>;
        }
    </style>
    <?php
}
add_action('wp_head', 'cdv_customizer_css');
