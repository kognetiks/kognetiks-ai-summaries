<?php
/**
 * Kognetiks AI Summaries - Category Functions - Ver 1.0.2
 *
 * This file contains the code for the Category function calls in WordPress.
 *
 * 
 * @package kognetiks-ai-summaries
 */

 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

// Get the categories for the post
function kognetiks_ai_summaries_get_categories($post_id) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_get_categories');
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'Getting cetegories for post ID: ' . $post_id );

    // Get the categories
    $categories = get_the_category($post_id);

    // Return the categories list
    return $categories;

}

// Add the categories to the post
function kognetiks_ai_summaries_add_categories($post_id, $categories_string) {

    // DIAG - Diagnostics
    kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_add_categories');

    if (empty($post_id) || empty($categories_string) || !is_string($categories_string)) {
        kognetiks_ai_summaries_back_trace('ERROR', 'Invalid input: Post ID or categories string is missing/incorrect');
        return;
    }

    // Convert the comma-separated string into an array and upcase the first letter of each category
    $categories = array_map('trim', explode(',', $categories_string));
    $categories = array_map('ucwords', $categories);
    
    $category_ids = [];
    
    foreach ($categories as $category_name) {
        // Check if the category exists
        $term = term_exists($category_name, 'category');
        
        if (!$term || is_wp_error($term)) {
            // Create the category if it doesn't exist
            $term = wp_insert_term($category_name, 'category');
            
            if (is_wp_error($term)) {
                kognetiks_ai_summaries_back_trace('ERROR', 'Error creating category: ' . $category_name . '. Error: ' . $term->get_error_message());
                continue;
            }
        }
        
        // Add the category ID to the list
        $category_ids[] = is_array($term) ? $term['term_id'] : $term;
    }

    // Link the categories to the post
    $results = wp_set_post_terms($post_id, $category_ids, 'category');

    if (is_wp_error($results)) {
        kognetiks_ai_summaries_back_trace('ERROR', 'Error linking categories to post ID: ' . $post_id . '. Error: ' . $results->get_error_message());
    } else {
        kognetiks_ai_summaries_back_trace('NOTICE', 'Successfully added categories to post ID: ' . $post_id);
    }

}

// Remove the categories from the post
function kognetiks_ai_summaries_remove_categories($post_id) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_remove_categories');
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'Getting cetegories for post ID: ' . $post_id );

    // Remove the categories from the post
    wp_remove_object_terms($post_id, 'category', 'category');

}