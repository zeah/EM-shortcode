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
		$this->icon = EMS_fa::get_instance();
		$this->wp_hooks();
	}

	public function wp_hooks() {
		add_action('wp_head', array($this, 'add_head'));

		if (!shortcode_exists('icon')) add_shortcode('icon', array($this, 'icons'));
		
		elseif (!shortcode_exists('em-icon')) add_shortcode('em-icon', array($this, 'icons'));
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
					width: 15rem; 
				}
				.icon-list {
					display: flex;
					flex-wrap: wrap;
					justify-content: space-between;
				}
				.icon-title {
					width: 100%;
					margin-top: 10rem;
				}

			</style>';
	}

	public function icons($atts, $content = null) {
		// return 'hi';
		// if (!is_array($atts)) return $this->icon->get_all();

		// wp_die('<xmp>'.print_r($this->icon->get_icon('bell'), true).'</xmp>');
		
		if ($atts[0] == 'all') return $this->icon->get_all();

		// return sprintf($this->icon->get_icon('bell'),
		// 				'class="em-svg em-svg-'.$key.'"', 
		// 				'class="em-path em-path-'.$key.'"'
		// 				);

	}

}