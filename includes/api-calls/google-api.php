<?php
/**
 * Kognetiks AI Summaries - Google Gemini API - Ver 1.0.0
 *
 * This file contains the code for the Google Gemini API calls.
 *
 * 
 * @package kognetiks-ai-summaries
 */

 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

// Call the Google Gemini API without trappings
function kognetiks_ai_summaries_google_api_call($api_key, $message) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_google_api_call');

    global $kognetiks_ai_summaries_error_responses;

    // Get the base URL from Google settings or default
    $google_base_url = esc_attr(get_option('kognetiks_ai_summaries_google_gemini_base_url', 'https://generativelanguage.googleapis.com/v1beta'));
    $google_base_url = rtrim($google_base_url, '/');
    
    // Remove /models if present to ensure we have just the base URL
    if (substr($google_base_url, -7) === '/models') {
        $google_base_url = substr($google_base_url, 0, -7);
    }

    // Get the saved model from the settings or default to "gemini-2.5-flash"
    $model = esc_attr(get_option('kognetiks_ai_summaries_google_gemini_model_choice', 'gemini-2.5-flash'));
    
    // Ensure model name doesn't already include "models/" prefix
    // Google API expects just the model identifier (e.g., "gemini-2.5-flash")
    if (strpos($model, 'models/') === 0) {
        $model = substr($model, 7); // Remove "models/" prefix if present
    }

    // Google API endpoint format: {base}/models/{model}:generateContent
    $api_url = $google_base_url . '/models/' . $model . ':generateContent';
    
    // Add API key as query parameter
    $api_url = add_query_arg('key', $api_key, $api_url);

    // Max tokens
    $max_tokens = intval(esc_attr(get_option('kognetiks_ai_summaries_google_gemini_max_tokens', '500')));

    // Conversation Context
    $context = esc_attr(get_option('kognetiks_ai_summaries_google_gemini_conversation_context', 'You are a versatile, friendly, and helpful assistant designed to support me in a variety of tasks that responds in Markdown.'));

    // Temperature
    $temperature = floatval(esc_attr(get_option('kognetiks_ai_summaries_google_gemini_temperature', '0.5')));

    // Media Resolution - Ver 1.0.1
    // Note: Google's API doesn't have a direct "resolution" parameter.
    // The resolution is determined by the image data itself (base64 encoded).
    // This setting is stored for potential future use or documentation.
    $media_resolution = esc_attr(get_option('kognetiks_ai_summaries_google_gemini_media_resolution', 'Default'));

    // Thinking Level - Ver 1.0.1
    // Note: For thinking models (Gemini 2.0/3.0), this controls the depth of reasoning.
    // API support for thinking level may vary between versions.
    $thinking_level = esc_attr(get_option('kognetiks_ai_summaries_google_gemini_thinking_level', 'Low'));

    // Timeout
    $timeout = intval(esc_attr(get_option('kognetiks_ai_summaries_google_gemini_timeout_setting', '240')));

    // Define the headers
    $headers = array(
        'Content-Type' => 'application/json'
    );

    // Generation Configuration
    $generationConfig = array(
        'maxOutputTokens' => $max_tokens,
        'temperature'     => $temperature
    );

    // System Instruction
    $systemInstruction = null;
    if (!empty($context)) {
        $systemInstruction = array(
            'parts' => array(
                array('text' => $context)
            )
        );
    }

    // Build the Contents Array - simple user message for summaries
    $contents = array(
        array(
            'role' => 'user',
            'parts' => array(
                array('text' => $message)
            )
        )
    );

    // Assemble Final Body
    $body = array(
        'contents'         => $contents,
        'generationConfig' => $generationConfig,
        'safetySettings'   => array(
            // Default Gemini settings are strict. This prevents "FinishReason: SAFETY" blocks.
            array(
                'category' => 'HARM_CATEGORY_HARASSMENT',
                'threshold' => 'BLOCK_ONLY_HIGH'
            ),
            array(
                'category' => 'HARM_CATEGORY_HATE_SPEECH',
                'threshold' => 'BLOCK_ONLY_HIGH'
            ),
            array(
                'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                'threshold' => 'BLOCK_ONLY_HIGH'
            ),
            array(
                'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                'threshold' => 'BLOCK_ONLY_HIGH'
            )
        )
    );

    // Add System Instruction if it exists
    if ($systemInstruction) {
        $body['systemInstruction'] = $systemInstruction;
    }

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', '$body: ' . print_r($body, true));

    // API Call
    $response = wp_remote_post($api_url, array(
        'headers' => $headers,
        'body'    => json_encode($body),
        'timeout' => $timeout,
    ));

    // Handle WP Error
    if (is_wp_error($response)) {
        // DIAG - Diagnostics
        kognetiks_ai_summaries_prod_trace('ERROR', 'Error: ' . $response->get_error_message());
        return 'ERROR';
    }

    // Retrieve and Decode Response
    $response_body = json_decode(wp_remote_retrieve_body($response), true);

    // DIAG - Diagnostics - Log response for debugging
    // kognetiks_ai_summaries_back_trace( 'NOTICE', '$response_body: ' . print_r($response_body, true));
    
    // Check HTTP response code
    $response_code = wp_remote_retrieve_response_code($response);
    if ($response_code !== 200) {
        $error_msg = 'HTTP ' . $response_code . ': ' . wp_remote_retrieve_response_message($response);
        if (isset($response_body['error'])) {
            $error_msg = $response_body['error']['message'] ?? $error_msg;
        }
        kognetiks_ai_summaries_prod_trace('ERROR', 'Gemini API HTTP Error: ' . $error_msg);
        return 'ERROR';
    }

    // Handle API Errors
    if (isset($response_body['error'])) {
        // Extract error type and message safely
        $error_msg = $response_body['error']['message'] ?? 'Unknown API Error';
        // DIAG - Diagnostics
        kognetiks_ai_summaries_prod_trace('ERROR', 'Gemini API Error: ' . $error_msg);
        return 'ERROR';
    }

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', '$response_body: ' . print_r($response_body, true));

    // Google API uses 'candidates' instead of 'choices'
    if (isset($response_body['candidates']) && is_array($response_body['candidates']) && !empty($response_body['candidates'][0])) {
        $candidate = $response_body['candidates'][0];
        $finish_reason = $candidate['finishReason'] ?? 'UNKNOWN';
        
        if ($finish_reason === 'SAFETY') {
            // DIAG - Diagnostics
            kognetiks_ai_summaries_prod_trace('ERROR', 'Response blocked by safety filters. Finish reason: ' . $finish_reason);
            return 'ERROR';
        }
        
        // Check for other finish reasons that indicate failure
        if (in_array($finish_reason, array('STOP', 'MAX_TOKENS', 'RECITATION'))) {
            // These are acceptable finish reasons, continue processing
        } elseif ($finish_reason !== 'STOP' && $finish_reason !== 'UNKNOWN') {
            // Log unexpected finish reasons
            kognetiks_ai_summaries_prod_trace('WARNING', 'Unexpected finish reason: ' . $finish_reason);
        }

        // Extract text from response
        $full_response_text = '';
        if (isset($candidate['content']['parts']) && is_array($candidate['content']['parts'])) {
            foreach ($candidate['content']['parts'] as $part) {
                if (isset($part['text']) && !empty($part['text'])) {
                    $full_response_text .= $part['text'];
                }
            }
        }

        // Handle alternative response structures
        if (empty($full_response_text) && isset($candidate['content']['text']) && !empty($candidate['content']['text'])) {
            $full_response_text = $candidate['content']['text'];
        }

        if (!empty($full_response_text)) {
            return trim($full_response_text);
        } else {
            // DIAG - Diagnostics
            kognetiks_ai_summaries_prod_trace('ERROR', 'Empty response text. Finish reason: ' . $finish_reason . '. Response structure: ' . print_r($candidate, true));
        }
    } else {
        // DIAG - Diagnostics - Log the full response for debugging
        kognetiks_ai_summaries_prod_trace('ERROR', 'No candidates in response. Response body: ' . print_r($response_body, true));
    }
    
    // If we get here, there was no valid response
    // DIAG - Diagnostics
    kognetiks_ai_summaries_prod_trace('ERROR', 'No valid response received from Google Gemini API. Full response: ' . print_r($response_body, true));
    return 'ERROR';

}