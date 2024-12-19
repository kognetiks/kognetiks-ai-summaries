<?php
/**
 * Kognetiks AI Summaries - Utilities - Ver 1.0.0
 *
 * This file contains the code for the Kognetiks AI Summaries utitlies.
 * It handles the support settings and other parameters.
 * 
 * @package kognetiks-ai-summaries
 */

 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

// Function to create a directory and an index.php file
function ksum_create_directory_and_index_file($dir_path) {
    // Ensure the directory ends with a slash
    $dir_path = rtrim($dir_path, '/') . '/';

    // Check if the directory exists, if not create it
    if (!file_exists($dir_path) && !wp_mkdir_p($dir_path)) {
        // Error handling, e.g., log the error or handle the failure appropriately
        // back_trace( 'ERROR', 'Failed to create directory.');
        return false;
    }

    // Path for the index.php file
    $index_file_path = $dir_path . 'index.php';

    // Check if the index.php file exists, if not create it
    if (!file_exists($index_file_path)) {
        $file_content = "<?php\n// Silence is golden.\n\n// Load WordPress Environment\n\$wp_load_path = dirname(__FILE__, 5) . '/wp-load.php';\nif (file_exists(\$wp_load_path)) {\n    require_once(\$wp_load_path);\n} else {\n    exit('Could not find wp-load.php');\n}\n\n// Force a 404 error\nstatus_header(404);\nnocache_headers();\ninclude(get_404_template());\nexit;\n?>";
        file_put_contents($index_file_path, $file_content);
    }

    // Set directory permissions
    chmod($dir_path, 0755);

    return true;

}

