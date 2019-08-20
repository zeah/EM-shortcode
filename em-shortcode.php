<?php
/*
Plugin Name: EM Shortcode
Description: Shortcodes
Version: 0.0.5
GitHub Plugin URI: zeah/EM-shortcode
*/

defined('ABSPATH') or die('Blank Space');

// constant for plugin location
define('EM_SHORTCODE_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once 'inc/ems-shortcode.php';

function init_emshortcode() {
	EMS_shortcode::get_instance();
}
add_action('plugins_loaded', 'init_emshortcode');