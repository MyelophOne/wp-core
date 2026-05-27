=== MyelophOne Core ===
Contributors: myelophone
Donate link: https://buymeacoffee.com/aleksivanou
Tags: performance, speed, optimization, security, cleanup
Requires at least: 5.6
Tested up to: 7.0
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Comprehensive lightweight FREE WordPress optimization plugin with performance tools, security features and maintenance utilities.

== Description ==

**MyelophOne Core** is a comprehensive FREE WordPress optimization and management plugin that combines powerful system monitoring, performance optimization, security hardening and maintenance tools in one lightweight package. Designed for website administrators who want to improve their site's speed, security and stability without technical complexity.

🚀 **Performance Boost**:  Remove unnecessary scripts, defer jQuery, clean the `<head>`, and add smart pre‑connect hints.
🛡️ **Hardened Security**: Hide WP version, disable XML‑RPC, enforce strict security headers, and protect against author enumeration.
📊 **System Insights**: See PHP version, memory usage, DB size, active plugins, and health indicators at a glance.
🧹 **Automated Maintenance**: Scheduled revision limits, transient cleanup, trash auto‑empty, lightweight maintenance page that can be turned on with 1 click.

✨ Has Recommended settings option - **1 click** and your site becomes safer and faster.

### Why Choose MyelophOne Core?

One plugin, one settings page, zero conflicts.

✅ **All-in-One Solution**: Get performance, security and maintenance tools in one plugin.
✅ **Lightweight Design**: Minimal resource usage, maximum impact on site performance.
✅ **User-Friendly Interface**: Modern dashboard with intuitive toggle switches.
✅ **No Bloat**: Only essential features that actually improve your site.
✅ **Regular Updates**: Continuously maintained and improved.

### 🎯 Key Benefits

* **Faster Page Load Times**: Optimize scripts, remove bloat, and improve efficiency - up to 30% reduction in total request size (emoji, embeds, dashicons removed).
* **Better Security Posture**: Protect against common WordPress vulnerabilities.
* **Cleaner Database**: Monthly auto‑optimisation removes old revisions, transients & unused image sizes.
* **Improved SEO**: Faster sites and cleaner code improve search rankings and Google PageSpeed ranking.
* **Simplified Management**: One dashboard for all optimization needs.
* **Multilingual ready**: UI translated into 8 languages; community contributions welcome. Supported: English, Polish, German, Russian, Spanish, Italian, French, Portuguese.

== Features ==

### 📊 **Comprehensive System Information Dashboard**
* **Actual System Stats**: Monitor WordPress version, PHP version, memory usage and database size.
* **Detailed Server Information**: Server software, PHP extensions, uploads directory status.
* **Site Statistics**: Total posts, users, active plugins, and installed themes.
* **Performance Metrics**: Memory usage percentage, database optimization status.
* **Health Indicators**: Visual status indicators for critical system parameters.

### ⚡ **Performance Optimization Tools**
* **WP Head Cleanup**: Remove unnecessary meta tags, links, and scripts from WordPress head section.
* **Disable Emoji Scripts**: Remove WordPress emoji scripts and styles to reduce HTTP requests.
* **Disable Embeds**: Disable WordPress oEmbed scripts to prevent external site embeds.
* **Move jQuery to Footer**: Move jQuery and jQuery Migrate scripts from header to footer.
* **Remove jQuery Migrate**: Eliminate jQuery Migrate script to reduce file size (use with caution).
* **Disable Dashicons on Frontend**: Remove Dashicons CSS for non-logged-in users.
* **Google Fonts Optimization**: Add preconnect and dns-prefetch hints for faster font loading.
* **Cleanup SVG Filters**: Remove unnecessary SVG filters and Global Styles added by WordPress.

### 🛡️ **Security Enhancement Features**
* **Hide WordPress Version**: Remove version information from HTML source code.
* **Disable XML-RPC**: Completely disable XML-RPC to prevent DDoS and brute force attacks.
* **Restrict REST API**: Limit REST API access to authenticated users only.
* **Security Headers**: Add X-Content-Type-Options, X-Frame-Options, and X-XSS-Protection headers.
* **Disable File Editor**: Prevent unauthorized code modifications in WordPress admin.
* **Require Admin Email Confirmation**: Extra security layer for admin email changes.
* **Prevent Author Enumeration**: Block author scanning attempts (/?author=1 redirects).
* **Safe SVG Upload Support**: Enable secure SVG file uploads with XSS protection.
* **Hide Login Error Messages**: Show generic errors instead of specific login failures.
* **Force SSL/HTTPS**: Redirect all HTTP traffic to secure HTTPS.

### 🧹 **Maintenance & Cleanup Utilities**
* **Limit Post Revisions**: Reduce database size by limiting revisions to 5 per post.
* **Disable Comments Globally**: Turn off comments to reduce spam and database load.
* **Automatic Database Optimization**: Monthly cleanup of old revisions and transients.
* **Disable Self Pingbacks**: Prevent trackbacks from your own site.
* **Remove Website Field from Comments**: Simplify comment forms and reduce spam.
* **Disable Unnecessary Image Sizes**: Save disk space by disabling thumbnail, medium, large sizes.
* **Disable Attachment Pages**: Redirect media files directly to the file itself.
* **Empty Trash Faster**: Automatically empty trash every 7 days instead of 30.
* **WooCommerce Optimization**: Disable WooCommerce assets on non-shop pages.
* **Enable Classic Widgets**: Restore traditional widgets interface.

### ⚙️ **General Settings & Tools**
* **Display PHP Errors for Admins**: Show PHP errors and warnings on frontend for administrators.
* **Disable Heartbeat API**: Reduce server load by disabling WordPress Heartbeat.
* **Maintenance Mode**: Enable maintenance mode with custom message for visitors.
* **Post Cloning Feature**: Clone posts, pages, and custom post types with one click.
* **Search Engine Verification**: Add verification codes for Google, Bing, Yandex, Baidu, Alexa, Pinterest, Facebook.

== Installation ==

### 📦 **Method 1: Install directly from the WordPress.org plugin repository (Recommended)**

1. Open your WordPress dashboard.
2. Navigate to **Plugins → Add New**.
3. In the **Search plugins…** field type **MyelophOne Core**.
4. When the plugin appears in the results, click **Install Now**.
5. After WordPress finishes installing, press **Activate**.

Tip:** Once installed, the plugin will receive automatic updates from the WordPress.org repository, so you never have to download a ZIP file manually.

### 🔧 **Method 2: Manual Installation via FTP**
1. Extract the `myelophone-core-1.0.0.zip` archive
2. Connect to your web server using FTP client (FileZilla, Cyberduck, etc.)
3. Navigate to `/wp-content/plugins/` directory
4. Upload the entire `myelophone-core` folder
5. In WordPress admin, go to **Plugins**
6. Find **MyelophOne Core** and click **Activate**

== Frequently Asked Questions ==

= Is this plugin compatible with my WordPress version? =

Yes, MyelophOne Core is compatible with WordPress 5.6 and higher, including the latest WordPress 7.0. It requires PHP 7.4 or higher.

= Will this plugin slow down my website? =

No, the plugin is designed to improve performance. It removes unnecessary scripts, optimizes database queries, and implements performance best practices. The plugin itself is lightweight and efficient. And it is fully FREE.

= Is MyelophOne Core free? =

Yes, MyelophOne Core is a comprehensive lightweight and absolutely FREE WordPress optimization plugin.

= Is it safe to disable XML-RPC? =

Yes, unless you specifically use XML-RPC for remote publishing (like the WordPress mobile app) or other services. Most websites don't need XML-RPC, and disabling it improves security by closing a common attack vector.

= Can I use this plugin on a multisite installation? =

Yes, the plugin is fully compatible with WordPress Multisite installations. All features work in multisite environments.

= What happens to my data when I uninstall the plugin? =

All plugin settings and data are completely removed when you uninstall the plugin, leaving no traces in your database. Your WordPress core data remains untouched.

= Does this plugin work with caching plugins? =

Yes, it's fully compatible with popular caching plugins like WP Rocket, W3 Total Cache, WP Super Cache, and others. The optimizations complement caching solutions.

= How often does the database optimization run? =

The automatic database optimization runs on a monthly schedule (every 30 days). It cleans up post revisions older than 45 days and optimizes database tables.

= Can I still use SVG files with the safe SVG upload feature? =

Yes, the safe SVG upload feature allows you to upload SVG files while protecting against XSS attacks through proper sanitization. It's safer than allowing unrestricted SVG uploads.

= What happens when I enable maintenance mode? =

When maintenance mode is enabled, visitors see a simple maintenance message while administrators can still access the site normally. This is useful for temporary maintenance or updates. Maintenance page texts will be shown on user's browser language (if available, otherwise site language will be used).

= Do I need to be a developer to use it? =

Absolutely no. Every feature is toggled with a single switch in a clean, mobile‑responsive settings page. Advanced users can still use filters to fine‑tune behavior, but the default “one‑click enable” mode works perfectly for beginners. And MyelophOne Core plugin is built to be *lighter* than most performance plugins – all optimizations are applied via simple hooks that add virtually no overhead.

= I’m using WooCommerce. Will the plugin break my shop? =

The “WooCommerce Optimization” toggle disables WooCommerce assets (scripts, styles) on non‑shop pages, dramatically improving load times while leaving the shop pages fully functional. All core WooCommerce features remain intact.

== Changelog ==

= 1.0.0 =
* Initial release with comprehensive feature set
* System information dashboard with actual stats
* Performance optimization tools (30+ options)
* Security enhancement features
* Maintenance and cleanup utilities
* Modern admin interface with toggle switches
* Multi-language support (English, Polish, German, Russian, Spanish, Italian, French, Portugese)
* WordPress 7.0 compatibility
* Clean, well-documented codebase

== Upgrade Notice ==

= 1.0.0 =
Initial public release. No upgrade needed for new installations.

== Screenshots ==

1. **System Information Dashboard** - Real-time system stats and detailed server information.
2. **Settings Panel** - Modern toggle switches for all optimization options (General, Performance, Security, Maintenance).
3. **Search Engine Verification** - Add verification codes for major search engines.

== Translations ==

MyelophOne Core includes translation files for:
* English (default) - en_US
* Polish - pl_PL
* German - de_DE
* Russian - ru_RU
* Spanish - es_ES
* French - fr_FR
* Portuguese - pt_PT
* Italian - it_IT

Contributions for additional translations are welcome!

== Support ==

For support, feature requests, or bug reports:
* WordPress.org support forum: [Plugin Support](https://wordpress.org/support/plugin/myelophone-core)
* Email: aleksivanov.me@gmail.com

== Donate ==

If you find this plugin useful, consider supporting the development:
* [Buy Me a Coffee](https://buymeacoffee.com/aleksivanou)
* Your support helps maintain and improve the plugin!

== Credits ==

* Developed by Aliaksandr Ivanou
* Inspired by best practices in WordPress optimization
* Built with clean, maintainable code following WordPress coding standards
* Icons and UI elements designed for optimal user experience

== License ==

MyelophOne Core is released under the GPLv2 or later license. See the [license file](https://www.gnu.org/licenses/gpl-2.0.html) for details.
