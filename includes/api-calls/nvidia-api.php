<?php
/**
 * Kognetiks AI Summaries - NVIDIA API - Ver 1.0.0
 *
 * This file contains the code for the NVIDIA API calls.
 *
 * 
 * @package kognetiks-ai-summaries
 */

 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

// Call the NVIDIA API without trappings
function ksum_nvidia_api_call($api_key, $message) {

    // DIAG - Diagnostics
    // ksum_back_trace('NOTICE', 'ksum_nvidia_api_call');

    global $ksum_error_responses;

    // The current NVIDIA API URL endpoint for chat completions
    // $api_url = 'https://integrate.api.nvidia.com/v1';
    $api_url = ksum_get_chat_completions_api_url();

    $headers = array(
        'Authorization' => 'Bearer ' . $api_key,
        'Content-Type' => 'application/json',
    );

    // Select the NVIDIA Model
    // Get the saved model from the settings or default to "nvidia/llama-3.1-nemotron-51b-instruct"
    $model = esc_attr(get_option('chatbot_nvidia_model_choice', 'nvidia/llama-3.1-nemotron-51b-instruct'));
 
    // Max tokens
    $max_tokens = intval(esc_attr(get_option('chatbot_nvidia_max_tokens_setting', '500')));

    // Conversation Context - Ver 1.6.1
    $context = esc_attr(get_option('ksum_nvidia_conversation_context', 'You are a versatile, friendly, and helpful assistant designed to support me in a variety of tasks that responds in Markdown.'));

    // Temperature - Ver 2.1.8
    $temperature = floatval(esc_attr(get_option('ksum_nvidia_temperature', '0.5')));

    // Top P - Ver 2.1.8
    $top_p = floatval(esc_attr(get_option('ksum_nvidia_top_p', '1.0')));
 
    // Added Role, System, Content Static Variable - Ver 1.6.0
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

    $timeout = intval(esc_attr(get_option('ksum_nvidia_timeout_setting', '240')));

    $args = array(
        'headers' => $headers,
        'body' => wp_json_encode($body),
        'method' => 'POST',
        'data_format' => 'body',
        'timeout' => $timeout, // Increase the timeout values to 15 seconds to wait just a bit longer for a response from the engine
    );

    $response = wp_remote_post($api_url, $args);
    // DIAG - Diagnostics - Ver
    // ksum_back_trace( 'NOTICE', '$response: ' . print_r($response, true));

    // Handle any errors that are returned from the chat engine
    if (is_wp_error($response)) {
        return 'Error: ' . $response->get_error_message().' Please check Settings for a valid API key or your OpenAI account for additional information.';
    }

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', print_r($response, true));

    // Return json_decode(wp_remote_retrieve_body($response), true);
    $response_body = json_decode(wp_remote_retrieve_body($response), true);

    // if (isset($response_body['message'])) {
    //     $response_body['message'] = trim($response_body['message']);
    //     if (!str_ends_with($response_body['message'], '.')) {
    //         $response_body['message'] .= '.';
    //     }
    // }

    if (isset($response_body['choices'][0]['message']['content'])) {
        // Extract the assistant's message content
        $message_content = trim($response_body['choices'][0]['message']['content']);
        
        // Ensure the response ends with a period
        if (!str_ends_with($message_content, '.')) {
            $message_content .= '.';
        }
    }

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', '$response_body: ' . print_r($response_body, true));
    
    if (!empty($response_body['choices'])) {
        // Handle the response from the chat engine
        return $response_body['choices'][0]['message']['content'];
    } else {
        // Return a random error message
        // return $ksum_error_responses[array_rand($ksum_error_responses)];
        return 'ERROR';
    }
    
}