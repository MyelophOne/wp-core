<?php
/**
 * PHP compatibility check
 *
 * @package Myelophone Core
 */
if (!defined("ABSPATH")) {
    exit();
}

if (version_compare(PHP_VERSION, "7.4.0", "<")) {
    add_action("admin_notices", function () {
        ?>
        <div class="notice notice-error">
            <p>
                <?php printf(
                    /* translators: %s: PHP version */
                    esc_html__(
                        "MyelophOne Core requires PHP version 7.4 or higher. Your current PHP version is %s.",
                        "myelophone-core",
                    ),
                    esc_html(PHP_VERSION),
                ); ?>
            </p>
        </div>
        <?php
    });
    return;
}

global $wp_version;
if (version_compare($wp_version, "5.6", "<")) {
    add_action("admin_notices", function () use ($wp_version) {
        ?>
        <div class="notice notice-error">
            <p>
                <?php printf(
                    /* translators: %s: WordPress version */
                    esc_html__(
                        "MyelophOne Core requires WordPress version 5.6 or higher. Your current WordPress version is %s.",
                        "myelophone-core",
                    ),
                    esc_html($wp_version),
                ); ?>
            </p>
        </div>
        <?php
    });
    return;
}

$meph_required_extensions = ["json", "mbstring"];
$meph_missing_extensions = [];

foreach ($meph_required_extensions as $meph_extension) {
    if (!extension_loaded($meph_extension)) {
        $meph_missing_extensions[] = $meph_extension;
    }
}

if (!empty($meph_missing_extensions)) {
    add_action("admin_notices", function () use ($meph_missing_extensions) {
        ?>
        <div class="notice notice-error">
            <p>
                <?php printf(
                    /* translators: %s: Comma-separated list of missing extensions */
                    esc_html__(
                        "MyelophOne Core requires the following PHP extensions: %s. Please enable them in your PHP configuration.",
                        "myelophone-core",
                    ),
                    esc_html(implode(", ", $meph_missing_extensions)),
                ); ?>
            </p>
        </div>
        <?php
    });
    return;
}
