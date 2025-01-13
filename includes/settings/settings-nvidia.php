<?php
/**
 * Kognetiks AI Summaries - NVIDIA API Settings - Ver 1.0.0
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

// API/NVIDIA Settings section callback
function kognetiks_ai_summaries_nvidia_general_settings_callback($args) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_nvidia_general_settings_callback');

    $nonce = wp_create_nonce('kognetiks_ai_summaries_support_nonce');
    $url = add_query_arg(array(
    'page' => 'kognetiks-ai-summaries',
    'tab' => 'support',
    'dir' => 'api-settings',
    'file' => 'api-nvidia-settings.md',
    '_wpnonce' => $nonce
    ), admin_url('admin.php'));
        
    ?>
    <p>Configure the default settings for the plugin NVIDIA for AI Summary generation.  Start by adding your API key then selecting your choices below.</p>
    <p>More information about NVIDIA models and their capability can be found at <a href="https://build.nvidia.com/explore/discover" target="_blank">https://build.nvidia.com/explore/discover</a>.</p>
    <p><b><i>Don't forget to click </i><code>Save Settings</code><i> to save any changes your might make.</i></b></p>
    <p style="background-color: #e0f7fa; padding: 10px;"><b>For an explanation of the NVIDIA settings and additional documentation please click <a href="<?php echo esc_url($url); ?>">here</a>.</b></p>
    <?php
    
}

// API key field callback
function kognetiks_ai_summaries_nvidia_api_key_callback($args) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_nvidia_api_key_callback');

    $api_key = esc_attr(get_option('kognetiks_ai_summaries_nvidia_api_key'));
    ?>
    <input type="password" id="kognetiks_ai_summaries_nvidia_api_key" name="kognetiks_ai_summaries_nvidia_api_key" value="<?php echo esc_attr( $api_key ); ?>" class="regular-text"  autocomplete="off">
    <?php

}

// Hook into the update process for the API key
add_action('update_option_kognetiks_ai_summaries_nvidia_api_key', 'kognetiks_ai_summaries_process_nvidia_api_key', 10, 2);

// Activate AI Summaries if the API key is valid
function kognetiks_ai_summaries_process_nvidia_api_key($old_value, $new_value) {

    // Ensure the new value is sanitized
    $new_value = sanitize_text_field($new_value);

    if (!empty($new_value)) {
        // Enable summaries if the API key is valid (not empty)
        update_option('kognetiks_ai_summaries_enabled', 'Off');
    } else {
        // Disable summaries if the API key is empty
        update_option('kognetiks_ai_summaries_enabled', 'On');
    }

    // Optionally, provide additional logging or actions
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'API key updated. Summaries enabled: ' . (!empty($new_value) ? 'On' : 'Off'));

}

// NVIDIA Model Section Callback
function kognetiks_ai_summaries_nvidia_model_section_callback($args) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_nvidia_model_section_callback');

    ?>
    <p>Configure the settings for the plugin by selecting the NVIDIA model you would like to use. The plugin will use the selected model to generate responses.</p>
    <?php

}

// NVIDIA model choice
function kognetiks_ai_summaries_nvidia_model_choice_callback($args) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_nvidia_model_choice_callback');
  
    // Get the saved kognetiks_ai_summaries_nvidia_model_choice value or default to "nvidia/llama-3.1-nemotron-51b-instruct"
    $model_choice = esc_attr(get_option('kognetiks_ai_summaries_nvidia_model_choice', 'nvidia/llama-3.1-nemotron-51b-instruct'));

    // Fetch models from the API
    $models = kognetiks_ai_summaries_nvidia_get_models();

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', '$models: ' . print_r($models, true) );

    // Limit the models to chat models
    $models = array_filter($models, function($model) {
        return strpos($model['id'], 'nvidia') !== false;
    });

    // Check for errors
    if (is_string($models) && strpos($models, 'Error:') === 0) {
        // If there's an error, display the hardcoded list
        $model_choice = esc_attr(get_option('kognetiks_ai_summaries_nvidia_model_choice', 'nvidia/llama-3.1-nemotron-51b-instruct'));
        ?>
        <select id="kognetiks_ai_summaries_nvidia_model_choice" name="kognetiks_ai_summaries_nvidia_model_choice">
            <option value="<?php echo esc_attr( 'nvidia/llama-3.1-nemotron-51b-instruct' ); ?>" <?php selected( $model_choice, 'nvidia/llama-3.1-nemotron-51b-instruct' ); ?>><?php echo esc_html( 'nvidia/llama-3.1-nemotron-51b-instruct' ); ?></option>
        </select>
        <?php
    } else {
        // If models are fetched successfully, display them dynamically
        ?>
        <select id="kognetiks_ai_summaries_nvidia_model_choice" name="kognetiks_ai_summaries_nvidia_model_choice">
            <?php foreach ($models as $model): ?>
                <option value="<?php echo esc_attr($model['id']); ?>" <?php selected(esc_attr(get_option('kognetiks_ai_summaries_nvidia_model_choice')), $model['id']); ?>><?php echo esc_html($model['id']); ?></option>
            <?php endforeach; ?>
            ?>
        </select>
        <?php
    }

}

// NVIDIA Advanced Settings Section Callback
function kognetiks_ai_summaries_nvidia_advanced_settings_section_callback($args) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_nvidia_advanced_settings_section_callback');

    ?>
    <p>Configure the advanced settings for the plugin. These settings are optional and can be used to fine-tune the plugin's behavior.</p>
    <?php

}

// Max Tokens choice
function kognetiks_ai_summaries_nvidia_max_tokens_callback($args) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_nvidia_max_tokens_callback');

    // Get the saved kognetiks_ai_summaries_openai_max_tokens_setting or default to 500
    $max_tokens = esc_attr(get_option('kognetiks_ai_summaries_nvidia_max_tokens', '500'));

    // Allow for a range of tokens between 100 and 4000 in 100-step increments
    ?>
    <select id="kognetiks_ai_summaries_nvidia_max_tokens" name="kognetiks_ai_summaries_nvidia_max_tokens">
        <?php
        for ($i=100; $i<=4000; $i+=100) {
            echo '<option value="' . esc_attr($i) . '" ' . selected($max_tokens, (string)$i, false) . '>' . esc_html($i) . '</option>';
        }
        ?>
    </select>
    <?php

}

// Set kognetiks_ai_summaries_nvidia_temperature
function kognetiks_ai_summaries_nvidia_temperature_callback($args) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_nvidia_temperature_callback');

    $temperature = esc_attr(get_option('kognetiks_ai_summaries_nvidia_temperature', 0.50));

    ?>
    <select id="kognetiks_ai_summaries_nvidia_temperature" name="kognetiks_ai_summaries_nvidia_temperature">
        <?php
        for ($i = 0.01; $i <= 2.01; $i += 0.01) {
            echo '<option value="' . esc_attr($i) . '" ' . selected($temperature, (string)$i) . '>' . esc_html($i) . '</option>';
        }
        ?>
    </select>
    <?php

}

// Set kognetiks_ai_summaries_nvidia_top_p
function kognetiks_ai_summaries_nvidia_top_p_callback($args) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_nvidia_top_p_callback');

    $top_p = esc_attr(get_option('kognetiks_ai_summaries_nvidia_top_p', 1.00));

    ?>
    <select id="kognetiks_ai_summaries_open_aitop_p" name="kognetiks_ai_summaries_nvidia_top_p">
        <?php
        for ($i = 0.01; $i <= 1.01; $i += 0.01) {
            echo '<option value="' . esc_attr($i) . '" ' . selected($top_p, (string)$i) . '>' . esc_html($i) . '</option>';
        }
        ?>
    </select>
    <?php

}

// Base URL for the NVIDIA API
function kognetiks_ai_summaries_nvidia_base_url_callback($args) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_nvidia_base_url_callback');

    $kognetiks_ai_summaries_nvidia_base_url = esc_attr(get_option('kognetiks_ai_summaries_nvidia_base_url', kognetiks_ai_summaries_get_api_base_url()));

    ?>
    <input type="text" id="kognetiks_ai_summaries_nvidia_base_url" name="kognetiks_ai_summaries_nvidia_base_url" value="<?php echo esc_attr( $kognetiks_ai_summaries_nvidia_base_url ); ?>" class="regular-text">
    <?php

}

// Register the NVIDIA API settings
function kognetiks_ai_summaries_nvidia_settings_init() {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_nvidia_settings_init');

    // Add the settings section
    add_settings_section(
        'kognetiks_ai_summaries_api_nvidia_settings_section',
        'API/NVIDIA Settings',
        'kognetiks_ai_summaries_nvidia_general_settings_callback',
        'kognetiks_ai_summaries_api_nvidia_general_settings'
    );

    // NVIDIA API Key and Model settings
    register_setting(
        'kognetiks_ai_summaries_nvidia_settings',
        'kognetiks_ai_summaries_nvidia_api_key',
        array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    register_setting(
        'kognetiks_ai_summaries_nvidia_settings',
        'kognetiks_ai_summaries_nvidia_model_choice',
        array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    add_settings_section(
        'kognetiks_ai_summaries_nvidia_model_section',
        'API/NVIDIA Settings',
        'kognetiks_ai_summaries_nvidia_model_section_callback',
        'kognetiks_ai_summaries_nvidia_model_settings'
    );

    add_settings_field(
        'kognetiks_ai_summaries_nvidia_api_key',
        'NVIDIA API Key',
        'kognetiks_ai_summaries_nvidia_api_key_callback',
        'kognetiks_ai_summaries_nvidia_model_settings',
        'kognetiks_ai_summaries_nvidia_model_section'
    );

    add_settings_field(
        'kognetiks_ai_summaries_nvidia_model_choice',
        'NVIDIA Model Choice',
        'kognetiks_ai_summaries_nvidia_model_choice_callback',
        'kognetiks_ai_summaries_nvidia_model_settings',
        'kognetiks_ai_summaries_nvidia_model_section'
    );

    // Advanced NVIDIA API settings
    register_setting(
        'kognetiks_ai_summaries_nvidia_settings',
        'kognetiks_ai_summaries_nvidia_max_tokens',
        array(
            'type'              => 'integer',
            'sanitize_callback' => 'absint',
        )
    );

    register_setting(
        'kognetiks_ai_summaries_nvidia_settings',
        'kognetiks_ai_summaries_nvidia_temperature',
        array(
            'type'              => 'float',
            'sanitize_callback' => 'floatval',
        )
    );

    register_setting(
        'kognetiks_ai_summaries_nvidia_settings',
        'kognetiks_ai_summaries_nvidia_top_p',
        array(
            'type'              => 'float',
            'sanitize_callback' => 'floatval',
        )
    );

    register_setting(
        'kognetiks_ai_summaries_nvidia_settings',
        'kognetiks_ai_summaries_nvidia_base_url',
        array(
            'type'              => 'string',
            'sanitize_callback' => 'esc_url_raw',
        )
    );

    // Add the settings section
    add_settings_section(
        'kognetiks_ai_summaries_nvidia_advanced_settings_section',
        'API/NVIDIA Advanced Settings',
        'kognetiks_ai_summaries_nvidia_advanced_settings_section_callback',
        'kognetiks_ai_summaries_nvidia_advanced_settings'
    );

    add_settings_field(
        'kognetiks_ai_summaries_nvidia_max_tokens',
        'Max Tokens',
        'kognetiks_ai_summaries_nvidia_max_tokens_callback',
        'kognetiks_ai_summaries_nvidia_advanced_settings',
        'kognetiks_ai_summaries_nvidia_advanced_settings_section'
    );

    add_settings_field(
        'kognetiks_ai_summaries_nvidia_temperature',
        'Temperature',
        'kognetiks_ai_summaries_nvidia_temperature_callback',
        'kognetiks_ai_summaries_nvidia_advanced_settings',
        'kognetiks_ai_summaries_nvidia_advanced_settings_section'
    );

    add_settings_field(
        'kognetiks_ai_summaries_nvidia_top_p',
        'Top P',
        'kognetiks_ai_summaries_nvidia_top_p_callback',
        'kognetiks_ai_summaries_nvidia_advanced_settings',
        'kognetiks_ai_summaries_nvidia_advanced_settings_section'
    );

    add_settings_field(
        'kognetiks_ai_summaries_nvidia_base_url',
        'NVIDIA Base URL',
        'kognetiks_ai_summaries_nvidia_base_url_callback',
        'kognetiks_ai_summaries_nvidia_advanced_settings',
        'kognetiks_ai_summaries_nvidia_advanced_settings_section'
    );

}
add_action('admin_init', 'kognetiks_ai_summaries_nvidia_settings_init');
