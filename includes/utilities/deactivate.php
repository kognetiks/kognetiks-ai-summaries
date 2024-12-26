<?php
/**
 * Kognetiks AI Summaries - Deactivate and/or Delete the Plugin
 *
 * This file contains the code for deactivating and/or deleting the plugin.
 * 
 *
 * @package kognetiks-ai-summaries
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

// Deactivation Hook
function ksum_deactivate() {

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', 'ksum_deactivate' );

    if (empty(esc_attr(get_option('ksum_delete_data')))) {      
        ksum_admin_notices();
    }

}
// Delete Plugin Data Notice
add_action('admin_notices', 'ksum_admin_notices');

function ksum_admin_notices() {

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', 'ksum_admin_notices' );

    if (empty(esc_attr(get_option('ksum_delete_data')))) {     

        echo '<div class="notice notice-warning is-dismissible">
            <p><strong>Kognetiks AI Summaries:</strong> Remember to set your data deletion preferences in the plugin settings if you plan to uninstall the plugin.</p>
        </div>';
        update_option('ksum_delete_data', 'No');

    }

}

// Upgrade Logic
function ksum_uninstall(){

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', 'ksum_uninstall' );

    global $wpdb;

    // Ask if the data should be removed, if not return
    if (esc_attr(get_option('ksum_delete_data')) != 'Yes') {
        return;
    }

    // Check for a setting that specifies whether to delete data
    if (esc_attr(get_option('ksum_delete_data')) == 'Yes') {

        // Delete AI Summaries options
        // ksum_back_trace( 'NOTICE', 'Deleting Plugin One-off options');
        // Execute the query
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->prefix}options WHERE option_name LIKE %s",
                'ksum%'
            )
        );      
        // Clear the cache for the deleted options
        wp_cache_flush();

        // Delete AI Summaries tables
        // ksum_back_trace( 'NOTICE', 'Deleting tables');
        // Execute the query
        $wpdb->query(
            $wpdb->prepare(
                "DROP TABLE IF EXISTS %s",
                $wpdb->prefix . 'ksum_ai_summaries'
            )
        );
        // Clear the cache for the deleted table
        wp_cache_flush();

        // Delete transients
        // ksum_back_trace( 'NOTICE', 'Deleting transients');
        // Execute the query
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->prefix}options WHERE option_name LIKE %s OR option_name LIKE %s",
                '_transient_ksum%', '_transient_timeout_ksum%'
            )
        );
        // Clear the cache for the deleted options
        wp_cache_flush();

        // Delete any scheduled cron events
        // ksum_back_trace( 'NOTICE', 'Deleting cron events');
        $crons = _get_cron_array();
        foreach ($crons as $timestamp => $cron) {
            foreach ($cron as $hook => $events) {
                if (strpos($hook, 'ksum') !== false) {
                    foreach ($events as $event) {
                        wp_unschedule_event($timestamp, $hook, $event['args']);
                    }
                }
            }
        }

    }

    // DIAG - Log the uninstall
    // ksum_back_trace( 'NOTICE', 'PLUGIN UNINSTALL COMPLETED');

    return;

}
