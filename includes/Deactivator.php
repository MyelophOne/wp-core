<?php
/**
 * Plugin deactivator
 *
 * @package Myelophone Core
 */
if (!defined("ABSPATH")) {
    exit();
}

class Meph_Deactivator
{
    /**
     * Deactivate the plugin
     *
     * @return void
     */
    public static function deactivate()
    {
        self::clear_scheduled_events();

        flush_rewrite_rules();
    }

    /**
     * Clear scheduled events
     *
     * @return void
     */
    private static function clear_scheduled_events()
    {
        wp_clear_scheduled_hook("meph_monthly_cleanup");
    }
}
