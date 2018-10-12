<?php 
defined('ABSPATH') or die('Blank Space');


final class Ems_fa_overview {
	/* singleton */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		$this->wp_hooks();
	}

	public function wp_hooks() {
		add_action('admin_menu', array($this, 'add_menu'));
		add_action('admin_enqueue_scripts', array($this, 'add_sands'));
	}

	public function add_sands() {

		$screen = get_current_screen();

		if ($screen->id != 'toplevel_page_em-fa-icons') return;

        wp_enqueue_style('em-icon-style', EM_SHORTCODE_PLUGIN_URL.'assets/css/admin/em-icon.css', array(), '1.0.0');
		wp_enqueue_script('em-icon-script', EM_SHORTCODE_PLUGIN_URL . '/assets/js/admin/em-icon.js', array( 'wp-color-picker' ), false, true);
	}


	public function add_menu() {
		// add_menu_page('Icons', 'FA Icons', 'manage_options', 'em-fa-icons', array($this, 'add_page'), 'data:image/svg+xml;base64,' . base64_encode('<svg width="20" height="20" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg"><path fill="rgba(240,245,250,0.45)" d="M397.8 67.8c7.8 0 14.3 6.6 14.3 14.3v347.6c0 7.8-6.6 14.3-14.3 14.3H50.2c-7.8 0-14.3-6.6-14.3-14.3V82.2c0-7.8 6.6-14.3 14.3-14.3h347.6m0-35.9H50.2C22.7 32 0 54.7 0 82.2v347.6C0 457.3 22.7 480 50.2 480h347.6c27.5 0 50.2-22.7 50.2-50.2V82.2c0-27.5-22.7-50.2-50.2-50.2zm-58.5 139.2c-6 0-29.9 15.5-52.6 15.5-4.2 0-8.4-.6-12.5-2.4-19.7-7.8-37-13.7-59.1-13.7-20.3 0-41.8 6.6-59.7 13.7-1.8.6-3.6 1.2-4.8 1.8v-17.9c7.8-6 12.5-14.9 12.5-25.7 0-17.9-14.3-32.3-32.3-32.3s-32.3 14.3-32.3 32.3c0 10.2 4.8 19.7 12.5 25.7v212.1c0 10.8 9 19.7 19.7 19.7 9 0 16.1-6 18.5-13.7V385c.6-1.8.6-3 .6-4.8V336c1.2 0 2.4-.6 3-1.2 19.7-8.4 43-16.7 65.7-16.7 31.1 0 43 16.1 69.3 16.1 18.5 0 36.4-6.6 52-13.7 4.2-1.8 7.2-3.6 7.2-7.8V178.3c1.8-4.1-2.3-7.1-7.7-7.1z"/></svg>'));
	}

	public function add_page() {
		echo '<div class="em-icon-header"><div class="em-icon-scmaker"></div><input type="search" class="em-icon-input" placeholder="Search.."></div>';

		echo do_shortcode('[icon all]');
	}
}