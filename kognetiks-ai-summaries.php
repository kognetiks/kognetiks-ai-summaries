<?php
/*
 * Plugin Name: Kognetiks AI Summaries
 * Plugin URI:  https://github.com/kognetiks/kognetiks-ai-summaries
 * Description: This simple plugin adds AI-powered summaries of posts and page excerpts.
 * Version:     1.0.5
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
$kognetiks_ai_summaries_plugin_version = '1.0.5';

// DB schema version for upgrade routine (bump when table/index/column changes)
if ( ! defined( 'KOGNETIKS_AI_SUMMARIES_DB_VERSION' ) ) {
	define( 'KOGNETIKS_AI_SUMMARIES_DB_VERSION', 1 );
}

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
require_once plugin_dir_path( __FILE__ ) . 'includes/settings/settings-summaries.php';
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

// DB schema upgrade on admin only (never on front-end; dbDelta not in hot paths)
add_action('admin_init', 'kognetiks_ai_summaries_maybe_upgrade_db');

// Clear caches when a post is saved so stale check and content use fresh data on next excerpt request
add_action('save_post', 'kognetiks_ai_summaries_clear_post_caches_on_save', 10, 1);

// Trigger AI generation when a post is published (so excerpt, categories, tags are ready for home/search)
add_action('transition_post_status', 'kognetiks_ai_summaries_maybe_generate_on_publish', 10, 3);

// Proactively trigger generation when viewing a single post that has no summary (many themes don't call get_the_excerpt on single posts)
add_action('template_redirect', 'kognetiks_ai_summaries_maybe_generate_on_singular_view', 5);

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
    
    // Check for common error messages (including API error phrases - never write these to excerpt)
    $error_patterns = array(
        'An API error occurred',
        'An error occurred',
        'Error:',
        'Please check Settings',
        'No valid response',
        'No response received',
        'temporary issue with the API',
        'try your request again',
        'try again later',
        'give it another shot',
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

/**
 * Check if a string looks like an API error (for categories/tags - no word count check).
 * Valid category/tag responses are short comma-separated words; errors are long sentences.
 *
 * @param string $string The string to check.
 * @return bool True if the string appears to be an API error.
 */
function kognetiks_ai_summaries_is_api_error_response( $string ) {
    if ( empty( $string ) || ! is_string( $string ) ) {
        return true;
    }
    $string = trim( $string );
    if ( $string === 'ERROR' ) {
        return true;
    }
    global $kognetiks_ai_summaries_error_responses;
    if ( in_array( $string, $kognetiks_ai_summaries_error_responses, true ) ) {
        return true;
    }
    // Also check trimmed - error_responses have leading space
    if ( in_array( ' ' . $string, $kognetiks_ai_summaries_error_responses, true ) ) {
        return true;
    }
    $error_patterns = array(
        'An API error occurred',
        'An error occurred',
        'Error:',
        'Please check Settings',
        'No valid response',
        'No response received',
        "I'm sorry",
        'temporary issue with the API',
        'try your request again',
        'try again later',
        'give it another shot',
        'error from the API side',
        'retry in a bit',
        "Let's try again",
        "Let's give it",
        "there's an error",
    );
    foreach ( $error_patterns as $pattern ) {
        if ( stripos( $string, $pattern ) !== false ) {
            return true;
        }
    }
    return false;
}

// Per-request limit for API generations to prevent timeout (524) on search/archive pages.
if ( ! function_exists( 'kognetiks_ai_summaries_can_generate_this_request' ) ) {
    function kognetiks_ai_summaries_can_generate_this_request() {
        static $count = 0;
        $limit = (int) apply_filters( 'kognetiks_ai_summaries_generations_per_request', 2 );
        if ( $limit <= 0 ) {
            return true;
        }
        return ( $count++ < $limit );
    }
}

// Return an AI summary for the page or post
//
// @param int  $pid               Post ID.
// @param bool $force_generation  When true, bypass is_singular() check so generation runs
//                                on publish, in admin Tools, etc. Default false.
// @return string|null AI summary text or null.
function kognetiks_ai_summaries_generate_ai_summary( $pid, $force_generation = false )  {

    global $wpdb;
    global $kognetiks_ai_summaries_error_responses;

    // Gate by post type: only generate for enabled post types.
    $post = get_post( $pid );
    if ( ! $post ) {
        return null;
    }
    $enabled_types = get_option( 'kognetiks_ai_summaries_enabled_post_types', null );
    if ( null === $enabled_types || ! is_array( $enabled_types ) ) {
        $enabled_types = function_exists( 'kognetiks_ai_summaries_default_enabled_post_types' ) ? kognetiks_ai_summaries_default_enabled_post_types() : array( 'post' => 1, 'page' => 1 );
    }
    $gen_cat = get_option( 'kognetiks_ai_summaries_generate_categories', 1 );
    $gen_tag = get_option( 'kognetiks_ai_summaries_generate_tags', 1 );
    if ( 1 !== (int) ( $enabled_types[ $post->post_type ] ?? 0 ) ) {
        return null;
    }

    // Check that the table exists, if not create it
    kognetiks_ai_summaries_create_ai_summary_table();

    // On list views (search, archives, home): never generate - only use cached summaries.
    // Prevents regeneration and DB updates when refreshing search/archive pages.
    // Bypass when $force_generation is true (e.g. on publish, from Tools).
    if ( ! $force_generation && ! is_singular() ) {
        $cached = kognetiks_ai_summaries_ai_summary_exists( $pid );
        if ( ! empty( $cached ) && kognetiks_ai_summaries_validate_ai_summary( $cached ) ) {
            return $cached;
        }
        return null;
    }

    // Early return: if excerpt, categories, and tags are current, skip regeneration
    $existing_summary = kognetiks_ai_summaries_ai_summary_exists( $pid );
    if ( ! empty( $existing_summary ) && kognetiks_ai_summaries_validate_ai_summary( $existing_summary ) ) {
        if ( ! kognetiks_ai_summaries_ai_summary_is_stale( $pid ) ) {
            kognetiks_ai_summaries_prod_trace( 'NOTICE', 'Summary current, skipping regeneration pid=' . (int) $pid );
            return $existing_summary;
        }
    }

    // Add a lock to prevent concurrent execution for the same post ID
    $lock_key = 'kognetiks_ai_summaries_lock_' . $pid;

    if ( get_transient( $lock_key ) ) {

        kognetiks_ai_summaries_prod_trace( 'NOTICE', 'Generate lock active pid=' . (int) $pid );

        // Try to get existing summary from database if available
        $existing_summary = kognetiks_ai_summaries_ai_summary_exists($pid);
        if ( ! empty( $existing_summary ) && kognetiks_ai_summaries_validate_ai_summary( $existing_summary ) ) {
            kognetiks_ai_summaries_prod_trace( 'NOTICE', 'Returning existing summary (lock) pid=' . (int) $pid );
            return $existing_summary;
        }
        kognetiks_ai_summaries_prod_trace( 'WARNING', 'Lock active but no valid existing summary pid=' . (int) $pid );
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

    if ( ! $row || ! is_object( $row ) || ! isset( $row->post_content, $row->post_modified ) ) {
        kognetiks_ai_summaries_prod_trace( 'WARNING', 'Missing row or post_content/post_modified pid=' . (int) $pid );
        delete_transient( $lock_key );
        return null;
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

            // Limit generations per request to prevent timeout (524) on search/archive pages
            if ( ! kognetiks_ai_summaries_can_generate_this_request() ) {
                kognetiks_ai_summaries_prod_trace( 'NOTICE', 'Skipping generation (per-request limit) pid=' . (int) $pid );
                delete_transient( $lock_key );
                return null;
            }

            $ai_summary = kognetiks_ai_summaries_generate_ai_summary_api($model, $content);

            // Validate the AI summary response
            if ($ai_summary == 'ERROR' || ! kognetiks_ai_summaries_validate_ai_summary( $ai_summary )) {

                kognetiks_ai_summaries_prod_trace( 'ERROR', 'API error or invalid summary (new) pid=' . (int) $pid );
                delete_transient( $lock_key );
                return null;

            } else {

                kognetiks_ai_summaries_insert_ai_summary($pid, $ai_summary, $post_modified);
            }

            // Generate the AI categories (if enabled)
            if ( 1 === (int) get_option( 'kognetiks_ai_summaries_generate_categories', 1 ) ) {
                $ai_categories = kognetiks_ai_summaries_generate_ai_summary_api($model, $content, 'categories');
                kognetiks_ai_summaries_add_categories($pid, $ai_categories);
            }

            if ( 1 === (int) get_option( 'kognetiks_ai_summaries_generate_tags', 1 ) ) {
                $ai_tags = kognetiks_ai_summaries_generate_ai_summary_api($model, $content, 'tags');
                kognetiks_ai_summaries_add_tags($pid, $ai_tags);
            }

            break;

        default:

            // DIAG - Diagnostics
            // kognetiks_ai_summaries_back_trace( 'NOTICE', 'AI summary exists' );

            if ( kognetiks_ai_summaries_ai_summary_is_stale($pid) ) {

                // Limit generations per request to prevent timeout (524) on search/archive pages
                if ( ! kognetiks_ai_summaries_can_generate_this_request() ) {
                    kognetiks_ai_summaries_prod_trace( 'NOTICE', 'Skipping stale refresh (per-request limit) pid=' . (int) $pid );
                    delete_transient( $lock_key );
                    return $ai_summary; // Return existing (stale) summary rather than null
                }

                // kognetiks_ai_summaries_back_trace( 'NOTICE', 'AI summary is stale' );
                $ai_summary = kognetiks_ai_summaries_generate_ai_summary_api($model, $content);

                // Validate the AI summary response
                if ($ai_summary == 'ERROR' || ! kognetiks_ai_summaries_validate_ai_summary( $ai_summary )) {

                    kognetiks_ai_summaries_prod_trace( 'ERROR', 'API error or invalid summary (stale refresh) pid=' . (int) $pid );
                    delete_transient( $lock_key );
                    return null;

                } else {

                    kognetiks_ai_summaries_update_ai_summary($pid, $ai_summary, $post_modified);
                }

                if ( 1 === (int) get_option( 'kognetiks_ai_summaries_generate_categories', 1 ) ) {
                    $ai_categories = kognetiks_ai_summaries_generate_ai_summary_api($model, $content, 'categories');
                    kognetiks_ai_summaries_add_categories($pid, $ai_categories);
                }

                if ( 1 === (int) get_option( 'kognetiks_ai_summaries_generate_tags', 1 ) ) {
                    $ai_tags = kognetiks_ai_summaries_generate_ai_summary_api($model, $content, 'tags');
                    kognetiks_ai_summaries_add_tags($pid, $ai_tags);
                }

                break;
            }

    }

    // Final validation check - ensure we have a valid summary before processing
    if ( empty( $ai_summary ) || ! kognetiks_ai_summaries_validate_ai_summary( $ai_summary ) ) {
        kognetiks_ai_summaries_prod_trace( 'WARNING', 'Invalid or empty summary after switch pid=' . (int) $pid );
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

    delete_transient( $lock_key );
    return $ai_summary;

}

/**
 * Returns the default LLM prompt instruction prefix for a given type.
 * Used when options are blank and for validation on the settings page.
 *
 * @param string $type One of 'summary', 'categories', 'tags'.
 * @return string Default instruction prefix.
 */
function kognetiks_ai_summaries_get_prompt_instruction_default( $type ) {
	$defaults = array(
		'summary'    => "Here are some special instructions for the content that follows - please summarize this content in ",
		'categories' => "Here are some special instructions for the content that follows - please suggest ",
		'tags'       => "Here are some special instructions for the content that follows - please suggest ",
	);
	return isset( $defaults[ $type ] ) ? $defaults[ $type ] : '';
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

            // Prepare special instructions: user instructions + appended word count
            // $prompt_base = get_option( 'kognetiks_ai_summaries_prompt_instructions_summary', "Here are some special instructions for the content that follows - please summarize this content in " );
            // $special_instructions = $prompt_base . $word_count . " or fewer words and just return the summary text without stating that it is a summary: ";

            $default_summary = kognetiks_ai_summaries_get_prompt_instruction_default( 'summary' );
            $prompt_base = get_option( 'kognetiks_ai_summaries_prompt_instructions_summary', $default_summary );
            if ( trim( (string) $prompt_base ) === '' ) {
                $prompt_base = $default_summary;
            }
            $special_instructions =
                $prompt_base .
                $word_count .
                " or fewer words and just return the summary text without stating that it is a summary. Never use an em dash. Rewrite sentences to avoid it: ";

            break;

        case 'categories':

            // Get the desired category count from options
            $category_count = esc_attr(get_option('kognetiks_ai_summaries_category_count', 3));

            // Prepare special instructions: user instructions + appended category count
            $default_categories = kognetiks_ai_summaries_get_prompt_instruction_default( 'categories' );
            $prompt_base = get_option( 'kognetiks_ai_summaries_prompt_instructions_categories', $default_categories );
            if ( trim( (string) $prompt_base ) === '' ) {
                $prompt_base = $default_categories;
            }
            $special_instructions = $prompt_base . $category_count . " one-word (no compound words) categories or fewer and just return the categories separated by commas without stating that these are the categories: ";

            break;

        case 'tags':

            // Get the desired tag count from options
            $tag_count = esc_attr(get_option('kognetiks_ai_summaries_tag_count', 3));

            // Prepare special instructions: user instructions + appended tag count
            $default_tags = kognetiks_ai_summaries_get_prompt_instruction_default( 'tags' );
            $prompt_base = get_option( 'kognetiks_ai_summaries_prompt_instructions_tags', $default_tags );
            if ( trim( (string) $prompt_base ) === '' ) {
                $prompt_base = $default_tags;
            }
            $special_instructions = $prompt_base . $tag_count . " one-word (no compound words) tags or fewer and just return the tags separated by commas without stating that these are the tags: ";

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

// Run dbDelta for AI summary table (create or alter). Only called from upgrade routine or create_ai_summary_table when table missing in admin.
function kognetiks_ai_summaries_run_db_schema_upgrade() {

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

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );

    if ( $wpdb->last_error ) {
        kognetiks_ai_summaries_prod_trace( 'ERROR', 'Error creating/upgrading AI summary table' );
    }
}

// Ensure AI summary table exists. Fast path: exists check only; create via dbDelta only when missing and in admin context (never during get_the_excerpt).
function kognetiks_ai_summaries_create_ai_summary_table() {

    global $wpdb;

    $table_name = $wpdb->prefix . 'kognetiks_ai_summaries';
    if ( $wpdb->get_var( "SHOW TABLES LIKE '" . esc_sql( $table_name ) . "'" ) === $table_name ) {
        return;
    }

    // Table missing: only run dbDelta in admin so it never runs during get_the_excerpt (front-end).
    if ( ! is_admin() ) {
        return;
    }

    kognetiks_ai_summaries_run_db_schema_upgrade();
}

// Run DB schema upgrade when version option is lower than current (activation + admin_init only).
function kognetiks_ai_summaries_maybe_upgrade_db() {

    $current = (int) get_option( 'kognetiks_ai_summaries_db_version', 0 );

    if ( $current >= KOGNETIKS_AI_SUMMARIES_DB_VERSION ) {
        return;
    }

    kognetiks_ai_summaries_run_db_schema_upgrade();
    update_option( 'kognetiks_ai_summaries_db_version', KOGNETIKS_AI_SUMMARIES_DB_VERSION );
}

// Helper function to update post_excerpt based on setting
function kognetiks_ai_summaries_update_post_excerpt( $pid, $ai_summary ) {

    // Never write API errors or invalid content to the excerpt
    if ( empty( $ai_summary ) || ! kognetiks_ai_summaries_validate_ai_summary( $ai_summary ) ) {
        kognetiks_ai_summaries_prod_trace( 'NOTICE', 'Excerpt update skipped (invalid or error content) pid=' . (int) $pid );
        return;
    }

    $replacement_setting = esc_attr(get_option('kognetiks_ai_summaries_post_excerpt_replacement', 'Do Not Replace'));

    if ( $replacement_setting === 'Do Not Replace' ) {
        kognetiks_ai_summaries_prod_trace( 'NOTICE', 'Excerpt update skipped (Do Not Replace) pid=' . (int) $pid );
        return;
    }

    // Get the current post
    $post = get_post( $pid );
    if ( ! $post ) {
        return;
    }

    // Get the current post_excerpt
    $current_excerpt = $post->post_excerpt;

    // Determine if we should update
    $should_update = false;
    if ( $replacement_setting === 'Replace' ) {
        $should_update = true;
    } elseif ( $replacement_setting === 'Replace if Blank' ) {
        // Only update if post_excerpt is empty or blank
        if ( empty( trim( $current_excerpt ) ) ) {
            $should_update = true;
        }
    }

    // Update post_excerpt if needed
    if ( $should_update ) {
        // Use the provided summary (which is the full summary from the database)
        $full_summary = $ai_summary;
        
        // Remove unwanted prefixes that might be in the summary
        $full_summary = preg_replace('/^These Are The Categories:\s*/i', '', $full_summary);
        $full_summary = preg_replace('/^These Are The Tags:\s*/i', '', $full_summary);
        $full_summary = preg_replace('/^These are the categories:\s*/i', '', $full_summary);
        $full_summary = preg_replace('/^These are the tags:\s*/i', '', $full_summary);
        $full_summary = preg_replace('/^Categories:\s*/i', '', $full_summary);
        $full_summary = preg_replace('/^Tags:\s*/i', '', $full_summary);
        $full_summary = trim($full_summary);
        
        // Get the desired excerpt length from options
        $ai_summary_length = intval( esc_attr( get_option( 'kognetiks_ai_summaries_length', 55 ) ) );

        // Trim the AI summary to the specified length
        $trimmed_summary = wp_trim_words( $full_summary, $ai_summary_length, '' );

        // Check if the text was trimmed by comparing the original and trimmed versions
        if ( str_word_count( $full_summary ) > $ai_summary_length ) {
            // Remove trailing punctuation if present
            $trimmed_summary = rtrim($trimmed_summary, '.,!?;:');
            // Append ellipsis
            $trimmed_summary .= '...';
        }

        // Update only post_excerpt via $wpdb to avoid changing post_modified.
        // wp_update_post would set post_modified to now, causing our stale check to trigger unnecessary regeneration.
        global $wpdb;
        $wpdb->update(
            $wpdb->posts,
            array( 'post_excerpt' => $trimmed_summary ),
            array( 'ID' => $pid ),
            array( '%s' ),
            array( '%d' )
        );
        clean_post_cache( $pid );
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
    $table_name = $wpdb->prefix . 'kognetiks_ai_summaries';
    $result = $wpdb->query( $wpdb->prepare(
        'INSERT INTO %i (post_id, ai_summary, post_modified)
         VALUES (%d, %s, %s)
         ON DUPLICATE KEY UPDATE
         ai_summary = VALUES(ai_summary),
         post_modified = VALUES(post_modified)',
        $table_name, $pid, $ai_summary, $post_modified
    ) );


    // Handle any errors
    if ( $wpdb->last_error ) {

        // DIAG - Diagnostics
        kognetiks_ai_summaries_prod_trace( 'ERROR', 'Error inserting or updating AI summary: ' . $wpdb->last_error );

    } else {

        // DIAG - Diagnostics
        // kognetiks_ai_summaries_back_trace( 'NOTICE', 'AI summary successfully inserted or updated.' );

        wp_cache_delete( 'kognetiks_ai_summaries_ai_modified_' . $pid );
        wp_cache_delete( 'kognetiks_ai_summaries_post_modified_' . $pid );
        wp_cache_delete( 'kognetiks_ai_summaries_' . $pid );
        wp_cache_delete( 'kognetiks_ai_summaries_post_' . $pid );

        // Update post_excerpt based on setting
        kognetiks_ai_summaries_update_post_excerpt( $pid, $ai_summary );

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
    $table_name = $wpdb->prefix . 'kognetiks_ai_summaries';
    
    if ($row === false) {

        $row = $wpdb->get_row(
            $wpdb->prepare(
                'SELECT ai_summary, post_modified FROM %i WHERE post_id = %d',
                $table_name, $pid
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

    $table_name = $wpdb->prefix . 'kognetiks_ai_summaries';

    $wpdb->delete(
        "{$table_name}",
        array( 'post_id' => $pid )
    );

    // Handle any errors
    if ( $wpdb->last_error ) {

        kognetiks_ai_summaries_prod_trace( 'ERROR', 'Error deleting AI summary from table' );

    } else {

        wp_cache_delete( 'kognetiks_ai_summaries_ai_modified_' . $pid );
        wp_cache_delete( 'kognetiks_ai_summaries_post_modified_' . $pid );
        wp_cache_delete( 'kognetiks_ai_summaries_' . $pid );
        wp_cache_delete( 'kognetiks_ai_summaries_post_' . $pid );

    }

}

/**
 * Clear plugin caches for a post when it is saved.
 * Ensures the stale check and content fetch use fresh data on the next excerpt request.
 * Important when using persistent object cache (Redis, Memcached).
 *
 * @param int $post_id Post ID.
 */
function kognetiks_ai_summaries_clear_post_caches_on_save( $post_id ) {
	$pid = (int) $post_id;
	if ( $pid <= 0 ) {
		return;
	}
	wp_cache_delete( 'kognetiks_ai_summaries_ai_modified_' . $pid );
	wp_cache_delete( 'kognetiks_ai_summaries_post_modified_' . $pid );
	wp_cache_delete( 'kognetiks_ai_summaries_' . $pid );
	wp_cache_delete( 'kognetiks_ai_summaries_post_' . $pid );
}

/**
 * Trigger AI generation when a post transitions to 'publish'.
 * Ensures excerpt, categories, and tags are populated for home/search before first view.
 * Only runs when AI summaries are enabled and metadata is left blank by the author.
 *
 * @param string  $new_status New post status.
 * @param string  $old_status Old post status.
 * @param WP_Post $post       Post object.
 */
function kognetiks_ai_summaries_maybe_generate_on_publish( $new_status, $old_status, $post ) {
	if ( $new_status !== 'publish' || ! $post instanceof WP_Post ) {
		return;
	}
	$pid = (int) $post->ID;
	if ( $pid <= 0 ) {
		return;
	}
	// Skip revisions and autosaves
	if ( wp_is_post_revision( $pid ) || wp_is_post_autosave( $pid ) ) {
		return;
	}
	// Only run when AI summaries are enabled
	$enabled = esc_attr( get_option( 'kognetiks_ai_summaries_enabled', 'Off' ) );
	if ( $enabled === 'Off' ) {
		return;
	}
	// Check if we should generate: no AI summary yet, or excerpt/categories/tags are blank
	$has_summary = kognetiks_ai_summaries_ai_summary_exists( $pid );
	$valid_summary = ! empty( $has_summary ) && kognetiks_ai_summaries_validate_ai_summary( $has_summary );
	$excerpt_blank = empty( trim( (string) $post->post_excerpt ) );
	$categories    = get_the_category( $pid );
	$cats_blank    = empty( $categories ) || ( count( $categories ) === 1 && strcasecmp( $categories[0]->name, 'Uncategorized' ) === 0 );
	$tags          = get_the_tags( $pid );
	$tags_blank    = empty( $tags );
	$should_run    = ! $valid_summary || $excerpt_blank || $cats_blank || $tags_blank;
	if ( ! $should_run ) {
		return;
	}
	// Bypass per-request limit for publish-triggered generation
	add_filter( 'kognetiks_ai_summaries_generations_per_request', '__return_zero' );
	kognetiks_ai_summaries_generate_ai_summary( $pid, true );
	remove_filter( 'kognetiks_ai_summaries_generations_per_request', '__return_zero' );
}

/**
 * Trigger AI generation when viewing a single post that has no summary.
 * Many themes don't call get_the_excerpt on single post pages (they show full content),
 * so generation would never run. This ensures we generate when the user visits the post.
 */
function kognetiks_ai_summaries_maybe_generate_on_singular_view() {
	if ( ! is_singular() ) {
		return;
	}
	$post = get_queried_object();
	if ( ! $post instanceof WP_Post ) {
		return;
	}
	$pid = (int) $post->ID;
	if ( $pid <= 0 ) {
		return;
	}
	$enabled = esc_attr( get_option( 'kognetiks_ai_summaries_enabled', 'Off' ) );
	if ( $enabled === 'Off' ) {
		return;
	}
	$enabled_types = get_option( 'kognetiks_ai_summaries_enabled_post_types', null );
	if ( null === $enabled_types || ! is_array( $enabled_types ) ) {
		$enabled_types = function_exists( 'kognetiks_ai_summaries_default_enabled_post_types' ) ? kognetiks_ai_summaries_default_enabled_post_types() : array( 'post' => 1, 'page' => 1 );
	}
	if ( 1 !== (int) ( $enabled_types[ $post->post_type ] ?? 0 ) ) {
		return;
	}
	$has_summary = kognetiks_ai_summaries_ai_summary_exists( $pid );
	$valid_summary = ! empty( $has_summary ) && kognetiks_ai_summaries_validate_ai_summary( $has_summary );
	if ( $valid_summary ) {
		return;
	}
	add_filter( 'kognetiks_ai_summaries_generations_per_request', '__return_zero' );
	kognetiks_ai_summaries_generate_ai_summary( $pid, true );
	remove_filter( 'kognetiks_ai_summaries_generations_per_request', '__return_zero' );
}

// Check if an AI summary is stale
function kognetiks_ai_summaries_ai_summary_is_stale( $pid ) {
	$pid = (int) $pid;
	if ( $pid <= 0 ) {
		return false;
	}

	global $wpdb;

	// Cache keys must be distinct.
	$cache_key_ai   = 'kognetiks_ai_summaries_ai_modified_' . $pid;
	$cache_key_post = 'kognetiks_ai_summaries_post_modified_' . $pid;
    $table_name = $wpdb->prefix . 'kognetiks_ai_summaries';

	// 1) AI summary modified (from summaries table)
	$ai_modified = wp_cache_get( $cache_key_ai );
	if ( false === $ai_modified ) {
		// phpcs:ignore PluginCheck.Security.DirectDB.UnescapedDBParameter -- Table name from wpdb prefix, escaped via prepare() %i
		$ai_modified = $wpdb->get_var(
			$wpdb->prepare( 'SELECT post_modified FROM %i WHERE post_id = %d LIMIT 1', $table_name, $pid )
		);
		// Cache with short TTL (~60s) to avoid repeated queries.
		wp_cache_set( $cache_key_ai, (string) $ai_modified, '', 60 );
	}

	// No AI row means "not stale" (generator should treat this as "needs generation" elsewhere).
	if ( empty( $ai_modified ) ) {
		return false;
	}

	// 2) Post modified (from wp_posts)
	$post_modified = wp_cache_get( $cache_key_post );
	if ( false === $post_modified ) {
		$post_modified = $wpdb->get_var(
			$wpdb->prepare( 'SELECT post_modified FROM %i WHERE ID = %d LIMIT 1', $wpdb->posts, $pid )
		);
		// Cache with short TTL (~60s).
		wp_cache_set( $cache_key_post, (string) $post_modified, '', 60 );
	}

	if ( empty( $post_modified ) ) {
		return false;
	}

	$ai_ts   = strtotime( $ai_modified );
	$post_ts = strtotime( $post_modified );

	if ( ! $ai_ts || ! $post_ts ) {
		return false;
	}

	return $post_ts > $ai_ts;
}


// Update an AI summary in the AI summary table
function kognetiks_ai_summaries_update_ai_summary( $pid, $ai_summary, $post_modified ) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_update_ai_summary' );

    global $wpdb;

    $table_name = $wpdb->prefix . 'kognetiks_ai_summaries';
    
    // Prepare and execute the query
    $wpdb->query( $wpdb->prepare(
        'INSERT INTO %i (post_id, ai_summary, post_modified)
         VALUES (%d, %s, %s)
         ON DUPLICATE KEY UPDATE
         ai_summary = VALUES(ai_summary),
         post_modified = VALUES(post_modified)',
        $table_name, $pid, $ai_summary, $post_modified
    ) );
    
    // Handle any errors
    if ( $wpdb->last_error ) {
        kognetiks_ai_summaries_prod_trace( 'ERROR', 'Error updating AI summary in table' );
    } else {
        wp_cache_delete( 'kognetiks_ai_summaries_ai_modified_' . $pid );
        wp_cache_delete( 'kognetiks_ai_summaries_post_modified_' . $pid );
        wp_cache_delete( 'kognetiks_ai_summaries_' . $pid );
        wp_cache_delete( 'kognetiks_ai_summaries_post_' . $pid );
        // Update post_excerpt based on setting
        kognetiks_ai_summaries_update_post_excerpt( $pid, $ai_summary );
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

        $full_ai_summary = kognetiks_ai_summaries_ai_summary_exists( $post->ID );

        if ( ! empty( $full_ai_summary ) && kognetiks_ai_summaries_validate_ai_summary( $full_ai_summary ) ) {
            $replacement_setting = esc_attr(get_option('kognetiks_ai_summaries_post_excerpt_replacement', 'Do Not Replace'));
            if ( $replacement_setting === 'Replace' || $replacement_setting === 'Replace if Blank' ) {
                kognetiks_ai_summaries_update_post_excerpt( $post->ID, $full_ai_summary );
            }
        }

        // Get the desired excerpt length from options
        $ai_summary_length = intval( esc_attr( get_option( 'kognetiks_ai_summaries_length', 55 ) ) );

        // Trim the AI summary to the specified length
        $excerpt = wp_trim_words( $ai_summary, $ai_summary_length, '...' );

    } else {

        // AI summary not available or invalid - use post_excerpt only if it's not an error message
        $excerpt = $post->post_excerpt;
        if ( ! empty( $excerpt ) && ! kognetiks_ai_summaries_validate_ai_summary( $excerpt ) ) {
            // Current excerpt looks like an API error - discard it and use content-based default
            $excerpt = '';
        }

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
