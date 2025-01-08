<?php
/**
 * Kognetiks AI Summaries -Tools - Ver 1.0.0
 *
 * This file contains the code for the Tools settings page.
 * It handles the support settings and other parameters.
 * 
 * @package kognetiks-ai-summaries
 */

 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

// Add the Tools section
function kognetiks_ai_summaries_tools_overview_section_callback() {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_tools_overview_section_callback' );

    $nonce = wp_create_nonce('kognetiks_ai_summaries_support_nonce');
    $url = add_query_arg(array(
    'page' => 'kognetiks-ai-summaries',
    'tab' => 'support',
    'dir' => 'tools',
    'file' => 'tools.md',
    '_wpnonce' => $nonce
    ), admin_url('admin.php'));
    
    ?>
    <div>
        <p>This tab provides tools, tests and diagnostics that are enabled when the plugin Diagnostics are enabled on the Diagnostics tab.</p>
        <p><b><i>Don't forget to click </i><code>Save Settings</code><i> to save any changes your might make.</i></b></p>
        <p style="background-color: #e0f7fa; padding: 10px;"><b>For an explanation of the Tool settings and additional documentation please click <a href="<?php echo esc_url($url); ?>">here</a>.</b></p>
    </div>
    <?php
    
}

// Options Exporter
function kognetiks_ai_summaries_options_exporter_tools_section_callback() {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_options_exporter_tools_section_callback' );

    ?>
    <div>
        <p>Export the options to a file.</p>
        <p><b>NOTE:</b> If you change the format from CSV to JSON, or vice versa, you will need to scroll to the bottom of the page and <code>Save Changes</code> to update the format.</p>
    </div>
    <?php

}

// Export the options to a file
function kognetiks_ai_summaries_options_exporter_tools_callback() {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_options_exporter_tools_callback' );

    // Get the saved kognetiks_ai_summaries_options_exporter_extension value or default to "CSV"
    $output_choice = esc_attr(get_option('kognetiks_ai_summaries_options_exporter_extension', 'CSV'));
    ?>
    <div>
        <select id="kognetiks_ai_summaries_options_exporter_extension" name="kognetiks_ai_summaries_options_exporter_extension">
            <option value="<?php echo esc_attr( 'csv' ); ?>" <?php selected( $output_choice, 'csv' ); ?>><?php echo esc_html( 'CSV' ); ?></option>
            <option value="<?php echo esc_attr( 'json' ); ?>" <?php selected( $output_choice, 'json' ); ?>><?php echo esc_html( 'JSON' ); ?></option>
        </select>
    </div>
    <?php

}

function kognetiks_ai_summaries_options_exporter_button_callback() {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_options_exporter_button_callback' );

    ?>
    <div>
        <p>Use the button (below) to retrieve the plugin options and download the file.</p>
        <?php
            if (is_admin()) {
                $header = '<a class="button button-primary" href="' . esc_url(admin_url('admin-post.php?action=kognetiks_ai_summaries_download_options_data')) . '">Download Options Data</a>';
                echo wp_kses_post($header);
            }
        ?>
    </div>
    <?php

}

// Manage Error Logs
function kognetiks_ai_summaries_manage_error_logs_section_callback() {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_manage_error_logs_section_callback' );

    ?>
    <div>
        <p>Click the <code>Download</code> button to retrieve a log file, or the <code>Delete</code> button to remove a log file.</p>
        <p>Click the <code>Delete All</code> button to remove all log files.</p>
    </div>
    <?php

    // Call the capability tester
    kognetiks_ai_summaries_manage_error_logs();

}

// Register Tools settings
function kognetiks_ai_summaries_tools_settings_init() {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_tools_settings_init' );

    // Register tools settings with sanitization
    register_setting(
        'kognetiks_ai_summaries_tools',
        'kognetiks_ai_summaries_options_exporter_extension',
        array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    // Tools Overview
    add_settings_section(
        'kognetiks_ai_summaries_tools_overview_section',
        'Tools Overview',
        'kognetiks_ai_summaries_tools_overview_section_callback',
        'kognetiks_ai_summaries_tools_overview'
    );

    // Options Exporter Check Overview
    add_settings_section(
        'kognetiks_ai_summaries_options_exporter_tools_section',
        'Options Exporter Extension',
        'kognetiks_ai_summaries_options_exporter_tools_section_callback',
        'kognetiks_ai_summaries_tools'
    );

    // Options Exporter Check Tool
    add_settings_field(
        'kognetiks_ai_summaries_options_exporter_extension',
        'Options Exporter Extension',
        'kognetiks_ai_summaries_options_exporter_tools_callback',
        'kognetiks_ai_summaries_tools',
        'kognetiks_ai_summaries_options_exporter_tools_section'
    );

    // Options Exporter Button Section
    add_settings_section(
        'kognetiks_ai_summaries_options_exporter_button_section',
        'Options Exporter',
        'kognetiks_ai_summaries_options_exporter_button_callback',
        'kognetiks_ai_summaries_tools_exporter_button'
    );

    // Manage Error Logs
    add_settings_section(
        'kognetiks_ai_summaries_manage_error_logs_section',
        'Manage Error Logs',
        'kognetiks_ai_summaries_manage_error_logs_section_callback',
        'kognetiks_ai_summaries_manage_error_logs'
    );

}
add_action('admin_init', 'kognetiks_ai_summaries_tools_settings_init');
