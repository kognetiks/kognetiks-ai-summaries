<?php
/**
 * Kognetiks AI Summaries - Anthropic API - Ver 1.0.0
 *
 * This file contains the code for the Anthropic API calls.
 *
 * 
 * @package kognetiks-ai-summaries
 */

 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

// Call the NVIDIA API without trappings
function ksum_anthropic_api_call($api_key, $message) {

    // DIAG - Diagnostics
    ksum_back_trace('NOTICE', 'ksum_anthropic_api_call');

    global $errorResponses;

    // API URL
    $api_url = ksum_get_chat_completions_api_url();

    // Headers
    $headers = array(
        'x-api-key' => $api_key,
        'anthropic-version' => '2023-06-01',
        'Content-Type' => 'application/json',
    );

    // Options
    $model = esc_attr(get_option('ksum_anthropic_model_choice', 'claude-3-5-sonnet-latest'));
    $max_tokens = intval(esc_attr(get_option('ksum_anthropic_max_tokens_setting', 500)));
    $context = sanitize_text_field(esc_attr(get_option('ksum_anthropic_conversation_context', '')));
    $temperature = floatval(esc_attr(get_option('ksum_anthropic_temperature', 0.5)));
    $top_p = floatval(esc_attr(get_option('ksum_anthropic_top_p', 1.0)));
    $timeout = intval(esc_attr(get_option('ksum_anthropic_timeout_setting', 240)));

    // Body
    $body = wp_json_encode(array(
        'model' => $model,
        'max_tokens' => $max_tokens,
        'messages' => array(
            array(
                'role' => 'user',
                'content' => $message,
            ),
        ),
    ));

    // API Call
    $response = wp_remote_post($api_url, array(
        'headers' => $headers,
        'body'    => $body,
        'timeout' => $timeout,
    ));

    // Handle WP Error
    if (is_wp_error($response)) {

        // DIAG - Diagnostics
        ksum_prod_trace('ERROR', 'Error: ' . $response->get_error_message());
        return isset($errorResponses['api_error']) ? $errorResponses['api_error'] : 'An API error occurred.';

    }

    // Retrieve and Decode Response
    $response_body = json_decode(wp_remote_retrieve_body($response), true);

    // Handle API Errors
    if (isset($response_body['error'])) {

        // DIAG - Diagnostics
        ksum_prod_trace('ERROR', 'Error: Type: ' . $response_body['error']['type'] . ' Message: ' . $response_body['error']['message']);
        return isset($errorResponses['api_error']) ? $errorResponses['api_error'] : 'An error occurred.';

    }

    // Extract Response
    if (!empty($response_body['content'][0]['text'])) {

        return $response_body['content'][0]['text'];

    }

    // Fallback Response
    // DIAG - Diagnostics
    ksum_prod_trace('ERROR', 'No valid response received from API.');
    return 'No response received.';

}
