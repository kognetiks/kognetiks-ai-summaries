<?php
/**
 * Kognetiks AI Summaries - Utilities - Ver 1.0.0
 *
 * This file contains the code for the Kognetiks AI Summaries utilities.
 * It handles the support settings and other parameters.
 * 
 * @package kognetiks-ai-summaries
 */

 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

// Create directory and file(s) for logs and debugging
function kognetiks_ai_summaries_create_directory_and_index_file( $directory ){

    global $wp_filesystem;

    // Setup the directory path
    $upload = wp_upload_dir();
    $kognetiks_ai_summaries_upload_dir = $upload['basedir'];
    $kognetiks_ai_summaries_upload_dir = $kognetiks_ai_summaries_upload_dir . '/kognetiks-ai-summaries/' . $directory . '/';

    // Ensure the directory exists
    if (!wp_mkdir_p($kognetiks_ai_summaries_upload_dir)) {
        wp_die(esc_html__('Failed to create directory.', 'kognetiks-ai-summaries'));
    }

    return $kognetiks_ai_summaries_upload_dir;

}