<?php
/**
 * Kognetiks AI Summaries - Settings - Ver 1.0.0
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
function kognetiks_ai_summaries_settings_page() {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_settings_page' );
    
    if (!current_user_can('manage_options')) {
        return;
    }

    // Verify the nonce for tab navigation
    if (isset($_GET['kognetiks_ai_summaries_tab_nonce'])) {
        if (!check_admin_referer('kognetiks_ai_summaries_tab_navigation', 'kognetiks_ai_summaries_tab_nonce')) {
            // kognetiks_ai_summaries_back_trace( 'ERROR', 'Nonce verification failed.');
            return;
        } else {
            $active_tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'general';
            // kognetiks_ai_summaries_back_trace( 'WARNING', 'Nonce verification passed. $active_tab: ' . $active_tab);
        }
    } else {
        $active_tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'general';
        // kognetiks_ai_summaries_back_trace( 'WARNING', 'Nonce verification not required. $active_tab: ' . $active_tab);
    }

    $active_tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'general';
    $settings_updated = isset($_GET['settings-updated']) ? sanitize_text_field(wp_unslash($_GET['settings-updated'])) : '';

    if ($settings_updated) {
        add_settings_error('kognetiks_ai_summaries_messages', 'kognetiks_ai_summaries_message', 'Settings Saved', 'updated');
        settings_errors('kognetiks_ai_summaries_messages');
    }

    // Check reminderCount in local storage
    $reminderCount = intval(esc_attr(get_option('kognetiks_ai_summaries_reminder_count', 0)));
    $last_reminder_date = esc_attr(get_option('kognetiks_ai_summaries_last_reminder_date', ''));
    $installation_date = esc_attr(get_option('kognetiks_ai_summaries_installation_date'));

    if (!$installation_date) {
        $installation_date = current_time('mysql');
        update_option('kognetiks_ai_summaries_installation_date', $installation_date);
    } else {
        $installation_date = esc_attr($installation_date);
    }

    // Get the current date
    $current_date = current_time('mysql');
    $install_date = new DateTime($installation_date);
    $current_date_obj = new DateTime($current_date);
    $days_since_installation = $install_date->diff($current_date_obj)->days;

    // Show the message only if it has been more than 1 day and less than or equal to 11 days since installation
    if ($days_since_installation > 1 && $days_since_installation <= 11) {
        // Check if the message has already been shown today
        $today = $current_date_obj->format('Y-m-d');
        if ($last_reminder_date !== $today) {
            // $message = 'If you and your visitors are enjoying having AI summaries on your site, please take a moment to <a href="https://wordpress.org/support/plugin/kognetiks-ai-summaries/reviews/" target="_blank">rate and review this plugin</a>. Thank you!';
            $message = 'If you and your visitors are enjoying having AI summaries on your site, please take a moment to rate and review this plugin. Thank you!';
            kognetiks_ai_summaries_general_admin_notice($message);

            // Update the last reminder date and increment reminderCount
            update_option('kognetiks_ai_summaries_last_reminder_date', $today);
            $reminderCount++;
            update_option('kognetiks_ai_summaries_reminder_count', $reminderCount);
        }
    }

    // Check if the user wants to reset the plugin's settings to default
    $kognetiks_ai_summaries_reset = esc_attr(get_option('kognetiks_ai_summaries_reset', 'No'));
    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', '$kognetiks_ai_summaries_reset: ' . $kognetiks_ai_summaries_reset);
    if ( $kognetiks_ai_summaries_reset == 'Yes' ) {
        kognetiks_ai_summaries_restore_default_settings();
    }

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'JUST BEFORE HTML OUTPUT $active_tab: ' . $active_tab);

    ?>
    <div class="wrap">
        <h1><span class="dashicons dashicons-text"></span> Kognetiks AI Summaries</h1>

        <h2 class="nav-tab-wrapper">
            <a href="?page=kognetiks-ai-summaries&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">General</a>
            <?php if (esc_attr(get_option('kognetiks_ai_summaries_ai_platform_choice', 'OpenAI')) == 'OpenAI') { ?><a href="?page=kognetiks-ai-summaries&tab=api_openai" class="nav-tab <?php echo $active_tab == 'api_openai' ? 'nav-tab-active' : ''; ?>">API/OpenAI</a> <?php } ?>
            <?php if (esc_attr(get_option('kognetiks_ai_summaries_ai_platform_choice', 'OpenAI')) == 'NVIDIA') { ?><a href="?page=kognetiks-ai-summaries&tab=api_nvidia" class="nav-tab <?php echo $active_tab == 'api_nvidia' ? 'nav-tab-active' : ''; ?>">API/NVIDIA</a> <?php } ?>
            <?php if (esc_attr(get_option('kognetiks_ai_summaries_ai_platform_choice', 'OpenAI')) == 'Anthropic') { ?><a href="?page=kognetiks-ai-summaries&tab=api_anthropic" class="nav-tab <?php echo $active_tab == 'api_anthropic' ? 'nav-tab-active' : ''; ?>">API/Anthropic</a> <?php } ?>
            <?php if (esc_attr(get_option('kognetiks_ai_summaries_ai_platform_choice', 'OpenAI')) == 'DeepSeek') { ?><a href="?page=kognetiks-ai-summaries&tab=api_deepseek" class="nav-tab <?php echo $active_tab == 'api_deepseek' ? 'nav-tab-active' : ''; ?>">API/DeepSeek</a> <?php } ?>
            <a href="?page=kognetiks-ai-summaries&tab=diagnostics" class="nav-tab <?php echo $active_tab == 'diagnostics' ? 'nav-tab-active' : ''; ?>">Diagnostics</a>
            <a href="?page=kognetiks-ai-summaries&tab=tools" class="nav-tab <?php echo $active_tab == 'tools' ? 'nav-tab-active' : ''; ?>">Tools</a>
            <a href="?page=kognetiks-ai-summaries&tab=support" class="nav-tab <?php echo $active_tab == 'support' ? 'nav-tab-active' : ''; ?>">Support</a>
       </h2>


       <form id="kognetiks-ai-summaries-settings-form" action="options.php" method="post">
            <?php

            wp_nonce_field('kognetiks_ai_summaries_settings_save', 'kognetiks_ai_summaries_nonce'); // Nonce field

            $kognetiks_ai_summaries_ai_platform_choice = esc_attr(get_option('kognetiks_ai_summaries_ai_platform_choice', 'OpenAI'));

            if ($active_tab == 'general') {

                // DIAG - Diagnostics
                // kognetiks_ai_summaries_back_trace( 'NOTICE', 'General Settings' );

                settings_fields('kognetiks_ai_summaries_general_settings');

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_general_settings');
                echo '</div>';

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_ai_engine_settings');
                echo '</div>';

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_additional_selections_settings');
                echo '</div>';

            } elseif ($active_tab == 'api_openai' && $kognetiks_ai_summaries_ai_platform_choice == 'OpenAI') {

                // DIAG - Diagnostics
                // kognetiks_ai_summaries_back_trace( 'NOTICE', 'API/OpenAI Settings' );

                settings_fields('kognetiks_ai_summaries_openai_settings');

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_api_openai_general_settings');
                echo '</div>';

                // API Settings
                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_openai_model_settings');
                echo '</div>';

                // Advanced Settings
                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_openai_advanced_settings');
                echo '</div>';

            } elseif ($active_tab == 'api_nvidia' && $kognetiks_ai_summaries_ai_platform_choice == 'NVIDIA') {

                settings_fields('kognetiks_ai_summaries_nvidia_settings');

                // NVIDIA API Settings

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_api_nvidia_general_settings');
                echo '</div>';

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_nvidia_model_settings');
                echo '</div>';

                // Advanced Settings
                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_nvidia_advanced_settings');
                echo '</div>';

            } elseif ($active_tab == 'api_anthropic' && $kognetiks_ai_summaries_ai_platform_choice == 'Anthropic') {

                settings_fields('kognetiks_ai_summaries_anthropic_settings');

                // NVIDIA API Settings

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_api_anthropic_general_settings');
                echo '</div>';

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_anthropic_model_settings');
                echo '</div>';

                // Advanced Settings
                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_anthropic_advanced_settings');
                echo '</div>';

            } elseif ($active_tab == 'api_deepseek' && $kognetiks_ai_summaries_ai_platform_choice == 'DeepSeek') {

                settings_fields('kognetiks_ai_summaries_deepseek_settings');

                // NVIDIA API Settings

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_api_deepseek_general_settings');
                echo '</div>';

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_deepseek_model_settings');
                echo '</div>';

                // Advanced Settings
                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_deepseek_advanced_settings');
                echo '</div>';


            } elseif ($active_tab == 'tools') {

                settings_fields('kognetiks_ai_summaries_tools');

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_tools_overview');
                echo '</div>';

                // echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                // do_settings_sections('kognetiks_ai_summaries_tools');
                // echo '</div>';

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_tools_exporter_button');
                echo '</div>';

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_manage_error_logs');
                echo '</div>';

            } elseif ($active_tab == 'diagnostics') {

                settings_fields('kognetiks_ai_summaries_diagnostics');

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_diagnostics_overview');
                echo '</div>';

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_diagnostics_system_settings');
                echo '</div>';

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_diagnostics_api_status');
                echo '</div>';

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_diagnostics');
                echo '</div>';

            } elseif ($active_tab == 'tools') {

                settings_fields('kognetiks_ai_summaries_tools');

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_tools_overview');
                echo '</div>';

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_tools_exporter_button');
                echo '</div>';

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_manage_error_logs');
                echo '</div>';


            } elseif ($active_tab == 'support') {

                settings_fields('kognetiks_ai_summaries_support');

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_support');
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