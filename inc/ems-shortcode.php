<?php 


defined('ABSPATH') or die('Blank Space');

require_once 'icons/ems-fa.php';

final class EMS_shortcode {
	/* singleton */
	private static $instance = null;

	private $icon = null; 

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		$this->icon = EMS_fa::get_instance(); // FA shortcode
		// $this->wp_hooks();
	}

	public function wp_hooks() {

	}


}