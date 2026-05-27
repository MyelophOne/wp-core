<?php
/**
 * Admin pages management
 *
 * @package Myelophone Core
 */
if (!defined("ABSPATH")) {
    exit();
}

class Meph_Admin_Pages
{
    /**
     * Register admin pages
     *
     * @return void
     */
    public function register_admin_pages()
    {
        add_menu_page(
            __("MyelophOne", "myelophone-core"),
            __("MyelophOne", "myelophone-core"),
            "manage_options",
            "myelophone-core",
            [$this, "redirect_to_info"],
            "dashicons-superhero",
            4.5,
        );

        add_submenu_page(
            "myelophone-core",
            __("MyelophOne Info", "myelophone-core"),
            __("Info", "myelophone-core"),
            "manage_options",
            "myelophone-core-info",
            [$this, "render_info_page"],
        );

        add_action(
            "admin_menu",
            function () {
                remove_submenu_page("myelophone-core", "myelophone-core");
            },
            999,
        );
    }

    /**
     * Redirect main menu to Info page
     *
     * @return void
     */
    public function redirect_to_info()
    {
        $this->render_info_page();
    }

    /**
     * Render info page
     *
     * @return void
     */
    public function render_info_page()
    {
        $info_page = new Meph_InfoPage();
        $info_page->render();
    }

    /**
     * Get current tab
     *
     * @return string
     */
    public static function get_current_tab()
    {
        $default_tab = "about";

        $page = filter_input(
            INPUT_GET,
            "page",
            FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        );

        if ($page && $page === "myelophone-core-info") {
            $tab = filter_input(
                INPUT_GET,
                "tab",
                FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            );
            $tab = $tab ? htmlspecialchars_decode($tab, ENT_QUOTES) : "";
            return $tab ?: $default_tab;
        }

        return $default_tab;
    }

    /**
     * Get tab URL
     *
     * @param string $tab Tab name
     * @return string
     */
    public static function get_tab_url($tab)
    {
        return add_query_arg(
            [
                "page" => "myelophone-core-info",
                "tab" => $tab,
            ],
            admin_url("admin.php"),
        );
    }
}
