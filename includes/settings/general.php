<?php
/**
 * Kognetiks AI Summaries - Settings - General page
 *
 * This file contains the code for the AI summaries general settings page.
 * It handles the setup settings and other parameters.
 * 
 *
 * @package kognetiks-ai-summaries
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

// General settings section callback - Ver 2.0.2.1
function kognetiks_ai_summaries_general_settings_callback($args) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_general_settings_callback');

    $nonce = wp_create_nonce('kognetiks_ai_summaries_support_nonce');
    $url = add_query_arg(array(
    'page' => 'kognetiks-ai-summaries',
    'tab' => 'support',
    'dir' => 'general',
    'file' => 'general.md',
    '_wpnonce' => $nonce
    ), admin_url('admin.php'));

    ?>
    <p>Configure the general settings for the Kognetiks AI Summary plugin.</p>
    <p><b><i>Don't forget to click </i><code>Save Settings</code><i> to save any changes your might make.</i></b></p>
    <p style="background-color: #e0f7fa; padding: 10px;"><b>For an explanation of the General settings and additional documentation please click <a href="<?php echo esc_url($url); ?>">here</a>.</b></p>
    <?php

}

// AI Platform Selection section callback - Ver 2.1.8
function kognetiks_ai_summaries_engine_section_callback($args) {

    // DIAG - Diagnostics - Ver 2.1.8
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_engine_section_callback');

    $kognetiks_ai_summaries_ai_platform_choice = esc_attr(get_option('kognetiks_ai_summaries_ai_platform_choice', 'OpenAI'));

    ?>
    <p>Configure the AI Platform for the plugin. The default will be one of <?php echo esc_html( $kognetiks_ai_summaries_ai_platform_choice ) ?>'s AI models; assumes you have or will provide a valid API key.</p>
    <?php

}

// AI Platform Choice - Ver 2.1.8
function kognetiks_ai_summaries_ai_platform_choice_callback($args) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_ai_platform_choice_callback');

    $kognetiks_ai_summaries_ai_platform_choice = esc_attr(get_option('kognetiks_ai_summaries_ai_platform_choice', 'OpenAI'));

    if (empty($kognetiks_ai_summaries_ai_platform_choice) || $kognetiks_ai_summaries_ai_platform_choice == 'OpenAI') {

        $kognetiks_ai_summaries_ai_platform_choice = 'OpenAI';
        update_option('kognetiks_ai_summaries_ai_platform_choice', 'OpenAI');
    } else if ($kognetiks_ai_summaries_ai_platform_choice == 'NVIDIA') {

        $kognetiks_ai_summaries_ai_platform_choice = 'NVIDIA';
        update_option('kognetiks_ai_summaries_ai_platform_choice', 'NVIDIA');

    } else if ($kognetiks_ai_summaries_ai_platform_choice == 'Anthropic') {

        $kognetiks_ai_summaries_ai_platform_choice = 'Anthropic';
        update_option('kognetiks_ai_summaries_ai_platform_choice', 'Anthropic');

    } else if ($kognetiks_ai_summaries_ai_platform_choice == 'DeepSeek') {

        $kognetiks_ai_summaries_ai_platform_choice = 'DeepSeek';
        update_option('kognetiks_ai_summaries_ai_platform_choice', 'DeepSeek');

    } else {

        $kognetiks_ai_summaries_ai_platform_choice = 'OpenAI';
        update_option('kognetiks_ai_summaries_ai_platform_choice', 'OpenAI');

    }

    ?>
    <select id="kognetiks_ai_summaries_ai_platform_choice" name="kognetiks_ai_summaries_ai_platform_choice">
        <option value="OpenAI" <?php selected( $kognetiks_ai_summaries_ai_platform_choice, 'OpenAI' ); ?>><?php echo esc_html( 'OpenAI' ); ?></option>
        <option value="NVIDIA" <?php selected( $kognetiks_ai_summaries_ai_platform_choice, 'NVIDIA' ); ?>><?php echo esc_html( 'NVIDIA' ); ?></option>
        <option value="Anthropic" <?php selected( $kognetiks_ai_summaries_ai_platform_choice, 'Anthropic' ); ?>><?php echo esc_html( 'Anthropic' ); ?></option>
        <option value="DeepSeek" <?php selected( $kognetiks_ai_summaries_ai_platform_choice, 'DeepSeek' ); ?>><?php echo esc_html( 'DeepSeek' ); ?></option>
    </select>
    <?php

}

// AI Summaries Enabled section callback
function kognetiks_ai_summaries_additional_selections_section_callback($args) {

    // DIAG - Diagnostics - Ver 2.1.8
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_additional_selections_section_callback');

    ?>
    <p>Turn AI Summaries on/off for your site. <b>NOTE</b>: The default is off until you configure the plugin with a valid API key.</p>
    <p>Select the general length of the AI summaries in words. <b>NOTE</b>: The default is 55 words, however fewer or more words may be included in the summary based on analysis of the content by the AI platform of your choice.</p>
    <?php

}

// Activate the AI Summaries
function kognetiks_ai_summaries_additional_selections_callback($args) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_additional_selections_callback');

    if (esc_attr(get_option('kognetiks_ai_summaries_ai_platform_choice')) == 'OpenAI' && esc_attr(get_option('kognetiks_ai_summaries_openai_api_key')) == '') {
        $kognetiks_ai_summaries_enabled = 'Off';
    } elseif (esc_attr(get_option('kognetiks_ai_summaries_ai_platform_choice')) == 'NVIDIA' && esc_attr(get_option('kognetiks_ai_summaries_nvidia_api_key')) == '') {
        $kognetiks_ai_summaries_enabled = 'Off';
    } elseif (esc_attr(get_option('kognetiks_ai_summaries_ai_platform_choice')) == 'Anthropic' && esc_attr(get_option('kognetiks_ai_summaries_anthropic_api_key')) == '') {
        $kognetiks_ai_summaries_enabled = 'Off';
    } elseif (esc_attr(get_option('kognetiks_ai_summaries_ai_platform_choice')) == 'DeepSeek' && esc_attr(get_option('kognetiks_ai_summaries_deepseek_api_key')) == '') {
        $kognetiks_ai_summaries_enabled = 'Off';
    } else {
        $kognetiks_ai_summaries_enabled = 'Off';
    }

    $kognetiks_ai_summaries_enabled = esc_attr(get_option('kognetiks_ai_summaries_enabled', 'Off'));
    ?>
    <select id="kognetiks_ai_summaries_enabled" name="kognetiks_ai_summaries_enabled">
        <option value="On" <?php selected( $kognetiks_ai_summaries_enabled, 'On' ); ?>><?php echo esc_html( 'On' ); ?></option>
        <option value="Off" <?php selected( $kognetiks_ai_summaries_enabled, 'Off' ); ?>><?php echo esc_html( 'Off' ); ?></option>
    </select>
    <?php    
}

function kognetiks_ai_summaries_length_callback() {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_length_callback');

    $value = esc_attr(get_option('kognetiks_ai_summaries_length', 55));

    ?>
    <select id="kognetiks_ai_summaries_length" name="kognetiks_ai_summaries_length">
        <?php
        for ( $i = 1; $i <= 500; $i++ ) {
            echo '<option value="' . esc_attr( $i ) . '" ' . selected( $value, (string) $i, false ) . '>' . esc_html( $i ) . '</option>';
        }
        ?>
    </select>
    <?php

}

// Register the general settings
function kognetiks_ai_summaries_general_settings_init() {

    add_settings_section(
        'kognetiks_ai_summaries_general_settings_section',
        'General Settings',
        'kognetiks_ai_summaries_general_settings_callback',
        'kognetiks_ai_summaries_general_settings'
    );

    // Platform selection with sanitization
    register_setting(
        'kognetiks_ai_summaries_general_settings',
        'kognetiks_ai_summaries_ai_platform_choice',
        array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    add_settings_section(
        'kognetiks_ai_summaries_engine_section',
        'AI Platform Selection',
        'kognetiks_ai_summaries_engine_section_callback',
        'kognetiks_ai_summaries_ai_engine_settings'
    );

    add_settings_field(
        'kognetiks_ai_summaries_ai_platform_choice',
        'AI Platform Choice',
        'kognetiks_ai_summaries_ai_platform_choice_callback',
        'kognetiks_ai_summaries_ai_engine_settings',
        'kognetiks_ai_summaries_engine_section'
    );

    // AI Enabled Setting with sanitization
    register_setting(
        'kognetiks_ai_summaries_general_settings',
        'kognetiks_ai_summaries_enabled',
        array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    // AI Length Setting with sanitization
    register_setting(
        'kognetiks_ai_summaries_general_settings',
        'kognetiks_ai_summaries_length',
        array(
            'type'              => 'integer',
            'sanitize_callback' => 'absint',
        )
    );

    add_settings_section(
        'kognetiks_ai_summaries_additional_selections_section',
        'AI Summary Settings',
        'kognetiks_ai_summaries_additional_selections_section_callback',
        'kognetiks_ai_summaries_additional_selections_settings'
    );

    add_settings_field(
        'kognetiks_ai_summaries_enabled',
        'Turn AI Summaries On/Off',
        'kognetiks_ai_summaries_additional_selections_callback',
        'kognetiks_ai_summaries_additional_selections_settings',
        'kognetiks_ai_summaries_additional_selections_section'
    );

    add_settings_field(
        'kognetiks_ai_summaries_length',
        'AI Summaries Length (Words)',
        'kognetiks_ai_summaries_length_callback',
        'kognetiks_ai_summaries_additional_selections_settings',
        'kognetiks_ai_summaries_additional_selections_section'
    );
}
add_action('admin_init', 'kognetiks_ai_summaries_general_settings_init');
