<?php
/**
 * MyelophOne Core - WordPress Plugin
 *
 * @package     MyelophOne Core
 * @author      Aliaksandr Ivanou
 * @license     GPLv2 or later
 * @copyright   Copyright (c) 2026 Aliaksandr Ivanou
 *
 * @wordpress-plugin
 * Plugin Name: MyelophOne Core
 * Plugin URI:  https://github.com/MyelophOne/wp-core
 * Description: Core plugin for site core settings management.
 * Version:     1.0.0
 * Author:      Aliaksandr Ivanou
 * Author URI:  https://github.com/aleksivanou
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: myelophone-core
 * Domain Path: /languages
 * Requires at least: 5.6
 * Requires PHP: 7.4
 */

if (!defined("ABSPATH")) {
	exit();
}

require_once __DIR__ . "/compatibility.php";

define("MYELOPHONE_CORE_VERSION", "1.0.0");
define("MYELOPHONE_CORE_FILE", __FILE__);
define("MYELOPHONE_CORE_DIR", plugin_dir_path(__FILE__));
define("MYELOPHONE_CORE_URL", plugin_dir_url(__FILE__));
define("MYELOPHONE_CORE_BASENAME", plugin_basename(__FILE__));

function MYELOPHONE_CORE_should_display_errors()
{
	$option_enabled = get_option("meph_core_display_errors", "0") === "1";

	if (!$option_enabled) {
		return false;
	}

	if (
		function_exists("is_user_logged_in") &&
		function_exists("current_user_can") &&
		is_user_logged_in() &&
		current_user_can("manage_options")
	) {
		return true;
	}

	return false;
}

if (!defined("WP_DEBUG") || !WP_DEBUG) {
	add_action(
		"plugins_loaded",
		function () {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_set_error_handler
			set_error_handler(function ($errno, $errstr, $errfile, $errline) {
				if (MYELOPHONE_CORE_should_display_errors()) {
					return false;
				} else {
					return true;
				}
			});

			set_exception_handler(function ($exception) {
				if (MYELOPHONE_CORE_should_display_errors()) {
					throw $exception;
				} else {
					// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
					error_log("PHP Exception: " . $exception->getMessage());

					if (!headers_sent()) {
						header("HTTP/1.1 500 Internal Server Error");
					}
					exit();
				}
			});

			register_shutdown_function(function () {
				$error = error_get_last();
				if (
					$error &&
					in_array($error["type"], [
						E_ERROR,
						E_PARSE,
						E_CORE_ERROR,
						E_COMPILE_ERROR,
					])
				) {
					if (MYELOPHONE_CORE_should_display_errors()) {
						return;
					} else {
						// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
						error_log(
							"MyelophOne Fatal Error: " .
								$error["message"] .
								" in " .
								$error["file"] .
								" on line " .
								$error["line"],
						);

						if (ob_get_level()) {
							ob_end_clean();
						}

						if (!headers_sent()) {
							header("HTTP/1.1 500 Internal Server Error");
						}
					}
				}
			});
		},
		2,
	);
}

spl_autoload_register(function ($class_name) {
	$prefix = "Meph_";
	$base_dir = MYELOPHONE_CORE_DIR . "includes/";

	$len = strlen($prefix);
	if (strncmp($prefix, $class_name, $len) !== 0) {
		return;
	}

	$relative_class = substr($class_name, $len);
	$file = $base_dir . str_replace("_", "/", $relative_class) . ".php";

	if (file_exists($file)) {
		require_once $file;
	}
});

add_action("plugins_loaded", "MYELOPHONE_CORE_init_plugin");

/**
 * Initialize the plugin
 *
 * @return void
 */
function MYELOPHONE_CORE_init_plugin()
{
	if (class_exists("Meph_Plugin")) {
		$plugin = Meph_Plugin::get_instance();
		$plugin->init();
	}
}

add_filter("myelophone_core_plugin_recommendations", function (
	$recommendations,
) {
	$filtered_recommendations = [];

	foreach ($recommendations as $plugin) {
		if (!is_array($plugin) || empty($plugin["name"])) {
			continue;
		}

		// Security check: only allow own - MyelophOne created - plugins
		// Check if slug or action_url contains 'myelophone' (case-insensitive)
		$is_myelophone_plugin = false;

		if (
			!empty($plugin["slug"]) &&
			stripos($plugin["slug"], "myelophone") !== false
		) {
			$is_myelophone_plugin = true;
		} elseif (
			!empty($plugin["action_url"]) &&
			stripos($plugin["action_url"], "myelophone") !== false
		) {
			$is_myelophone_plugin = true;
		}

		if ($is_myelophone_plugin) {
			$filtered_recommendations[] = $plugin;
		}
	}

	return $filtered_recommendations;
});

/**
 * Activation hook
 *
 * @return void
 */
function MYELOPHONE_CORE_activate_plugin()
{
	require_once MYELOPHONE_CORE_DIR . "includes/Activator.php";
	Meph_Activator::activate();
}
register_activation_hook(__FILE__, "MYELOPHONE_CORE_activate_plugin");

/**
 * Deactivation hook
 *
 * @return void
 */
function MYELOPHONE_CORE_deactivate_plugin()
{
	require_once MYELOPHONE_CORE_DIR . "includes/Deactivator.php";
	Meph_Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, "MYELOPHONE_CORE_deactivate_plugin");

/**
 * Uninstall hook
 *
 * @return void
 */
function MYELOPHONE_CORE_uninstall_plugin()
{
	require_once MYELOPHONE_CORE_DIR . "includes/Uninstaller.php";
	Meph_Uninstaller::uninstall();
}
register_uninstall_hook(__FILE__, "MYELOPHONE_CORE_uninstall_plugin");
