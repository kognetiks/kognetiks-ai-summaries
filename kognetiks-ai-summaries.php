<?php
/*
 * Plugin Name: Kognetiks AI Summaries
 * Plugin URI:  https://github.com/kognetiks/kognetiks-ai-summaries
 * Description: This simple plugin adds an AI powered summaries of posts and page excerpts.
 * Version:     1.0.3
 * Author:      Kognetiks.com
 * Author URI:  https://www.kognetiks.com
 * License:     GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * 
 * Copyright (c) 2024-2025 Stephen Howell
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

// Globals for plugin name
global $kognetiks_ai_summaries_plugin_name;
$kognetiks_ai_summaries_plugin_name = 'kognetiks-ai-summaries';

// Globals for plugin version
global $kognetiks_ai_summaries_plugin_version;
$kognetiks_ai_summaries_plugin_version = '1.0.3';

// Plugin directory path
global $kognetiks_ai_summaries_plugin_dir_path;
$kognetiks_ai_summaries_plugin_dir_path = plugin_dir_path( __FILE__ );

// Plugin directory URL
global $kognetiks_ai_summaries_plugin_dir_url;
$kognetiks_ai_summaries_plugin_dir_url = plugin_dir_url( __FILE__ );

// Declare globals
global $wpdb;

// Include the main functions
require_once plugin_dir_path( __FILE__ ) . 'includes/functions/tags.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/functions/categories.php';

// Include the necessary files - Main files
require_once plugin_dir_path( __FILE__ ) . 'includes/api-calls/anthropic-api.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/api-calls/deepseek-api.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/api-calls/google-api.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/api-calls/local-api.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/api-calls/mistral-api.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/api-calls/nvidia-api.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/api-calls/openai-api.php';

// Include the necessary files - Settings files
require_once plugin_dir_path( __FILE__ ) . 'includes/settings/api-status.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/settings/diagnostics.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/settings/general.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/settings/menus.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/settings/settings-anthropic.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/settings/settings-deepseek.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/settings/settings-google.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/settings/settings-local.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/settings/settings-mistral.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/settings/settings-nvidia.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/settings/settings-openai.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/settings/settings.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/settings/support.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/settings/tools.php';

// Include the necessary files - Tools files
require_once plugin_dir_path( __FILE__ ) . 'includes/tools/manage-error-logs.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/tools/options-exporter.php';

// Include the necessary files - Utilities files
require_once plugin_dir_path( __FILE__ ) . 'includes/utilities/deactivate.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/utilities/diagnostics.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/utilities/globals.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/utilities/keyguard.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/utilities/links.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/utilities/models.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/utilities/notices.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/utilities/restore.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/utilities/upgrade.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/utilities/utilities.php';

// Use the WP Filesystem API
global $wp_filesystem;
if ( ! function_exists( 'WP_Filesystem' ) ) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
}
WP_Filesystem();

// Settings and Deactivation
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'kognetiks_ai_summaries_plugin_action_links');

// Diagnostics on/off setting can be set in the settings page
$kognetiks_ai_summaries_diagnostics = esc_attr(get_option('kognetiks_ai_summaries_diagnostics', 'Off'));

// Activation, deactivation, and uninstall functions
register_activation_hook(__FILE__, 'kognetiks_ai_summaries_activate');
register_deactivation_hook(__FILE__, 'kognetiks_ai_summaries_deactivate');
register_uninstall_hook(__FILE__, 'kognetiks_ai_summaries_uninstall');
add_action('upgrader_process_complete', 'kognetiks_ai_summaries_upgrade_completed', 10, 2);

function kognetiks_ai_summaries_enqueue_admin_scripts() {

    // DiAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_enqueue_admin_scripts');

    global $kognetiks_ai_summaries_plugin_version;

    wp_enqueue_style('dashicons');
    wp_enqueue_style('kognetiks-ai-summaries-css', plugins_url('assets/css/admin.css', __FILE__), array(), $kognetiks_ai_summaries_plugin_version, 'all');

    wp_enqueue_script('jquery'); // Ensure jQuery is enqueued
    wp_enqueue_script('kognetiks_ai_summaries_admin', plugins_url('assets/js/admin.js', __FILE__), array('jquery'), $kognetiks_ai_summaries_plugin_version, true);

}
add_action('admin_enqueue_scripts', 'kognetiks_ai_summaries_enqueue_admin_scripts');

// Validate AI summary response to ensure it's not an error or placeholder text
function kognetiks_ai_summaries_validate_ai_summary( $summary ) {
    
    if ( empty( $summary ) || ! is_string( $summary ) ) {
        return false;
    }
    
    // Check if it's the ERROR string
    if ( $summary === 'ERROR' ) {
        return false;
    }
    
    // Check if it's an error response from the error_responses array
    global $kognetiks_ai_summaries_error_responses;
    if ( in_array( $summary, $kognetiks_ai_summaries_error_responses, true ) ) {
        return false;
    }
    
    // Check for common error messages
    $error_patterns = array(
        'An API error occurred',
        'An error occurred',
        'Error:',
        'Please check Settings',
        'No valid response',
        'No response received',
        'Understood. Please provide',
        'Please provide the content',
        'I understand',
        'I\'m ready',
        'How can I help',
        'What would you like',
    );
    
    foreach ( $error_patterns as $pattern ) {
        if ( stripos( $summary, $pattern ) !== false ) {
            return false;
        }
    }
    
    // Check if the summary is too short (likely not a real summary)
    if ( str_word_count( $summary ) < 5 ) {
        return false;
    }
    
    return true;
}

// Return an AI summary for the page or post
function kognetiks_ai_summaries_generate_ai_summary( $pid )  {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_generate_ai_summary' );

    global $wpdb;
    global $kognetiks_ai_summaries_error_responses;

    // Check that the table exists, if not create it
    kognetiks_ai_summaries_create_ai_summary_table();

    // Add a lock to prevent concurrent execution for the same post ID
    $lock_key = 'kognetiks_ai_summaries_lock_' . $pid;

    if ( get_transient( $lock_key ) ) {

        // DIAG - Diagnostics
        // kognetiks_ai_summaries_back_trace( 'NOTICE', "AI summary generation for Post ID {$pid} is already in progress." );

        // Try to get existing summary from database if available
        $existing_summary = kognetiks_ai_summaries_ai_summary_exists($pid);
        if ( ! empty( $existing_summary ) && kognetiks_ai_summaries_validate_ai_summary( $existing_summary ) ) {
            return $existing_summary;
        }
        
        return null; // Exit early to prevent duplicate processing

    }

    // Set a transient lock with a timeout of 30 seconds
    set_transient( $lock_key, true, 30 );

    // Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'Generating AI summary' );
    // kognetiks_ai_summaries_back_trace( 'NOTICE', '$pid: ' . $pid );

     // Fetch and sanitize the content
     $cache_key = 'kognetiks_ai_summaries_post_' . $pid;
     $row = wp_cache_get($cache_key);
     
     if ($row === false) {

         $post = get_post($pid);
     
         if ($post) {

             $row = (object) [
                 'post_content' => $post->post_content,
                 'post_modified' => $post->post_modified,
             ];
             wp_cache_set($cache_key, $row);

         } else {

             $row = null;

         }

     }

    $content = $row->post_content;
    $post_modified = $row->post_modified;

    $kognetiks_ai_summaries_ai_platform_choice = esc_attr(get_option('kognetiks_ai_summaries_ai_platform_choice', 'OpenAI'));

    switch ($kognetiks_ai_summaries_ai_platform_choice) {

        case 'OpenAI':

            $model = esc_attr(get_option('kognetiks_ai_summaries_chatgpt_model_choice', 'chatgpt-4o-latest'));
            break;

        case 'NVIDIA':

            $model = esc_attr(get_option('kognetiks_ai_summaries_nvidia_model_choice', 'nvidia/llama-3.1-nemotron-51b-instruct'));
            break;

        case 'Anthropic':

            $model = esc_attr(get_option('kognetiks_ai_summaries_anthropic_model_choice', 'claude-3-5-sonnet-latest'));
            break;

        case 'DeepSeek':

            $model = esc_attr(get_option('kognetiks_ai_summaries_deepseek_model_choice', 'deepseek-chat'));
            break;

        case 'Mistral':

            $model = esc_attr(get_option('kognetiks_ai_summaries_mistral_model_choice', 'mistral-small-latest'));
            break;

        case 'Google':

            $model = esc_attr(get_option('kognetiks_ai_summaries_google_gemini_model_choice', 'gemini-2.5-flash'));
            break;

        case 'Local':

            $model = esc_attr(get_option('kognetiks_ai_summaries_local_model_choice', 'llama3.2-3b-instruct'));
            break;

        default:

            $model = null; // No model selected
            kognetiks_ai_summaries_prod_trace('ERROR', 'No valid model found for AI summary generation');
            return;

    }
  
    // Check for an existing AI summary
    $ai_summary = kognetiks_ai_summaries_ai_summary_exists($pid);
    
    // Validate existing summary - if it's invalid, treat it as if it doesn't exist
    if ( ! empty( $ai_summary ) && ! kognetiks_ai_summaries_validate_ai_summary( $ai_summary ) ) {
        // Delete the invalid summary from database
        kognetiks_ai_summaries_delete_ai_summary( $pid );
        // Clear cache
        wp_cache_delete( 'kognetiks_ai_summaries_' . $pid );
        // Set to null to trigger regeneration
        $ai_summary = null;
    }
    
    // Handle a generation error from earlier summarization
    if ($ai_summary == 'An API error occurred.') {
        // Delete the error summary from database
        kognetiks_ai_summaries_delete_ai_summary( $pid );
        // Clear cache
        wp_cache_delete( 'kognetiks_ai_summaries_' . $pid );
        // Set to null to trigger regeneration
        $ai_summary = null;
    }

    switch ($ai_summary) {

        case null:

            // DIAG - Diagnostics
            // kognetiks_ai_summaries_back_trace( 'NOTICE', 'AI summary does not exist' );

            $ai_summary = kognetiks_ai_summaries_generate_ai_summary_api($model, $content);

            // Validate the AI summary response
            if ($ai_summary == 'ERROR' || ! kognetiks_ai_summaries_validate_ai_summary( $ai_summary )) {

                // DIAG - Diagnostics
                // kognetiks_ai_summaries_back_trace( 'NOTICE', 'An API error occurred or invalid response received.' );

                // Release the lock
                delete_transient( $lock_key );

                // Return null to indicate failure (don't return error message as it might be used as excerpt)
                return null;

            } else {

                // Insert the AI summary only if it's valid
                kognetiks_ai_summaries_insert_ai_summary($pid, $ai_summary, $post_modified);

            }

            // Generate the AI categories
            $ai_categories = kognetiks_ai_summaries_generate_ai_summary_api($model, $content, 'categories');
            // DIAG - Diagnostics
            // kognetiks_ai_summaries_back_trace( 'NOTICE', '$ai_categories: ' . $ai_categories );

            // Add the categories to the post
            kognetiks_ai_summaries_add_categories($pid, $ai_categories);

            // // Generate the AI tags
            $ai_tags = kognetiks_ai_summaries_generate_ai_summary_api($model, $content, 'tags');
            // DIAG - Diagnostics
            // kognetiks_ai_summaries_back_trace( 'NOTICE', '$ai_tags: ' . $ai_tags );

            // Add the tags to the post
            kognetiks_ai_summaries_add_tags($pid, $ai_tags);

            break;

        default:

            // DIAG - Diagnostics
            // kognetiks_ai_summaries_back_trace( 'NOTICE', 'AI summary exists' );

            if ( kognetiks_ai_summaries_ai_summary_is_stale($pid) ) {

                // kognetiks_ai_summaries_back_trace( 'NOTICE', 'AI summary is stale' );
                $ai_summary = kognetiks_ai_summaries_generate_ai_summary_api($model, $content);

                // Validate the AI summary response
                if ($ai_summary == 'ERROR' || ! kognetiks_ai_summaries_validate_ai_summary( $ai_summary )) {

                    // DIAG - Diagnostics
                    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'An API error occurred or invalid response received.' );
    
                    // Release the lock
                    delete_transient( $lock_key );
                    
                    // Return null to indicate failure (don't return error message as it might be used as excerpt)
                    return null;
    
                } else {

                    // Update the AI summary only if it's valid
                    kognetiks_ai_summaries_update_ai_summary($pid, $ai_summary, $post_modified);

                }

                // Generate the AI categories            
                $ai_categories = kognetiks_ai_summaries_generate_ai_summary_api($model, $content, 'categories');
                // DIAG - Diagnostics
                // kognetiks_ai_summaries_back_trace( 'NOTICE', '$ai_categories: ' . $ai_categories );

                // Add the categories to the post
                kognetiks_ai_summaries_add_categories($pid, $ai_categories);

                // Generate the AI tags
                $ai_tags = kognetiks_ai_summaries_generate_ai_summary_api($model, $content, 'tags');
                // DIAG - Diagnostics
                // kognetiks_ai_summaries_back_trace( 'NOTICE', '$ai_tags: ' . $ai_tags );

                // Add the tags to the post
                kognetiks_ai_summaries_add_tags($pid, $ai_tags);

                break;
            }

    }

    // Final validation check - ensure we have a valid summary before processing
    if ( empty( $ai_summary ) || ! kognetiks_ai_summaries_validate_ai_summary( $ai_summary ) ) {
        
        // DIAG - Diagnostics
        // kognetiks_ai_summaries_back_trace( 'NOTICE', 'Invalid or empty AI summary, returning null' );
        
        // Release the lock
        delete_transient( $lock_key );
        
        return null;
    }

    // Get the desired excerpt length from options
    $ai_summary_length = intval( esc_attr( get_option( 'kognetiks_ai_summaries_length', 55 ) ) );

    // Trim the text to the specified number of words without appending '...'
    $trimmed_summary = wp_trim_words( $ai_summary, $ai_summary_length, '' );

    // Check if the text was trimmed by comparing the original and trimmed versions
    if ( str_word_count( $ai_summary ) > $ai_summary_length ) {
        // Remove trailing punctuation if present
        $trimmed_summary = rtrim($trimmed_summary, '.,!?;:');
        // Append ellipsis
        $trimmed_summary .= '...';
    }

    $ai_summary = $trimmed_summary;

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', '$ai_summary: ' . $ai_summary );

    // Trim the AI summary if it starts with 'Summary:' or 'Here's a 55-word summary:'
    if ( strpos($ai_summary, 'Summary: ') === 0 ) {

        $ai_summary = substr($ai_summary, 9);

    } elseif ( strpos($ai_summary, "Here's a 55-word summary: ") === 0 ) {

        $ai_summary = substr($ai_summary, 26);

    }

    // Remove unwanted prefixes for categories and tags
    $ai_summary = preg_replace('/^These Are The Categories:\s*/i', '', $ai_summary);
    $ai_summary = preg_replace('/^These Are The Tags:\s*/i', '', $ai_summary);
    $ai_summary = preg_replace('/^These are the categories:\s*/i', '', $ai_summary);
    $ai_summary = preg_replace('/^These are the tags:\s*/i', '', $ai_summary);
    // Remove "Categories: " prefix - Ver 1.0.3
    $ai_summary = preg_replace('/^Categories:\s*/i', '', $ai_summary);
    // Remove "Tags: " prefix - Ver 1.0.3
    $ai_summary = preg_replace('/^Tags:\s*/i', '', $ai_summary);
    $ai_summary = trim($ai_summary);

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', '$ai_summary: ' . $ai_summary );

    // Release the lock
    delete_transient( $lock_key );

    return $ai_summary;

}

// Generate an AI summary using the appropriate API
function kognetiks_ai_summaries_generate_ai_summary_api( $model, $content, $type = 'summary' ) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_generate_ai_summary_api' );

    $content = htmlspecialchars(wp_strip_all_tags($content), ENT_QUOTES, 'UTF-8');
    $content = preg_replace('/\s+/', ' ', $content);

    switch ( $type ) {

        case 'summary':

            // Get the desired word count from options
            $word_count = esc_attr(get_option('kognetiks_ai_summaries_length', 55));

            // Prepare special instructions if needed
            $special_instructions = "Here are some special instructions for the content that follows - please summarize this content in " . $word_count . " or fewer words and just return the summary text without stating that it is a summary: ";

            break;

        case 'categories':

            // Get the desired category count from options
            $category_count = esc_attr(get_option('kognetiks_ai_summaries_category_count', 3));

            // Prepare special instructions if needed
            $special_instructions = "Here are some special instructions for the content that follows - please suggest " . $category_count . " one-word (no compound words) categories or fewer and just return the categories separated by commas without stating that these are the categories: ";

            break;

        case 'tags':

            // Get the desired tag count from options
            $tag_count = esc_attr(get_option('kognetiks_ai_summaries_tag_count', 3));

            // Prepare special instructions if needed
            $special_instructions = "Here are some special instructions for the content that follows - please suggest " . $tag_count . " one-word (no compound words) tags or fewer and just return the tags separated by commas without stating that these are the tags: ";

            break;

    }

    // Update the platform choice
    $kognetiks_ai_summaries_ai_platform_choice = esc_attr(get_option('kognetiks_ai_summaries_ai_platform_choice'));

    // Call the appropriate API based on the model
    switch ($kognetiks_ai_summaries_ai_platform_choice) {

        case 'OpenAI':

            // kognetiks_ai_summaries_back_trace( 'NOTICE', 'Calling OpenAI API');
            $api_key = esc_attr(get_option('kognetiks_ai_summaries_openai_api_key'));
            // Decrypt the API key - Ver 2.2.6
            $api_key = kognetiks_ai_summaries_decrypt_api_key($api_key);
            // kognetiks_ai_summaries_back_trace( 'NOTICE', 'Adding special instructions to the content');
            $message = $special_instructions . $content;
            $response = kognetiks_ai_summaries_openai_api_call($api_key, $message);

            break;

        case 'NVIDIA':

            // kognetiks_ai_summaries_back_trace( 'NOTICE', 'Calling NVIDIA API');
            $api_key = esc_attr(get_option('kognetiks_ai_summaries_nvidia_api_key'));
            // Decrypt the API key - Ver 2.2.6
            $api_key = kognetiks_ai_summaries_decrypt_api_key($api_key);
            // kognetiks_ai_summaries_back_trace( 'NOTICE', 'Adding special instructions to the content');
            $message = $special_instructions . $content;
            $response = kognetiks_ai_summaries_nvidia_api_call($api_key, $message);

            break;

        case 'Anthropic':

            // kognetiks_ai_summaries_back_trace( 'NOTICE', 'Calling Anthropic API');
            $api_key = esc_attr(get_option('kognetiks_ai_summaries_anthropic_api_key'));
            // Decrypt the API key - Ver 2.2.6
            $api_key = kognetiks_ai_summaries_decrypt_api_key($api_key);
            // kognetiks_ai_summaries_back_trace( 'NOTICE', 'Adding special instructions to the content');
            $message = $special_instructions . $content;
            $response = kognetiks_ai_summaries_anthropic_api_call($api_key, $message);

            break;

        case 'DeepSeek':

            // kognetiks_ai_summaries_back_trace( 'NOTICE', 'Calling DeepSeek API');
            $api_key = esc_attr(get_option('kognetiks_ai_summaries_deepseek_api_key'));
            // Decrypt the API key - Ver 2.2.6
            $api_key = kognetiks_ai_summaries_decrypt_api_key($api_key);
            // kognetiks_ai_summaries_back_trace( 'NOTICE', 'Adding special instructions to the content');
            $message = $special_instructions . $content;
            $response = kognetiks_ai_summaries_deepseek_api_call($api_key, $message);
            // kognetiks_ai_summaries_back_trace( 'NOTICE', '$Response: ' . print_r($response, true));

            break;

        case 'Mistral':

            // kognetiks_ai_summaries_back_trace( 'NOTICE', 'Calling Mistral API');
            $api_key = esc_attr(get_option('kognetiks_ai_summaries_mistral_api_key'));
            // Decrypt the API key - Ver 2.2.6
            $api_key = kognetiks_ai_summaries_decrypt_api_key($api_key);
            // kognetiks_ai_summaries_back_trace( 'NOTICE', 'Adding special instructions to the content');
            $message = $special_instructions . $content;
            $response = kognetiks_ai_summaries_mistral_api_call($api_key, $message);
            // kognetiks_ai_summaries_back_trace( 'NOTICE', '$Response: ' . print_r($response, true));

            break;

        case 'Google':

            // kognetiks_ai_summaries_back_trace( 'NOTICE', 'Calling Google Gemini API');
            $api_key = esc_attr(get_option('kognetiks_ai_summaries_google_api_key'));
            // Decrypt the API key - Ver 2.2.6
            $api_key = kognetiks_ai_summaries_decrypt_api_key($api_key);
            // kognetiks_ai_summaries_back_trace( 'NOTICE', 'Adding special instructions to the content');
            $message = $special_instructions . $content;
            $response = kognetiks_ai_summaries_google_api_call($api_key, $message);
            // kognetiks_ai_summaries_back_trace( 'NOTICE', '$Response: ' . print_r($response, true));

            break;

        case 'Local':

            // kognetiks_ai_summaries_back_trace( 'NOTICE', 'Calling Local API');
            $api_key = esc_attr(get_option('kognetiks_ai_summaries_local_api_key'));
            // Decrypt the API key - Ver 2.2.6
            $api_key = kognetiks_ai_summaries_decrypt_api_key($api_key);
            // kognetiks_ai_summaries_back_trace( 'NOTICE', 'Adding special instructions to the content');
            $message = $special_instructions . $content;
            $response = kognetiks_ai_summaries_local_api_call($api_key, $message);
            // kognetiks_ai_summaries_back_trace( 'NOTICE', '$Response: ' . print_r($response, true));

            break;

        default:

            // DIAG - Diagnostics
            // kognetiks_ai_summaries_back_trace( 'NOTICE', 'No valid platform selected for AI summary generation');
            $response = '';

            break;

    }

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', '$response: ' . print_r($response, true));

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

    // REMOVE UNWANTED PREFIXES - Strip "These Are The Categories:" or "These Are The Tags:" prefixes
    $response = preg_replace('/^These Are The Categories:\s*/i', '', $response);
    $response = preg_replace('/^These Are The Tags:\s*/i', '', $response);
    $response = preg_replace('/^These are the categories:\s*/i', '', $response);
    $response = preg_replace('/^These are the tags:\s*/i', '', $response);
    // Remove "Categories: " prefix - Ver 1.0.3
    $response = preg_replace('/^Categories:\s*/i', '', $response);
    // Remove "Tags: " prefix - Ver 1.0.3
    $response = preg_replace('/^Tags:\s*/i', '', $response);
    $response = trim($response);

    // Return the AI summary
    return $response;

}

// Create the AI summary table if it does not exist
function kognetiks_ai_summaries_create_ai_summary_table() {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_create_ai_summary_table' );

    global $wpdb;

    // If table exists, return
    if ( $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}kognetiks_ai_summaries'") == "{$wpdb->prefix}kognetiks_ai_summaries" ) {
        return;
    }

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
        kognetiks_ai_summaries_prod_trace( 'ERROR', 'Error creating AI summary table' );
    }

}

// Insert or update an AI summary in the AI summary table
function kognetiks_ai_summaries_insert_ai_summary( $pid, $ai_summary, $post_modified ) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_insert_ai_summary' );
    // kognetiks_ai_summaries_back_trace( 'NOTICE', '$pid: ' . $pid );
    // kognetiks_ai_summaries_back_trace( 'NOTICE', '$ai_summary: ' . $ai_summary );
    // kognetiks_ai_summaries_back_trace( 'NOTICE', '$post_modified: ' . $post_modified );

    global $wpdb;

    // Create the table if it does not exist
    kognetiks_ai_summaries_create_ai_summary_table();
    
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

        // DIAG - Diagnostics
        kognetiks_ai_summaries_prod_trace( 'ERROR', 'Error inserting or updating AI summary: ' . $wpdb->last_error );

    } else {

        // DIAG - Diagnostics
        // kognetiks_ai_summaries_back_trace( 'NOTICE', 'AI summary successfully inserted or updated.' );

    }

    return $result; // Return result for further processing if needed

}


// Check if an AI summary exists for a post
function kognetiks_ai_summaries_ai_summary_exists( $pid ) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_ai_summary_exists' );

    global $wpdb;
   
    // Fetch ai_summary and post_modified from ai_summaries table
    $cache_key = 'kognetiks_ai_summaries_' . $pid;
    $row = wp_cache_get($cache_key);
    
    if ($row === false) {

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT ai_summary, post_modified FROM {$wpdb->prefix}kognetiks_ai_summaries WHERE post_id = %d", 
                $pid
            )
        );
    
        if ($row) {

            wp_cache_set($cache_key, $row);

        }
    }

    if ( $row ) {

        $ai_summary = $row->ai_summary;
        $post_modified = $row->post_modified;

        // DIAG - Diagnostics
        // kognetiks_ai_summaries_back_trace( 'NOTICE', 'AI summary exists for $pid: ' . $pid );

        return $ai_summary;

    } else {

        // DIAG - Diagnostics
        // kognetiks_ai_summaries_back_trace( 'NOTICE', 'AI summary does not exist' );
        
        return null;

    }

}

// Delete an AI summary from the AI summary table
function kognetiks_ai_summaries_delete_ai_summary( $pid ) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_delete_ai_summary' );

    global $wpdb;

    $wpdb->delete(
        "{$wpdb->prefix}kognetiks_ai_summaries",
        array( 'post_id' => $pid )
    );

    // Handle any errors
    if ( $wpdb->last_error ) {

        kognetiks_ai_summaries_prod_trace( 'ERROR', 'Error deleting AI summary from table' );

    }

}

// Check if an AI summary is stale
function kognetiks_ai_summaries_ai_summary_is_stale( $pid ) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_ai_summary_is_stale' );

    global $wpdb;
           
    // Fetch post_modified from ai_summaries table
    $cache_key = 'kognetiks_ai_summaries_post_modified_' . $pid;
    $row = wp_cache_get($cache_key);
    
    if ($row === false) {

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT post_modified FROM {$wpdb->prefix}kognetiks_ai_summaries WHERE post_id = %d", 
                $pid
            )
        );
    
        if ($row) {

            wp_cache_set($cache_key, $row);

        }

    }

    if ( ! $row ) {
        // AI summary doesn't exist; it's stale by default
        return true;
    }

    $ai_post_modified = $row->post_modified;

    // Fetch post_modified from posts table
    $cache_key = 'kognetiks_ai_summaries_post_modified_' . $pid;
    $post_modified = wp_cache_get($cache_key);
    
    if ($post_modified === false) {

        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT post_modified FROM {$wpdb->posts} WHERE ID = %d", $pid)
        );
    
        if ($row) {

            $post_modified = $row->post_modified;
            wp_cache_set($cache_key, $post_modified);

        }

    }

    // Compare the dates

    if ( isset( $ai_post_modified->post_modified ) && isset( $post_modified->post_modified ) ) {
        if ( strtotime( $ai_post_modified->post_modified ) < strtotime( $post_modified->post_modified ) ) {
            // DIAG - Diagnostics
            // kognetiks_ai_summaries_back_trace( 'NOTICE', 'AI summary is stale' );
            return true;
        } else {
            // DIAG - Diagnostics
            // kognetiks_ai_summaries_back_trace( 'NOTICE', 'AI summary is not stale' );
            return false;
        }
    } else {
        // DIAG - Diagnostics
        // kognetiks_ai_summaries_back_trace( 'NOTICE', 'AI summary is not stale' );
        return false;
    }

}

// Update an AI summary in the AI summary table
function kognetiks_ai_summaries_update_ai_summary( $pid, $ai_summary, $post_modified ) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_update_ai_summary' );

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
        kognetiks_ai_summaries_prod_trace( 'ERROR', 'Error updating AI summary in table' );
    }

}

// Function to replace the excerpt with AI summary
function kognetiks_ai_summaries_replace_excerpt_with_ai_summary( $excerpt, $post = null ) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_replace_excerpt_with_ai_summary');

    // Check if AI summaries are enabled
    $enabled = esc_attr(get_option('kognetiks_ai_summaries_enabled', 'Off'));

    if ($enabled === 'Off') {

        // DIAG - Diagnostics
        // kognetiks_ai_summaries_back_trace( 'NOTICE', 'AI summaries are DISABLED');
        return $excerpt; // Return the default excerpt

    } else {

        // DIAG - Diagnostics
        // kognetiks_ai_summaries_back_trace( 'NOTICE', 'AI summaries are ENABLED');

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
    $ai_summary = kognetiks_ai_summaries_generate_ai_summary( $post->ID );

    // If AI summary exists and is valid, use it
    if ( ! empty( $ai_summary ) && kognetiks_ai_summaries_validate_ai_summary( $ai_summary ) ) {

        // Get the desired excerpt length from options
        $ai_summary_length = intval( esc_attr( get_option( 'kognetiks_ai_summaries_length', 55 ) ) );

        // Trim the AI summary to the specified length
        $excerpt = wp_trim_words( $ai_summary, $ai_summary_length, '...' );

    } else {

        // AI summary not available or invalid, proceed with default excerpt generation

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
add_filter( 'get_the_excerpt', 'kognetiks_ai_summaries_replace_excerpt_with_ai_summary', 10, 2 );
