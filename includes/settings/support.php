<?php
/**
 * Kognetiks Chatbot for WordPress - Settings - Support Page
 *
 * This file contains the code for the Chatbot settings page.
 * It handles the support settings and other parameters.
 * 
 *
 * @package kognetiks-ai-summaries
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

// Register Support settings
function ksum_support_settings_init() {

    // Support settings tab
    register_setting('ksum_support', 'chatgpt_support_key');

    add_settings_section(
        'ksum_support_section',
        'Support',
        'ksum_support_section_callback',
        'ksum_support'
    );

}
add_action('admin_init', 'ksum_support_settings_init');

// Get the list of documentation contents
function ksum_list_documentation_contents() {

    global $ksum_plugin_dir_path;

    $documentationPath = $ksum_plugin_dir_path . '/documentation';
    
    if (!file_exists($documentationPath)) {
        return "The specified documentation directory does not exist.";
    }

    return ksum_traverse_directory($documentationPath);
    
}

// Traverse the directory structure to get the list of directories and files
function ksum_traverse_directory($path) {

    $contents = scandir($path);
    $result = [
        'directories' => [],
        'files' => []
    ];

    foreach ($contents as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }

        $fullPath = $path . '/' . $item;

        if (is_dir($fullPath)) {
            $result['directories'][$item] = ksum_traverse_directory($fullPath);
        } elseif (is_file($fullPath) && pathinfo($item, PATHINFO_EXTENSION) === 'md') {
            $result['files'][] = $item;
        }
    }

    return $result;

}

// Validate the requested directory and file
function ksum_validate_documentation($dir, $file) {

    $allowed_file_extension = 'md'; // Set the allowed file extension to 'md'

    if (!preg_match('/^[a-zA-Z0-9_\-\/]+$/', $dir) || pathinfo($file, PATHINFO_EXTENSION) !== $allowed_file_extension) {
        return false;
    }

    $data = []; // Initialize $data as an empty array
    $sub_directory = ""; // Initialize $sub_directory as an empty string
    $directory = ""; // Initialize $directory as an empty string

    $contents = ksum_list_documentation_contents();

    // Flatten the directory structure to create a list of valid directories and files
    $valid_directories = array_keys($contents['directories']);
    $valid_files = [];

    foreach ($contents['directories'] as $directory => $data) {
        if (isset($data['files'])) {
            $valid_files[$directory] = $data['files'];
        } else {
            $valid_files[$directory] = [];
        }

        // Traverse subdirectories recursively
        $sub_directories = array_keys($data['directories']);
        foreach ($sub_directories as $sub_directory) {
            $valid_directories[] = $directory . '/' . $sub_directory;
            $valid_files[$directory . '/' . $sub_directory] = $data['directories'][$sub_directory]['files'];
        }
    }

    // Diagnostics
    ksum_back_trace( 'NOTICE', '$valid_directories: ' . print_r($valid_directories, true));
    ksum_back_trace( 'NOTICE', '$valid_files: ' . print_r($valid_files, true));

    if (!empty($valid_directories) && !empty($valid_files) && !empty($dir) && !empty($file)) {
        // If the $dir and $file are found in the list of $valid_directories and $valid_files, return true
        if (in_array($dir, $valid_directories) && in_array($file, $valid_files[$dir])) {

            // DIAG - Diagnostics
            ksum_back_trace( 'NOTICE', 'ksum_validate_documentation: $dir: '. $dir );
            ksum_back_trace( 'NOTICE', 'ksum_validate_documentation: $file: '. $file );

            // Return true if the directory and file are valid
            return true;

        }
    }

    // Return false if the directory and file are invalid
    return false;

}

// Support settings section callback
function ksum_support_section_callback() {

    global $ksum_plugin_dir_path;

    // Get the 'documentation' parameter from the URL
    $docLocation = '';

    if (isset($_GET['dir'])) {
        $dir = sanitize_file_name(basename(sanitize_text_field($_GET['dir'])));
    } else {
        $dir = '';
    }

    if (isset($_GET['file'])) {
        $file = sanitize_file_name(basename(sanitize_text_field($_GET['file'])));
    } else {
        $file = '';
    }

    if (!empty($dir) && !empty($file)) {
        $docLocation = $dir . '/' . $file;
    } else if (empty($dir) && empty($file)) {
        $docLocation = 'overview.md';
    }

    // Validate the that the requested documentation directory and file exist
    if (ksum_validate_documentation($dir, $file)) {
        $docLocation = $ksum_plugin_dir_path . 'documentation/' . $docLocation;
    } else {
        $docLocation = $ksum_plugin_dir_path . 'documentation/' . 'overview.md';
    }

    // DIAG - Diagnostics
    ksum_back_trace( 'NOTICE', '$docLocation: '. $docLocation );

    // DIAG - Diagnostics
    // error_reporting(E_ALL);
    // ini_set('display_errors', 1);
  
    $parsedown = new Parsedown();
    $markdownContent = file_get_contents($docLocation);
    $htmlContent = $parsedown->text($markdownContent);

    $dir = isset($_GET['dir']) ? sanitize_text_field($_GET['dir']) : '';
    $file = isset($_GET['file']) ? sanitize_text_field($_GET['file']) : '';
    
    $basePath = $ksum_plugin_dir_path . 'documentation/';
    $basePath = "?page=kognetiks-ai-summaries";
    if ($dir !== '') {
        $basePath .= "&tab=support&dir=" . $dir;
    }
    if ($file !== '') {
        // Remove 'overview.md/' from the file parameter
        $file = str_replace('overview.md/', '', $file);
        $basePath .= "&file=" . $file;
    }
    $adjustedHtmlContent = ksum_adjust_paths($htmlContent, $basePath);

    // Add inline styling to <ul> and <li> tags
    $adjustedHtmlContent = str_replace('<ul>', '<ul style="list-style-type: disc; margin-left: 20px;">', $adjustedHtmlContent);
    $adjustedHtmlContent = str_replace('<li>', '<li style="margin-bottom: 10px;">', $adjustedHtmlContent);

    // DIAG - Diagnostics
    // $absolutePath = __DIR__ . '/adjustedHtmlContent.html';
    // $result = file_put_contents($absolutePath, $adjustedHtmlContent);
    // if ($result === false) {
    //     ksum_back_trace(  "Failed to write to file: " . $absolutePath );
    // } else {
    //     ksum_back_trace( 'NOTICE', "File written successfully to: " . $absolutePath );
    // }

    echo wp_kses_post($adjustedHtmlContent);

}

function ksum_file_exists_in_doc_location($docLocation) {

    return file_exists($docLocation);

}

function ksum_adjust_paths($html, $basePath) {

    // Adjust image paths
    $html = preg_replace_callback(
        '/<img\s+src="([^"]+)"/i',
        function ($matches) use ($basePath) {
            $adjustedImagePath = ksum_adjust_image_path($matches[1], $basePath);
            return '<img src="' . esc_url($adjustedImagePath) . '" style="max-width: 80%; width: auto; height: auto; border: 1px solid black; box-shadow: 5px 5px 7px rgba(0, 0, 0, 0.3);"';
        },
        $html
    );

    // Adjust anchor paths
    $html = preg_replace_callback(
        '/<a\s+href="([^"]+)"/i',
        function ($matches) use ($basePath) {
            $adjustedHref = ksum_adjust_path($matches[1], $basePath);
            return '<a href="' . esc_url($adjustedHref) . '"';
        },
        $html
    );

    return $html;
    
}

function ksum_adjust_path($url, $basePath) {

    if (strpos($url, 'http') !== 0 && strpos($url, '#') !== 0) {

        // Split the URL by '/' to get the dir and file
        $parts = explode('/', $url);

        if (count($parts) >= 2) {
            $dir = $parts[0];
            $file = $parts[1];

            // Construct the URL with the correct parameters
            $url = rtrim($basePath, '/') . "&tab=support&dir=" . $dir . "&file=" . $file;
        } else {
            // If the URL is a relative path, append it to the base path of the current document
            $basePathParts = explode('&file=', $basePath);
            $url = rtrim($basePathParts[0], '/') . "&file=" . $url;

        }

    }

    return $url;

}

function ksum_adjust_image_path($url, $basePath) {

    if (strpos($url, 'http') !== 0) {

        // If the URL is a relative path, construct the direct path to the image
        $basePathParts = explode('&dir=', $basePath);

        if (count($basePathParts) > 1) {
            $dirParts = explode('&file=', $basePathParts[1]);
            $dir = rtrim($dirParts[0], '/');
            // Check if the URL is a relative path and sanitize it
            $url = site_url() . '/wp-content/plugins/kognetiks-ai-summaries/documentation/' . $dir . '/' . $url;
        } else {
            // Check if the URL is a relative path and sanitize it
            $url = site_url() . '/wp-content/plugins/kognetiks-ai-summaries/documentation/' . $url;
        }

    }

    return esc_url($url);

}
