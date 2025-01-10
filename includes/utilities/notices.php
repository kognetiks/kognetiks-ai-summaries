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
function kognetiks_ai_summaries_general_admin_notice($message = null) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_general_admin_notice' );

    if (!empty($message)) {
        printf(
            '<div class="%1$s"><p><strong>Kognetiks AI Summaries: </strong>%2$s</p></div>',
            esc_attr('notice notice-error is-dismissible'),
            esc_html($message)
        );
    }

}
add_action('admin_notices', 'kognetiks_ai_summaries_general_admin_notice');

// Notify outcomes
function kognetiks_ai_summaries_admin_notice() {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_admin_notice' );

    // Suppress Notices On/Off
    global $kognetiks_ai_summaries_suppress_notices;
    $kognetiks_ai_summaries_suppress_notices = esc_attr(get_option('kognetiks_ai_summaries_suppress_notices', 'Off'));

    if ($kognetiks_ai_summaries_suppress_notices == 'On') {
        return;
    }

    // Check if notice is already dismissed
    $kognetiks_ai_summaries_status = esc_attr(get_option('kognetiks_ai_summaries_results'));
    if ($kognetiks_ai_summaries_status) {
        // Check if notice is already dismissed
        $dismiss_url = wp_nonce_url(
            add_query_arg('kognetiks_ai_summaries_dismiss_notice', '1'),
            'kognetiks_ai_summaries_dismiss_notice',
            '_kognetiks_ai_summaries_dismiss_nonce'
        );
        echo '<div class="notice notice-success is-dismissible"><p><strong>Kognetiks AI Summaries:</strong> ' . esc_html($kognetiks_ai_summaries_status) . ' <a href="' . esc_url($dismiss_url) . '">Dismiss</a></p></div>';
    }

}
add_action('admin_notices', 'kognetiks_ai_summaries_admin_notice');

// Handle outcome notification dismissal
function kognetiks_ai_summaries_dismiss_notice() {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_dismiss_notice' );

    if (isset($_GET['kognetiks_ai_summaries_dismiss_notice']) && isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'kognetiks_ai_summaries_dismiss_notice')) {
        delete_option('kognetiks_ai_summaries_status');
    }

}
add_action('admin_init', 'kognetiks_ai_summaries_dismiss_notice');
