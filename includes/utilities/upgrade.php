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
function kognetiks_ai_summaries_activate() {

    // DIAG - Diagnotics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_activate');

    // Logic to run during activation
    kognetiks_ai_summaries_upgrade();

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
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_activate - completed');

}

// Upgrade Hook for Plugin Update
function kognetiks_ai_summaries_upgrade_completed($upgrader_object, $options) {

    // DIAG - Diagnotics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_upgrade_completed');

    if ($options['action'] == 'update' && $options['type'] == 'plugin') {

        if (isset($options['plugins']) && is_array($options['plugins'])) {

            foreach($options['plugins'] as $plugin) {
                if (plugin_basename(__FILE__) === $plugin) {
                    // Logic to run during upgrade
                    kognetiks_ai_summaries_upgrade();
                    break;
                }
            }

        } else {

            // DIAG - Log the warning
            // kognetiks_ai_summaries_back_trace( 'WARNING', '"plugins" key is not set or not an array');

        }

    }

    // DIAG - Log the activation
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_upgrade_completed - completed');

}

// Upgrade Logic - Revised 1.7.6
function kognetiks_ai_summaries_upgrade() {

    // DIAG - Diagnotics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_upgrade');

    // DB schema upgrade (table creation/updates via dbDelta)
    if ( function_exists( 'kognetiks_ai_summaries_maybe_upgrade_db' ) ) {
        kognetiks_ai_summaries_maybe_upgrade_db();
    }

    // Removed obsolete or replaced options
    // None at this time - Ver 1.0.0

    // DIAG - Diagnotics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_upgrade - completed');

}
