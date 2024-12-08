<?php
/**
 * Kognetiks AI Summaries for WordPress - Diagnostics - Ver 1.0.0
 *
 * This file contains the code for the Kognetiks AI Summaries diagnostics.
 * It handles the support settings and other parameters.
 * 
 * @package kognetiks-ai-summaries
 */

 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

// Production Back Trace Function - Ver 1.0.0
function ksum_prod_trace($message_type = "NOTICE", $message = "No message") {

    // Trace production messages to the error log
    ksum_back_trace($message_type, $message);

}

// Back Trace Function - Ver 1.0.0
function ksum_back_trace($message_type = "NOTICE", $message = "No message") {

    // Usage Instructions
    // 
    // NOTE: Set WP_DEBUG and WP_DEBUG_LOG to true in wp-config.php to log messages to the debug.log file
    // 
    // Call the function // ksum_back_trace() from any file to log messages to your server's error log
    // 
    // Uncomment the // ksum_back_trace() function in the file(s) where you want to log messages
    // Or add new // ksum_back_trace() calls to log messages at any point in the code
    //
    // Go to the Chatbot Settings, then the Messages tab
    // Set the Chatbot Diagnotics to one of Off, Success, Notice, Failure, Warning, or Error
    //
    // Each level will log messages based on the following criteria (Off will not log any messages)
    // [ERROR], [WARNING], [NOTICE], or [SUCCESS]
    // 
    // Call this function using // ksum_back_trace( 'NOTICE', $message);
    // ksum_back_trace( 'ERROR', 'Some message');
    // ksum_back_trace( 'WARNING', 'Some message');
    // ksum_back_trace( 'NOTICE', 'Some message');
    // ksum_back_trace( 'SUCCESS', 'Some message');

    // Check if diagnostics is On
    $ksum_diagnostics = esc_attr(get_option('ksum_diagnostics', 'Off'));

    $ksum_diagnostics = esc_attr(get_option('ksum_diagnostics', 'Error'));
    if ('Off' === $ksum_diagnostics) {
        return;
    }

    // Belt and suspenders - make sure the value is either Off or Error
    if ('On' === $ksum_diagnostics) {
        $ksum_diagnostics = 'Error';
        update_option('ksum_diagnostics', $ksum_diagnostics);
    }

    $backtrace = debug_backtrace();
    $caller = isset($backtrace[1]) ? $backtrace[1] : null; // Get the second element from the backtrace array

    if ($caller) {
        $file = isset($caller['file']) ? basename($caller['file']) : 'unknown';
        $function = isset($caller['function']) ? $caller['function'] : 'unknown';
        $line = isset($caller['line']) ? $caller['line'] : 'unknown';
    } else {
        $file = 'unknown';
        $function = 'unknown';
        $line = 'unknown';
    }

    if ($message === null || $message === '') {
        $message = "No message";
    }
    if ($message_type === null || $message_type === '') {
        $message_type = "NOTICE";
    }

    // Convert the message to a string if it's an array
    if (is_array($message)) {
        $message = print_r($message, true); // Return the output as a string
    }

    // Upper case the message type
    $message_type = strtoupper($message_type);

    $date_time = (new DateTime())->format('d-M-Y H:i:s \U\T\C');

    // Message Type: Indicating whether the log is an error, warning, notice, or success message.
    // Prefix the message with [ERROR], [WARNING], [NOTICE], or [SUCCESS].
    // Check for other levels and print messages accordingly
    if ('Error' === $ksum_diagnostics) {
        // Print all types of messages
        error_log("[Ksum] [". $file ."] [". $function ."] [". $line  ."] [". $message_type ."] [" .$message ."]");
        ksum_error_log( "[". $date_time ."] [Ksum] [". $file ."] [". $function ."] [". $line  ."] [". $message_type ."] [" .$message ."]" );
    } elseif (in_array($ksum_diagnostics, ['Success', 'Failure'])) {
        // Print only SUCCESS and FAILURE messages
        if (in_array($message_type, ['SUCCESS', 'FAILURE'])) {
            error_log("[Ksum] [". $file ."] [". $function ."] [". $line  ."] [". $message_type ."] [" .$message ."]");
            ksum_error_log( "[". $date_time ."] [Ksum] [". $file ."] [". $function ."] [". $line  ."] [". $message_type ."] [" .$message ."]" );
        }
    } elseif ('Warning' === $ksum_diagnostics) {
        // Print only ERROR and WARNING messages
        if (in_array($message_type, ['ERROR', 'WARNING'])) {
            error_log("[Ksum] [". $file ."] [". $function ."] [". $line  ."] [". $message_type ."] [" .$message ."]");
            ksum_error_log( "[". $date_time ."] [Ksum] [". $file ."] [". $function ."] [". $line  ."] [". $message_type ."] [" .$message ."]" );
        }
    } elseif ('Notice' === $ksum_diagnostics) {
        // Print ERROR, WARNING, and NOTICE messages
        if (in_array($message_type, ['ERROR', 'WARNING', 'NOTICE'])) {
            error_log("[Ksum] [". $file ."] [". $function ."] [". $line  ."] [". $message_type ."] [" .$message ."]");
            ksum_error_log( "[". $date_time ."] [Ksum] [". $file ."] [". $function ."] [". $line  ."] [". $message_type ."] [" .$message ."]" );
        }
    } elseif ('Debug' === $ksum_diagnostics) {
        // Print all types of messages
        error_log("[Ksum] [". $file ."] [". $function ."] [". $line  ."] [". $message_type ."] [" .$message ."]");
        ksum_error_log( "[". $date_time ."] [Ksum] [". $file ."] [". $function ."] [". $line  ."] [". $message_type ."] [" .$message ."]" );
    } else {
        // Exit if none of the conditions are met
        return;
    }

}

// Log Chatbot Errors to the Server - Ver 1.0.0
function ksum_error_log($message) {

    global $ksum_plugin_dir_path;

    $chatbot_logs_dir = $ksum_plugin_dir_path . 'logs/';

    // Ensure the directory and index file exist
    create_directory_and_index_file($chatbot_logs_dir);

    // Get the current date to create a daily log file
    $current_date = date('Y-m-d');
    
    $log_file = $chatbot_logs_dir . 'ksum-error-log-' . $current_date . '.log';

    // Append the error message to the log file
    file_put_contents($log_file, $message . PHP_EOL, FILE_APPEND | LOCK_EX);

}

// Log Chatbot Errors to the Server - Ver 1.0.0
function log_ksum_error() {

    global $ksum_plugin_dir_path;
    
    if (isset($_POST['error_message'])) {
        $error_message = sanitize_text_field($_POST['error_message']);
        $chatbot_logs_dir = $ksum_plugin_dir_path . 'logs/';

        // Ensure the directory and index file exist
        create_directory_and_index_file($chatbot_logs_dir);

        // Get the current date to create a daily log file
        $current_date = date('Y-m-d');

        $log_file = $chatbot_logs_dir . 'ksum-error-log-' . $current_date . '.log';

        // Get additional info
        $session_id = session_id();
        $user_id = get_current_user_id();
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $date_time = date('Y-m-d H:i:s');

        // Construct the log message
        $log_message = sprintf(
            "[Chatbot] [ERROR] [%s] [Session ID: %s] [User ID: %s] [IP Address: %s] [%s] [%s]",
            $date_time,
            $session_id ? $session_id : 'N/A',
            $user_id ? $user_id : 'N/A',
            $ip_address,
            $error_message,
            PHP_EOL
        );

        // Append the error message to the log file
        file_put_contents($log_file, $log_message, FILE_APPEND | LOCK_EX);
    }
    wp_die(); // this is required to terminate immediately and return a proper response
}

// Register AJAX actions
add_action('wp_ajax_log_ksum_error', 'log_ksum_error');
add_action('wp_ajax_nopriv_log_ksum_error', 'log_ksum_error');
