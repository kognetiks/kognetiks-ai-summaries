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
function ksum_general_settings_callback($args) {

    ?>
    <p>Configure the general settings for the Kognetiks AI Summary plugin.</p>
    <p><b><i>Don't forget to click </i><code>Save Settings</code><i> to save any changes your might make.</i></b></p>
    <p style="background-color: #e0f7fa; padding: 10px;"><b>For an explanation of the general Settings and additional documentation please click <a href="?page=kognetiks-ai-summaries&tab=support&dir=general&file=general.md">here</a>.</b></p>
    <?php

}

// AI Platform Selection section callback - Ver 2.1.8
function ksum_engine_section_callback($args) {

    // DIAG - Diagnostics - Ver 2.1.8
    back_trace( 'NOTICE', 'ksum_engine_section_callback');

    $ksum_ai_platform_choice = esc_attr(get_option('ksum_ai_platform_choice', 'OpenAI'));

    ?>
    <p>Configure the AI Platform for the plugin. The default will be one of <?php echo esc_html( $ksum_ai_platform_choice ) ?>'s AI models; assumes you have or will provide a valid API key.</p>
    <?php

}

// AI Platform Choice - Ver 2.1.8
function ksum_ai_platform_choice_callback($args) {

    $ksum_ai_platform_choice = esc_attr(get_option('ksum_ai_platform_choice', 'OpenAI'));

    if (empty($ksum_ai_platform_choice) || $ksum_ai_platform_choice == 'OpenAI') {

        $ksum_ai_platform_choice = 'OpenAI';
        update_option('ksum_ai_platform_choice', 'OpenAI');
    } else if ($ksum_ai_platform_choice == 'NVIDIA') {

        $ksum_ai_platform_choice = 'NVIDIA';
        update_option('ksum_ai_platform_choice', 'NVIDIA');

    } else if ($ksum_ai_platform_choice == 'Anthropic') {

        $ksum_ai_platform_choice = 'Anthropic';
        update_option('ksum_ai_platform_choice', 'Anthropic');

    } else {

        $ksum_ai_platform_choice = 'OpenAI';
        update_option('ksum_ai_platform_choice', 'OpenAI');

    }

    ?>
    <select id="ksum_ai_platform_choice" name="ksum_ai_platform_choice">
        <option value="OpenAI" <?php selected( $ksum_ai_platform_choice, 'OpenAI' ); ?>><?php echo esc_html( 'OpenAI' ); ?></option>
        <option value="NVIDIA" <?php selected( $ksum_ai_platform_choice, 'NVIDIA' ); ?>><?php echo esc_html( 'NVIDIA' ); ?></option>
        <option value="Anthropic" <?php selected( $ksum_ai_platform_choice, 'Anthropic' ); ?>><?php echo esc_html( 'Anthropic' ); ?></option>
    </select>
    <?php

}

// AI Summaries Enabled section callback
function ksum_additional_selections_section_callback($args) {

    // DIAG - Diagnostics - Ver 2.1.8
    back_trace( 'NOTICE', 'ksum_additional_selections_section_callback');

    ?>
    <p>Turn AI Summaries on/off for your site. <b>NOTE</b>: The default is off until you configure the plugin with a valid API key.</p>
    <p>Select the general lenght of the AI summaries in words. <b>NOTE</b>: The default is 55 words, however fewer or more words my be included in the summary based on analysis of the content by the AI platform of your choice.</p>
    <?php

}

// Activate the AI Summaries
function ksum_additional_selections_callback($args) {

    if (esc_attr(get_option('ksum_ai_platform_choice')) == 'OpenAI' && esc_attr(get_option('ksum_openai_api_key')) == '') {
        $ksum_ai_summaries_enabled = 'Off';
    } elseif (esc_attr(get_option('ksum_ai_platform_choice')) == 'NVIDIA' && esc_attr(get_option('ksum_nvidia_api_key')) == '') {
        $ksum_ai_summaries_enabled = 'Off';
    } elseif (esc_attr(get_option('ksum_ai_platform_choice')) == 'Anthropic' && esc_attr(get_option('ksum_anthropic_api_key')) == '') {
        $ksum_ai_summaries_enabled = 'Off';
   } else {
        $ksum_ai_summaries_enabled = 'Off';
    }

    $ksum_ai_summaries_enabled = esc_attr(get_option('ksum_ai_summaries_enabled', 'Off'));
    ?>
    <select id="ksum_ai_summaries_enabled" name="ksum_ai_summaries_enabled">
        <option value="On" <?php selected( $ksum_ai_summaries_enabled, 'On' ); ?>><?php echo esc_html( 'On' ); ?></option>
        <option value="Off" <?php selected( $ksum_ai_summaries_enabled, 'Off' ); ?>><?php echo esc_html( 'Off' ); ?></option>
    </select>
    <?php    
}

function ksum_ai_summaries_length_callback() {
    $value = esc_attr(get_option('ksum_ai_summaries_length', 55));
    ?>
    <select id="ksum_ai_summaries_length" name="ksum_ai_summaries_length">
        <?php
        for ( $i = 1; $i <= 500; $i++ ) {
            echo '<option value="' . esc_attr( $i ) . '" ' . selected( $value, (string) $i, false ) . '>' . esc_html( $i ) . '</option>';
        }
        ?>
    </select>
    <?php
}

// Register the general settings
function ksum_general_settings_init() {

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', 'ksum_general_settings_init');

    add_settings_section(
        'ksum_general_settings_section',
        'General Settings',
        'ksum_general_settings_callback',
        'ksum_general_settings'
    );

    // Platform selection

    register_setting('ksum_general_settings', 'ksum_ai_platform_choice');

    add_settings_section(
        'ksum_engine_section',
        'AI Platform Selection',
        'ksum_engine_section_callback',
        'ksum_ai_engine_settings'
    );

    add_settings_field(
        'ksum_ai_platform_choice',
        'AI Platform Choice',
        'ksum_ai_platform_choice_callback',
        'ksum_ai_engine_settings',
        'ksum_engine_section'
    );

    // Additional Settings

    register_setting('ksum_general_settings', 'ksum_ai_summaries_enabled');
    register_setting('ksum_general_settings', 'ksum_ai_summaries_length');

    // AI Enabled Section Selection
    add_settings_section(
        'ksum_additional_selections_section',
        'AI Summary Settings',
        'ksum_additional_selections_section_callback',
        'ksum_additional_selections_settings'
    );

    add_settings_field(
        'ksum_ai_summaries_enabled',
        'Turn AI Summaries On/Off',
        'ksum_additional_selections_callback',
        'ksum_additional_selections_settings',
        'ksum_additional_selections_section'
    );

    add_settings_field(
        'ksum_ai_summaries_length',
        'AI Summaries Length (Words)',
        'ksum_ai_summaries_length_callback',
        'ksum_additional_selections_settings',
        'ksum_additional_selections_section'
    );

}
add_action('admin_init', 'ksum_general_settings_init');
