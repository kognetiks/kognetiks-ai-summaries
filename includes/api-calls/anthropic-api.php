<?php
/**
 * Kognetiks AI Summaries - Anthropic API - Ver 1.0.0
 *
 * This file contains the code for the Anthropic API calls.
 *
 * 
 * @package kognetiks-ai-summaries
 */

 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

// Call the NVIDIA API without trappings
function kognetiks_ai_summaries_anthropic_api_call($api_key, $message) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_anthropic_api_call');

    global $kognetiks_ai_summaries_error_responses;

    // API URL
    $api_url = kognetiks_ai_summaries_get_chat_completions_api_url();

    // Headers
    $headers = array(
        'x-api-key' => $api_key,
        'anthropic-version' => '2023-06-01',
        'Content-Type' => 'application/json',
    );

    // Options. A saved id that is missing from the live list is replaced before the POST.
    $model = esc_attr(get_option('kognetiks_ai_summaries_anthropic_model_choice', kognetiks_ai_summaries_anthropic_default_model()));
    $model = kognetiks_ai_summaries_anthropic_resolve_model($model);
    $max_tokens = intval(esc_attr(get_option('kognetiks_ai_summaries_anthropic_max_tokens_setting', 500)));
    $context = sanitize_text_field(esc_attr(get_option('kognetiks_ai_summaries_anthropic_conversation_context', 'You are a versatile, friendly, and helpful assistant designed to support me in a variety of tasks that responds in Markdown.')));
    $temperature = floatval(esc_attr(get_option('kognetiks_ai_summaries_anthropic_temperature', 0.5)));
    $top_p = floatval(esc_attr(get_option('kognetiks_ai_summaries_anthropic_top_p', 1.0)));
    $timeout = intval(esc_attr(get_option('kognetiks_ai_summaries_anthropic_timeout_setting', 240)));

    // Body. max_tokens covers thinking and text, so a short summary is cut off while thinking is on.
    $body = array(
        'model' => $model,
        'max_tokens' => $max_tokens,
        'messages' => array(
            array(
                'role' => 'user',
                'content' => $message,
            ),
        ),
    );
    $thinking = kognetiks_ai_summaries_anthropic_thinking_config($model);
    if (is_array($thinking)) {
        $body['thinking'] = $thinking;
    }
    $body = wp_json_encode($body);

    // API Call
    $response = wp_remote_post($api_url, array(
        'headers' => $headers,
        'body'    => $body,
        'timeout' => $timeout,
    ));

    // Handle WP Error
    if (is_wp_error($response)) {

        // DIAG - Diagnostics
        kognetiks_ai_summaries_prod_trace('ERROR', 'Error: ' . $response->get_error_message());
        return isset($kognetiks_ai_summaries_error_responses['api_error']) ? $kognetiks_ai_summaries_error_responses['api_error'] : 'An API error occurred.';

    }

    // Retrieve and Decode Response
    $response_body = json_decode(wp_remote_retrieve_body($response), true);

    // Handle API Errors
    if (isset($response_body['error'])) {

        // DIAG - Diagnostics
        kognetiks_ai_summaries_prod_trace('ERROR', 'Error: Type: ' . $response_body['error']['type'] . ' Message: ' . $response_body['error']['message']);
        return isset($kognetiks_ai_summaries_error_responses['api_error']) ? $kognetiks_ai_summaries_error_responses['api_error'] : 'An error occurred.';

    }

    // Current models can return thinking blocks before the summary. Read text blocks by type.
    // A max_tokens stop leaves the last sentence unfinished, so that text is not a summary.
    $summary = kognetiks_ai_summaries_anthropic_response_text($response_body);
    $stop_reason = (is_array($response_body) && isset($response_body['stop_reason']) && is_string($response_body['stop_reason'])) ? $response_body['stop_reason'] : '';
    if ('' !== $summary && 'max_tokens' !== $stop_reason) {

        return $summary;

    }

    // Fallback Response
    // DIAG - Diagnostics
    kognetiks_ai_summaries_prod_trace('ERROR', 'No valid response received from API.' . ('' !== $stop_reason ? ' stop_reason=' . $stop_reason : ''));
    // return 'No response received.';
    return 'ERROR';

}

// Turn off up-front thinking so the token budget is available for the summary.
// Sonnet 5.5 rejects "disabled". Haiku 5.5 rejects "between_tools". Other models keep the request unchanged.
function kognetiks_ai_summaries_anthropic_thinking_config($model) {

    $id = strtolower((string) $model);

    if (false !== strpos($id, 'sonnet-5-5')) {
        return array('type' => 'between_tools');
    }

    if (false !== strpos($id, 'haiku-5-5') || false !== strpos($id, 'sonnet-5')) {
        return array('type' => 'disabled');
    }

    if (false !== strpos($id, 'opus-5') && false === strpos($id, 'opus-5-5')) {
        return array('type' => 'disabled');
    }

    return null;

}

// Join text blocks and skip thinking blocks that precede them.
function kognetiks_ai_summaries_anthropic_response_text($response_body) {

    if (!is_array($response_body) || empty($response_body['content']) || !is_array($response_body['content'])) {
        return '';
    }

    $parts = array();

    foreach ($response_body['content'] as $block) {

        if (!is_array($block)) {
            continue;
        }

        $type = isset($block['type']) ? $block['type'] : 'text';
        if ('text' !== $type || !isset($block['text']) || !is_string($block['text'])) {
            continue;
        }

        if ('' === trim($block['text'])) {
            continue;
        }

        $parts[] = $block['text'];

    }

    return trim(implode("\n", $parts));

}
