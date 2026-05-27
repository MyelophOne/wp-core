/**
 * MyelophOne Core
 * @package MyelophOne Core
 */

(function ($) {
  "use strict";

  const recommendedSettings = {
    // General Settings
    meph_core_hide_wp_version: "1",
    meph_core_disable_emoji: "1",
    meph_core_restrict_rest_api: "1",
    meph_core_disable_xmlrpc: "1",
    meph_core_limit_revisions: "1",
    meph_core_disable_comments: "1",
    meph_core_disable_heartbeat: "1",
    meph_core_cleanup_head: "1",
    meph_core_disable_embeds: "1",
    meph_core_optimize_database: "1",
    meph_core_security_headers: "1",

    // Performance Optimization
    meph_core_hide_admin_bar: "1",
    meph_core_move_jquery_footer: "1",
    meph_core_remove_jquery_migrate: "1",

    // Security Enhancements
    meph_core_disable_author_scans: "1",
    meph_core_enable_svg_safe: "1",
    meph_core_disable_file_editor: "1",
    meph_core_disable_dashicons_frontend: "1",
    meph_core_disable_self_pingbacks: "1",
    meph_core_remove_website_field_comments: "1",
    meph_core_hide_login_errors: "1",
    meph_core_disable_image_sizes: "1",
    meph_core_disable_attachment_pages: "1",
    meph_core_auto_empty_trash: "1",
    meph_core_disable_woocommerce_non_shop: "1",
    meph_core_force_ssl: "1",
    meph_core_require_admin_email_confirmation: "1",
    meph_core_enable_classic_widgets: "1",
    meph_core_enable_post_cloning: "1",

    // turned off default options
    meph_core_display_errors: "0",
    meph_core_cleanup_svg_filters: "0",
    meph_core_cleanup_svg_filters: "0",
    meph_core_google_fonts_preload: "0",
    meph_core_maintenance_mode: "0",
  };

  // Default settings (all off)
  const defaultSettings = {
    meph_core_display_errors: "0",
    meph_core_hide_wp_version: "0",
    meph_core_disable_emoji: "0",
    meph_core_restrict_rest_api: "0",
    meph_core_disable_xmlrpc: "0",
    meph_core_limit_revisions: "0",
    meph_core_disable_comments: "0",
    meph_core_disable_heartbeat: "0",
    meph_core_cleanup_head: "0",
    meph_core_disable_embeds: "0",
    meph_core_optimize_database: "0",
    meph_core_security_headers: "0",
    meph_core_hide_admin_bar: "0",
    meph_core_move_jquery_footer: "0",
    meph_core_remove_jquery_migrate: "0",
    meph_core_cleanup_svg_filters: "0",
    meph_core_disable_author_scans: "0",
    meph_core_enable_svg_safe: "0",
    meph_core_google_fonts_preload: "0",
    meph_core_disable_file_editor: "0",
    meph_core_disable_dashicons_frontend: "0",
    meph_core_disable_self_pingbacks: "0",
    meph_core_remove_website_field_comments: "0",
    meph_core_hide_login_errors: "0",
    meph_core_disable_image_sizes: "0",
    meph_core_disable_attachment_pages: "0",
    meph_core_auto_empty_trash: "0",
    meph_core_disable_woocommerce_non_shop: "0",
    meph_core_maintenance_mode: "0",
    meph_core_force_ssl: "0",
    meph_core_require_admin_email_confirmation: "0",
    meph_core_enable_classic_widgets: "0",
    meph_core_enable_post_cloning: "0",
  };

  /**
   * Apply settings to checkboxes
   * @param {Object} settings - Settings object with option_name: value pairs
   */
  function applySettings(settings) {
    $.each(settings, function (optionName, value) {
      const $checkbox = $('input[name="' + optionName + '"]');
      if ($checkbox.length) {
        const isChecked = value === "1";
        $checkbox.prop("checked", isChecked).trigger("change");
      }
    });
  }

  /**
   * Initialize admin scripts
   */
  function init() {
    $("#meph-recommended-settings").on("click", function (e) {
      e.preventDefault();
      applySettings(recommendedSettings);
    });

    $("#meph-restore-defaults").on("click", function (e) {
      e.preventDefault();
      applySettings(defaultSettings);
    });
  }

  $(document).ready(init);
})(jQuery);
