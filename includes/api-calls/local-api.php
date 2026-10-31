<?php
/**
 * Kognetiks AI Summaries - Local API Calls
 *
 * This file contains the code to make API calls to the Local AI Server.
 *
 * @package kognetiks-ai-summaries
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

// Function to make API calls to Local AI Server
function kognetiks_ai_summaries_local_api_call($api_key, $message) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_local_api_call');

    // Get the base URL
    $base_url = get_option( 'kognetiks_ai_summaries_local_base_url', 'http://127.0.0.1:1337/v1' );
    if ( ! is_string( $base_url ) || '' === $base_url ) {
        $base_url = 'http://127.0.0.1:1337/v1';
    }
    $base_url = esc_url_raw( untrailingslashit( $base_url ) );
    if ( '' === $base_url || ! wp_http_validate_url( $base_url ) ) {
        $base_url = 'http://127.0.0.1:1337/v1';
    }

    // Current JAN.AI serves POST /v1/chat/completions and starts the model there.
    // /v1/models/start is not provided by this server.
    $api_url = $base_url . '/chat/completions';

    $headers = array(
        'Content-Type' => 'application/json',
        'Accept'       => 'application/json',
    );
    if ( is_string( $api_key ) && '' !== $api_key && 'NOT SET' !== $api_key ) {
        $headers['Authorization'] = 'Bearer ' . $api_key;
    }

    // Retrieve model settings. Max tokens is the value saved by the Local settings screen.
    $model = get_option( 'kognetiks_ai_summaries_local_model_choice', 'llama3.2-3b-instruct' );
    if ( ! is_string( $model ) || '' === $model ) {
        $model = 'llama3.2-3b-instruct';
    }
    $max_tokens = (int) get_option( 'kognetiks_ai_summaries_local_max_tokens', 1000 );
    if ( $max_tokens < 1 ) {
        $max_tokens = 1000;
    }
    $temperature = (float) get_option( 'kognetiks_ai_summaries_local_temperature', 0.5 );
    $top_p       = (float) get_option( 'kognetiks_ai_summaries_local_top_p', 1 );
    $context     = get_option( 'kognetiks_ai_summaries_local_conversation_context', 'You are a versatile, friendly, and helpful assistant designed to support me in a variety of tasks that responds in Markdown.' );
    if ( ! is_string( $context ) || '' === $context ) {
        $context = 'You are a versatile, friendly, and helpful assistant designed to support me in a variety of tasks that responds in Markdown.';
    }
    $timeout = (int) get_option( 'kognetiks_ai_summaries_local_timeout_setting', 360 );
    if ( $timeout < 1 ) {
        $timeout = 360;
    }

    $body = array(
        'model'       => $model,
        'messages'    => array(
            array(
                'role'    => 'system',
                'content' => $context,
            ),
            array(
                'role'    => 'user',
                'content' => $message,
            ),
        ),
        'max_tokens'  => $max_tokens,
        'temperature' => $temperature,
        'top_p'       => $top_p,
        'stream'      => false,
        'n'           => 1,
    );

    // API request arguments
    $args = array(
        'headers' => $headers,
        'body'    => json_encode($body),
        'method'  => 'POST',
        'timeout' => $timeout,
        'data_format' => 'body',
    );

    // Perform the request
    $response = wp_remote_post($api_url, $args);

    // Log response for debugging
    // kognetiks_ai_summaries_back_trace( 'NOTICE', '$response: ' . print_r($response, true));

    // Handle request errors
    if (is_wp_error($response)) {
        return 'Error: ' . $response->get_error_message() . ' Please check Settings for a valid API key.';
    }

    // Decode the response
    $response_body = json_decode(wp_remote_retrieve_body($response), true);

    // Get the response code
    $response_code = wp_remote_retrieve_response_code($response);

    if ($response['response']['code'] == 200) {
        // kognetiks_ai_summaries_back_trace( 'NOTICE', 'API Response: ' . print_r($response, true));
    } else {
        // kognetiks_ai_summaries_back_trace( 'WARNING', 'API Response: ' . print_r($response, true));
    }

    if ( $response_code < 200 || $response_code >= 300 ) {
        $detail = '';
        if ( is_array( $response_body ) && isset( $response_body['error']['message'] ) && is_string( $response_body['error']['message'] ) ) {
            $detail = $response_body['error']['message'];
        }
        if ( '' === $detail ) {
            $detail = 'HTTP ' . $response_code;
        }
        kognetiks_ai_summaries_prod_trace( 'WARNING', 'Local API request failed: ' . $detail );
        return 'Error: ' . $detail;
    }

    $response_text = '';
    if ( isset( $response_body['choices'][0]['message']['content'] ) && is_string( $response_body['choices'][0]['message']['content'] ) ) {
        $response_text = trim( $response_body['choices'][0]['message']['content'] );
    }
    if ( '' !== $response_text ) {
        return $response_text;
    }

    kognetiks_ai_summaries_prod_trace( 'WARNING', 'No valid response text found in the Local API response.' );
    return 'Error: No valid response text found in API response.';

}

// Start the chat completions model
function kognetiks_ai_summaries_local_start_model() {

    // DiAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'chatbot_local_start_model');

    global $kognetiks_ai_summaries_local_model_status;

    // Get the model choice from this plugin, not the chatbot settings.
    $model = get_option( 'kognetiks_ai_summaries_local_model_choice', 'llama3.2-3b-instruct' );
    if ( ! is_string( $model ) || '' === $model ) {
        $model = 'llama3.2-3b-instruct';
    }

    $base_url = get_option( 'kognetiks_ai_summaries_local_base_url', 'http://127.0.0.1:1337/v1' );
    if ( ! is_string( $base_url ) || '' === $base_url ) {
        $base_url = 'http://127.0.0.1:1337/v1';
    }
    $base_url = esc_url_raw( untrailingslashit( $base_url ) );
    if ( '' === $base_url || ! wp_http_validate_url( $base_url ) ) {
        $base_url = 'http://127.0.0.1:1337/v1';
    }
    $api_url = $base_url . '/models/start';

    // Prepare the data
    $data = array(
        'model' => $model
    );

    // Send the request
    $response = wp_remote_post($api_url, array(
        'headers' => array(
            'Content-Type' => 'application/json',
        ),
        'body' => json_encode(array(
            'model' => $model
        )),
    ));

    // Check for errors
    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        // Log the error
        kognetiks_ai_summaries_prod_trace( 'ERROR', $error_message );
        // Set the model status
        $kognetiks_ai_summaries_local_model_status = 'error';
        return $response;
    }

    // Get the response body
    $response_body = wp_remote_retrieve_body($response);

    // DiAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', '$response_body: ' . $response_body);

    // Set the model status
    $kognetiks_ai_summaries_local_model_status = 'started';

    return $response_body;
    
}