<?php
/**
 * Kognetiks AI Summaries - OpenAI API Settings - Ver 1.0.0
 *
 * This file contains the code for the OpenAI API settings page.
 * It handles the support settings and other parameters.
 * 
 * @package kognetiks-ai-summaries
 */

 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

// API/OpenAI Settings section callback
function ksum_openai_general_settings_callback($args) {

    ksum_back_trace( 'NOTICE', 'ksum_openai_general_settings_callback');
    
    ?>
    <p>Configure the default settings for the plugin OpenAI for AI Summary generation.  Start by adding your API key then selecting your choices below.</p>
    <p>More information about OpenAI models and their capability can be found at <a href="https://platform.openai.com/docs/models/overview" target="_blank">https://platform.openai.com/docs/models/overview</a>.</p>
    <p><b><i>Don't forget to click </i><code>Save Settings</code><i> to save any changes your might make.</i></b></p>
    <p style="background-color: #e0f7fa; padding: 10px;"><b>For an explanation of the API/OpenAI Settings and additional documentation please click <a href="?page=kognetiks-ai-summariest&tab=support&dir=api-chatgpt-settings&file=api-chatgpt-model-settings.md">here</a>.</b></p>
    <?php
}

// API key field callback
function ksum_openai_api_key_callback($args) {
    $api_key = esc_attr(get_option('ksum_openai_api_key'));
    ?>
    <input type="password" id="ksum_openai_api_key" name="ksum_openai_api_key" value="<?php echo esc_attr( $api_key ); ?>" class="regular-text"  autocomplete="off">
    <?php
}

// OpenAI Model Section Callback
function ksum_openai_model_section_callback($args) {
    ?>
    <p>Configure the settings for the plugin by selecting the OpenAI model you would like to use. The plugin will use the selected model to generate responses.</p>
    <?php
}

// OpenAI model choice
// https://platform.openai.com/docs/models
function ksum_openai_model_choice_callback($args) {
  
    // Get the saved ksum_openai_model_choice value or default to "chatgpt-4o-latest"
    $model_choice = esc_attr(get_option('ksum_openai_model_choice', 'chatgpt-4o-latest'));

    // Fetch models from the API
    $models = ksum_openai_get_models();

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', '$models: ' . print_r($models, true) );

    // Limit the models to chat models
    $models = array_filter($models, function($model) {
        return strpos($model['id'], 'gpt') !== false;
    });

    // Check for errors
    if (is_string($models) && strpos($models, 'Error:') === 0) {
        // If there's an error, display the hardcoded list
        $model_choice = esc_attr(get_option('ksum_openai_model_choice', 'chatgpt-4o-latest'));
        ?>
        <select id="ksum_openai_model_choice" name="ksum_openai_model_choice">
            <option value="<?php echo esc_attr( 'chatgpt-4o-latest' ); ?>" <?php selected( $model_choice, 'chatgpt-4o-latest' ); ?>><?php echo esc_html( 'chatgpt-4o-latest' ); ?></option>
        </select>
        <?php
    } else {
        // If models are fetched successfully, display them dynamically
        ?>
        <select id="ksum_openai_model_choice" name="ksum_openai_model_choice">
            <?php foreach ($models as $model): ?>
                <option value="<?php echo esc_attr($model['id']); ?>" <?php selected(esc_attr(get_option('ksum_openai_model_choice')), $model['id']); ?>><?php echo esc_html($model['id']); ?></option>
            <?php endforeach; ?>
            ?>
        </select>
        <?php
    }

}

// OpenAI Advanced Settings Section Callback
function ksum_openai_advanced_settings_section_callback($args) {
    ?>
    <p>Configure the advanced settings for the plugin. These settings are optional and can be used to fine-tune the plugin's behavior.</p>
    <?php
}

// Max Tokens choice
function ksum_openai_max_tokens_callback($args) {
    // Get the saved ksum_openai_max_tokens_setting or default to 500
    $max_tokens = esc_attr(get_option('ksum_openai_max_tokens', '500'));
    // Allow for a range of tokens between 100 and 4096 in 100-step increments
    ?>
    <select id="ksum_openai_max_tokens" name="ksum_openai_max_tokens">
        <?php
        for ($i=100; $i<=4000; $i+=100) {
            echo '<option value="' . esc_attr($i) . '" ' . selected($max_tokens, (string)$i, false) . '>' . esc_html($i) . '</option>';
        }
        ?>
    </select>
    <?php
}

// Set ksum_openai_temperature
// https://platform.openai.com/docs/assistants/how-it-works/temperature
function ksum_openai_temperature_callback($args) {
    $temperature = esc_attr(get_option('ksum_openai_temperature', 0.50));
    ?>
    <select id="ksum_openai_temperature" name="ksum_openai_temperature">
        <?php
        for ($i = 0.01; $i <= 2.01; $i += 0.01) {
            echo '<option value="' . esc_attr($i) . '" ' . selected($temperature, (string)$i) . '>' . esc_html($i) . '</option>';
        }
        ?>
    </select>
    <?php
}

// Set ksum_openai_top_p
// https://platform.openai.com/docs/assistants/how-it-works/top-p
function ksum_openai_top_p_callback($args) {
    $top_p = esc_attr(get_option('ksum_openai_top_p', 1.00));
    ?>
    <select id="ksum_open_aitop_p" name="ksum_openai_top_p">
        <?php
        for ($i = 0.01; $i <= 1.01; $i += 0.01) {
            echo '<option value="' . esc_attr($i) . '" ' . selected($top_p, (string)$i) . '>' . esc_html($i) . '</option>';
        }
        ?>
    </select>
    <?php
}

// Base URL for the OpenAI API
function ksum_openai_base_url_callback($args) {
    $ksum_openai_base_url = esc_attr(get_option('ksum_openai_base_url', ksum_get_api_base_url()));
    ?>
    <input type="text" id="ksum_openai_base_url" name="ksum_openai_base_url" value="<?php echo esc_attr( $ksum_openai_base_url ); ?>" class="regular-text">
    <?php
}

// Register the OpenAI API settings
function ksum_openai_settings_init() {

    // DIAG - Diagnostics
    ksum_back_trace( 'NOTICE', 'ksum_openai_settings_init');

    // Add the settings section
    add_settings_section(
        'ksum_api_openai_settings_section',
        'API/OpenAI Settings',
        'ksum_openai_general_settings_callback',
        'ksum_api_openai_general_settings'
    );

    // OpenAI API Key and Model settings

    register_setting('ksum_openai_settings', 'ksum_openai_api_key');
    register_setting('ksum_openai_settings', 'ksum_openai_model_choice');
    
    add_settings_section(
        'ksum_openai_model_section',
        'API/OpenAI Settings',
        'ksum_openai_model_section_callback',
        'ksum_openai_model_settings'
    );

    add_settings_field(
        'ksum_openai_api_key',
        'OpenAI API Key',
        'ksum_openai_api_key_callback',
        'ksum_openai_model_settings',
        'ksum_openai_model_section'
    );

    add_settings_field(
        'ksum_openai_model_choice',
        'OpenAI Model Choice',
        'ksum_openai_model_choice_callback',
        'ksum_openai_model_settings',
        'ksum_openai_model_section'
    );

    // Advanced OpenAI API settings

    register_setting('ksum_openai_settings', 'ksum_openai_max_tokens');
    register_setting('ksum_openai_settings', 'ksum_openai_temperature');
    register_setting('ksum_openai_settings', 'ksum_openai_top_p');
    register_setting('ksum_openai_settings', 'ksum_openai_base_url');

    // Add the settings section
    add_settings_section(
        'ksum_openai_advanced_settings_section',
        'API/OpenAI Advanced Settings',
        'ksum_openai_advanced_settings_section_callback',
        'ksum_openai_advanced_settings'
    );

    add_settings_field(
        'ksum_openai_max_tokens',
        'Max Tokens',
        'ksum_openai_max_tokens_callback',
        'ksum_openai_advanced_settings',
        'ksum_openai_advanced_settings_section'
    );

    add_settings_field(
        'ksum_openai_temperature',
        'Temperature',
        'ksum_openai_temperature_callback',
        'ksum_openai_advanced_settings',
        'ksum_openai_advanced_settings_section'
    );

    add_settings_field(
        'ksum_openai_top_p',
        'Top P',
        'ksum_openai_top_p_callback',
        'ksum_openai_advanced_settings',
        'ksum_openai_advanced_settings_section'
    );

    add_settings_field(
        'ksum_openai_base_url',
        'OpenAI Base URL',
        'ksum_openai_base_url_callback',
        'ksum_openai_advanced_settings',
        'ksum_openai_advanced_settings_section'
    );

}
add_action('admin_init', 'ksum_openai_settings_init');
