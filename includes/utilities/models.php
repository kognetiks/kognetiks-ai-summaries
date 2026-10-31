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
    // Decrypt the API key - Ver 2.2.6
    $api_key = kognetiks_ai_summaries_decrypt_api_key($api_key);

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
    // Decrypt the API key - Ver 2.2.6
    $api_key = kognetiks_ai_summaries_decrypt_api_key($api_key);

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

// Fallback when no Anthropic model has been saved. Sonnet is the balanced default.
function kognetiks_ai_summaries_anthropic_default_model() {

    return 'claude-sonnet-5-5';

}

// Shown until an API key can load the live list from GET /v1/models.
function kognetiks_ai_summaries_anthropic_fallback_models() {

    $ids = array(
        'claude-opus-5-5',
        'claude-sonnet-5-5',
        'claude-haiku-5-5',
        'claude-fable-5-1',
    );
    $models = array();

    foreach ( $ids as $id ) {
        $models[] = array(
            'id'       => $id,
            'object'   => 'model',
            'created'  => null,
            'owned_by' => 'anthropic',
        );
    }

    return $models;

}

// Model ids returned by the Anthropic Models API.
function kognetiks_ai_summaries_anthropic_model_id_is_valid( $model_id ) {

    return is_string( $model_id ) && preg_match( '/^[A-Za-z0-9._:-]+$/', $model_id );

}

// True after kognetiks_ai_summaries_anthropic_get_models() loaded ids from the Models API.
function kognetiks_ai_summaries_anthropic_models_are_live( $set = null ) {

    static $live = false;
    if ( null !== $set ) {
        $live = (bool) $set;
    }
    return $live;

}

// Standard base input price, USD per million tokens. Longer names match first.
function kognetiks_ai_summaries_anthropic_model_base_input_price( $model_id ) {

    if ( ! kognetiks_ai_summaries_anthropic_model_id_is_valid( $model_id ) ) {
        return null;
    }

    $id = strtolower( $model_id );
    $prices = array(
        'haiku-3-5'  => 0.80,
        'haiku-4-5'  => 1.00,
        'haiku-5-5'  => 0.10,
        'haiku-3'    => 0.25,
        'sonnet-4-5' => 3.00,
        'sonnet-4-6' => 3.00,
        'sonnet-5-5' => 2.00,
        'sonnet-5'   => 2.00,
        'opus-4-5'   => 5.00,
        'opus-4-6'   => 5.00,
        'opus-4-7'   => 5.00,
        'opus-4-8'   => 5.00,
        'opus-5-5'   => 4.00,
        'opus-5'     => 5.00,
        'fable-5-1'  => 10.00,
        'fable-5'    => 10.00,
    );

    uksort(
        $prices,
        function( $a, $b ) {
            return strlen( $b ) <=> strlen( $a );
        }
    );

    foreach ( $prices as $needle => $price ) {
        if ( false !== strpos( $id, $needle ) ) {
            return $price;
        }
    }

    return null;

}

// Cheapest model in a live list. Haiku is the cheap tier; the lowest base input price wins.
function kognetiks_ai_summaries_anthropic_cheapest_model_id( $models ) {

    if ( ! is_array( $models ) ) {
        return '';
    }

    $chosen = '';
    $best   = null;

    foreach ( array_values( $models ) as $index => $model ) {
        if ( ! is_array( $model ) || empty( $model['id'] ) || ! kognetiks_ai_summaries_anthropic_model_id_is_valid( $model['id'] ) ) {
            continue;
        }

        $id       = $model['id'];
        $is_haiku = false !== stripos( $id, 'haiku' );
        $price    = kognetiks_ai_summaries_anthropic_model_base_input_price( $id );

        if ( $is_haiku && null !== $price ) {
            $rank = array( 0, $price );
        } elseif ( $is_haiku ) {
            $rank = array( 1, -1 * $index );
        } elseif ( null !== $price ) {
            $rank = array( 2, $price );
        } else {
            $rank = array( 3, -1 * $index );
        }

        if ( null === $best || $rank < $best ) {
            $best   = $rank;
            $chosen = $id;
        }
    }

    return $chosen;

}

// Use the requested id when the live list includes it. Otherwise use the cheapest model.
function kognetiks_ai_summaries_anthropic_resolve_model( $model ) {

    if ( ! is_string( $model ) || '' === $model || ! kognetiks_ai_summaries_anthropic_model_id_is_valid( $model ) ) {
        $model = kognetiks_ai_summaries_anthropic_default_model();
    }

    $models = kognetiks_ai_summaries_anthropic_get_models();
    if ( ! kognetiks_ai_summaries_anthropic_models_are_live() || ! is_array( $models ) ) {
        return $model;
    }

    foreach ( $models as $entry ) {
        if ( is_array( $entry ) && isset( $entry['id'] ) && $entry['id'] === $model ) {
            return $model;
        }
    }

    $replacement = kognetiks_ai_summaries_anthropic_cheapest_model_id( $models );
    if ( kognetiks_ai_summaries_anthropic_model_id_is_valid( $replacement ) ) {
        return $replacement;
    }

    return $model;

}

// Function to get the Model names from Anthropic API
// https://platform.claude.com/docs/en/api/models/list
function kognetiks_ai_summaries_anthropic_get_models( $refresh = false ) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_anthropic_get_models');

    kognetiks_ai_summaries_anthropic_models_are_live( false );
    $fallback = kognetiks_ai_summaries_anthropic_fallback_models();

    $api_key = get_option( 'kognetiks_ai_summaries_anthropic_api_key', '' );
    if ( ! is_string( $api_key ) || '' === $api_key ) {
        return $fallback;
    }

    $api_key = kognetiks_ai_summaries_decrypt_api_key( esc_attr( $api_key ) );
    if ( ! is_string( $api_key ) || '' === $api_key ) {
        return $fallback;
    }

    $base_url = get_option( 'kognetiks_ai_summaries_anthropic_base_url', 'https://api.anthropic.com/v1' );
    if ( ! is_string( $base_url ) || '' === $base_url ) {
        $base_url = 'https://api.anthropic.com/v1';
    }
    $base_url = esc_url_raw( untrailingslashit( $base_url ) );
    if ( '' === $base_url || ! wp_http_validate_url( $base_url ) ) {
        $base_url = 'https://api.anthropic.com/v1';
    }

    $cache_key = 'kognetiks_ai_sum_anth_' . substr( hash( 'sha256', $api_key ), 0, 16 );
    if ( ! $refresh ) {
        $cached = get_transient( $cache_key );
        if ( is_array( $cached ) && ! empty( $cached ) ) {
            kognetiks_ai_summaries_anthropic_models_are_live( true );
            return $cached;
        }
    }

    $models   = array();
    $seen     = array();
    $after_id = '';
    $pages    = 0;

    do {
        $url = $base_url . '/models?limit=1000';
        if ( '' !== $after_id ) {
            $url .= '&after_id=' . rawurlencode( $after_id );
        }

        $response = wp_remote_get(
            $url,
            array(
                'headers' => array(
                    'x-api-key'         => $api_key,
                    'anthropic-version' => '2023-06-01',
                    'content-type'      => 'application/json',
                ),
                'timeout' => 15,
            )
        );

        if ( is_wp_error( $response ) ) {
            kognetiks_ai_summaries_prod_trace( 'ERROR', 'Error fetching Anthropic models: ' . $response->get_error_message() );
            if ( empty( $models ) ) {
                return $fallback;
            }
            set_transient( $cache_key, $models, 5 * MINUTE_IN_SECONDS );
            kognetiks_ai_summaries_anthropic_models_are_live( true );
            return $models;
        }

        $code = (int) wp_remote_retrieve_response_code( $response );
        $data = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( $code < 200 || $code >= 300 || ! is_array( $data ) || isset( $data['error'] ) || empty( $data['data'] ) || ! is_array( $data['data'] ) ) {
            $message = ( is_array( $data ) && isset( $data['error']['message'] ) ) ? $data['error']['message'] : 'HTTP ' . $code;
            kognetiks_ai_summaries_prod_trace( 'ERROR', 'Anthropic models request failed: ' . $message );
            if ( empty( $models ) ) {
                return $fallback;
            }
            set_transient( $cache_key, $models, 5 * MINUTE_IN_SECONDS );
            kognetiks_ai_summaries_anthropic_models_are_live( true );
            return $models;
        }

        foreach ( $data['data'] as $model ) {
            if ( ! is_array( $model ) || empty( $model['id'] ) || ! kognetiks_ai_summaries_anthropic_model_id_is_valid( $model['id'] ) ) {
                continue;
            }
            if ( isset( $seen[ $model['id'] ] ) ) {
                continue;
            }
            $seen[ $model['id'] ] = true;

            $created = null;
            if ( ! empty( $model['created_at'] ) && is_string( $model['created_at'] ) ) {
                $timestamp = strtotime( $model['created_at'] );
                if ( false !== $timestamp ) {
                    $created = $timestamp;
                }
            }

            // The Models API does not return owned_by. Set it here so callers do not filter the payload on that field.
            $models[] = array(
                'id'       => $model['id'],
                'object'   => 'model',
                'created'  => $created,
                'owned_by' => 'anthropic',
            );
        }

        $has_more = ! empty( $data['has_more'] );
        $next_id  = ( isset( $data['last_id'] ) && is_string( $data['last_id'] ) ) ? $data['last_id'] : '';
        if ( '' === $next_id || $next_id === $after_id || ! kognetiks_ai_summaries_anthropic_model_id_is_valid( $next_id ) ) {
            $has_more = false;
        }
        $after_id = $next_id;
        $pages++;
    } while ( $has_more && $pages < 10 );

    if ( empty( $models ) ) {
        return $fallback;
    }

    // The API already returns the most recently released model first.
    set_transient( $cache_key, $models, 5 * MINUTE_IN_SECONDS );
    kognetiks_ai_summaries_anthropic_models_are_live( true );
    return $models;

}

// Function to get the Model names from DeepSeek API
function kognetiks_ai_summaries_deepseek_get_models() {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_deepseek_get_models');

    $api_key = esc_attr(get_option('kognetiks_ai_summaries_deepseek_api_key'));
    // Decrypt the API key - Ver 2.2.6
    $api_key = kognetiks_ai_summaries_decrypt_api_key($api_key);

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

// Function to get the Model names from Mistral API
function kognetiks_ai_summaries_mistral_get_models() {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_mistral_get_models');

    // Default model list
    $default_model_list = array(
        array(
            'id' => 'mistral-small-2503',
            'object' => 'model',
            'created' => 1745356399,
            'owned_by' => 'mistralai',
            'capabilities' => array(
                'completion_chat' => 1,
                'completion_fim' => '',
                'function_calling' => 1,
                'fine_tuning' => '',
                'vision' => 1,
                'classification' => ''
            ),
            'name' => 'mistral-small-2503',
            'description' => 'Official mistral-small-2503 Mistral AI model',
            'max_context_length' => 131072,
            'aliases' => array('mistral-small-latest'),
            'deprecation' => '',
            'default_model_temperature' => 0.3,
            'type' => 'base'
        )
    );

    // Retrieve the API key
    $api_key = esc_attr(get_option('kognetiks_ai_summaries_mistral_api_key'));
    // Decrypt the API key - Ver 2.2.6
    $api_key = kognetiks_ai_summaries_decrypt_api_key($api_key);

    // Check if the API key is empty
    if (empty($api_key)) {

        return $default_model_list;

    }

    // Set the API URL
    $mistral_models_url = esc_attr(get_option('kognetiks_ai_summaries_mistral_base_url', kognetiks_ai_summaries_get_api_base_url()));
    $mistral_models_url = rtrim($mistral_models_url, '/') . '/models';

    // Set headers
    $args = array(
        'headers' => array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
        ),
    );

    // Perform the request
    $response = wp_remote_get($mistral_models_url, $args);

    // Check for errors in the response
    if (is_wp_error($response)) {

        return $default_model_list;

    }

    // Decode the JSON response
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', '$data: ' . print_r($data, true));

    // Check if the response is valid and contains data
    if (isset($data['data']) && is_array($data['data'])) {
        $default_model_list = array_map(function($model) {
            return array(
                'id' => $model['id'],
                'object' => $model['object'],
                'created' => null,
                'owned_by' => $model['owned_by']
            );
        }, $data['data']);
    } else {
        // Handle the case where the response is not valid
        $default_model_list = array(
            array(
                'id' => 'mistral-small-2503',
                'object' => 'model',
                'created' => 1745356399,
                'owned_by' => 'mistralai',
                'capabilities' => array(
                    'completion_chat' => 1,
                    'completion_fim' => '',
                    'function_calling' => 1,
                    'fine_tuning' => '',
                    'vision' => 1,
                    'classification' => ''
                ),
                'name' => 'mistral-small-2503',
                'description' => 'Official mistral-small-2503 Mistral AI model',
                'max_context_length' => 131072,
                'aliases' => array('mistral-small-latest'),
                'deprecation' => '',
                'default_model_temperature' => 0.3,
                'type' => 'base'
            ),
        );
    }

    // Return the default model list
    return $default_model_list;

}

// Function to get the Model names from Google Gemini API
function kognetiks_ai_summaries_google_gemini_get_models() {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_google_gemini_get_models');

    $api_key = esc_attr(get_option('kognetiks_ai_summaries_google_api_key'));
    // Decrypt the API key - Ver 2.2.6
    $api_key = kognetiks_ai_summaries_decrypt_api_key($api_key);

    // Default model list
    $default_model_list = array(
        array(
            'id' => 'gemini-2.5-flash',
            'object' => 'model',
            'created' => null,
            'owned_by' => 'google',
        ),
        array(
            'id' => 'gemini-2.5-pro',
            'object' => 'model',
            'created' => null,
            'owned_by' => 'google',
        ),
    );

    // Check if the API key is empty
    if (empty($api_key)) {
        return $default_model_list;
    }

    // Get the base URL from Google settings or default
    $google_base_url = esc_attr(get_option('kognetiks_ai_summaries_google_gemini_base_url', 'https://generativelanguage.googleapis.com/v1beta'));
    $google_base_url = rtrim($google_base_url, '/');
    
    // Remove /models if present to ensure we have just the base URL
    if (substr($google_base_url, -7) === '/models') {
        $google_base_url = substr($google_base_url, 0, -7);
    }

    // Google API endpoint for listing models
    $google_models_url = $google_base_url . '/models';
    
    // Add API key as query parameter
    $google_models_url = add_query_arg('key', $api_key, $google_models_url);

    // Set headers
    $args = array(
        'headers' => array(
            'Content-Type' => 'application/json',
        ),
    );

    // Perform the request
    $response = wp_remote_get($google_models_url, $args);

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
    // Google API returns models in a 'models' array
    if (isset($data['models']) && is_array($data['models'])) {
        $models = array();
        foreach ($data['models'] as $model) {
            // Only include models that support generateContent
            if (isset($model['supportedGenerationMethods']) && 
                in_array('generateContent', $model['supportedGenerationMethods'])) {
                // Extract model name - Google returns "models/gemini-2.5-flash" format
                $model_name = $model['name'] ?? $model['displayName'] ?? '';
                // Remove "models/" prefix if present to store just the model identifier
                if (strpos($model_name, 'models/') === 0) {
                    $model_name = substr($model_name, 7); // Remove "models/" prefix
                }
                $models[] = array(
                    'id' => $model_name,
                    'object' => 'model',
                    'created' => null,
                    'owned_by' => 'google',
                );
            }
        }
        
        // If we got valid models, return them; otherwise return default
        if (!empty($models)) {
            // Sort the models by name
            usort($models, function ($a, $b) {
                return $a['id'] <=> $b['id'];
            });
            return $models;
        }
    }

    // Return default model list if API call failed or returned no valid models
    return $default_model_list;

}

// Fetch model ids from the JAN.AI OpenAI-compatible /v1/models endpoint.
function kognetiks_ai_summaries_local_get_models() {

    $base = get_option( 'kognetiks_ai_summaries_local_base_url', 'http://127.0.0.1:1337/v1' );
    if ( ! is_string( $base ) || '' === $base ) {
        $base = 'http://127.0.0.1:1337/v1';
    }
    $base = esc_url_raw( untrailingslashit( $base ) );
    if ( '' === $base || ! wp_http_validate_url( $base ) ) {
        $base = 'http://127.0.0.1:1337/v1';
    }

    $headers = array(
        'Accept'       => 'application/json',
        'Content-Type' => 'application/json',
    );
    $api_key = get_option( 'kognetiks_ai_summaries_local_api_key', '' );
    if ( is_string( $api_key ) && '' !== $api_key && function_exists( 'kognetiks_ai_summaries_decrypt_api_key' ) ) {
        $api_key = kognetiks_ai_summaries_decrypt_api_key( $api_key );
    }
    if ( is_string( $api_key ) && '' !== $api_key ) {
        $headers['Authorization'] = 'Bearer ' . $api_key;
    }

    $response = wp_remote_get(
        $base . '/models',
        array(
            'headers' => $headers,
            'timeout' => 15,
        )
    );

    if ( is_wp_error( $response ) ) {
        kognetiks_ai_summaries_prod_trace( 'ERROR', 'Local model list request failed: ' . $response->get_error_message() );
        return array( 'llama3.2-3b-instruct' );
    }

    $code = (int) wp_remote_retrieve_response_code( $response );
    $data = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( $code < 200 || $code >= 300 || ! is_array( $data ) ) {
        kognetiks_ai_summaries_prod_trace( 'ERROR', 'Local model list request failed: HTTP ' . $code );
        return array( 'llama3.2-3b-instruct' );
    }

    $list = ( isset( $data['data'] ) && is_array( $data['data'] ) ) ? $data['data'] : $data;
    $models = array();
    if ( is_array( $list ) ) {
        foreach ( $list as $model ) {
            if ( is_string( $model ) && '' !== $model ) {
                $models[] = $model;
                continue;
            }
            if ( ! is_array( $model ) || empty( $model['id'] ) || ! is_string( $model['id'] ) ) {
                continue;
            }
            // Older JAN lists include a status. The current /v1/models list does not.
            if ( isset( $model['status'] ) && is_string( $model['status'] ) && 'downloaded' !== $model['status'] ) {
                continue;
            }
            $models[] = $model['id'];
        }
    }

    $models = array_values( array_unique( $models ) );
    sort( $models, SORT_STRING );
    if ( empty( $models ) ) {
        return array( 'llama3.2-3b-instruct' );
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

        case 'Mistral':
            return esc_attr(get_option('kognetiks_ai_summaries_mistral_base_url', 'https://api.mistral.ai/v1'));
            break;

        case 'Google':
            return esc_attr(get_option('kognetiks_ai_summaries_google_gemini_base_url', 'https://generativelanguage.googleapis.com/v1beta'));
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

        case 'Mistral':

            // DIAG - Diagnostics
            // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_get_chat_completions_api_url: Mistral API' );
            return kognetiks_ai_summaries_get_api_base_url() . "/chat/completions";
            break;

        case 'Google':

            // DIAG - Diagnostics
            // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_get_chat_completions_api_url: Google API' );
            // Note: Google uses a different endpoint format (/models/{model}:generateContent)
            // This function is not used by Google API calls, but included for consistency
            return kognetiks_ai_summaries_get_api_base_url() . "/models";
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
