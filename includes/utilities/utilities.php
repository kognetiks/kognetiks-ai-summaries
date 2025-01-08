<?php
/**
 * Kognetiks AI Summaries - Utilities - Ver 1.0.0
 *
 * This file contains the code for the Kognetiks AI Summaries utilities.
 * It handles the support settings and other parameters.
 * 
 * @package kognetiks-ai-summaries
 */

 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

// REMOVE - Ver 1.0.0
// Function to create a directory and an index.php file
// function kognetiks_ai_summaries_create_directory_and_index_file($dir_path) {

//     // DIAG - Diagnostics
//     // kognetiks_ai_summaries_back_trace( 'NOTICE', 'kognetiks_ai_summaries_create_directory_and_index_file' );

//     global $wp_filesystem;
    
//     // Ensure the directory ends with a slash
//     $dir_path = rtrim($dir_path, '/') . '/';

//     // Check if the directory exists, if not create it
//     if (!file_exists($dir_path) && !wp_mkdir_p($dir_path)) {
//         // Error handling, e.g., log the error or handle the failure appropriately
//         // kognetiks_ai_summaries_back_trace( 'ERROR', 'Failed to create directory.');
//         return false;
//     }

//     // Path for the index.php file
//     $index_file_path = $dir_path . 'index.php';

//     // Check if the index.php file exists, if not create it
//     if (!file_exists($index_file_path)) {
//         $file_content = "<?php\n// Silence is golden.\n\n";
//         if ( ! $wp_filesystem->put_contents( $index_file_path, $file_content, FS_CHMOD_FILE ) ) {
//             wp_die( esc_html__( 'Failed to write to file.', 'kognetiks-ai-summaries' ) );
//         }
//     }

//     // Set directory permissions
//     if ( ! $wp_filesystem->chmod( $dir_path, 0755 ) ) {
//         wp_die( esc_html__( 'Failed to change directory permissions.', 'kognetiks-ai-summaries' ) );
//     }

//     return true;

// }
