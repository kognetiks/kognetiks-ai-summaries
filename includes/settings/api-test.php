<?php
/**
 * Kognetiks AI Summaries for WordPress - Settings - API/Model Test
 *
 * This file contains the code for the checking the API status.
 *
 *
 *
  * @package kognetiks-ai-summaries
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

// Test OpenAI API for any errors
 function ksum_test_openai_api_status() {

    $ksum_ai_platform_choice = esc_attr(get_option('ksum_ai_platform_choice', 'OpenAI'));

    $ksum_ai_platform_choice = esc_attr(get_option('ksum_ai_platform_choice'));

    switch ($ksum_ai_platform_choice) {

        case 'OpenAI':

            update_option('ksum_api_status', 'API Error Type: Status Unknown');
            $api_key = esc_attr(get_option('ksum_openai_api_key', 'NOT SET'));

            // Model and message for testing
            $model = esc_attr(get_option('ksum_openai_model_choice', 'chatgpt-4o-latest'));

            // Call the API to test the connection
            $updated_status = ksum_openai_api_call($api_key, 'Test message.');

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

            update_option('ksum_api_status', $updated_status);
            
            break;

        case 'NVIDIA':

            update_option('ksum_nvidia_api_status', 'API Error Type: Status Unknown');
            $api_key = esc_attr(get_option('ksum_nvidia_api_key', 'NOT SET'));

            // Model and message for testing
            $model = esc_attr(get_option('ksum_nvidia_model_choice', 'nvidia/llama-3.1-nemotron-51b-instruct'));

            // Call the API to test the connection
            $updated_status = ksum_nvidia_api_call($api_key, $model);

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

            update_option('ksum_api_status', $updated_status);

            break;

        case 'Anthropic':

            update_option('ksum_api_status', 'API Error Type: Status Unknown');

            break;

        default:

            $updated_status = 'API Error Type: Platform Choice Invalid';

            update_option('ksum_api_status', $updated_status);
            
            break;

    }

    return $updated_status;

}
