<?php
/**
 * AJAX handlers for frontend interactions
 */

if (!defined('ABSPATH')) {
    exit;
}

class CDV_Ajax_Handlers {

    /**
     * Initialize
     */
    public static function init() {
        // For logged-in users
        add_action('wp_ajax_cdv_join_travel', array(__CLASS__, 'join_travel'));
        add_action('wp_ajax_cdv_send_message', array(__CLASS__, 'send_message'));
        add_action('wp_ajax_cdv_get_new_messages', array(__CLASS__, 'get_new_messages'));
        add_action('wp_ajax_cdv_add_review', array(__CLASS__, 'add_review'));
        add_action('wp_ajax_cdv_accept_participant', array(__CLASS__, 'accept_participant'));
        add_action('wp_ajax_cdv_reject_participant', array(__CLASS__, 'reject_participant'));
        add_action('wp_ajax_cdv_approve_participant', array(__CLASS__, 'accept_participant'));
        add_action('wp_ajax_cdv_change_travel_status', array(__CLASS__, 'change_travel_status'));
        add_action('wp_ajax_cdv_delete_travel', array(__CLASS__, 'delete_travel'));
        add_action('wp_ajax_cdv_resend_verification', array(__CLASS__, 'resend_verification'));

        // Profile management
        add_action('wp_ajax_cdv_update_profile', array(__CLASS__, 'update_profile'));
        add_action('wp_ajax_cdv_change_password', array(__CLASS__, 'change_password'));
        add_action('wp_ajax_cdv_delete_account', array(__CLASS__, 'delete_account'));
        add_action('wp_ajax_cdv_upload_profile_image', array(__CLASS__, 'upload_profile_image'));

        // Travel creation
        add_action('wp_ajax_cdv_create_travel', array(__CLASS__, 'create_travel'));

        // Group Chat
        add_action('wp_ajax_cdv_send_group_message', array(__CLASS__, 'send_group_message'));
        add_action('wp_ajax_cdv_get_group_messages', array(__CLASS__, 'get_group_messages'));

        // For non-logged-in users (if needed)
        // add_action('wp_ajax_nopriv_action_name', array(__CLASS__, 'method_name'));
    }

    /**
     * AJAX: Join travel
     */
    public static function join_travel() {
        check_ajax_referer('cdv_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Devi essere autenticato'));
        }

        $travel_id = isset($_POST['travel_id']) ? intval($_POST['travel_id']) : 0;
        $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';

        if (!$travel_id) {
            wp_send_json_error(array('message' => 'ID viaggio non valido'));
        }

        $result = CDV_Participants::request_join($travel_id, get_current_user_id(), $message);

        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }

        wp_send_json_success(array(
            'message' => 'Richiesta inviata con successo',
            'id' => $result,
        ));
    }

    /**
     * AJAX: Send chat message
     */
    public static function send_message() {
        check_ajax_referer('cdv_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Devi essere autenticato'));
        }

        $chat_group_id = isset($_POST['chat_group_id']) ? intval($_POST['chat_group_id']) : 0;
        $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';

        if (!$chat_group_id || empty($message)) {
            wp_send_json_error(array('message' => 'Dati non validi'));
        }

        // Check access
        if (!CDV_Chat::can_user_access_chat($chat_group_id, get_current_user_id())) {
            wp_send_json_error(array('message' => 'Non hai accesso a questa chat'));
        }

        $result = CDV_Chat::send_message($chat_group_id, get_current_user_id(), $message);

        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }

        $user = wp_get_current_user();

        wp_send_json_success(array(
            'message' => array(
                'id' => $result,
                'user' => array(
                    'id' => $user->ID,
                    'name' => $user->display_name,
                    'avatar' => get_avatar_url($user->ID, array('size' => 40)),
                ),
                'message' => $message,
                'created_at' => current_time('mysql'),
            ),
        ));
    }

    /**
     * AJAX: Get new chat messages
     */
    public static function get_new_messages() {
        check_ajax_referer('cdv_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Devi essere autenticato'));
        }

        $chat_group_id = isset($_POST['chat_group_id']) ? intval($_POST['chat_group_id']) : 0;
        $since = isset($_POST['since']) ? sanitize_text_field($_POST['since']) : '';

        if (!$chat_group_id) {
            wp_send_json_error(array('message' => 'ID chat non valido'));
        }

        // Check access
        if (!CDV_Chat::can_user_access_chat($chat_group_id, get_current_user_id())) {
            wp_send_json_error(array('message' => 'Non hai accesso a questa chat'));
        }

        $messages = CDV_Chat::get_new_messages($chat_group_id, $since);

        $formatted = array();
        foreach ($messages as $msg) {
            $user = get_user_by('id', $msg->user_id);
            $formatted[] = array(
                'id' => $msg->id,
                'user' => array(
                    'id' => $user->ID,
                    'name' => $user->display_name,
                    'avatar' => get_avatar_url($user->ID, array('size' => 40)),
                ),
                'message' => $msg->message,
                'created_at' => $msg->created_at,
            );
        }

        wp_send_json_success(array('messages' => $formatted));
    }

    /**
     * AJAX: Add review
     */
    public static function add_review() {
        check_ajax_referer('cdv_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Devi essere autenticato'));
        }

        $travel_id = isset($_POST['travel_id']) ? intval($_POST['travel_id']) : 0;
        $reviewed_id = isset($_POST['reviewed_id']) ? intval($_POST['reviewed_id']) : 0;
        $scores = array(
            'punctuality' => isset($_POST['punctuality']) ? intval($_POST['punctuality']) : 0,
            'group_spirit' => isset($_POST['group_spirit']) ? intval($_POST['group_spirit']) : 0,
            'respect' => isset($_POST['respect']) ? intval($_POST['respect']) : 0,
            'adaptability' => isset($_POST['adaptability']) ? intval($_POST['adaptability']) : 0,
        );
        $comment = isset($_POST['comment']) ? sanitize_textarea_field($_POST['comment']) : '';

        if (!$travel_id || !$reviewed_id) {
            wp_send_json_error(array('message' => 'Dati non validi'));
        }

        $result = CDV_Reviews::add_review($travel_id, get_current_user_id(), $reviewed_id, $scores, $comment);

        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }

        wp_send_json_success(array(
            'message' => 'Recensione aggiunta con successo',
            'id' => $result,
        ));
    }

    /**
     * AJAX: Accept participant
     */
    public static function accept_participant() {
        check_ajax_referer('cdv_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Devi essere autenticato'));
        }

        $travel_id = isset($_POST['travel_id']) ? intval($_POST['travel_id']) : 0;
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

        if (!$travel_id || !$user_id) {
            wp_send_json_error(array('message' => 'Dati non validi'));
        }

        // Check if current user is the organizer
        $travel = get_post($travel_id);
        if ($travel->post_author != get_current_user_id()) {
            wp_send_json_error(array('message' => 'Solo l\'organizzatore può accettare partecipanti'));
        }

        $result = CDV_Participants::accept_participant($travel_id, $user_id);

        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }

        wp_send_json_success(array('message' => 'Partecipante accettato'));
    }

    /**
     * AJAX: Reject participant
     */
    public static function reject_participant() {
        check_ajax_referer('cdv_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Devi essere autenticato'));
        }

        $travel_id = isset($_POST['travel_id']) ? intval($_POST['travel_id']) : 0;
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

        if (!$travel_id || !$user_id) {
            wp_send_json_error(array('message' => 'Dati non validi'));
        }

        // Check if current user is the organizer
        $travel = get_post($travel_id);
        if ($travel->post_author != get_current_user_id()) {
            wp_send_json_error(array('message' => 'Solo l\'organizzatore può rifiutare partecipanti'));
        }

        $result = CDV_Participants::reject_participant($travel_id, $user_id);

        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }

        wp_send_json_success(array('message' => 'Partecipante rifiutato'));
    }

    /**
     * AJAX: Change travel status
     */
    public static function change_travel_status() {
        check_ajax_referer('cdv_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error('Devi essere autenticato');
        }

        $travel_id = isset($_POST['travel_id']) ? intval($_POST['travel_id']) : 0;
        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';

        if (!$travel_id || !$status) {
            wp_send_json_error('Dati non validi');
        }

        // Check if current user is the author
        $travel = get_post($travel_id);
        if (!$travel || $travel->post_author != get_current_user_id()) {
            wp_send_json_error('Non hai i permessi per modificare questo viaggio');
        }

        // Validate status
        $valid_statuses = array('open', 'full', 'closed', 'completed');
        if (!in_array($status, $valid_statuses)) {
            wp_send_json_error('Stato non valido');
        }

        update_post_meta($travel_id, 'cdv_travel_status', $status);

        wp_send_json_success('Stato aggiornato con successo');
    }

    /**
     * AJAX: Delete travel
     */
    public static function delete_travel() {
        check_ajax_referer('cdv_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error('Devi essere autenticato');
        }

        $travel_id = isset($_POST['travel_id']) ? intval($_POST['travel_id']) : 0;

        if (!$travel_id) {
            wp_send_json_error('ID viaggio non valido');
        }

        // Check if current user is the author
        $travel = get_post($travel_id);
        if (!$travel || $travel->post_author != get_current_user_id()) {
            wp_send_json_error('Non hai i permessi per eliminare questo viaggio');
        }

        // Delete the post (moves to trash)
        $result = wp_trash_post($travel_id);

        if (!$result) {
            wp_send_json_error('Errore durante l\'eliminazione del viaggio');
        }

        wp_send_json_success('Viaggio eliminato con successo');
    }

    /**
     * AJAX: Resend verification email
     */
    public static function resend_verification() {
        check_ajax_referer('cdv_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error('Devi essere autenticato');
        }

        $user_id = get_current_user_id();

        // Controlla se già verificato
        if (CDV_Email_Verification::is_email_verified($user_id)) {
            wp_send_json_error('Email già verificata');
        }

        // Reinvia email
        $result = CDV_Email_Verification::resend_verification_email($user_id);

        if ($result) {
            wp_send_json_success('Email di verifica inviata con successo');
        } else {
            wp_send_json_error('Errore durante l\'invio dell\'email');
        }
    }

    /**
     * AJAX: Update Profile
     */
    public static function update_profile() {
        check_ajax_referer('cdv_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Devi essere autenticato'));
        }

        $user_id = get_current_user_id();

        $display_name = isset($_POST['display_name']) ? sanitize_text_field($_POST['display_name']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $city = isset($_POST['city']) ? sanitize_text_field($_POST['city']) : '';
        $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
        $bio = isset($_POST['bio']) ? sanitize_textarea_field($_POST['bio']) : '';

        // Update WordPress user
        $user_data = array(
            'ID' => $user_id,
            'display_name' => $display_name,
            'user_email' => $email,
        );

        $result = wp_update_user($user_data);

        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }

        // Update user meta
        update_user_meta($user_id, 'cdv_city', $city);
        update_user_meta($user_id, 'cdv_phone', $phone);
        update_user_meta($user_id, 'cdv_bio', $bio);

        wp_send_json_success(array('message' => 'Profilo aggiornato con successo'));
    }

    /**
     * AJAX: Change Password
     */
    public static function change_password() {
        check_ajax_referer('cdv_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Devi essere autenticato'));
        }

        $user_id = get_current_user_id();
        $user = get_user_by('id', $user_id);

        $current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
        $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';

        // Verify current password
        if (!wp_check_password($current_password, $user->user_pass, $user_id)) {
            wp_send_json_error(array('message' => 'Password attuale non corretta'));
        }

        // Update password
        wp_set_password($new_password, $user_id);

        wp_send_json_success(array('message' => 'Password cambiata con successo'));
    }

    /**
     * AJAX: Delete Account
     */
    public static function delete_account() {
        check_ajax_referer('cdv_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Devi essere autenticato'));
        }

        $user_id = get_current_user_id();

        // Don't allow admins to delete themselves via frontend
        if (user_can($user_id, 'manage_options')) {
            wp_send_json_error(array('message' => 'Gli amministratori non possono eliminare il proprio account'));
        }

        // Delete user's travels
        $travels = get_posts(array(
            'post_type' => 'viaggio',
            'author' => $user_id,
            'posts_per_page' => -1,
            'fields' => 'ids',
        ));

        foreach ($travels as $travel_id) {
            wp_delete_post($travel_id, true);
        }

        // Delete user
        require_once(ABSPATH . 'wp-admin/includes/user.php');
        wp_delete_user($user_id);

        // Logout
        wp_logout();

        wp_send_json_success(array('message' => 'Account eliminato con successo'));
    }

    /**
     * AJAX: Create Travel
     */
    public static function create_travel() {
        check_ajax_referer('cdv_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Devi essere autenticato'));
        }

        $user_id = get_current_user_id();

        // Check if user has capability
        if (!current_user_can('create_viaggi')) {
            wp_send_json_error(array('message' => 'Non hai i permessi per creare viaggi'));
        }

        // Validate required fields
        $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
        $description = isset($_POST['description']) ? wp_kses_post($_POST['description']) : '';
        $destination = isset($_POST['destination']) ? sanitize_text_field($_POST['destination']) : '';
        $country = isset($_POST['country']) ? sanitize_text_field($_POST['country']) : '';
        $travel_month = isset($_POST['travel_month']) ? sanitize_text_field($_POST['travel_month']) : '';
        $budget = isset($_POST['budget']) ? intval($_POST['budget']) : 0;
        $max_participants = isset($_POST['max_participants']) ? intval($_POST['max_participants']) : 5;

        if (empty($title) || empty($description) || empty($destination) || empty($country) ||
            empty($travel_month) || $budget <= 0 || $max_participants < 2) {
            wp_send_json_error(array('message' => 'Compila tutti i campi obbligatori'));
        }

        // Validate month format
        if (!preg_match('/^\d{4}-\d{2}$/', $travel_month)) {
            wp_send_json_error(array('message' => 'Formato mese non valido'));
        }

        // Convert month to first and last day
        $start_date = $travel_month . '-01'; // First day of month
        $last_day = date('t', strtotime($start_date)); // Number of days in month
        $end_date = $travel_month . '-' . $last_day; // Last day of month

        // Validate dates
        if (strtotime($start_date) < strtotime('today')) {
            wp_send_json_error(array('message' => 'Il mese selezionato deve essere futuro'));
        }

        // Create travel post
        $post_data = array(
            'post_type' => 'viaggio',
            'post_title' => $title,
            'post_content' => $description,
            'post_status' => 'pending', // Pending approval
            'post_author' => $user_id,
        );

        $travel_id = wp_insert_post($post_data);

        if (is_wp_error($travel_id)) {
            wp_send_json_error(array('message' => 'Errore durante la creazione del viaggio'));
        }

        // Save meta data
        update_post_meta($travel_id, 'cdv_destination', $destination);
        update_post_meta($travel_id, 'cdv_country', $country);
        update_post_meta($travel_id, 'cdv_start_date', $start_date);
        update_post_meta($travel_id, 'cdv_end_date', $end_date);
        update_post_meta($travel_id, 'cdv_travel_month', $travel_month); // Store original month for display
        update_post_meta($travel_id, 'cdv_budget', $budget);
        update_post_meta($travel_id, 'cdv_max_participants', $max_participants);
        update_post_meta($travel_id, 'cdv_travel_status', 'open');

        // Set travel types
        if (isset($_POST['travel_types']) && is_array($_POST['travel_types'])) {
            $travel_types = array_map('intval', $_POST['travel_types']);
            wp_set_post_terms($travel_id, $travel_types, 'tipo_viaggio');
        }

        // Add organizer as first participant
        global $wpdb;
        $table_name = $wpdb->prefix . 'cdv_travel_participants';

        $wpdb->insert(
            $table_name,
            array(
                'travel_id' => $travel_id,
                'user_id' => $user_id,
                'status' => 'accepted',
                'is_organizer' => 1,
                'requested_at' => current_time('mysql'),
            ),
            array('%d', '%d', '%s', '%d', '%s')
        );

        wp_send_json_success(array(
            'message' => 'Viaggio creato con successo! In attesa di approvazione da parte degli amministratori.',
            'redirect_url' => home_url('/dashboard'),
        ));
    }

    /**
     * AJAX: Upload profile image
     */
    public static function upload_profile_image() {
        check_ajax_referer('cdv_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Devi essere autenticato'));
        }

        $user_id = get_current_user_id();

        // Check if file was uploaded
        if (!isset($_FILES['profile_image'])) {
            wp_send_json_error(array('message' => 'Nessun file caricato'));
        }

        $file = $_FILES['profile_image'];

        // Validate file type
        $allowed_types = array('image/jpeg', 'image/jpg', 'image/png');
        if (!in_array($file['type'], $allowed_types)) {
            wp_send_json_error(array('message' => 'Formato file non valido. Usa JPG o PNG'));
        }

        // Validate file size (5MB max)
        if ($file['size'] > 5 * 1024 * 1024) {
            wp_send_json_error(array('message' => 'File troppo grande. Massimo 5MB'));
        }

        // Handle upload using WordPress
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        // Delete old profile image if exists
        $old_attachment_id = get_user_meta($user_id, 'cdv_profile_image_id', true);
        if ($old_attachment_id) {
            wp_delete_attachment($old_attachment_id, true);
        }

        // Upload new image
        $attachment_id = media_handle_upload('profile_image', 0);

        if (is_wp_error($attachment_id)) {
            wp_send_json_error(array('message' => 'Errore durante il caricamento: ' . $attachment_id->get_error_message()));
        }

        // Save attachment ID to user meta
        update_user_meta($user_id, 'cdv_profile_image_id', $attachment_id);

        // Get image URL
        $image_url = wp_get_attachment_url($attachment_id);

        wp_send_json_success(array(
            'message' => 'Immagine profilo aggiornata con successo',
            'image_url' => $image_url,
        ));
    }

    /**
     * AJAX: Send group message
     */
    public static function send_group_message() {
        check_ajax_referer('cdv_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Devi essere autenticato'));
        }

        $travel_id = isset($_POST['travel_id']) ? intval($_POST['travel_id']) : 0;
        $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';
        $user_id = get_current_user_id();

        if (empty($travel_id) || empty($message)) {
            wp_send_json_error(array('message' => 'Parametri mancanti'));
        }

        // Send message
        $result = CDV_Group_Chat::send_message($travel_id, $user_id, $message);

        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }

        wp_send_json_success(array(
            'message' => 'Messaggio inviato',
            'message_id' => $result,
        ));
    }

    /**
     * AJAX: Get group messages
     */
    public static function get_group_messages() {
        check_ajax_referer('cdv_ajax_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Devi essere autenticato'));
        }

        $travel_id = isset($_POST['travel_id']) ? intval($_POST['travel_id']) : 0;
        $user_id = get_current_user_id();

        if (empty($travel_id)) {
            wp_send_json_error(array('message' => 'Travel ID mancante'));
        }

        // Check if user is participant
        if (!CDV_Group_Chat::is_participant($travel_id, $user_id)) {
            wp_send_json_error(array('message' => 'Non sei un partecipante di questo viaggio'));
        }

        // Get messages
        $messages = CDV_Group_Chat::get_messages($travel_id, 100);
        $formatted = CDV_Group_Chat::format_messages($messages, $user_id);

        // Get participants
        $participants = CDV_Group_Chat::get_participants($travel_id);

        wp_send_json_success(array(
            'messages' => $formatted,
            'participants' => $participants,
            'participants_count' => count($participants),
        ));
    }
}
