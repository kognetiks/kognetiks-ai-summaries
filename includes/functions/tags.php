<?php
/**
 * Kognetiks AI Summaries - Tag Functions - Ver 1.0.2
 *
 * This file contains the code for the Tag function calls in WordPress.
 *
 * 
 * @package kognetiks-ai-summaries
 */

 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

// Function to get the tags for a post
function kognetiks_ai_summaries_get_tags( $post_id ) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_get_tags');
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'Getting tags for post ID: ' . $post_id );

    $tags = get_the_tags( $post_id );
    $tag_list = array();

    if ( $tags ) {
        foreach ( $tags as $tag ) {
            $tag_list[] = $tag->name;
        }
    }

    // Return the tags list
    return $tag_list;

}

// Function to add tags to the post
function kognetiks_ai_summaries_add_tags($post_id, $tags_string) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_add_tags');
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'Adding tags to post ID: ' . $post_id );

    // Validate input
    if (empty($post_id) || empty($tags_string)) {
        // kognetiks_ai_summaries_back_trace('ERROR', 'Invalid input: Post ID or tags are missing');
        return;
    }

    // Convert the comma-separated string into an array and apply proper case to the tags
    $tags = array_map('trim', explode(',', $tags_string));
    
    // Apply proper case with acronym handling - Ver 1.0.3
    // Check if the function exists (from tools.php)
    if (function_exists('kognetiks_ai_summaries_convert_to_proper_case_with_acronyms')) {
        $tags = array_map('kognetiks_ai_summaries_convert_to_proper_case_with_acronyms', $tags);
    } else {
        // Fallback to ucwords if function not available
        $tags = array_map('ucwords', $tags);
    }

    // Filter out em dash "—" and other invalid tag values - Ver 1.0.3
    $tags = array_filter($tags, function($tag) {
        $tag = trim($tag);
        // Remove em dash (—), en dash (–), regular dash (-), and empty strings
        return !empty($tag) && 
               $tag !== '—' && 
               $tag !== '–' && 
               $tag !== '-' &&
               mb_strlen($tag) > 0;
    });
    
    // Re-index array after filtering
    $tags = array_values($tags);

    if (empty($tags)) {
        // kognetiks_ai_summaries_back_trace('ERROR', 'No valid tags provided after processing');
        return;
    }

    // Assign tags to the post
    $results = wp_set_post_tags($post_id, $tags);

    if (is_wp_error($results)) {
        kognetiks_ai_summaries_prod_trace('ERROR', 'Error adding tags to post ID: ' . $post_id . '. Error: ' . $results->get_error_message());
    } else {
        // kognetiks_ai_summaries_back_trace('NOTICE', 'Successfully added tags to post ID: ' . $post_id);
    }
}

// Function to remove tags from the post
function kognetiks_ai_summaries_remove_tags( $post_id ) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_remove_tags');
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'Removing tags from post ID: ' . $post_id );

    // Remove all the tags from the post
    wp_set_post_tags( $post_id, '' );

}


