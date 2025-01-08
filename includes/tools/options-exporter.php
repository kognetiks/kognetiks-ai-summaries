<?php
/**
 * Kognetiks AI Summaries - Options Exporter - Ver 1.0.0
 *
 * This file contains the code for exporting the plugin options.
 * 
 *
 * @package kognetiks-ai-summaries
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

function kognetiks_ai_summaries_download_options_data() {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_download_options_data');
    global $wp_filesystem;
    global $kognetiks_ai_summaries_plugin_dir_path;
    global $wpdb;

    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'kognetiks-ai-summaries'));
    }

    // Option export format choice
    $output_choice = strtolower(esc_attr(get_option('kognetiks_ai_summaries_options_exporter_extension', 'csv')));

    // REMOVE - Ver 1.0.0
    // $kognetiks_ai_summaries_debug_dir_path = $kognetiks_ai_summaries_plugin_dir_path . 'debug/';
    // kognetiks_ai_summaries_create_directory_and_index_file($kognetiks_ai_summaries_debug_dir_path);

    // Create the logs directory in the uploads folder
    $kognetiks_ai_summaries_debug_dir = kognetiks_ai_summaries_create_directory_and_index_file( 'debug' );
    $options_file = $kognetiks_ai_summaries_debug_dir . 'kognetiks-ai-summaries-options.' . $output_choice;

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', '$output_choice: ' . $output_choice);
    // kognetiks_ai_summaries_back_trace( 'NOTICE', '$options_file: ' . $options_file);

    // Use caching to retrieve options
    $cache_key = 'kognetiks_ai_summaries_options_exporter_options';
    $options = wp_cache_get($cache_key);

    if ($options === false) {
        global $wpdb;
        $options = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}options WHERE option_name LIKE 'kognetiks_ai_summaries%' AND option_name NOT LIKE '%api_key%'", ARRAY_A);
        wp_cache_set($cache_key, $options);
    }

    if ($output_choice === 'json') {

        $options_data = wp_json_encode($options, JSON_PRETTY_PRINT);

        if (!$wp_filesystem->put_contents($options_file, $options_data, FS_CHMOD_FILE)) {

            wp_die(esc_html__('Failed to write options to file.', 'kognetiks-ai-summaries'));

        }

        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="kognetiks-ai-summaries-options.json"');

    } elseif ($output_choice === 'csv') {

        $content = "option_id,option_name,option_value,autoload\n";

        foreach ($options as $option) {

            $content .= implode(',', $option) . "\n";

        }
        if (!$wp_filesystem->put_contents($options_file, $content, FS_CHMOD_FILE)) {

            wp_die(esc_html__('Failed to write CSV content to file', 'kognetiks-ai-summaries'));
        }

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="kognetiks-ai-summaries-options.csv"');

    } else {

        kognetiks_ai_summaries_general_admin_notice(__('Invalid output choice.', 'kognetiks-ai-summaries'));
        return;
    }

    // Read and output the file content
    $file_content = $wp_filesystem->get_contents( $options_file );
    if ( $file_content !== false ) {
        echo esc_html( $file_content );
    } else {
        // Handle error
        echo esc_html( 'Error reading file.' );
    }

    // Delete the file
    wp_delete_file( $options_file );

    exit;

}
add_action('admin_post_kognetiks_ai_summaries_download_options_data', 'kognetiks_ai_summaries_download_options_data');
