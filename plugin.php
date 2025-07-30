<?php
/*
Plugin Name: ICC Meta Redirect
Plugin URI: https://github.com/ivancarlosti/yourlsiccmetaredirect
Description: Use meta tag redirect by adding a prefix before the keyword (default is dot).
Version: 1.01
Author: Ivan Carlos
Author URI: https://ivancarlos.com.br/
*/

// No direct call
if( !defined( 'YOURLS_ABSPATH' ) ) die();

// Default redirect delay in seconds (used when option unset)
define( 'ICC_MRDR_DEFAULT_DELAY', 1 );

// Register admin page for settings
yourls_add_action( 'plugins_loaded', 'icc_mrdr_add_page' );
function icc_mrdr_add_page() {
    yourls_register_plugin_page( 'icc_meta_redirect_config', 'Meta Redirect', 'icc_mrdr_do_page' );
}

// Display and handle the plugin settings admin page
function icc_mrdr_do_page() {
    if ( isset($_POST['icc_mrdr_submit']) ) {
        icc_mrdr_update_option();
        echo '<div id="message" class="updated fade"><p>Settings updated!</p></div>';
    }

    // Get saved options or defaults
    $prefix = yourls_get_option('icc_mrdr_url_prefix');
    if ($prefix === false) {
        $prefix = '.';
    }
    $delay = yourls_get_option('icc_mrdr_delay');
    if ($delay === false || !is_numeric($delay) || (int)$delay < 0) {
        $delay = ICC_MRDR_DEFAULT_DELAY;
    }

    $escaped_prefix = htmlspecialchars($prefix, ENT_QUOTES | ENT_HTML5);
    $escaped_delay = (int)$delay;

    echo <<<HTML
<h2>Meta Redirect Settings</h2>
<form method="post">
    <p>
        <label for="icc_mrdr_url_prefix" style="display:inline-block; width:180px;">
            Redirect Prefix Character:
        </label>
        <input type="text" id="icc_mrdr_url_prefix" name="icc_mrdr_url_prefix" value="{$escaped_prefix}" maxlength="1" size="3" />
        <br><small>Single character prefix to trigger meta redirect. Default is a dot (.)</small>
    </p>
    <p>
        <label for="icc_mrdr_delay" style="display:inline-block; width:180px;">
            Redirect Delay (seconds):
        </label>
        <input type="number" id="icc_mrdr_delay" name="icc_mrdr_delay" value="{$escaped_delay}" min="0" step="1" size="3" />
        <br><small>Delay before redirecting. Default is 1 second. Use 0 for immediate redirect.</small>
    </p>
    <p><input type="submit" name="icc_mrdr_submit" value="Save Settings" /></p>
</form>
<hr style="margin-top: 40px" />
<p><strong><a href="https://ivancarlos.me/" target="_blank">Ivan Carlos</a></strong>  &raquo; 
<a href="http://github.com/ivancarlosti/" target="_blank">GitHub</a> &raquo; 
<a href="https://buymeacoffee.com/ivancarlos" target="_blank">Buy Me a Coffee</a></p>
HTML;
}

// Save POSTed options
function icc_mrdr_update_option() {
    if (isset($_POST['icc_mrdr_url_prefix'])) {
        $prefix = substr(trim($_POST['icc_mrdr_url_prefix']), 0, 1); // single char only
        if ($prefix === '') {
            yourls_delete_option('icc_mrdr_url_prefix');
        } else {
            yourls_update_option('icc_mrdr_url_prefix', $prefix);
        }
    }

    if (isset($_POST['icc_mrdr_delay'])) {
        $delay = intval($_POST['icc_mrdr_delay']);
        if ($delay < 0) {
            $delay = ICC_MRDR_DEFAULT_DELAY;
        }
        yourls_update_option('icc_mrdr_delay', $delay);
    }
}

// Hook into loader_failed for meta redirect handling
yourls_add_action('loader_failed', 'icc_mrdr');

function icc_mrdr($args) {
    // Get prefix from option or fallback default
    $prefix = yourls_get_option('icc_mrdr_url_prefix');
    if ($prefix === false || $prefix === '') {
        $prefix = '.';
    }

    // Get delay from option or fallback default
    $delay = yourls_get_option('icc_mrdr_delay');
    if ($delay === false || !is_numeric($delay) || (int)$delay < 0) {
        $delay = ICC_MRDR_DEFAULT_DELAY;
    }
    $delay = (int)$delay;

    // Escape prefix safely for regex
    $escaped_prefix = preg_quote($prefix, '!');

    // Check if requested keyword starts with prefix
    if (preg_match('!^' . $escaped_prefix . '(.*)!', $args[0], $matches)) {
        $keyword = yourls_sanitize_keyword($matches[1]);

        // Load YOURLS core to use the URL functions
        require_once(dirname(__FILE__) . '/../../../includes/load-yourls.php');

        $url = yourls_get_keyword_longurl($keyword);
        if (!$url) {
            return; // No redirect - normal YOURLS continues
        }

        // Output meta refresh redirect with configured delay
        echo '<meta http-equiv="refresh" content="' . $delay . '; url=' . htmlspecialchars($url, ENT_QUOTES) . '">';
        echo 'You will be redirected to <a href="' . htmlspecialchars($url, ENT_QUOTES) . '">' . htmlspecialchars($url) . '</a>.';
        exit;
    }
    // else no prefix match - normal YOURLS continues
}
