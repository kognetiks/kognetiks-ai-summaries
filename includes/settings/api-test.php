<?php
/**
 * Kognetiks AI Summaries for WordPress - Settings - API/Model Test
 *
 * This file contains the code for the Chatbot settings page.
 * It allows users to configure the API key and other parameters
 * required to access the ChatGPT API from their own account.
 *
  * @package kognetiks-ai-summaries
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

// Test OpenAI Model for any errors - Ver 1.6.3
 function ksum_test_api_status() {

    $ksum_ai_platform_choice = esc_attr(get_option('ksum_ai_platform_choice', 'OpenAI'));

    // Reset Status and Error
    if ($ksum_ai_platform_choice == 'OpenAI') {
        update_option('ksum_api_status', 'API Error Type: Status Unknown');
        $api_key = esc_attr(get_option('ksum_openai_api_key', 'NOT SET'));
        // Model and message for testing
        $model = esc_attr(get_option('ksum_openai_model_choice', 'gpt-3.5-turbo'));
        $updated_status = ksum_test_api($api_key, $model);
        update_option('ksum_api_status', $updated_status);
    } elseif ($ksum_ai_platform_choice == 'NVIDIA') {
        update_option('ksum_nvidia_api_status', 'API Error Type: Status Unknown');
        $api_key = esc_attr(get_option('ksum_nvidia_api_key', 'NOT SET'));
        // Model and message for testing
        $model = esc_attr(get_option('ksum_nvidia_model_choice', 'nvidia/llama-3.1-nemotron-51b-instruct'));
        $updated_status = ksum_test_api($api_key, $model);
        update_option('ksum_api_status', $updated_status);
    } else {
        $updated_status = 'API Error Type: Platform Choice Invalid';
        update_option('ksum_api_status', $updated_status);
    }

    return $updated_status;

}

// FIXME - Test the API connection and current status
function ksum_test_api($api_key, $model) {

    // The current API URL endpoint
    // $api_url = 'https://api.openai.com/v1/chat/completions';
    $api_url = get_chat_completions_api_url();

    // DIAG - Diagnostics - Ver 2.1.8
    back_trace( 'NOTICE', 'API URL: ' . $api_url);

    $headers = array(
        'Authorization' => 'Bearer ' . $api_key,
        'Content-Type' => 'application/json',
    );

    // $message = 'Translate the following English text to French: Hello, world!';
    $message = 'Test message.';

    $body = array(
        'model' => $model,
        'max_tokens' => 100,
        'temperature' => 0.5,
        'messages' => array(
            array('role' => 'system', 'content' => 'You are a test function.'),
            array('role' => 'user', 'content' => $message)
        ),
    );

    // DIAG - Diagnostics
    ksum_back_trace( 'NOTICE', 'API Body: ' . print_r(json_encode($body),true));

    $args = array(
        'headers' => $headers,
        'body' => json_encode($body),
        'method' => 'POST',
        'data_format' => 'body',
        'timeout' => 50,
    );

    $response = wp_remote_post($api_url, $args);

    // DIAG - Diagnostics
    ksum_back_trace( 'NOTICE', 'API Response: ' . print_r(json_encode($response),true));

    if (is_wp_error($response)) {
        // DIAG - Log the response body
        ksum_back_trace( 'ERROR', $response->get_error_message());
        return 'WP_Error: ' . $response->get_error_message() . '. Please check Settings for a valid API key or your AI Platform vendor account for additional information.';
    }

    $response_body = json_decode(wp_remote_retrieve_body($response), true);

    // DIAG - Log the response body
    ksum_back_trace( 'NOTICE', '$response_body: ' . print_r($response_body,true));

    // Check for API-specific errors
    //
    // https://platform.openai.com/docs/guides/error-codes/api-errors
    //
    $ksum_ai_platform_choice = esc_attr(get_option('ksum_ai_platform_choice', 'OpenAI'));
    if (isset($response_body['error'])) {
        $error_type = $response_body['error']['type'] ?? 'Unknown';
        $error_message = $response_body['error']['message'] ?? 'No additional information.';
        $updated_status = 'API Error Type: ' . $error_type . ' Message: ' . $error_message;
    } elseif (!empty($response_body['choices'])) {
        $updated_status = 'Success: Connection to the ' . $ksum_ai_platform_choice . ' API was successful!';
        // back_trace( 'SUCCESS', 'API Status: ' . $updated_status);
    } else {
        $updated_status = 'Error: Unable to fetch response from the ' . $ksum_ai_platform_choice . ' API. Please check Settings for a valid API key or your ' . $ksum_ai_platform_choice . ' account for additional information.';
        // back_trace( 'ERROR', 'API Status: ' . $updated_status);
    }

    return $updated_status;

}
