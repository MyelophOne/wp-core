<?php
/**
 * Plugin hooks
 *
 * @package Myelophone Core
 */
if (!defined("ABSPATH")) {
    exit();
}

class Meph_Hooks
{
    /**
     * Settings instance
     *
     * @var Meph_Settings
     */
    private $settings;

    /**
     * Constructor
     *
     * @param Meph_Settings $settings Settings instance
     */
    public function __construct($settings)
    {
        $this->settings = $settings;
        $this->register_hooks();
    }

    /**
     * Register hooks
     *
     * @return void
     */
    private function register_hooks()
    {
        add_filter("cron_schedules", [$this, "add_cron_schedules"]);

        add_action("init", [$this, "handle_frontend_hooks"]);

        add_action("admin_init", [$this, "handle_admin_hooks"]);
    }

    /**
     * Add custom cron schedules
     *
     * @param array $schedules Existing schedules
     * @return array
     */
    public function add_cron_schedules($schedules)
    {
        $schedules["monthly"] = [
            "interval" => 30 * DAY_IN_SECONDS,
            "display" => __("Once Monthly", "myelophone-core"),
        ];

        return $schedules;
    }

    /**
     * Handle frontend hooks
     *
     * @return void
     */
    public function handle_frontend_hooks()
    {
        // Hide WordPress version
        if ($this->settings->get_option("hide_wp_version") === "1") {
            add_filter("the_generator", "__return_empty_string");
            remove_action("wp_head", "wp_generator");
        }

        // Disable emoji
        if ($this->settings->get_option("disable_emoji") === "1") {
            $this->disable_emoji();
        }

        // Restrict REST API
        if ($this->settings->get_option("restrict_rest_api") === "1") {
            add_filter("rest_authentication_errors", [
                $this,
                "restrict_rest_api",
            ]);
        }

        // Disable XML-RPC
        if ($this->settings->get_option("disable_xmlrpc") === "1") {
            add_filter("xmlrpc_enabled", "__return_false");
            add_filter("xmlrpc_methods", [$this, "disable_xmlrpc_methods"]);
        }

        // Limit post revisions
        if ($this->settings->get_option("limit_revisions") === "1") {
            add_filter(
                "wp_revisions_to_keep",
                "meph_custom_limit_revisions",
                10,
                2,
            );

            function meph_custom_limit_revisions(int $num, WP_Post $post): int
            {
                if (get_option("limit_revisions") === "1") {
                    return 5;
                }

                return $num;
            }
        }

        // Disable comments
        if ($this->settings->get_option("disable_comments") === "1") {
            $this->disable_comments();
        }

        // Disable Heartbeat API
        if ($this->settings->get_option("disable_heartbeat") === "1") {
            add_action("init", [$this, "disable_heartbeat"], 1);
        }

        // Cleanup WordPress head
        if ($this->settings->get_option("cleanup_head") === "1") {
            $this->cleanup_wp_head();
        }

        // Disable embeds
        if ($this->settings->get_option("disable_embeds") === "1") {
            $this->disable_embeds();
        }

        // Security headers
        if ($this->settings->get_option("security_headers") === "1") {
            add_action("send_headers", [$this, "add_security_headers"]);
        }

        // Hide admin bar
        if ($this->settings->get_option("hide_admin_bar") === "1") {
            add_filter("show_admin_bar", [$this, "hide_admin_bar"]);
        }

        // Move jQuery to footer and remove jQuery Migrate
        if (
            $this->settings->get_option("move_jquery_footer") === "1" ||
            $this->settings->get_option("remove_jquery_migrate") === "1"
        ) {
            add_action(
                "wp_enqueue_scripts",
                [$this, "handle_jquery_optimization"],
                99,
            );
        }

        // Add verification meta tags
        add_action("wp_head", [$this, "add_verification_meta_tags"]);

        // Cleanup SVG filters and Global Styles
        if ($this->settings->get_option("cleanup_svg_filters") === "1") {
            add_filter(
                "should_load_separate_core_block_assets",
                "__return_false",
                99,
            );

            // Remove global styles and SVG filters from frontend
            $this->remove_global_styles_frontend();

            // Remove global styles and SVG filters from admin
            $this->remove_global_styles_admin();

            // Remove theme JSON style nodes
            add_filter("wp_theme_json_get_style_nodes", "__return_empty_array");

            // Remove inline styles from style tags
            add_filter(
                "style_loader_tag",
                [$this, "remove_inline_styles_from_tags"],
                10,
                4,
            );

            // Remove inline styles from HTML output
            add_action("template_redirect", [
                $this,
                "start_inline_styles_removal",
            ]);
            add_action("shutdown", [$this, "end_inline_styles_removal"], 0);
        }

        // Database optimization - manage schedule based on setting
        $this->manage_database_optimization_schedule();

        // Enable safe SVG upload support
        if ($this->settings->get_option("enable_svg_safe") === "1") {
            add_filter("upload_mimes", [$this, "enable_svg_upload"]);
            add_filter(
                "wp_check_filetype_and_ext",
                [$this, "check_svg_filetype"],
                10,
                4,
            );
        }

        // Add Google Fonts preload
        if ($this->settings->get_option("google_fonts_preload") === "1") {
            add_action("wp_head", [$this, "add_google_fonts_preload"], 2);
        }

        // Disable file editor
        if ($this->settings->get_option("disable_file_editor") === "1") {
            if (!defined("DISALLOW_FILE_EDIT")) {
                define("DISALLOW_FILE_EDIT", true);
            }
        }

        // Require admin email confirmation
        if (
            $this->settings->get_option("require_admin_email_confirmation") ===
            "1"
        ) {
            add_filter("send_email_change_email", "__return_true");
        }

        // Prevent author enumeration scans
        if ($this->settings->get_option("disable_author_scans") === "1") {
            add_action("template_redirect", [
                $this,
                "prevent_author_enumeration",
            ]);
        }

        // Disable Dashicons for non-logged-in users
        if ($this->settings->get_option("disable_dashicons_frontend") === "1") {
            add_action(
                "wp_enqueue_scripts",
                [$this, "disable_dashicons_frontend"],
                100,
            );
        }

        // Disable self pingbacks
        if ($this->settings->get_option("disable_self_pingbacks") === "1") {
            add_filter("pre_ping", [$this, "disable_self_pingbacks"]);
        }

        // Remove website field from comment form
        if (
            $this->settings->get_option("remove_website_field_comments") === "1"
        ) {
            add_filter("comment_form_default_fields", [
                $this,
                "remove_website_field_comments",
            ]);
        }

        // Hide login error messages
        if ($this->settings->get_option("hide_login_errors") === "1") {
            add_filter("login_errors", [$this, "hide_login_errors"]);
            add_filter("wp_login_errors", [$this, "hide_login_wp_errors"]);
            add_filter("authenticate", [$this, "hide_auth_errors"], 30, 3);
        }

        // Disable unnecessary image sizes
        if ($this->settings->get_option("disable_image_sizes") === "1") {
            add_filter("intermediate_image_sizes_advanced", [
                $this,
                "disable_image_sizes",
            ]);
            add_filter("big_image_size_threshold", "__return_false");
            // Disable default image sizes
            add_filter("intermediate_image_sizes", "__return_empty_array");
            // Remove thumbnail size
            update_option("thumbnail_size_w", 0);
            update_option("thumbnail_size_h", 0);
            update_option("thumbnail_crop", 0);
            // Remove medium size
            update_option("medium_size_w", 0);
            update_option("medium_size_h", 0);
            // Remove large size
            update_option("large_size_w", 0);
            update_option("large_size_h", 0);
        }

        // Disable attachment pages
        if ($this->settings->get_option("disable_attachment_pages") === "1") {
            add_action("template_redirect", [
                $this,
                "disable_attachment_pages",
            ]);
        }

        // Auto empty trash
        if ($this->settings->get_option("auto_empty_trash") === "1") {
            add_action("init", [$this, "auto_empty_trash"]);
        }

        // Disable WooCommerce scripts on non-shop pages
        if (
            $this->settings->get_option("disable_woocommerce_non_shop") === "1"
        ) {
            add_action(
                "wp_enqueue_scripts",
                [$this, "disable_woocommerce_non_shop"],
                99,
            );
        }

        // Maintenance mode
        if ($this->settings->get_option("maintenance_mode") === "1") {
            add_action("template_redirect", [$this, "maintenance_mode"]);
        }

        // Force SSL
        if ($this->settings->get_option("force_ssl") === "1") {
            add_action("template_redirect", [$this, "force_ssl"]);
        }

        add_action("wp_head", [$this, "add_plugin_signature"], 1);
    }

    /**
     * Handle admin hooks
     *
     * @return void
     */
    public function handle_admin_hooks()
    {
        add_filter("plugin_action_links_" . MYELOPHONE_CORE_BASENAME, [
            $this,
            "add_plugin_action_links",
        ]);

        // Enable classic widgets
        if ($this->settings->get_option("enable_classic_widgets") === "1") {
            $this->enable_classic_widgets();
        }

        // Enable post cloning
        if ($this->settings->get_option("enable_post_cloning") === "1") {
            $this->enable_post_cloning();
        }

        add_action("admin_head", [$this, "add_plugin_signature"], 1);
    }

    /**
     * Add plugin signature to site header
     *
     * @return void
     */
    public function add_plugin_signature()
    {
        echo "<!-- Site powerfully enhanced by MyelophOne Core v" .
            esc_html(MYELOPHONE_CORE_VERSION) .
            " -->" .
            "\n";
    }

    /**
     * Manage database optimization schedule based on setting
     *
     * @return void
     */
    private function manage_database_optimization_schedule()
    {
        $is_enabled = $this->settings->get_option("optimize_database") === "1";
        $next_scheduled = wp_next_scheduled("meph_monthly_cleanup");

        if ($is_enabled) {
            add_action("meph_monthly_cleanup", [$this, "optimize_database"]);

            if (!$next_scheduled) {
                $next_month = strtotime("first day of next month 03:00:00");
                wp_schedule_event(
                    $next_month,
                    "monthly",
                    "meph_monthly_cleanup",
                );

                if (defined("WP_DEBUG") && WP_DEBUG) {
                    // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
                    error_log(
                        "MyelophOne: Scheduled monthly database optimization",
                    );
                }
            }
        } else {
            if ($next_scheduled) {
                wp_clear_scheduled_hook("meph_monthly_cleanup");

                if (defined("WP_DEBUG") && WP_DEBUG) {
                    // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
                    error_log(
                        "MyelophOne: Removed monthly database optimization schedule",
                    );
                }
            }
        }
    }

    /**
     * Disable WordPress emoji
     *
     * @return void
     */
    private function disable_emoji()
    {
        remove_action("wp_head", "print_emoji_detection_script", 7);
        remove_action("admin_print_scripts", "print_emoji_detection_script");
        remove_action("wp_print_styles", "print_emoji_styles");
        remove_action("admin_print_styles", "print_emoji_styles");
        remove_filter("the_content_feed", "wp_staticize_emoji");
        remove_filter("comment_text_rss", "wp_staticize_emoji");
        remove_filter("wp_mail", "wp_staticize_emoji_for_email");

        add_filter("tiny_mce_plugins", [$this, "disable_emoji_tinymce"]);
        add_filter(
            "wp_resource_hints",
            [$this, "disable_emoji_dns_prefetch"],
            10,
            2,
        );
    }

    /**
     * Disable emoji in TinyMCE
     *
     * @param array $plugins TinyMCE plugins
     * @return array
     */
    public function disable_emoji_tinymce($plugins)
    {
        if (is_array($plugins)) {
            return array_diff($plugins, ["wpemoji"]);
        }

        return $plugins;
    }

    /**
     * Disable emoji DNS prefetch
     *
     * @param array $urls URLs to prefetch
     * @param string $relation_type Relation type
     * @return array
     */
    public function disable_emoji_dns_prefetch($urls, $relation_type)
    {
        if ("dns-prefetch" === $relation_type) {
            $emoji_svg_url = apply_filters(
                "myelophone_core_emoji_svg_url",
                "https://s.w.org/images/core/emoji/",
            );

            foreach ($urls as $key => $url) {
                if (strpos($url, $emoji_svg_url) !== false) {
                    unset($urls[$key]);
                }
            }
        }

        return $urls;
    }

    /**
     * Restrict REST API access
     *
     * @param WP_Error|null $result Authentication result
     * @return WP_Error|null|bool
     */
    public function restrict_rest_api($result)
    {
        if (!empty($result)) {
            return $result;
        }

        if (!is_user_logged_in()) {
            return new WP_Error(
                "rest_not_logged_in",
                __("You are not currently logged in.", "myelophone-core"),
                ["status" => 401],
            );
        }

        return $result;
    }

    /**
     * Disable XML-RPC methods
     *
     * @param array $methods XML-RPC methods
     * @return array
     */
    public function disable_xmlrpc_methods($methods)
    {
        return [];
    }

    /**
     * Disable WordPress comments
     *
     * @return void
     */
    private function disable_comments()
    {
        // Close comments on the front-end
        add_filter("comments_open", "__return_false", 20, 2);
        add_filter("pings_open", "__return_false", 20, 2);

        // Hide existing comments
        add_filter("comments_array", "__return_empty_array", 10, 2);

        // Remove comment support from post types
        add_action("init", function () {
            $post_types = get_post_types();
            foreach ($post_types as $post_type) {
                if (post_type_supports($post_type, "comments")) {
                    remove_post_type_support($post_type, "comments");
                    remove_post_type_support($post_type, "trackbacks");
                }
            }
        });

        // Remove comments page from admin menu
        add_action("admin_menu", function () {
            remove_menu_page("edit-comments.php");
        });

        // Remove comments from admin bar
        add_action("wp_before_admin_bar_render", function () {
            global $wp_admin_bar;
            $wp_admin_bar->remove_menu("comments");
        });
    }

    /**
     * Disable WordPress Heartbeat API
     *
     * @return void
     */
    public function disable_heartbeat()
    {
        wp_deregister_script("heartbeat");
    }

    /**
     * Cleanup WordPress head
     *
     * @return void
     */
    private function cleanup_wp_head()
    {
        remove_action("wp_head", "rsd_link");
        remove_action("wp_head", "wlwmanifest_link");
        remove_action("wp_head", "wp_shortlink_wp_head");
        remove_action("wp_head", "feed_links", 2);
        remove_action("wp_head", "feed_links_extra", 3);
        remove_action("wp_head", "adjacent_posts_rel_link_wp_head");
        remove_action("wp_head", "wp_oembed_add_discovery_links");
        remove_action("wp_head", "wp_oembed_add_host_js");
        remove_action("wp_head", "rest_output_link_wp_head");
        remove_action("wp_head", "wp_resource_hints", 2);

        // Also remove shortlink from HTTP headers
        remove_action("template_redirect", "wp_shortlink_header", 11);

        // Disable shortlink functionality completely
        add_filter("get_shortlink", "__return_false");
        add_filter("pre_get_shortlink", "__return_false");

        // Remove shortlink from REST API
        remove_action("wp_head", "wp_shortlink_wp_head", 10);
        remove_action("template_redirect", "wp_shortlink_header", 11);

        // Remove shortlink from all locations
        add_filter("after_setup_theme", function () {
            remove_action("wp_head", "wp_shortlink_wp_head", 10);
            remove_action("template_redirect", "wp_shortlink_header", 11);
        });

        // Remove REST API Link HTTP header
        remove_action("template_redirect", "rest_output_link_header", 11);

        add_filter("rest_authentication_errors", function ($result) {
            if (!is_user_logged_in()) {
                return new WP_Error(
                    "rest_not_logged_in",
                    "REST API limited to authenticated users.",
                    ["status" => 401],
                );
            }
            return $result;
        });
    }

    /**
     * Disable WordPress embeds
     *
     * @return void
     */
    private function disable_embeds()
    {
        remove_action("wp_head", "wp_oembed_add_discovery_links");
        remove_action("wp_head", "wp_oembed_add_host_js");
        remove_action("rest_api_init", "wp_oembed_register_route");
        remove_filter("oembed_dataparse", "wp_filter_oembed_result");
        add_filter("embed_oembed_discover", "__return_false");
        remove_filter("pre_oembed_result", "wp_filter_pre_oembed_result");
        remove_action("wp_head", "wp_generator");
        remove_action("wp_head", "feed_links_extra", 3);
        remove_action("wp_head", "feed_links", 2);
        remove_action("wp_head", "rsd_link");
        remove_action("wp_head", "wlwmanifest_link");
        remove_action("wp_head", "wp_shortlink_wp_head");
        // Remove embeds rewrite rules
        add_filter("rewrite_rules_array", [$this, "disable_embeds_rewrites"]);

        // Remove embeds endpoint
        remove_action("init", "wp_oembed_register_route");
    }

    /**
     * Remove embeds rewrite rules
     *
     * @param array $rules Rewrite rules
     * @return array
     */
    public function disable_embeds_rewrites($rules)
    {
        foreach ($rules as $rule => $rewrite) {
            if (strpos($rewrite, "embed=true") !== false) {
                unset($rules[$rule]);
            }
        }
        return $rules;
    }

    /**
     * Add security headers
     *
     * @return void
     */
    public function add_security_headers()
    {
        if (is_admin() || $GLOBALS["pagenow"] === "wp-login.php") {
            return;
        }

        if (!headers_sent()) {
            header_remove("server");
            header_remove("x-powered-by");

            header("X-Content-Type-Options: nosniff");
            header("X-Frame-Options: SAMEORIGIN");
            header("X-XSS-Protection: 1; mode=block");
            header("Referrer-Policy: strict-origin-when-cross-origin");
        }
    }

    /**
     * Remove sensitive headers via wp_headers filter
     *
     * @param array $headers HTTP headers
     * @return array
     */
    public function remove_sensitive_headers($headers)
    {
        unset($headers["server"]);
        unset($headers["x-powered-by"]);

        // Remove REST API Link header
        if (isset($headers["Link"])) {
            if (
                strpos($headers["Link"], 'rel="https://api.w.org/"') !== false
            ) {
                unset($headers["Link"]);
            }
        }

        return $headers;
    }

    /**
     * Optimize database
     *
     * @return void
     */
    public function optimize_database()
    {
        global $wpdb;

        // Note: SHOW TABLES is a static query, no need for prepare()
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Direct query required for maintenance operation: getting list of tables
        $tables = $wpdb->get_results("SHOW TABLES", ARRAY_N);
        foreach ($tables as $table) {
            $table_name = $table[0];
            if (strpos($table_name, $wpdb->prefix) === 0) {
                // Use %s instead of %i for backward compatibility (WP < 6.2)
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Direct query required for maintenance operation: OPTIMIZE TABLE
                $wpdb->query($wpdb->prepare("OPTIMIZE TABLE %s", $table_name));
            }
        }

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Direct query required for maintenance operation: bulk deletion of transients
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
                "_transient_%",
                "_site_transient_%",
            ),
        );

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Direct query required for maintenance operation: bulk deletion of expired transients
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
                "_transient_timeout_%",
                "_site_transient_timeout_%",
            ),
        );

        $this->delete_old_revisions();
    }

    /**
     * Delete old post revisions (older than 45 days)
     *
     * @return void
     */
    private function delete_old_revisions()
    {
        global $wpdb;

        $days_ago = 45;
        $cutoff_date = gmdate("Y-m-d H:i:s", strtotime("-$days_ago days"));

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $revisions = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'revision' AND post_date < %s",
                $cutoff_date,
            ),
        );

        if (!empty($revisions)) {
            $revision_ids = [];
            foreach ($revisions as $revision) {
                $revision_ids[] = $revision->ID;
            }

            $chunks = array_chunk($revision_ids, 100);
            foreach ($chunks as $chunk) {
                $placeholders_count = count($chunk);
                $placeholders = implode(
                    ",",
                    array_fill(0, $placeholders_count, "%d"),
                );

                foreach ($chunk as $post_id) {
                    $meta_keys = get_post_custom_keys($post_id);

                    if ($meta_keys) {
                        foreach ($meta_keys as $meta_key) {
                            delete_post_meta($post_id, $meta_key);
                        }
                    }
                }

                foreach ($chunk as $post_id) {
                    wp_delete_post($post_id, true);
                }
            }

            $total_deleted = count($revision_ids);
            if (defined("WP_DEBUG") && WP_DEBUG) {
                // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
                error_log(
                    "MyelophOne: Deleted $total_deleted post revisions older than $days_ago days",
                );
            }
        }
    }

    /**
     * Add plugin action links
     *
     * @param array $links Existing links
     * @return array
     */
    public function add_plugin_action_links($links)
    {
        $info_link = sprintf(
            '<a href="%s">%s</a>',
            admin_url("admin.php?page=myelophone-core-info"),
            __("Info", "myelophone-core"),
        );

        array_unshift($links, $info_link);

        return $links;
    }

    /**
     * Hide admin bar for all users when option is enabled
     *
     * @param bool $show Whether to show the admin bar
     * @return bool
     */
    public function hide_admin_bar($show)
    {
        if (is_admin()) {
            return $show;
        }

        return false;
    }

    /**
     * Handle jQuery optimization
     *
     * @return void
     */
    public function handle_jquery_optimization()
    {
        global $wp_scripts;

        // Move jQuery to footer
        if ($this->settings->get_option("move_jquery_footer") === "1") {
            if (isset($wp_scripts->registered["jquery"])) {
                $wp_scripts->registered["jquery"]->extra["group"] = 1;
            }
            if (isset($wp_scripts->registered["jquery-core"])) {
                $wp_scripts->registered["jquery-core"]->extra["group"] = 1;
            }
            if (isset($wp_scripts->registered["jquery-migrate"])) {
                $wp_scripts->registered["jquery-migrate"]->extra["group"] = 1;
            }
        }

        // Remove jQuery Migrate
        if ($this->settings->get_option("remove_jquery_migrate") === "1") {
            if (isset($wp_scripts->registered["jquery-migrate"])) {
                unset($wp_scripts->registered["jquery-migrate"]);
            }

            // Remove from dependencies
            foreach ($wp_scripts->registered as $script) {
                if (isset($script->deps) && is_array($script->deps)) {
                    $key = array_search("jquery-migrate", $script->deps);
                    if ($key !== false) {
                        unset($script->deps[$key]);
                    }
                }
            }
        }
    }

    /**
     * Add verification meta tags to head
     *
     * @return void
     */
    public function add_verification_meta_tags()
    {
        $verification_codes = [
            "google" => $this->settings->get_option("google_verification"),
            "yandex" => $this->settings->get_option("yandex_verification"),
            "bing" => $this->settings->get_option("bing_verification"),
            "baidu" => $this->settings->get_option("baidu_verification"),
            "alexa" => $this->settings->get_option("alexa_verification"),
            "pinterest" => $this->settings->get_option(
                "pinterest_verification",
            ),
            "facebook" => $this->settings->get_option(
                "facebook_domain_verification",
            ),
        ];

        foreach ($verification_codes as $engine => $code) {
            if (!empty($code)) {
                switch ($engine) {
                    case "google":
                        echo '<meta name="google-site-verification" content="' .
                            esc_attr($code) .
                            '" />' .
                            "\n";
                        break;
                    case "yandex":
                        echo '<meta name="yandex-verification" content="' .
                            esc_attr($code) .
                            '" />' .
                            "\n";
                        break;
                    case "bing":
                        echo '<meta name="msvalidate.01" content="' .
                            esc_attr($code) .
                            '" />' .
                            "\n";
                        break;
                    case "baidu":
                        echo '<meta name="baidu-site-verification" content="' .
                            esc_attr($code) .
                            '" />' .
                            "\n";
                        break;
                    case "alexa":
                        echo '<meta name="alexaVerifyID" content="' .
                            esc_attr($code) .
                            '" />' .
                            "\n";
                        break;
                    case "pinterest":
                        echo '<meta name="p:domain_verify" content="' .
                            esc_attr($code) .
                            '" />' .
                            "\n";
                        break;
                    case "facebook":
                        echo '<meta name="facebook-domain-verification" content="' .
                            esc_attr($code) .
                            '" />' .
                            "\n";
                        break;
                }
            }
        }
    }

    /**
     * Remove global styles and SVG filters from frontend
     *
     * @return void
     */
    private function remove_global_styles_frontend()
    {
        // Remove all global styles actions
        remove_action("wp_head", "wp_enqueue_global_styles", 1);
        remove_action("wp_enqueue_scripts", "wp_enqueue_global_styles");
        remove_action("wp_footer", "wp_enqueue_global_styles", 1);

        // Remove SVG filters completely
        remove_action("wp_body_open", "wp_global_styles_render_svg_filters");
        remove_action("wp_footer", "wp_global_styles_render_svg_filters");
        remove_action("admin_footer", "wp_global_styles_render_svg_filters");

        // Hook into wp_enqueue_scripts to remove all block-related styles
        add_action(
            "wp_enqueue_scripts",
            function () {
                // Remove classic theme styles
                wp_dequeue_style("classic-theme-styles");

                // Remove core block styles
                wp_dequeue_style("core-block-supports");
                wp_dequeue_style("wp-block-library");
                wp_dequeue_style("wp-block-library-theme");

                // Remove all styles with wp-block- prefix
                global $wp_styles;
                if (isset($wp_styles->registered)) {
                    foreach ($wp_styles->registered as $handle => $style) {
                        if (strpos($handle, "wp-block-") === 0) {
                            wp_dequeue_style($handle);
                            // Also remove inline data
                            wp_style_add_data($handle, "after", "");
                            wp_style_add_data($handle, "before", "");
                        }
                    }
                }
            },
            99,
        );

        add_action(
            "wp_footer",
            function () {
                wp_dequeue_style("core-block-supports");

                // Remove any remaining wp-block- styles
                global $wp_styles;
                if (isset($wp_styles->registered)) {
                    foreach ($wp_styles->registered as $handle => $style) {
                        if (strpos($handle, "wp-block-") === 0) {
                            wp_dequeue_style($handle);
                        }
                    }
                }
            },
            5,
        );
    }

    /**
     * Remove global styles and SVG filters from admin
     *
     * @return void
     */
    private function remove_global_styles_admin()
    {
        // Remove font faces from admin
        remove_action("admin_print_styles", "wp_print_font_faces", 50);

        // Remove block library styles from admin
        add_action("admin_enqueue_scripts", function () {
            wp_dequeue_style("wp-block-library");
            wp_dequeue_style("wp-block-library-theme");
            wp_dequeue_style("core-block-supports");

            // Remove all styles with wp-block- prefix in admin
            global $wp_styles;
            if (isset($wp_styles->registered)) {
                foreach ($wp_styles->registered as $handle => $style) {
                    if (strpos($handle, "wp-block-") === 0) {
                        wp_dequeue_style($handle);
                        // Also remove inline data
                        wp_style_add_data($handle, "after", "");
                        wp_style_add_data($handle, "before", "");
                    }
                }
            }
        });
    }

    /**
     * Remove inline styles from style tags
     *
     * @param string $tag    The link tag for the enqueued style
     * @param string $handle The style's registered handle
     * @param string $href   The stylesheet's source URL
     * @param string $media  The stylesheet's media attribute
     * @return string
     */
    public function remove_inline_styles_from_tags($tag, $handle, $href, $media)
    {
        $should_clean = false;

        if (strpos($handle, "wp-block-") === 0) {
            $should_clean = true;
        }

        $specific_handles = [
            "wp-block-library",
            "wp-block-library-theme",
            "core-block-supports",
            "classic-theme-styles",
            "global-styles",
        ];

        if (in_array($handle, $specific_handles)) {
            $should_clean = true;
        }

        if ($should_clean) {
            $tag = preg_replace('/\s*style="[^"]*"/', "", $tag);
            $tag = preg_replace("/<style[^>]*>.*?<\/style>/s", "", $tag);
            $tag = preg_replace('/\s*data-[^=]*="[^"]*"/', "", $tag);
        }

        return $tag;
    }

    /**
     * Start output buffering to remove inline styles
     *
     * @return void
     */
    public function start_inline_styles_removal()
    {
        ob_start([$this, "remove_inline_styles_from_output"]);
    }

    /**
     * End output buffering
     *
     * @return void
     */
    public function end_inline_styles_removal()
    {
        if (ob_get_length()) {
            ob_end_flush();
        }
    }

    /**
     * Remove inline styles from HTML output
     *
     * @param string $buffer The HTML output buffer
     * @return string
     */
    public function remove_inline_styles_from_output($buffer)
    {
        // Remove inline style attributes
        $buffer = preg_replace('/\s*style="[^"]*"/', "", $buffer);

        // Remove style tags that contain wp-block- or related styles
        $buffer = preg_replace(
            "/<style[^>]*>.*?wp-block-.*?<\/style>/s",
            "",
            $buffer,
        );
        $buffer = preg_replace(
            "/<style[^>]*>.*?block-library.*?<\/style>/s",
            "",
            $buffer,
        );
        $buffer = preg_replace(
            "/<style[^>]*>.*?global-styles.*?<\/style>/s",
            "",
            $buffer,
        );

        // Remove SVG filters
        $buffer = preg_replace(
            '/<svg[^>]*class="wp-block-.*?<\/svg>/s',
            "",
            $buffer,
        );
        $buffer = preg_replace("/<svg[^>]*>.*?<\/svg>/s", "", $buffer);

        return $buffer;
    }

    /**
     * Enable SVG upload support
     *
     * @param array $mimes Current mime types
     * @return array
     */
    public function enable_svg_upload($mimes)
    {
        $mimes["svg"] = "image/svg+xml";
        $mimes["svgz"] = "image/svg+xml";
        return $mimes;
    }

    /**
     * Check SVG filetype for security
     *
     * @param array $data File data
     * @param string $file File path
     * @param string $filename File name
     * @param array $mimes Mime types
     * @return array
     */
    public function check_svg_filetype($data, $file, $filename, $mimes)
    {
        $filetype = wp_check_filetype($filename, $mimes);

        if ($filetype["type"] === "image/svg+xml") {
            $svg_content = file_get_contents($file);

            $dangerous_patterns = [
                "/<script/i",
                "/javascript:/i",
                "/onload=/i",
                "/onerror=/i",
                "/onclick=/i",
                "/<iframe/i",
                "/<object/i",
                "/<embed/i",
            ];

            foreach ($dangerous_patterns as $pattern) {
                if (preg_match($pattern, $svg_content)) {
                    $data["ext"] = false;
                    $data["type"] = false;
                    break;
                }
            }
        }

        return $data;
    }

    /**
     * Add Google Fonts preconnect and dns-prefetch hints
     *
     * @return void
     */
    public function add_google_fonts_preload()
    {
        // Add preconnect for Google Fonts domains
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">' .
            "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' .
            "\n";

        // Add dns-prefetch for Google Fonts domains
        echo '<link rel="dns-prefetch" href="https://fonts.googleapis.com">' .
            "\n";
        echo '<link rel="dns-prefetch" href="https://fonts.gstatic.com">' .
            "\n";
    }

    /**
     * Prevent author enumeration scans
     *
     * @return void
     */
    public function prevent_author_enumeration()
    {
        if (is_admin()) {
            return;
        }

        $author_param = filter_input(
            INPUT_GET,
            "author",
            FILTER_SANITIZE_NUMBER_INT,
        );
        if ($author_param !== null && $author_param !== false) {
            $author_id = intval($author_param);
            if ($author_id > 0) {
                wp_safe_redirect(home_url(), 301);
                exit();
            }
        }

        if (is_author()) {
            wp_safe_redirect(home_url(), 301);
            exit();
        }
    }

    /**
     * Disable Dashicons for non-logged-in users
     *
     * @return void
     */
    public function disable_dashicons_frontend()
    {
        if (!is_user_logged_in()) {
            wp_dequeue_style("dashicons");
        }
    }

    /**
     * Disable self pingbacks
     *
     * @param array $links Array of pingback links (passed by reference)
     * @return void
     */
    public function disable_self_pingbacks(array &$links)
    {
        $home = get_option("home");
        foreach ($links as $l => $link) {
            if (0 === strpos($link, $home)) {
                unset($links[$l]);
            }
        }
    }

    /**
     * Remove website field from comment form
     *
     * @param array $fields Comment form fields
     * @return array Modified fields
     */
    public function remove_website_field_comments($fields)
    {
        if (isset($fields["url"])) {
            unset($fields["url"]);
        }
        return $fields;
    }

    /**
     * Hide specific login error messages
     *
     * @param string $error Login error message
     * @return string Generic error message
     */
    public function hide_login_errors($error)
    {
        $is_login_attempt = isset($_POST["log"]) && isset($_POST["pwd"]);

        $nonce_verified = false;
        if ($is_login_attempt && isset($_POST["_wpnonce"])) {
            $nonce_verified = wp_verify_nonce(
                sanitize_text_field(wp_unslash($_POST["_wpnonce"])),
                "log-in",
            );
        }

        if ($is_login_attempt && $nonce_verified && !empty($error)) {
            return esc_html__(
                "<strong>Error:</strong> Login error. Please try again.",
                "myelophone-core",
            );
        }

        return $error;
    }

    /**
     * Hook into login errors via wp_login_errors filter
     * This handles WP_Error objects for better control
     *
     * @param WP_Error $errors WP_Error object
     * @return WP_Error Modified WP_Error object
     */
    public function hide_login_wp_errors($errors)
    {
        $is_login_attempt = isset($_POST["log"]) && isset($_POST["pwd"]);

        $nonce_verified = false;
        if ($is_login_attempt && isset($_POST["_wpnonce"])) {
            $nonce_verified = wp_verify_nonce(
                sanitize_text_field(wp_unslash($_POST["_wpnonce"])),
                "log-in",
            );
        }

        if ($is_login_attempt && $nonce_verified && $errors->has_errors()) {
            $error_codes = $errors->get_error_codes();
            foreach ($error_codes as $code) {
                $errors->remove($code);
            }

            $errors->add(
                "login_error",
                esc_html__(
                    "<strong>Error:</strong> Login error. Please try again.",
                    "myelophone-core",
                ),
            );
        }

        return $errors;
    }

    /**
     * Hide authentication errors by always returning generic error
     * This is the most reliable way to hide specific login error details
     *
     * @param WP_User|WP_Error|null $user WP_User object, WP_Error, or null
     * @param string $username Username
     * @param string $password Password
     * @return WP_User|WP_Error WP_User on success, generic WP_Error on failure
     */
    public function hide_auth_errors($user, $username, $password)
    {
        $is_login_attempt = isset($_POST["log"]) && isset($_POST["pwd"]);

        $nonce_verified = false;
        if ($is_login_attempt && isset($_POST["_wpnonce"])) {
            $nonce_verified = wp_verify_nonce(
                sanitize_text_field(wp_unslash($_POST["_wpnonce"])),
                "log-in",
            );
        }

        if ($is_login_attempt && $nonce_verified && is_wp_error($user)) {
            $generic_error = new WP_Error(
                "authentication_failed",
                esc_html__(
                    "<strong>Error:</strong> Login error. Please try again.",
                    "myelophone-core",
                ),
            );
            return $generic_error;
        }

        return $user;
    }

    /**
     * Disable unnecessary image sizes
     *
     * @param array $sizes Image sizes
     * @return array Modified image sizes
     */
    public function disable_image_sizes($sizes)
    {
        return [];
    }

    /**
     * Disable attachment pages
     *
     * @return void
     */
    public function disable_attachment_pages()
    {
        global $post;
        if (is_attachment()) {
            $url = wp_get_attachment_url($post->ID);
            if ($url) {
                wp_safe_redirect($url, 301);
                exit();
            }
        }
    }

    /**
     * Auto empty trash
     *
     * @return void
     */
    public function auto_empty_trash()
    {
        if (!defined("EMPTY_TRASH_DAYS")) {
            define("EMPTY_TRASH_DAYS", 7);
        }
    }

    /**
     * Disable WooCommerce scripts on non-shop pages
     *
     * @return void
     */
    public function disable_woocommerce_non_shop()
    {
        if (!class_exists("WooCommerce")) {
            return;
        }

        if (
            !is_woocommerce() &&
            !is_cart() &&
            !is_checkout() &&
            !is_account_page()
        ) {
            // Dequeue WooCommerce styles
            wp_dequeue_style("woocommerce-general");
            wp_dequeue_style("woocommerce-layout");
            wp_dequeue_style("woocommerce-smallscreen");
            wp_dequeue_style("woocommerce_frontend_styles");
            wp_dequeue_style("woocommerce_fancybox_styles");
            wp_dequeue_style("woocommerce_chosen_styles");
            wp_dequeue_style("woocommerce_prettyPhoto_css");

            // Dequeue WooCommerce scripts
            wp_dequeue_script("wc-add-to-cart");
            wp_dequeue_script("wc-cart-fragments");
            wp_dequeue_script("woocommerce");
            wp_dequeue_script("wc-checkout");
            wp_dequeue_script("wc-add-to-cart-variation");
            wp_dequeue_script("wc-single-product");
            wp_dequeue_script("wc-cart");
            wp_dequeue_script("wc-chosen");
            wp_dequeue_script("woocommerce_widgets");
        }
    }

    /**
     * Maintenance mode
     *
     * @return void
     */
    public function maintenance_mode()
    {
        if (!current_user_can("manage_options") && !is_admin()) {

            $browser_lang = $this->get_browser_language();
            $locale = $this->get_available_locale($browser_lang);
            switch_to_locale($locale);

            $home_url = home_url("/");

            status_header(503);
            header("Retry-After: 3600");
            header("Content-Type: text/html; charset=utf-8");
            ?>
             <!DOCTYPE html>
             <html lang="<?php echo esc_attr($locale); ?>">
             <head>
                 <meta charset="UTF-8">
                 <meta name="viewport" content="width=device-width, initial-scale=1.0">
                 <title><?php echo esc_html__(
                     "Site Under Maintenance",
                     "myelophone-core",
                 ); ?></title>
             </head>
             <body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center;">
                 <div class="maintenance-container" style="background: white; padding: 3rem; border-radius: 12px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); text-align: center; max-width: 500px; width: 90%;">
                     <h1 class="maintenance-title" style="color: #333; margin: 0 0 1.5rem; font-size: 2rem; font-weight: 700;">
                         <?php echo esc_html__(
                             "Site Under Maintenance",
                             "myelophone-core",
                         ); ?>
                     </h1>
                     <p class="status-code" style="color: #764ba2; font-weight: 600; font-size: 1.1rem; margin: 1rem 0;">
                         <?php echo esc_html(
                             sprintf(
                                 /* translators: %s: Status code message */
                                 __("Status Code: %s", "myelophone-core"),
                                 "503 Service Unavailable",
                             ),
                         ); ?>
                     </p>
                     <p class="maintenance-message" style="color: #666; font-size: 1.1rem; line-height: 1.6; margin: 1.5rem 0;">
                         <?php echo esc_html__(
                             "We are currently performing scheduled maintenance. Please check back soon.",
                             "myelophone-core",
                         ); ?>
                     </p>
                     <a href="<?php echo esc_url($home_url); ?>"
                        class="maintenance-home-link"
                        style="display: inline-block; background: #667eea; color: white; text-decoration: none; padding: 1rem 2rem; border-radius: 50px; font-weight: 600; transition: all 0.3s ease; margin-top: 1rem;"
                        onmouseover="this.style.background='#5a67d8'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 20px rgba(0, 0, 0, 0.2)';"
                        onmouseout="this.style.background='#667eea'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                         <?php echo esc_html__(
                             "Return to Homepage",
                             "myelophone-core",
                         ); ?>
                     </a>
                 </div>
             </body>
             </html>
             <?php exit();
        }
    }

    /**
     * Get browser language from HTTP_ACCEPT_LANGUAGE header
     *
     * @return string Browser language code or empty string if not detected
     */
    private function get_browser_language()
    {
        if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
            // Sanitize and validate the input
            $accept_language = sanitize_text_field(
                wp_unslash($_SERVER["HTTP_ACCEPT_LANGUAGE"]),
            );
            $browser_langs = explode(",", $accept_language);
            if (!empty($browser_langs[0])) {
                $lang = explode(";", $browser_langs[0]);
                return trim(sanitize_text_field($lang[0]));
            }
        }
        return "";
    }

    /**
     * Get available locale based on browser language or fallback to site locale
     *
     * @param string $browser_lang Browser language code
     * @return string Available locale code
     */
    private function get_available_locale($browser_lang)
    {
        $site_locale = get_locale();

        if (empty($browser_lang)) {
            return $site_locale;
        }

        $normalized_lang = str_replace("-", "_", $browser_lang);

        $available_locales = $this->get_available_translations();

        if (in_array($normalized_lang, $available_locales)) {
            return $normalized_lang;
        }

        $lang_only = explode("_", $normalized_lang)[0];
        foreach ($available_locales as $locale) {
            if (strpos($locale, $lang_only . "_") === 0) {
                return $locale;
            }
        }

        return $site_locale;
    }

    /**
     * Get list of available translations for the plugin
     *
     * @return array List of available locale codes
     */
    private function get_available_translations()
    {
        $translations = [];
        $languages_dir = plugin_dir_path(dirname(__FILE__)) . "languages/";

        if (is_dir($languages_dir)) {
            $files = scandir($languages_dir);
            foreach ($files as $file) {
                if (
                    preg_match('/myelophone-core-(.+)\.mo$/', $file, $matches)
                ) {
                    $translations[] = $matches[1];
                }
            }
        }

        if (!in_array("en_US", $translations)) {
            $translations[] = "en_US";
        }

        return $translations;
    }

    /**
     * Force SSL
     *
     * @return void
     */
    public function force_ssl()
    {
        if (!is_ssl()) {
            $host = isset($_SERVER["HTTP_HOST"])
                ? sanitize_text_field(wp_unslash($_SERVER["HTTP_HOST"]))
                : "";
            $request_uri = isset($_SERVER["REQUEST_URI"])
                ? esc_url_raw(wp_unslash($_SERVER["REQUEST_URI"]))
                : "";

            if ($host && $request_uri) {
                wp_safe_redirect("https://" . $host . $request_uri, 301);
                exit();
            }
        }
    }

    /**
     * Enable classic widgets
     *
     * @return void
     */
    public function enable_classic_widgets()
    {
        add_filter("gutenberg_use_widgets_block_editor", "__return_false");
        add_filter("use_widgets_block_editor", "__return_false");
    }

    /**
     * Enable post cloning
     *
     * @return void
     */
    public function enable_post_cloning()
    {
        add_filter(
            "post_row_actions",
            [$this, "add_clone_link_to_posts"],
            10,
            2,
        );
        add_filter(
            "page_row_actions",
            [$this, "add_clone_link_to_posts"],
            10,
            2,
        );

        add_action("admin_action_meph_clone_post", [
            $this,
            "handle_clone_post",
        ]);
    }

    /**
     * Add clone link to post row actions
     *
     * @param array $actions Existing actions
     * @param WP_Post $post Post object
     * @return array Modified actions
     */
    public function add_clone_link_to_posts($actions, $post)
    {
        if (!current_user_can("edit_post", $post->ID)) {
            return $actions;
        }

        $post_type_object = get_post_type_object($post->post_type);
        if (!$post_type_object) {
            return $actions;
        }

        $clone_url = wp_nonce_url(
            add_query_arg(
                [
                    "action" => "meph_clone_post",
                    "post" => $post->ID,
                ],
                admin_url("admin.php"),
            ),
            "meph_clone_post_" . $post->ID,
        );

        $actions["clone"] = sprintf(
            '<a href="%s" aria-label="%s">%s</a>',
            esc_url($clone_url),
            esc_attr(
                sprintf(
                    // translators: %s: Post title
                    esc_html__('Clone "%s"', "myelophone-core"),
                    $post->post_title,
                ),
            ),
            esc_html__("Clone", "myelophone-core"),
        );

        return $actions;
    }

    /**
     * Handle post cloning
     *
     * @return void
     */
    public function handle_clone_post()
    {
        if (!isset($_GET["post"]) || !is_numeric($_GET["post"])) {
            wp_die(esc_html__("Invalid post ID.", "myelophone-core"));
        }

        $post_id = intval($_GET["post"]);

        $nonce = isset($_GET["_wpnonce"])
            ? sanitize_text_field(wp_unslash($_GET["_wpnonce"]))
            : "";
        if (
            empty($nonce) ||
            !wp_verify_nonce($nonce, "meph_clone_post_" . $post_id)
        ) {
            wp_die(esc_html__("Security check failed.", "myelophone-core"));
        }

        if (!current_user_can("edit_post", $post_id)) {
            wp_die(
                esc_html__(
                    "You do not have permission to clone this post.",
                    "myelophone-core",
                ),
            );
        }

        $post = get_post($post_id);
        if (!$post) {
            wp_die(esc_html__("Post not found.", "myelophone-core"));
        }

        $new_post_data = [
            "post_title" =>
                $post->post_title .
                " " .
                esc_html__("(Copy)", "myelophone-core"),
            "post_content" => $post->post_content,
            "post_excerpt" => $post->post_excerpt,
            "post_status" => "draft",
            "post_type" => $post->post_type,
            "post_author" => get_current_user_id(),
            "post_parent" => $post->post_parent,
            "menu_order" => $post->menu_order,
            "comment_status" => $post->comment_status,
            "ping_status" => $post->ping_status,
            "post_password" => $post->post_password,
        ];

        $new_post_id = wp_insert_post($new_post_data);

        if (is_wp_error($new_post_id)) {
            wp_die(esc_html($new_post_id->get_error_message()));
        }

        $taxonomies = get_object_taxonomies($post->post_type);
        foreach ($taxonomies as $taxonomy) {
            $post_terms = wp_get_object_terms($post_id, $taxonomy, [
                "fields" => "slugs",
            ]);
            wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
        }

        $post_meta = get_post_meta($post_id);
        foreach ($post_meta as $meta_key => $meta_values) {
            foreach ($meta_values as $meta_value) {
                if (in_array($meta_key, ["_edit_lock", "_edit_last"])) {
                    continue;
                }

                $meta_value = maybe_unserialize($meta_value);
                add_post_meta($new_post_id, $meta_key, $meta_value);
            }
        }

        $edit_url = get_edit_post_link($new_post_id, "raw");
        if ($edit_url) {
            wp_safe_redirect($edit_url);
            exit();
        } else {
            wp_safe_redirect(
                admin_url("edit.php?post_type=" . $post->post_type),
            );
            exit();
        }
    }
}
