<?php
/**
 * Settings management
 *
 * @package Myelophone Core
 */
if (!defined("ABSPATH")) {
    exit();
}

class Meph_Settings
{
    /**
     * Settings group for main options
     *
     * @var string
     */
    const SETTINGS_GROUP = "meph_core_settings_group";

    /**
     * Settings group for verification options
     *
     * @var string
     */
    const VERIFICATION_SETTINGS_GROUP = "meph_core_verification_group";

    /**
     * Settings page slug
     *
     * @var string
     */
    const SETTINGS_PAGE = "myelophone-core-info";

    /**
     * Option names mapping
     *
     * @var array
     */
    private $option_names = [
        "display_errors" => "meph_core_display_errors",
        "hide_wp_version" => "meph_core_hide_wp_version",
        "disable_emoji" => "meph_core_disable_emoji",
        "restrict_rest_api" => "meph_core_restrict_rest_api",
        "disable_xmlrpc" => "meph_core_disable_xmlrpc",
        "limit_revisions" => "meph_core_limit_revisions",
        "disable_comments" => "meph_core_disable_comments",
        "disable_heartbeat" => "meph_core_disable_heartbeat",
        "cleanup_head" => "meph_core_cleanup_head",
        "disable_embeds" => "meph_core_disable_embeds",
        "optimize_database" => "meph_core_optimize_database",
        "security_headers" => "meph_core_security_headers",
        "hide_admin_bar" => "meph_core_hide_admin_bar",
        "move_jquery_footer" => "meph_core_move_jquery_footer",
        "remove_jquery_migrate" => "meph_core_remove_jquery_migrate",
        "google_verification" => "meph_core_google_verification",
        "yandex_verification" => "meph_core_yandex_verification",
        "bing_verification" => "meph_core_bing_verification",
        "baidu_verification" => "meph_core_baidu_verification",
        "alexa_verification" => "meph_core_alexa_verification",
        "pinterest_verification" => "meph_core_pinterest_verification",
        "facebook_domain_verification" =>
            "meph_core_facebook_domain_verification",
        "cleanup_svg_filters" => "meph_core_cleanup_svg_filters",
        "disable_author_scans" => "meph_core_disable_author_scans",
        "enable_svg_safe" => "meph_core_enable_svg_safe",
        "google_fonts_preload" => "meph_core_google_fonts_preload",
        "disable_file_editor" => "meph_core_disable_file_editor",
        "disable_dashicons_frontend" => "meph_core_disable_dashicons_frontend",
        "disable_self_pingbacks" => "meph_core_disable_self_pingbacks",
        "remove_website_field_comments" =>
            "meph_core_remove_website_field_comments",
        "hide_login_errors" => "meph_core_hide_login_errors",
        "disable_image_sizes" => "meph_core_disable_image_sizes",
        "disable_attachment_pages" => "meph_core_disable_attachment_pages",
        "auto_empty_trash" => "meph_core_auto_empty_trash",
        "disable_woocommerce_non_shop" =>
            "meph_core_disable_woocommerce_non_shop",
        "maintenance_mode" => "meph_core_maintenance_mode",
        "force_ssl" => "meph_core_force_ssl",
        "require_admin_email_confirmation" =>
            "meph_core_require_admin_email_confirmation",
        "enable_classic_widgets" => "meph_core_enable_classic_widgets",
        "enable_post_cloning" => "meph_core_enable_post_cloning",
    ];

    /**
     * Detailed descriptions for each option
     *
     * @var array
     */
    private $option_descriptions = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->init_option_descriptions();
    }

    /**
     * Initialize option descriptions
     *
     * @return void
     */
    private function init_option_descriptions()
    {
        $this->option_descriptions = [
            "display_errors" => __(
                "Show PHP errors and warnings on the frontend for administrators only. Useful for debugging issues on your site.",
                "myelophone-core",
            ),
            "hide_wp_version" => __(
                "Remove WordPress version information from your site's HTML source code. This improves security by hiding which version you're using.",
                "myelophone-core",
            ),
            "disable_emoji" => __(
                "Remove WordPress emoji scripts and styles to reduce HTTP requests and improve page loading speed.",
                "myelophone-core",
            ),
            "restrict_rest_api" => __(
                "Limit REST API access to authenticated users only. Prevents unauthorized access to your site's data through the API.",
                "myelophone-core",
            ),
            "disable_xmlrpc" => __(
                "Completely disable XML-RPC to prevent DDoS attacks, brute force attempts, and other security vulnerabilities.",
                "myelophone-core",
            ),
            "limit_revisions" => __(
                "Limit the number of post revisions stored in the database to 5 per post. Reduces database size and improves performance.",
                "myelophone-core",
            ),
            "disable_comments" => __(
                "Disable comments globally on your site. Reduces spam, improves security, and reduces database load.",
                "myelophone-core",
            ),
            "disable_heartbeat" => __(
                "Disable WordPress Heartbeat API to reduce server load and improve performance, especially on shared hosting.",
                "myelophone-core",
            ),
            "cleanup_head" => __(
                "Remove unnecessary meta tags, links, and scripts from the WordPress head section. Reduces page size and improves loading speed.",
                "myelophone-core",
            ),
            "disable_embeds" => __(
                "Disable WordPress oEmbed scripts to prevent external sites from embedding your content and improve loading performance.",
                "myelophone-core",
            ),
            "optimize_database" => __(
                "Automatically removes post revisions older than 45 days on a monthly schedule. Also optimizes database tables and cleans up transients.",
                "myelophone-core",
            ),
            "security_headers" => __(
                "Add security headers like X-Content-Type-Options, X-Frame-Options, and X-XSS-Protection to improve site security.",
                "myelophone-core",
            ),
            "hide_admin_bar" => __(
                "Hide the WordPress admin bar on the frontend for all users except administrators. Provides a cleaner browsing experience for visitors.",
                "myelophone-core",
            ),
            "move_jquery_footer" => __(
                "Move jQuery and jQuery Migrate scripts from the header to the footer. Improves page loading speed by deferring JavaScript execution.",
                "myelophone-core",
            ),
            "remove_jquery_migrate" => __(
                "Remove jQuery Migrate script to reduce file size and improve performance. Only enable if your theme and plugins don't require it.",
                "myelophone-core",
            ),
            "google_verification" => __(
                "Google Search Console verification code (meta tag). Format: content=\"verification_code\"",
                "myelophone-core",
            ),
            "yandex_verification" => __(
                "Yandex.Webmaster verification code (meta tag). Format: content=\"verification_code\"",
                "myelophone-core",
            ),
            "bing_verification" => __(
                "Bing Webmaster Tools verification code (meta tag). Format: content=\"verification_code\"",
                "myelophone-core",
            ),
            "baidu_verification" => __(
                "Baidu Webmaster Tools verification code (meta tag). Format: content=\"verification_code\"",
                "myelophone-core",
            ),
            "alexa_verification" => __(
                "Alexa verification code (meta tag). Format: content=\"verification_code\"",
                "myelophone-core",
            ),
            "pinterest_verification" => __(
                "Pinterest verification code (meta tag). Format: content=\"verification_code\"",
                "myelophone-core",
            ),
            "facebook_domain_verification" => __(
                "Facebook Domain Verification code (meta tag). Format: content=\"verification_code\"",
                "myelophone-core",
            ),
            "cleanup_svg_filters" => __(
                "Remove unnecessary SVG filters and Global Styles added by WordPress to reduce page size and improve loading performance.",
                "myelophone-core",
            ),
            "disable_author_scans" => __(
                "Prevent author enumeration scans by redirecting requests like /?author=1 to homepage. Improves security by hiding author usernames.",
                "myelophone-core",
            ),
            "enable_svg_safe" => __(
                "Enable safe SVG upload support. Allows uploading SVG files with security sanitization to prevent XSS attacks.",
                "myelophone-core",
            ),
            "google_fonts_preload" => __(
                "Add preconnect and dns-prefetch hints for Google Fonts domains to improve loading performance and reduce connection time.",
                "myelophone-core",
            ),
            "disable_file_editor" => __(
                "Disable the file editor in WordPress admin to prevent unauthorized code modifications. This prevents users from editing theme and plugin files through the admin interface.",
                "myelophone-core",
            ),
            "require_admin_email_confirmation" => __(
                "Require email confirmation when administrators change their email address. Adds an extra layer of security to prevent unauthorized email changes.",
                "myelophone-core",
            ),
            "disable_dashicons_frontend" => __(
                "Disable Dashicons CSS loading on the frontend for non-logged-in users. Reduces page size and improves loading speed.",
                "myelophone-core",
            ),
            "disable_self_pingbacks" => __(
                "Disable self-pingbacks (trackbacks from your own site). Reduces spam and unnecessary database entries.",
                "myelophone-core",
            ),
            "remove_website_field_comments" => __(
                "Remove the 'Website' field from comment forms. Reduces spam and simplifies the commenting process.",
                "myelophone-core",
            ),
            "hide_login_errors" => __(
                "Hide specific login error messages. Instead of 'Invalid username' or 'Incorrect password', shows a generic error message for security.",
                "myelophone-core",
            ),
            "disable_image_sizes" => __(
                "Disable generation of unnecessary image sizes (thumbnail, medium, large, etc.) to save disk space and improve performance.",
                "myelophone-core",
            ),
            "disable_attachment_pages" => __(
                "Disable attachment pages and redirect media file URLs directly to the file itself. Improves SEO and user experience.",
                "myelophone-core",
            ),
            "auto_empty_trash" => __(
                "Automatically empty trash every 7 days instead of the default 30 days. Reduces database size and improves performance.",
                "myelophone-core",
            ),
            "disable_woocommerce_non_shop" => __(
                "Disable WooCommerce styles and scripts on non-shop pages. Improves loading speed on pages without WooCommerce functionality.",
                "myelophone-core",
            ),
            "maintenance_mode" => __(
                "Enable maintenance mode with a simple message for visitors. Administrators can still access the site normally.",
                "myelophone-core",
            ),
            "force_ssl" => __(
                "Force SSL/HTTPS on all pages. Redirects all HTTP requests to HTTPS for improved security and SEO.",
                "myelophone-core",
            ),
            "enable_classic_widgets" => __(
                "Enable classic widgets editor instead of block-based widgets. Restores the traditional WordPress widgets interface.",
                "myelophone-core",
            ),
            "enable_post_cloning" => __(
                "Enable post cloning feature. Adds a 'Clone' link in the admin for posts, pages, and custom post types to create duplicates.",
                "myelophone-core",
            ),
        ];
    }

    /**
     * Register settings
     *
     * @return void
     */
    public function register_settings()
    {
        add_filter("pre_update_option", [$this, "prevent_option_reset"], 10, 3);

        $verification_options = [
            "google_verification",
            "yandex_verification",
            "bing_verification",
            "baidu_verification",
            "alexa_verification",
            "pinterest_verification",
            "facebook_domain_verification",
        ];

        foreach ($this->option_names as $key => $option_name) {
            if (!in_array($key, $verification_options)) {
                register_setting(self::SETTINGS_GROUP, $option_name, [
                    "type" => "string",
                    "default" => "0",
                    "sanitize_callback" => [$this, "sanitize_checkbox"],
                ]);
            }
        }

        foreach ($this->option_names as $key => $option_name) {
            if (in_array($key, $verification_options)) {
                register_setting(
                    self::VERIFICATION_SETTINGS_GROUP,
                    $option_name,
                    [
                        "type" => "string",
                        "default" => "",
                        "sanitize_callback" => "sanitize_text_field",
                    ],
                );
            }
        }

        add_settings_section(
            "meph_general_section",
            __("General Settings", "myelophone-core"),
            [$this, "render_general_section"],
            self::SETTINGS_PAGE,
        );

        add_settings_section(
            "meph_performance_section",
            __("Performance Optimization", "myelophone-core"),
            [$this, "render_performance_section"],
            self::SETTINGS_PAGE,
        );

        add_settings_section(
            "meph_security_section",
            __("Security Enhancements", "myelophone-core"),
            [$this, "render_security_section"],
            self::SETTINGS_PAGE,
        );

        add_settings_section(
            "meph_maintenance_section",
            __("Maintenance & Cleanup", "myelophone-core"),
            [$this, "render_maintenance_section"],
            self::SETTINGS_PAGE,
        );

        add_settings_field(
            "meph_display_errors",
            "",
            [$this, "render_display_errors_field"],
            self::SETTINGS_PAGE,
            "meph_general_section",
        );

        add_settings_field(
            "meph_hide_wp_version",
            "",
            [$this, "render_hide_wp_version_field"],
            self::SETTINGS_PAGE,
            "meph_general_section",
        );

        add_settings_field(
            "meph_disable_emoji",
            "",
            [$this, "render_disable_emoji_field"],
            self::SETTINGS_PAGE,
            "meph_general_section",
        );

        add_settings_field(
            "meph_restrict_rest_api",
            "",
            [$this, "render_restrict_rest_api_field"],
            self::SETTINGS_PAGE,
            "meph_general_section",
        );

        add_settings_field(
            "meph_disable_heartbeat",
            "",
            [$this, "render_disable_heartbeat_field"],
            self::SETTINGS_PAGE,
            "meph_performance_section",
        );

        add_settings_field(
            "meph_disable_embeds",
            "",
            [$this, "render_disable_embeds_field"],
            self::SETTINGS_PAGE,
            "meph_performance_section",
        );

        add_settings_field(
            "meph_cleanup_head",
            "",
            [$this, "render_cleanup_head_field"],
            self::SETTINGS_PAGE,
            "meph_performance_section",
        );

        add_settings_field(
            "meph_disable_xmlrpc",
            "",
            [$this, "render_disable_xmlrpc_field"],
            self::SETTINGS_PAGE,
            "meph_security_section",
        );

        add_settings_field(
            "meph_security_headers",
            "",
            [$this, "render_security_headers_field"],
            self::SETTINGS_PAGE,
            "meph_security_section",
        );

        add_settings_field(
            "meph_disable_author_scans",
            "",
            [$this, "render_disable_author_scans_field"],
            self::SETTINGS_PAGE,
            "meph_security_section",
        );

        add_settings_field(
            "meph_limit_revisions",
            "",
            [$this, "render_limit_revisions_field"],
            self::SETTINGS_PAGE,
            "meph_maintenance_section",
        );

        add_settings_field(
            "meph_disable_comments",
            "",
            [$this, "render_disable_comments_field"],
            self::SETTINGS_PAGE,
            "meph_maintenance_section",
        );

        add_settings_field(
            "meph_optimize_database",
            "",
            [$this, "render_optimize_database_field"],
            self::SETTINGS_PAGE,
            "meph_maintenance_section",
        );

        add_settings_field(
            "meph_hide_admin_bar",
            "",
            [$this, "render_hide_admin_bar_field"],
            self::SETTINGS_PAGE,
            "meph_general_section",
        );

        add_settings_field(
            "meph_move_jquery_footer",
            "",
            [$this, "render_move_jquery_footer_field"],
            self::SETTINGS_PAGE,
            "meph_performance_section",
        );

        add_settings_field(
            "meph_remove_jquery_migrate",
            "",
            [$this, "render_remove_jquery_migrate_field"],
            self::SETTINGS_PAGE,
            "meph_performance_section",
        );

        add_settings_field(
            "meph_cleanup_svg_filters",
            "",
            [$this, "render_cleanup_svg_filters_field"],
            self::SETTINGS_PAGE,
            "meph_performance_section",
        );

        add_settings_field(
            "meph_enable_svg_safe",
            "",
            [$this, "render_enable_svg_safe_field"],
            self::SETTINGS_PAGE,
            "meph_security_section",
        );

        add_settings_field(
            "meph_core_disable_file_editor",
            "",
            [$this, "render_disable_file_editor_field"],
            self::SETTINGS_PAGE,
            "meph_security_section",
        );

        add_settings_field(
            "meph_core_require_admin_email_confirmation",
            "",
            [$this, "render_require_admin_email_confirmation_field"],
            self::SETTINGS_PAGE,
            "meph_security_section",
        );

        add_settings_field(
            "meph_google_fonts_preload",
            "",
            [$this, "render_google_fonts_preload_field"],
            self::SETTINGS_PAGE,
            "meph_performance_section",
        );

        add_settings_section(
            "meph_verification_section",
            __("Search Engine Verification", "myelophone-core"),
            [$this, "render_verification_section"],
            self::SETTINGS_PAGE,
        );

        add_settings_field(
            "meph_google_verification",
            __("Google", "myelophone-core"),
            [$this, "render_google_verification_field"],
            self::SETTINGS_PAGE,
            "meph_verification_section",
        );

        add_settings_field(
            "meph_yandex_verification",
            __("Yandex", "myelophone-core"),
            [$this, "render_yandex_verification_field"],
            self::SETTINGS_PAGE,
            "meph_verification_section",
        );

        add_settings_field(
            "meph_bing_verification",
            __("Bing", "myelophone-core"),
            [$this, "render_bing_verification_field"],
            self::SETTINGS_PAGE,
            "meph_verification_section",
        );

        add_settings_field(
            "meph_baidu_verification",
            __("Baidu", "myelophone-core"),
            [$this, "render_baidu_verification_field"],
            self::SETTINGS_PAGE,
            "meph_verification_section",
        );

        add_settings_field(
            "meph_alexa_verification",
            __("Alexa", "myelophone-core"),
            [$this, "render_alexa_verification_field"],
            self::SETTINGS_PAGE,
            "meph_verification_section",
        );

        add_settings_field(
            "meph_pinterest_verification",
            __("Pinterest", "myelophone-core"),
            [$this, "render_pinterest_verification_field"],
            self::SETTINGS_PAGE,
            "meph_verification_section",
        );

        add_settings_field(
            "meph_facebook_domain_verification",
            __("Facebook", "myelophone-core"),
            [$this, "render_facebook_domain_verification_field"],
            self::SETTINGS_PAGE,
            "meph_verification_section",
        );

        add_settings_field(
            "meph_disable_dashicons_frontend",
            "",
            [$this, "render_disable_dashicons_frontend_field"],
            self::SETTINGS_PAGE,
            "meph_performance_section",
        );

        add_settings_field(
            "meph_disable_image_sizes",
            "",
            [$this, "render_disable_image_sizes_field"],
            self::SETTINGS_PAGE,
            "meph_performance_section",
        );

        add_settings_field(
            "meph_disable_woocommerce_non_shop",
            "",
            [$this, "render_disable_woocommerce_non_shop_field"],
            self::SETTINGS_PAGE,
            "meph_performance_section",
        );

        add_settings_field(
            "meph_disable_self_pingbacks",
            "",
            [$this, "render_disable_self_pingbacks_field"],
            self::SETTINGS_PAGE,
            "meph_security_section",
        );

        add_settings_field(
            "meph_remove_website_field_comments",
            "",
            [$this, "render_remove_website_field_comments_field"],
            self::SETTINGS_PAGE,
            "meph_security_section",
        );

        add_settings_field(
            "meph_hide_login_errors",
            "",
            [$this, "render_hide_login_errors_field"],
            self::SETTINGS_PAGE,
            "meph_security_section",
        );

        add_settings_field(
            "meph_disable_attachment_pages",
            "",
            [$this, "render_disable_attachment_pages_field"],
            self::SETTINGS_PAGE,
            "meph_security_section",
        );

        add_settings_field(
            "meph_force_ssl",
            "",
            [$this, "render_force_ssl_field"],
            self::SETTINGS_PAGE,
            "meph_security_section",
        );

        add_settings_field(
            "meph_auto_empty_trash",
            "",
            [$this, "render_auto_empty_trash_field"],
            self::SETTINGS_PAGE,
            "meph_maintenance_section",
        );

        add_settings_field(
            "meph_maintenance_mode",
            "",
            [$this, "render_maintenance_mode_field"],
            self::SETTINGS_PAGE,
            "meph_maintenance_section",
        );

        add_settings_field(
            "meph_enable_classic_widgets",
            "",
            [$this, "render_enable_classic_widgets_field"],
            self::SETTINGS_PAGE,
            "meph_general_section",
        );

        add_settings_field(
            "meph_enable_post_cloning",
            "",
            [$this, "render_enable_post_cloning_field"],
            self::SETTINGS_PAGE,
            "meph_general_section",
        );
    }

    /**
     * Sanitize checkbox value
     *
     * @param string $value Checkbox value
     * @return string
     */
    public function sanitize_checkbox($value)
    {
        return $value === "1" ? "1" : "0";
    }

    /**
     * Prevent checkbox options from being reset when not in POST
     * This fixes the issue where verification form resets other options
     *
     * @param mixed $value New value
     * @param string $option Option name
     * @param mixed $old_value Old value
     * @return mixed
     */
    public function prevent_option_reset($value, $option, $old_value)
    {
        $checkbox_options = [
            "meph_core_display_errors",
            "meph_core_hide_wp_version",
            "meph_core_disable_emoji",
            "meph_core_restrict_rest_api",
            "meph_core_disable_xmlrpc",
            "meph_core_limit_revisions",
            "meph_core_disable_comments",
            "meph_core_disable_heartbeat",
            "meph_core_cleanup_head",
            "meph_core_disable_embeds",
            "meph_core_optimize_database",
            "meph_core_security_headers",
            "meph_core_hide_admin_bar",
            "meph_core_move_jquery_footer",
            "meph_core_remove_jquery_migrate",
            "meph_core_cleanup_svg_filters",
            "meph_core_disable_author_scans",
        ];

        if (in_array($option, $checkbox_options) && $value === "") {
            if (isset($_POST["option_page"])) {
                if (!isset($_POST["_wpnonce"])) {
                    return $value;
                }

                $nonce = sanitize_text_field(wp_unslash($_POST["_wpnonce"]));
                $submitted_group = sanitize_text_field(
                    wp_unslash($_POST["option_page"]),
                );

                if (!wp_verify_nonce($nonce, $submitted_group . "-options")) {
                    return $value;
                }

                if ($submitted_group === Meph_Settings::SETTINGS_GROUP) {
                    return $value;
                } elseif (
                    $submitted_group ===
                    Meph_Settings::VERIFICATION_SETTINGS_GROUP
                ) {
                    return $old_value;
                }
            }
        }

        return $value;
    }

    /**
     * Render general section
     *
     * @return void
     */
    public function render_general_section()
    {
        echo "<p>" .
            esc_html__(
                "Configure general website settings and basic optimizations.",
                "myelophone-core",
            ) .
            "</p>";
    }

    /**
     * Render performance section
     *
     * @return void
     */
    public function render_performance_section()
    {
        echo "<p>" .
            esc_html__(
                "Optimize website performance and loading speed.",
                "myelophone-core",
            ) .
            "</p>";
    }

    /**
     * Render security section
     *
     * @return void
     */
    public function render_security_section()
    {
        echo "<p>" .
            esc_html__(
                "Enhance website security and protect against common threats.",
                "myelophone-core",
            ) .
            "</p>";
    }

    /**
     * Render maintenance section
     *
     * @return void
     */
    public function render_maintenance_section()
    {
        echo "<p>" .
            esc_html__(
                "Cleanup and maintenance options to keep your site running smoothly.",
                "myelophone-core",
            ) .
            "</p>";
    }

    /**
     * Render display errors field
     *
     * @return void
     */
    public function render_display_errors_field()
    {
        $value = get_option($this->option_names["display_errors"], "0");
        $this->render_switch_field(
            $this->option_names["display_errors"],
            $value,
        );
    }

    /**
     * Render hide WP version field
     *
     * @return void
     */
    public function render_hide_wp_version_field()
    {
        $value = get_option($this->option_names["hide_wp_version"], "0");
        $this->render_switch_field(
            $this->option_names["hide_wp_version"],
            $value,
        );
    }

    /**
     * Render disable emoji field
     *
     * @return void
     */
    public function render_disable_emoji_field()
    {
        $value = get_option($this->option_names["disable_emoji"], "0");
        $this->render_switch_field(
            $this->option_names["disable_emoji"],
            $value,
        );
    }

    /**
     * Render restrict REST API field
     *
     * @return void
     */
    public function render_restrict_rest_api_field()
    {
        $value = get_option($this->option_names["restrict_rest_api"], "0");
        $this->render_switch_field(
            $this->option_names["restrict_rest_api"],
            $value,
        );
    }

    /**
     * Render disable XML-RPC field
     *
     * @return void
     */
    public function render_disable_xmlrpc_field()
    {
        $value = get_option($this->option_names["disable_xmlrpc"], "0");
        $this->render_switch_field(
            $this->option_names["disable_xmlrpc"],
            $value,
        );
    }

    /**
     * Render limit revisions field
     *
     * @return void
     */
    public function render_limit_revisions_field()
    {
        $value = get_option($this->option_names["limit_revisions"], "0");
        $this->render_switch_field(
            $this->option_names["limit_revisions"],
            $value,
        );
    }

    /**
     * Render disable comments field
     *
     * @return void
     */
    public function render_disable_comments_field()
    {
        $value = get_option($this->option_names["disable_comments"], "0");
        $this->render_switch_field(
            $this->option_names["disable_comments"],
            $value,
        );
    }

    /**
     * Render disable heartbeat field
     *
     * @return void
     */
    public function render_disable_heartbeat_field()
    {
        $value = get_option($this->option_names["disable_heartbeat"], "0");
        $this->render_switch_field(
            $this->option_names["disable_heartbeat"],
            $value,
        );
    }

    /**
     * Render cleanup head field
     *
     * @return void
     */
    public function render_cleanup_head_field()
    {
        $value = get_option($this->option_names["cleanup_head"], "0");
        $this->render_switch_field($this->option_names["cleanup_head"], $value);
    }

    /**
     * Render disable embeds field
     *
     * @return void
     */
    public function render_disable_embeds_field()
    {
        $value = get_option($this->option_names["disable_embeds"], "0");
        $this->render_switch_field(
            $this->option_names["disable_embeds"],
            $value,
        );
    }

    /**
     * Render optimize database field
     *
     * @return void
     */
    public function render_optimize_database_field()
    {
        $value = get_option($this->option_names["optimize_database"], "0");
        $this->render_switch_field(
            $this->option_names["optimize_database"],
            $value,
        );
    }

    /**
     * Render security headers field
     *
     * @return void
     */
    public function render_security_headers_field()
    {
        $value = get_option($this->option_names["security_headers"], "0");
        $this->render_switch_field(
            $this->option_names["security_headers"],
            $value,
        );
    }

    /**
     * Render switch field
     *
     * @param string $name Field name
     * @param string $value Field value
     * @param string $icon Optional icon
     * @return void
     */
    private function render_switch_field($name, $value, $icon = "")
    {
        $field_icon = $icon ?: "";

        $option_key = str_replace("meph_core_", "", $name);

        $field_titles = [
            "meph_core_display_errors" => __(
                "Display PHP Errors",
                "myelophone-core",
            ),
            "meph_core_hide_wp_version" => __(
                "Hide WordPress Version",
                "myelophone-core",
            ),
            "meph_core_disable_emoji" => __("Disable Emoji", "myelophone-core"),
            "meph_core_restrict_rest_api" => __(
                "Restrict REST API",
                "myelophone-core",
            ),
            "meph_core_disable_xmlrpc" => __(
                "Disable XML-RPC",
                "myelophone-core",
            ),
            "meph_core_limit_revisions" => __(
                "Limit Post Revisions",
                "myelophone-core",
            ),
            "meph_core_disable_comments" => __(
                "Disable Comments",
                "myelophone-core",
            ),
            "meph_core_disable_heartbeat" => __(
                "Disable Heartbeat API",
                "myelophone-core",
            ),
            "meph_core_cleanup_head" => __(
                "Cleanup WP Head",
                "myelophone-core",
            ),
            "meph_core_disable_embeds" => __(
                "Disable Embeds",
                "myelophone-core",
            ),
            "meph_core_optimize_database" => __(
                "Remove old revisions",
                "myelophone-core",
            ),
            "meph_core_security_headers" => __(
                "Security Headers",
                "myelophone-core",
            ),
            "meph_core_hide_admin_bar" => __(
                "Hide Admin Bar",
                "myelophone-core",
            ),
            "meph_core_move_jquery_footer" => __(
                "Move jQuery to Footer",
                "myelophone-core",
            ),
            "meph_core_remove_jquery_migrate" => __(
                "Remove jQuery Migrate",
                "myelophone-core",
            ),
            "meph_core_cleanup_svg_filters" => __(
                "Remove SVG filters and Global Styles",
                "myelophone-core",
            ),
            "meph_core_disable_author_scans" => __(
                "Prevent author enumeration scans",
                "myelophone-core",
            ),
            "meph_core_enable_svg_safe" => __(
                "Enable Safe SVG",
                "myelophone-core",
            ),
            "meph_core_google_fonts_preload" => __(
                "Google Fonts Preload",
                "myelophone-core",
            ),
            "meph_core_disable_file_editor" => __(
                "Disable File Editor",
                "myelophone-core",
            ),
            "meph_core_require_admin_email_confirmation" => __(
                "Require Admin Email Confirmation",
                "myelophone-core",
            ),
            "meph_core_disable_dashicons_frontend" => __(
                "Disable Dashicons Frontend",
                "myelophone-core",
            ),
            "meph_core_disable_self_pingbacks" => __(
                "Disable Self Pingbacks",
                "myelophone-core",
            ),
            "meph_core_remove_website_field_comments" => __(
                "Remove Website Field in Comments",
                "myelophone-core",
            ),
            "meph_core_hide_login_errors" => __(
                "Hide Login Errors",
                "myelophone-core",
            ),
            "meph_core_disable_image_sizes" => __(
                "Disable Image Sizes",
                "myelophone-core",
            ),
            "meph_core_disable_attachment_pages" => __(
                "Disable Attachment Pages",
                "myelophone-core",
            ),
            "meph_core_auto_empty_trash" => __(
                "Auto Empty Trash",
                "myelophone-core",
            ),
            "meph_core_disable_woocommerce_non_shop" => __(
                "Disable WooCommerce on Non-Shop Pages",
                "myelophone-core",
            ),
            "meph_core_maintenance_mode" => __(
                "Maintenance Mode",
                "myelophone-core",
            ),
            "meph_core_force_ssl" => __("Force SSL", "myelophone-core"),
            "meph_core_enable_classic_widgets" => __(
                "Enable Classic Widgets",
                "myelophone-core",
            ),
            "meph_core_enable_post_cloning" => __(
                "Enable Post Cloning",
                "myelophone-core",
            ),
        ];

        $title = $field_titles[$name] ?? $name;
        $description = $this->option_descriptions[$option_key] ?? "";
        ?>
        <label class="meph-switch-label">
            <span class="meph-switch">
                <input type="checkbox"
                       name="<?php echo esc_attr($name); ?>"
                       value="1"
                       <?php checked($value, "1"); ?>>
                <span class="meph-slider"></span>
            </span>
            <span class="meph-switch-content">
                <div class="meph-switch-title">
                    <?php if ($field_icon): ?>
                        <span class="meph-switch-icon"><?php echo esc_html(
                            $field_icon,
                        ); ?></span>
                    <?php endif; ?>
                    <span class="meph-switch-text">
                        <?php echo esc_html($title); ?>
                    </span>
                    <span class="meph-switch-status">
                        <?php echo $value === "1"
                            ? esc_html__("Enabled", "myelophone-core")
                            : esc_html__("Disabled", "myelophone-core"); ?>
                    </span>
                </div>
                <?php if ($description): ?>
                    <p class="meph-switch-description">
                        <?php echo esc_html($description); ?>
                    </p>
                <?php endif; ?>
            </span>
        </label>
        <?php
    }

    /**
     * Get option value
     *
     * @param string $key Option key
     * @return string
     */
    public function get_option($key)
    {
        if (!isset($this->option_names[$key])) {
            return "0";
        }

        return get_option($this->option_names[$key], "0");
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function get_all_options()
    {
        $options = [];

        foreach ($this->option_names as $key => $option_name) {
            $options[$key] = get_option($option_name, "0");
        }

        return $options;
    }

    /**
     * Render hide admin bar field
     *
     * @return void
     */
    public function render_hide_admin_bar_field()
    {
        $value = get_option($this->option_names["hide_admin_bar"], "0");
        $this->render_switch_field(
            $this->option_names["hide_admin_bar"],
            $value,
        );
    }

    /**
     * Render move jQuery to footer field
     *
     * @return void
     */
    public function render_move_jquery_footer_field()
    {
        $value = get_option($this->option_names["move_jquery_footer"], "0");
        $this->render_switch_field(
            $this->option_names["move_jquery_footer"],
            $value,
        );
    }

    /**
     * Render remove jQuery migrate field
     *
     * @return void
     */
    public function render_remove_jquery_migrate_field()
    {
        $value = get_option($this->option_names["remove_jquery_migrate"], "0");
        $this->render_switch_field(
            $this->option_names["remove_jquery_migrate"],
            $value,
        );
    }

    /**
     * Render verification section
     *
     * @return void
     */
    public function render_verification_section()
    {
        // Empty section - description is already shown in render_verification()
    }

    /**
     * Render Google verification field
     *
     * @return void
     */
    public function render_google_verification_field()
    {
        $value = get_option($this->option_names["google_verification"], "");
        $this->render_text_field(
            $this->option_names["google_verification"],
            $value,
            __("Google Search Console verification code", "myelophone-core"),
            __("Example: ABCDEFG1234567", "myelophone-core"),
        );
    }

    /**
     * Render Yandex verification field
     *
     * @return void
     */
    public function render_yandex_verification_field()
    {
        $value = get_option($this->option_names["yandex_verification"], "");
        $this->render_text_field(
            $this->option_names["yandex_verification"],
            $value,
            __("Yandex.Webmaster verification code", "myelophone-core"),
            __("Example: 1234567890abcdef", "myelophone-core"),
        );
    }

    /**
     * Render Bing verification field
     *
     * @return void
     */
    public function render_bing_verification_field()
    {
        $value = get_option($this->option_names["bing_verification"], "");
        $this->render_text_field(
            $this->option_names["bing_verification"],
            $value,
            __("Bing Webmaster Tools verification code", "myelophone-core"),
            __("Example: ABCDEFGHIJKLMNOP", "myelophone-core"),
        );
    }

    /**
     * Render Baidu verification field
     *
     * @return void
     */
    public function render_baidu_verification_field()
    {
        $value = get_option($this->option_names["baidu_verification"], "");
        $this->render_text_field(
            $this->option_names["baidu_verification"],
            $value,
            __("Baidu Webmaster Tools verification code", "myelophone-core"),
            __("Example: abcdef123456", "myelophone-core"),
        );
    }

    /**
     * Render Alexa verification field
     *
     * @return void
     */
    public function render_alexa_verification_field()
    {
        $value = get_option($this->option_names["alexa_verification"], "");
        $this->render_text_field(
            $this->option_names["alexa_verification"],
            $value,
            __("Alexa verification code", "myelophone-core"),
            __("Example: abcdef123456", "myelophone-core"),
        );
    }

    /**
     * Render Pinterest verification field
     *
     * @return void
     */
    public function render_pinterest_verification_field()
    {
        $value = get_option($this->option_names["pinterest_verification"], "");
        $this->render_text_field(
            $this->option_names["pinterest_verification"],
            $value,
            __("Pinterest verification code", "myelophone-core"),
            __("Example: abcdef1234567890", "myelophone-core"),
        );
    }

    /**
     * Render Facebook domain verification field
     *
     * @return void
     */
    public function render_facebook_domain_verification_field()
    {
        $value = get_option(
            $this->option_names["facebook_domain_verification"],
            "",
        );
        $this->render_text_field(
            $this->option_names["facebook_domain_verification"],
            $value,
            __("Facebook Domain Verification code", "myelophone-core"),
            __("Example: abcdef1234567890", "myelophone-core"),
        );
    }

    /**
     * Render cleanup SVG filters field
     *
     * @return void
     */
    public function render_cleanup_svg_filters_field()
    {
        $value = get_option($this->option_names["cleanup_svg_filters"], "0");
        $this->render_switch_field(
            $this->option_names["cleanup_svg_filters"],
            $value,
        );
    }

    /**
     * Render disable author scans field
     *
     * @return void
     */
    public function render_disable_author_scans_field()
    {
        $value = get_option($this->option_names["disable_author_scans"], "0");
        $this->render_switch_field(
            $this->option_names["disable_author_scans"],
            $value,
        );
    }

    /**
     * Render text field
     *
     * @param string $name Field name
     * @param string $value Field value
     * @param string $label Field label
     * @param string $placeholder Field placeholder
     * @return void
     */
    private function render_text_field(
        $name,
        $value,
        $label = "",
        $placeholder = "",
    ) {
        ?>
        <div class="meph-text-field">
            <label for="<?php echo esc_attr($name); ?>" class="meph-text-label">
                <?php echo esc_html($label); ?>
            </label>
            <input type="text"
                   id="<?php echo esc_attr($name); ?>"
                   name="<?php echo esc_attr($name); ?>"
                   value="<?php echo esc_attr($value); ?>"
                   placeholder="<?php echo esc_attr($placeholder); ?>"
                   class="regular-text">
            <p class="description">
                <?php
                $option_key = str_replace("meph_core_", "", $name);
                echo esc_html($this->option_descriptions[$option_key] ?? "");?>
            </p>
        </div>
        <?php
    }

    /**
     * Render enable SVG safe field
     *
     * @return void
     */
    public function render_enable_svg_safe_field()
    {
        $value = get_option($this->option_names["enable_svg_safe"], "0");
        $this->render_switch_field(
            $this->option_names["enable_svg_safe"],
            $value,
        );
    }

    /**
     * Render Google Fonts preload field
     *
     * @return void
     */
    public function render_google_fonts_preload_field()
    {
        $value = get_option($this->option_names["google_fonts_preload"], "0");
        $this->render_switch_field(
            $this->option_names["google_fonts_preload"],
            $value,
        );
    }

    /**
     * Render disable file editor field
     *
     * @return void
     */
    public function render_disable_file_editor_field()
    {
        $value = get_option($this->option_names["disable_file_editor"], "0");
        $this->render_switch_field(
            $this->option_names["disable_file_editor"],
            $value,
        );
    }

    /**
     * Render require admin email confirmation field
     *
     * @return void
     */
    public function render_require_admin_email_confirmation_field()
    {
        $value = get_option(
            $this->option_names["require_admin_email_confirmation"],
            "0",
        );
        $this->render_switch_field(
            $this->option_names["require_admin_email_confirmation"],
            $value,
        );
    }

    public function render_disable_dashicons_frontend_field()
    {
        $value = get_option(
            $this->option_names["disable_dashicons_frontend"],
            "0",
        );
        $this->render_switch_field(
            $this->option_names["disable_dashicons_frontend"],
            $value,
        );
    }

    public function render_disable_self_pingbacks_field()
    {
        $value = get_option($this->option_names["disable_self_pingbacks"], "0");
        $this->render_switch_field(
            $this->option_names["disable_self_pingbacks"],
            $value,
        );
    }

    public function render_remove_website_field_comments_field()
    {
        $value = get_option(
            $this->option_names["remove_website_field_comments"],
            "0",
        );
        $this->render_switch_field(
            $this->option_names["remove_website_field_comments"],
            $value,
        );
    }

    public function render_hide_login_errors_field()
    {
        $value = get_option($this->option_names["hide_login_errors"], "0");
        $this->render_switch_field(
            $this->option_names["hide_login_errors"],
            $value,
        );
    }

    public function render_disable_image_sizes_field()
    {
        $value = get_option($this->option_names["disable_image_sizes"], "0");
        $this->render_switch_field(
            $this->option_names["disable_image_sizes"],
            $value,
        );
    }

    public function render_disable_attachment_pages_field()
    {
        $value = get_option(
            $this->option_names["disable_attachment_pages"],
            "0",
        );
        $this->render_switch_field(
            $this->option_names["disable_attachment_pages"],
            $value,
        );
    }

    public function render_auto_empty_trash_field()
    {
        $value = get_option($this->option_names["auto_empty_trash"], "0");
        $this->render_switch_field(
            $this->option_names["auto_empty_trash"],
            $value,
        );
    }

    public function render_disable_woocommerce_non_shop_field()
    {
        $value = get_option(
            $this->option_names["disable_woocommerce_non_shop"],
            "0",
        );
        $this->render_switch_field(
            $this->option_names["disable_woocommerce_non_shop"],
            $value,
        );
    }

    public function render_maintenance_mode_field()
    {
        $value = get_option($this->option_names["maintenance_mode"], "0");
        $this->render_switch_field(
            $this->option_names["maintenance_mode"],
            $value,
        );
    }

    public function render_force_ssl_field()
    {
        $value = get_option($this->option_names["force_ssl"], "0");
        $this->render_switch_field($this->option_names["force_ssl"], $value);
    }

    public function render_enable_classic_widgets_field()
    {
        $value = get_option($this->option_names["enable_classic_widgets"], "0");
        $this->render_switch_field(
            $this->option_names["enable_classic_widgets"],
            $value,
        );
    }

    public function render_enable_post_cloning_field()
    {
        $value = get_option($this->option_names["enable_post_cloning"], "0");
        $this->render_switch_field(
            $this->option_names["enable_post_cloning"],
            $value,
        );
    }
}
