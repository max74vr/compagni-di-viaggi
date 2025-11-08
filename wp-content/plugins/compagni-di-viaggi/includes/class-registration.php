<?php
/**
 * Frontend Registration System
 */

if (!defined('ABSPATH')) {
    exit;
}

class CDV_Registration {

    /**
     * Initialize
     */
    public static function init() {
        add_action('wp_ajax_nopriv_cdv_register_step1', array(__CLASS__, 'ajax_register_step1'));
        add_action('wp_ajax_nopriv_cdv_register_step2', array(__CLASS__, 'ajax_register_step2'));
        add_action('wp_ajax_cdv_update_profile', array(__CLASS__, 'ajax_update_profile'));
        add_action('wp_ajax_cdv_upload_profile_image', array(__CLASS__, 'ajax_upload_profile_image'));
        add_action('wp_ajax_cdv_create_first_travel', array(__CLASS__, 'ajax_create_first_travel'));

        // Prevent backend registration
        add_filter('register_url', array(__CLASS__, 'custom_register_url'));
    }

    /**
     * Custom registration URL
     */
    public static function custom_register_url($url) {
        return home_url('/registrazione');
    }

    /**
     * AJAX: Register Step 1 - Account Creation
     */
    public static function ajax_register_step1() {
        check_ajax_referer('cdv_ajax_nonce', 'nonce');

        $username = sanitize_user($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $display_name = sanitize_text_field($_POST['display_name']);

        // Validation
        if (empty($username) || empty($email) || empty($password) || empty($display_name)) {
            wp_send_json_error(array('message' => 'Tutti i campi sono obbligatori'));
        }

        if (!is_email($email)) {
            wp_send_json_error(array('message' => 'Email non valida'));
        }

        if (username_exists($username)) {
            wp_send_json_error(array('message' => 'Username già in uso'));
        }

        if (email_exists($email)) {
            wp_send_json_error(array('message' => 'Email già registrata'));
        }

        if (strlen($password) < 8) {
            wp_send_json_error(array('message' => 'La password deve essere di almeno 8 caratteri'));
        }

        // Create user
        $user_id = wp_create_user($username, $password, $email);

        if (is_wp_error($user_id)) {
            wp_send_json_error(array('message' => $user_id->get_error_message()));
        }

        // Set role to viaggiatore
        $user = new WP_User($user_id);
        $user->set_role('viaggiatore');

        // Update display name
        wp_update_user(array(
            'ID' => $user_id,
            'display_name' => $display_name,
        ));

        // Set pending approval
        update_user_meta($user_id, 'cdv_user_approved', 'pending');
        update_user_meta($user_id, 'cdv_registration_date', current_time('mysql'));

        // Invia email di verifica
        $email_sent = CDV_Email_Verification::send_verification_email($user_id);

        // Auto-login (temporaneo per completare la registrazione)
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);

        // Award early adopter badge
        CDV_Badges::award_badge($user_id, 'early_adopter');

        wp_send_json_success(array(
            'message' => 'Account creato! Controlla la tua email per confermare l\'indirizzo.',
            'user_id' => $user_id,
            'email_sent' => $email_sent,
        ));
    }

    /**
     * AJAX: Register Step 2 - Profile Information
     */
    public static function ajax_register_step2() {
        check_ajax_referer('cdv_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Devi essere autenticato'));
        }

        $user_id = get_current_user_id();

        // Personal Info
        $birth_date = sanitize_text_field($_POST['birth_date']);
        $gender = sanitize_text_field($_POST['gender']);
        $city = sanitize_text_field($_POST['city']);
        $country = sanitize_text_field($_POST['country']);
        $phone = sanitize_text_field($_POST['phone']);

        // Bio & Interests
        $bio = sanitize_textarea_field($_POST['bio']);
        $languages = sanitize_text_field($_POST['languages']);
        $travel_styles = isset($_POST['travel_styles']) ? array_map('sanitize_text_field', $_POST['travel_styles']) : array();
        $interests = isset($_POST['interests']) ? array_map('sanitize_text_field', $_POST['interests']) : array();

        // Travel Preferences
        $budget_range = sanitize_text_field($_POST['budget_range']);
        $travel_frequency = sanitize_text_field($_POST['travel_frequency']);
        $accommodation_preference = sanitize_text_field($_POST['accommodation_preference']);
        $travel_pace = sanitize_text_field($_POST['travel_pace']);

        // Social Links (optional)
        $instagram = sanitize_text_field($_POST['instagram']);
        $facebook = sanitize_text_field($_POST['facebook']);

        // Privacy Settings
        $show_age = isset($_POST['show_age']) ? '1' : '0';
        $show_phone = isset($_POST['show_phone']) ? '1' : '0';
        $show_email = isset($_POST['show_email']) ? '1' : '0';
        $show_social = isset($_POST['show_social']) ? '1' : '0';

        // Validation
        if (empty($birth_date) || empty($bio) || empty($city) || empty($country)) {
            wp_send_json_error(array('message' => 'Completa tutti i campi obbligatori'));
        }

        // Check age (min 18)
        $birth = new DateTime($birth_date);
        $today = new DateTime();
        $age = $today->diff($birth)->y;

        if ($age < 18) {
            wp_send_json_error(array('message' => 'Devi avere almeno 18 anni'));
        }

        // Save data
        update_user_meta($user_id, 'cdv_birth_date', $birth_date);
        update_user_meta($user_id, 'cdv_gender', $gender);
        update_user_meta($user_id, 'cdv_city', $city);
        update_user_meta($user_id, 'cdv_country', $country);
        update_user_meta($user_id, 'cdv_phone', $phone);
        update_user_meta($user_id, 'cdv_bio', $bio);
        update_user_meta($user_id, 'cdv_languages', $languages);
        update_user_meta($user_id, 'cdv_travel_styles', implode(', ', $travel_styles));
        update_user_meta($user_id, 'cdv_interests', implode(', ', $interests));
        update_user_meta($user_id, 'cdv_budget_range', $budget_range);
        update_user_meta($user_id, 'cdv_travel_frequency', $travel_frequency);
        update_user_meta($user_id, 'cdv_accommodation_preference', $accommodation_preference);
        update_user_meta($user_id, 'cdv_travel_pace', $travel_pace);
        update_user_meta($user_id, 'cdv_instagram', $instagram);
        update_user_meta($user_id, 'cdv_facebook', $facebook);

        // Privacy settings
        update_user_meta($user_id, 'cdv_show_age', $show_age);
        update_user_meta($user_id, 'cdv_show_phone', $show_phone);
        update_user_meta($user_id, 'cdv_show_email', $show_email);
        update_user_meta($user_id, 'cdv_show_social', $show_social);

        // Mark profile as complete
        update_user_meta($user_id, 'cdv_profile_completed', '1');

        // Notify admin of new registration
        self::notify_admin_new_user($user_id);

        wp_send_json_success(array(
            'message' => 'Profilo completato! Il tuo account è in attesa di approvazione.',
            'redirect' => home_url('/profilo-in-attesa'),
        ));
    }

    /**
     * AJAX: Upload profile image
     */
    public static function ajax_upload_profile_image() {
        check_ajax_referer('cdv_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Devi essere autenticato'));
        }

        if (!isset($_FILES['profile_image'])) {
            wp_send_json_error(array('message' => 'Nessuna immagine caricata'));
        }

        $user_id = get_current_user_id();

        // Validate file
        $allowed_types = array('image/jpeg', 'image/png', 'image/jpg');
        $max_size = 5 * 1024 * 1024; // 5MB

        $file = $_FILES['profile_image'];

        if (!in_array($file['type'], $allowed_types)) {
            wp_send_json_error(array('message' => 'Formato immagine non valido. Usa JPG o PNG.'));
        }

        if ($file['size'] > $max_size) {
            wp_send_json_error(array('message' => 'Immagine troppo grande. Massimo 5MB.'));
        }

        // Upload file
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        $upload = wp_handle_upload($file, array('test_form' => false));

        if (isset($upload['error'])) {
            wp_send_json_error(array('message' => $upload['error']));
        }

        // Create attachment
        $attachment_id = wp_insert_attachment(array(
            'post_mime_type' => $upload['type'],
            'post_title' => 'Profilo ' . $user_id,
            'post_content' => '',
            'post_status' => 'inherit'
        ), $upload['file']);

        // Generate metadata
        $attach_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
        wp_update_attachment_metadata($attachment_id, $attach_data);

        // Save to user meta
        update_user_meta($user_id, 'cdv_profile_image', $attachment_id);

        wp_send_json_success(array(
            'message' => 'Immagine caricata con successo',
            'image_url' => wp_get_attachment_url($attachment_id),
        ));
    }

    /**
     * Notify admin of new user registration
     */
    private static function notify_admin_new_user($user_id) {
        $user = get_user_by('id', $user_id);
        $admin_email = get_option('admin_email');

        $subject = '[Compagni di Viaggi] Nuovo utente da approvare';
        $message = sprintf(
            "Nuovo utente registrato:\n\nNome: %s\nUsername: %s\nEmail: %s\n\nApprova qui: %s",
            $user->display_name,
            $user->user_login,
            $user->user_email,
            admin_url('admin.php?page=cdv-pending-users')
        );

        wp_mail($admin_email, $subject, $message);
    }

    /**
     * Get public profile fields
     */
    public static function get_public_profile_fields($user_id) {
        $user = get_user_by('id', $user_id);

        $profile = array(
            'id' => $user_id,
            'display_name' => $user->display_name,
            'avatar' => self::get_profile_image_url($user_id),
            'bio' => get_user_meta($user_id, 'cdv_bio', true),
            'city' => get_user_meta($user_id, 'cdv_city', true),
            'country' => get_user_meta($user_id, 'cdv_country', true),
            'languages' => get_user_meta($user_id, 'cdv_languages', true),
            'travel_styles' => get_user_meta($user_id, 'cdv_travel_styles', true),
            'interests' => get_user_meta($user_id, 'cdv_interests', true),
            'verified' => get_user_meta($user_id, 'cdv_verified', true) === '1',
            'reputation' => get_user_meta($user_id, 'cdv_reputation_score', true),
            'total_reviews' => get_user_meta($user_id, 'cdv_total_reviews', true),
        );

        // Conditional fields based on privacy
        if (get_user_meta($user_id, 'cdv_show_age', true) === '1') {
            $profile['age'] = CDV_User_Meta::get_user_age($user_id);
        }

        if (get_user_meta($user_id, 'cdv_show_email', true) === '1') {
            $profile['email'] = $user->user_email;
        }

        if (get_user_meta($user_id, 'cdv_show_phone', true) === '1') {
            $profile['phone'] = get_user_meta($user_id, 'cdv_phone', true);
        }

        if (get_user_meta($user_id, 'cdv_show_social', true) === '1') {
            $profile['instagram'] = get_user_meta($user_id, 'cdv_instagram', true);
            $profile['facebook'] = get_user_meta($user_id, 'cdv_facebook', true);
        }

        return $profile;
    }

    /**
     * Get profile image URL
     */
    public static function get_profile_image_url($user_id, $size = 'thumbnail') {
        $image_id = get_user_meta($user_id, 'cdv_profile_image', true);

        if ($image_id) {
            return wp_get_attachment_image_url($image_id, $size);
        }

        return get_avatar_url($user_id);
    }

    /**
     * AJAX: Create First Travel (during registration)
     */
    public static function ajax_create_first_travel() {
        check_ajax_referer('cdv_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Devi essere autenticato'));
        }

        $title = sanitize_text_field($_POST['travel_title']);
        $description = sanitize_textarea_field($_POST['travel_description']);
        $destination = sanitize_text_field($_POST['travel_destination']);
        $country = sanitize_text_field($_POST['travel_country']);
        $start_date = sanitize_text_field($_POST['travel_start_date']);
        $end_date = sanitize_text_field($_POST['travel_end_date']);
        $budget = intval($_POST['travel_budget']);
        $max_participants = intval($_POST['travel_max_participants']);
        $travel_types = isset($_POST['travel_types']) ? array_map('intval', $_POST['travel_types']) : array();

        // Validation
        if (empty($title) || empty($description) || empty($destination) || empty($country)) {
            wp_send_json_error(array('message' => 'Compila tutti i campi obbligatori'));
        }

        if (empty($start_date) || empty($end_date)) {
            wp_send_json_error(array('message' => 'Inserisci le date del viaggio'));
        }

        if (strtotime($start_date) < strtotime('today')) {
            wp_send_json_error(array('message' => 'La data di inizio deve essere futura'));
        }

        if (strtotime($end_date) < strtotime($start_date)) {
            wp_send_json_error(array('message' => 'La data di fine deve essere dopo la data di inizio'));
        }

        // Create travel post
        $post_data = array(
            'post_type' => 'viaggio',
            'post_title' => $title,
            'post_content' => $description,
            'post_status' => 'pending', // Will be moderated
            'post_author' => get_current_user_id(),
        );

        $post_id = wp_insert_post($post_data);

        if (is_wp_error($post_id)) {
            wp_send_json_error(array('message' => 'Errore durante la creazione del viaggio'));
        }

        // Add meta data
        update_post_meta($post_id, 'cdv_destination', $destination);
        update_post_meta($post_id, 'cdv_country', $country);
        update_post_meta($post_id, 'cdv_start_date', $start_date);
        update_post_meta($post_id, 'cdv_end_date', $end_date);
        update_post_meta($post_id, 'cdv_budget', $budget);
        update_post_meta($post_id, 'cdv_max_participants', $max_participants);
        update_post_meta($post_id, 'cdv_travel_status', 'open');
        update_post_meta($post_id, 'cdv_views', 0);

        // Add travel types taxonomy
        if (!empty($travel_types)) {
            wp_set_post_terms($post_id, $travel_types, 'tipo_viaggio');
        }

        // Set destination taxonomy
        if (!empty($destination)) {
            wp_set_post_terms($post_id, array($destination), 'destinazione', false);
        }

        // Award badge for first travel
        CDV_Badges::award_badge(get_current_user_id(), 'first_travel');

        wp_send_json_success(array(
            'message' => 'Viaggio creato! Sarà pubblicato dopo l\'approvazione.',
            'travel_id' => $post_id,
        ));
    }
}
