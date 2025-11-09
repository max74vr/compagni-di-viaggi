<?php
/**
 * Email Verification
 *
 * Gestisce la verifica dell'email per nuovi utenti
 */

class CDV_Email_Verification {

    public static function init() {
        add_action('init', array(__CLASS__, 'handle_verification'));
        add_filter('authenticate', array(__CLASS__, 'block_unverified_login'), 30, 3);
    }

    /**
     * Genera e invia token di verifica email
     */
    public static function send_verification_email($user_id) {
        global $wpdb;

        $user = get_userdata($user_id);
        if (!$user) {
            return false;
        }

        // Verifica se la tabella esiste
        $table_name = $wpdb->prefix . 'cdv_email_verification';
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;

        if (!$table_exists) {
            // Tabella non esiste, salta verifica email ma non bloccare registrazione
            error_log('CDV: Email verification table does not exist. Skipping email verification.');
            return true; // Return true per non bloccare la registrazione
        }

        // Genera token unico
        $token = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));

        // Salva token nel database
        $result = $wpdb->insert($table_name, array(
            'user_id' => $user_id,
            'token' => $token,
            'expires_at' => $expires_at,
        ));

        if (!$result) {
            error_log('CDV: Failed to insert email verification token for user ' . $user_id);
            return true; // Return true comunque per non bloccare
        }

        // Crea link di verifica
        $verification_link = home_url('/conferma-email/?token=' . $token);

        // Invia email
        $subject = 'Conferma il tuo account - Compagni di Viaggi';
        $message = "
Ciao {$user->display_name},

Grazie per esserti registrato su Compagni di Viaggi!

Per completare la registrazione e attivare il tuo account, clicca sul link qui sotto:

{$verification_link}

Questo link è valido per 24 ore.

Se non hai richiesto questa registrazione, ignora questa email.

A presto,
Il team di Compagni di Viaggi
        ";

        $headers = array('Content-Type: text/plain; charset=UTF-8');

        $result = wp_mail($user->user_email, $subject, $message, $headers);

        if (!$result) {
            error_log('CDV: Failed to send verification email to user ' . $user_id . ' (' . $user->user_email . ')');
            error_log('CDV: WordPress wp_mail() returned false. Possible causes:');
            error_log('CDV: 1. Server mail() function not configured');
            error_log('CDV: 2. No SMTP plugin installed (recommended: WP Mail SMTP)');
            error_log('CDV: 3. Email address or domain blocked by hosting provider');
        } else {
            error_log('CDV: Verification email sent successfully to user ' . $user_id . ' (' . $user->user_email . ')');
        }

        return $result;
    }

    /**
     * Verifica token e attiva account
     */
    public static function verify_token($token) {
        global $wpdb;

        if (empty($token)) {
            return new WP_Error('invalid_token', 'Token non valido');
        }

        $table_name = $wpdb->prefix . 'cdv_email_verification';

        // Cerca il token
        $record = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE token = %s AND verified_at IS NULL",
            $token
        ));

        if (!$record) {
            return new WP_Error('invalid_token', 'Token non valido o già utilizzato');
        }

        // Controlla scadenza
        if (strtotime($record->expires_at) < current_time('timestamp')) {
            return new WP_Error('expired_token', 'Token scaduto. Richiedi una nuova email di conferma.');
        }

        // Marca come verificato
        $wpdb->update(
            $table_name,
            array('verified_at' => current_time('mysql')),
            array('id' => $record->id)
        );

        // Aggiorna user meta
        update_user_meta($record->user_id, 'cdv_email_verified', 'yes');

        return $record->user_id;
    }

    /**
     * Controlla se un utente ha verificato l'email
     */
    public static function is_email_verified($user_id) {
        return get_user_meta($user_id, 'cdv_email_verified', true) === 'yes';
    }

    /**
     * Blocca login per utenti non verificati (DISABILITATO - verifica email opzionale)
     */
    public static function block_unverified_login($user, $username, $password) {
        // Email verification è opzionale - permettiamo login anche senza email verificata
        // TODO: Riabilitare se si configura SMTP correttamente
        return $user;
    }

    /**
     * Gestisce la verifica via URL
     */
    public static function handle_verification() {
        // Controlla se siamo sulla pagina di conferma
        if (isset($_GET['token']) && is_page('conferma-email')) {
            $token = sanitize_text_field($_GET['token']);
            $result = self::verify_token($token);

            if (is_wp_error($result)) {
                // Salva errore in sessione (o query var)
                set_transient('cdv_verification_error_' . session_id(), $result->get_error_message(), 60);
            } else {
                // Salva successo
                set_transient('cdv_verification_success_' . session_id(), $result, 60);
            }
        }
    }

    /**
     * Reinvia email di verifica
     */
    public static function resend_verification_email($user_id) {
        global $wpdb;

        // Elimina token precedenti non verificati
        $table_name = $wpdb->prefix . 'cdv_email_verification';
        $wpdb->query($wpdb->prepare(
            "DELETE FROM $table_name WHERE user_id = %d AND verified_at IS NULL",
            $user_id
        ));

        // Invia nuovo token
        return self::send_verification_email($user_id);
    }
}
