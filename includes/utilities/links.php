<?php
/**
 * Kognetiks AI Summaries for WordPress - Simplified Settings Links
 *
 * This file contains the code for deactivating and/or deleting the plugin.
 * 
 *
 * @package kognetiks-ai-summaries
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

// Add link to ChatGPT options in the plugins page.
function ksum_plugin_action_links( $links ) {

    $settings_link = '<a href="' . admin_url( 'options-general.php?page=kognetiks-ai-summaries' ) . '">' . __( 'Settings', 'kognetiks-ai-summaries' ) . '</a>';
    $support_link = '<a href="' . admin_url( 'options-general.php?page=kognetiks-ai-summaries&tab=support' ) . '">' . __( 'Support', 'kognetiks-ai-summaries' ) . '</a>';
    array_unshift( $links, $settings_link, $support_link );
    return $links;

}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'ksum_plugin_action_links' );

// Add deactivation link in the plugin row meta
function ksum_plugin_row_meta( $links, $file ) {

    if ( plugin_basename( __FILE__ ) == $file ) {
        $deactivate_link = '<a href="' . wp_nonce_url( 'plugins.php?action=deactivate&amp;plugin=' . urlencode( plugin_basename( __FILE__ ) ), 'deactivate-plugin_' . plugin_basename( __FILE__ ) ) . '">' . __( 'Deactivate', 'chatbot-chatgpt' ) . '</a>';
        $links[] = $deactivate_link;
    }
    return $links;
    
}
add_filter( 'plugin_row_meta', 'ksum_plugin_row_meta', 10, 2 );
