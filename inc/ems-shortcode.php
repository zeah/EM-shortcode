<?php 


defined('ABSPATH') or die('Blank Space');

require_once 'shortcodes/ems-svg.php';

final class EMS_shortcode {
	/* singleton */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		EMS_svg::get_instance();
		add_action('wp_head', array($this, 'add_head'));
	}

	public function add_head() {
		// echo '<style>.em-svg { vertical-align: middle; } </style>';
		echo '<style>.em-svg { 
				vertical-align: middle; 
				width: 64px; 
				height: 64px; 
			} 
				.icon-container { 
					margin: 1rem; display: inline-flex; 
					flex-direction: column; 
					align-items: center; 
					margin-bottom: 2rem; 
				}
				.icon-list {
					display: grid;
					grid-template-columns: 1fr 1fr 1fr 1fr 1fr;
				}

			</style>';
	}

}