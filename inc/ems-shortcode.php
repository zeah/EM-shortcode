<?php 


defined('ABSPATH') or die('Blank Space');

require_once 'icons/ems-fa.php';
require_once 'contact/ems-contact-shortcode.php';
require_once 'rating/ems-rating-shortcode.php';

final class EMS_shortcode {
	/* singleton */
	private static $instance = null;

	private $icon = null; 

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		EMS_fa::get_instance(); // FA shortcode
		EMS_contact_shortcode::get_instance();
		EMS_rating_shortcode::get_instance();
		// $this->wp_hooks();
	}

	public function wp_hooks() {

	}


}