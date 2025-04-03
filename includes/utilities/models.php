<?php
/**
 * Kognetiks AI Summaries - AI Models
 *
 * This file contains the code to retrieve the list of available models
 * from OpenAI API and display them in the settings page.
 *
 * @package kognetiks-ai-summaries
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

// Function to get the Model names from OpenAI API
function kognetiks_ai_summaries_openai_get_models() {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_openai_get_models');

    $api_key = esc_attr(get_option('kognetiks_ai_summaries_openai_api_key'));

    // Default model list
    $default_model_list = array(
        array(
            'id' => 'chatgpt-4o-latest',
            'object' => 'model',
            'created' => '1723515131',
            'owned_by' => 'system',
        ),
    );

    // Check if the API key is empty
    if (empty($api_key)) {

        return $default_model_list;

    }

    // Set the API URL
    $openai_models_url = esc_attr(get_option('kognetiks_ai_summaries_openai_base_url', kognetiks_ai_summaries_get_api_base_url()));
    $openai_models_url = rtrim($openai_models_url, '/') . '/models';

    // Set headers
    $args = array(
        'headers' => array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
        ),
    );

    // Perform the request
    $response = wp_remote_get($openai_models_url, $args);

    // Check for errors in the response
    if (is_wp_error($response)) {

        return $default_model_list;

    }

    // Decode the JSON response
    $data = json_decode(wp_remote_retrieve_body($response), true);

    // Check for API errors
    if (isset($data['error'])) {

        return $default_model_list;

    }

    // Extract and return the models
    $models = $data['data'] ?? [];
    if (!is_array($models)) {

        return $default_model_list;

    }

    // Sort the models by name
    usort($models, function ($a, $b) {

        return $a['id'] <=> $b['id'];

    });

    return $models;

}

// Function to get the Model names from NVIDIA API
function kognetiks_ai_summaries_nvidia_get_models() {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_nvidia_get_models');

    $api_key = esc_attr(get_option('kognetiks_ai_summaries_nvidia_api_key'));

    // Default model list
    $default_model_list = array(
        array(
            'id' => 'nvidia/llama-3.1-nemotron-51b-instruct',
            'object' => 'model',
            'created' => 735790403,
            'owned_by' => 'nvidia',
        ),
    );

    // Check if the API key is empty
    if (empty($api_key)) {

        return $default_model_list;

    }

    // Set the API URL
    $nvidia_models_url = esc_attr(get_option('kognetiks_ai_summaries_nvidia_base_url', kognetiks_ai_summaries_get_api_base_url()));
    $nvidia_models_url = rtrim($nvidia_models_url, '/') . '/models';

    // Set headers
    $args = array(
        'headers' => array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
        ),
    );

    // Perform the request
    $response = wp_remote_get($nvidia_models_url, $args);

    // Check for errors in the response
    if (is_wp_error($response)) {

        return $default_model_list;

    }

    // Decode the JSON response
    $data = json_decode(wp_remote_retrieve_body($response), true);

    // Check for API errors
    if (isset($data['error'])) {

        return $default_model_list;

    }

    // Extract and return the models
    $models = $data['data'] ?? [];
    if (!is_array($models)) {

        return $default_model_list;

    }

    // Sort the models by name
    usort($models, function ($a, $b) {

        return $a['id'] <=> $b['id'];

    });

    return $models;

}

// Function to get the Model names from Anthropic API
function kognetiks_ai_summaries_anthropic_get_models() {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_anthropic_get_models');

    // Default model list
    $default_model_list = array(
        array(
            'id' => 'claude-3-5-sonnet-latest',
            'object' => 'model',
            'created' => 20241022,
            'owned_by' => 'anthropic'
        ),
        array(
            'id' => 'claude-3-5-haiku-latest',
            'object' => 'model',
            'created' => 20241022,
            'owned_by' => 'anthropic'
        ),
        array(
            'id' => 'claude-3-opus-latest',
            'object' => 'model',
            'created' => 20240229,
            'owned_by' => 'anthropic'
        ),
        // array(
        //     'id' => 'claude-3-sonnet-20240229',
        //     'object' => 'model',
        //     'created' => 20240229,
        //     'owned_by' => 'anthropic'
        // ),
        // array(
        //     'id' => 'claude-3-haiku-20240307',
        //     'object' => 'model',
        //     'created' => 20240307,
        //     'owned_by' => 'anthropic'
        // ),
    );

    // Anthropic API does not have an endpoint for models
    return $default_model_list;

}

// Function to get the Model names from DeepSeek API
function kognetiks_ai_summaries_deepseek_get_models() {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_deepseek_get_models');

    $api_key = esc_attr(get_option('kognetiks_ai_summaries_deepseek_api_key'));

    // Default model list
    $default_model_list = array(
        array(
            'id' => 'deepseek-chat',
            'object' => 'model',
            'created' => null,
            'owned_by' => 'deepseek'
        ),
    );
        
    // Check if the API key is empty
    if (empty($api_key)) {

        return $default_model_list;

    }

    // Set the API URL
    $deepseek_models_url = esc_attr(get_option('kognetiks_ai_summaries_deepseek_base_url', kognetiks_ai_summaries_get_api_base_url()));
    $deepseek_models_url = rtrim($deepseek_models_url, '/') . '/models';

    // Set headers
    $args = array(
        'headers' => array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
        ),
    );

    // Perform the request
    $response = wp_remote_get($deepseek_models_url, $args);

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace('NOTICE', '$response: ' . print_r($response, true));
    
    // Check for errors in the response
    if (is_wp_error($response)) {

        return $default_model_list;

    }

    // Decode the JSON response
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    // Check if the response is valid and contains data
    if (isset($data['data']) && is_array($data['data'])) {
        $default_model_list = array_map(function($model) {
            return array(
                'id' => $model['id'],
                'object' => $model['object'],
                'created' => null, // Assuming 'created' is not provided in the response
                'owned_by' => $model['owned_by']
            );
        }, $data['data']);
    } else {
        // Handle the case where the response is not valid
        $default_model_list = array(
            array(
                'id' => 'deepseek-chat',
                'object' => 'model',
                'created' => null,
                'owned_by' => 'deepseek'
            ),
        );
    }

    // DeepSeek API does not have an endpoint for models
    return $default_model_list;

}

// Fetch the local models
function kognetiks_ai_summaries_local_get_models() {
    
    // DiAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'chatbot_local_get_models');

    // Set the API URL
    $api_url = esc_attr(get_option('chatbot_local_base_url','http://127.0.0.1:1337/v1')) . '/models';

    // Send the request
    $response = wp_remote_get($api_url, array(
        'headers' => array(
            'Content-Type' => 'application/json',
        ),
    ));

    // Check for errors
    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        // Log the error
        prod_trace( 'ERROR', $error_message);
        // Return a default model in teh $models array
        $models = array('llama3.2-3b-instruct');
        return $models;
    }

    // Get the response body
    $response_body = json_decode(wp_remote_retrieve_body($response), true);

    // DiAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', '$response_body: ' . print_r($response_body, true));

    // For each model in the $response_body, add the model to return array
    $models = array();
    foreach ($response_body['data'] as $model) {
        if (isset($model['status']) && $model['status'] == 'downloaded') {
            $models[] = $model['id'];
        }
    }

    return $models;
    
}

// Base URL for API Calls
function kognetiks_ai_summaries_get_api_base_url() {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_get_api_base_url' );

    $kognetiks_ai_summaries_ai_platform_choice = esc_attr(get_option('kognetiks_ai_summaries_ai_platform_choice', 'OpenAI'));

    switch ($kognetiks_ai_summaries_ai_platform_choice) {

        case 'OpenAI':
            return esc_attr(get_option('kognetiks_ai_summaries_openai_base_url', 'https://api.openai.com/v1'));
            break;

        case 'NVIDIA':
            return esc_attr(get_option('kognetiks_ai_summaries_nvidia_base_url', 'https://integrate.api.nvidia.com/v1'));
            break;

        case 'Anthropic':
            return esc_attr(get_option('kognetiks_ai_summaries_anthropic_base_url', 'https://api.anthropic.com/v1'));
            break;

        case 'DeepSeek':
            return esc_attr(get_option('kognetiks_ai_summaries_deepseek_base_url', 'https://api.deepseek.com'));
            break;

        case 'Local':
            return esc_attr(get_option('kognetiks_ai_summaries_local_base_url', 'http://127.0.0.1:1337/v1'));
            break;

        default:
            kognetiks_ai_summaries_prod_trace( 'ERROR', 'Missing AI platform choice' );
            break;
            
    }

}

// Function to get the URL for the completions API
function kognetiks_ai_summaries_get_chat_completions_api_url() {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_get_chat_completions_api_url' );

    $kognetiks_ai_summaries_ai_platform_choice = esc_attr(get_option('kognetiks_ai_summaries_ai_platform_choice', 'OpenAI'));

    switch ($kognetiks_ai_summaries_ai_platform_choice) {

        case 'OpenAI':

            // DIAG - Diagnostics
            // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_get_chat_completions_api_url: OpenAI API' );
            return kognetiks_ai_summaries_get_api_base_url() . "/chat/completions";
            break;

        case 'NVIDIA':

            // DIAG - Diagnostics
            // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_get_chat_completions_api_url: NVIDIA API' );
            return kognetiks_ai_summaries_get_api_base_url() . "/chat/completions";
            break;

        case 'Anthropic':

            // DIAG - Diagnostics
            // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_get_chat_completions_api_url: Anthropic API' );
            return kognetiks_ai_summaries_get_api_base_url() . "/messages";
            break;

        case 'DeepSeek':

            // DIAG - Diagnostics
            // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_get_chat_completions_api_url: DeepSeek API' );
            return kognetiks_ai_summaries_get_api_base_url() . "/chat/completions";
            break;

        case 'Local':

            // DIAG - Diagnostics
            // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_get_chat_completions_api_url: Local API' );
            return kognetiks_ai_summaries_get_api_base_url() . "/chat/completions";
            break;

        default:

            // DIAG - Diagnostics
            kognetiks_ai_summaries_prod_trace( 'ERROR', 'Missing AI platform choice' );
            break;
            
    }

}
