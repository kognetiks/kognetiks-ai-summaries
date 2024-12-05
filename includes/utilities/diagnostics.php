<?php
namespace Kognetiks\AISummaries;
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

// Production Back Trace Function - Revised in Ver 1.0.0
function prod_trace($message_type = "NOTICE", $message = "No message") {

    // Trace production messages to the error log
    back_trace($message_type, $message);

}

// Back Trace Function - Revised in Ver 1.0.0
function back_trace($message_type = "NOTICE", $message = "No message") {

    // Usage Instructions
    // 
    // NOTE: Set WP_DEBUG and WP_DEBUG_LOG to true in wp-config.php to log messages to the debug.log file
    // 
    // Call the function // back_trace() from any file to log messages to your server's error log
    // 
    // Uncomment the // back_trace() function in the file(s) where you want to log messages
    // Or add new // back_trace() calls to log messages at any point in the code
    //
    // Go to the Chatbot Settings, then the Messages tab
    // Set the Chatbot Diagnotics to one of Off, Success, Notice, Failure, Warning, or Error
    //
    // Each level will log messages based on the following criteria (Off will not log any messages)
    // [ERROR], [WARNING], [NOTICE], or [SUCCESS]
    // 
    // Call this function using // back_trace( 'NOTICE', $message);
    // back_trace( 'ERROR', 'Some message');
    // back_trace( 'WARNING', 'Some message');
    // back_trace( 'NOTICE', 'Some message');
    // back_trace( 'SUCCESS', 'Some message');

    // Check if diagnostics is On
    $chatbot_chatgpt_diagnostics = esc_attr(get_option('chatbot_chatgpt_diagnostics', 'Error'));
    if ('Off' === $chatbot_chatgpt_diagnostics) {
        return;
    }

    // Belt and suspenders - make sure the value is either Off or Error
    if ('On' === $chatbot_chatgpt_diagnostics) {
        $chatbot_chatgpt_diagnostics = 'Error';
        update_option('chatbot_chatgpt_diagnostics', $chatbot_chatgpt_diagnostics);
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
    if ('Error' === $chatbot_chatgpt_diagnostics) {
        // Print all types of messages
        error_log("[Chatbot] [". $file ."] [". $function ."] [". $line  ."] [". $message_type ."] [" .$message ."]");
        chatbot_error_log( "[". $date_time ."] [Chatbot] [". $file ."] [". $function ."] [". $line  ."] [". $message_type ."] [" .$message ."]" );
    } elseif (in_array($chatbot_chatgpt_diagnostics, ['Success', 'Failure'])) {
        // Print only SUCCESS and FAILURE messages
        if (in_array($message_type, ['SUCCESS', 'FAILURE'])) {
            error_log("[Chatbot] [". $file ."] [". $function ."] [". $line  ."] [". $message_type ."] [" .$message ."]");
            chatbot_error_log( "[". $date_time ."] [Chatbot] [". $file ."] [". $function ."] [". $line  ."] [". $message_type ."] [" .$message ."]" );
        }
    } elseif ('Warning' === $chatbot_chatgpt_diagnostics) {
        // Print only ERROR and WARNING messages
        if (in_array($message_type, ['ERROR', 'WARNING'])) {
            error_log("[Chatbot] [". $file ."] [". $function ."] [". $line  ."] [". $message_type ."] [" .$message ."]");
            chatbot_error_log( "[". $date_time ."] [Chatbot] [". $file ."] [". $function ."] [". $line  ."] [". $message_type ."] [" .$message ."]" );
        }
    } elseif ('Notice' === $chatbot_chatgpt_diagnostics) {
        // Print ERROR, WARNING, and NOTICE messages
        if (in_array($message_type, ['ERROR', 'WARNING', 'NOTICE'])) {
            error_log("[Chatbot] [". $file ."] [". $function ."] [". $line  ."] [". $message_type ."] [" .$message ."]");
            chatbot_error_log( "[". $date_time ."] [Chatbot] [". $file ."] [". $function ."] [". $line  ."] [". $message_type ."] [" .$message ."]" );
        }
    } elseif ('Debug' === $chatbot_chatgpt_diagnostics) {
        // Print all types of messages
        error_log("[Chatbot] [". $file ."] [". $function ."] [". $line  ."] [". $message_type ."] [" .$message ."]");
        chatbot_error_log( "[". $date_time ."] [Chatbot] [". $file ."] [". $function ."] [". $line  ."] [". $message_type ."] [" .$message ."]" );
    } else {
        // Exit if none of the conditions are met
        return;
    }

}