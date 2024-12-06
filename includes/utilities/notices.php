<?php
/**
 * Kognetiks AI Summaries for WordPress - Notices
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
    if (!empty($message)) {
        printf('<div class="%1$s"><p><strong>Kognetiks AI Summaries: </strong>%2$s</p></div>', 'notice notice-error is-dismissible', $message);
        return;
    }
}
add_action('admin_notices', 'ksum_general_admin_notice');

// Notify outcomes
function ksum_admin_notice() {

    // Suppress Notices On/Off
    global $ksum_suppress_notices;
    $ksum_suppress_notices = esc_attr(get_option('ksum_suppress_notices', 'Off'));

    if ($ksum_suppress_notices == 'On') {
        return;
    }

    // Check if notice is already dismissed
    $ksum_status = get_option('ksum_results');
    if ($ksum_status) {
        // Check if notice is already dismissed
        $dismiss_url = wp_nonce_url(
            add_query_arg('dismiss_ksum_notice', '1'),
            'dismiss_ksum_notice',
            '_ksum_dismiss_nonce'
        );
        echo '<div class="notice notice-success is-dismissible"><p><strong>Kognetiks AI Summaries:</strong> ' . $ksum_status . ' <a href="' . $dismiss_url . '">Dismiss</a></p></div>';
    }

}
add_action('admin_notices', 'ksum_admin_notice');

// Handle outcome notification dismissal

function dismiss_ksum_notice() {

    if (isset($_GET['dismiss_ksum_notice'])) {
        delete_option('ksum_status');
    }

}
add_action('admin_init', 'dismiss_ksum_notice');
