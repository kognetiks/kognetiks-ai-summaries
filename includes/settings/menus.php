<?php
/**
 * Kognetiks AI Summaries - Menus
 *
 * This file contains the code for the administrative menus for the plugin.
 * 
 *
 * @package kognetiks-ai-summaries
 */

 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

// Use a number lower than default (10), e.g., 5.
add_action('admin_menu', 'kognetiks_ai_summaries_register_menus', 7);

// Add a menu item in the admin panel
function kognetiks_ai_summaries_register_menus() {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_register_menus' );

    global $menu;

    // Check if the 'Kognetiks' menu already exists
    $kognetiks_menu_exists = false;

    foreach ( $menu as $menu_item ) {

        if ( isset( $menu_item[2] ) && $menu_item[2] === 'kognetiks_main_menu' ) {
            $kognetiks_menu_exists = true;
            break;
        }

    }

    // If no Kognetiks menu exists, add a standalone menu for this plugin
    if ( ! $kognetiks_menu_exists ) {

        add_menu_page(
            'Kognetiks',                            // Page title
            'Kognetiks',                            // Menu title
            'manage_options',                       // Capability
            'kognetiks_main_menu',                  // Menu slug
            'kognetiks_ai_summaries_settings_page', // Callback function
            'dashicons-rest-api',                   // Icon
            999                                     // Position
        );

        add_submenu_page(
            'kognetiks_main_menu',                  // Parent slug
            'AI Summaries',                         // Page title
            'AI Summaries',                         // Menu title
            'manage_options',                       // Capability     
            'kognetiks-ai-summaries',               // Menu slug
            'kognetiks_ai_summaries_settings_page'  // Callback function
        );

    } else {

        // If Kognetiks menu exists, add this as a submenu
        add_submenu_page(
            'kognetiks_main_menu',                  // Parent slug
            'AI Summaries',                         // Page title
            'AI Summaries',                         // Menu title
            'manage_options',                       // Capability     
            'kognetiks-ai-summaries',               // Menu slug
            'kognetiks_ai_summaries_settings_page'  // Callback function
        );

    }

};

// Remove the extra submenu page
add_action('admin_menu', 'kognetiks_ai_summaries_remove_extra_submenu', 999);
function kognetiks_ai_summaries_remove_extra_submenu() {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_remove_extra_submenu' );

    remove_submenu_page('kognetiks_main_menu', 'kognetiks_main_menu');

}
