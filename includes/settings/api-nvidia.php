<?php
/**
 * Kognetiks AI Summaries for WordPress - NVIDIA API Settings - Ver 1.0.0
 *
 * This file contains the code for the NVIDIA API settings page.
 * It handles the support settings and other parameters.
 * 
 * @package kognetiks-ai-summaries
 */

 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

// API/NVIDIA settings section callback
function ksum_api_nvidia_settings_general_section_callback($args) {
    ?>
    <p>Configure the default settings for the Chatbot plugin for chat, voice, and image generation.  Start by adding your API key then selecting your choices below.</p>
    <p>More information about NVIDIA models and their capability can be found at <a href="https://build.nvidia.com/explore/discover" target="_blank">https://build.nvidia.com/explore/discover</a>.</p>
    <p><b><i>Don't forget to click </i><code>Save Settings</code><i> to save any changes your might make.</i></b></p>
    <p style="background-color: #e0f7fa; padding: 10px;"><b>For an explanation of the API/NVIDIA settings and additional documentation please click <a href="?page=chatbot-api&tab=support&dir=api-nvidia-settings&file=api-nvidia-model-settings.md">here</a>.</b></p>                                                                                 
    <?php
}

function ksum_api_nvidia_model_general_section_callback($args) {
    ?>
    <p>Configure the settings for the plugin by adding your API key. This plugin requires an API key from NVIDIA to function. You can obtain an API key by signing up at <a href="https://build.nvidia.com/nim?signin=true" target="_blank">https://build.nvidia.com/nim?signin=true</a>.</p>
    <?php
}

// API key field callback
function ksum_api_nvidia_api_key_callback($args) {
    $api_key = get_option('ksum_api_nvidia_api_key');
    ?>
    <input type="password" id="ksum_api_nvidia_api_key" name="ksum_api_nvidia_api_key" value="<?php echo esc_attr( $api_key ); ?>" class="regular-text"  autocomplete="off">
    <?php
}

function ksum_api_nvidia_model_settings_section_callback($args) {
    ?>
    <p>Configure the settings for the plugin when using chat models. Depending on the NVIDIA model you choose, the maximum tokens may be as high as 4097. The default is 150. For more information about the maximum tokens parameter, please see <a href="https://docs.api.nvidia.com/nim/reference/models-1" target="_blank">https://docs.api.nvidia.com/nim/reference/models-1</a>. Enter a conversation context to help the model understand the conversation. See the default for ideas. Some example shortcodes include:</p>
    <?php
}

// NVIDIA Model Settings Callback
function ksum_api_nvidia_model_choice_callback($args) {

    $model_choice = esc_attr(get_option('ksum_api_nvidia_model_choice', 'nvidia/llama-3.1-nemotron-51b-instruct'));

    // Fetch models from the API
    $models = ksum_api_nvidia_get_models();

    // Remove the models not owned by NVIDIA
    $models = array_filter($models, function($model) {
        return $model['owned_by'] === 'nvidia';
    });

    // Check for errors
    if (is_string($models) && strpos($models, 'Error:') === 0) {
        // If there's an error, display the hardcoded list
        $model_choice = esc_attr(get_option('ksum_api_nvidia_model_choice', 'nvidia/llama-3.1-nemotron-51b-instruct'));
        ?>
        <select id="ksum_api_nvidia_model_choice" name="ksum_api_nvidia_model_choice">
            <option value="<?php echo esc_attr( 'nvidia/llama-3.1-nemotron-51b-instruct' ); ?>" <?php selected( $model_choice, 'nvidia/llama-3.1-nemotron-51b-instruct' ); ?>><?php echo esc_html( 'nvidia/llama-3.1-nemotron-51b-instruct' ); ?></option>
        </select>
        <?php
    } else {
        // If models are fetched successfully, display them dynamically
        ?>
        <select id="ksum_api_nvidia_model_choice" name="ksum_api_nvidia_model_choice">
            <?php foreach ($models as $model): ?>
                <option value="<?php echo esc_attr($model['id']); ?>" <?php selected(get_option('ksum_api_nvidia_model_choice'), $model['id']); ?>><?php echo esc_html($model['id']); ?></option>
            <?php endforeach; ?>
            ?>
        </select>
        <?php
    }

}

// Max Tokens choice
function ksum_api_nvidia_max_tokens_setting_callback($args) {
    // Get the saved ksum_api_nvidia_max_tokens_setting or default to 500
    $max_tokens = esc_attr(get_option('ksum_api_nvidia_max_tokens_setting', '500'));
    // Allow for a range of tokens between 100 and 4096 in 100-step increments - Ver 2.0.4
    ?>
    <select id="ksum_api_nvidia_max_tokens_setting" name="ksum_api_nvidia_max_tokens_setting">
        <?php
        for ($i=100; $i<=4000; $i+=100) {
            echo '<option value="' . esc_attr($i) . '" ' . selected($max_tokens, (string)$i, false) . '>' . esc_html($i) . '</option>';
        }
        ?>
    </select>
    <?php
}

// Set ksum_api_nvidia_temperature
function ksum_api_nvidia_temperature_callback($args) {
    $temperature = esc_attr(get_option('ksum_api_nvidia_temperature', 0.5));
    ?>
    <select id="ksum_api_nvidia_temperature" name="ksum_api_nvidia_temperature">
        <?php
        for ($i = 0.01; $i <= 2.01; $i += 0.01) {
            echo '<option value="' . $i . '" ' . selected($temperature, (string)$i) . '>' . esc_html($i) . '</option>';
        }
        ?>
    </select>
    <?php
}

// Set ksum_api_nvidia_top_p
function ksum_api_nvidia_top_p_callback($args) {
    $top_p = esc_attr(get_option('ksum_api_nvidia_top_p', 1.00));
    ?>
    <select id="ksum_api_nvidia_top_p" name="ksum_api_nvidia_top_p">
        <?php
        for ($i = 0.01; $i <= 1.01; $i += 0.01) {
            echo '<option value="' . $i . '" ' . selected($top_p, (string)$i) . '>' . esc_html($i) . '</option>';
        }
        ?>
    </select>
    <?php
}

// API Advanced settings section callback
function ksum_api_nvidia_model_advanced_section_callback($args) {
    ?>
    <p>CAUTION: Configure the advanced settings for the plugin. Enter the base URL for the NVIDIA API.  The default is <code>https://integrate.api.nvidia.com/v1</code>.</p>
    <?php
}

// Base URL for the NVIDIA API
function ksum_api_nvidia_base_url_callback($args) {
    $ksum_api_nvidia_base_url = esc_attr(get_option('ksum_api_nvidia_base_url', 'https://integrate.api.nvidia.com/v1'));
    ?>
    <input type="text" id="ksum_api_nvidia_base_url" name="ksum_api_nvidia_base_url" value="<?php echo esc_attr( $ksum_api_nvidia_base_url ); ?>" class="regular-text">
    <?php
}

// Timeout Settings Callback
function ksum_api_nvidia_timeout_setting_callback($args) {

    // Get the saved ksum_api_nvidia_timeout value or default to 240
    $timeout = esc_attr(get_option('ksum_api_nvidia_timeout_setting', 240));

    // Allow for a range of tokens between 5 and 500 in 5-step increments - Ver 1.8.8
    ?>
    <select id="ksum_api_nvidia_timeout_setting" name="ksum_api_nvidia_timeout_setting">
        <?php
        for ($i=5; $i<=500; $i+=5) {
            echo '<option value="' . esc_attr($i) . '" ' . selected($timeout, (string)$i, false) . '>' . esc_html($i) . '</option>';
        }
        ?>
    </select>
    <?php

}

// Register NVIDIA API settings
function ksum_api_nvidia_api_settings_init() {

    // DIAG - Diagnostics
    ksum_back_trace( 'NOTICE', 'ksum_api_nvidia_api_settings_init');

    add_settings_section(
        'ksum_api_nvidia_settings_section',
        'API/NVIDIA Settings',
        'ksum_api_nvidia_model_settings_general_section_callback',
        'ksum_api_nvidia_model_settings_general'
    );

    register_setting('ksum_api_nvidia_model', 'ksum_api_nvidia_api_key');
    register_setting('ksum_api_nvidia_model', 'ksum_api_nvidia_model_choice');

    add_settings_section(
        'ksum_api_nvidia_model_general_section',
        'NVIDIA API Settings',
        'ksum_api_nvidia_model_general_section_callback',
        'ksum_api_nvidia_model_general'
    );

    add_settings_field(
        'ksum_api_nvidia_api_key',
        'NVIDIA API Key',
        'ksum_api_nvidia_api_key_callback',
        'ksum_api_nvidia_model_general',
        'ksum_api_nvidia_model_general_section'
    );
    
    add_settings_field(
        'ksum_api_nvidia_model_choice',
        'NVIDIA Model Choice',
        'ksum_api_nvidia_model_choice_callback',
        'ksum_api_nvidia_model_general',
        'ksum_api_nvidia_model_general_section'
    );

    register_setting('ksum_api_nvidia_model', 'ksum_api_nvidia_max_tokens_setting');
    register_setting('ksum_api_nvidia_model', 'ksum_api_nvidia_temperature');
    register_setting('ksum_api_nvidia_model', 'ksum_api_nvidia_top_p');

    add_settings_section(
        'ksum_api_nvidia_model_settings_section',
        'Model Settings',
        'ksum_api_nvidia_model_settings_section_callback',
        'ksum_api_nvidia_model_settings'
    );

    // Setting to adjust in small increments the number of Max Tokens 
    add_settings_field(
        'ksum_api_nvidia_max_tokens_setting',
        'Maximum Tokens Setting',
        'ksum_api_nvidia_max_tokens_setting_callback',
        'ksum_api_nvidia_model_settings',
        'ksum_api_nvidia_model_settings_section'
    );

    // Temperature
    add_settings_field(
        'ksum_api_nvidia_temperature',
        'Temperature',
        'ksum_api_nvidia_temperature_callback',
        'ksum_api_nvidia_model_settings',
        'ksum_api_nvidia_model_settings_section'
    );

    // Top P
    add_settings_field(
        'ksum_api_nvidia_top_p',
        'Top P',
        'ksum_api_nvidia_top_p_callback',
        'ksum_api_nvidia_model_settings',
        'ksum_api_nvidia_model_settings_section'
    );

    // Advanced Model Settings - Ver 1.9.5
    register_setting('ksum_api_nvidia_model', 'ksum_api_nvidia_base_url');
    register_setting('ksum_api_nvidia_model', 'ksum_api_nvidia_timeout_setting');

    add_settings_section(
        'ksum_api_nvidia_model_advanced_section',
        'Advanced API Settings',
        'ksum_api_nvidia_model_advanced_section_callback',
        'ksum_api_nvidia_model_advanced'
    );

    // Set the base URL for the API
    add_settings_field(
        'ksum_api_nvidia_base_url',
        'Base URL for API',
        'ksum_api_nvidia_base_url_callback',
        'ksum_api_nvidia_model_advanced',
        'ksum_api_nvidia_model_advanced_section'
    );

    // Timeout setting
    add_settings_field(
        'ksum_api_nvidia_timeout_setting',
        'Timeout Setting (in seconds)',
        'ksum_api_nvidia_timeout_setting_callback',
        'ksum_api_nvidia_model_advanced',
        'ksum_api_nvidia_model_advanced_section'
    );
}
add_action('admin_init', 'ksum_api_nvidia_api_settings_init');