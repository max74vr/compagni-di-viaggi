<?php
/**
 * Custom User Roles and Capabilities
 */

if (!defined('ABSPATH')) {
    exit;
}

class CDV_User_Roles {

    /**
     * Initialize
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'register_roles'));

        // Block backend access for viaggiatore
        add_action('admin_init', array(__CLASS__, 'block_admin_access'));

        // Hide admin bar for viaggiatore
        add_action('after_setup_theme', array(__CLASS__, 'hide_admin_bar'));
    }

    /**
     * Register custom user roles
     */
    public static function register_roles() {
        // Add Viaggiatore role on plugin activation
        if (!get_role('viaggiatore')) {
            add_role(
                'viaggiatore',
                __('Viaggiatore', 'compagni-di-viaggi'),
                array(
                    'read' => true,
                    'edit_posts' => false,
                    'delete_posts' => false,
                    'publish_posts' => false,
                    'upload_files' => true,

                    // Custom capabilities
                    'create_viaggi' => true,
                    'edit_own_viaggi' => true,
                    'delete_own_viaggi' => true,
                    'join_viaggi' => true,
                    'use_chat' => true,
                    'leave_reviews' => true,
                )
            );
        }

        // Add custom capabilities to administrator
        $admin_role = get_role('administrator');
        if ($admin_role) {
            $admin_role->add_cap('approve_users');
            $admin_role->add_cap('approve_viaggi');
            $admin_role->add_cap('moderate_chat');
            $admin_role->add_cap('manage_viaggiatori');
        }
    }

    /**
     * Get user approval status
     */
    public static function get_user_approval_status($user_id) {
        return get_user_meta($user_id, 'cdv_user_approved', true);
    }

    /**
     * Check if user is approved
     */
    public static function is_user_approved($user_id) {
        return get_user_meta($user_id, 'cdv_user_approved', true) === '1';
    }

    /**
     * Approve user
     */
    public static function approve_user($user_id) {
        update_user_meta($user_id, 'cdv_user_approved', '1');
        update_user_meta($user_id, 'cdv_user_approved_date', current_time('mysql'));

        // Send approval email
        self::send_approval_email($user_id);

        do_action('cdv_user_approved', $user_id);
    }

    /**
     * Reject user
     */
    public static function reject_user($user_id, $reason = '') {
        update_user_meta($user_id, 'cdv_user_approved', '0');
        update_user_meta($user_id, 'cdv_user_rejected_date', current_time('mysql'));

        if ($reason) {
            update_user_meta($user_id, 'cdv_user_rejection_reason', $reason);
        }

        do_action('cdv_user_rejected', $user_id, $reason);
    }

    /**
     * Send approval email
     */
    private static function send_approval_email($user_id) {
        $user = get_user_by('id', $user_id);

        $subject = 'Il tuo account è stato approvato!';
        $message = sprintf(
            "Ciao %s,\n\nIl tuo account su Compagni di Viaggi è stato approvato!\n\nOra puoi:\n- Cercare viaggi\n- Creare i tuoi viaggi\n- Richiedere di partecipare\n- Usare la chat di gruppo\n\nAccedi qui: %s\n\nBuon viaggio!\nIl team di Compagni di Viaggi",
            $user->display_name,
            wp_login_url()
        );

        wp_mail($user->user_email, $subject, $message);
    }

    /**
     * Check if user can create travel posts
     */
    public static function can_create_travel($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }

        if (!$user_id) {
            return false;
        }

        // Must be approved
        if (!self::is_user_approved($user_id)) {
            return false;
        }

        // Must have capability
        return user_can($user_id, 'create_viaggi');
    }

    /**
     * Get pending users count
     */
    public static function get_pending_users_count() {
        $args = array(
            'role' => 'viaggiatore',
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => 'cdv_user_approved',
                    'value' => 'pending',
                    'compare' => '='
                ),
                array(
                    'key' => 'cdv_user_approved',
                    'compare' => 'NOT EXISTS'
                )
            ),
            'fields' => 'ID',
        );

        $users = get_users($args);
        return count($users);
    }

    /**
     * Get pending users
     */
    public static function get_pending_users() {
        $args = array(
            'role' => 'viaggiatore',
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => 'cdv_user_approved',
                    'value' => 'pending',
                    'compare' => '='
                ),
                array(
                    'key' => 'cdv_user_approved',
                    'compare' => 'NOT EXISTS'
                )
            ),
        );

        return get_users($args);
    }

    /**
     * Get profile completion percentage
     */
    public static function get_profile_completion($user_id) {
        $required_fields = array(
            'cdv_bio',
            'cdv_birth_date',
            'cdv_gender',
            'cdv_city',
            'cdv_country',
            'cdv_languages',
            'cdv_travel_styles',
        );

        $completed = 0;
        foreach ($required_fields as $field) {
            $value = get_user_meta($user_id, $field, true);
            if (!empty($value)) {
                $completed++;
            }
        }

        // Check avatar
        if (get_user_meta($user_id, 'cdv_profile_image', true)) {
            $completed++;
        }

        $total = count($required_fields) + 1; // +1 for avatar
        return round(($completed / $total) * 100);
    }

    /**
     * Block admin access for viaggiatore role
     */
    public static function block_admin_access() {
        if (current_user_can('viaggiatore') && !wp_doing_ajax()) {
            wp_redirect(home_url('/dashboard'));
            exit;
        }
    }

    /**
     * Hide admin bar for viaggiatore role
     */
    public static function hide_admin_bar() {
        if (current_user_can('viaggiatore')) {
            show_admin_bar(false);
        }
    }
}
