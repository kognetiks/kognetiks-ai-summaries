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
    if (isset($_GET['cleanup_success'])) {
        $action = sanitize_text_field($_GET['cleanup_success']);
        switch ($action) {
            case 'delete_all':
                echo '<div class="notice notice-success is-dismissible"><p>All AI summaries have been deleted.</p></div>';
                break;
            case 'refresh_all':
                echo '<div class="notice notice-success is-dismissible"><p>All AI summaries are being refreshed. This may take some time.</p></div>';
                break;
            case 'proper_case':
                $cats = isset($_GET['cats']) ? intval($_GET['cats']) : 0;
                $tags = isset($_GET['tags']) ? intval($_GET['tags']) : 0;
                echo '<div class="notice notice-success is-dismissible"><p>Categories and tags have been converted to Proper Case. Updated: ' . $cats . ' categories, ' . $tags . ' tags.</p></div>';
                break;
            case 'delete_empty_categories':
                $count = isset($_GET['count']) ? intval($_GET['count']) : 0;
                echo '<div class="notice notice-success is-dismissible"><p>Empty categories have been deleted. Removed: ' . $count . ' categories.</p></div>';
                break;
            case 'delete_empty_tags':
                $count = isset($_GET['count']) ? intval($_GET['count']) : 0;
                echo '<div class="notice notice-success is-dismissible"><p>Empty tags have been deleted. Removed: ' . $count . ' tags.</p></div>';
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
                    <p>This will regenerate summaries for all posts that have AI summaries. This may take a while.</p>
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

// Delete all AI summaries from the database
function kognetiks_ai_summaries_delete_all_summaries() {
    
    // DIAG - Diagnostics
    // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_delete_all_summaries' );
    
    global $wpdb;
    
    // Delete all rows from the AI summaries table
    $table_name = $wpdb->prefix . 'kognetiks_ai_summaries';
    $result = $wpdb->query("DELETE FROM {$table_name}");
    
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
    
    global $wpdb;
    
    // Get all post IDs that have AI summaries
    $table_name = $wpdb->prefix . 'kognetiks_ai_summaries';
    $post_ids = $wpdb->get_col("SELECT post_id FROM {$table_name}");
    
    $count = 0;
    $max_posts = 50; // Limit to prevent timeout - process in batches
    
    foreach ($post_ids as $post_id) {
        if ($count >= $max_posts) {
            // Store remaining post IDs in a transient for batch processing
            $remaining = array_slice($post_ids, $count);
            set_transient('kognetiks_ai_summaries_refresh_remaining', $remaining, HOUR_IN_SECONDS);
            break;
        }
        
        // Delete the existing summary to force regeneration
        kognetiks_ai_summaries_delete_ai_summary($post_id);
        
        // Clear cache for this post
        wp_cache_delete('kognetiks_ai_summaries_' . $post_id);
        
        // Trigger regeneration by calling the summary function
        $post = get_post($post_id);
        if ($post) {
            // Force regeneration by calling the generate function
            kognetiks_ai_summaries_generate_ai_summary($post_id);
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

// Handle delete all summaries action
function kognetiks_ai_summaries_handle_delete_all_summaries() {
    
    // Check nonce and permissions
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'kognetiks_ai_summaries_cleanup_action')) {
        wp_die('Security check failed');
    }
    
    if (!current_user_can('manage_options')) {
        wp_die('You do not have permission to perform this action');
    }
    
    kognetiks_ai_summaries_delete_all_summaries();
    
    wp_redirect(add_query_arg('cleanup_success', 'delete_all', admin_url('admin.php?page=kognetiks-ai-summaries&tab=tools')));
    exit;
}

// Handle refresh all summaries action
function kognetiks_ai_summaries_handle_refresh_all_summaries() {
    
    // Check nonce and permissions
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'kognetiks_ai_summaries_cleanup_action')) {
        wp_die('Security check failed');
    }
    
    if (!current_user_can('manage_options')) {
        wp_die('You do not have permission to perform this action');
    }
    
    kognetiks_ai_summaries_refresh_all_summaries();
    
    wp_redirect(add_query_arg('cleanup_success', 'refresh_all', admin_url('admin.php?page=kognetiks-ai-summaries&tab=tools')));
    exit;
}

// Handle proper case categories and tags action
function kognetiks_ai_summaries_handle_proper_case_categories_tags() {
    
    // Check nonce and permissions
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'kognetiks_ai_summaries_cleanup_action')) {
        wp_die('Security check failed');
    }
    
    if (!current_user_can('manage_options')) {
        wp_die('You do not have permission to perform this action');
    }
    
    $result = kognetiks_ai_summaries_convert_to_proper_case();
    
    $redirect_url = add_query_arg(array(
        'cleanup_success' => 'proper_case',
        'cats' => $result['categories'],
        'tags' => $result['tags']
    ), admin_url('admin.php?page=kognetiks-ai-summaries&tab=tools'));
    
    wp_redirect($redirect_url);
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
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'kognetiks_ai_summaries_cleanup_action')) {
        wp_die('Security check failed');
    }
    
    if (!current_user_can('manage_options')) {
        wp_die('You do not have permission to perform this action');
    }
    
    $count = kognetiks_ai_summaries_delete_empty_categories();
    
    $redirect_url = add_query_arg(array(
        'cleanup_success' => 'delete_empty_categories',
        'count' => $count
    ), admin_url('admin.php?page=kognetiks-ai-summaries&tab=tools'));
    
    wp_redirect($redirect_url);
    exit;
}

// Handle delete empty tags action
function kognetiks_ai_summaries_handle_delete_empty_tags() {
    
    // Check nonce and permissions
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'kognetiks_ai_summaries_cleanup_action')) {
        wp_die('Security check failed');
    }
    
    if (!current_user_can('manage_options')) {
        wp_die('You do not have permission to perform this action');
    }
    
    $count = kognetiks_ai_summaries_delete_empty_tags();
    
    $redirect_url = add_query_arg(array(
        'cleanup_success' => 'delete_empty_tags',
        'count' => $count
    ), admin_url('admin.php?page=kognetiks-ai-summaries&tab=tools'));
    
    wp_redirect($redirect_url);
    exit;
}
