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
function kognetiks_ai_summaries_test_api_status() {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_test_api_status');

    $kognetiks_ai_summaries_ai_platform_choice = esc_attr(get_option('kognetiks_ai_summaries_ai_platform_choice', 'OpenAI'));

    $test_message = 'Write a one sentence response to this test message.';

    $updated_status = '';

    switch ($kognetiks_ai_summaries_ai_platform_choice) {

        case 'OpenAI':

            update_option('kognetiks_ai_summaries_api_status', 'API Error Type: Status Unknown');
            $api_key = esc_attr(get_option('kognetiks_ai_summaries_openai_api_key', 'NOT SET'));

            // Model and message for testing
            $model = esc_attr(get_option('kognetiks_ai_summaries_openai_model_choice', 'chatgpt-4o-latest'));

            // Call the API to test the connection
            $updated_status = kognetiks_ai_summaries_openai_api_call($api_key, $test_message);

            // DIAG - Diagnostics
            // kognetiks_ai_summaries_back_trace( 'NOTICE', 'API Response: ' . print_r($updated_status, true));

            // Check for API-specific errors
            // if $updated_status starts with "Error" then it is an error
            if (strpos($updated_status, 'An error occurred.') === 0) {

                $updated_status = 'Error: Unexpected response format from the ' . $kognetiks_ai_summaries_ai_platform_choice . ' API. Please check Settings for a valid API key or your ' . $kognetiks_ai_summaries_ai_platform_choice . ' account for additional information.';

            } else {

                $updated_status = 'Success: Connection to the ' . $kognetiks_ai_summaries_ai_platform_choice . ' API was successful!';

                // DIAG - Diagnostics
                // kognetiks_ai_summaries_back_trace( 'SUCCESS', 'API Status: ' . $updated_status);
            }

            update_option('kognetiks_ai_summaries_api_status', $updated_status);
            
            break;

        case 'NVIDIA':

            update_option('kognetiks_ai_summaries_api_status', 'API Error Type: Status Unknown');
            $api_key = esc_attr(get_option('kognetiks_ai_summaries_nvidia_api_key', 'NOT SET'));

            // Model and message for testing
            $model = esc_attr(get_option('kognetiks_ai_summaries_nvidia_model_choice', 'nvidia/llama-3.1-nemotron-51b-instruct'));

            // Call the API to test the connection
            $updated_status = kognetiks_ai_summaries_nvidia_api_call($api_key, $test_message);

            // DIAG - Diagnostics
            // kognetiks_ai_summaries_back_trace( 'NOTICE', 'API Response: ' . print_r($updated_status, true));

            // Check for API-specific errors
            // if $updated_status starts with "Error" then it is an error
            if (strpos($updated_status, 'An error occurred.') === 0) {

                $updated_status = 'Error: Unexpected response format from the ' . $kognetiks_ai_summaries_ai_platform_choice . ' API. Please check Settings for a valid API key or your ' . $kognetiks_ai_summaries_ai_platform_choice . ' account for additional information.';

            } else {

                $updated_status = 'Success: Connection to the ' . $kognetiks_ai_summaries_ai_platform_choice . ' API was successful!';

                // DIAG - Diagnostics
                // kognetiks_ai_summaries_back_trace( 'SUCCESS', 'API Status: ' . $updated_status);

            }

            update_option('kognetiks_ai_summaries_api_status', $updated_status);

            break;

        case 'Anthropic':

            update_option('kognetiks_ai_summaries_api_status', 'API Error Type: Status Unknown');
            $api_key = esc_attr(get_option('kognetiks_ai_summaries_anthropic_api_key', 'NOT SET'));
            
            // Model and message for testing
            $model = esc_attr(get_option('kognetiks_ai_summaries_anthropic_model_choice', 'claude-3-5-sonnet-latest'));
            
            // Call the API to test the connection
            $updated_status = kognetiks_ai_summaries_anthropic_api_call($api_key, $test_message);

            // DIAG - Diagnostics
            // kognetiks_ai_summaries_back_trace( 'NOTICE', 'API Response: ' . print_r($updated_status, true));

            // Check for API-specific errors
            // if $updated_status start  with "Error" then it is an error
            if (strpos($updated_status, 'An error occurred.') === 0) {

                $updated_status = 'Error: Unexpected response format from the ' . $kognetiks_ai_summaries_ai_platform_choice . ' API. Please check Settings for a valid API key or your ' . $kognetiks_ai_summaries_ai_platform_choice . ' account for additional information.';

            } else {

                $updated_status = 'Success: Connection to the ' . $kognetiks_ai_summaries_ai_platform_choice . ' API was successful!';

                // DIAG - Diagnostics
                // kognetiks_ai_summaries_back_trace( 'SUCCESS', 'API Status: ' . $updated_status);
                
            }
            
            update_option('kognetiks_ai_summaries_api_status', $updated_status);

            break;

        case 'DeepSeek':

            update_option('kognetiks_ai_summaries_api_status', 'API Error Type: Status Unknown');
            $api_key = esc_attr(get_option('kognetiks_ai_summaries_deepseek_api_key', 'NOT SET'));

            // Model and message for testing
            $model = esc_attr(get_option('kognetiks_ai_summaries_deepseek_model_choice', 'deepseek-chat'));

            // Call the API to test the connection
            $updated_status = kognetiks_ai_summaries_deepseek_api_call($api_key, $test_message);

            // DIAG - Diagnostics
            // kognetiks_ai_summaries_back_trace( 'NOTICE', 'API Response: ' . print_r($updated_status, true));

            // Check for API-specific errors
            // if $updated_status start  with "Error" then it is an error

            if (strpos($updated_status, 'An error occurred.') === 0) {

                $updated_status = 'Error: Unexpected response format from the ' . $kognetiks_ai_summaries_ai_platform_choice . ' API. Please check Settings for a valid API key or your ' . $kognetiks_ai_summaries_ai_platform_choice . ' account for additional information.';

            } else {

                $updated_status = 'Success: Connection to the ' . $kognetiks_ai_summaries_ai_platform_choice . ' API was successful!';

                // DIAG - Diagnostics
                // kognetiks_ai_summaries_back_trace( 'SUCCESS', 'API Status: ' . $updated_status);

            }

            update_option('kognetiks_ai_summaries_api_status', $updated_status);

            break;

        default:

            $updated_status = 'API Error Type: Platform Choice Invalid';

            update_option('kognetiks_ai_summaries_api_status', $updated_status);
            
            break;

    }

    return $updated_status;

}
