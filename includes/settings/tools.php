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

// Register Tools settings - Ver 2.0.7
function ksum_tools_settings_init() {

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', 'ksum_tools_settings_init' );

    // Register tools settings
    register_setting('ksum_tools', 'ksum_options_exporter_extension');

    // Tools Overview
    add_settings_section(
        'ksum_tools_overview_section',
        'Tools Overview',
        'ksum_tools_overview_section_callback',
        'ksum_tools_overview'
    );

    // options_exporter Check Overview
    add_settings_section(
        'ksum_options_exporter_tools_section',
        'Options Exporter Extension',
        'ksum_options_exporter_tools_section_callback',
        'ksum_tools'
    );

    // options_exporter Check Tool
    add_settings_field(
        'ksum_options_exporter_extension',
        'Options Exporter Extension',
        'ksum_options_exporter_tools_callback',
        'ksum_tools',
        'ksum_options_exporter_tools_section'
    );

    add_settings_section(
        'ksum_options_exporter_button_section',
        'Options Exporter',
        'ksum_options_exporter_button_callback',
        'ksum_tools_exporter_button'
    );

    // Manage Error Logs
    add_settings_section(
        'ksum_manage_error_logs_section',
        'Manage Error Logs',
        'ksum_manage_error_logs_section_callback',
        'ksum_manage_error_logs'
    );
   
}
add_action('admin_init', 'ksum_tools_settings_init');

// Add the Tools section
function ksum_tools_overview_section_callback() {

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', 'ksum_tools_overview_section_callback' );

    $nonce = wp_create_nonce('ksum_support_nonce');
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
function ksum_options_exporter_tools_section_callback() {

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', 'ksum_options_exporter_tools_section_callback' );

    ?>
    <div>
        <p>Export the options to a file.</p>
        <p><b>NOTE:</b> If you change the format from CSV to JSON, or vice versa, you will need to scroll to the bottom of the page and <code>Save Changes</code> to update the format.</p>
    </div>
    <?php

}

// Export the options to a file
function ksum_options_exporter_tools_callback() {

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', 'ksum_options_exporter_tools_callback' );

    // Get the saved ksum_options_exporter_extension value or default to "CSV"
    $output_choice = esc_attr(get_option('ksum_options_exporter_extension', 'CSV'));
    ?>
    <div>
        <select id="ksum_options_exporter_extension" name="ksum_options_exporter_extension">
            <option value="<?php echo esc_attr( 'csv' ); ?>" <?php selected( $output_choice, 'csv' ); ?>><?php echo esc_html( 'CSV' ); ?></option>
            <option value="<?php echo esc_attr( 'json' ); ?>" <?php selected( $output_choice, 'json' ); ?>><?php echo esc_html( 'JSON' ); ?></option>
        </select>
    </div>
    <?php

}

function ksum_options_exporter_button_callback() {

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', 'ksum_options_exporter_button_callback' );

    ?>
    <div>
        <p>Use the button (below) to retrieve the plugin options and download the file.</p>
        <?php
            if (is_admin()) {
                $header = " ";
                $header .= '<a class="button button-primary" href="' . esc_url(admin_url('admin-post.php?action=ksum_download_options_data')) . '">Download Options Data</a>';
                echo $header;
            }
        ?>
    </div>
    <?php

}

// Manage Error Logs
function ksum_manage_error_logs_section_callback() {

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', 'ksum_manage_error_logs_section_callback' );

    ?>
    <div>
        <p>Click the <code>Download</code> button to retrieve a log file, or the <code>Delete</code> button to remove a log file.</p>
        <p>Click the <code>Delete All</code> button to remove all log files.</p>
    </div>
    <?php

    // Call the capability tester
    ksum_manage_error_logs();

}
