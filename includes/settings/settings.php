<?php
/**
 * Kognetiks AI Summaries for WordPress - Settings - Ver 1.0.0
 *
 * This file contains the code for the Kognetiks AI Summaries settings page.
 * It handles the support settings and other parameters.
 * 
 * @package kognetiks-ai-summaries
 */

 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

// Set up the Chatbot Main Menu Page - Ver 1.0.0
function ksum_menu_page() {

    add_menu_page(
        'AI Summaries Settings',                // Page title
        'Kognetiks AI Summaries',               // Menu title
        'manage_options',                       // Capability
        'kognetiks-ai-summaries',               // Menu slug
        'ksum_settings_page_html',              // Callback function
        'dashicons-text',                       // Icon URL (optional)
    );

}
add_action('admin_menu', 'ksum_menu_page');

// Settings page HTML - Ver 1.0.0
function ksum_settings_page_html() {
    
    if (!current_user_can('manage_options')) {
        return;
    }

    global $ksum_plugin_version;

    global $ksum_settings;

    $ksum_settings['ksum_version'] = $ksum_plugin_version;
    $ksum_settings_json = wp_json_encode($ksum_settings);
    $escaped_ksum_settings_json = esc_js($ksum_settings_json);
    // FIXME - DO I NEED THIS - IF SO FOR WHAT
    // wp_add_inline_script('ksum-local', 'if (typeof ksum_settings === "undefined") { var ksum_settings = ' . $escaped_ksum_settings_json . '; } else { ksum_settings = ' . $escaped_ksum_settings_json . '; }', 'before');
    
    // Localize the settings
    // FIXME - DO I NEED THIS - IF SO FOR WHAT
    // ksum_localize();

    $active_tab = $_GET['tab'] ?? 'general';
   
    if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
        add_settings_error('ksum_messages', 'ksum_message', 'Settings Saved', 'updated');
        settings_errors('ksum_messages');
    }

    // Check reminderCount in local storage
    $reminderCount = intval(esc_attr(get_option('ksum_reminder_count', 0)));
    if ($reminderCount % 100 === 0 && $reminderCount <= 500) {
        $message = 'If you and your visitors are enjoying having AI summaries on your site, please take a moment to <a href="https://wordpress.org/support/plugin/kognetiks-ai-summaries/reviews/" target="_blank">rate and review this plugin</a>. Thank you!';
        ksum_general_admin_notice($message);
    }
    // Add 1 to reminderCount and update localStorage
    if ($reminderCount < 501) {
        $reminderCount++;
        update_option('ksum_reminder_count', $reminderCount);
    }

    // Check if the user wants to reset the plugin's settings to default
    $ksum_reset = esc_attr(get_option('ksum_reset', 'No'));
    // DIAG - Diagnostics
    ksum_back_trace( 'NOTICE', '$ksum_reset: ' . $ksum_reset);
    if ( $ksum_reset == 'Yes' ) {
        ksum_restore_default_settings();
    }

    ?>
    <div class="wrap">
        <h1><span class="dashicons dashicons-text"></span> AI Summaries Settings</h1>

       <h2 class="nav-tab-wrapper">
            <a href="?page=kognetiks-ai-summaries&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">General</a>
            <?php if (esc_attr(get_option('ksum_ai_platform_choice', 'OpenAI')) == 'OpenAI') { ?><a href="?page=kognetiks-ai-summaries&tab=api_openai" class="nav-tab <?php echo $active_tab == 'api_openai' ? 'nav-tab-active' : ''; ?>">API/OpenAI</a> <?php } ?>
            <?php if (esc_attr(get_option('ksum_ai_platform_choice', 'OpenAI')) == 'NVIDIA') { ?><a href="?page=kognetiks-ai-summaries&tab=api_nvidia" class="nav-tab <?php echo $active_tab == 'api_nvidia' ? 'nav-tab-active' : ''; ?>">API/NVIDIA</a> <?php } ?>
            <?php if (esc_attr(get_option('ksum_ai_platform_choice', 'OpenAI')) == 'Anthropic') { ?><a href="?page=kognetiks-ai-summaries&tab=api_anthropic" class="nav-tab <?php echo $active_tab == 'api_anthropic' ? 'nav-tab-active' : ''; ?>">API/Anthropic</a> <?php } ?>
            <a href="?page=kognetiks-ai-summaries&tab=diagnostics" class="nav-tab <?php echo $active_tab == 'diagnostics' ? 'nav-tab-active' : ''; ?>">Diagnostics</a>
            <a href="?page=kognetiks-ai-summaries&tab=support" class="nav-tab <?php echo $active_tab == 'support' ? 'nav-tab-active' : ''; ?>">Support</a>
       </h2>

       <form id="ksum-settings-form" action="options.php" method="post">
            <?php

            $ksum_ai_platform_choice = esc_attr(get_option('ksum_ai_platform_choice', 'OpenAI'));

            if ($active_tab == 'general') {

                // DIAG - Diagnostics
                ksum_back_trace( 'NOTICE', 'General Settings' );

                settings_fields('ksum_settings');

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('ksum_general_settings');
                echo '</div>';

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('ksum_ai_engine_settings');
                echo '</div>';

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('ksum_additional_selections_settings');
                echo '</div>';

            } elseif ($active_tab == 'api_openai' && $ksum_ai_platform_choice == 'OpenAI') {

                // DIAG - Diagnostics
                ksum_back_trace( 'NOTICE', 'API/OpenAI Settings' );

                settings_fields('ksum_api_openai_settings');

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('ksum_api_openai_settings_section_section');
                echo '</div>';

                // API Settings
                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('ksum_api_openai_advanced_settings_section');
                echo '</div>';

                // Advanced Settings
                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('ksum_api_openai_advanced_settings_section');
                echo '</div>';

            } elseif ($active_tab == 'api_nvidia' && $ksum_ai_platform_choice == 'NVIDIA') {

                settings_fields('chatbot_nvidia_api_model');

                // NVIDIA API Settings

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('chatbot_nvidia_model_settings_general');
                echo '</div>';

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('chatbot_nvidia_api_model_general');
                echo '</div>';

                // Advanced Settings
                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('chatbot_nvidia_api_model_advanced');
                echo '</div>';

            } elseif ($active_tab == 'reporting') {

                settings_fields('ksum_reporting');

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('ksum_reporting_overview');
                echo '</div>';

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('ksum_reporting');
                echo '</div>';

            } elseif ($active_tab == 'diagnostics') {

                settings_fields('ksum_diagnostics');

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('ksum_diagnostics_overview');
                echo '</div>';

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('ksum_diagnostics_system_settings');
                echo '</div>';

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('ksum_diagnostics_api_status');
                echo '</div>';

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('ksum_diagnostics');
                echo '</div>';

            } elseif ($active_tab == 'tools') {

                settings_fields('ksum_tools');

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('ksum_tools_overview');
                echo '</div>';

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('ksum_tools_exporter_button');
                echo '</div>';

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('ksum_manage_error_logs');
                echo '</div>';


            } elseif ($active_tab == 'support') {

                settings_fields('ksum_support');

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('ksum_support');
                echo '</div>';

            }

            submit_button('Save Settings');
            ?>
       </form>
    </div>
    <!-- Added closing tags for body and html -->
    </body>
    </html>
    <?php
}
