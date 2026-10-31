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
                $header = '<a class="button button-primary" href="' . esc_url(wp_nonce_url(admin_url('admin-post.php?action=kognetiks_ai_summaries_download_options_data'), 'kognetiks_ai_summaries_download_options_data')) . '">Download Options Data</a>';
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

// Clean-up Tools Section
function kognetiks_ai_summaries_cleanup_tools_section_callback() {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_cleanup_tools_section_callback' );

    ?>
    <div>
        <p>Use these tools to clean up and maintain your AI summaries, categories, and tags.</p>
        <p><b>WARNING:</b> These operations cannot be undone. Please use with caution.</p>
    </div>
    <?php

}

// Clean-up Tools Callback
function kognetiks_ai_summaries_cleanup_tools_callback() {

    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_cleanup_tools_callback' );

    // Display success messages if actions were performed
    // Security: Verify nonce to prevent spoofed success messages
    if (isset($_GET['cleanup_success']) && isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'kognetiks_ai_summaries_cleanup_success')) {
        $action = sanitize_text_field(wp_unslash($_GET['cleanup_success']));
        switch ($action) {
            case 'delete_all':
                echo '<div class="notice notice-success is-dismissible"><p>All AI summaries have been deleted.</p></div>';
                break;
            case 'refresh_all':
                echo '<div class="notice notice-success is-dismissible"><p>Summary refresh has started. This page continues the remaining posts in batches.</p></div>';
                break;
            case 'proper_case':
                $cats = isset($_GET['cats']) ? intval(wp_unslash($_GET['cats'])) : 0;
                $tags = isset($_GET['tags']) ? intval(wp_unslash($_GET['tags'])) : 0;
                echo '<div class="notice notice-success is-dismissible"><p>Categories and tags have been converted to Proper Case. Updated: ' . esc_html($cats) . ' categories, ' . esc_html($tags) . ' tags.</p></div>';
                break;
            case 'delete_empty_categories':
                $count = isset($_GET['count']) ? intval(wp_unslash($_GET['count'])) : 0;
                echo '<div class="notice notice-success is-dismissible"><p>Empty categories have been deleted. Removed: ' . esc_html($count) . ' categories.</p></div>';
                break;
            case 'delete_empty_tags':
                $count = isset($_GET['count']) ? intval(wp_unslash($_GET['count'])) : 0;
                echo '<div class="notice notice-success is-dismissible"><p>Empty tags have been deleted. Removed: ' . esc_html($count) . ' tags.</p></div>';
                break;
            case 'delete_orphaned':
                $count = isset($_GET['count']) ? intval(wp_unslash($_GET['count'])) : 0;
                echo '<div class="notice notice-success is-dismissible"><p>Orphaned AI summaries have been deleted. Removed: ' . esc_html($count) . ' summaries.</p></div>';
                break;
        }
    }

    ?>
    <div>
        <table class="form-table">
            <tr>
                <th scope="row">Delete All Summaries</th>
                <td>
                    <p>This will remove all stored AI summaries from the database but will not affect your posts.</p>
                    <p>
                        <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=kognetiks_ai_summaries_delete_all_summaries'), 'kognetiks_ai_summaries_cleanup_action')); ?>" 
                           class="button button-secondary" 
                           onclick="return confirm('Are you sure you want to delete ALL AI summaries? This action cannot be undone.');">
                            Delete All Summaries
                        </a>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">Refresh All Summaries</th>
                <td>
                    <p>This will regenerate summaries for all posts that have AI summaries. This may take a while. This can also be expensive if you are on an AI Platform where there is a cost for tokens. <b>Therefore, exercise caution.</b></p>
                    <p>Refresh All runs in the background, one summary at a time. You can close this page. The queue continues on later visits. If the site receives no visits, WordPress cannot run the queue until the next visit, and it then continues the same posts. Invalid API credentials or an invalid model pauses the run until you correct the settings and resume.</p>
                    <?php
                    if ( function_exists( 'kognetiks_ai_summaries_refresh_watchdog' ) ) {
                        kognetiks_ai_summaries_refresh_watchdog();
                    }
                    $kognetiks_ai_summaries_refresh_state   = function_exists( 'kognetiks_ai_summaries_refresh_public_state' ) ? kognetiks_ai_summaries_refresh_public_state() : array();
                    $kognetiks_ai_summaries_refresh_message = function_exists( 'kognetiks_ai_summaries_refresh_status_text' ) ? kognetiks_ai_summaries_refresh_status_text( $kognetiks_ai_summaries_refresh_state ) : '';
                    $kognetiks_ai_summaries_refresh_paused  = isset( $kognetiks_ai_summaries_refresh_state['status'] ) && 'paused' === $kognetiks_ai_summaries_refresh_state['status'];
                    ?>
                    <p>
                        <button type="button" class="button button-secondary" id="kognetiks-ai-summaries-refresh-all">
                            Refresh All Summaries
                        </button>
                        <button type="button" class="button" id="kognetiks-ai-summaries-refresh-resume" <?php echo $kognetiks_ai_summaries_refresh_paused ? '' : 'hidden'; ?>>
                            Resume Refresh
                        </button>
                    </p>
                    <p id="kognetiks-ai-summaries-refresh-status" class="kognetiks-ai-summaries-refresh-status" aria-live="polite"><?php echo esc_html( $kognetiks_ai_summaries_refresh_message ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">Check for Proper Case Tags and Categories</th>
                <td>
                    <p>This will update all existing categories and tags to use Proper Case formatting (e.g., "acoustics" becomes "Acoustics").</p>
                    <p>
                        <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=kognetiks_ai_summaries_proper_case_categories_tags'), 'kognetiks_ai_summaries_cleanup_action')); ?>" 
                           class="button button-secondary" 
                           onclick="return confirm('Are you sure you want to convert all categories and tags to Proper Case?');">
                            Convert to Proper Case
                        </a>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">Delete Empty Categories</th>
                <td>
                    <p>This will permanently delete all categories that have no posts assigned to them (count of 0).</p>
                    <p>
                        <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=kognetiks_ai_summaries_delete_empty_categories'), 'kognetiks_ai_summaries_cleanup_action')); ?>" 
                           class="button button-secondary" 
                           onclick="return confirm('Are you sure you want to delete all empty categories? This action cannot be undone.');">
                            Delete Empty Categories
                        </a>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">Delete Empty Tags</th>
                <td>
                    <p>This will permanently delete all tags that have no posts assigned to them (count of 0).</p>
                    <p>
                        <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=kognetiks_ai_summaries_delete_empty_tags'), 'kognetiks_ai_summaries_cleanup_action')); ?>" 
                           class="button button-secondary" 
                           onclick="return confirm('Are you sure you want to delete all empty tags? This action cannot be undone.');">
                            Delete Empty Tags
                        </a>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">Delete Orphaned AI Summaries</th>
                <td>
                    <p>This will permanently delete all AI summaries for posts that no longer exist.</p>
                    <p>
                        <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=kognetiks_ai_summaries_delete_orphaned_summaries'), 'kognetiks_ai_summaries_cleanup_action')); ?>" 
                           class="button button-secondary" 
                           onclick="return confirm('Are you sure you want to delete all orphaned AI summaries? This action cannot be undone.');">
                            Delete Orphaned AI Summaries
                        </a>
                    </p>
                </td>
            </tr>
        </table>
    </div>
    <?php

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

    // Clean-up Tools Section
    add_settings_section(
        'kognetiks_ai_summaries_cleanup_tools_section',
        'Clean-up Tools',
        'kognetiks_ai_summaries_cleanup_tools_section_callback',
        'kognetiks_ai_summaries_cleanup_tools'
    );

    // Clean-up Tools Field
    add_settings_field(
        'kognetiks_ai_summaries_cleanup_tools',
        'Clean-up Actions',
        'kognetiks_ai_summaries_cleanup_tools_callback',
        'kognetiks_ai_summaries_cleanup_tools',
        'kognetiks_ai_summaries_cleanup_tools_section'
    );

}
add_action('admin_init', 'kognetiks_ai_summaries_tools_settings_init');

// Validate and sanitize table name to prevent SQL injection
function kognetiks_ai_summaries_validate_table_name($table_name) {
    
    global $wpdb;
    
    // Expected table name pattern
    $expected_table = $wpdb->prefix . 'kognetiks_ai_summaries';
    
    // Validate that the table name matches the expected pattern
    if ($table_name !== $expected_table) {
        kognetiks_ai_summaries_prod_trace( 'ERROR', 'Invalid table name provided: ' . esc_html($table_name) );
        return false;
    }
    
    // Additional validation: ensure table name contains only safe characters
    if (!preg_match('/^[a-zA-Z0-9_]+$/', str_replace($wpdb->prefix, '', $table_name))) {
        kognetiks_ai_summaries_prod_trace( 'ERROR', 'Table name contains invalid characters: ' . esc_html($table_name) );
        return false;
    }
    
    return $table_name;
}

// Delete all AI summaries from the database
function kognetiks_ai_summaries_delete_all_summaries() {
    
    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_delete_all_summaries' );
    
    global $wpdb;
    
    // Delete all rows from the AI summaries table
    $table_name = $wpdb->prefix . 'kognetiks_ai_summaries';
    
    // Validate table name to prevent SQL injection
    $validated_table = kognetiks_ai_summaries_validate_table_name($table_name);
    if ($validated_table === false) {
        return false;
    }
    
    // Since we've validated the table name against a whitelist, we can safely use it with backticks
    $result = $wpdb->query("DELETE FROM `" . esc_sql($validated_table) . "`");
    
    // Clear all caches
    wp_cache_flush();
    
    // Handle any errors
    if ( $wpdb->last_error ) {
        kognetiks_ai_summaries_prod_trace( 'ERROR', 'Error deleting all AI summaries: ' . $wpdb->last_error );
        return false;
    }
    
    return true;
}

/**
 * WP-Cron hook for one summary.
 *
 * With no site traffic, WordPress does not run this event or the watchdog.
 * The queue and lock stay stored. The next request that WordPress can use to
 * spawn cron, including a later visit to the Tools tab, continues the same queue.
 */
const KOGNETIKS_AI_SUMMARIES_REFRESH_WORKER = 'kognetiks_ai_summaries_refresh_worker';

/**
 * Recurring recovery hook for an expired lock or a missing worker event.
 */
const KOGNETIKS_AI_SUMMARIES_REFRESH_WATCHDOG = 'kognetiks_ai_summaries_refresh_watchdog';

/**
 * Seconds before a refresh lock expires.
 *
 * This matches one capped provider HTTP call plus time to record the outcome.
 * It does not extend a proxy or web-server timeout.
 *
 * @return int
 */
function kognetiks_ai_summaries_refresh_lock_ttl() {
    $platform = (string) get_option( 'kognetiks_ai_summaries_ai_platform_choice', 'OpenAI' );
    $map      = array(
        'OpenAI'    => array( 'kognetiks_ai_summaries_openai_timeout_setting', 240 ),
        'NVIDIA'    => array( 'kognetiks_ai_summaries_nvidia_timeout_setting', 240 ),
        'Anthropic' => array( 'kognetiks_ai_summaries_anthropic_timeout_setting', 240 ),
        'DeepSeek'  => array( 'kognetiks_ai_summaries_deepseek_timeout_setting', 240 ),
        'Mistral'   => array( 'kognetiks_ai_summaries_mistral_timeout_setting', 240 ),
        'Google'    => array( 'kognetiks_ai_summaries_google_gemini_timeout_setting', 240 ),
        'Local'     => array( 'kognetiks_ai_summaries_local_timeout_setting', 360 ),
    );
    $setting = isset( $map[ $platform ] ) ? $map[ $platform ] : array( 'kognetiks_ai_summaries_openai_timeout_setting', 240 );
    $timeout = (int) get_option( $setting[0], $setting[1] );
    $max     = (int) ini_get( 'max_execution_time' );
    if ( $max > 0 ) {
        $timeout = min( $timeout, max( 5, $max - 20 ) );
    }
    return max( 30, $timeout + 30 );
}

/**
 * Delay before another attempt after a transient failure.
 *
 * Retry-After is honored and capped at one hour. Otherwise the delay doubles
 * from 60 seconds and stops at 15 minutes.
 *
 * @param int $attempts    Attempts already used for this post.
 * @param int $retry_after Provider Retry-After value in seconds.
 * @return int
 */
function kognetiks_ai_summaries_refresh_retry_delay( $attempts, $retry_after ) {
    if ( (int) $retry_after > 0 ) {
        $delay = min( 3600, max( 1, (int) $retry_after ) );
    } else {
        $delay = min( 900, 60 * ( 2 ** max( 0, (int) $attempts - 1 ) ) );
    }
    return (int) apply_filters( 'kognetiks_ai_summaries_refresh_retry_delay', $delay, $attempts, $retry_after );
}

/**
 * @param array $schedules Registered cron schedules.
 * @return array
 */
function kognetiks_ai_summaries_refresh_cron_schedules( $schedules ) {
    if ( ! isset( $schedules['kognetiks_ai_summaries_minute'] ) ) {
        $schedules['kognetiks_ai_summaries_minute'] = array(
            'interval' => 60,
            'display'  => 'Every minute (Kognetiks AI Summaries)',
        );
    }
    return $schedules;
}
add_filter( 'cron_schedules', 'kognetiks_ai_summaries_refresh_cron_schedules' );

/**
 * Store a scheduling failure without changing the queue.
 *
 * @param mixed  $result Result from a WordPress cron call.
 * @param string $which  worker or watchdog.
 */
function kognetiks_ai_summaries_refresh_record_schedule_failure( $result, $which ) {
    $code    = 'schedule_failed';
    $message = 'The ' . $which . ' event could not be scheduled.';
    if ( is_wp_error( $result ) ) {
        $code    = $result->get_error_code();
        $message = $result->get_error_message();
    }
    update_option(
        'kognetiks_ai_summaries_refresh_schedule_error',
        array(
            'code'    => (string) $code,
            'message' => (string) $message,
            'event'   => (string) $which,
            'time'    => time(),
        ),
        false
    );
}

/**
 * Remove a stored scheduling failure after an event is confirmed.
 */
function kognetiks_ai_summaries_refresh_clear_schedule_failure() {
    delete_option( 'kognetiks_ai_summaries_refresh_schedule_error' );
}

/**
 * Ask WordPress to spawn cron. A false return means nothing was due.
 *
 * @param bool $spawn     Whether spawning was requested.
 * @param int  $timestamp Event time. Unused except to document the event.
 */
function kognetiks_ai_summaries_refresh_maybe_spawn( $spawn, $timestamp ) {
    unset( $timestamp );
    if ( $spawn && apply_filters( 'kognetiks_ai_summaries_refresh_spawn_cron', true ) ) {
        spawn_cron();
    }
}

/**
 * Schedule one worker event. A second event is not added while one is waiting.
 *
 * @param int  $timestamp Unix time.
 * @param bool $spawn     Whether to ask WordPress to spawn cron.
 * @return int|false Scheduled timestamp, or false when no worker event exists.
 */
function kognetiks_ai_summaries_refresh_schedule_worker( $timestamp, $spawn ) {
    $existing = wp_next_scheduled( KOGNETIKS_AI_SUMMARIES_REFRESH_WORKER );
    if ( $existing ) {
        kognetiks_ai_summaries_refresh_clear_schedule_failure();
        if ( $spawn && (int) $existing <= time() ) {
            kognetiks_ai_summaries_refresh_maybe_spawn( true, (int) $existing );
        }
        return (int) $existing;
    }

    $timestamp = max( time(), (int) $timestamp );
    $result    = wp_schedule_single_event( $timestamp, KOGNETIKS_AI_SUMMARIES_REFRESH_WORKER, array(), true );
    if ( is_wp_error( $result ) && 'duplicate_event' === $result->get_error_code() ) {
        $existing = wp_next_scheduled( KOGNETIKS_AI_SUMMARIES_REFRESH_WORKER );
        if ( $existing ) {
            kognetiks_ai_summaries_refresh_clear_schedule_failure();
            return (int) $existing;
        }
    }
    if ( is_wp_error( $result ) || false === $result ) {
        kognetiks_ai_summaries_refresh_record_schedule_failure( $result, 'worker' );
        return false;
    }

    kognetiks_ai_summaries_refresh_clear_schedule_failure();
    kognetiks_ai_summaries_refresh_maybe_spawn( $spawn, $timestamp );
    return $timestamp;
}

/**
 * Move the worker event to a new time without discarding the only copy first.
 *
 * @param int  $timestamp Desired Unix time.
 * @param bool $spawn     Whether to ask WordPress to spawn cron.
 * @return int|false Timestamp that remains scheduled, or false when none remains.
 */
function kognetiks_ai_summaries_refresh_replace_worker_event( $timestamp, $spawn ) {
    kognetiks_ai_summaries_refresh_ensure_watchdog();
    $desired  = max( time(), (int) $timestamp );
    $existing = wp_next_scheduled( KOGNETIKS_AI_SUMMARIES_REFRESH_WORKER );
    if ( $existing && (int) $existing === $desired ) {
        kognetiks_ai_summaries_refresh_clear_schedule_failure();
        kognetiks_ai_summaries_refresh_maybe_spawn( $spawn, $desired );
        return (int) $existing;
    }

    $result = wp_schedule_single_event( $desired, KOGNETIKS_AI_SUMMARIES_REFRESH_WORKER, array(), true );
    if ( ! is_wp_error( $result ) && false !== $result ) {
        if ( $existing && (int) $existing !== $desired ) {
            wp_unschedule_event( (int) $existing, KOGNETIKS_AI_SUMMARIES_REFRESH_WORKER, array(), true );
        }
        kognetiks_ai_summaries_refresh_clear_schedule_failure();
        kognetiks_ai_summaries_refresh_maybe_spawn( $spawn, $desired );
        return $desired;
    }

    if ( is_wp_error( $result ) && 'duplicate_event' === $result->get_error_code() ) {
        if ( ! $existing ) {
            $existing = wp_next_scheduled( KOGNETIKS_AI_SUMMARIES_REFRESH_WORKER );
        }
        if ( $existing && abs( (int) $existing - $desired ) < 2 ) {
            kognetiks_ai_summaries_refresh_clear_schedule_failure();
            kognetiks_ai_summaries_refresh_maybe_spawn( $spawn, (int) $existing );
            return (int) $existing;
        }
        if ( $existing ) {
            $removed = wp_unschedule_event( (int) $existing, KOGNETIKS_AI_SUMMARIES_REFRESH_WORKER, array(), true );
            if ( is_wp_error( $removed ) || false === $removed ) {
                kognetiks_ai_summaries_refresh_record_schedule_failure( $removed, 'worker' );
                return (int) $existing;
            }
        }
        $retry = wp_schedule_single_event( $desired, KOGNETIKS_AI_SUMMARIES_REFRESH_WORKER, array(), true );
        if ( is_wp_error( $retry ) || false === $retry ) {
            kognetiks_ai_summaries_refresh_record_schedule_failure( $retry, 'worker' );
            return false;
        }
        kognetiks_ai_summaries_refresh_clear_schedule_failure();
        kognetiks_ai_summaries_refresh_maybe_spawn( $spawn, $desired );
        return $desired;
    }

    if ( $existing ) {
        return (int) $existing;
    }
    kognetiks_ai_summaries_refresh_record_schedule_failure( $result, 'worker' );
    return false;
}

/**
 * Register a recovery event before a provider request.
 *
 * An existing worker event is left in place when the new event cannot be added.
 *
 * @param int $when Unix time at which recovery should run.
 * @return bool True when a worker event is waiting.
 */
function kognetiks_ai_summaries_refresh_prepare_recovery( $when ) {
    kognetiks_ai_summaries_refresh_ensure_watchdog();
    $scheduled = kognetiks_ai_summaries_refresh_schedule_recovery_event( $when );
    return false !== $scheduled && false !== wp_next_scheduled( KOGNETIKS_AI_SUMMARIES_REFRESH_WORKER );
}

/**
 * Add the recovery event, then drop an earlier one only after the new one exists.
 *
 * @param int $desired Unix time.
 * @return int|false
 */
function kognetiks_ai_summaries_refresh_schedule_recovery_event( $desired ) {
    $desired  = max( time() + 1, (int) $desired );
    $existing = wp_next_scheduled( KOGNETIKS_AI_SUMMARIES_REFRESH_WORKER );
    if ( $existing && (int) $existing >= $desired ) {
        kognetiks_ai_summaries_refresh_clear_schedule_failure();
        return (int) $existing;
    }

    $result = wp_schedule_single_event( $desired, KOGNETIKS_AI_SUMMARIES_REFRESH_WORKER, array(), true );
    if ( ! is_wp_error( $result ) && false !== $result ) {
        if ( $existing && (int) $existing < $desired ) {
            wp_unschedule_event( (int) $existing, KOGNETIKS_AI_SUMMARIES_REFRESH_WORKER, array(), true );
        }
        kognetiks_ai_summaries_refresh_clear_schedule_failure();
        return $desired;
    }

    if ( is_wp_error( $result ) && 'duplicate_event' === $result->get_error_code() && $existing ) {
        kognetiks_ai_summaries_refresh_clear_schedule_failure();
        return (int) $existing;
    }

    if ( $existing ) {
        return (int) $existing;
    }
    kognetiks_ai_summaries_refresh_record_schedule_failure( $result, 'worker' );
    return false;
}

/**
 * Keep the watchdog scheduled while a run exists.
 *
 * @return bool True when a watchdog event is waiting.
 */
function kognetiks_ai_summaries_refresh_ensure_watchdog() {
    if ( wp_next_scheduled( KOGNETIKS_AI_SUMMARIES_REFRESH_WATCHDOG ) ) {
        return true;
    }
    $result = wp_schedule_event( time() + 60, 'kognetiks_ai_summaries_minute', KOGNETIKS_AI_SUMMARIES_REFRESH_WATCHDOG, array(), true );
    if ( is_wp_error( $result ) || false === $result ) {
        kognetiks_ai_summaries_refresh_record_schedule_failure( $result, 'watchdog' );
        return false;
    }
    return true;
}

/**
 * Remove worker and watchdog events after the queue is finished or paused.
 *
 * @param bool $workers_only When true, leave the watchdog in place.
 * @return bool
 */
function kognetiks_ai_summaries_refresh_clear_events( $workers_only ) {
    $worker = wp_clear_scheduled_hook( KOGNETIKS_AI_SUMMARIES_REFRESH_WORKER, array(), true );
    $ok     = ! is_wp_error( $worker ) && false !== $worker;
    if ( ! $ok ) {
        kognetiks_ai_summaries_refresh_record_schedule_failure( $worker, 'worker' );
    }
    if ( ! $workers_only ) {
        $watchdog = wp_clear_scheduled_hook( KOGNETIKS_AI_SUMMARIES_REFRESH_WATCHDOG, array(), true );
        if ( is_wp_error( $watchdog ) || false === $watchdog ) {
            kognetiks_ai_summaries_refresh_record_schedule_failure( $watchdog, 'watchdog' );
            $ok = false;
        }
    }
    return $ok;
}

/**
 * Counts safe to show on the Tools tab.
 *
 * The stored ID list is not included.
 *
 * @return array{status: string, total: int, success: int, skipped: int, failed: int, remaining: int, reason: string}
 */
function kognetiks_ai_summaries_refresh_public_state() {
    $run = kognetiks_ai_summaries_refresh_read_run_state();
    if ( is_wp_error( $run ) ) {
        return kognetiks_ai_summaries_refresh_with_schedule_error(
            array(
                'status'    => 'unknown',
                'total'     => 0,
                'success'   => 0,
                'skipped'   => 0,
                'failed'    => 0,
                'remaining' => 0,
                'reason'    => '',
            )
        );
    }
    if ( is_array( $run ) && in_array( ( $run['status'] ?? '' ), array( 'running', 'paused' ), true ) ) {
        $remaining = ( isset( $run['ids'] ) && is_array( $run['ids'] ) ) ? count( $run['ids'] ) : 0;
        return kognetiks_ai_summaries_refresh_with_schedule_error(
            array(
                'status'    => (string) $run['status'],
                'total'     => (int) ( $run['total'] ?? 0 ),
                'success'   => (int) ( $run['success'] ?? 0 ),
                'skipped'   => (int) ( $run['skipped'] ?? 0 ),
                'failed'    => (int) ( $run['failed'] ?? 0 ),
                'remaining' => $remaining,
                'reason'    => isset( $run['pause_reason'] ) ? (string) $run['pause_reason'] : '',
            )
        );
    }

    $result = kognetiks_ai_summaries_refresh_read_stored_option( 'kognetiks_ai_summaries_refresh_result' );
    if ( is_array( $result ) && 'complete' === ( $result['status'] ?? '' ) ) {
        return kognetiks_ai_summaries_refresh_with_schedule_error(
            array(
                'status'    => 'complete',
                'total'     => (int) ( $result['total'] ?? 0 ),
                'success'   => (int) ( $result['success'] ?? 0 ),
                'skipped'   => (int) ( $result['skipped'] ?? 0 ),
                'failed'    => (int) ( $result['failed'] ?? 0 ),
                'remaining' => 0,
                'reason'    => '',
            )
        );
    }

    return kognetiks_ai_summaries_refresh_with_schedule_error(
        array(
            'status'    => 'idle',
            'total'     => 0,
            'success'   => 0,
            'skipped'   => 0,
            'failed'    => 0,
            'remaining' => 0,
            'reason'    => '',
        )
    );
}

/**
 * Attach the stored scheduling failure, if one exists.
 *
 * @param array $state Public state.
 * @return array
 */
function kognetiks_ai_summaries_refresh_with_schedule_error( $state ) {
    $error = kognetiks_ai_summaries_refresh_read_stored_option( 'kognetiks_ai_summaries_refresh_schedule_error' );
    $state['schedule_error'] = ( is_array( $error ) && ! empty( $error['message'] ) ) ? (string) $error['message'] : '';
    return $state;
}

/**
 * Visible status line for the Tools tab.
 *
 * @param array $state Public state.
 * @return string
 */
function kognetiks_ai_summaries_refresh_status_text( $state ) {
    $status    = isset( $state['status'] ) ? (string) $state['status'] : 'idle';
    $total     = (int) ( $state['total'] ?? 0 );
    $success   = (int) ( $state['success'] ?? 0 );
    $skipped   = (int) ( $state['skipped'] ?? 0 );
    $failed    = (int) ( $state['failed'] ?? 0 );
    $remaining = (int) ( $state['remaining'] ?? 0 );
    if ( ! empty( $state['start_error'] ) ) {
        return (string) $state['start_error'];
    }
    $counts    = sprintf( 'Total: %1$d, successful: %2$d, skipped: %3$d, failed: %4$d, remaining: %5$d.', $total, $success, $skipped, $failed, $remaining );
    $error     = isset( $state['schedule_error'] ) ? (string) $state['schedule_error'] : '';
    $suffix    = ( '' !== $error ) ? ' Scheduling failed: ' . $error : '';
    if ( 'running' === $status ) {
        return 'Refresh running. ' . $counts . $suffix;
    }
    if ( 'paused' === $status ) {
        $reason = isset( $state['reason'] ) ? (string) $state['reason'] : 'administrator action is required';
        return 'Refresh paused: ' . $reason . ' ' . $counts . $suffix;
    }
    if ( 'complete' === $status && $total > 0 ) {
        return 'Refresh completed. ' . $counts . $suffix;
    }
    return '';
}

/**
 * Post types currently enabled for summaries on this site.
 *
 * Uses the saved settings map. A missing map uses the plugin default, which
 * enables posts and pages only.
 *
 * @return string[]
 */
function kognetiks_ai_summaries_refresh_enabled_post_types() {
    $enabled = get_option( 'kognetiks_ai_summaries_enabled_post_types', null );
    if ( null === $enabled || ! is_array( $enabled ) ) {
        $enabled = function_exists( 'kognetiks_ai_summaries_default_enabled_post_types' )
            ? kognetiks_ai_summaries_default_enabled_post_types()
            : array( 'post' => 1, 'page' => 1 );
    }
    $types = array();
    foreach ( $enabled as $type => $on ) {
        $type = sanitize_key( (string) $type );
        if ( '' !== $type && 1 === (int) $on ) {
            $types[] = $type;
        }
    }
    return array_values( array_unique( $types ) );
}

/**
 * Read whether a summary row exists without treating a failed SELECT as absence.
 *
 * @param int $post_id Post ID.
 * @return string|WP_Error present, absent, or WP_Error when the SELECT fails.
 */
function kognetiks_ai_summaries_refresh_summary_row_state( $post_id ) {
    global $wpdb;

    $post_id = (int) $post_id;
    if ( $post_id < 1 ) {
        return 'absent';
    }
    $table_name      = $wpdb->prefix . 'kognetiks_ai_summaries';
    $validated_table = kognetiks_ai_summaries_validate_table_name( $table_name );
    if ( false === $validated_table ) {
        return new WP_Error( 'refresh_summary_read_failed', 'The summary row could not be read.' );
    }
    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is whitelist-validated above.
    $found = $wpdb->query( $wpdb->prepare( 'SELECT post_id FROM `' . esc_sql( $validated_table ) . '` WHERE post_id = %d LIMIT 1', $post_id ) );
    if ( false === $found || '' !== (string) $wpdb->last_error || ! ( $wpdb->dbh instanceof mysqli ) ) {
        return new WP_Error( 'refresh_summary_read_failed', 'The summary row could not be read.' );
    }
    if ( (int) $found < 1 || empty( $wpdb->last_result[0] ) || ! isset( $wpdb->last_result[0]->post_id ) ) {
        return 'absent';
    }
    if ( (int) $wpdb->last_result[0]->post_id !== $post_id ) {
        return 'absent';
    }
    return 'present';
}

/**
 * Whether a post may be regenerated by Refresh All.
 *
 * The post must be published, its type must be enabled, and this site must
 * already have a summary row for it. A failed summary SELECT is not absence.
 *
 * @param int $post_id Post ID.
 * @return bool|WP_Error True when eligible, false when confirmed ineligible, or WP_Error when the summary row cannot be read.
 */
function kognetiks_ai_summaries_refresh_post_is_eligible( $post_id ) {
    $post_id = (int) $post_id;
    if ( $post_id < 1 ) {
        return false;
    }
    $post = get_post( $post_id );
    if ( ! $post || 'publish' !== $post->post_status ) {
        return false;
    }
    if ( ! in_array( (string) $post->post_type, kognetiks_ai_summaries_refresh_enabled_post_types(), true ) ) {
        return false;
    }
    $summary = kognetiks_ai_summaries_refresh_summary_row_state( $post_id );
    if ( is_wp_error( $summary ) ) {
        return $summary;
    }
    return 'present' === $summary;
}

/**
 * Post IDs that already have a stored summary and are eligible to refresh.
 *
 * Captured once at the start of a run. The list is published posts of enabled
 * types, ordered by post ID. The filter may remove IDs. It cannot add an ID
 * the query rejected.
 *
 * @return int[]
 */
function kognetiks_ai_summaries_refresh_collect_post_ids() {
    global $wpdb;

    $table_name      = $wpdb->prefix . 'kognetiks_ai_summaries';
    $validated_table = kognetiks_ai_summaries_validate_table_name( $table_name );
    if ( false === $validated_table ) {
        return array();
    }
    $types = kognetiks_ai_summaries_refresh_enabled_post_types();
    if ( empty( $types ) ) {
        $post_ids = array();
    } else {
        $placeholders = implode( ', ', array_fill( 0, count( $types ), '%s' ) );
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is whitelist-validated; placeholders match the type list.
        $sql      = 'SELECT DISTINCT s.post_id FROM `' . esc_sql( $validated_table ) . '` s INNER JOIN ' . $wpdb->posts . ' p ON p.ID = s.post_id WHERE p.post_status = %s AND p.post_type IN (' . $placeholders . ') ORDER BY s.post_id ASC';
        $prepared = $wpdb->prepare( $sql, array_merge( array( 'publish' ), $types ) );
        $post_ids = $wpdb->get_col( $prepared );
        if ( ! is_array( $post_ids ) ) {
            $post_ids = array();
        }
    }
    $post_ids = array_values( array_unique( array_filter( array_map( 'intval', $post_ids ) ) ) );
    $allowed  = array_fill_keys( $post_ids, true );

    /**
     * Filters the fixed ID list captured when a refresh run starts.
     *
     * IDs that are not in the eligible set are discarded.
     *
     * @param int[] $post_ids Eligible post IDs, in ascending post ID order.
     */
    $filtered = apply_filters( 'kognetiks_ai_summaries_refresh_post_ids', $post_ids );
    if ( ! is_array( $filtered ) ) {
        return array();
    }
    $kept = array();
    $seen = array();
    foreach ( $filtered as $post_id ) {
        $post_id = (int) $post_id;
        if ( $post_id < 1 || isset( $seen[ $post_id ] ) || ! isset( $allowed[ $post_id ] ) ) {
            continue;
        }
        $seen[ $post_id ] = true;
        $kept[]           = $post_id;
    }
    return $kept;
}

/**
 * Store a finished run and remove the active queue.
 *
 * The result option remains so the Tools page can show the outcome.
 * A token deletes only the lock this request still owns.
 *
 * @param array  $run   Active run state.
 * @param string $token Owner token, or an empty string when no worker is active.
 * @return bool False when the token no longer owns the lock.
 */
function kognetiks_ai_summaries_refresh_complete_run( $run, $token = '' ) {
    $token = (string) $token;
    if ( '' !== $token && ! kognetiks_ai_summaries_refresh_lock_token_is( $token ) ) {
        return false;
    }

    update_option(
        'kognetiks_ai_summaries_refresh_result',
        array(
            'status'   => 'complete',
            'total'    => (int) ( $run['total'] ?? 0 ),
            'success'  => (int) ( $run['success'] ?? 0 ),
            'skipped'  => (int) ( $run['skipped'] ?? 0 ),
            'failed'   => (int) ( $run['failed'] ?? 0 ),
            'finished' => time(),
        ),
        false
    );
    delete_option( 'kognetiks_ai_summaries_refresh_run' );
    delete_option( 'kognetiks_ai_summaries_refresh_schedule_error' );
    delete_option( 'kognetiks_ai_summaries_refresh_transaction_error' );
    if ( '' !== $token ) {
        kognetiks_ai_summaries_refresh_release_lock( $token );
    } else {
        kognetiks_ai_summaries_refresh_delete_unowned_lock();
    }

    $left = kognetiks_ai_summaries_refresh_read_run_state();
    if ( is_wp_error( $left ) ) {
        return true;
    }
    if ( ! is_array( $left ) || ! in_array( ( $left['status'] ?? '' ), array( 'running', 'paused' ), true ) ) {
        kognetiks_ai_summaries_refresh_clear_events( false );
        kognetiks_ai_summaries_refresh_mark_closed();
    }
    return true;
}

/**
 * Tell the Tools screen that this start attempt changed nothing.
 *
 * @param string $message Administrator-facing reason.
 * @return array
 */
function kognetiks_ai_summaries_refresh_start_rejected( $message ) {
    $state                    = kognetiks_ai_summaries_refresh_public_state();
    $state['already_running'] = false;
    $state['start_error']     = (string) $message;
    return $state;
}

/**
 * Insert a new run without replacing a row that already exists.
 *
 * @param array $run Run state.
 * @return true|WP_Error
 */
function kognetiks_ai_summaries_refresh_insert_run( $run ) {
    global $wpdb;

    $serialized = maybe_serialize( $run );
    $suppressed = $wpdb->suppress_errors( true );
    $inserted   = $wpdb->insert(
        $wpdb->options,
        array(
            'option_name'  => 'kognetiks_ai_summaries_refresh_run',
            'option_value' => $serialized,
            'autoload'     => 'off',
        ),
        array( '%s', '%s', '%s' )
    );
    $error = (string) $wpdb->last_error;
    $errno = ( $wpdb->dbh instanceof mysqli ) ? (int) $wpdb->dbh->errno : 0;
    $wpdb->suppress_errors( $suppressed );
    if ( 1 === (int) $inserted ) {
        kognetiks_ai_summaries_refresh_sync_option_cache( $run );
        return true;
    }
    if ( 1062 === $errno || false !== stripos( $error, 'Duplicate' ) ) {
        return new WP_Error( 'refresh_run_exists', 'A refresh queue already exists.' );
    }
    return new WP_Error( 'refresh_run_insert_failed', 'The refresh queue could not be saved.' );
}

/**
 * Start a refresh, or return the run that is already in progress.
 *
 * Does not generate summaries. The ID list is fixed here and is not rebuilt
 * on later batches. A finished run is stored in the result option, so a
 * missing run row is the only state that may create a new queue.
 *
 * @return array
 */
function kognetiks_ai_summaries_refresh_start() {
    $existing = kognetiks_ai_summaries_refresh_read_run_state();
    if ( is_wp_error( $existing ) ) {
        return kognetiks_ai_summaries_refresh_start_rejected( 'The refresh queue could not be read. Try again. The existing queue was left unchanged.' );
    }
    if ( is_array( $existing ) && in_array( ( $existing['status'] ?? '' ), array( 'running', 'paused' ), true ) ) {
        $state                    = kognetiks_ai_summaries_refresh_public_state();
        $state['already_running'] = true;
        kognetiks_ai_summaries_refresh_mark_open();
        if ( 'running' === ( $existing['status'] ?? '' ) ) {
            kognetiks_ai_summaries_refresh_ensure_watchdog();
            kognetiks_ai_summaries_refresh_schedule_worker( time() + 1, true );
        }
        return $state;
    }
    if ( false !== $existing ) {
        $state                    = kognetiks_ai_summaries_refresh_public_state();
        $state['already_running'] = true;
        return $state;
    }
    delete_transient( 'kognetiks_ai_summaries_refresh_remaining' );

    $post_ids = kognetiks_ai_summaries_refresh_collect_post_ids();
    if ( empty( $post_ids ) ) {
        kognetiks_ai_summaries_refresh_complete_run(
            array(
                'total'   => 0,
                'success' => 0,
                'skipped' => 0,
                'failed'  => 0,
            )
        );
        $state                    = kognetiks_ai_summaries_refresh_public_state();
        $state['already_running'] = false;
        return $state;
    }

    $run = array(
        'status'  => 'running',
        'ids'     => $post_ids,
        'total'   => count( $post_ids ),
        'success'        => 0,
        'skipped'        => 0,
        'failed'         => 0,
        'started'        => time(),
        'head_attempts'  => 0,
        'head_invalid'   => 0,
        'next_attempt_at'=> 0,
        'pause_reason'   => '',
    );
    $inserted = kognetiks_ai_summaries_refresh_insert_run( $run );
    if ( is_wp_error( $inserted ) ) {
        if ( 'refresh_run_exists' === $inserted->get_error_code() ) {
            $state                    = kognetiks_ai_summaries_refresh_public_state();
            $state['already_running'] = true;
            return $state;
        }
        return kognetiks_ai_summaries_refresh_start_rejected( 'The refresh queue could not be saved. Try again. No new queue was started.' );
    }
    kognetiks_ai_summaries_refresh_mark_open();

    delete_option( 'kognetiks_ai_summaries_refresh_result' );
    kognetiks_ai_summaries_refresh_delete_unowned_lock();
    kognetiks_ai_summaries_refresh_ensure_watchdog();
    kognetiks_ai_summaries_refresh_schedule_worker( time() + 1, true );
    $state                    = kognetiks_ai_summaries_refresh_public_state();
    $state['already_running'] = false;
    return $state;
}

/**
 * Whether the database lock is a usable claim with time still left.
 *
 * Anything else, including a missing token, a missing expiration, an empty
 * array, a scalar, or corrupted data, can be reclaimed.
 *
 * @param mixed $lock Unserialized lock option.
 * @return bool
 */
function kognetiks_ai_summaries_refresh_lock_is_held( $lock ) {
    if ( ! is_array( $lock ) || ! isset( $lock['token'], $lock['expires'] ) ) {
        return false;
    }
    if ( ! is_string( $lock['token'] ) || '' === $lock['token'] ) {
        return false;
    }
    $expires = $lock['expires'];
    if ( ! is_int( $expires ) && ! ( is_string( $expires ) && is_numeric( $expires ) ) ) {
        return false;
    }
    return (int) $expires >= time();
}

/**
 * Read the lock row from the current site options table.
 *
 * @return object|null Row with option_value, or null when the row is absent.
 */
function kognetiks_ai_summaries_refresh_read_lock_row() {
    global $wpdb;

    $row = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s LIMIT 1",
            'kognetiks_ai_summaries_refresh_lock'
        )
    );
    if ( ! is_object( $row ) ) {
        return null;
    }
    return $row;
}

/**
 * Drop cached copies of the lock after the database row changes.
 */
function kognetiks_ai_summaries_refresh_forget_lock_cache() {
    wp_cache_delete( 'kognetiks_ai_summaries_refresh_lock', 'options' );
    wp_cache_delete( 'alloptions', 'options' );
    wp_cache_delete( 'notoptions', 'options' );
}

/**
 * Delete the lock only when the database still holds the inspected value.
 *
 * @param string|null $raw Exact option_value, or null for an SQL NULL.
 * @return bool True when this call deleted the row.
 */
function kognetiks_ai_summaries_refresh_delete_lock_raw( $raw ) {
    global $wpdb;

    if ( null === $raw ) {
        $deleted = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name = %s AND option_value IS NULL",
                'kognetiks_ai_summaries_refresh_lock'
            )
        );
    } else {
        $deleted = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name = %s AND option_value = %s",
                'kognetiks_ai_summaries_refresh_lock',
                (string) $raw
            )
        );
    }
    if ( 1 !== (int) $deleted ) {
        return false;
    }
    kognetiks_ai_summaries_refresh_forget_lock_cache();
    return true;
}

/**
 * Insert a lock without replacing a row that already exists.
 *
 * @param array $payload Token and expiration.
 * @return bool
 */
function kognetiks_ai_summaries_refresh_insert_lock( $payload ) {
    global $wpdb;

    $serialized = maybe_serialize( $payload );
    $suppressed = $wpdb->suppress_errors( true );
    $inserted   = $wpdb->insert(
        $wpdb->options,
        array(
            'option_name'  => 'kognetiks_ai_summaries_refresh_lock',
            'option_value' => $serialized,
            'autoload'     => 'off',
        ),
        array( '%s', '%s', '%s' )
    );
    $wpdb->suppress_errors( $suppressed );
    if ( 1 !== (int) $inserted ) {
        kognetiks_ai_summaries_refresh_forget_lock_cache();
        return false;
    }

    wp_cache_set( 'kognetiks_ai_summaries_refresh_lock', $serialized, 'options' );
    wp_cache_delete( 'alloptions', 'options' );
    $notoptions = wp_cache_get( 'notoptions', 'options' );
    if ( is_array( $notoptions ) && isset( $notoptions['kognetiks_ai_summaries_refresh_lock'] ) ) {
        unset( $notoptions['kognetiks_ai_summaries_refresh_lock'] );
        wp_cache_set( 'notoptions', $notoptions, 'options' );
    }
    return true;
}

/**
 * Replace one inspected lock value with a new claim.
 *
 * The delete matches the exact stored bytes. A lock written by another
 * worker after that inspection is left in place.
 *
 * @param string|null $raw     Inspected option_value.
 * @param array       $payload Replacement lock.
 * @return bool
 */
function kognetiks_ai_summaries_refresh_reclaim_lock( $raw, $payload ) {
    $lock = null === $raw ? null : maybe_unserialize( $raw );
    if ( kognetiks_ai_summaries_refresh_lock_is_held( $lock ) ) {
        return false;
    }

    /**
     * The exact bytes that may be deleted.
     *
     * Tests use this to change the row after it has been inspected.
     *
     * @param string|null $raw Inspected option_value.
     */
    $raw = apply_filters( 'kognetiks_ai_summaries_refresh_lock_reclaim_raw', $raw );
    if ( null !== $raw && ! is_string( $raw ) ) {
        return false;
    }
    if ( ! kognetiks_ai_summaries_refresh_delete_lock_raw( $raw ) ) {
        return false;
    }
    return kognetiks_ai_summaries_refresh_insert_lock( $payload );
}

/**
 * Acquire the single-batch lock.
 *
 * A held lock is left alone. An expired or malformed row is replaced only
 * when the database still contains the value this request inspected.
 *
 * @return string|false Lock token, or false when another batch is running.
 */
function kognetiks_ai_summaries_refresh_acquire_lock() {
    $token   = 'kas1_' . wp_generate_password( 20, false );
    $payload = array(
        'token'   => $token,
        'expires' => time() + kognetiks_ai_summaries_refresh_lock_ttl(),
    );
    $row     = kognetiks_ai_summaries_refresh_read_lock_row();
    if ( null === $row ) {
        if ( kognetiks_ai_summaries_refresh_insert_lock( $payload ) ) {
            return $token;
        }
        return false;
    }
    if ( kognetiks_ai_summaries_refresh_reclaim_lock( $row->option_value, $payload ) ) {
        return $token;
    }
    return false;
}

/**
 * Release a lock only when this request still owns the stored bytes.
 *
 * @param string $token Lock token returned by the acquire function.
 */
function kognetiks_ai_summaries_refresh_release_lock( $token ) {
    $row = kognetiks_ai_summaries_refresh_read_lock_row();
    if ( null === $row ) {
        return;
    }
    $existing = maybe_unserialize( $row->option_value );
    if ( ! is_array( $existing ) || ! isset( $existing['token'] ) || ! is_string( $existing['token'] ) || '' === $existing['token'] ) {
        return;
    }
    if ( ! hash_equals( $existing['token'], (string) $token ) ) {
        return;
    }
    kognetiks_ai_summaries_refresh_delete_lock_raw( $row->option_value );
}

/**
 * Whether the current site lock row still belongs to this token.
 *
 * @param string $token Lock token.
 * @return bool
 */
function kognetiks_ai_summaries_refresh_lock_token_is( $token ) {
    $token = (string) $token;
    if ( '' === $token ) {
        return false;
    }
    $row = kognetiks_ai_summaries_refresh_read_lock_row();
    if ( null === $row ) {
        return false;
    }
    $lock = null === $row->option_value ? null : maybe_unserialize( $row->option_value );
    return is_array( $lock )
        && isset( $lock['token'] )
        && is_string( $lock['token'] )
        && hash_equals( $lock['token'], $token );
}

/**
 * Remove a lock that no worker currently holds.
 *
 * A held lock is left for its owner. Expired and malformed rows are removed
 * only when the stored bytes still match the row just read.
 */
function kognetiks_ai_summaries_refresh_delete_unowned_lock() {
    $row = kognetiks_ai_summaries_refresh_read_lock_row();
    if ( null === $row ) {
        return;
    }
    $lock = null === $row->option_value ? null : maybe_unserialize( $row->option_value );
    if ( kognetiks_ai_summaries_refresh_lock_is_held( $lock ) ) {
        return;
    }
    kognetiks_ai_summaries_refresh_delete_lock_raw( $row->option_value );
}

/**
 * Read one refresh option from the database, ignoring the object cache.
 *
 * A database failure is returned as false so existing callers keep their
 * previous behavior. Use the run-state reader when absence and failure
 * must be told apart.
 *
 * @param string $name Option name.
 * @return mixed Stored value, or false when the row is absent or the read fails.
 */
function kognetiks_ai_summaries_refresh_read_stored_option( $name ) {
    $value = kognetiks_ai_summaries_refresh_read_option_state( $name );
    if ( is_wp_error( $value ) ) {
        return false;
    }
    return $value;
}

/**
 * Read the refresh run and distinguish absence from a failed read.
 *
 * @return array|false|WP_Error Run array, false when the row is confirmed absent, or WP_Error.
 */
function kognetiks_ai_summaries_refresh_read_run_state() {
    return kognetiks_ai_summaries_refresh_read_option_state( 'kognetiks_ai_summaries_refresh_run' );
}

/**
 * Read one allowed refresh option directly from the current site table.
 *
 * @param string $name Option name.
 * @return mixed|false|WP_Error Stored value, false when confirmed absent, or WP_Error.
 */
function kognetiks_ai_summaries_refresh_read_option_state( $name ) {
    global $wpdb;

    $allowed = array(
        'kognetiks_ai_summaries_refresh_run',
        'kognetiks_ai_summaries_refresh_lock',
        'kognetiks_ai_summaries_refresh_result',
        'kognetiks_ai_summaries_refresh_schedule_error',
        'kognetiks_ai_summaries_refresh_transaction_error',
    );
    if ( ! in_array( $name, $allowed, true ) ) {
        return false;
    }

    $found = $wpdb->query(
        $wpdb->prepare(
            "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s LIMIT 1",
            $name
        )
    );
    if ( false === $found || '' !== (string) $wpdb->last_error || ! ( $wpdb->dbh instanceof mysqli ) ) {
        return new WP_Error( 'refresh_read_failed', 'The refresh option could not be read.' );
    }
    if ( (int) $found < 1 || empty( $wpdb->last_result[0] ) ) {
        return false;
    }
    $raw = isset( $wpdb->last_result[0]->option_value ) ? $wpdb->last_result[0]->option_value : null;
    if ( null === $raw || '' === $raw ) {
        return false;
    }
    return maybe_unserialize( $raw );
}

/**
 * Fields that decide which post is current and how many attempts it has used.
 *
 * @param array $run Run state.
 * @return array
 */
function kognetiks_ai_summaries_refresh_run_signature( $run ) {
    $ids = array();
    if ( isset( $run['ids'] ) && is_array( $run['ids'] ) ) {
        foreach ( $run['ids'] as $id ) {
            $ids[] = (int) $id;
        }
    }
    return array(
        'status'          => (string) ( $run['status'] ?? '' ),
        'ids'             => $ids,
        'total'           => (int) ( $run['total'] ?? 0 ),
        'success'         => (int) ( $run['success'] ?? 0 ),
        'skipped'         => (int) ( $run['skipped'] ?? 0 ),
        'failed'          => (int) ( $run['failed'] ?? 0 ),
        'head_attempts'   => (int) ( $run['head_attempts'] ?? 0 ),
        'head_invalid'    => (int) ( $run['head_invalid'] ?? 0 ),
        'next_attempt_at' => (int) ( $run['next_attempt_at'] ?? 0 ),
        'pause_reason'    => (string) ( $run['pause_reason'] ?? '' ),
    );
}

/**
 * Whether a proposed queue still belongs to the committed head.
 *
 * The proposed list may repeat the committed list or drop its first ID.
 * It cannot put a completed ID back at the head.
 *
 * @param array $stored          Committed run.
 * @param array $proposed        Run about to be stored.
 * @param int   $summary_post_id Summary target, or 0.
 * @return bool
 */
function kognetiks_ai_summaries_refresh_queue_write_is_current( $stored, $proposed, $summary_post_id ) {
    $stored_ids   = kognetiks_ai_summaries_refresh_run_signature( $stored )['ids'];
    $proposed_ids = kognetiks_ai_summaries_refresh_run_signature( $proposed )['ids'];
    $same         = $proposed_ids === $stored_ids;
    $advanced     = $proposed_ids === array_slice( $stored_ids, 1 );
    if ( $summary_post_id > 0 ) {
        if ( empty( $stored_ids ) || (int) $stored_ids[0] !== (int) $summary_post_id ) {
            return false;
        }
        return $same || $advanced;
    }
    return $same || $advanced;
}

/**
 * Publish a committed run into the option cache.
 *
 * Direct SQL does not update the cache. Call this only after COMMIT.
 *
 * @param array $run Committed run state.
 */
function kognetiks_ai_summaries_refresh_sync_option_cache( $run ) {
    wp_cache_set( 'kognetiks_ai_summaries_refresh_run', $run, 'options' );
    wp_cache_delete( 'kognetiks_ai_summaries_refresh_lock', 'options' );
    wp_cache_delete( 'kognetiks_ai_summaries_refresh_result', 'options' );
    wp_cache_delete( 'alloptions', 'options' );
    wp_cache_delete( 'notoptions', 'options' );
}

/**
 * Store a transaction failure after the connection is usable again.
 *
 * @param string $phase start, commit, or rollback.
 * @param string $error Database error text.
 */
function kognetiks_ai_summaries_refresh_record_transaction_failure( $phase, $error ) {
    update_option(
        'kognetiks_ai_summaries_refresh_transaction_error',
        array(
            'phase' => (string) $phase,
            'error' => (string) $error,
            'time'  => time(),
        ),
        false
    );
}

/**
 * Whether the global WordPress database handle is gone.
 *
 * @return bool
 */
function kognetiks_ai_summaries_refresh_connection_is_lost() {
    global $wpdb;

    if ( empty( $wpdb->dbh ) || ! ( $wpdb->dbh instanceof mysqli ) ) {
        return true;
    }
    $errno = mysqli_errno( $wpdb->dbh );
    if ( in_array( (int) $errno, array( 2006, 2013 ), true ) ) {
        return true;
    }
    $error = (string) $wpdb->last_error;
    return false !== stripos( $error, 'server has gone away' ) || false !== stripos( $error, 'Lost connection' );
}

/**
 * Open the global WordPress connection when it has been closed.
 *
 * @return bool
 */
function kognetiks_ai_summaries_refresh_reconnect_database() {
    global $wpdb;

    if ( $wpdb->dbh instanceof mysqli ) {
        return true;
    }
    return (bool) $wpdb->db_connect( false );
}

/**
 * Roll the open transaction back once.
 *
 * A failed rollback is recorded. The connection is then reopened so the
 * failed statement does not stay attached to a later request.
 *
 * @return bool
 */
function kognetiks_ai_summaries_refresh_rollback_transaction() {
    global $wpdb;

    if ( kognetiks_ai_summaries_refresh_connection_is_lost() ) {
        kognetiks_ai_summaries_refresh_reconnect_database();
        kognetiks_ai_summaries_refresh_record_transaction_failure( 'rollback', 'The database connection was lost before rollback.' );
        return false;
    }

    $result = $wpdb->query( 'ROLLBACK' );
    if ( false === $result ) {
        $error = (string) $wpdb->last_error;
        if ( '' === $error ) {
            $error = 'ROLLBACK failed.';
        }
        $wpdb->close();
        kognetiks_ai_summaries_refresh_reconnect_database();
        kognetiks_ai_summaries_refresh_record_transaction_failure( 'rollback', $error );
        return false;
    }
    return true;
}

/**
 * Reread the committed queue and summary from the database.
 *
 * Used only after COMMIT cannot be confirmed. It does not write again.
 *
 * @param array       $run             Run that was sent to the database.
 * @param int         $summary_post_id Summary target, or 0.
 * @param string|null $summary         Summary text, when one was written.
 * @return bool
 */
function kognetiks_ai_summaries_refresh_commit_landed( $run, $summary_post_id, $summary ) {
    global $wpdb;

    if ( ! kognetiks_ai_summaries_refresh_reconnect_database() ) {
        return false;
    }

    $stored = kognetiks_ai_summaries_refresh_read_stored_option( 'kognetiks_ai_summaries_refresh_run' );
    if ( '' !== (string) $wpdb->last_error || ! is_array( $stored ) ) {
        return false;
    }
    if ( kognetiks_ai_summaries_refresh_run_signature( $stored ) !== kognetiks_ai_summaries_refresh_run_signature( $run ) ) {
        return false;
    }
    if ( $summary_post_id > 0 && is_string( $summary ) && kognetiks_ai_summaries_validate_ai_summary( $summary ) ) {
        $table          = $wpdb->prefix . 'kognetiks_ai_summaries';
        $stored_summary = $wpdb->get_var(
            $wpdb->prepare(
                'SELECT ai_summary FROM %i WHERE post_id = %d',
                $table,
                (int) $summary_post_id
            )
        );
        if ( '' !== (string) $wpdb->last_error || (string) $stored_summary !== $summary ) {
            return false;
        }
    }
    return true;
}

/**
 * Publish the run cache after a confirmed commit.
 *
 * @param array $run             Committed run.
 * @param int   $summary_post_id Post whose cache should be cleared, or 0.
 */
function kognetiks_ai_summaries_refresh_finish_commit( $run, $summary_post_id ) {
    delete_option( 'kognetiks_ai_summaries_refresh_transaction_error' );
    if ( ! apply_filters( 'kognetiks_ai_summaries_refresh_skip_cache_sync', false ) ) {
        kognetiks_ai_summaries_refresh_sync_option_cache( $run );
    }
    if ( $summary_post_id > 0 ) {
        clean_post_cache( $summary_post_id );
    }
}

/**
 * Write the run, and optionally one summary, only while this token owns the lock.
 *
 * The lock row is read with FOR UPDATE before any summary or queue write.
 * Both writes stay in that transaction, so an expired worker cannot store a
 * summary after a newer worker has replaced the lock. This requires InnoDB;
 * MyISAM would ignore the row lock. The provider request stays outside this
 * function. WordPress returns 0 for a successful COMMIT, so only false is a
 * failure.
 *
 * @param string      $token           Owner token.
 * @param array       $run             Run state to store.
 * @param int         $summary_post_id Post to update, or 0 to leave summaries untouched.
 * @param string|null $summary         Full summary text.
 * @param array|null  $read_run        Database row this worker read before changing it.
 * @return bool
 */
function kognetiks_ai_summaries_refresh_commit_locked( $token, $run, $summary_post_id = 0, $summary = null, $read_run = null ) {
    global $wpdb;

    $token           = (string) $token;
    $summary_post_id = (int) $summary_post_id;
    if ( '' === $token || ! is_array( $run ) ) {
        return false;
    }

    if ( false === $wpdb->query( 'START TRANSACTION' ) ) {
        $error = (string) $wpdb->last_error;
        kognetiks_ai_summaries_refresh_record_transaction_failure( 'start', '' !== $error ? $error : 'START TRANSACTION failed.' );
        return false;
    }

    $lock_raw = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s FOR UPDATE",
            'kognetiks_ai_summaries_refresh_lock'
        )
    );
    if ( '' !== (string) $wpdb->last_error ) {
        kognetiks_ai_summaries_refresh_rollback_transaction();
        return false;
    }
    $lock = maybe_unserialize( $lock_raw );
    $owns = is_array( $lock )
        && isset( $lock['token'], $lock['expires'] )
        && hash_equals( (string) $lock['token'], $token )
        && (int) $lock['expires'] >= time();
    if ( ! $owns ) {
        kognetiks_ai_summaries_refresh_rollback_transaction();
        return false;
    }

    $stored_raw = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s FOR UPDATE",
            'kognetiks_ai_summaries_refresh_run'
        )
    );
    if ( '' !== (string) $wpdb->last_error ) {
        kognetiks_ai_summaries_refresh_rollback_transaction();
        return false;
    }
    $stored  = maybe_unserialize( $stored_raw );
    $current = is_array( $stored )
        && ( ! is_array( $read_run ) || kognetiks_ai_summaries_refresh_run_signature( $stored ) === kognetiks_ai_summaries_refresh_run_signature( $read_run ) )
        && kognetiks_ai_summaries_refresh_queue_write_is_current( $stored, $run, $summary_post_id );
    if ( ! $current ) {
        kognetiks_ai_summaries_refresh_rollback_transaction();
        return false;
    }

    if ( $summary_post_id > 0 && is_string( $summary ) && kognetiks_ai_summaries_validate_ai_summary( $summary ) ) {
        $post = get_post( $summary_post_id );
        if ( '' !== (string) $wpdb->last_error ) {
            kognetiks_ai_summaries_refresh_rollback_transaction();
            return false;
        }
        $modified = ( $post && ! empty( $post->post_modified ) ) ? $post->post_modified : current_time( 'mysql' );
        $wrote    = kognetiks_ai_summaries_update_ai_summary( $summary_post_id, $summary, $modified );
        if ( false === $wrote || '' !== (string) $wpdb->last_error ) {
            kognetiks_ai_summaries_refresh_rollback_transaction();
            return false;
        }
    }

    $updated = $wpdb->update(
        $wpdb->options,
        array( 'option_value' => maybe_serialize( $run ) ),
        array( 'option_name' => 'kognetiks_ai_summaries_refresh_run' ),
        array( '%s' ),
        array( '%s' )
    );
    if ( false === $updated || (int) $updated < 1 || '' !== (string) $wpdb->last_error ) {
        kognetiks_ai_summaries_refresh_rollback_transaction();
        return false;
    }

    $connection = $wpdb->dbh;
    $committed  = $wpdb->query( 'COMMIT' );
    $uncertain  = kognetiks_ai_summaries_refresh_connection_is_lost() || $wpdb->dbh !== $connection;
    if ( false === $committed || $uncertain ) {
        if ( $uncertain ) {
            if ( kognetiks_ai_summaries_refresh_commit_landed( $run, $summary_post_id, $summary ) ) {
                kognetiks_ai_summaries_refresh_finish_commit( $run, $summary_post_id );
                return true;
            }
            $error = (string) $wpdb->last_error;
            kognetiks_ai_summaries_refresh_record_transaction_failure( 'commit', '' !== $error ? $error : 'COMMIT could not be confirmed.' );
            return false;
        }
        $error  = (string) $wpdb->last_error;
        $rolled = kognetiks_ai_summaries_refresh_rollback_transaction();
        if ( $rolled ) {
            kognetiks_ai_summaries_refresh_record_transaction_failure( 'commit', '' !== $error ? $error : 'COMMIT failed.' );
        }
        return false;
    }

    kognetiks_ai_summaries_refresh_finish_commit( $run, $summary_post_id );
    return true;
}

/**
 * Decide whether to advance, retry, or pause after one summary attempt.
 *
 * @param array       $run         Run state, including the attempt just started.
 * @param string      $class       Failure class, or ok.
 * @param string|null $summary     Full summary when the class is ok.
 * @param int         $retry_after Provider Retry-After seconds.
 * @return array{run: array, write_post: int, summary: string|null}
 */
function kognetiks_ai_summaries_refresh_decide( $run, $class, $summary, $retry_after ) {
    $attempts = (int) ( $run['head_attempts'] ?? 0 );
    $invalid  = (int) ( $run['head_invalid'] ?? 0 );
    $post_id  = (int) $run['ids'][0];

    if ( 'ok' === $class && is_string( $summary ) && kognetiks_ai_summaries_validate_ai_summary( $summary ) ) {
        array_shift( $run['ids'] );
        $run['success']         = (int) $run['success'] + 1;
        $run['head_attempts']   = 0;
        $run['head_invalid']    = 0;
        $run['next_attempt_at'] = 0;
        return array(
            'run'        => $run,
            'write_post' => $post_id,
            'summary'    => $summary,
        );
    }

    if ( 'skipped' === $class ) {
        array_shift( $run['ids'] );
        $run['skipped']         = (int) ( $run['skipped'] ?? 0 ) + 1;
        $run['head_attempts']   = 0;
        $run['head_invalid']    = 0;
        $run['next_attempt_at'] = 0;
        return array(
            'run'        => $run,
            'write_post' => 0,
            'summary'    => null,
        );
    }

    if ( 'credentials' === $class || 'configuration' === $class ) {
        $run['head_attempts']   = max( 0, $attempts - 1 );
        $run['status']          = 'paused';
        $run['pause_reason']    = ( 'credentials' === $class ) ? 'the API credentials were rejected' : 'the model configuration was rejected';
        $run['next_attempt_at'] = 0;
        return array(
            'run'        => $run,
            'write_post' => 0,
            'summary'    => null,
        );
    }

    if ( 'invalid_output' === $class ) {
        $invalid++;
        $run['head_invalid'] = $invalid;
        if ( $invalid < 2 && $attempts < 3 ) {
            $run['next_attempt_at'] = time() + kognetiks_ai_summaries_refresh_retry_delay( $attempts, 0 );
            return array(
                'run'        => $run,
                'write_post' => 0,
                'summary'    => null,
            );
        }
    } elseif ( 'permanent' !== $class && $attempts < 3 ) {
        $run['next_attempt_at'] = time() + kognetiks_ai_summaries_refresh_retry_delay( $attempts, $retry_after );
        return array(
            'run'        => $run,
            'write_post' => 0,
            'summary'    => null,
        );
    }

    array_shift( $run['ids'] );
    $run['failed']          = (int) $run['failed'] + 1;
    $run['head_attempts']   = 0;
    $run['head_invalid']    = 0;
    $run['next_attempt_at'] = 0;
    $run['status']          = 'running';
    return array(
        'run'        => $run,
        'write_post' => 0,
        'summary'    => null,
    );
}

/**
 * Regenerate the next summary, or recover a claim whose worker did not finish.
 *
 * One provider request at most. The successor event is scheduled before that
 * request. Closing the browser does not change this function.
 *
 * @return array
 */
function kognetiks_ai_summaries_refresh_worker() {
    $run = kognetiks_ai_summaries_refresh_read_stored_option( 'kognetiks_ai_summaries_refresh_run' );
    if ( ! is_array( $run ) || 'running' !== ( $run['status'] ?? '' ) ) {
        return kognetiks_ai_summaries_refresh_public_state();
    }
    if ( ! empty( $run['next_attempt_at'] ) && (int) $run['next_attempt_at'] > time() ) {
        kognetiks_ai_summaries_refresh_replace_worker_event( (int) $run['next_attempt_at'], false );
        return kognetiks_ai_summaries_refresh_public_state();
    }

    $token = kognetiks_ai_summaries_refresh_acquire_lock();
    if ( false === $token ) {
        $state         = kognetiks_ai_summaries_refresh_public_state();
        $state['busy'] = true;
        return $state;
    }

    $run = kognetiks_ai_summaries_refresh_read_stored_option( 'kognetiks_ai_summaries_refresh_run' );
    if ( ! is_array( $run ) || 'running' !== ( $run['status'] ?? '' ) || empty( $run['ids'] ) || ! is_array( $run['ids'] ) ) {
        if ( is_array( $run ) && 'running' === ( $run['status'] ?? '' ) ) {
            kognetiks_ai_summaries_refresh_complete_run( $run, $token );
        } else {
            kognetiks_ai_summaries_refresh_release_lock( $token );
        }
        return kognetiks_ai_summaries_refresh_public_state();
    }

    $post_id  = (int) $run['ids'][0];
    $read_run = $run;
    $eligible = kognetiks_ai_summaries_refresh_post_is_eligible( $post_id );
    if ( is_wp_error( $eligible ) ) {
        // A failed summary read is not a skip. Wait on the existing retry
        // schedule without consuming an attempt or advancing the queue.
        $deferred                     = $run;
        $deferred['next_attempt_at']  = time() + kognetiks_ai_summaries_refresh_retry_delay( 1, 0 );
        if ( ! kognetiks_ai_summaries_refresh_commit_locked( $token, $deferred, 0, null, $read_run ) ) {
            kognetiks_ai_summaries_refresh_release_lock( $token );
            return kognetiks_ai_summaries_refresh_public_state();
        }
        if ( (int) $deferred['next_attempt_at'] > time() ) {
            kognetiks_ai_summaries_refresh_replace_worker_event( (int) $deferred['next_attempt_at'], true );
        } else {
            kognetiks_ai_summaries_refresh_ensure_watchdog();
        }
        kognetiks_ai_summaries_refresh_release_lock( $token );
        return kognetiks_ai_summaries_refresh_public_state();
    }
    if ( ! $eligible ) {
        $decision = kognetiks_ai_summaries_refresh_decide( $run, 'skipped', null, 0 );
        if ( ! kognetiks_ai_summaries_refresh_commit_locked( $token, $decision['run'], 0, null, $read_run ) ) {
            kognetiks_ai_summaries_refresh_release_lock( $token );
            return kognetiks_ai_summaries_refresh_public_state();
        }
        if ( 'after_commit' === apply_filters( 'kognetiks_ai_summaries_refresh_interrupt', '' ) ) {
            return kognetiks_ai_summaries_refresh_public_state();
        }
        if ( empty( $decision['run']['ids'] ) ) {
            kognetiks_ai_summaries_refresh_complete_run( $decision['run'], $token );
            return kognetiks_ai_summaries_refresh_public_state();
        }
        $when = ! empty( $decision['run']['next_attempt_at'] ) ? (int) $decision['run']['next_attempt_at'] : time() + 1;
        kognetiks_ai_summaries_refresh_replace_worker_event( $when, true );
        kognetiks_ai_summaries_refresh_release_lock( $token );
        return kognetiks_ai_summaries_refresh_public_state();
    }

    $run['head_attempts'] = (int) ( $run['head_attempts'] ?? 0 ) + 1;
    if ( ! kognetiks_ai_summaries_refresh_commit_locked( $token, $run, 0, null, $read_run ) ) {
        kognetiks_ai_summaries_refresh_release_lock( $token );
        return kognetiks_ai_summaries_refresh_public_state();
    }

    // A recovery event must exist before the provider call. Do not remove the current event first.
    if ( ! kognetiks_ai_summaries_refresh_prepare_recovery( time() + kognetiks_ai_summaries_refresh_lock_ttl() ) ) {
        kognetiks_ai_summaries_refresh_release_lock( $token );
        return kognetiks_ai_summaries_refresh_public_state();
    }
    $interrupt = apply_filters( 'kognetiks_ai_summaries_refresh_interrupt', '' );
    if ( 'before_api' === $interrupt ) {
        return kognetiks_ai_summaries_refresh_public_state();
    }

    kognetiks_ai_summaries_refresh_set_report( 'permanent', null, 0 );
    add_filter( 'kognetiks_ai_summaries_generations_per_request', '__return_zero' );
    kognetiks_ai_summaries_generate_ai_summary( $post_id, true, true, array( 'refresh_run' => true ) );
    remove_filter( 'kognetiks_ai_summaries_generations_per_request', '__return_zero' );
    if ( 'after_api' === apply_filters( 'kognetiks_ai_summaries_refresh_interrupt', '' ) ) {
        return kognetiks_ai_summaries_refresh_public_state();
    }
    $report   = kognetiks_ai_summaries_refresh_get_report();
    $decision = kognetiks_ai_summaries_refresh_decide( $run, $report['class'], $report['summary'], $report['retry_after'] );

    if ( ! kognetiks_ai_summaries_refresh_commit_locked( $token, $decision['run'], $decision['write_post'], $decision['summary'], $run ) ) {
        kognetiks_ai_summaries_refresh_release_lock( $token );
        return kognetiks_ai_summaries_refresh_public_state();
    }
    if ( 'after_commit' === apply_filters( 'kognetiks_ai_summaries_refresh_interrupt', '' ) ) {
        return kognetiks_ai_summaries_refresh_public_state();
    }

    if ( 'paused' === ( $decision['run']['status'] ?? '' ) ) {
        kognetiks_ai_summaries_refresh_ensure_watchdog();
        kognetiks_ai_summaries_refresh_clear_events( true );
        kognetiks_ai_summaries_refresh_release_lock( $token );
        return kognetiks_ai_summaries_refresh_public_state();
    }
    if ( empty( $decision['run']['ids'] ) ) {
        kognetiks_ai_summaries_refresh_complete_run( $decision['run'], $token );
        return kognetiks_ai_summaries_refresh_public_state();
    }

    $when = ! empty( $decision['run']['next_attempt_at'] ) ? (int) $decision['run']['next_attempt_at'] : time() + 1;
    kognetiks_ai_summaries_refresh_replace_worker_event( $when, true );
    kognetiks_ai_summaries_refresh_release_lock( $token );
    return kognetiks_ai_summaries_refresh_public_state();
}
add_action( KOGNETIKS_AI_SUMMARIES_REFRESH_WORKER, 'kognetiks_ai_summaries_refresh_worker' );

/**
 * Schedule a worker when a running queue has no live lock and no waiting event.
 *
 * This does not resume a paused run. Without site traffic, WordPress will not
 * invoke the watchdog or the worker until some later request can spawn cron.
 */
function kognetiks_ai_summaries_refresh_watchdog() {
    $run = kognetiks_ai_summaries_refresh_read_run_state();
    if ( is_wp_error( $run ) ) {
        return;
    }
    if ( is_array( $run ) && 'paused' === ( $run['status'] ?? '' ) ) {
        return;
    }
    if ( ! is_array( $run ) || 'running' !== ( $run['status'] ?? '' ) ) {
        // WordPress inserts the next recurring watchdog before this callback.
        // Remove that successor only. Leave any worker event in place.
        $cleared = wp_clear_scheduled_hook( KOGNETIKS_AI_SUMMARIES_REFRESH_WATCHDOG, array(), true );
        if ( is_wp_error( $cleared ) || false === $cleared ) {
            kognetiks_ai_summaries_refresh_record_schedule_failure( $cleared, 'watchdog' );
        } else {
            kognetiks_ai_summaries_refresh_clear_schedule_failure();
        }
        $again = kognetiks_ai_summaries_refresh_read_run_state();
        if ( is_wp_error( $again ) ) {
            return;
        }
        if ( is_array( $again ) && in_array( ( $again['status'] ?? '' ), array( 'running', 'paused' ), true ) ) {
            kognetiks_ai_summaries_refresh_ensure_watchdog();
        }
        return;
    }
    $lock = kognetiks_ai_summaries_refresh_read_stored_option( 'kognetiks_ai_summaries_refresh_lock' );
    if ( kognetiks_ai_summaries_refresh_lock_is_held( $lock ) ) {
        return;
    }
    $when = time() + 1;
    if ( ! empty( $run['next_attempt_at'] ) && (int) $run['next_attempt_at'] > time() ) {
        $when = (int) $run['next_attempt_at'];
    }
    $next = wp_next_scheduled( KOGNETIKS_AI_SUMMARIES_REFRESH_WORKER );
    if ( $next ) {
        if ( (int) $next <= time() && apply_filters( 'kognetiks_ai_summaries_refresh_spawn_cron', true ) ) {
            spawn_cron();
        }
        return;
    }
    kognetiks_ai_summaries_refresh_schedule_worker( $when, false );
}
add_action( KOGNETIKS_AI_SUMMARIES_REFRESH_WATCHDOG, 'kognetiks_ai_summaries_refresh_watchdog' );

/**
 * Remember that this site has a running or paused queue.
 *
 * The flag is autoloaded, so ordinary requests can see it in the options
 * array WordPress has already loaded.
 */
function kognetiks_ai_summaries_refresh_mark_open() {
    add_option( 'kognetiks_ai_summaries_refresh_open', 1, '', true );
}

/**
 * Drop the open flag after the queue is no longer running or paused.
 */
function kognetiks_ai_summaries_refresh_mark_closed() {
    $run = kognetiks_ai_summaries_refresh_read_run_state();
    if ( is_wp_error( $run ) ) {
        return;
    }
    if ( is_array( $run ) && in_array( ( $run['status'] ?? '' ), array( 'running', 'paused' ), true ) ) {
        return;
    }
    delete_option( 'kognetiks_ai_summaries_refresh_open' );
    delete_option( 'kognetiks_ai_summaries_refresh_recover_gate' );
}

/**
 * Whether the loaded autoloaded options say a queue is open.
 *
 * This does not read the queue row.
 *
 * @return bool
 */
function kognetiks_ai_summaries_refresh_is_open() {
    $alloptions = wp_load_alloptions();
    return is_array( $alloptions ) && ! empty( $alloptions['kognetiks_ai_summaries_refresh_open'] );
}

/**
 * Store the recovery gate in the option cache after this request wrote it.
 *
 * @param string $value Gate timestamp stored in the database.
 */
function kognetiks_ai_summaries_refresh_sync_gate_cache( $value ) {
    wp_cache_set( 'kognetiks_ai_summaries_refresh_recover_gate', (string) $value, 'options' );
    wp_cache_delete( 'alloptions', 'options' );
    $notoptions = wp_cache_get( 'notoptions', 'options' );
    if ( is_array( $notoptions ) && isset( $notoptions['kognetiks_ai_summaries_refresh_recover_gate'] ) ) {
        unset( $notoptions['kognetiks_ai_summaries_refresh_recover_gate'] );
        wp_cache_set( 'notoptions', $notoptions, 'options' );
    }
}

/**
 * Claim the next recovery window for this site.
 *
 * The first claim inserts a new row. A duplicate key means another request
 * already owns the window. An expired row is replaced only when its stored
 * bytes still match the value just read.
 *
 * @return bool
 */
function kognetiks_ai_summaries_refresh_claim_recover_window() {
    global $wpdb;

    $interval = (int) apply_filters( 'kognetiks_ai_summaries_refresh_recover_interval', 60 );
    if ( $interval < 1 ) {
        return true;
    }
    $until      = (string) ( time() + $interval );
    $suppressed = $wpdb->suppress_errors( true );
    $inserted   = $wpdb->insert(
        $wpdb->options,
        array(
            'option_name'  => 'kognetiks_ai_summaries_refresh_recover_gate',
            'option_value' => $until,
            'autoload'     => 'off',
        ),
        array( '%s', '%s', '%s' )
    );
    $wpdb->suppress_errors( $suppressed );
    if ( 1 === (int) $inserted ) {
        kognetiks_ai_summaries_refresh_sync_gate_cache( $until );
        return true;
    }

    $found = $wpdb->query(
        $wpdb->prepare(
            "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s LIMIT 1",
            'kognetiks_ai_summaries_refresh_recover_gate'
        )
    );
    if ( false === $found || '' !== (string) $wpdb->last_error || ! ( $wpdb->dbh instanceof mysqli ) ) {
        return false;
    }
    if ( (int) $found < 1 || empty( $wpdb->last_result[0] ) || ! isset( $wpdb->last_result[0]->option_value ) ) {
        return false;
    }
    $raw = (string) $wpdb->last_result[0]->option_value;
    if ( '' === $raw || (int) $raw > time() ) {
        return false;
    }
    $claimed = $wpdb->query(
        $wpdb->prepare(
            "UPDATE {$wpdb->options} SET option_value = %s WHERE option_name = %s AND option_value = %s",
            $until,
            'kognetiks_ai_summaries_refresh_recover_gate',
            $raw
        )
    );
    if ( 1 !== (int) $claimed ) {
        return false;
    }
    kognetiks_ai_summaries_refresh_sync_gate_cache( $until );
    return true;
}

/**
 * Restore missing cron events for an open queue.
 *
 * Idle requests stop before any queue or cron write. This function does not
 * generate a summary and does not resume a paused run.
 */
function kognetiks_ai_summaries_refresh_recover_events() {
    if ( ! kognetiks_ai_summaries_refresh_is_open() ) {
        return;
    }
    if ( ! kognetiks_ai_summaries_refresh_claim_recover_window() ) {
        return;
    }

    $run = kognetiks_ai_summaries_refresh_read_run_state();
    if ( is_wp_error( $run ) ) {
        return;
    }
    if ( ! is_array( $run ) || ! in_array( ( $run['status'] ?? '' ), array( 'running', 'paused' ), true ) ) {
        kognetiks_ai_summaries_refresh_mark_closed();
        return;
    }
    if ( 'paused' === $run['status'] ) {
        kognetiks_ai_summaries_refresh_ensure_watchdog();
        return;
    }

    kognetiks_ai_summaries_refresh_ensure_watchdog();
    $lock = kognetiks_ai_summaries_refresh_read_stored_option( 'kognetiks_ai_summaries_refresh_lock' );
    if ( kognetiks_ai_summaries_refresh_lock_is_held( $lock ) ) {
        return;
    }
    $next = wp_next_scheduled( KOGNETIKS_AI_SUMMARIES_REFRESH_WORKER );
    if ( $next ) {
        return;
    }
    $when = time() + 1;
    if ( ! empty( $run['next_attempt_at'] ) && (int) $run['next_attempt_at'] > time() ) {
        $when = (int) $run['next_attempt_at'];
    }
    kognetiks_ai_summaries_refresh_schedule_worker( $when, false );
}
add_action( 'init', 'kognetiks_ai_summaries_refresh_recover_events', 20 );

/**
 * Continue a paused run without rebuilding the queue.
 *
 * @return array
 */
function kognetiks_ai_summaries_refresh_resume() {
    $run = kognetiks_ai_summaries_refresh_read_stored_option( 'kognetiks_ai_summaries_refresh_run' );
    if ( ! is_array( $run ) || 'paused' !== ( $run['status'] ?? '' ) ) {
        return kognetiks_ai_summaries_refresh_public_state();
    }
    $run['status']          = 'running';
    $run['pause_reason']    = '';
    $run['next_attempt_at'] = 0;
    update_option( 'kognetiks_ai_summaries_refresh_run', $run, false );
    kognetiks_ai_summaries_refresh_mark_open();
    kognetiks_ai_summaries_refresh_ensure_watchdog();
    kognetiks_ai_summaries_refresh_schedule_worker( time() + 1, true );
    return kognetiks_ai_summaries_refresh_public_state();
}

/**
 * Status only. The browser must not advance the queue.
 *
 * @return array
 */
function kognetiks_ai_summaries_refresh_continue() {
    return kognetiks_ai_summaries_refresh_public_state();
}

/**
 * Check capability and nonce, then run one refresh step.
 *
 * The client sends only the step name. Post IDs are never accepted from the request.
 *
 * @param string $step  start, status, or resume.
 * @param string $nonce AJAX nonce.
 * @return array|WP_Error
 */
function kognetiks_ai_summaries_refresh_batch_request( $step, $nonce ) {
    if ( ! current_user_can( 'manage_options' ) ) {
        return new WP_Error( 'forbidden', 'You do not have permission to refresh summaries.', array( 'status' => 403 ) );
    }
    if ( ! wp_verify_nonce( $nonce, 'kognetiks_ai_summaries_refresh_batch' ) ) {
        return new WP_Error( 'invalid_nonce', 'Security check failed.', array( 'status' => 403 ) );
    }

    $step = sanitize_key( (string) $step );
    if ( 'start' === $step ) {
        return kognetiks_ai_summaries_refresh_start();
    }
    if ( 'resume' === $step ) {
        return kognetiks_ai_summaries_refresh_resume();
    }
    if ( 'status' === $step || 'continue' === $step ) {
        return kognetiks_ai_summaries_refresh_public_state();
    }
    return new WP_Error( 'bad_step', 'Unknown refresh step.', array( 'status' => 400 ) );
}

/**
 * AJAX entry for the next refresh batch.
 */
function kognetiks_ai_summaries_ajax_refresh_batch() {
    $step  = isset( $_POST['step'] ) ? sanitize_key( wp_unslash( $_POST['step'] ) ) : '';
    $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
    $result = kognetiks_ai_summaries_refresh_batch_request( $step, $nonce );
    if ( is_wp_error( $result ) ) {
        $data   = $result->get_error_data();
        $status = ( is_array( $data ) && isset( $data['status'] ) ) ? (int) $data['status'] : 400;
        wp_send_json_error( array( 'message' => $result->get_error_message() ), $status );
    }
    wp_send_json_success( $result );
}
add_action( 'wp_ajax_kognetiks_ai_summaries_refresh_batch', 'kognetiks_ai_summaries_ajax_refresh_batch' );

/**
 * Queue a background refresh and return without generating summaries.
 *
 * @return int Always 0. Work continues through WP-Cron.
 */
function kognetiks_ai_summaries_refresh_all_summaries() {
    kognetiks_ai_summaries_refresh_start();
    return 0;
}

// Convert a string to Proper Case while preserving acronyms
function kognetiks_ai_summaries_convert_to_proper_case_with_acronyms($text) {
    
    // List of common acronyms that should be all uppercase (2-3 letters)
    $acronyms = array(
        'AI', 'API', 'URL', 'SEO', 'CSS', 'HTML', 'JS', 'PHP', 'XML', 'JSON',
        'PDF', 'CMS', 'CRM', 'ERP', 'SaaS', 'PaaS', 'IaaS', 'IoT', 'UI', 'UX',
        'ID', 'IP', 'OS', 'PC', 'TV', 'CD', 'DVD', 'USB', 'HD', 'SD', 'GB', 'MB',
        'CPU', 'GPU', 'RAM', 'SSD', 'HDD', 'WiFi', 'GPS', 'NFC', 'RFID', 'LED',
        'LCD', 'OLED', 'HDMI', 'VGA', 'DVI', 'AC', 'DC', 'HVAC',
        'CEO', 'CFO', 'CTO', 'CIO', 'HR', 'IT', 'PR', 'QA', 'QC', 'R&D',
        'FAQ', 'TOS', 'EULA', 'GDPR', 'HIPAA', 'PCI', 'SSL', 'TLS', 'HTTP', 'HTTPS',
        'FTP', 'SSH', 'DNS', 'SMTP', 'POP', 'IMAP', 'SQL', 'NoSQL', 'REST', 'SOAP',
        'AJAX', 'DOM', 'BOM', 'WYSIWYG', 'IDE', 'SDK', 'CLI', 'GUI'
    );
    
    // Convert to proper case first
    $proper_case = ucwords(strtolower($text));
    
    // Replace acronyms (case-insensitive) with their uppercase versions
    foreach ($acronyms as $acronym) {
        // Use word boundaries to match whole words only
        $pattern = '/\b' . preg_quote(strtolower($acronym), '/') . '\b/i';
        $proper_case = preg_replace($pattern, $acronym, $proper_case);
    }
    
    return $proper_case;
}

// Convert all categories and tags to Proper Case
function kognetiks_ai_summaries_convert_to_proper_case() {
    
    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_convert_to_proper_case' );
    
    $categories_updated = 0;
    $tags_updated = 0;
    
    // Get all categories
    $categories = get_terms(array(
        'taxonomy' => 'category',
        'hide_empty' => false,
    ));
    
    foreach ($categories as $category) {
        $old_name = $category->name;
        $new_name = kognetiks_ai_summaries_convert_to_proper_case_with_acronyms($old_name);
        
        // Only update if the name changed
        if ($old_name !== $new_name) {
            wp_update_term($category->term_id, 'category', array(
                'name' => $new_name
            ));
            $categories_updated++;
        }
    }
    
    // Get all tags
    $tags = get_terms(array(
        'taxonomy' => 'post_tag',
        'hide_empty' => false,
    ));
    
    foreach ($tags as $tag) {
        $old_name = $tag->name;
        $new_name = kognetiks_ai_summaries_convert_to_proper_case_with_acronyms($old_name);
        
        // Only update if the name changed
        if ($old_name !== $new_name) {
            wp_update_term($tag->term_id, 'post_tag', array(
                'name' => $new_name
            ));
            $tags_updated++;
        }
    }
    
    return array(
        'categories' => $categories_updated,
        'tags' => $tags_updated
    );
}

// Handle admin_post actions for cleanup tools
add_action('admin_post_kognetiks_ai_summaries_delete_all_summaries', 'kognetiks_ai_summaries_handle_delete_all_summaries');
add_action('admin_post_kognetiks_ai_summaries_refresh_all_summaries', 'kognetiks_ai_summaries_handle_refresh_all_summaries');
add_action('admin_post_kognetiks_ai_summaries_proper_case_categories_tags', 'kognetiks_ai_summaries_handle_proper_case_categories_tags');
add_action('admin_post_kognetiks_ai_summaries_delete_empty_categories', 'kognetiks_ai_summaries_handle_delete_empty_categories');
add_action('admin_post_kognetiks_ai_summaries_delete_empty_tags', 'kognetiks_ai_summaries_handle_delete_empty_tags');
add_action('admin_post_kognetiks_ai_summaries_delete_orphaned_summaries', 'kognetiks_ai_summaries_handle_delete_orphaned_summaries');

// Handle delete all summaries action
function kognetiks_ai_summaries_handle_delete_all_summaries() {
    
    // Check nonce and permissions
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'kognetiks_ai_summaries_cleanup_action')) {
        wp_die(esc_html__('Security check failed.', 'kognetiks-ai-summaries'));
    }
    
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have permission to perform this action.', 'kognetiks-ai-summaries'));
    }
    
    kognetiks_ai_summaries_delete_all_summaries();
    
    // Add nonce to redirect URL for security
    $redirect_url = add_query_arg(array(
        'cleanup_success' => 'delete_all',
        '_wpnonce' => wp_create_nonce('kognetiks_ai_summaries_cleanup_success')
    ), admin_url('admin.php?page=kognetiks-ai-summaries&tab=tools'));
    wp_safe_redirect($redirect_url);
    exit;
}

// Handle refresh all summaries action
function kognetiks_ai_summaries_handle_refresh_all_summaries() {
    
    // Check nonce and permissions
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'kognetiks_ai_summaries_cleanup_action')) {
        wp_die(esc_html__('Security check failed.', 'kognetiks-ai-summaries'));
    }
    
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have permission to perform this action.', 'kognetiks-ai-summaries'));
    }

    // Snapshot the ID list and schedule WP-Cron. This request does not generate summaries.
    kognetiks_ai_summaries_refresh_start();

    $redirect_url = add_query_arg(array(
        'cleanup_success' => 'refresh_all',
        '_wpnonce' => wp_create_nonce('kognetiks_ai_summaries_cleanup_success')
    ), admin_url('admin.php?page=kognetiks-ai-summaries&tab=tools'));
    wp_safe_redirect($redirect_url);
    exit;
}

// Handle proper case categories and tags action
function kognetiks_ai_summaries_handle_proper_case_categories_tags() {
    
    // Check nonce and permissions
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'kognetiks_ai_summaries_cleanup_action')) {
        wp_die(esc_html__('Security check failed.', 'kognetiks-ai-summaries'));
    }
    
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have permission to perform this action.', 'kognetiks-ai-summaries'));
    }
    
    $result = kognetiks_ai_summaries_convert_to_proper_case();
    
    // Add nonce to redirect URL for security
    $redirect_url = add_query_arg(array(
        'cleanup_success' => 'proper_case',
        'cats' => $result['categories'],
        'tags' => $result['tags'],
        '_wpnonce' => wp_create_nonce('kognetiks_ai_summaries_cleanup_success')
    ), admin_url('admin.php?page=kognetiks-ai-summaries&tab=tools'));
    
    wp_safe_redirect($redirect_url);
    exit;
}

// Delete all empty categories (count = 0)
function kognetiks_ai_summaries_delete_empty_categories() {
    
    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_delete_empty_categories' );
    
    $count = 0;
    
    // Get all categories with count = 0
    $categories = get_terms(array(
        'taxonomy' => 'category',
        'hide_empty' => true, // This will only get categories with posts
    ));
    
    // Get all categories including empty ones
    $all_categories = get_terms(array(
        'taxonomy' => 'category',
        'hide_empty' => false,
    ));
    
    // Find categories that are empty (not in the hide_empty list)
    $empty_categories = array();
    foreach ($all_categories as $category) {
        // Check if category has count of 0
        if ($category->count == 0) {
            // Skip "Uncategorized" category as it's the default
            if (strcasecmp($category->name, 'Uncategorized') !== 0) {
                $empty_categories[] = $category;
            }
        }
    }
    
    // Delete each empty category
    foreach ($empty_categories as $category) {
        $result = wp_delete_term($category->term_id, 'category');
        if (!is_wp_error($result) && $result) {
            $count++;
        }
    }
    
    return $count;
}

// Delete all empty tags (count = 0)
function kognetiks_ai_summaries_delete_empty_tags() {
    
    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_delete_empty_tags' );
    
    $count = 0;
    
    // Get all tags including empty ones
    $all_tags = get_terms(array(
        'taxonomy' => 'post_tag',
        'hide_empty' => false,
    ));
    
    // Find tags that are empty (count = 0)
    $empty_tags = array();
    foreach ($all_tags as $tag) {
        if ($tag->count == 0) {
            $empty_tags[] = $tag;
        }
    }
    
    // Delete each empty tag
    foreach ($empty_tags as $tag) {
        $result = wp_delete_term($tag->term_id, 'post_tag');
        if (!is_wp_error($result) && $result) {
            $count++;
        }
    }
    
    return $count;
}

// Handle delete empty categories action
function kognetiks_ai_summaries_handle_delete_empty_categories() {
    
    // Check nonce and permissions
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'kognetiks_ai_summaries_cleanup_action')) {
        wp_die(esc_html__('Security check failed.', 'kognetiks-ai-summaries'));
    }
    
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have permission to perform this action.', 'kognetiks-ai-summaries'));
    }
    
    $count = kognetiks_ai_summaries_delete_empty_categories();
    
    // Add nonce to redirect URL for security
    $redirect_url = add_query_arg(array(
        'cleanup_success' => 'delete_empty_categories',
        'count' => $count,
        '_wpnonce' => wp_create_nonce('kognetiks_ai_summaries_cleanup_success')
    ), admin_url('admin.php?page=kognetiks-ai-summaries&tab=tools'));
    
    wp_safe_redirect($redirect_url);
    exit;
}

// Handle delete empty tags action
function kognetiks_ai_summaries_handle_delete_empty_tags() {
    
    // Check nonce and permissions
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'kognetiks_ai_summaries_cleanup_action')) {
        wp_die(esc_html__('Security check failed.', 'kognetiks-ai-summaries'));
    }
    
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have permission to perform this action.', 'kognetiks-ai-summaries'));
    }
    
    $count = kognetiks_ai_summaries_delete_empty_tags();
    
    // Add nonce to redirect URL for security
    $redirect_url = add_query_arg(array(
        'cleanup_success' => 'delete_empty_tags',
        'count' => $count,
        '_wpnonce' => wp_create_nonce('kognetiks_ai_summaries_cleanup_success')
    ), admin_url('admin.php?page=kognetiks-ai-summaries&tab=tools'));
    
    wp_safe_redirect($redirect_url);
    exit;
}

// Delete orphaned AI summaries (summaries for posts that no longer exist)
function kognetiks_ai_summaries_delete_orphaned_summaries() {
    
    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_delete_orphaned_summaries' );
    
    global $wpdb;
    
    $count = 0;
    
    // Get all post IDs that have AI summaries
    $table_name = $wpdb->prefix . 'kognetiks_ai_summaries';
    
    // Validate table name to prevent SQL injection
    $validated_table = kognetiks_ai_summaries_validate_table_name($table_name);
    if ($validated_table === false) {
        return 0;
    }
    
    // Use esc_sql() to escape the validated table name
    $post_ids = $wpdb->get_col("SELECT post_id FROM `" . esc_sql($validated_table) . "`");
    
    // Check each post ID to see if the post still exists
    foreach ($post_ids as $post_id) {
        $post = get_post($post_id);
        
        // If post doesn't exist, delete the orphaned summary
        if (!$post) {
            kognetiks_ai_summaries_delete_ai_summary($post_id);
            
            // Clear cache for this post
            wp_cache_delete('kognetiks_ai_summaries_' . $post_id);
            
            $count++;
        }
    }
    
    // Clear all caches after cleanup
    wp_cache_flush();
    
    return $count;
}

// Handle delete orphaned summaries action
function kognetiks_ai_summaries_handle_delete_orphaned_summaries() {
    
    // Check nonce and permissions
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'kognetiks_ai_summaries_cleanup_action')) {
        wp_die(esc_html__('Security check failed.', 'kognetiks-ai-summaries'));
    }
    
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have permission to perform this action.', 'kognetiks-ai-summaries'));
    }
    
    $count = kognetiks_ai_summaries_delete_orphaned_summaries();
    
    // Add nonce to redirect URL for security
    $redirect_url = add_query_arg(array(
        'cleanup_success' => 'delete_orphaned',
        'count' => $count,
        '_wpnonce' => wp_create_nonce('kognetiks_ai_summaries_cleanup_success')
    ), admin_url('admin.php?page=kognetiks-ai-summaries&tab=tools'));
    
    wp_safe_redirect($redirect_url);
    exit;
}
