<?php
/**
 * Custom Avatars System
 *
 * Sostituisce Gravatar con immagini profilo personalizzate
 */

if (!defined('ABSPATH')) {
    exit;
}

class CDV_Custom_Avatars {

    /**
     * Initialize
     */
    public static function init() {
        // Override WordPress avatar
        add_filter('get_avatar_url', array(__CLASS__, 'get_custom_avatar_url'), 10, 3);
        add_filter('get_avatar', array(__CLASS__, 'get_custom_avatar'), 10, 6);
    }

    /**
     * Get custom avatar URL
     */
    public static function get_custom_avatar_url($url, $id_or_email, $args) {
        $user = false;

        // Get user from different input types
        if (is_numeric($id_or_email)) {
            $user = get_user_by('id', $id_or_email);
        } elseif (is_object($id_or_email)) {
            if (isset($id_or_email->user_id)) {
                $user = get_user_by('id', $id_or_email->user_id);
            } elseif (isset($id_or_email->ID)) {
                $user = get_user_by('id', $id_or_email->ID);
            }
        } elseif (is_string($id_or_email) && is_email($id_or_email)) {
            $user = get_user_by('email', $id_or_email);
        }

        if (!$user) {
            return $url;
        }

        // Check for custom profile image
        $attachment_id = get_user_meta($user->ID, 'cdv_profile_image_id', true);

        if ($attachment_id) {
            $custom_url = wp_get_attachment_url($attachment_id);
            if ($custom_url) {
                return $custom_url;
            }
        }

        return $url;
    }

    /**
     * Get custom avatar HTML
     */
    public static function get_custom_avatar($avatar, $id_or_email, $size, $default, $alt, $args) {
        $user = false;

        // Get user from different input types
        if (is_numeric($id_or_email)) {
            $user = get_user_by('id', $id_or_email);
        } elseif (is_object($id_or_email)) {
            if (isset($id_or_email->user_id)) {
                $user = get_user_by('id', $id_or_email->user_id);
            } elseif (isset($id_or_email->ID)) {
                $user = get_user_by('id', $id_or_email->ID);
            }
        } elseif (is_string($id_or_email) && is_email($id_or_email)) {
            $user = get_user_by('email', $id_or_email);
        }

        if (!$user) {
            return $avatar;
        }

        // Check for custom profile image
        $attachment_id = get_user_meta($user->ID, 'cdv_profile_image_id', true);

        if ($attachment_id) {
            $custom_url = wp_get_attachment_url($attachment_id);
            if ($custom_url) {
                $avatar = sprintf(
                    '<img alt="%s" src="%s" class="avatar avatar-%d photo" height="%d" width="%d" loading="lazy" decoding="async" />',
                    esc_attr($alt),
                    esc_url($custom_url),
                    esc_attr($size),
                    esc_attr($size),
                    esc_attr($size)
                );
            }
        }

        return $avatar;
    }

    /**
     * Get user avatar URL
     */
    public static function get_user_avatar_url($user_id, $size = 96) {
        $attachment_id = get_user_meta($user_id, 'cdv_profile_image_id', true);

        if ($attachment_id) {
            $custom_url = wp_get_attachment_url($attachment_id);
            if ($custom_url) {
                return $custom_url;
            }
        }

        // Fallback to gravatar
        $user = get_user_by('id', $user_id);
        if ($user) {
            return get_avatar_url($user->user_email, array('size' => $size));
        }

        return '';
    }
}
