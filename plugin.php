<?php
/*
Plugin Name: ICC Meta Redirect
Plugin URI: https://yourls.org/
Description: Use meta tag redirect by adding a prefix before the keyword (default is dot)
Version: 1.0
Author: Ivan Carlos
Author URI: https://ivancarlos.com.br/
*/

// You can safely change this prefix, including special regex chars like '.' 
define( 'ICC_MRDR_URL_PREFIX', '.' );  // example: using dot as prefix
define( 'ICC_MRDR_DELAY', '1' );       // delay in seconds before redirect

yourls_add_action( 'loader_failed', 'icc_mrdr' );

function icc_mrdr( $args ) {
    // Escape prefix for regex safely
    $escaped_prefix = preg_quote( ICC_MRDR_URL_PREFIX, '!' );

    // If request starts with the prefix
    if ( preg_match( '!^' . $escaped_prefix . '(.*)!', $args[0], $matches ) ) {
        $keyword = yourls_sanitize_keyword( $matches[1] );

        // Load YOURLS core
        require_once( dirname( __FILE__ ) . '/../../../includes/load-yourls.php' );
        $url = yourls_get_keyword_longurl( $keyword );
        if ( ! $url ) {
            return; // no redirect if no URL found
        }

        // Output meta refresh redirect and clickable link
        echo '<meta http-equiv="refresh" content="' . ICC_MRDR_DELAY . '; url=' . htmlspecialchars($url, ENT_QUOTES) . '">';
        echo 'You will be redirected to <a href="' . htmlspecialchars($url, ENT_QUOTES) . '">' . htmlspecialchars($url) . '</a>.';
        exit;
    }
    // else no prefix match, normal YOURLS continues.
}
