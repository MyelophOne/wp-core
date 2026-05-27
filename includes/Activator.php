<?php
/**
 * Plugin activator
 *
 * @package Myelophone Core
 */
if (!defined("ABSPATH")) {
    exit();
}

class Meph_Activator
{
    /**
     * Activate the plugin
     *
     * @return void
     */
    public static function activate()
    {
        $default_options = [
            "display_errors" => "0",
            "hide_wp_version" => "0",
            "disable_emoji" => "0",
            "restrict_rest_api" => "0",
            "disable_file_editor" => "0",
            "require_admin_email_confirmation" => "0",
        ];

        foreach ($default_options as $key => $value) {
            if (false === get_option("meph_core_{$key}")) {
                add_option("meph_core_{$key}", $value);
            }
        }

        self::create_tables();

        flush_rewrite_rules();
    }

    /**
     * Create database tables
     *
     * @return void
     */
    private static function create_tables()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
    }
}
