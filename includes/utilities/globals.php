<?php
/**
 * Kognetiks AI Summaries - Globals - Ver 1.0.0
 *
 * This file contains the code for the Kognetiks AI Summaries globals.
 *
 * 
 * @package kognetiks-ai-summaries
 */

 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

// Declare the $errorResponses array as global
global $errorResponses;
$errorResponses = [
    " It seems there may have been an issue with the API. Let's try again later.",
    " Unfortunately, we might have encountered a problem with the API. Please give it another shot in a little while.",
    " I apologize, but it appears there's a hiccup with the API at the moment. We can attempt this again later.",
    " The API seems to be experiencing difficulties right now. We can come back to this when it's resolved.",
    " I'm sorry, but it seems like there's an error from the API side. Please retry in a bit.",
    " There might be a temporary issue with the API. Please try your request again in a little while.",
    " The API encountered an error, but don't worry, it happens. Let's give it another shot later.",
    " It looks like there could be a technical problem with the API. Feel free to try again in a bit to see if things are working smoothly."
];

