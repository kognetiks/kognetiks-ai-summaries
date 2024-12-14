<?php
/**
 * Kognetiks AI Summaries for WordPress - Anthorpic API Settings - Ver 1.0.0
 *
 * This file contains the code for the Anthropic API settings page.
 * It handles the support settings and other parameters.
 * 
 * @package kognetiks-ai-summaries
 */

 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

// API/Anthropic Settings section callback
function ksum_anthropic_general_settings_callback($args) {

    ksum_back_trace( 'NOTICE', 'ksum_anthropic_general_settings_callback');
    
    ?>
    <p>Configure the default settings for the plugin Anthropic for AI Summary generation.  Start by adding your API key then selecting your choices below.</p>
    <p>More information about Anthropic models and their capability can be found at <a href="https://docs.anthropic.com/en/docs/about-claude/models" target="_blank">https://docs.anthropic.com/en/docs/about-claude/models</a>.</p>
    <p><b><i>Don't forget to click </i><code>Save Settings</code><i> to save any changes your might make.</i></b></p>
    <p style="background-color: #e0f7fa; padding: 10px;"><b>For an explanation of the API/Anthropic settings and additional documentation please click <a href="?page=kognetiks-ai-summaries&tab=support&dir=api-anthropic-settings&file=api-anthropic-model-settings.md">here</a>.</b></p>                                                                                 
    <?php
    
}

// API key field callback
function ksum_anthropic_api_key_callback($args) {
    $api_key = esc_attr(get_option('ksum_anthropic_api_key'));
    ?>
    <input type="password" id="ksum_anthropic_api_key" name="ksum_anthropic_api_key" value="<?php echo esc_attr( $api_key ); ?>" class="regular-text"  autocomplete="off">
    <?php
}

// Anthropic Model Section Callback
function ksum_anthropic_model_section_callback($args) {
    ?>
    <p>Configure the settings for the plugin by selecting the Anthropic model you would like to use. The plugin will use the selected model to generate responses.</p>
    <?php
}

// Anthropic model choice
function ksum_anthropic_model_choice_callback($args) {

    // DIAG - Diagnostics
    ksum_back_trace( 'NOTICE', 'ksum_anthropic_model_choice_callback');
  
    // Get the saved ksum_anthropic_model_choice value or default to "claude-3-5-sonnet-latest"
    $model_choice = esc_attr(get_option('ksum_anthropic_model_choice', 'claude-3-5-sonnet-latest'));

    // Fetch models from the API
    $models = ksum_anthropic_get_models();

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', '$models: ' . print_r($models, true) );

    // // Limit the models to chat models
    // $models = array_filter($models, function($model) {
    //     return strpos($model['id'], 'anthropic') !== false;
    // });

    // Check for errors
    if (is_string($models) && strpos($models, 'Error:') === 0) {
        // If there's an error, display the hardcoded list
        $model_choice = esc_attr(get_option('ksum_anthropic_model_choice', 'claude-3-5-sonnet-latest'));
        ?>
        <select id="ksum_anthropic_model_choice" name="ksum_anthropic_model_choice">
            <option value="<?php echo esc_attr( 'claude-3-5-sonnet-latest' ); ?>" <?php selected( $model_choice, 'claude-3-5-sonnet-latest' ); ?>><?php echo esc_html( 'claude-3-5-sonnet-latest' ); ?></option>
        </select>
        <?php
    } else {
        // If models are fetched successfully, display them dynamically
        ?>
        <select id="ksum_anthropic_model_choice" name="ksum_anthropic_model_choice">
            <?php foreach ($models as $model): ?>
                <option value="<?php echo esc_attr($model['id']); ?>" <?php selected(esc_attr(get_option('ksum_anthropic_model_choice')), $model['id']); ?>><?php echo esc_html($model['id']); ?></option>
            <?php endforeach; ?>
            ?>
        </select>
        <?php
    }

}

// Anthropic Advanced Settings Section Callback
function ksum_anthropic_advanced_settings_section_callback($args) {
    ?>
    <p>Configure the advanced settings for the plugin. These settings are optional and can be used to fine-tune the plugin's behavior.</p>
    <?php
}

// Max Tokens choice
function ksum_anthropic_max_tokens_callback($args) {
    // Get the saved ksum_openai_max_tokens_setting or default to 500
    $max_tokens = esc_attr(get_option('ksum_anthropic_max_tokens', '500'));
    // Allow for a range of tokens between 100 and 4096 in 100-step increments
    ?>
    <select id="ksum_anthropic_max_tokens" name="ksum_anthropic_max_tokens">
        <?php
        for ($i=100; $i<=4000; $i+=100) {
            echo '<option value="' . esc_attr($i) . '" ' . selected($max_tokens, (string)$i, false) . '>' . esc_html($i) . '</option>';
        }
        ?>
    </select>
    <?php
}

// Set ksum_anthropic_temperature
function ksum_anthropic_temperature_callback($args) {
    $temperature = esc_attr(get_option('ksum_anthropic_temperature', 0.50));
    ?>
    <select id="ksum_anthropic_temperature" name="ksum_anthropic_temperature">
        <?php
        for ($i = 0.01; $i <= 2.01; $i += 0.01) {
            echo '<option value="' . $i . '" ' . selected($temperature, (string)$i) . '>' . esc_html($i) . '</option>';
        }
        ?>
    </select>
    <?php
}

// Set ksum_anthropic_top_p
function ksum_anthropic_top_p_callback($args) {
    $top_p = esc_attr(get_option('ksum_anthropic_top_p', 1.00));
    ?>
    <select id="ksum_open_aitop_p" name="ksum_anthropic_top_p">
        <?php
        for ($i = 0.01; $i <= 1.01; $i += 0.01) {
            echo '<option value="' . $i . '" ' . selected($top_p, (string)$i) . '>' . esc_html($i) . '</option>';
        }
        ?>
    </select>
    <?php
}

// Base URL for the Anthropic API
function ksum_anthropic_base_url_callback($args) {
    $ksum_anthropic_base_url = esc_attr(get_option('ksum_anthropic_base_url', ksum_get_api_base_url()));
    ?>
    <input type="text" id="ksum_anthropic_base_url" name="ksum_anthropic_base_url" value="<?php echo esc_attr( $ksum_anthropic_base_url ); ?>" class="regular-text">
    <?php
}

// Register the Anthropic API settings
function ksum_anthropic_settings_init() {

    // DIAG - Diagnostics
    ksum_back_trace( 'NOTICE', 'ksum_anthropic_settings_init');

    // Add the settings section
    add_settings_section(
        'ksum_api_anthropic_settings_section',
        'API/Anthropic Settings',
        'ksum_anthropic_general_settings_callback',
        'ksum_api_anthropic_general_settings'
    );

    // Anthropic API Key and Model settings

    register_setting('ksum_anthropic_settings', 'ksum_anthropic_api_key');
    register_setting('ksum_anthropic_settings', 'ksum_anthropic_model_choice');
    
    add_settings_section(
        'ksum_anthropic_model_section',
        'API/Anthropic Settings',
        'ksum_anthropic_model_section_callback',
        'ksum_anthropic_model_settings'
    );

    add_settings_field(
        'ksum_anthropic_api_key',
        'Anthropic API Key',
        'ksum_anthropic_api_key_callback',
        'ksum_anthropic_model_settings',
        'ksum_anthropic_model_section'
    );

    add_settings_field(
        'ksum_anthropic_model_choice',
        'Anthropic Model Choice',
        'ksum_anthropic_model_choice_callback',
        'ksum_anthropic_model_settings',
        'ksum_anthropic_model_section'
    );

    // Advanced Anthropic API settings

    register_setting('ksum_anthropic_settings', 'ksum_anthropic_max_tokens');
    register_setting('ksum_anthropic_settings', 'ksum_anthropic_temperature');
    register_setting('ksum_anthropic_settings', 'ksum_anthropic_top_p');
    register_setting('ksum_anthropic_settings', 'ksum_anthropic_base_url');

    // Add the settings section
    add_settings_section(
        'ksum_anthropic_advanced_settings_section',
        'API/Anthropic Advanced Settings',
        'ksum_anthropic_advanced_settings_section_callback',
        'ksum_anthropic_advanced_settings'
    );

    add_settings_field(
        'ksum_anthropic_max_tokens',
        'Max Tokens',
        'ksum_anthropic_max_tokens_callback',
        'ksum_anthropic_advanced_settings',
        'ksum_anthropic_advanced_settings_section'
    );

    add_settings_field(
        'ksum_anthropic_temperature',
        'Temperature',
        'ksum_anthropic_temperature_callback',
        'ksum_anthropic_advanced_settings',
        'ksum_anthropic_advanced_settings_section'
    );

    add_settings_field(
        'ksum_anthropic_top_p',
        'Top P',
        'ksum_anthropic_top_p_callback',
        'ksum_anthropic_advanced_settings',
        'ksum_anthropic_advanced_settings_section'
    );

    add_settings_field(
        'ksum_anthropic_base_url',
        'Anthropic Base URL',
        'ksum_anthropic_base_url_callback',
        'ksum_anthropic_advanced_settings',
        'ksum_anthropic_advanced_settings_section'
    );

}
add_action('admin_init', 'ksum_anthropic_settings_init');
