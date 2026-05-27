<?php
/**
 * Main plugin class
 *
 * @package Myelophone Core
 */
if (!defined("ABSPATH")) {
    exit();
}

class Meph_Plugin
{
    /**
     * Plugin instance
     *
     * @var Meph_Plugin
     */
    private static $instance = null;

    /**
     * Admin pages instance
     *
     * @var Meph_Admin_Pages
     */
    private $admin_pages;

    /**
     * Settings instance
     *
     * @var Meph_Settings
     */
    private $settings;

    /**
     * Get plugin instance
     *
     * @return Meph_Plugin
     */
    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {}

    /**
     * Initialize the plugin
     *
     * @return void
     */
    public function init()
    {
        $this->load_dependencies();
        $this->init_components();
        $this->register_hooks();
    }

    /**
     * Load required dependencies
     *
     * @return void
     */
    private function load_dependencies()
    {
        require_once MYELOPHONE_CORE_DIR . "includes/Admin/Pages.php";
        require_once MYELOPHONE_CORE_DIR . "includes/Admin/Settings.php";
        require_once MYELOPHONE_CORE_DIR . "includes/Admin/InfoPage.php";
        require_once MYELOPHONE_CORE_DIR . "includes/Hooks.php";
    }

    /**
     * Initialize components
     *
     * @return void
     */
    private function init_components()
    {
        $this->admin_pages = new Meph_Admin_Pages();
        $this->settings = new Meph_Settings();

        new Meph_Hooks($this->settings);
    }

    /**
     * Register WordPress hooks
     *
     * @return void
     */
    private function register_hooks()
    {
        add_action("admin_menu", [$this->admin_pages, "register_admin_pages"]);
        add_action("admin_init", [$this->settings, "register_settings"]);
        add_action("admin_enqueue_scripts", [$this, "enqueue_admin_assets"]);
    }

    /**
     * Enqueue admin assets
     *
     * @param string $hook Current admin page
     * @return void
     */
    public function enqueue_admin_assets($hook)
    {
        if (strpos($hook, "myelophone") === false) {
            return;
        }

        wp_enqueue_style(
            "myelophone-core-admin-style",
            MYELOPHONE_CORE_URL . "assets/css/admin.css",
            [],
            MYELOPHONE_CORE_VERSION,
            "all",
        );

        wp_enqueue_script(
            "myelophone-core-admin-script",
            MYELOPHONE_CORE_URL . "assets/js/admin.js",
            ["jquery"],
            MYELOPHONE_CORE_VERSION,
            true,
        );

        wp_localize_script("myelophone-core-admin-script", "mephAdmin", [
            "i18n" => [
                "apply_recommended_confirm" => __(
                    "Apply recommended settings? All current values will be changed to recommended ones.",
                    "myelophone-core",
                ),
                "restore_defaults_confirm" => __(
                    "Restore default settings? All settings will be disabled.",
                    "myelophone-core",
                ),
            ],
        ]);

        wp_enqueue_style("dashicons");
    }

    /**
     * Get admin pages instance
     *
     * @return Meph_Admin_Pages
     */
    public function get_admin_pages()
    {
        return $this->admin_pages;
    }

    /**
     * Get settings instance
     *
     * @return Meph_Settings
     */
    public function get_settings()
    {
        return $this->settings;
    }
}
