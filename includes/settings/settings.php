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

    // Tabs
    $tabs = array(
        'general'   => 'General',
        'api'       => 'API/OpenAI',
        'summaries' => 'Summaries',
        // ...
    );

    // Default tab
    $active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'general';

    // Verify nonce for tab navigation only if nonce is present
    if ( isset( $_GET['kognetiks_ai_summaries_tab_nonce'] ) ) {
        if ( ! check_admin_referer( 'kognetiks_ai_summaries_tab_navigation', 'kognetiks_ai_summaries_tab_nonce' ) ) {
            // Nonce failed, fall back to general (or just return)
            $active_tab = 'general';
        }
    }
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

        <?php
        $platform = get_option( 'kognetiks_ai_summaries_ai_platform_choice', 'OpenAI' );
        $platform = is_string( $platform ) ? $platform : 'OpenAI';

        // DIAG - Diagnostics
        // kognetiks_ai_summaries_back_trace( 'NOTICE', '$platform: ' . $platform );

        $api_tabs = array(
            'OpenAI'     => array( 'tab' => 'api_openai',    'label' => 'API/OpenAI' ),
            'NVIDIA'     => array( 'tab' => 'api_nvidia',    'label' => 'API/NVIDIA' ),
            'Anthropic'  => array( 'tab' => 'api_anthropic', 'label' => 'API/Anthropic' ),
            'DeepSeek'   => array( 'tab' => 'api_deepseek',  'label' => 'API/DeepSeek' ),
            'Mistral'    => array( 'tab' => 'api_mistral',   'label' => 'API/Mistral' ),
            'Google'     => array( 'tab' => 'api_google',    'label' => 'API/Google' ),
            'Local'      => array( 'tab' => 'api_local',     'label' => 'API/Local' ),
        );

        $base_url = admin_url( 'admin.php?page=kognetiks-ai-summaries' );

        $tabs = array(
            'general'     => array( 'label' => 'General',     'href' => add_query_arg( 'tab', 'general', $base_url ) ),
            'summaries'   => array( 'label' => 'Summaries',   'href' => add_query_arg( 'tab', 'summaries', $base_url ) ),
            'diagnostics' => array( 'label' => 'Diagnostics', 'href' => add_query_arg( 'tab', 'diagnostics', $base_url ) ),
            'tools'       => array( 'label' => 'Tools',       'href' => add_query_arg( 'tab', 'tools', $base_url ) ),
            'support'     => array( 'label' => 'Support',     'href' => add_query_arg( 'tab', 'support', $base_url ) ),
        );

        // Inject the one visible API tab right after General (matches your current behavior).
        if ( isset( $api_tabs[ $platform ] ) ) {
            $api = $api_tabs[ $platform ];
            $tabs = array_slice( $tabs, 0, 1, true )
                + array(
                    $api['tab'] => array(
                        'label' => $api['label'],
                        'href'  => add_query_arg( 'tab', $api['tab'], $base_url ),
                    ),
                )
                + array_slice( $tabs, 1, null, true );
        }
        ?>

        <h2 class="nav-tab-wrapper">
            <?php foreach ( $tabs as $tab_key => $tab ) : ?>
                <a
                    href="<?php echo esc_url( $tab['href'] ); ?>"
                    class="nav-tab <?php echo ( $active_tab === $tab_key ) ? 'nav-tab-active' : ''; ?>"
                >
                    <?php echo esc_html( $tab['label'] ); ?>
                </a>
            <?php endforeach; ?>
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

            } elseif ($active_tab == 'api_mistral' && $kognetiks_ai_summaries_ai_platform_choice == 'Mistral') {

                settings_fields('kognetiks_ai_summaries_mistral_settings');

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_api_mistral_general_settings');
                echo '</div>';

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_mistral_model_settings');
                echo '</div>';

                // Advanced Settings
                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_mistral_advanced_settings');
                echo '</div>';


            } elseif ($active_tab == 'api_google' && $kognetiks_ai_summaries_ai_platform_choice == 'Google') {

                settings_fields('kognetiks_ai_summaries_google_gemini_settings');

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_api_google_gemini_general_settings');
                echo '</div>';

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_google_gemini_model_settings');
                echo '</div>';

                // Advanced Settings
                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_google_gemini_advanced_settings');
                echo '</div>';


            } elseif ($active_tab == 'api_local' && $kognetiks_ai_summaries_ai_platform_choice == 'Local') {

                settings_fields('kognetiks_ai_summaries_local_settings');

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_api_local_general_settings');
                echo '</div>';

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_local_model_settings');
                echo '</div>';

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_local_advanced_settings');
                echo '</div>';


            } elseif ($active_tab == 'summaries') {

                settings_fields('kognetiks_ai_summaries_summaries_settings');

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_summaries_settings');
                echo '</div>';

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_summaries_prompt_settings');
                echo '</div>';

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_summaries_taxonomy_settings');
                echo '</div>';

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_summaries_post_types_settings');
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

                echo '<div style="background-color: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ccc;">';
                do_settings_sections('kognetiks_ai_summaries_cleanup_tools');
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