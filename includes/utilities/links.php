<?php
/**
 * Kognetiks AI Summaries - Simplified Settings Links
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

// Add link to plugin options in the plugins page.
function ksum_plugin_action_links( $links ) {

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', 'ksum_plugin_action_links' );

    if ( current_user_can( 'manage_options' ) ) {
        $settings_link = '<a href="' . esc_url( wp_nonce_url( admin_url( 'admin.php?page=kognetiks-ai-summaries' ), 'kognetiks_ai_summaries_settings' ) ) . '">' . __( 'Settings', 'kognetiks-ai-summaries' ) . '</a>';
        $support_link = '<a href="' . esc_url( wp_nonce_url( admin_url( 'admin.php?page=kognetiks-ai-summaries&tab=support' ), 'kognetiks_ai_summaries_support' ) ) . '">' . __( 'Support', 'kognetiks-ai-summaries' ) . '</a>';
        array_unshift( $links, $settings_link, $support_link );
    }
    return $links;

}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'ksum_plugin_action_links' );

// Add deactivation link in the plugin row meta
function ksum_plugin_row_meta( $links, $file ) {

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', 'ksum_plugin_row_meta' );

    if ( plugin_basename( __FILE__ ) == $file ) {
        $deactivate_link = '<a href="' . esc_url( wp_nonce_url( 'plugins.php?action=deactivate&amp;plugin=' . urlencode( plugin_basename( __FILE__ ) ), 'deactivate-plugin_' . plugin_basename( __FILE__ ) ) ) . '">' . __( 'Deactivate', 'kognetiks-ai-summaries' ) . '</a>';
        $links[] = $deactivate_link;
    }
    return $links;
    
}
add_filter( 'plugin_row_meta', 'ksum_plugin_row_meta', 10, 2 );
