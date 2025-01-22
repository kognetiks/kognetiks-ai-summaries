<?php
/**
 * Kognetiks AI Summaries - DeepSeek API - Ver 1.0.1
 *
 * This file contains the code for the DeepSeek API calls.
 *
 * 
 * @package kognetiks-ai-summaries
 */

 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

// Call the DeepSeek API without trappings
function kognetiks_ai_summaries_deepseek_api_call($api_key, $message) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_deepseek_api_call');

    global $kognetiks_ai_summaries_error_responses;

    // The current DeepSeek API URL endpoint for chat completions
    // $api_url = 'https://api.deepseek.com/chat/completions';
    $api_url = kognetiks_ai_summaries_get_chat_completions_api_url();

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', '$api_url: ' . $api_url);

    $headers = array(
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . $api_key,
    );

    // Select the DeepSeek Model
    // Get the saved model from the settings or default to "deepseek-chat"
    $model = esc_attr(get_option('kognetiks_ai_summaries_deepseek_model_choice', 'deepseek-chat'));
 
    // Max tokens
    $max_tokens = intval(esc_attr(get_option('kognetiks_ai_summaries_deepseek_max_tokens_setting', '1024')));

    // Conversation Context - Ver 1.6.1
    $context = esc_attr(get_option('kognetiks_ai_summaries_deepseek_conversation_context', 'You are a versatile, friendly, and helpful assistant designed to support me in a variety of tasks that responds in Markdown.'));

    // Temperature - Ver 2.1.8
    $temperature = floatval(esc_attr(get_option('kognetiks_ai_summaries_deepseek_temperature', '0.5')));

    // Top P - Ver 2.1.8
    $top_p = floatval(esc_attr(get_option('kognetiks_ai_summaries_deepseek_top_p', '1.0')));
 
    // Added Role, System, Content Static Variable - Ver 1.6.0
    $body = json_encode(array(
        'model' => $model,
        'max_tokens' => $max_tokens,
        'messages' => array(
            array(
                'role' => 'system',
                'content' => $context, // System input
            ),
            array(
                'role' => 'user',
                'content' => $message, // User input
            ),
        ),
        'stream' => false,
    ));

    $timeout = intval(esc_attr(get_option('kognetiks_ai_summaries_deepseek_timeout_setting', 240)));

    // DIAG - Diagnostics - Ver 2.2.2
    // back_trace( 'NOTICE', '$body: ' . print_r($body, true));

    // Convert the body array to JSON
    $body_json = wp_json_encode($body);

    // DIAG Diagnostics - Ver 1.0.1
    // back_trace( 'NOTICE', '$context: ' . $context);
    // back_trace( 'NOTICE', '$message: ' . $message);  

    // API Call
    $response = wp_remote_post($api_url, array(
        'headers' => $headers,
        'body' => $body,
        'timeout' => $timeout,
    ));

    // Handle WP Error
    if (is_wp_error($response)) {
    
        // DIAG - Diagnostics
        prod_trace('ERROR', 'Error: ' . $response->get_error_message());
        return 'An API error occurred.';
    
    }
    
    // Retrieve and Decode Response
    $response_body = json_decode(wp_remote_retrieve_body($response));

    // DIAG - Diagnostics
    // back_trace( 'NOTICE', '$response_body: ' . print_r($response_body, true));
    
    // Handle API Errors
    if (isset($response_body->error)) {
    
        // Extract error type and message safely
        $error_type = $response_body->error->type ?? 'Unknown Error Type';
        $error_message = $response_body->error->message ?? 'No additional information.';
    
        // DIAG - Diagnostics
        prod_trace('ERROR', 'Error: Type: ' . $error_type . ' Message: ' . $error_message);
        return isset($errorResponses['api_error']) ? $errorResponses['api_error'] : 'An error occurred.';
    
    }

    // DIAG - Diagnostics - Ver 1.8.1
    // back_trace( 'NOTICE', 'deepseek-api $response_body: ' . print_r($response_body, true));
    
    // Access response content properly
    if (isset($response_body->choices[0]->message->content) && !empty($response_body->choices[0]->message->content)) {
        $response_text = $response_body->choices[0]->message->content;
        return $response_text;
    } else {
        // Return a random error message
        // return $kognetiks_ai_summaries_error_responses[array_rand($kognetiks_ai_summaries_error_responses)];
        return 'ERROR';
    }
    
}
