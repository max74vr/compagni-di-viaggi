<?php
/**
 * Admin panel
 */

if (!defined('ABSPATH')) {
    exit;
}

class CDV_Admin {

    /**
     * Initialize
     */
    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_menu'));
        add_action('add_meta_boxes', array(__CLASS__, 'add_meta_boxes'));
        add_action('save_post_viaggio', array(__CLASS__, 'save_meta_boxes'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_scripts'));
    }

    /**
     * Add admin menu
     */
    public static function add_menu() {
        add_menu_page(
            'Compagni di Viaggi',
            'Compagni di Viaggi',
            'manage_options',
            'cdv-dashboard',
            array(__CLASS__, 'dashboard_page'),
            'dashicons-palmtree',
            6
        );

        add_submenu_page(
            'cdv-dashboard',
            'Impostazioni',
            'Impostazioni',
            'manage_options',
            'cdv-settings',
            array(__CLASS__, 'settings_page')
        );
    }

    /**
     * Dashboard page
     */
    public static function dashboard_page() {
        global $wpdb;

        $stats = array(
            'total_travels' => wp_count_posts('viaggio')->publish,
            'total_users' => count_users()['total_users'],
            'total_participants' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}cdv_travel_participants WHERE status = 'accepted'"),
            'total_reviews' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}cdv_reviews"),
        );

        ?>
        <div class="wrap">
            <h1>Dashboard Compagni di Viaggi</h1>

            <div class="cdv-stats">
                <div class="cdv-stat-box">
                    <h3>Viaggi Pubblicati</h3>
                    <p class="cdv-stat-number"><?php echo $stats['total_travels']; ?></p>
                </div>
                <div class="cdv-stat-box">
                    <h3>Utenti Registrati</h3>
                    <p class="cdv-stat-number"><?php echo $stats['total_users']; ?></p>
                </div>
                <div class="cdv-stat-box">
                    <h3>Partecipazioni</h3>
                    <p class="cdv-stat-number"><?php echo $stats['total_participants']; ?></p>
                </div>
                <div class="cdv-stat-box">
                    <h3>Recensioni</h3>
                    <p class="cdv-stat-number"><?php echo $stats['total_reviews']; ?></p>
                </div>
            </div>

            <style>
                .cdv-stats {
                    display: grid;
                    grid-template-columns: repeat(4, 1fr);
                    gap: 20px;
                    margin: 30px 0;
                }
                .cdv-stat-box {
                    background: white;
                    padding: 20px;
                    border-left: 4px solid #667eea;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                }
                .cdv-stat-box h3 {
                    margin: 0 0 10px 0;
                    color: #667eea;
                }
                .cdv-stat-number {
                    font-size: 32px;
                    font-weight: bold;
                    margin: 0;
                }
            </style>
        </div>
        <?php
    }

    /**
     * Settings page
     */
    public static function settings_page() {
        if (isset($_POST['cdv_save_settings'])) {
            check_admin_referer('cdv_settings_nonce');

            update_option('cdv_max_participants', intval($_POST['cdv_max_participants']));
            update_option('cdv_min_age', intval($_POST['cdv_min_age']));
            update_option('cdv_chat_enabled', isset($_POST['cdv_chat_enabled']));
            update_option('cdv_reviews_enabled', isset($_POST['cdv_reviews_enabled']));
            update_option('cdv_auto_approve_participants', isset($_POST['cdv_auto_approve_participants']));

            echo '<div class="notice notice-success"><p>Impostazioni salvate con successo!</p></div>';
        }

        $max_participants = get_option('cdv_max_participants', 10);
        $min_age = get_option('cdv_min_age', 18);
        $chat_enabled = get_option('cdv_chat_enabled', true);
        $reviews_enabled = get_option('cdv_reviews_enabled', true);
        $auto_approve = get_option('cdv_auto_approve_participants', false);

        ?>
        <div class="wrap">
            <h1>Impostazioni Compagni di Viaggi</h1>

            <form method="post" action="">
                <?php wp_nonce_field('cdv_settings_nonce'); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="cdv_max_participants">Numero Massimo Partecipanti (default)</label>
                        </th>
                        <td>
                            <input type="number" name="cdv_max_participants" id="cdv_max_participants" value="<?php echo esc_attr($max_participants); ?>" class="regular-text" />
                            <p class="description">Numero massimo di partecipanti per viaggio (può essere sovrascritto per singolo viaggio)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="cdv_min_age">Età Minima</label>
                        </th>
                        <td>
                            <input type="number" name="cdv_min_age" id="cdv_min_age" value="<?php echo esc_attr($min_age); ?>" class="regular-text" />
                            <p class="description">Età minima richiesta per registrarsi</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Chat</th>
                        <td>
                            <label>
                                <input type="checkbox" name="cdv_chat_enabled" value="1" <?php checked($chat_enabled, true); ?> />
                                Abilita sistema chat
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Recensioni</th>
                        <td>
                            <label>
                                <input type="checkbox" name="cdv_reviews_enabled" value="1" <?php checked($reviews_enabled, true); ?> />
                                Abilita sistema recensioni
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Approvazione Automatica</th>
                        <td>
                            <label>
                                <input type="checkbox" name="cdv_auto_approve_participants" value="1" <?php checked($auto_approve, true); ?> />
                                Approva automaticamente i partecipanti
                            </label>
                            <p class="description">Se abilitato, gli utenti vengono accettati automaticamente senza approvazione dell'organizzatore</p>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <input type="submit" name="cdv_save_settings" class="button button-primary" value="Salva Impostazioni" />
                </p>
            </form>
        </div>
        <?php
    }

    /**
     * Add meta boxes
     */
    public static function add_meta_boxes() {
        add_meta_box(
            'cdv_travel_details',
            'Dettagli Viaggio',
            array(__CLASS__, 'travel_details_meta_box'),
            'viaggio',
            'normal',
            'high'
        );

        add_meta_box(
            'cdv_travel_participants',
            'Partecipanti',
            array(__CLASS__, 'travel_participants_meta_box'),
            'viaggio',
            'side',
            'default'
        );
    }

    /**
     * Travel details meta box
     */
    public static function travel_details_meta_box($post) {
        wp_nonce_field('cdv_meta_box_nonce', 'cdv_meta_box_nonce');

        $start_date = get_post_meta($post->ID, 'cdv_start_date', true);
        $end_date = get_post_meta($post->ID, 'cdv_end_date', true);
        $destination = get_post_meta($post->ID, 'cdv_destination', true);
        $country = get_post_meta($post->ID, 'cdv_country', true);
        $budget = get_post_meta($post->ID, 'cdv_budget', true);
        $max_participants = get_post_meta($post->ID, 'cdv_max_participants', true);
        $status = get_post_meta($post->ID, 'cdv_travel_status', true);

        ?>
        <table class="form-table">
            <tr>
                <th><label for="cdv_start_date">Data Inizio</label></th>
                <td><input type="date" name="cdv_start_date" id="cdv_start_date" value="<?php echo esc_attr($start_date); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="cdv_end_date">Data Fine</label></th>
                <td><input type="date" name="cdv_end_date" id="cdv_end_date" value="<?php echo esc_attr($end_date); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="cdv_destination">Destinazione</label></th>
                <td><input type="text" name="cdv_destination" id="cdv_destination" value="<?php echo esc_attr($destination); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="cdv_country">Paese</label></th>
                <td><input type="text" name="cdv_country" id="cdv_country" value="<?php echo esc_attr($country); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="cdv_budget">Budget Stimato (€)</label></th>
                <td><input type="number" name="cdv_budget" id="cdv_budget" value="<?php echo esc_attr($budget); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="cdv_max_participants">Max Partecipanti</label></th>
                <td><input type="number" name="cdv_max_participants" id="cdv_max_participants" value="<?php echo esc_attr($max_participants); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="cdv_travel_status">Stato Viaggio</label></th>
                <td>
                    <select name="cdv_travel_status" id="cdv_travel_status">
                        <option value="open" <?php selected($status, 'open'); ?>>Aperto</option>
                        <option value="full" <?php selected($status, 'full'); ?>>Completo</option>
                        <option value="in_progress" <?php selected($status, 'in_progress'); ?>>In Corso</option>
                        <option value="completed" <?php selected($status, 'completed'); ?>>Completato</option>
                        <option value="cancelled" <?php selected($status, 'cancelled'); ?>>Annullato</option>
                    </select>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Travel participants meta box
     */
    public static function travel_participants_meta_box($post) {
        $participants = CDV_Participants::get_participants($post->ID);

        if (empty($participants)) {
            echo '<p>Nessun partecipante ancora.</p>';
            return;
        }

        echo '<ul>';
        foreach ($participants as $participant) {
            $user = get_user_by('id', $participant->user_id);
            $status_label = array(
                'pending' => 'In Attesa',
                'accepted' => 'Accettato',
                'rejected' => 'Rifiutato',
            );
            echo '<li>';
            echo esc_html($user->display_name);
            echo ' - <strong>' . $status_label[$participant->status] . '</strong>';
            echo '</li>';
        }
        echo '</ul>';
    }

    /**
     * Save meta boxes
     */
    public static function save_meta_boxes($post_id) {
        if (!isset($_POST['cdv_meta_box_nonce']) || !wp_verify_nonce($_POST['cdv_meta_box_nonce'], 'cdv_meta_box_nonce')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $fields = array(
            'cdv_start_date',
            'cdv_end_date',
            'cdv_destination',
            'cdv_country',
            'cdv_budget',
            'cdv_max_participants',
            'cdv_travel_status',
        );

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }
    }

    /**
     * Enqueue admin scripts
     */
    public static function enqueue_scripts($hook) {
        // Add admin-specific CSS/JS if needed
    }
}
