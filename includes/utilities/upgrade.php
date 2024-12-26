<?php
/**
 * Kognetiks AI Summaries - Activate and Upgrade the Plugin
 *
 * This file contains the code for activating and upgrading the plugin.
 * It should run when the plugin is activated or updated.
 *
 * @package kognetiks-ai-summaries
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

// Activation Hook
function ksum_activate() {

    // DIAG - Diagnotics
    // ksum_back_trace( 'NOTICE', 'ksum_activate');

    // Logic to run during activation
    ksum_upgrade();

    // Handle unexpect output during activation
    $unexpected_output = ob_get_clean();
    if (!empty($unexpected_output)) {
        // Handle unexpected output
        if (!empty($unexpected_output)) {
            $error_message = 'Unexpected output during plugin activation: ' . esc_html($unexpected_output);
            add_action('admin_notices', function() use ($error_message) {
                echo '<div class="notice notice-error is-dismissible"><p>' . esc_html($error_message) . '</p></div>';
            });
        }
    }

    // DIAG - Diagnotics
    // ksum_back_trace( 'NOTICE', 'ksum_activate - completed');

    return;

}

// Upgrade Hook for Plugin Update
function ksum_upgrade_completed($upgrader_object, $options) {

    // DIAG - Diagnotics
    // ksum_back_trace( 'NOTICE', 'ksum_upgrade_completed');

    if ($options['action'] == 'update' && $options['type'] == 'plugin') {
        if (isset($options['plugins']) && is_array($options['plugins'])) {
            foreach($options['plugins'] as $plugin) {
                if (plugin_basename(__FILE__) === $plugin) {
                    // Logic to run during upgrade
                    ksum_upgrade();
                    break;
                }
            }
        } else {
            // DIAG - Log the warning
            // ksum_back_trace( 'WARNING', '"plugins" key is not set or not an array');
        }
    }

    // DIAG - Log the activation
    // ksum_back_trace( 'NOTICE', 'ksum_upgrade_completed - completed');

    return;

}

// Upgrade Logic - Revised 1.7.6
function ksum_upgrade() {

    // DIAG - Diagnotics
    // back_trace( 'NOTICE', 'ksum_upgrade');

    // Removed obsolete or replaced options
    // None at this time - Ver 1.0.0

    // DIAG - Diagnotics
    // back_trace( 'NOTICE', 'ksum_upgrade - completed');

    return;

}
