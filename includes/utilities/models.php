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

        default:

            // DIAG - Diagnostics
            kognetiks_ai_summaries_prod_trace( 'ERROR', 'Missing AI platform choice' );
            break;
            
    }

}