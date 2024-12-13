<?php
/**
 * Kognetiks AI Summaries for WordPress - Anthropic API - Ver 1.0.0
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
    ksum_back_trace( 'NOTICE', 'ksum_anthropic_api_call');

    global $errorResponses;

    // The current ChatGPT API URL endpoint for chatgpt-4o-latest
    // $api_url = ' https://api.anthropic.com/v1/messages';
    // Set the API endpoint
    $api_url = ksum_get_chat_completions_api_url();

    // Set the headers
    $headers = array(
        'x-api-key' => $api_key,
        'anthropic-version' => '2023-06-01',
        'Content-Type' => 'application/json'
    );

    // Select the OpenAI Model
    // Get the saved model from the settings or default to "chatgpt-4o-latest"
    $model = esc_attr(get_option('ksum_anthropic_model_choice', 'chatgpt-4o-latest'));

    // FIXME - OVERRIDE MODEL
    $model = 'claude-3-5-sonnet-20240620';
 
    // Max tokens - Ver 1.4.2
    $max_tokens = intval(esc_attr(get_option('ksum_anthropic_max_tokens_setting', '500')));

    // Conversation Context - Ver 1.6.1
    $context = esc_attr(get_option('ksum_anthropic_conversation_context', 'You are a versatile, friendly, and helpful assistant designed to support me in a variety of tasks that responds in Markdown.'));

    // Temperature - Ver 2.1.8
    $temperature = floatval(esc_attr(get_option('ksum_anthropic_temperature', '0.5')));

    // Top P - Ver 2.1.8
    $top_p = floatval(esc_attr(get_option('ksum_anthropic_top_p', '1.0')));

    // DIAG - Diagnostics
    ksum_back_trace( 'NOTICE', 'Model: ' . $model);
    ksum_back_trace( 'NOTICE', 'Max Tokens: ' . $max_tokens);
    ksum_back_trace( 'NOTICE', 'Context: ' . $context);
    ksum_back_trace( 'NOTICE', 'Temperature: ' . $temperature);
    ksum_back_trace( 'NOTICE', 'Top P: ' . $top_p);
    ksum_back_trace( 'NOTICE', 'Message: ' . $message);
    
    // Set the body
    $body = array(
        'model' => $model,
        'max_tokens' => 500,
        'messages' => array(
            array(
                'role' => 'user',
                'content' => $message
            )
        )
    );

    // Encode the body
    $body = json_encode($body);

    // DIAG - Diagnostics
    ksum_back_trace( 'NOTICE', 'URL: ' . $api_url);
    ksum_back_trace( 'NOTICE', 'Headers: ' . print_r($headers, true));
    ksum_back_trace( 'NOTICE', 'Body: ' . $body);

    // Call the API
    $response = wp_remote_post($api_url, array(
        'headers' => $headers,
        'body' => $body
    ));

    // Get the response body
    $response_body = wp_remote_retrieve_body($response);

    // Decode the response body
    $response_body = json_decode($response_body, true);

    // DIAG - Diagnostics
    ksum_back_trace( 'NOTICE', 'Response: ' . print_r($response_body, true));

    // Return the response
    return $response_body;
    
}