<?php
/**
 * Kognetiks AI Summaries - Manage Error Logs - Ver 1.0.0
 *
 * This file contains the code for managing the plugin error logs
 * 
 *
 * @package kognetiks-ai-summaries
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

// Retrieve error log file names
function ksum_manage_error_logs() {

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', 'ksum_manage_error_logs');

    global $ksum_plugin_dir_path;

    $ksum_logs_dir = $ksum_plugin_dir_path . 'logs/';

    // Ensure the directory and index file exist
    ksum_create_directory_and_index_file($ksum_logs_dir);

    // Initialize $scanned_dir with a default value
    $scanned_dir = false;

    // Check if the directory exists
    if (is_dir($ksum_logs_dir)) {

        $scanned_dir = scandir($ksum_logs_dir);

    } else {

        // Handle the error, e.g., log it, create the directory, or throw an exception
        // DIAG - Diagnostics
        // ksum_back_trace( 'ERROR', 'Directory not found: ' . $ksum_logs_dir);
        return;

    }

    // Check if scandir returns false and handle the error
    if ($scanned_dir === false) {

        // DIAG - Diagnostics
        // ksum_back_trace( 'ERROR', 'Error accessing log files directory.');
        return;

    }

    $files = array_diff($scanned_dir, array('..', '.'));

    // Exclude non-log files
    $files = array_filter($files, function($file) {

        return preg_match('/\.log$/', $file);

    });

    if (empty($files)) {

        echo '<p>No log files found.</p>';

        return;

    }

    // DIAG - Log files for troubleshooting - Ver 2.0.7
    // ksum_back_trace( 'NOTICE', 'ksum_manage_error_logs - Files: ' . print_r($files, true));

    $output = '<div class="wrap error-log-templates-display">';

    $output .= '<form method="post" action="">';
    $output .= '<table>';
    $output .= '<thead>';
    $output .= '<tr>';
    $output .= '<th>File Name</th>';
    $output .= '<th>Actions</th>';
    $output .= '</tr>';
    $output .= '</thead>';
    $output .= '<tbody>';

    foreach ($files as $file) {
        $file_path = $ksum_logs_dir . $file;
        $output .= '<tr>';
        $output .= '<td>' . esc_html($file) . '</td>';
        $output .= '<td>';
        $output .= '<a href="' . esc_url(wp_nonce_url(admin_url('admin-post.php?action=ksum_download_log&file=' . urlencode($file)), 'ksum_download_log_' . $file)) . '" class="button button-primary">Download</a> ';
        $output .= '<a href="' . esc_url(wp_nonce_url(admin_url('admin-post.php?action=ksum_delete_log&file=' . urlencode($file)), 'ksum_delete_log_' . $file)) . '" class="button button-primary">Delete</a>';
        $output .= '</td>';
        $output .= '</tr>';
    }

    $output .= '</tbody>';
    $output .= '</table>';
    $output .= '<p><a href="' . esc_url(wp_nonce_url(admin_url('admin-post.php?action=ksum_delete_all_logs'), 'ksum_delete_all_logs')) . '" class="button button-danger">Delete All</a></p>';
    $output .= '</form>';
    $output .= '</div>';

    echo wp_kses_post($output); // Output the generated HTML

}

// Handle error log actions
function ksum_handle_log_actions() {

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', 'ksum_handle_log_actions');

    global $wp_filesystem;
    global $ksum_plugin_dir_path;

    if (!isset($_GET['action']) || !isset($_GET['_wpnonce'])) {

        return;

    }

    $action = sanitize_text_field(wp_unslash($_GET['action']));
    $nonce = sanitize_text_field(wp_unslash($_GET['_wpnonce']));

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', 'ksum_handle_log_actions - Action: ' . $action . ' - Nonce: ' . $nonce);

    switch ($action) {

        case 'ksum_download_log':

            if (isset($_GET['file']) && !empty($_GET['file'])) {

                $file = sanitize_file_name(wp_unslash($_GET['file']));

                if (!wp_verify_nonce($nonce, 'ksum_download_log_' . $file)) {

                    wp_die(esc_html__('Invalid nonce specified.', 'kognetiks-ai-summaries'));

                }

            } else {

                wp_die(esc_html__('No file specified.', 'kognetiks-ai-summaries'));

            }

            $file = sanitize_file_name(basename(sanitize_text_field(wp_unslash($_GET['file']))));
            $ksum_logs_dir = $ksum_plugin_dir_path . 'logs/';
            $file_path = $ksum_logs_dir . $file;

            if (file_exists($file_path)) {

                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file_path));

                $file_content = $wp_filesystem->get_contents( $file_path );

                if ( $file_content !== false ) {

                    echo esc_html( $file_content );

                } else {

                    // Handle error
                    echo esc_html( 'Error reading file.' );

                }

                exit;

            } else {

                wp_die('File not found');

            }

            break;

        case 'ksum_delete_log':

            if (!wp_verify_nonce($nonce, 'ksum_delete_log_' . sanitize_file_name(wp_unslash($_GET['file'])))) {

                wp_die(esc_html__('Invalid nonce specified.', 'kognetiks-ai-summaries'));

            }

            $file = sanitize_file_name(basename(sanitize_text_field(wp_unslash($_GET['file']))));
            $ksum_logs_dir = $ksum_plugin_dir_path . 'logs/';
            $file_path = $ksum_logs_dir . $file;

            // DIAG - Diagnostics
            // ksum_back_trace( 'NOTICE', 'ksum_handle_log_actions - delete_log: ' . $file . ' - File Path: ' . $file_path);

            if (file_exists($file_path)) {

                wp_delete_file($file_path);
                wp_redirect(admin_url('admin.php?page=kognetiks-ai-summaries&tab=tools')); // Redirect to plugin page

                exit;

            } else {

                wp_die('File not found');

            }
            
            break;

        case 'ksum_delete_all_logs':

            if (!wp_verify_nonce($nonce, 'ksum_delete_all_logs')) {

                wp_die('Invalid nonce');

            }

            $ksum_logs_dir = $ksum_plugin_dir_path . 'logs/';
            $files = array_diff(scandir($ksum_logs_dir), array('..', '.'));

            foreach ($files as $file) {

                $file_path = $ksum_logs_dir . $file;

                if (file_exists($file_path)) {

                    wp_delete_file($file_path);

                }
            }

            wp_redirect(admin_url('admin.php?page=kognetiks-ai-summaries&tab=tools')); // Redirect to plugin page

            exit;

            break;

        default:

            wp_die('Invalid action');

    }
}
add_action('admin_post_nopriv_ksum_download_log', 'ksum_handle_log_actions');
add_action('admin_post_ksum_download_log', 'ksum_handle_log_actions');
add_action('admin_post_nopriv_ksum_delete_log', 'ksum_handle_log_actions');
add_action('admin_post_ksum_delete_log', 'ksum_handle_log_actions');
add_action('admin_post_nopriv_ksum_delete_all_logs', 'ksum_handle_log_actions');
add_action('admin_post_ksum_delete_all_logs', 'ksum_handle_log_actions');
