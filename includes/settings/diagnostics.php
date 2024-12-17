<?php
/**
 * Kognetiks AI Summaries - Settings - Diagnostics
 *
 * This file contains the code for the Diagnostics settings page.
 * It allows users to configure the reporting and other parameters
 * required to access the AI APIs from their own account.
 *
* @package kognetiks-ai-summaries
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

// Diagnostics overview section callback
function ksum_diagnostics_overview_section_callback($args) {
    ?>
        <p>The Diagnostics tab checks the API status and set options for diagnostics and notices.</p>
        <p>You can turn on/off console and error logging (as of Version 1.0.0 most are now commented out).</p>
        <p><b><i>Don't forget to click </i><code>Save Settings</code><i> to save any changes your might make.</i></b></p>
        <p style="background-color: #e0f7fa; padding: 10px;"><b>For an explanation on how to use the diagnostics, messages, and additional documentation please click <a href="?page=kognetiks-ai-summaries&tab=support&dir=diagnostics&file=diagnostics.md">here</a>.</b></p>
    <?php
}

// System Details
function ksum_diagnostics_system_settings_section_callback($args) {

    // DIAG - Diagnostics
    ksum_back_trace( 'NOTICE', 'ksum_diagnostics_system_settings_section_callback');
    
    // Get PHP version
    $php_version = phpversion();

    // Get WordPress version
    global $wp_version;

    echo '<p>Kognetiks AI Summaries Version: <b>' . esc_html( ksum_get_plugin_version() ) . '</b><br>';
    echo 'PHP Version: <b>' . esc_html( $php_version ) . '</b><br>';
    echo 'PHP Memory Limit: <b>' . esc_html( ini_get('memory_limit') ) . '</b><br>';
    echo 'WordPress Version: <b>' . esc_html( $wp_version ) . '</b><br>';
    echo 'WordPress Language Code: <b>' . esc_html( get_locale() ) . '</b></p>';

}

// Diagnostics settings section callback
function ksum_diagnostics_section_callback($args) {
    ?>
        <p>Choose your settings for Diagnostics and Plugin Data retention settings.</p>
    <?php
}

// API Status and Results section callback
function ksum_diagnostics_api_status_section_callback($args) {

        $updated_status = ksum_test_api_status();

    ?>
        <p>API STATUS: <b><?php echo esc_html( $updated_status ); ?></b></p>
    <?php
    
}

// Call the api-test.php file to test the API
function ksum_api_test_callback($args) {

    $updated_status = ksum_test_api_status();
    ?>
    <p>API STATUS: <b><?php echo esc_html( $updated_status ); ?></b></p>
    <?php

}

// Diagnostics On/Off
function ksum_diagnostics_setting_callback($args) {

    $ksum_diagnostics = esc_attr(get_option('ksum_diagnostics', 'Off'));
    ?>
    <select id="ksum_diagnostics" name = "ksum_diagnostics">
        <option value="Off" <?php selected( $ksum_diagnostics, 'Off' ); ?>><?php echo esc_html( 'Off' ); ?></option>
        <option value="Success" <?php selected( $ksum_diagnostics, 'Success' ); ?>><?php echo esc_html( 'Success' ); ?></option>
        <option value="Notice" <?php selected( $ksum_diagnostics, 'Notice' ); ?>><?php echo esc_html( 'Notice' ); ?></option>
        <option value="Failure" <?php selected( $ksum_diagnostics, 'Failure' ); ?>><?php echo esc_html( 'Failure' ); ?></option>
        <option value="Warning" <?php selected( $ksum_diagnostics, 'Warning' ); ?>><?php echo esc_html( 'Warning' ); ?></option>
        <option value="Error" <?php selected( $ksum_diagnostics, 'Error' ); ?>><?php echo esc_html( 'Error' ); ?></option>
     </select>
    <?php
    
}

// Custom Error Message
function ksum_custom_error_message_callback($args) {
    $ksum_custom_error_message = esc_attr(get_option('ksum_custom_error_message', 'Your custom error message goes here.'));
    if ( $ksum_custom_error_message === null || $ksum_custom_error_message === '' ) {
        $ksum_custom_error_message = 'Your custom error message goes here.';
    }
    ?>
    <input type="text" id="ksum_custom_error_message" name="ksum_custom_error_message" value="<?php echo esc_html( $ksum_custom_error_message ); ?>" size="50">
    <?php
}

// Suppress Notices On/Off
function ksum_suppress_notices_callback($args) {
    global $ksum_suppress_notices;
    $ksum_suppress_notices = esc_attr(get_option('ksum_suppress_notices', 'Off'));
    ?>
    <select id="chatgpt_suppress_notices_setting" name = "ksum_suppress_notices">
        <option value="On" <?php selected( $ksum_suppress_notices, 'On' ); ?>><?php echo esc_html( 'On' ); ?></option>
        <option value="Off" <?php selected( $ksum_suppress_notices, 'Off' ); ?>><?php echo esc_html( 'Off' ); ?></option>
    </select>
    <?php
}


// Delete Plugin Data on Uninstall
function ksum_delete_data_callback($args) {
    global $ksum_delete_data;
    $ksum_delete_data = esc_attr(get_option('ksum_delete_data', 'no'));
    ?>
    <select id="chatgpt_delete_data_setting" name="ksum_delete_data">
    <option value="no" <?php selected( $ksum_delete_data, 'no' ); ?>><?php echo esc_html( 'DO NOT DELETE' ); ?></option>
    <option value="yes" <?php selected( $ksum_delete_data, 'yes' ); ?>><?php echo esc_html( 'DELETE ALL DATA' ); ?></option>
    </select>
    <?php
}

// Register Diagnostics settings
function ksum_diagnostics_settings_init() {

    register_setting('ksum_diagnostics', 'ksum_diagnostics');
    register_setting('ksum_diagnostics', 'ksum_custom_error_message');
    register_setting('ksum_diagnostics', 'ksum_suppress_notices');
    register_setting('ksum_diagnostics', 'ksum_delete_data');

    add_settings_section(
        'ksum_diagnostics_overview_section',
        'Messages and Diagnostics Overview',
        'ksum_diagnostics_overview_section_callback',
        'ksum_diagnostics_overview'
    );

    add_settings_section(
        'ksum_diagnostics_system_settings_section',
        'Platform Settings',
        'ksum_diagnostics_system_settings_section_callback',
        'ksum_diagnostics_system_settings'
    );

    // Diagnotics API Status
    add_settings_section(
        'ksum_diagnostics_api_status_section',
        'API Status and Results',
        'ksum_diagnostics_api_status_section_callback',
        'ksum_diagnostics_api_status'
    );

    add_settings_field(
        'ksum_api_test',
        'API Test Results',
        'ksum_api_test_callback',
        'ksum_diagnostics',
        'ksum_diagnostics_api_status_section'
    );

    // Diagnostic Settings Section
    add_settings_section(
        'ksum_diagnostics_section',
        'Messages and Diagnostics Settings',
        'ksum_diagnostics_section_callback',
        'ksum_diagnostics'
    );

    // Option to set diagnostics on/off
    add_settings_field(
        'ksum_diagnostics',
        'Plugin Diagnostics',
        'ksum_diagnostics_setting_callback',
        'ksum_diagnostics',
        'ksum_diagnostics_section'
    );

    // Custom Error Message
    add_settings_field(
        'ksum_custom_error_message',
        'Custom Error Message',
        'ksum_custom_error_message_callback',
        'ksum_diagnostics',
        'ksum_diagnostics_section'
    );

    // Option to suppress notices and warnings
    add_settings_field(
        'ksum_suppress_notices',
        'Suppress Notices and Warnings',
        'ksum_suppress_notices_callback',
        'ksum_diagnostics',
        'ksum_diagnostics_section'
    );

    // Option to delete data on uninstall
    add_settings_field(
        'ksum_delete_data',
        'Delete Plugin Data on Uninstall',
        'ksum_delete_data_callback',
        'ksum_diagnostics',
        'ksum_diagnostics_section'
    );
    
}
add_action('admin_init', 'ksum_diagnostics_settings_init');