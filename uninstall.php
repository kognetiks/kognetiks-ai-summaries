<?php
/**
 * Fired when the plugin is uninstalled (deleted).
 *
 * Runs the plugin's uninstall routine only when the user has chosen
 * "DELETE ALL DATA" in Settings > Diagnostics. Does not load the
 * full plugin; only the deactivate utility and its dependency.
 *
 * @package kognetiks-ai-summaries
 */

// If not called by WordPress uninstall, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Path to plugin directory (uninstall.php lives in plugin root).
$plugin_dir = plugin_dir_path( __FILE__ );

// Load the helper that builds log/debug paths (used by uninstall).
require_once $plugin_dir . 'includes/utilities/utilities.php';

// Load the deactivate/uninstall logic.
require_once $plugin_dir . 'includes/utilities/deactivate.php';

// Run the existing uninstall function (respects "delete data" option).
kognetiks_ai_summaries_uninstall();
