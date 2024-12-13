<?php
/**
 * Kognetiks AI Summaries for WordPress - AI Models
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
function ksum_openai_get_models() {
    
    $api_key = '';

    // Retrieve the API key
    $api_key = esc_attr(get_option('ksum_openai_api_key'));

    // Default model list
    $default_model_list = '';
    $default_model_list = array(
        array(
            'id' => 'chatgpt-4o-latest',
            'object' => 'model',
            'created' => '1723515131',
            'owned_by' => 'system',
        ),

    );

    // See if the option exists, if not then create it and set the default
    if (get_option('ksum_openai_model_choice') === false) {
        update_option('ksum_openai_model_choice', 'chatgpt-4o-latest');
    }

    // Check if the API key is empty
    if (empty($api_key)) {
        return $default_model_list;
    }

    // Initialize cURL session
    $ch = curl_init();

    // https://api.openai.com/v1
    $openai_models_url = esc_attr(get_option('ksum_openai_base_url','https://api.openai.com/v1'));
    $openai_models_url = rtrim($openai_models_url, '/') . '/models';

    // Set the URL
    curl_setopt($ch, CURLOPT_URL, "$openai_models_url");

    // Include the headers
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json",
        "Authorization: Bearer " . $api_key
    ));
    // Return the response as a string instead of directly outputting it
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // Execute the request and decode the JSON response into an associative array
    $response = curl_exec($ch);
    curl_close($ch);

    // Decode the JSON response
    $data = json_decode($response, true);

    // Check for API errors
    if (isset($data['error'])) {
        // return "Error: " . $data['error']['message'];
        // On 1st install needs an API key
        // So return a short list of the base models until an API key is entered
        return $default_model_list;
    }

    // Extract the models from the response
    if (isset($data['data']) && !is_null($data['data'])) {
        $models = $data['data'];
    } else {
        // Handle the case where 'data' is not set or is null
        $models = []; // Empty array
        ksum_prod_trace( 'WARNING', 'Data key is not set or is null in the \$data array.');
    }

    // Ensure $models is an array
    if (!is_array($models)) {
        return $default_model_list;
    } else {
        // Sort the models by name
        usort($models, function($a, $b) {
            return $a['id'] <=> $b['id'];
        });
    }

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE' , '$models: ' . print_r($models, true));

    // Return the list of models
    return $models;

}

// Function to get the Model names from NVIDIA API
function ksum_nvidia_get_models() {
    
    $api_key = '';

    // Retrieve the API key
    $api_key = esc_attr(get_option('ksum_nvidia_api_key'));

    // Default model list
    $default_model_list = '';
    $default_model_list = array(
        array(
            'id' => 'nvidia/llama-3.1-nemotron-51b-instruct',
            'object' => 'model',
            'created' => 735790403,
            'owned_by' => 'nvidia'
        ),
    );

    // See if the option exists, if not then create it and set the default
    if (get_option('ksum_nvidia_model_choice') === false) {
        update_option('ksum_nvidia_model_choice', 'nvidia/llama-3.1-nemotron-51b-instruct');
    }

    // Check if the API key is empty
    if (empty($api_key)) {
        return $default_model_list;
    }

    // Initialize cURL session
    $ch = curl_init();

    // https://integrate.api.nvidia.com/v1
    $nvidia_models_url = esc_attr(get_option('ksum_nvidia_base_url','https://integrate.api.nvidia.com/v1'));
    $nvidia_models_url = rtrim($nvidia_models_url, '/') . '/models';

    // Set the URL
    curl_setopt($ch, CURLOPT_URL, "$nvidia_models_url");
    // Include the headers
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json",
        "Authorization: Bearer " . $api_key
    ));
    // Return the response as a string instead of directly outputting it
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // Execute the request and decode the JSON response into an associative array
    $response = curl_exec($ch);
    curl_close($ch);

    // Decode the JSON response
    $data = json_decode($response, true);

    // Check for API errors
    if (isset($data['error'])) {
        // return "Error: " . $data['error']['message'];
        // On 1st install needs an API key
        // So return a short list of the base models until an API key is entered
        return $default_model_list;
    }

    // Extract the models from the response
    if (isset($data['data']) && !is_null($data['data'])) {
        $models = $data['data'];
    } else {
        // Handle the case where 'data' is not set or is null
        $models = []; // Empty array
        ksum_prod_trace( 'WARNING', 'Data key is not set or is null in the \$data array.');
    }

    // Ensure $models is an array
    if (!is_array($models)) {
        return $default_model_list;
    } else {
        // Sort the models by name
        usort($models, function($a, $b) {
            return $a['id'] <=> $b['id'];
        });
    }

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE' , '$models: ' . print_r($models, true));

    // Return the list of models
    return $models;

}

// Function to get the Model names from Anthropic API
function ksum_anthropic_get_models() {

    // https://docs.anthropic.com/en/api/messages-examples
    // https://docs.anthropic.com/en/docs/models-overview
    // https://docs.anthropic.com/en/docs/about-claude/models

    // Default model list
    $default_model_list = '';
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
        array(
            'id' => 'claude-3-sonnet-20240229',
            'object' => 'model',
            'created' => 20240229,
            'owned_by' => 'anthropic'
        ),
        array(
            'id' => 'claude-3-haiku-20240307',
            'object' => 'model',
            'created' => 20240307,
            'owned_by' => 'anthropic'
        )
    );

    // FIXME - Anthropic API does not have an endpoint for models
    // Call the API to get the models

    // Decode the JSON response
    // $data = json_decode($response, true);

    // FIXME - Force an error since there is no api endpoint for models
    $data = array('error' => array('message' => 'No models endpoint available'));

    // Check for API errors
    if (isset($data['error'])) {
        // return "Error: " . $data['error']['message'];
        // On 1st install needs an API key
        // So return a short list of the base models until an API key is entered
        return $default_model_list;
    }

    // Extract the models from the response
    if (isset($data['data']) && !is_null($data['data'])) {
        $models = $data['data'];
    } else {
        // Handle the case where 'data' is not set or is null
        $models = []; // Empty array
        ksum_prod_trace( 'WARNING', 'Data key is not set or is null in the \$data array.');
    }

    // Ensure $models is an array
    if (!is_array($models)) {
        return $default_model_list;
    } else {
        // Sort the models by name
        usort($models, function($a, $b) {
            return $a['id'] <=> $b['id'];
        });
    }

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE' , '$models: ' . print_r($models, true));

    // Return the list of models
    return $models;

}

// Base URL for API Calls
function ksum_get_api_base_url() {

    $ksum_ai_platform_choice = esc_attr(get_option('ksum_ai_platform_choice'));

    switch ($ksum_ai_platform_choice) {

        case 'OpenAI':
            return esc_attr(get_option('ksum_openai_base_url', 'https://api.openai.com/v1'));
            break;

        case 'NVIDIA':
            return esc_attr(get_option('ksum_nvidia_base_url', 'https://integrate.api.nvidia.com/v1'));
            break;

        case 'Anthropic':
            return esc_attr(get_option('ksum_anthropic_base_url', 'https://api.anthropic.com/v1'));
            break;

        default:
            ksum_prod_trace( 'ERROR', 'Missing AI platform choice' );
            break;
            
    }

}

// Function to get the URL for the completions API
function ksum_get_chat_completions_api_url() {

    $ksum_ai_platform_choice = esc_attr(get_option('ksum_ai_platform_choice'));

    switch ($ksum_ai_platform_choice) {

        case 'OpenAI':

            // DIAG - Diagnostics
            ksum_back_trace( 'NOTICE', 'ksum_get_chat_completions_api_url: OpenAI API' );
            return ksum_get_api_base_url() . "/chat/completions";
            break;

        case 'NVIDIA':

            // DIAG - Diagnostics
            ksum_back_trace( 'NOTICE', 'ksum_get_chat_completions_api_url: NVIDIA API' );
            return ksum_get_api_base_url() . "/chat/completions";
            break;

        case 'Anthropic':

            // DIAG - Diagnostics
            ksum_back_trace( 'NOTICE', 'ksum_get_chat_completions_api_url: Anthropic API' );
            return ksum_get_api_base_url() . "/messages";
            break;

        default:

            // DIAG - Diagnostics
            ksum_prod_trace( 'ERROR', 'Missing AI platform choice' );
            break;
            
    }

}