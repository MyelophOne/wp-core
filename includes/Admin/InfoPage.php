<?php
/**
 * Info page
 *
 * @package Myelophone Core
 */
if (!defined("ABSPATH")) {
    exit();
}

class Meph_InfoPage
{
    /**
     * Check if PHP version is outdated
     *
     * @param string $php_version Current PHP version
     * @return bool True if version is outdated, false otherwise
     */
    private function is_php_version_outdated($php_version)
    {
        $min_recommended_version = "8.1";

        return version_compare($php_version, $min_recommended_version, "<");
    }

    /**
     * Get warning icon if PHP version is outdated
     *
     * @param string $php_version Current PHP version
     * @return string Warning icon HTML or empty string
     */
    private function get_php_version_warning($php_version)
    {
        if ($this->is_php_version_outdated($php_version)) {
            return '<span class="outdated">🚨</span>';
        }
        return "";
    }

    /**
     * Check plugin status
     *
     * @param string $plugin_slug Plugin slug
     * @return string Plugin status: 'not_installed', 'inactive', 'active'
     */
    private function get_plugin_status($plugin_slug)
    {
        if (!function_exists("get_plugins")) {
            require_once ABSPATH . "wp-admin/includes/plugin.php";
        }

        $all_plugins = get_plugins();

        foreach ($all_plugins as $plugin_path => $plugin_data) {
            $path_parts = explode("/", $plugin_path);
            $current_slug = $path_parts[0];

            if ($current_slug === $plugin_slug) {
                if (is_plugin_active($plugin_path)) {
                    return "active";
                } else {
                    return "inactive";
                }
            }
        }

        return "not_installed";
    }

    /**
     * Get plugin action button based on status
     *
     * @param array $plugin Plugin data
     * @return string Button HTML
     */
    private function get_plugin_action_button($plugin)
    {
        if (empty($plugin["name"])) {
            return "";
        }

        if (!empty($plugin["slug"])) {
            $status = $this->get_plugin_status($plugin["slug"]);

            switch ($status) {
                case "not_installed":
                    if (
                        !empty($plugin["install_url"]) &&
                        !empty($plugin["install_text"])
                    ) {
                        return sprintf(
                            '<a href="%s" class="button button-primary" target="_blank">%s</a>',
                            esc_url($plugin["install_url"]),
                            esc_html($plugin["install_text"]),
                        );
                    }
                    break;

                case "inactive":
                    if (
                        !empty($plugin["activate_url"]) &&
                        !empty($plugin["activate_text"])
                    ) {
                        return sprintf(
                            '<a href="%s" class="button button-secondary">%s</a>',
                            esc_url($plugin["activate_url"]),
                            esc_html($plugin["activate_text"]),
                        );
                    }
                    break;

                case "active":
                    return '<span class="isok">✅ ' .
                        esc_html__("Installed & Active", "myelophone-core") .
                        "</span>";
                    break;
            }
        }

        if (!empty($plugin["action_url"]) && !empty($plugin["action_text"])) {
            return sprintf(
                '<a href="%s" class="button button-primary" target="_blank">%s</a>',
                esc_url($plugin["action_url"]),
                esc_html($plugin["action_text"]),
            );
        }

        return "";
    }

    /**
     * Render the info page
     *
     * @return void
     */
    public function render()
    {
        $current_tab = Meph_Admin_Pages::get_current_tab(); ?>

        <div class="wrap">
            <h1>
                <?php echo esc_html__("MyelophOne Core", "myelophone-core"); ?>
                <span class="meph-version-badge">v<?php echo esc_html(
                    MYELOPHONE_CORE_VERSION,
                ); ?></span>
            </h1>

            <?php $this->render_tabs(); ?>

            <div class="meph-tab-content">
                <?php if ($current_tab === "info"): ?>
                    <?php $this->render_stats_grid(); ?>
                    <?php $this->render_system_info(); ?>
                <?php elseif ($current_tab === "settings"): ?>
                    <?php $this->render_settings(); ?>
                <?php elseif ($current_tab === "verification"): ?>
                    <?php $this->render_verification(); ?>
                <?php else: ?>
                    <?php $this->render_about(); ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render tabs navigation
     *
     * @return void
     */
    private function render_tabs()
    {
        $current_tab = Meph_Admin_Pages::get_current_tab(); ?>
        <nav class="meph-tabs">
            <ul class="meph-tabs-nav">
                <li>
                    <a href="<?php echo esc_url(
                        Meph_Admin_Pages::get_tab_url("about"),
                    ); ?>"
                       class="<?php echo $current_tab === "about"
                           ? "current"
                           : ""; ?>">
                        <?php echo esc_html__("About", "myelophone-core"); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo esc_url(
                        Meph_Admin_Pages::get_tab_url("info"),
                    ); ?>"
                       class="<?php echo $current_tab === "info"
                           ? "current"
                           : ""; ?>">
                        <?php echo esc_html__(
                            "System Info",
                            "myelophone-core",
                        ); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo esc_url(
                        Meph_Admin_Pages::get_tab_url("settings"),
                    ); ?>"
                       class="<?php echo $current_tab === "settings"
                           ? "current"
                           : ""; ?>">
                        <?php echo esc_html__("Settings", "myelophone-core"); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo esc_url(
                        Meph_Admin_Pages::get_tab_url("verification"),
                    ); ?>"
                       class="<?php echo $current_tab === "verification"
                           ? "current"
                           : ""; ?>">
                        <?php echo esc_html__(
                            "Verification",
                            "myelophone-core",
                        ); ?>
                    </a>
                </li>
            </ul>
        </nav>
        <?php
    }

    /**
     * Render stats grid
     *
     * @return void
     */
    private function render_stats_grid()
    {
        global $wpdb;

        $post_count = wp_count_posts();
        $total_posts =
            $post_count->publish +
            $post_count->draft +
            $post_count->pending +
            $post_count->future +
            $post_count->private;

        $user_count = count_users();
        $total_users = $user_count["total_users"];

        $comment_count = wp_count_comments();
        $total_comments = $comment_count->total_comments;

        $plugin_count = count(get_plugins());

        $theme = wp_get_theme();

        $db_size = 0;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $tables = $wpdb->get_results("SHOW TABLE STATUS", ARRAY_A);
        foreach ($tables as $table) {
            $db_size += $table["Data_length"] + $table["Index_length"];
        }
        $db_size_formatted = size_format($db_size, 2);

        $memory_usage = memory_get_usage(true);
        $memory_limit = ini_get("memory_limit");
        $memory_usage_percent = 0;
        if ($memory_limit != "-1") {
            $memory_limit_bytes = wp_convert_hr_to_bytes($memory_limit);
            $memory_usage_percent = round(
                ($memory_usage / $memory_limit_bytes) * 100,
                1,
            );
        }
        $memory_usage_formatted = size_format($memory_usage, 2);
        ?>

        <div class="meph-stats-grid">
            <div class="meph-stat-card">
                <h3><?php echo esc_html__(
                    "WordPress Version",
                    "myelophone-core",
                ); ?></h3>
                <div class="meph-stat-value"><?php echo esc_html(
                    get_bloginfo("version"),
                ); ?></div>
                <div class="meph-stat-label"><?php echo esc_html__(
                    "Current Version",
                    "myelophone-core",
                ); ?></div>
            </div>

            <?php
            $php_version = phpversion();
            $php_warning_icon = $this->get_php_version_warning($php_version);
            ?>
            <div class="meph-stat-card">
                <h3><?php echo esc_html__(
                    "PHP Version",
                    "myelophone-core",
                ); ?></h3>
                <div class="meph-stat-value">
                    <?php
                    echo wp_kses_post($php_warning_icon);
                    echo esc_html($php_version);
                    ?>
                </div>
                <div class="meph-stat-label"><?php echo esc_html__(
                    "Server Version",
                    "myelophone-core",
                ); ?></div>
            </div>

            <div class="meph-stat-card">
                <h3><?php echo esc_html__(
                    "Memory Usage",
                    "myelophone-core",
                ); ?></h3>
                <div class="meph-stat-value"><?php echo esc_html(
                    $memory_usage_formatted,
                ); ?></div>
                <div class="meph-stat-label"><?php echo esc_html__(
                    "of ",
                    "myelophone-core",
                ) . esc_html($memory_limit); ?> (<?php echo esc_html(
     $memory_usage_percent,
 ); ?>%)</div>
            </div>

            <div class="meph-stat-card">
                <h3><?php echo esc_html__(
                    "Database Size",
                    "myelophone-core",
                ); ?></h3>
                <div class="meph-stat-value"><?php echo esc_html(
                    $db_size_formatted,
                ); ?></div>
                <div class="meph-stat-label"><?php echo esc_html__(
                    "Total Size",
                    "myelophone-core",
                ); ?></div>
            </div>

            <div class="meph-stat-card">
                <h3><?php echo esc_html__(
                    "Total Posts",
                    "myelophone-core",
                ); ?></h3>
                <div class="meph-stat-value"><?php echo esc_html(
                    number_format_i18n($total_posts),
                ); ?></div>
                <div class="meph-stat-label"><?php echo esc_html__(
                    "Published: ",
                    "myelophone-core",
                ) . esc_html(number_format_i18n($post_count->publish)); ?></div>
            </div>

            <div class="meph-stat-card">
                <h3><?php echo esc_html__(
                    "Total Users",
                    "myelophone-core",
                ); ?></h3>
                <div class="meph-stat-value"><?php echo esc_html(
                    number_format_i18n($total_users),
                ); ?></div>
                <div class="meph-stat-label"><?php echo esc_html__(
                    "Registered Users",
                    "myelophone-core",
                ); ?></div>
            </div>

            <div class="meph-stat-card">
                <h3><?php echo esc_html__(
                    "Active Plugins",
                    "myelophone-core",
                ); ?></h3>
                <div class="meph-stat-value"><?php echo esc_html(
                    number_format_i18n($plugin_count),
                ); ?></div>
                <div class="meph-stat-label"><?php echo esc_html__(
                    "Installed Plugins",
                    "myelophone-core",
                ); ?></div>
            </div>

            <div class="meph-stat-card">
                <h3><?php echo esc_html__(
                    "Active Theme",
                    "myelophone-core",
                ); ?></h3>
                <div class="meph-stat-value"><?php echo esc_html(
                    $theme->get("Name"),
                ); ?></div>
                <div class="meph-stat-label">v<?php echo esc_html(
                    $theme->get("Version"),
                ); ?></div>
            </div>
        </div>
        <?php
    }

    /**
     * Render system information
     *
     * @return void
     */
    private function render_system_info()
    {
        global $wpdb;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $db_version = $wpdb->get_var("SELECT VERSION()");

        $memory_limit = defined("WP_MEMORY_LIMIT")
            ? WP_MEMORY_LIMIT
            : __("Not defined", "myelophone-core");

        $debug_status =
            defined("WP_DEBUG") && WP_DEBUG
                ? '<span class="meph-status-enabled">' .
                    __("Enabled", "myelophone-core") .
                    "</span>"
                : '<span class="meph-status-disabled">' .
                    __("Disabled", "myelophone-core") .
                    "</span>";

        $server_software = isset($_SERVER["SERVER_SOFTWARE"])
            ? sanitize_text_field(wp_unslash($_SERVER["SERVER_SOFTWARE"]))
            : __("Unknown", "myelophone-core");

        $php_extensions = get_loaded_extensions();
        $essential_extensions = [
            "curl",
            "gd",
            "json",
            "mbstring",
            "mysqli",
            "openssl",
            "xml",
        ];
        $missing_extensions = array_diff(
            $essential_extensions,
            $php_extensions,
        );

        $upload_dir = wp_upload_dir();
        $upload_path = $upload_dir["basedir"];
        $upload_url = $upload_dir["baseurl"];

        $uploads_writable = wp_is_writable($upload_path);
        $uploads_status = $uploads_writable
            ? '<span class="meph-status-enabled">' .
                __("Writable", "myelophone-core") .
                "</span>"
            : '<span class="meph-status-warning">' .
                __("Not Writable", "myelophone-core") .
                "</span>";

        $timezone = get_option("timezone_string");
        if (empty($timezone)) {
            $timezone = __("UTC", "myelophone-core");
        }
        ?>

        <div class="meph-system-info">
            <table>
                <thead>
                    <tr>
                        <th colspan="2"><?php echo esc_html__(
                            "Detailed System Information",
                            "myelophone-core",
                        ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th><?php echo esc_html__(
                            "WordPress Version",
                            "myelophone-core",
                        ); ?></th>
                        <td><?php echo esc_html(
                            get_bloginfo("version"),
                        ); ?></td>
                    </tr>
                    <?php
                    $php_version = phpversion();
                    $php_warning_icon = $this->get_php_version_warning(
                        $php_version,
                    );
                    ?>
                    <tr>
                        <th><?php echo esc_html__(
                            "PHP Version",
                            "myelophone-core",
                        ); ?></th>
                        <td>
                            <?php
                            echo wp_kses_post($php_warning_icon);
                            echo esc_html($php_version);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo esc_html__(
                            "Database Version",
                            "myelophone-core",
                        ); ?></th>
                        <td><?php echo esc_html($db_version); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo esc_html__(
                            "Server Software",
                            "myelophone-core",
                        ); ?></th>
                        <td><?php echo esc_html($server_software); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo esc_html__(
                            "Memory Limit",
                            "myelophone-core",
                        ); ?></th>
                        <td><?php echo esc_html($memory_limit); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo esc_html__(
                            "Debug Mode",
                            "myelophone-core",
                        ); ?></th>
                        <td><?php echo wp_kses($debug_status, [
                            "span" => ["class" => []],
                        ]); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo esc_html__(
                            "Plugin Version",
                            "myelophone-core",
                        ); ?></th>
                        <td><?php echo esc_html(
                            MYELOPHONE_CORE_VERSION,
                        ); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo esc_html__(
                            "Site URL",
                            "myelophone-core",
                        ); ?></th>
                        <td><?php echo esc_html(get_site_url()); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo esc_html__(
                            "Home URL",
                            "myelophone-core",
                        ); ?></th>
                        <td><?php echo esc_html(get_home_url()); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo esc_html__(
                            "Multisite",
                            "myelophone-core",
                        ); ?></th>
                        <td><?php echo is_multisite()
                            ? '<span class="meph-status-info">' .
                                esc_html__("Yes", "myelophone-core") .
                                "</span>"
                            : esc_html__("No", "myelophone-core"); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo esc_html__(
                            "Active Theme",
                            "myelophone-core",
                        ); ?></th>
                        <td>
                            <?php
                            $theme = wp_get_theme();
                            echo esc_html(
                                $theme->get("Name") .
                                    " v" .
                                    $theme->get("Version"),
                            );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo esc_html__(
                            "PHP Memory Limit",
                            "myelophone-core",
                        ); ?></th>
                        <td><?php echo esc_html(
                            ini_get("memory_limit"),
                        ); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo esc_html__(
                            "PHP Max Execution Time",
                            "myelophone-core",
                        ); ?></th>
                        <td><?php echo esc_html(
                            ini_get("max_execution_time"),
                        ); ?>s</td>
                    </tr>
                    <tr>
                        <th><?php echo esc_html__(
                            "PHP Upload Max Filesize",
                            "myelophone-core",
                        ); ?></th>
                        <td><?php echo esc_html(
                            ini_get("upload_max_filesize"),
                        ); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo esc_html__(
                            "PHP Post Max Size",
                            "myelophone-core",
                        ); ?></th>
                        <td><?php echo esc_html(
                            ini_get("post_max_size"),
                        ); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo esc_html__(
                            "PHP Max Input Vars",
                            "myelophone-core",
                        ); ?></th>
                        <td><?php echo esc_html(
                            ini_get("max_input_vars"),
                        ); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo esc_html__(
                            "PHP Extensions",
                            "myelophone-core",
                        ); ?></th>
                        <td>
                            <?php if (empty($missing_extensions)): ?>
                                <span class="meph-status-enabled"><?php echo esc_html__(
                                    "All essential extensions loaded",
                                    "myelophone-core",
                                ); ?></span>
                            <?php else: ?>
                                <span class="meph-status-warning"><?php echo esc_html__(
                                    "Missing: ",
                                    "myelophone-core",
                                ) .
                                    esc_html(
                                        implode(", ", $missing_extensions),
                                    ); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo esc_html__(
                            "Uploads Directory",
                            "myelophone-core",
                        ); ?></th>
                        <td>
                            <?php echo esc_html($upload_path); ?><br>
                            <?php echo wp_kses($uploads_status, [
                                "span" => ["class" => []],
                            ]); ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo esc_html__(
                            "Timezone",
                            "myelophone-core",
                        ); ?></th>
                        <td><?php echo esc_html($timezone); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo esc_html__(
                            "Site Language",
                            "myelophone-core",
                        ); ?></th>
                        <td><?php echo esc_html(get_locale()); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo esc_html__(
                            "Charset",
                            "myelophone-core",
                        ); ?></th>
                        <td><?php echo esc_html(
                            get_bloginfo("charset"),
                        ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
    }

    /**
     * Render settings form
     *
     * @return void
     */
    private function render_settings()
    {
        $settings = new Meph_Settings();
        $all_options = $settings->get_all_options();
        ?>
        <div class="meph-settings-actions">
            <button type="button" class="button button-secondary" id="meph-recommended-settings">
                <?php echo esc_html__(
                    "Recommended Settings",
                    "myelophone-core",
                ); ?>
            </button>
            <button type="button" class="button button-secondary" id="meph-restore-defaults">
                <?php echo esc_html__("Restore Defaults", "myelophone-core"); ?>
            </button>
            <p class="description">
                <?php echo esc_html__(
                    'To save changes, click the "Save Changes" button below.',
                    "myelophone-core",
                ); ?>
            </p>
        </div>

        <form method="post" action="options.php" class="meph-settings-form">
            <?php
            settings_fields(Meph_Settings::SETTINGS_GROUP);

            $this->render_settings_sections_grid();

            submit_button();?>
        </form>
        <?php
    }

    /**
     * Render settings sections in grid layout
     *
     * @return void
     */
    private function render_settings_sections_grid()
    {
        global $wp_settings_sections, $wp_settings_fields;

        $page = Meph_Settings::SETTINGS_PAGE;

        if (!isset($wp_settings_sections[$page])) {
            return;
        }

        $bottom_fields = [
            "meph_display_errors",
            "meph_cleanup_svg_filters",
            "meph_maintenance_mode",
        ];

        $all_fields = [];
        $bottom_fields_data = [];

        foreach ((array) $wp_settings_sections[$page] as $section) {
            if ($section["id"] === "meph_verification_section") {
                continue;
            }

            if (!isset($wp_settings_fields[$page][$section["id"]])) {
                continue;
            }

            foreach (
                (array) $wp_settings_fields[$page][$section["id"]]
                as $field
            ) {
                $field_id = $field["id"] ?? "";

                if (in_array($field_id, $bottom_fields)) {
                    $bottom_fields_data[] = $field;
                } else {
                    $all_fields[] = $field;
                }
            }
        }

        $all_fields = array_merge($all_fields, $bottom_fields_data);
        ?>
        <div class="meph-settings-grid">
        <?php foreach ($all_fields as $field) {
            $class_value = !empty($field["args"]["class"])
                ? $field["args"]["class"]
                : "";
            printf(
                "<div%s>",
                $class_value ? ' class="' . esc_attr($class_value) . '"' : "",
            );
            if (!empty($field["args"]["label_for"])) {
                echo '<label for="' .
                    esc_attr($field["args"]["label_for"]) .
                    '">' .
                    esc_html($field["title"]) .
                    "</label>";
            } else {
                echo esc_html($field["title"]);
            }
            call_user_func($field["callback"], $field["args"]);
            echo "</div>";
        } ?>
        </div>
        <?php
    }

    /**
     * Render about tab content
     *
     * @return void
     */
    private function render_about()
    {
        ?>
         <div class="meph-about-content">
             <div class="meph-about-section meph-about-hero">
                 <h2 class="meph-about-title">
                     <?php echo esc_html__(
                         "MyelophOne Core",
                         "myelophone-core",
                     ); ?>
                 </h2>
                 <p class="meph-about-lead">
                     <?php echo esc_html__(
                         "Core plugin for WordPress optimization and management. Provides essential tools for site performance, security, and maintenance.",
                         "myelophone-core",
                     ); ?>
                 </p>

                 <div class="meph-about-grid meph-about-features-grid">
                     <div class="meph-about-card">
                         <h3 class="meph-about-card-title">
                             <span class="dashicons dashicons-shield meph-icon-security"></span>
                             <?php echo esc_html__(
                                 "Security",
                                 "myelophone-core",
                             ); ?>
                         </h3>
                         <p class="meph-about-card-desc">
                             <?php echo esc_html__(
                                 "Protect your site with security headers, version hiding, and REST API restrictions.",
                                 "myelophone-core",
                             ); ?>
                         </p>
                     </div>

                     <div class="meph-about-card">
                         <h3 class="meph-about-card-title">
                             <span class="dashicons dashicons-performance meph-icon-performance"></span>
                             <?php echo esc_html__(
                                 "Performance",
                                 "myelophone-core",
                             ); ?>
                         </h3>
                         <p class="meph-about-card-desc">
                             <?php echo esc_html__(
                                 "Disable unnecessary features, remove old revisions monthly, and clean up for faster loading.",
                                 "myelophone-core",
                             ); ?>
                         </p>
                     </div>

                     <div class="meph-about-card">
                         <h3 class="meph-about-card-title">
                             <span class="dashicons dashicons-admin-tools meph-icon-maintenance"></span>
                             <?php echo esc_html__(
                                 "Maintenance",
                                 "myelophone-core",
                             ); ?>
                         </h3>
                         <p class="meph-about-card-desc">
                             <?php echo esc_html__(
                                 "Manage post revisions, clean up comments, and optimize database automatically.",
                                 "myelophone-core",
                             ); ?>
                         </p>
                     </div>
                 </div>
             </div>

             <div class="meph-about-section">
                 <h3 class="meph-about-subtitle">
                     <?php echo esc_html__(
                         "Plugin Information",
                         "myelophone-core",
                     ); ?>
                 </h3>

                 <div class="meph-about-grid meph-about-meta-grid">
                     <div>
                         <div class="meph-about-meta-label">
                             <?php echo esc_html__(
                                 "Version",
                                 "myelophone-core",
                             ); ?>
                         </div>
                         <div class="meph-about-meta-value">
                             <?php echo esc_html(MYELOPHONE_CORE_VERSION); ?>
                         </div>
                     </div>

                     <div>
                         <div class="meph-about-meta-label">
                             <?php echo esc_html__(
                                 "PHP Required",
                                 "myelophone-core",
                             ); ?>
                         </div>
                         <div class="meph-about-meta-value">7.4+</div>
                     </div>

                     <div>
                         <div class="meph-about-meta-label">
                             <?php echo esc_html__(
                                 "WordPress Required",
                                 "myelophone-core",
                             ); ?>
                         </div>
                         <div class="meph-about-meta-value">5.6+</div>
                     </div>

                     <div>
                         <div class="meph-about-meta-label">
                             <?php echo esc_html__(
                                 "Author",
                                 "myelophone-core",
                             ); ?>
                         </div>
                         <div class="meph-about-meta-value">
                             <a href="https://github.com/aleksivanou" target="_blank" class="meph-about-author-link">Aliaksandr Ivanou</a>
                         </div>
                     </div>
                 </div>

                 <div class="meph-about-footer">
                     <p class="meph-about-footer-text">
                         <?php echo esc_html__(
                             "MyelophOne Core is the foundation for the MyelophOne ecosystem. Future plugins can extend functionality and add recommendations here.",
                             "myelophone-core",
                         ); ?>
                     </p>

                     <p class="meph-about-footer-text meph-mt-1">
                         <?php echo esc_html__(
                             "If you find this plugin useful, consider supporting the author by buying him a coffee. Thank you for your support!",
                             "myelophone-core",
                         ); ?>
                     </p>

                     <a href="<?php echo esc_url(
                         add_query_arg(
                             [
                                 "utm_source" => wp_parse_url(
                                     home_url(),
                                     PHP_URL_HOST,
                                 ),
                             ],
                             "https://www.buymeacoffee.com/aleksivanou",
                         ),
                     ); ?>" target="_blank" class="meph-about-donate-btn">
                         <img src="<?php echo esc_url(
                             MYELOPHONE_CORE_URL .
                                 "assets/img/default-yellow.png",
                         ); ?>" alt="Buy Me a Coffee" class="meph-about-donate-img">
                     </a>

                     <?php
                     $all_recommendations = apply_filters(
                         "myelophone_core_plugin_recommendations",
                         [],
                     );

                     $plugin_recommendations = [];
                     foreach ($all_recommendations as $plugin) {
                         if (!is_array($plugin) || empty($plugin["name"])) {
                             continue;
                         }
                         $plugin_recommendations[] = $plugin;
                     }

                     if (!empty($plugin_recommendations)): ?>
                         <div class="meph-plugin-recommendations">
                             <h3>
                                 <?php echo esc_html__(
                                     "Available Plugins",
                                     "myelophone-core",
                                 ); ?>
                             </h3>
                             <div class="meph-recommendations-grid">
                                 <?php foreach (
                                     $plugin_recommendations
                                     as $plugin
                                 ): ?>
                                     <div class="meph-plugin-card">
                                         <div class="meph-plugin-header">
                                             <h4><?php echo esc_html(
                                                 $plugin["name"],
                                             ); ?></h4>
                                             <?php if (
                                                 !empty($plugin["description"])
                                             ): ?>
                                                 <p><?php echo esc_html(
                                                     $plugin["description"],
                                                 ); ?></p>
                                             <?php endif; ?>
                                         </div>
                                         <div class="meph-plugin-actions">
                                             <?php echo wp_kses_post(
                                                 $this->get_plugin_action_button(
                                                     $plugin,
                                                 ),
                                             ); ?>
                                         </div>
                                     </div>
                                 <?php endforeach; ?>
                             </div>
                         </div>
                     <?php endif;?>
                 </div>
             </div>
         </div>
         <?php
    }

    /**
     * Render verification page
     *
     * @return void
     */
    private function render_verification()
    {
        if (!class_exists("Meph_Settings")) {
            require_once MYELOPHONE_CORE_DIR . "includes/Admin/Settings.php";
        }

        $settings = new Meph_Settings();

        if (!has_action("admin_init", [$settings, "register_settings"])) {
            $settings->register_settings();
        }
        ?>
        <div class="meph-verification-page">
            <h2 class="meph-verification-title"><?php echo esc_html__(
                "Search Engine Verification",
                "myelophone-core",
            ); ?></h2>
            <p class="meph-verification-description">
                <?php echo esc_html__(
                    "Add verification codes from search engines and social platforms to verify your website ownership. These meta tags will be added to the &lt;head&gt; section.",
                    "myelophone-core",
                ); ?>
            </p>

            <form method="post" action="options.php">
                <?php
                settings_fields(Meph_Settings::VERIFICATION_SETTINGS_GROUP);
                meph_do_settings_sections_for_tab(
                    Meph_Settings::SETTINGS_PAGE,
                    "meph_verification_section",
                );
                submit_button();?>
            </form>
        </div>
        <?php
    }
}

/**
 * Display settings sections for a specific tab
 *
 * @param string $page The slug name of the page
 * @param string $section_id The ID of the section to display
 * @return void
 */
function meph_do_settings_sections_for_tab($page, $section_id)
{
    global $wp_settings_sections, $wp_settings_fields;

    if (
        !isset($wp_settings_sections[$page]) ||
        !isset($wp_settings_sections[$page][$section_id])
    ) {
        return;
    }

    $section = $wp_settings_sections[$page][$section_id];

    if ($section["callback"]) {
        call_user_func($section["callback"], $section);
    }

    if (!isset($wp_settings_fields[$page][$section_id])) {
        return;
    }

    if ($section_id === "meph_verification_section") {
        if (!class_exists("Meph_Settings")) {
            require_once MYELOPHONE_CORE_DIR . "includes/Admin/Settings.php";
        }

        $settings = new Meph_Settings();

        echo '<div class="meph-verification-grid">';

        echo '<div class="meph-verification-field">';
        $settings->render_google_verification_field();
        echo "</div>";

        echo '<div class="meph-verification-field">';
        $settings->render_yandex_verification_field();
        echo "</div>";

        echo '<div class="meph-verification-field">';
        $settings->render_bing_verification_field();
        echo "</div>";

        echo '<div class="meph-verification-field">';
        $settings->render_baidu_verification_field();
        echo "</div>";

        echo '<div class="meph-verification-field">';
        $settings->render_alexa_verification_field();
        echo "</div>";

        echo '<div class="meph-verification-field">';
        $settings->render_pinterest_verification_field();
        echo "</div>";

        echo '<div class="meph-verification-field">';
        $settings->render_facebook_domain_verification_field();
        echo "</div>";

        echo "</div>";
    } else {
        echo '<table class="form-table">';
        do_settings_fields($page, $section_id);
        echo "</table>";
    }
}
