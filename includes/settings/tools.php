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
                echo '<div class="notice notice-success is-dismissible"><p>All AI summaries are being refreshed. This may take some time.</p></div>';
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
                    <p>
                        <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=kognetiks_ai_summaries_refresh_all_summaries'), 'kognetiks_ai_summaries_cleanup_action')); ?>" 
                           class="button button-secondary" 
                           onclick="return confirm('Are you sure you want to refresh ALL AI summaries? This may take a long time.');">
                            Refresh All Summaries
                        </a>
                    </p>
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

// Refresh all AI summaries by regenerating them
function kognetiks_ai_summaries_refresh_all_summaries() {
    
    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_refresh_all_summaries' );

    // Bypass per-request generation limit for bulk refresh
    add_filter( 'kognetiks_ai_summaries_generations_per_request', '__return_zero' );
    
    global $wpdb;
    
    // Get all post IDs that have AI summaries
    $table_name = $wpdb->prefix . 'kognetiks_ai_summaries';
    
    // Validate table name to prevent SQL injection
    $validated_table = kognetiks_ai_summaries_validate_table_name($table_name);
    if ($validated_table === false) {
        return 0;
    }
    
    // Use esc_sql() to escape the validated table name
    $post_ids = $wpdb->get_col("SELECT post_id FROM `" . esc_sql($validated_table) . "`");
    
    $count = 0;
    $max_posts = 50; // Limit to prevent timeout - process in batches
    
    foreach ($post_ids as $post_id) {
        if ($count >= $max_posts) {
            // Store remaining post IDs in a transient for batch processing
            $remaining = array_slice($post_ids, $count);
            set_transient('kognetiks_ai_summaries_refresh_remaining', $remaining, HOUR_IN_SECONDS);
            break;
        }
        
        kognetiks_ai_summaries_delete_ai_summary($post_id);
        wp_cache_delete('kognetiks_ai_summaries_' . $post_id);

        $post = get_post($post_id);
        if ($post) {
            kognetiks_ai_summaries_generate_ai_summary($post_id);

            $full_ai_summary = kognetiks_ai_summaries_ai_summary_exists($post_id);
            if ( ! empty( $full_ai_summary ) && kognetiks_ai_summaries_validate_ai_summary( $full_ai_summary ) ) {
                kognetiks_ai_summaries_update_post_excerpt( $post_id, $full_ai_summary );
            }

            $count++;
        }
    }
    
    return $count;
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
    
    kognetiks_ai_summaries_refresh_all_summaries();
    
    // Add nonce to redirect URL for security
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
