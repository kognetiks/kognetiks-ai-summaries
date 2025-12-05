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
    $base_url = esc_attr(get_option('kognetiks_ai_summaries_local_base_url', 'http://127.0.0.1:1337/v1'));

    // Set the API URL
    $api_url = rtrim($base_url, '/') . '/chat/completions';

    // Start the model
    kognetiks_ai_summaries_local_start_model();

    $headers = array(
        'Authorization' => 'Bearer ' . $api_key,
        'Content-Type'  => 'application/json',
    );

    // Retrieve model settings
    $model = esc_attr(get_option('kognetiks_ai_summaries_local_model_choice', 'llama3.2-3b-instruct'));
    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', '$model: ' . $model);
    $max_tokens = intval(get_option('kognetiks_ai_summaries_local_max_tokens_setting', 10000));
    $temperature = floatval(get_option('ckognetiks_ai_summaries_local_temperature', 0.8));
    $top_p = floatval(get_option('kognetiks_ai_summaries_local_top_p', 0.95));
    $context = esc_attr(get_option('kognetiks_ai_summaries_local_conversation_context', 'You are a versatile, friendly, and helpful assistant designed to support me in a variety of tasks that responds in Markdown.'));
    $timeout = intval(get_option('kognetiks_ai_summaries_local_timeout_setting', 360));
    
    // Construct request body to match the expected schema
    $body = array(
        'model' => $model,
        'stream' => null,
        'max_tokens' => $max_tokens,
        'stop' => array("End"),
        'frequency_penalty' => 0.2,
        'presence_penalty' => 0.6,
        'temperature' => $temperature,
        'top_p' => $top_p,
        'modalities' => array("text"),
        'audio' => array(
            'voice' => 'default',
            'format' => 'mp3'
        ),
        'store' => null,
        'metadata' => array(
            'type' => 'conversation'
        ),
        'logit_bias' => array(
            "15496" => -100,
            "51561" => -100
        ),
        'logprobs' => null,
        'n' => 1,
        'response_format' => array('type' => 'text'),
        'seed' => 123,
        'stream_options' => null,
        // 'tools' => array(
        //     array(
        //         'type' => 'function',
        //         'function' => array(
        //             'name' => '',
        //             'parameters' => array(),
        //             'strict' => null
        //         )
        //     )
        // ),
        'tools' => null,
        'parallel_tool_calls' => null,
        'messages' => array(
            array('role' => 'system', 'content' => $context),
            array('role' => 'user', 'content' => $message)
        )
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

    if (isset($response_body['choices'][0]['message']['content']) && !empty($response_body['choices'][0]['message']['content'])) {
        $response_text = $response_body['choices'][0]['message']['content'];
        // DiAG - Diagnostics - Ver 1.0.2
        // kognetiks_ai_summaries_back_trace( 'NOTICE', '$response_text: ' . $response_text);
        return $response_text;
    } else {
        prod_trace('WARNING', 'No valid response text found in API response.');
        return 'No valid response text found in API response.';
    }

}

// Start the chat completions model
function kognetiks_ai_summaries_local_start_model() {

    // DiAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'chatbot_local_start_model');

    global $kognetiks_ai_summaries_local_model_status;

    // Get the model choice
    $model = esc_attr(get_option('chatbot_local_model_choice', 'llama3.2-3b-instruct'));

    // DiAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', '$model: ' . $model);

    // Set the API URL
    $api_url = esc_attr(get_option('chatbot_local_base_url','http://127.0.0.1:1337/v1')) . '/models/start';

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
        prod_trace( 'ERROR', $error_message);
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