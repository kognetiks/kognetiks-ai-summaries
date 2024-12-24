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

// Settings page HTML - Ver 1.0.0
function ksum_settings_page() {

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', 'ksum_settings_page' );
    
    if (!current_user_can('manage_options')) {
        return;
    }

    // Verify the nonce for tab navigation
    if (isset($_GET['ksum_tab_nonce'])) {
        check_admin_referer('ksum_tab_navigation', 'ksum_tab_nonce');
    }

    $active_tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'general';
    $settings_updated = isset($_GET['settings-updated']) ? sanitize_text_field(wp_unslash($_GET['settings-updated'])) : '';

    if ($settings_updated) {
        add_settings_error('ksum_messages', 'ksum_message', 'Settings Saved', 'updated');
        settings_errors('ksum_messages');
    }

    // Check reminderCount in local storage
    $reminderCount = intval(esc_attr(get_option('ksum_reminder_count', 0)));
    if ($reminderCount % 100 === 0 && $reminderCount <= 500) {
        // $message = 'If you and your visitors are enjoying having AI summaries on your site, please take a moment to <a href="https://wordpress.org/support/plugin/kognetiks-ai-summaries/reviews/" target="_blank">rate and review this plugin</a>. Thank you!';
        // $message = 'If you and your visitors are enjoying having AI summaries on your site, please take a moment to rate and review this plugin. Thank you!';
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
    // ksum_back_trace( 'NOTICE', '$ksum_reset: ' . $ksum_reset);
    if ( $ksum_reset == 'Yes' ) {
        ksum_restore_default_settings();
    }

    ?>
    <div class="wrap">
        <h1><span class="dashicons dashicons-text"></span> Kognetiks AI Summaries</h1>

        <h2 class="nav-tab-wrapper">
            <a href="?page=kognetiks-ai-summaries&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">General</a>
            <?php if (esc_attr(get_option('ksum_ai_platform_choice', 'OpenAI')) == 'OpenAI') { ?><a href="?page=kognetiks-ai-summaries&tab=api_openai" class="nav-tab <?php echo $active_tab == 'api_openai' ? 'nav-tab-active' : ''; ?>">API/OpenAI</a> <?php } ?>
            <?php if (esc_attr(get_option('ksum_ai_platform_choice', 'OpenAI')) == 'NVIDIA') { ?><a href="?page=kognetiks-ai-summaries&tab=api_nvidia" class="nav-tab <?php echo $active_tab == 'api_nvidia' ? 'nav-tab-active' : ''; ?>">API/NVIDIA</a> <?php } ?>
            <?php if (esc_attr(get_option('ksum_ai_platform_choice', 'OpenAI')) == 'Anthropic') { ?><a href="?page=kognetiks-ai-summaries&tab=api_anthropic" class="nav-tab <?php echo $active_tab == 'api_anthropic' ? 'nav-tab-active' : ''; ?>">API/Anthropic</a> <?php } ?>
            <a href="?page=kognetiks-ai-summaries&tab=diagnostics" class="nav-tab <?php echo $active_tab == 'diagnostics' ? 'nav-tab-active' : ''; ?>">Diagnostics</a>
            <a href="?page=kognetiks-ai-summaries&tab=tools" class="nav-tab <?php echo $active_tab == 'tools' ? 'nav-tab-active' : ''; ?>">Tools</a>
            <a href="?page=kognetiks-ai-summaries&tab=support" class="nav-tab <?php echo $active_tab == 'support' ? 'nav-tab-active' : ''; ?>">Support</a>
       </h2>


       <form id="ksum-settings-form" action="options.php" method="post">
            <?php

            wp_nonce_field('ksum_settings_save', 'ksum_nonce'); // Nonce field

            $ksum_ai_platform_choice = esc_attr(get_option('ksum_ai_platform_choice', 'OpenAI'));

            if ($active_tab == 'general') {

                // DIAG - Diagnostics
                // ksum_back_trace( 'NOTICE', 'General Settings' );

                settings_fields('ksum_general_settings');

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
                // ksum_back_trace( 'NOTICE', 'API/OpenAI Settings' );

                settings_fields('ksum_openai_settings');

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('ksum_api_openai_general_settings');
                echo '</div>';

                // API Settings
                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('ksum_openai_model_settings');
                echo '</div>';

                // Advanced Settings
                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('ksum_openai_advanced_settings');
                echo '</div>';

            } elseif ($active_tab == 'api_nvidia' && $ksum_ai_platform_choice == 'NVIDIA') {

                settings_fields('ksum_nvidia_settings');

                // NVIDIA API Settings

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('ksum_api_nvidia_general_settings');
                echo '</div>';

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('ksum_nvidia_model_settings');
                echo '</div>';

                // Advanced Settings
                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('ksum_nvidia_advanced_settings');
                echo '</div>';

            } elseif ($active_tab == 'api_anthropic' && $ksum_ai_platform_choice == 'Anthropic') {

                settings_fields('ksum_anthropic_settings');

                // NVIDIA API Settings

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('ksum_api_anthropic_general_settings');
                echo '</div>';

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('ksum_anthropic_model_settings');
                echo '</div>';

                // Advanced Settings
                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('ksum_anthropic_advanced_settings');
                echo '</div>';


            } elseif ($active_tab == 'tools') {

                settings_fields('ksum_tools');

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('ksum_tools_overview');
                echo '</div>';

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('ksum_tools');
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