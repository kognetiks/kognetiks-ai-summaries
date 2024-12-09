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
 * along with Kognetiks Chatbot for WordPress. If not, see https://www.gnu.org/licenses/gpl-3.0.html.
 */

// If this file is called directly, die.
defined( 'WPINC' ) || die();

// Plugin version
global $ksum_version;
$ksum_version = '1.0.0';

// Plugin directory path
global $ksum_plugin_dir_path;
$ksum_plugin_dir_path = plugin_dir_path( __FILE__ );

// Plugin directory URL
global $ksum_plugin_dir_url;
$ksum_plugin_dir_url = plugin_dir_url( __FILE__ );

// Declare globals
global $wpdb;
global $ksum_ai_summaries_table_name;
$ksum_ai_summaries_table_name = 'kognetiks_ai_summaries';

// Include the necessary files - Main files
// TBD

// Include the necessary files - Settings files
require_once plugin_dir_path( __FILE__ ) . 'includes/settings/api-openai.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/settings/diagnostics.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/settings/general.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/settings/settings.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/settings/support.php';

// Include the necessary files - Utilities files
require_once plugin_dir_path( __FILE__ ) . 'includes/utilities/deactivate.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/utilities/diagnostics.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/utilities/links.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/utilities/models.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/utilities/notices.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/utilities/restore.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/utilities/upgrade.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/utilities/utilities.php';

// Include the necessary files - Documentation files
// TBD

// Settings and Deactivation
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'ksum_plugin_action_links');

// Diagnostics on/off setting can be set in the settings page
$ksum_diagnostics = esc_attr(get_option('ksum_diagnostics', 'Off'));
// FIXME - OVERRIDE
update_option('ksum_diagnostics', 'Error');

// Activation, deactivation, and uninstall functions
register_activation_hook(__FILE__, 'ksum_activate');
register_deactivation_hook(__FILE__, 'ksum_deactivate');
register_uninstall_hook(__FILE__, 'ksum_uninstall');
add_action('upgrader_process_complete', 'ksum_upgrade_completed', 10, 2);


// Return an AI summary for the page or post
function ksum_generate_ai_summary( $pid )  {

    global $wpdb;
    global $ksum_settings;
    global $ksum_ai_summaries_table_name;

    // Add a lock to prevent concurrent execution for the same post ID
    $lock_key = "ai_summary_lock_{$pid}";
    if ( get_transient( $lock_key ) ) {
        // ksum_back_trace( 'NOTICE', "AI summary generation for Post ID {$pid} is already in progress." );
        return null; // Exit early to prevent duplicate processing
    }

    // Set a transient lock with a timeout of 30 seconds
    set_transient( $lock_key, true, 30 );

    // Diagnostics
    ksum_back_trace( 'NOTICE', 'Generating AI summary' );
    ksum_back_trace( 'NOTICE', '$pid: ' . $pid );

    // Set the model
    // $ksum_settings = get_option('ksum_settings'); // Assuming this is how you get the settings

    if (isset($ksum_settings['chatbot_chatgpt_model'])) {
        $model = $ksum_settings['chatbot_chatgpt_model'];
    } else {
        $model = null; // or set a default value
    }
    // ksum_back_trace( 'NOTICE', '$model at start of AI summaries: ' . $model );

    // Fetch and sanitize the content
    $query = $wpdb->prepare("SELECT post_content, post_modified FROM $wpdb->posts WHERE ID = %d", $pid);

    $row = $wpdb->get_row($query);

    $content = $row->post_content;
    $post_modified = $row->post_modified;

    // Check for an existing AI summary
    $ai_summary = ksum_ai_summary_exists($pid);

    if ( $ai_summary ) {

        // DIAG - Diagnostics
    ksum_back_trace( 'NOTICE', 'AI summary exists' );

        if ( ksum_ai_summary_is_stale($pid) ) {
            // ksum_back_trace( 'NOTICE', 'AI summary is stale' );
            $ai_summary = ksum_generate_ai_summary_api($model, $content);
            ksum_update_ai_summary($pid, $ai_summary, $post_modified);
        }

    } else {

        // DIAG - Diagnostics
        ksum_back_trace( 'NOTICE', 'AI summary does not exist' );

        if ($model == null) {
            if (esc_attr(get_option('ksum_ai_platform_choice')) == 'OpenAI') {
                $model = esc_attr(get_option('chatbot_chatgpt_model_choice', 'gpt-3.5-turbo'));
            } else if (esc_attr(get_option('ksum_ai_platform_choice')) == 'NVIDIA') {
                $model = esc_attr(get_option('chatbot_nvidia_model_choice', 'nvidia/llama-3.1-nemotron-51b-instruct'));
            } else if (esc_attr(get_option('chatbot_ai_platform_choing')) == 'Anthropic') {
                $model = esc_attr(get_option('chatbot_anthropic_model_choice', 'claude-3-5-sonnet-latest'));
            } else {
                $model = null; // No model selected
                ksum_prod_trace('ERROR', 'No valid model found for AI summary generation');
            }
        }

        $ai_summary = ksum_generate_ai_summary_api($model, $content);
        ksum_insert_ai_summary($pid, $ai_summary, $post_modified);

    }

    // Get the desired excerpt length from options
    $ai_summary_length = intval( get_option( 'ksum_length', 55 ) );

    // Trim the AI summary to the specified length
    $ai_summary = wp_trim_words( $ai_summary, $ai_summary_length, '...' );

    // DIAG - Diagnostics
    ksum_back_trace( 'NOTICE', '$ai_summary: ' . $ai_summary );

    // Release the lock
    delete_transient( $lock_key );

    return $ai_summary;

}

// Generate an AI summary using the appropriate API
function ksum_generate_ai_summary_api( $model, $content ) {

    global $ksum_ai_summaries_table_name;

    $content = htmlspecialchars(strip_tags($content), ENT_QUOTES, 'UTF-8');
    $content = preg_replace('/\s+/', ' ', $content);

    // Prepare special instructions if needed
    $special_instructions = "Here are some special instructions for the content that follows - please summarize this content in as few words as possible: ";

    // Update the model in settings
    $ksum_settings['model'] = $model;
    $ksum_ai_platform_choice = esc_attr(get_option('ksum_ai_platform_choice'));

    // Call the appropriate API based on the model
    switch (true) {

        case str_starts_with($ksum_ai_platform_choice, 'OpenAI'):

            // ksum_back_trace( 'NOTICE', 'Calling OpenAI API');
            $api_key = esc_attr(get_option('ksum_openai_api_key'));
            // ksum_back_trace( 'NOTICE', 'Adding special instructions to the content');
            $message = $special_instructions . $content;
            $response = chatbot_chatgpt_call_api_basic($api_key, $message);
            break;

        case str_starts_with($ksum_ai_platform_choice, 'NVIDIA'):

            // ksum_back_trace( 'NOTICE', 'Calling NVIDIA API');
            $api_key = esc_attr(get_option('ksum_nvidia_api_key'));
            // ksum_back_trace( 'NOTICE', 'Adding special instructions to the content');
            $message = $special_instructions . $content;
            $response = chatbot_nvidia_call_api($api_key, $message);
            break;

        case str_starts_with($ksum_ai_platform_choice, 'Anthropic'):

            // ksum_back_trace( 'NOTICE', 'Calling Anthropic API');
            $api_key = esc_attr(get_option('ksum_anthropic_api_key'));
            // ksum_back_trace( 'NOTICE', 'Adding special instructions to the content');
            $message = $special_instructions . $content;
            $response = chatbot_anthropic_call_api($api_key, $message);
            break;
            
        default:

            // DIAG - Diagnostics
            ksum_back_trace( 'NOTICE', 'No valid platform selected for for AI summary generation');
            $response = '';
            break;

    }

    // REMOVE ANY HTML
    $response = strip_tags($response);

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

// Create the ai summary table if it does not exist
function ksum_create_ai_summary_table() {

    // DIAG - Diagnostics
    ksum_back_trace( 'NOTICE', 'Creating AI summary table' );

    global $wpdb;
    global $ksum_ai_summaries_table_name;

    $table_name = $wpdb->prefix . $ksum_ai_summaries_table_name;

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
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

// Insert an AI summary into the ai summary table
function ksum_insert_ai_summary( $pid, $ai_summary, $post_modified ) {


    // DIAG - Diagnostics
    ksum_back_trace( 'NOTICE', 'Inserting AI summary into table' );

    global $wpdb;
    global $ksum_ai_summaries_table_name;
    
    // Create the table if it does not exist
    ksum_create_ai_summary_table();

    $table_name = $wpdb->prefix . $ksum_ai_summaries_table_name;

    $wpdb->insert(
        $table_name,
        array(
            'post_id' => $pid,
            'ai_summary' => $ai_summary,
            'post_modified' => $post_modified
        )
    );

    // Handle any errors
    if ( $wpdb->last_error ) {
        ksum_prod_trace( 'ERROR', 'Error inserting AI summary into table' );
    }

}

// Check if an AI summary exists for a post
function ksum_ai_summary_exists( $pid ) {

    // DIAG - Diagnostics
    ksum_back_trace( 'NOTICE', 'Checking if AI summary exists' );

    global $wpdb;
    global $ksum_ai_summaries_table_name;

    $table_name = $wpdb->prefix . $ksum_ai_summaries_table_name;

    $query = $wpdb->prepare("SELECT ai_summary, post_modified FROM $table_name WHERE post_id = %d", $pid);

    $row = $wpdb->get_row($query);

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
    global $ksum_ai_summaries_table_name;

    $table_name = $wpdb->prefix . $ksum_ai_summaries_table_name;

    $wpdb->delete(
        $table_name,
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
    global $ksum_ai_summaries_table_name;

    $table_name = $wpdb->prefix . $ksum_ai_summaries_table_name;

    // Fetch post_modified from ai_summaries table
    $query = $wpdb->prepare("SELECT post_modified FROM $table_name WHERE post_id = %d", $pid);
    $row = $wpdb->get_row($query);
    if ( ! $row ) {
        // AI summary doesn't exist; it's stale by default
        return true;
    }

    $ai_post_modified = $row->post_modified;

    // Fetch post_modified from posts table
    $query = $wpdb->prepare("SELECT post_modified FROM $wpdb->posts WHERE ID = %d", $pid);
    $row = $wpdb->get_row($query);
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
    global $ksum_ai_summaries_table_name;

    $table_name = $wpdb->prefix . $ksum_ai_summaries_table_name;

    $wpdb->query(
        $wpdb->prepare(
            "INSERT INTO $table_name (post_id, ai_summary, post_modified) 
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
    $enabled = esc_attr(get_option( 'ksum_ai_summaries_enabled', 'Off' ));
    $enabled = 'Off';
    if ( 'Off' !== $enabled ) {
        // DIAG - Diagnostics
        ksum_back_trace( 'NOTICE', 'AI summaries are DIABLED' );
        return $excerpt; // Return the default excerpt
    } else {
        // DIAG - Diagnostics
        ksum_back_trace( 'NOTICE', 'AI summaries are ENABLED' );
    }

    // Get the global post if not provided
    if ( null === $post ) {
        global $post;
    } else {
        $post = get_post( $post );
    }

    if ( ! $post ) {
        return ''; // No post found, return empty string
    }

    // Check if the post is password protected
    if ( post_password_required( $post ) ) {
        return __( 'There is no excerpt because this is a protected post.' );
    }

    // Attempt to generate or retrieve the AI summary
    $ai_summary = ksum_generate_ai_summary( $post->ID ); // Replace with your actual function

    // If AI summary exists, use it
    if ( ! empty( $ai_summary ) ) {

        // Get the desired excerpt length from options
        $ai_summary_length = intval( get_option( 'ksum_length', 55 ) );

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

