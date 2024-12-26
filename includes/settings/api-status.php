<?php
/**
 * Kognetiks AI Summaries - Settings - API/Model Status
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

// Test API for status and errors
function ksum_test_api_status() {

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', 'ksum_test_api_status');

    $ksum_ai_platform_choice = esc_attr(get_option('ksum_ai_platform_choice', 'OpenAI'));

    $test_message = 'Write a one sentence response to this test message.';

    $updated_status = '';

    switch ($ksum_ai_platform_choice) {

        case 'OpenAI':

            update_option('ksum_api_status', 'API Error Type: Status Unknown');
            $api_key = esc_attr(get_option('ksum_openai_api_key', 'NOT SET'));

            // Model and message for testing
            $model = esc_attr(get_option('ksum_openai_model_choice', 'chatgpt-4o-latest'));

            // Call the API to test the connection
            $updated_status = ksum_openai_api_call($api_key, $test_message);

            // DIAG - Diagnostics
            // ksum_back_trace('NOTICE', 'API Response: ' . print_r($updated_status, true));

            // Check for API-specific errors
            // if $updated_status starts with "Error" then it is an error
            if (strpos($updated_status, 'An error occurred.') === 0) {

                $updated_status = 'Error: Unexpected response format from the ' . $ksum_ai_platform_choice . ' API. Please check Settings for a valid API key or your ' . $ksum_ai_platform_choice . ' account for additional information.';

            } else {

                $updated_status = 'Success: Connection to the ' . $ksum_ai_platform_choice . ' API was successful!';

                // DIAG - Diagnostics
                // ksum_back_trace('SUCCESS', 'API Status: ' . $updated_status);
            }

            update_option('ksum_api_status', $updated_status);
            
            break;

        case 'NVIDIA':

            update_option('ksum_nvidia_api_status', 'API Error Type: Status Unknown');
            $api_key = esc_attr(get_option('ksum_nvidia_api_key', 'NOT SET'));

            // Model and message for testing
            $model = esc_attr(get_option('ksum_nvidia_model_choice', 'nvidia/llama-3.1-nemotron-51b-instruct'));

            // Call the API to test the connection
            $updated_status = ksum_nvidia_api_call($api_key, $test_message);

            // DIAG - Diagnostics
            // ksum_back_trace('NOTICE', 'API Response: ' . print_r($updated_status, true));

            // Check for API-specific errors
            // if $updated_status starts with "Error" then it is an error
            if (strpos($updated_status, 'An error occurred.') === 0) {

                $updated_status = 'Error: Unexpected response format from the ' . $ksum_ai_platform_choice . ' API. Please check Settings for a valid API key or your ' . $ksum_ai_platform_choice . ' account for additional information.';

            } else {

                $updated_status = 'Success: Connection to the ' . $ksum_ai_platform_choice . ' API was successful!';

                // DIAG - Diagnostics
                // ksum_back_trace('SUCCESS', 'API Status: ' . $updated_status);

            }

            update_option('ksum_api_status', $updated_status);

            break;

        case 'Anthropic':

            update_option('ksum_anthropic_api_status', 'API Error Type: Status Unknown');
            $api_key = esc_attr(get_option('ksum_anthropic_api_key', 'NOT SET'));
            
            // Model and message for testing
            $model = esc_attr(get_option('ksum_anthropic_model_choice', 'claude-3-5-sonnet-latest'));
            
            // Call the API to test the connection
            $updated_status = ksum_anthropic_api_call($api_key, $test_message);

            // DIAG - Diagnostics
            // ksum_back_trace('NOTICE', 'API Response: ' . print_r($updated_status, true));

            // Check for API-specific errors
            // if $updated_status start  with "Error" then it is an error
            if (strpos($updated_status, 'An error occurred.') === 0) {

                $updated_status = 'Error: Unexpected response format from the ' . $ksum_ai_platform_choice . ' API. Please check Settings for a valid API key or your ' . $ksum_ai_platform_choice . ' account for additional information.';

            } else {

                $updated_status = 'Success: Connection to the ' . $ksum_ai_platform_choice . ' API was successful!';

                // DIAG - Diagnostics
                // ksum_back_trace('SUCCESS', 'API Status: ' . $updated_status);
                
            }
            
            update_option('ksum_api_status', $updated_status);

            break;

        default:

            $updated_status = 'API Error Type: Platform Choice Invalid';

            update_option('ksum_api_status', $updated_status);
            
            break;

    }

    return $updated_status;

}
