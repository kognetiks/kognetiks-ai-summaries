<?php
/*
 * Plugin Name: Kognetiks AI Summaries
 * Plugin URI:  https://github.com/kognetiks/kognetiks-ai-summaries
 * Description: This simple plugin adds an AI powered summaries of posts and page excerpts.
 * Version:     1.0.0
 * Author:      Kognetiks.com
 * Author URI:  https://www.kognetiks.com
 * License:     GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-30.html
 * 
 * Copyright (c) 2024 Stephen Howell
 *  
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 3, as published by the Free Software Foundation. You may NOT assume
 * that you can use any other version of the GPL.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Kognetiks AI Summaries. If not, see https://www.gnu.org/licenses/gpl-3.0.html.
 */

// If this file is called directly, die.
defined( 'WPINC' ) || die();

// Plugin version
global $ksum_plugin_version;
$ksum_plugin_version = '1.0.0';

// Plugin directory path
global $ksum_plugin_dir_path;
$ksum_plugin_dir_path = plugin_dir_path( __FILE__ );

// Plugin directory URL
global $ksum_plugin_dir_url;
$ksum_plugin_dir_url = plugin_dir_url( __FILE__ );

// Declare globals
global $wpdb;

// Include the necessary files - Main files
require_once plugin_dir_path( __FILE__ ) . 'includes/api-calls/anthropic-api.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/api-calls/nvidia-api.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/api-calls/openai-api.php';

// Include the necessary files - Settings files
require_once plugin_dir_path( __FILE__ ) . 'includes/settings/api-status.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/settings/diagnostics.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/settings/general.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/settings/settings-anthropic.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/settings/settings-nvidia.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/settings/settings-openai.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/settings/settings.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/settings/support.php';

// Include the necessary files - Utilities files
require_once plugin_dir_path( __FILE__ ) . 'includes/utilities/deactivate.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/utilities/diagnostics.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/utilities/globals.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/utilities/links.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/utilities/models.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/utilities/notices.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/utilities/restore.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/utilities/upgrade.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/utilities/utilities.php';

// Include the necessary files - Documentation files
// TBD

// Use the WP Filesystem API
global $wp_filesystem;

if ( ! function_exists( 'WP_Filesystem' ) ) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
}

WP_Filesystem();

// Settings and Deactivation
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'ksum_plugin_action_links');

// Diagnostics on/off setting can be set in the settings page
$ksum_diagnostics = esc_attr(get_option('ksum_diagnostics', 'Off'));

// Activation, deactivation, and uninstall functions
register_activation_hook(__FILE__, 'ksum_activate');
register_deactivation_hook(__FILE__, 'ksum_deactivate');
register_uninstall_hook(__FILE__, 'ksum_uninstall');
add_action('upgrader_process_complete', 'ksum_upgrade_completed', 10, 2);

function ksum_enqueue_admin_scripts() {

    global $ksum_plugin_version;

    wp_enqueue_script('jquery'); // Ensure jQuery is enqueued
    wp_enqueue_script('ksum_admin', plugins_url('assets/js/ksum-admin.js', __FILE__), array('jquery'), $ksum_plugin_version, true);

}
add_action('admin_enqueue_scripts', 'ksum_enqueue_admin_scripts');


// Return an AI summary for the page or post
function ksum_generate_ai_summary( $pid )  {

    global $wpdb;

    // Add a lock to prevent concurrent execution for the same post ID
    $lock_key = "ai_summary_lock_{$pid}";

    if ( get_transient( $lock_key ) ) {

        // DIAG - Diagnostics
        // ksum_back_trace( 'NOTICE', "AI summary generation for Post ID {$pid} is already in progress." );

        return null; // Exit early to prevent duplicate processing

    }

    // Set a transient lock with a timeout of 30 seconds
    set_transient( $lock_key, true, 30 );

    // Diagnostics
    ksum_back_trace( 'NOTICE', 'Generating AI summary' );
    ksum_back_trace( 'NOTICE', '$pid: ' . $pid );

     // Fetch and sanitize the content
    $row = $wpdb->get_row(
        $wpdb->prepare("SELECT post_content, post_modified FROM {$wpdb->posts} WHERE ID = %d", $pid)
    );

    $content = $row->post_content;
    $post_modified = $row->post_modified;

    $ksum_ai_platform_choice = esc_attr(get_option('ksum_ai_platform_choice'));

    switch ($ksum_ai_platform_choice) {

        case 'OpenAI':

            $model = esc_attr(get_option('ksum_chatgpt_model_choice', 'chatgpt-4o-latest'));
            break;

        case 'NVIDIA':

            $model = esc_attr(get_option('ksum_nvidia_model_choice', 'nvidia/llama-3.1-nemotron-51b-instruct'));
            break;

        case 'Anthropic':

            $model = esc_attr(get_option('ksum_anthropic_model_choice', 'claude-3-5-sonnet-latest'));
            break;

        default:

            $model = null; // No model selected
            ksum_prod_trace('ERROR', 'No valid model found for AI summary generation');
            return;

    }

    
    // Check for an existing AI summary
    $ai_summary = ksum_ai_summary_exists($pid);
    
    // Handle a generation error from earlier summarization
    if ($ai_summary == 'An API error occurred.') {
        // Try to generate a new AI summary
        $ai_summary = false;
    }

    switch ($ai_summary) {

        case null:

            // DIAG - Diagnostics
            ksum_back_trace( 'NOTICE', 'AI summary does not exist' );

            $ai_summary = ksum_generate_ai_summary_api($model, $content);
            ksum_insert_ai_summary($pid, $ai_summary, $post_modified);

            break;

        default:

            // DIAG - Diagnostics
            ksum_back_trace( 'NOTICE', 'AI summary exists' );

            if ( ksum_ai_summary_is_stale($pid) ) {
                // ksum_back_trace( 'NOTICE', 'AI summary is stale' );
                $ai_summary = ksum_generate_ai_summary_api($model, $content);
                ksum_update_ai_summary($pid, $ai_summary, $post_modified);
            }

            break;

    }

    // Get the desired excerpt length from options
    $ai_summary_length = intval( esc_attr( get_option( 'ksum_ai_summaries_length', 55 ) ) );

    // Trim the AI summary to the specified length
    $ai_summary = wp_trim_words( $ai_summary, $ai_summary_length, '...' );

    // DIAG - Diagnostics
    ksum_back_trace( 'NOTICE', '$ai_summary: ' . $ai_summary );

    // Trim the AI summary if it starts with 'Summary:' or 'Here's a 55-word summary:'
    if ( strpos($ai_summary, 'Summary: ') === 0 ) {

        $ai_summary = substr($ai_summary, 9);

    } elseif ( strpos($ai_summary, "Here's a 55-word summary: ") === 0 ) {

        $ai_summary = substr($ai_summary, 26);

    }

    // DIAG - Diagnostics
    ksum_back_trace( 'NOTICE', '$ai_summary: ' . $ai_summary );

    // Release the lock
    delete_transient( $lock_key );

    return $ai_summary;

}

// Generate an AI summary using the appropriate API
function ksum_generate_ai_summary_api( $model, $content ) {

    $content = htmlspecialchars(wp_strip_all_tags($content), ENT_QUOTES, 'UTF-8');
    $content = preg_replace('/\s+/', ' ', $content);

    $word_count = esc_attr(get_option('ksum_ai_summaries_length', 55));

    // Prepare special instructions if needed
    $special_instructions = "Here are some special instructions for the content that follows - please summarize this content in " . $word_count . " or few words: ";

    // Update the platform choice
    $ksum_ai_platform_choice = esc_attr(get_option('ksum_ai_platform_choice'));

    // Call the appropriate API based on the model
    switch (true) {

        case str_starts_with($ksum_ai_platform_choice, 'OpenAI'):

            // ksum_back_trace( 'NOTICE', 'Calling OpenAI API');
            $api_key = esc_attr(get_option('ksum_openai_api_key'));
            // ksum_back_trace( 'NOTICE', 'Adding special instructions to the content');
            $message = $special_instructions . $content;
            $response = ksum_openai_api_call($api_key, $message);

            break;

        case str_starts_with($ksum_ai_platform_choice, 'NVIDIA'):

            // ksum_back_trace( 'NOTICE', 'Calling NVIDIA API');
            $api_key = esc_attr(get_option('ksum_nvidia_api_key'));
            // ksum_back_trace( 'NOTICE', 'Adding special instructions to the content');
            $message = $special_instructions . $content;
            $response = ksum_nvidia_call_api($api_key, $message);

            break;

        case str_starts_with($ksum_ai_platform_choice, 'Anthropic'):

            // ksum_back_trace( 'NOTICE', 'Calling Anthropic API');
            $api_key = esc_attr(get_option('ksum_anthropic_api_key'));
            // ksum_back_trace( 'NOTICE', 'Adding special instructions to the content');
            $message = $special_instructions . $content;
            $response = ksum_anthropic_api_call($api_key, $message);

            break;
            
        default:

            // DIAG - Diagnostics
            ksum_back_trace( 'NOTICE', 'No valid platform selected for for AI summary generation');
            $response = '';

            break;

    }

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', '$response: ' . print_r($response, true));

    // REMOVE ANY HTML
    $response = wp_strip_all_tags($response);

    // REMOVE MARKDOWN LINKS
    $response = preg_replace('/\[(.*?)\]\((.*?)\)/', '$1', $response);

    // REMOVE MARKDOWN HEADERS
    $response = preg_replace('/^#{1,6}\s*(.*)/m', '$1', $response);

    // REMOVE MARKDOWN BOLD AND ITALIC
    $response = preg_replace('/(\*\*|__)(.*?)\1/', '$2', $response);
    $response = preg_replace('/(\*|_)(.*?)\1/', '$2', $response);

    // REMOVE MARKDOWN INLINE CODE
    $response = preg_replace('/`(.*?)`/', '$1', $response);

    // REMOVE MARKDOWN BLOCKQUOTES
    $response = preg_replace('/^\s*>+\s?(.*)/m', '$1', $response);

    // REMOVE MARKDOWN LISTS
    $response = preg_replace('/^\s*[-+*]\s+(.*)/m', '$1', $response);
    $response = preg_replace('/^\s*\d+\.\s+(.*)/m', '$1', $response);

    // REMOVE EXTRA SPACES
    $response = preg_replace('/\s+/', ' ', $response);

    $ai_summary = $response;

    return $ai_summary;

}

// Create the AI summary table if it does not exist
function ksum_create_ai_summary_table() {

    // DIAG - Diagnostics
    ksum_back_trace( 'NOTICE', 'Creating AI summary table' );

    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE {$wpdb->prefix}kognetiks_ai_summaries (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        post_id mediumint(9) NOT NULL,
        ai_summary text NOT NULL,
        post_modified datetime NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY unique_post_id (post_id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    // Handle any errors
    if ( $wpdb->last_error ) {
        ksum_prod_trace( 'ERROR', 'Error creating AI summary table' );
    }

}

// Insert or update an AI summary in the AI summary table
function ksum_insert_ai_summary( $pid, $ai_summary, $post_modified ) {

    // DIAG - Diagnostics
    ksum_back_trace( 'NOTICE', 'Inserting or updating AI summary into table' );

    global $wpdb;

    // Create the table if it does not exist
    ksum_create_ai_summary_table();
    
    // Prepare SQL to handle existing rows
    $result = $wpdb->query($wpdb->prepare(
        "INSERT INTO {$wpdb->prefix}kognetiks_ai_summaries (post_id, ai_summary, post_modified)
         VALUES (%d, %s, %s)
         ON DUPLICATE KEY UPDATE
         ai_summary = VALUES(ai_summary),
         post_modified = VALUES(post_modified)",
        $pid, $ai_summary, $post_modified
        )
    );


    // Handle any errors
    if ( $wpdb->last_error ) {

        ksum_prod_trace( 'ERROR', 'Error inserting or updating AI summary: ' . $wpdb->last_error );

    } else {

        ksum_back_trace( 'NOTICE', 'AI summary successfully inserted or updated.' );

    }

    return $result; // Return result for further processing if needed

}


// Check if an AI summary exists for a post
function ksum_ai_summary_exists( $pid ) {

    // DIAG - Diagnostics
    ksum_back_trace( 'NOTICE', 'Checking if AI summary exists' );

    global $wpdb;
   
    // Fetch ai_summary and post_modified from ai_summaries table
    $row = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT ai_summary, post_modified FROM {$wpdb->prefix}kognetiks_ai_summaries WHERE post_id = %d", $pid
            )
        );

    if ( $row ) {

        $ai_summary = $row->ai_summary;
        $post_modified = $row->post_modified;

        // DIAG - Diagnostics
        ksum_back_trace( 'NOTICE', 'AI summary exists for $pid: ' . $pid );

        return $ai_summary;

    } else {

        // DIAG - Diagnostics
        ksum_back_trace( 'NOTICE', 'AI summary does not exist' );
        
        return null;

    }

}

// Delete an AI summary from the ai summary table
function ksum_delete_ai_summary( $pid ) {

    // DIAG - Diagnostics
    ksum_back_trace( 'NOTICE', 'Deleting AI summary from table' );

    global $wpdb;

    $wpdb->delete(
        "{$wpdb->prefix}kognetiks_ai_summaries",
        array( 'post_id' => $pid )
    );

    // Handle any errors
    if ( $wpdb->last_error ) {

        ksum_prod_trace( 'ERROR', 'Error deleting AI summary from table' );

    }

}

// Check if an AI summary is stale
function ksum_ai_summary_is_stale( $pid ) {

    // DIAG - Diagnostics
    ksum_back_trace( 'NOTICE', 'Checking if AI summary is stale' );

    global $wpdb;
           
    // Fetch post_modified from ai_summaries table
    $row = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT post_modified FROM {$wpdb->prefix}kognetiks_ai_summaries WHERE post_id = %d", $pid
        )
    );

    if ( ! $row ) {
        // AI summary doesn't exist; it's stale by default
        return true;
    }

    $ai_post_modified = $row->post_modified;

    // Fetch post_modified from posts table
    $row = $wpdb->get_row(
        $wpdb->prepare("SELECT post_modified FROM {$wpdb->posts} WHERE ID = %d", $pid)
    );
    $post_modified = $row->post_modified;

    // Compare the dates
    if ( strtotime($ai_post_modified) < strtotime($post_modified) ) {

        // DIAG - Diagnostics
        ksum_back_trace( 'NOTICE', 'AI summary is stale' );

        return true;

    } else {

        // DIAG - Diagnostics
        ksum_back_trace( 'NOTICE', 'AI summary is not stale' );

        return false;

    }

}

// Update an AI summary in the ai summary table
function ksum_update_ai_summary( $pid, $ai_summary, $post_modified ) {

    // DIAG - Diagnostics
    ksum_back_trace( 'NOTICE', 'Updating AI summary in table' );

    global $wpdb;
    
    // Prepare and execute the query
    $wpdb->query(
        $wpdb->prepare(
            "INSERT INTO {$wpdb->prefix}kognetiks_ai_summaries (post_id, ai_summary, post_modified) 
            VALUES (%d, %s, %s) 
            ON DUPLICATE KEY UPDATE 
            ai_summary = VALUES(ai_summary), 
            post_modified = VALUES(post_modified)",
            $pid, $ai_summary, $post_modified
        )
    );
    
    // Handle any errors
    if ( $wpdb->last_error ) {
        ksum_prod_trace( 'ERROR', 'Error updating AI summary in table' );
    }

}

// Function to replace the excerpt with AI summary
function ksum_replace_excerpt_with_ai_summary( $excerpt, $post = null ) {

    // Check if AI summaries are enabled
    $enabled = esc_attr(get_option('ksum_ai_summaries_enabled', 'Off'));

    if ($enabled === 'Off') {

        // DIAG - Diagnostics
        ksum_back_trace('NOTICE', 'AI summaries are DISABLED');
        return $excerpt; // Return the default excerpt

    } else {

        // DIAG - Diagnostics
        ksum_back_trace('NOTICE', 'AI summaries are ENABLED');

    }

    // Get the global post if not provided
    if ($post === null) {
        global $post;
    } else {
        $post = get_post( $post );
    }

    if ( ! $post ) {
        return ''; // No post found, return empty string
    }

    // Check if the post is password protected
    if ( post_password_required( $post ) ) {
        return 'There is no excerpt because this is a protected post.';
    }

    // Attempt to generate or retrieve the AI summary
    $ai_summary = ksum_generate_ai_summary( $post->ID );

    // If AI summary exists, use it
    if ( ! empty( $ai_summary ) ) {

        // Get the desired excerpt length from options
        $ai_summary_length = intval( esc_attr( get_option( 'ksum_length', 55 ) ) );

        // Trim the AI summary to the specified length
        $excerpt = wp_trim_words( $ai_summary, $ai_summary_length, '...' );

    } else {

        // AI summary not available, proceed with default excerpt generation

        $excerpt = $post->post_excerpt;

        if ( empty( $excerpt ) ) {
            $content = $post->post_content;
            $content = strip_shortcodes( $content );

            // Apply 'the_content' filters
            $content = apply_filters( 'the_content', $content );
            $content = str_replace( ']]>', ']]&gt;', $content );

            // Get the default excerpt length and more string
            $excerpt_length = apply_filters( 'excerpt_length', 55 );
            $excerpt_more   = apply_filters( 'excerpt_more', ' [&hellip;]' );

            // Generate the excerpt
            $excerpt = wp_trim_words( $content, $excerpt_length, $excerpt_more );
        }
    
    }

    // Return the final excerpt without re-applying 'get_the_excerpt' filter to avoid recursion
    return $excerpt;

}
// Hook the function into 'get_the_excerpt' filter
add_filter( 'get_the_excerpt', 'ksum_replace_excerpt_with_ai_summary', 10, 2 );

