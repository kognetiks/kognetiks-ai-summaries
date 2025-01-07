<?php
/**
 * Kognetiks AI Summaries - OpenAI API - Ver 1.0.0
 *
 * This file contains the code for the OpenAI API calls.
 *
 * 
 * @package kognetiks-ai-summaries
 */

 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

// Call the OpenAI API without trappings
function kognetiks_ai_summaries_openai_api_call($api_key, $message) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_openai_api_call' );

    global $kognetiks_ai_summaries_error_responses;

    // The current ChatGPT API URL endpoint for chatgpt-4o-latest
    // $api_url = 'https://api.openai.com/v1/chat/completions';
    $api_url = kognetiks_ai_summaries_get_chat_completions_api_url();

    $headers = array(
        'Authorization' => 'Bearer ' . $api_key,
        'Content-Type' => 'application/json',
    );

    // Select the OpenAI Model
    // Get the saved model from the settings or default to "chatgpt-4o-latest"
    $model = esc_attr(get_option('kognetiks_ai_summaries_openai_model_choice', 'chatgpt-4o-latest'));
 
    // Max tokens - Ver 1.4.2
    $max_tokens = intval(esc_attr(get_option('kognetiks_ai_summaries_openai_max_tokens_setting', '500')));

    // Conversation Context - Ver 1.6.1
    $context = esc_attr(get_option('kognetiks_ai_summaries_openai_conversation_context', 'You are a versatile, friendly, and helpful assistant designed to support me in a variety of tasks that responds in Markdown.'));

    // Temperature - Ver 2.1.8
    $temperature = floatval(esc_attr(get_option('kognetiks_ai_summaries_openai_temperature', '0.5')));

    // Top P - Ver 2.1.8
    $top_p = floatval(esc_attr(get_option('kognetiks_ai_summaries_openai_top_p', '1.0')));
 
    // Added Role, System, Content Static Variable
    $body = array(
        'model' => $model,
        'max_tokens' => $max_tokens,
        'temperature' => $temperature,
        'top_p' => $top_p,
        'messages' => array(
            array('role' => 'system', 'content' => $context),
            array('role' => 'user', 'content' => $message)
            ),
    );

    $timeout = intval(esc_attr(get_option('kognetiks_ai_summaries_openai_timeout_setting', '240')));

    $args = array(
        'headers' => $headers,
        'body' => wp_json_encode($body),
        'method' => 'POST',
        'data_format' => 'body',
        'timeout' => $timeout, // Increase the timeout values to 15 seconds to wait just a bit longer for a response from the engine
    );

    $response = wp_remote_post($api_url, $args);
    // DIAG - Diagnostics - Ver
    // kognetiks_ai_summaries_back_trace( 'NOTICE', '$response: ' . print_r($response, true));

    // Handle any errors that are returned from the chat engine
    if (is_wp_error($response)) {
        return 'Error: ' . $response->get_error_message().' Please check Settings for a valid API key or your OpenAI account for additional information.';
    }

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', print_r($response, true));

    // Return json_decode(wp_remote_retrieve_body($response), true);
    $response_body = json_decode(wp_remote_retrieve_body($response), true);
    if (isset($response_body['message'])) {
        $response_body['message'] = trim($response_body['message']);
        if (!str_ends_with($response_body['message'], '.')) {
            $response_body['message'] .= '.';
        }
    }

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', '$response_body: ' . print_r($response_body));
    
    if (!empty($response_body['choices'])) {
        // Handle the response from the chat engine
        return $response_body['choices'][0]['message']['content'];
    } else {
        // Return a random error message
        // return $kognetiks_ai_summaries_error_responses[array_rand($kognetiks_ai_summaries_error_responses)];
        return 'ERROR';
    }

}
