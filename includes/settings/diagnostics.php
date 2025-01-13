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
function kognetiks_ai_summaries_diagnostics_overview_section_callback($args) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_diagnostics_overview_section_callback');

    $nonce = wp_create_nonce('kognetiks_ai_summaries_support_nonce');
    $url = add_query_arg(array(
    'page' => 'kognetiks-ai-summaries',
    'tab' => 'support',
    'dir' => 'diagnostics',
    'file' => 'diagnostics.md',
    '_wpnonce' => $nonce
    ), admin_url('admin.php'));

    ?>
        <p>The Diagnostics tab checks the API status and set options for diagnostics and notices.</p>
        <p>You can turn on/off console and error logging (as of Version 1.0.0 most are now commented out).</p>
        <p><b><i>Don't forget to click </i><code>Save Settings</code><i> to save any changes your might make.</i></b></p>
        <p style="background-color: #e0f7fa; padding: 10px;"><b>For an explanation of the Diagnostics settings, Messages  and additional documentation please click <a href="<?php echo esc_url($url); ?>">here</a>.</b></p>
    <?php

}

// System Details
function kognetiks_ai_summaries_diagnostics_system_settings_section_callback($args) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_diagnostics_system_settings_section_callback');
    
    // Get PHP version
    $php_version = phpversion();

    // Get WordPress version
    global $wp_version;

    echo '<p>Kognetiks AI Summaries Version: <b>' . esc_html( kognetiks_ai_summaries_get_plugin_version() ) . '</b><br>';
    echo 'PHP Version: <b>' . esc_html( $php_version ) . '</b><br>';
    echo 'PHP Memory Limit: <b>' . esc_html( ini_get('memory_limit') ) . '</b><br>';
    echo 'WordPress Version: <b>' . esc_html( $wp_version ) . '</b><br>';
    echo 'WordPress Language Code: <b>' . esc_html( get_locale() ) . '</b></p>';

}

// Diagnostics settings section callback
function kognetiks_ai_summaries_diagnostics_section_callback($args) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_diagnostics_section_callback');

    ?>
        <p>Choose your settings for Diagnostics and Plugin Data retention settings.</p>
    <?php

}

// API Status and Results section callback
function kognetiks_ai_summaries_diagnostics_api_status_section_callback($args) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_diagnostics_api_status_section_callback');

    $updated_status = kognetiks_ai_summaries_test_api_status();

    ?>
        <p>API STATUS: <b><?php echo esc_html( $updated_status ); ?></b></p>
    <?php
    
}

// Call the api-test.php file to test the API
function kognetiks_ai_summaries_api_test_callback($args) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_api_test_callback');

    $updated_status = kognetiks_ai_summaries_test_api_status();

    ?>
    <p>API STATUS: <b><?php echo esc_html( $updated_status ); ?></b></p>
    <?php

}

// Diagnostics On/Off
function kognetiks_ai_summaries_diagnostics_setting_callback($args) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_diagnostics_setting_callback');

    $kognetiks_ai_summaries_diagnostics = esc_attr(get_option('kognetiks_ai_summaries_diagnostics', 'Off'));

    ?>
    <select id="kognetiks_ai_summaries_diagnostics" name = "kognetiks_ai_summaries_diagnostics">
        <option value="Off" <?php selected( $kognetiks_ai_summaries_diagnostics, 'Off' ); ?>><?php echo esc_html( 'Off' ); ?></option>
        <option value="Success" <?php selected( $kognetiks_ai_summaries_diagnostics, 'Success' ); ?>><?php echo esc_html( 'Success' ); ?></option>
        <option value="Notice" <?php selected( $kognetiks_ai_summaries_diagnostics, 'Notice' ); ?>><?php echo esc_html( 'Notice' ); ?></option>
        <option value="Failure" <?php selected( $kognetiks_ai_summaries_diagnostics, 'Failure' ); ?>><?php echo esc_html( 'Failure' ); ?></option>
        <option value="Warning" <?php selected( $kognetiks_ai_summaries_diagnostics, 'Warning' ); ?>><?php echo esc_html( 'Warning' ); ?></option>
        <option value="Error" <?php selected( $kognetiks_ai_summaries_diagnostics, 'Error' ); ?>><?php echo esc_html( 'Error' ); ?></option>
     </select>
    <?php
    
}

// Custom Error Message
function kognetiks_ai_summaries_custom_error_message_callback($args) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_custom_error_message_callback');

    $kognetiks_ai_summaries_custom_error_message = esc_attr(get_option('kognetiks_ai_summaries_custom_error_message', 'Your custom error message goes here.'));

    if ( $kognetiks_ai_summaries_custom_error_message === null || $kognetiks_ai_summaries_custom_error_message === '' ) {
        $kognetiks_ai_summaries_custom_error_message = 'Your custom error message goes here.';
    }

    ?>
    <input type="text" id="kognetiks_ai_summaries_custom_error_message" name="kognetiks_ai_summaries_custom_error_message" value="<?php echo esc_html( $kognetiks_ai_summaries_custom_error_message ); ?>" size="50">
    <?php

}

// Suppress Notices On/Off
function kognetiks_ai_summaries_suppress_notices_callback($args) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_suppress_notices_callback');

    global $kognetiks_ai_summaries_suppress_notices;

    $kognetiks_ai_summaries_suppress_notices = esc_attr(get_option('kognetiks_ai_summaries_suppress_notices', 'Off'));

    ?>
    <select id="kognetiks_ai_summaries_suppress_notices_setting" name = "kognetiks_ai_summaries_suppress_notices">
        <option value="On" <?php selected( $kognetiks_ai_summaries_suppress_notices, 'On' ); ?>><?php echo esc_html( 'On' ); ?></option>
        <option value="Off" <?php selected( $kognetiks_ai_summaries_suppress_notices, 'Off' ); ?>><?php echo esc_html( 'Off' ); ?></option>
    </select>
    <?php

}


// Delete Plugin Data on Uninstall
function kognetiks_ai_summaries_delete_data_callback($args) {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_delete_data_callback');

    global $kognetiks_ai_summaries_delete_data;

    $kognetiks_ai_summaries_delete_data = esc_attr(get_option('kognetiks_ai_summaries_delete_data', 'no'));

    ?>
    <select id="kognetiks_ai_summaries_delete_data_setting" name="kognetiks_ai_summaries_delete_data">
    <option value="no" <?php selected( $kognetiks_ai_summaries_delete_data, 'no' ); ?>><?php echo esc_html( 'DO NOT DELETE' ); ?></option>
    <option value="yes" <?php selected( $kognetiks_ai_summaries_delete_data, 'yes' ); ?>><?php echo esc_html( 'DELETE ALL DATA' ); ?></option>
    </select>
    <?php

}

// Register Diagnostics settings
function kognetiks_ai_summaries_diagnostics_settings_init() {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_diagnostics_settings_init');

    // Diagnostics On/Off
    register_setting(
        'kognetiks_ai_summaries_diagnostics',
        'kognetiks_ai_summaries_diagnostics',
        array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    // Custom Error Message
    register_setting(
        'kognetiks_ai_summaries_diagnostics',
        'kognetiks_ai_summaries_custom_error_message',
        array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    // Suppress Notices
    register_setting(
        'kognetiks_ai_summaries_diagnostics',
        'kognetiks_ai_summaries_suppress_notices',
        array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    // Delete Plugin Data on Uninstall
    register_setting(
        'kognetiks_ai_summaries_diagnostics',
        'kognetiks_ai_summaries_delete_data',
        array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    add_settings_section(
        'kognetiks_ai_summaries_diagnostics_overview_section',
        'Messages and Diagnostics Overview',
        'kognetiks_ai_summaries_diagnostics_overview_section_callback',
        'kognetiks_ai_summaries_diagnostics_overview'
    );

    add_settings_section(
        'kognetiks_ai_summaries_diagnostics_system_settings_section',
        'Platform Settings',
        'kognetiks_ai_summaries_diagnostics_system_settings_section_callback',
        'kognetiks_ai_summaries_diagnostics_system_settings'
    );

    // Diagnostics API Status
    add_settings_section(
        'kognetiks_ai_summaries_diagnostics_api_status_section',
        'API Status and Results',
        'kognetiks_ai_summaries_diagnostics_api_status_section_callback',
        'kognetiks_ai_summaries_diagnostics_api_status'
    );

    add_settings_field(
        'kognetiks_ai_summaries_api_test',
        'API Test Results',
        'kognetiks_ai_summaries_api_test_callback',
        'kognetiks_ai_summaries_diagnostics',
        'kognetiks_ai_summaries_diagnostics_api_status_section'
    );

    // Diagnostic Settings Section
    add_settings_section(
        'kognetiks_ai_summaries_diagnostics_section',
        'Messages and Diagnostics Settings',
        'kognetiks_ai_summaries_diagnostics_section_callback',
        'kognetiks_ai_summaries_diagnostics'
    );

    // Option to set diagnostics on/off
    add_settings_field(
        'kognetiks_ai_summaries_diagnostics',
        'Plugin Diagnostics',
        'kognetiks_ai_summaries_diagnostics_setting_callback',
        'kognetiks_ai_summaries_diagnostics',
        'kognetiks_ai_summaries_diagnostics_section'
    );

    // Custom Error Message
    add_settings_field(
        'kognetiks_ai_summaries_custom_error_message',
        'Custom Error Message',
        'kognetiks_ai_summaries_custom_error_message_callback',
        'kognetiks_ai_summaries_diagnostics',
        'kognetiks_ai_summaries_diagnostics_section'
    );

    // Option to suppress notices and warnings
    add_settings_field(
        'kognetiks_ai_summaries_suppress_notices',
        'Suppress Notices and Warnings',
        'kognetiks_ai_summaries_suppress_notices_callback',
        'kognetiks_ai_summaries_diagnostics',
        'kognetiks_ai_summaries_diagnostics_section'
    );

    // Option to delete data on uninstall
    add_settings_field(
        'kognetiks_ai_summaries_delete_data',
        'Delete Plugin Data on Uninstall',
        'kognetiks_ai_summaries_delete_data_callback',
        'kognetiks_ai_summaries_diagnostics',
        'kognetiks_ai_summaries_diagnostics_section'
    );
}
add_action('admin_init', 'kognetiks_ai_summaries_diagnostics_settings_init');
