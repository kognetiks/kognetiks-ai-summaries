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
    die;
}

// Add a menu item in the admin panel
add_action('admin_menu', function() {

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
            'ksum_settings_page',                   // Callback function
            'dashicons-text',                       // Icon
            999                                     // Position
        );

        add_submenu_page(
            'kognetiks_main_menu',                  // Parent slug
            'AI Summaries',                         // Page title
            'AI Summaries',                         // Menu title
            'manage_options',                       // Capability     
            'kognetiks-ai-summaries',               // Menu slug
            'ksum_settings_page'                    // Callback function
        );

    } else {

        // Add this plugin as a submenu of the existing Kognetiks menu
        add_submenu_page(
            'kognetiks_main_menu',                  // Parent slug
            'AI Summaries',                         // Page title
            'AI Summaries',                         // Menu title
            'manage_options',                       // Capability     
            'kognetiks-ai-summaries',               // Menu slug
            'ksum_settings_page'                    // Callback function
        );

    }

});