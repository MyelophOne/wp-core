<?php
/**
 * Plugin uninstaller
 *
 * @package Myelophone Core
 */
if (!defined("ABSPATH")) {
    exit();
}

class Meph_Uninstaller
{
    /**
     * Uninstall the plugin
     *
     * @return void
     */
    public static function uninstall()
    {
        if (!defined("WP_UNINSTALL_PLUGIN")) {
            exit();
        }

        self::remove_options();

        self::remove_tables();

        self::remove_transients();

        wp_cache_flush();
    }

    /**
     * Remove plugin options
     *
     * @return void
     */
    private static function remove_options()
    {
        $options = [
            "meph_core_display_errors",
            "meph_core_hide_wp_version",
            "meph_core_disable_emoji",
            "meph_core_restrict_rest_api",
            "meph_core_disable_file_editor",
            "meph_core_require_admin_email_confirmation",
            "meph_core_version",
        ];

        foreach ($options as $option) {
            delete_option($option);
            delete_site_option($option);
        }
    }

    /**
     * Remove database tables
     *
     * @return void
     */
    private static function remove_tables()
    {
        global $wpdb;

        // future possible feature if any table is created
    }

    /**
     * Remove transients
     *
     * @return void
     */
    private static function remove_transients()
    {
        global $wpdb;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
                "_transient_meph_core_%",
                "_transient_timeout_meph_core_%",
            ),
        );

        if (is_multisite()) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM {$wpdb->sitemeta} WHERE meta_key LIKE %s OR meta_key LIKE %s",
                    "_site_transient_meph_core_%",
                    "_site_transient_timeout_meph_core_%",
                ),
            );
        }
    }
}
