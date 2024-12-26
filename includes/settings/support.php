<?php
/**
 * Kognetiks AI Summaries - Settings - Support Page
 *
 * This file contains the code for the Chatbot settings page.
 * It handles the support settings and other parameters.
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
    register_setting( 'ksum_support', 'chatgpt_support_key' );

    add_settings_section(
        'ksum_support_section',
        'Support',
        'ksum_support_section_callback',
        'ksum_support'
    );
}
add_action( 'admin_init', 'ksum_support_settings_init' );

// Get the list of documentation contents
function ksum_list_documentation_contents( $dir = '', $file = '' ) {

    global $ksum_plugin_dir_path;

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', 'ksum_list_documentation_contents' );
    // ksum_back_trace( 'NOTICE', '$ksum_plugin_dir_path: ' . $ksum_plugin_dir_path );

    $documentation_path = $ksum_plugin_dir_path . 'documentation';

    if ( ! file_exists( $documentation_path ) ) {
        return 'The specified documentation directory does not exist.';
    }

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', '$documentation_path: ' . $documentation_path );

    return ksum_traverse_directory( $documentation_path );

}

// Traverse the directory structure to get the list of directories and files
function ksum_traverse_directory( $path ) {

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', 'ksum_traverse_directory' );
    // ksum_back_trace( 'NOTICE', '$path: ' . $path );

    $contents = scandir( $path );
    $result   = [
        'directories' => [],
        'files' => [],
    ];

    foreach ( $contents as $item ) {
        if ( $item === '.' || $item === '..' ) {
            continue;
        }

        $full_path = $path . '/' . $item;

        if ( is_dir( $full_path ) ) {
            $result['directories'][ $item ] = ksum_traverse_directory( $full_path );
        } elseif ( is_file( $full_path ) && pathinfo( $item, PATHINFO_EXTENSION ) === 'md' ) {
            $result['files'][] = $item;
        }
    }

    return $result;

}

// Validate the requested directory and file
function ksum_validate_documentation( $dir, $file ) {

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', 'ksum_validate_documentation' );
    // ksum_back_trace( 'NOTICE', '$dir: ' . $dir );
    // ksum_back_trace( 'NOTICE', '$file: ' . $file );

    $allowed_file_extension = 'md'; // Only allow .md files

    // Quick checks for invalid characters and file extension
    if (
        ! preg_match( '/^[a-zA-Z0-9_\-\/]+$/', $dir ) ||
        pathinfo( $file, PATHINFO_EXTENSION ) !== $allowed_file_extension
    ) {
        return false;
    }

    // Initialize placeholders
    $data = [];
    $sub_directory = '';
    $directory = '';

    // Gather the entire document structure
    $contents = ksum_list_documentation_contents( $dir, $file );

    // Flatten the directory structure to create a list of valid directories and files
    $valid_directories = array_keys( $contents['directories'] );
    $valid_files = [];

    foreach ( $contents['directories'] as $directory => $data ) {
        if ( isset( $data['files'] ) ) {
            $valid_files[ $directory ] = $data['files'];
        } else {
            $valid_files[ $directory ] = [];
        }

        // Traverse subdirectories recursively
        $sub_directories = array_keys( $data['directories'] );
        foreach ( $sub_directories as $sub_directory ) {
            $valid_directories[] = $directory . '/' . $sub_directory;
            $valid_files[ $directory . '/' . $sub_directory ] = $data['directories'][ $sub_directory ]['files'];
        }
    }

    if ( ! empty( $valid_directories ) && ! empty( $valid_files ) && ! empty( $dir ) && ! empty( $file ) ) {
        // Check if the $dir and $file are found in valid arrays
        if (
            in_array( $dir, $valid_directories, true ) &&
            in_array( $file, $valid_files[ $dir ], true )
        ) {
            return true;
        }
    }

    return false;

}

// Support settings section callback
function ksum_support_section_callback() {

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', 'ksum_support_section_callback' );

    global $ksum_plugin_dir_path, $wp_filesystem;

    // Check the nonce if either param is set
    // This ensures the GET request is valid and not tampered with
    if ( isset( $_GET['dir'] ) || isset( $_GET['file'] ) ) {
        check_admin_referer( 'ksum_support_nonce' );
    }

    // Unsplash and sanitize $_GET['dir'] and $_GET['file']
    // If not set, default to an empty string
    if ( isset( $_GET['dir'] ) ) {
        $dir = sanitize_text_field( wp_unslash( $_GET['dir'] ) );
    } else {
        $dir = '';
    }

    if ( isset( $_GET['file'] ) ) {
        $file = sanitize_text_field( wp_unslash( $_GET['file'] ) );
    } else {
        $file = '';
    }

    // Determine the documentation path to load
    if ( ! empty( $dir ) && ! empty( $file ) ) {
        $doc_location = ltrim( $dir, '/' ) . '/' . ltrim( $file, '/' );
    } elseif ( empty( $dir ) && empty( $file ) ) {
        $doc_location = '/documentation/overview.md';
    }

    // Validate directory/file combination
    if ( ksum_validate_documentation( $dir, $file ) ) {

        // DIAG - Diagnostics
        // ksum_back_trace( 'NOTICE', '$doc_location: ' . $doc_location );

        // Document found, display it
        $doc_location = $ksum_plugin_dir_path . 'documentation/' . $doc_location;

    } else {

        // DIAG - Diagnostics
        // ksum_back_trace( 'NOTICE', 'Invalid directory/file combination.' );

        // Fallback to overview.md
        $doc_location = $ksum_plugin_dir_path . 'documentation/overview.md';

    }

    // Initialize the WP Filesystem API if not already done
    if ( ! function_exists( 'WP_Filesystem' ) ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }
    if ( ! is_object( $wp_filesystem ) ) {
        WP_Filesystem();
    }

    // Safely get file contents via WP_Filesystem
    $markdown_content = '';
    if ( $wp_filesystem->exists( $doc_location ) ) {
        $markdown_content = $wp_filesystem->get_contents( $doc_location );
    }

    // Parse the markdown to HTML
    $parsedown = new Parsedown();
    $html_content = $parsedown->text( $markdown_content );

    // Build the base path for anchor & image adjustments
    $base_path = '?page=kognetiks-ai-summaries';
    if ( '' !== $dir ) {
        $base_path .= '&tab=support&dir=' . rawurlencode( $dir );
    }
    if ( '' !== $file ) {
        // Remove 'overview.md/' from the file parameter
        $file = str_replace( 'overview.md/', '', $file );
        $base_path .= '&file=' . rawurlencode( $file );
    }
    $adjusted_html_content = ksum_adjust_paths( $html_content, $base_path );

    // Optional: Add inline styling to <ul> and <li> tags
    $adjusted_html_content = str_replace(
        '<ul>',
        '<ul style="list-style-type: disc; margin-left: 20px;">',
        $adjusted_html_content
    );
    $adjusted_html_content = str_replace(
        '<li>',
        '<li style="margin-bottom: 10px;">',
        $adjusted_html_content
    );

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', '$adjusted_html_content: ' . $adjusted_html_content );

    // Output the HTML content
    echo wp_kses_post( $adjusted_html_content );

}

// Check if a file exists in the documentation location
function ksum_file_exists_in_doc_location( $doc_location ) {

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', 'ksum_file_exists_in_doc_location' );

    return file_exists( $doc_location );

}

// Adjust the paths of images and anchors in the documentation
function ksum_adjust_paths( $html, $base_path ) {

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', 'ksum_adjust_paths' );

    // Adjust image paths
    $html = preg_replace_callback(
        '/<img\s+src="([^"]+)"/i',
        function ( $matches ) use ( $base_path ) {
            $adjusted_image_path = ksum_adjust_image_path( $matches[1], $base_path );
            return '<img src="' . esc_url( $adjusted_image_path ) . '" style="max-width: 80%; width: auto; height: auto; border: 1px solid black; box-shadow: 5px 5px 7px rgba(0, 0, 0, 0.3);"';
        },
        $html
    );

    // Adjust anchor paths and conditionally add target="_blank"
    $html = preg_replace_callback(
        '/<a\s+href="([^"]+)"/i',
        function ( $matches ) use ( $base_path ) {
            $adjusted_href = ksum_adjust_path( $matches[1], $base_path );
            $plugin_url = plugins_url('/', dirname(__FILE__));

            // DIAG - Diagnostics
            ksum_back_trace( 'NOTICE', '$adjusted_href: ' . $adjusted_href );
            ksum_back_trace( 'NOTICE', '$plugin_url: ' . $plugin_url );

            $target_blank = strpos($adjusted_href, $plugin_url) === false ? ' target="_blank"' : '';
            return '<a href="' . esc_url( $adjusted_href ) . '"' . $target_blank;
        },
        $html
    );

    return $html;

}

// Adjust the path of an anchor
function ksum_adjust_path( $url, $base_path ) {

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', 'ksum_adjust_path' );

    // If its not an absolute URL and not an anchor (#)
    if ( 0 !== strpos( $url, 'http' ) && 0 !== strpos( $url, '#' ) ) {
        
        $nonce = wp_create_nonce( 'ksum_support_nonce' );

        // Split the URL by '/' to get the dir and file
        $parts = explode( '/', $url );

        // Build the new link
        if ( count( $parts ) >= 2 ) {
            $dir  = $parts[0];
            $file = $parts[1];

            // Construct the URL with the correct parameters
            $url = add_query_arg(
                array(
                    'tab'      => 'support',
                    'dir'      => $dir,
                    'file'     => $file,
                    '_wpnonce' => $nonce,
                ),
                $base_path
            );
        } else {
            $base_path_parts = explode( '&file=', $base_path );
            $url = add_query_arg(
                array(
                    'file'     => $url,
                    '_wpnonce' => $nonce,
                ),
                rtrim( $base_path_parts[0], '/' )
            );
        }
    }

    return esc_url( $url );

}

// Adjust the path of an image
function ksum_adjust_image_path( $url, $base_path ) {

    // DIAG - Diagnostics
    // ksum_back_trace( 'NOTICE', 'ksum_adjust_image_path' );

    // If its not an absolute URL
    if ( 0 !== strpos( $url, 'http' ) ) {
        // If the URL is a relative path, construct the direct path to the image
        $base_path_parts = explode( '&dir=', $base_path );

        $plugin_url = plugins_url('/', dirname(dirname(__FILE__)));

        if ( count( $base_path_parts ) > 1 ) {
            $dir_parts = explode( '&file=', $base_path_parts[1] );
            $dir = rtrim( $dir_parts[0], '/' );
            // Build the full path
            $url = $plugin_url . 'documentation/' . $dir . '/' . $url;
        } else {
            $url = $plugin_url . 'documentation/' . $url;
        }
    }

    return esc_url( $url );

}
