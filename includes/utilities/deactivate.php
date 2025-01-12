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
function kognetiks_ai_summaries_deactivate() {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_deactivate' );

    if (empty(esc_attr(get_option('kognetiks_ai_summaries_delete_data')))) {      
        kognetiks_ai_summaries_admin_notices();
    }

}
// Delete Plugin Data Notice
add_action('admin_notices', 'kognetiks_ai_summaries_admin_notices');

function kognetiks_ai_summaries_admin_notices() {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_admin_notices' );

    if (empty(esc_attr(get_option('kognetiks_ai_summaries_delete_data')))) {     

        echo '<div class="notice notice-warning is-dismissible">
            <p><strong>Kognetiks AI Summaries:</strong> Remember to set your data deletion preferences in the plugin settings if you plan to uninstall the plugin.</p>
        </div>';
        update_option('kognetiks_ai_summaries_delete_data', 'no');

    }

}

// Upgrade Logic
function kognetiks_ai_summaries_uninstall(){

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_uninstall - started' );

    global $wpdb;
    global $wp_filesystem;

    // Ask if the data should be removed, if not return
    if (esc_attr(get_option('kognetiks_ai_summaries_delete_data')) != 'yes') {
        return;
    }

    if (empty($wp_filesystem)) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        WP_Filesystem();
    }

    // Check for a setting that specifies whether to delete data
    if (esc_attr(get_option('kognetiks_ai_summaries_delete_data')) == 'yes') {

        // Delete AI Summaries options
        // kognetiks_ai_summaries_back_trace( 'NOTICE', 'Deleting Plugin One-off options');
        // Execute the query
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->prefix}options WHERE option_name LIKE %s",
                'kognetiks_ai_summaries%'
            )
        );      
        // Clear the cache for the deleted options
        wp_cache_flush();

        // Delete AI Summaries tables
        // kognetiks_ai_summaries_back_trace('NOTICE', 'Deleting tables');
        $table_name = $wpdb->prefix . 'kognetiks_ai_summaries';
        // Execute the query to drop the table
        $wpdb->query(
            $wpdb->prepare(
                "DROP TABLE IF EXISTS %s",
                $table_name
            )
        );       
        // Clear the cache for the deleted table
        wp_cache_flush();

        // Delete transients
        // kognetiks_ai_summaries_back_trace( 'NOTICE', 'Deleting transients');
        // Execute the query
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->prefix}options WHERE option_name LIKE %s OR option_name LIKE %s",
                '_transient_kognetiks_ai_summaries%', '_transient_timeout_kognetiks_ai_summaries%'
            )
        );
        // Clear the cache for the deleted options
        wp_cache_flush();

        // Delete any scheduled cron events
        // kognetiks_ai_summaries_back_trace( 'NOTICE', 'Deleting cron events');
        $crons = _get_cron_array();
        foreach ($crons as $timestamp => $cron) {
            foreach ($cron as $hook => $events) {
                if (strpos($hook, 'kognetiks_ai_summaries') !== false) {
                    foreach ($events as $event) {
                        wp_unschedule_event($timestamp, $hook, $event['args']);
                    }
                }
            }
        }

        // Delete the log files, if any
        // kognetiks_ai_summaries_back_trace( 'NOTICE', 'Deleting log files');
        $kognetiks_ai_summaries_logs_dir = kognetiks_ai_summaries_create_directory_and_index_file('logs');
        $files = array_diff(scandir($kognetiks_ai_summaries_logs_dir), array('..', '.'));
        foreach ($files as $file) {
            wp_delete_file($kognetiks_ai_summaries_logs_dir . $file);
        }

        // Delete the debug files, if any
        // kognetiks_ai_summaries_back_trace( 'NOTICE', 'Deleting debug files');
        $kognetiks_ai_summaries_debug_dir = kognetiks_ai_summaries_create_directory_and_index_file('debug');
        $files = array_diff(scandir($kognetiks_ai_summaries_debug_dir), array('..', '.'));
        foreach ($files as $file) {
            wp_delete_file($kognetiks_ai_summaries_debug_dir . $file);
        }

        // Delete the directory
        $upload = wp_upload_dir();
        $kognetiks_ai_summaries_upload_dir = $upload['basedir'];
        $kognetiks_ai_summaries_upload_dir = $kognetiks_ai_summaries_upload_dir . '/kognetiks-ai-summaries';
        $wp_filesystem->rmdir($kognetiks_ai_summaries_upload_dir, true);

    }

    // DIAG - Log the uninstall
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_uninstall - completed');

}
