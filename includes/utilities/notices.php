<?php
/**
 * Kognetiks AI Summaries - Notices
 *
 * This file contains the code for handling notices.
 *
 * 
 *
 * @package kognetiks-ai-summaries
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

// General function to display the message
function ksum_general_admin_notice($message = null) {

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', 'ksum_general_admin_notice' );

    if (!empty($message)) {
        printf(
            '<div class="%1$s"><p><strong>Kognetiks AI Summaries: </strong>%2$s</p></div>',
            esc_attr('notice notice-error is-dismissible'),
            esc_html($message)
        );

        return;

    }

}
add_action('admin_notices', 'ksum_general_admin_notice');

// Notify outcomes
function ksum_admin_notice() {

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', 'ksum_admin_notice' );

    // Suppress Notices On/Off
    global $ksum_suppress_notices;
    $ksum_suppress_notices = esc_attr(get_option('ksum_suppress_notices', 'Off'));

    if ($ksum_suppress_notices == 'On') {
        return;
    }

    // Check if notice is already dismissed
    $ksum_status = esc_attr(get_option('ksum_results'));
    if ($ksum_status) {
        // Check if notice is already dismissed
        $dismiss_url = wp_nonce_url(
            add_query_arg('dismiss_ksum_notice', '1'),
            'dismiss_ksum_notice',
            '_ksum_dismiss_nonce'
        );
        echo '<div class="notice notice-success is-dismissible"><p><strong>Kognetiks AI Summaries:</strong> ' . esc_html($ksum_status) . ' <a href="' . esc_url($dismiss_url) . '">Dismiss</a></p></div>';
    }

}
add_action('admin_notices', 'ksum_admin_notice');

// Handle outcome notification dismissal
function dismiss_ksum_notice() {

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', 'dismiss_ksum_notice' );

    if (isset($_GET['dismiss_ksum_notice']) && isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'ksum_dismiss_notice')) {
        delete_option('ksum_status');
    }

}
add_action('admin_init', 'dismiss_ksum_notice');
