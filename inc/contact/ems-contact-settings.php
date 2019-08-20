<?php

final class EMS_contact_settings {
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	} 

	public function __construct() {
		$this->hooks();
	}

	private function hooks() {
		add_action('admin_menu', [$this, 'add_menu'], 99);
		add_action('admin_init', [$this, 'register_settings']);
	}


	public function add_menu() {
		add_submenu_page(
			'options-general.php', 
			'Contact form', 
			'Contact', 
			'manage_options', 
			'shortcode-contact', 
			[$this, 'page']
		);
	}

	public function page() {
		echo '<div style="padding: 30px;"><code>[contact title="" name="" email="" message="" button="" style="(css)" slide=true]</code></div>';
		echo '<form action="options.php" method="POST">';
		settings_fields('em-contact-settings');
		do_settings_sections('shortcode-contact');
		submit_button('save');
		echo '</form>';
	}

	public function register_settings() {
		register_setting('em-contact-settings', 'em_contact', ['sanitize_callback' => [$this, 'sanitize']]);

		add_settings_section('em-shortcode-setting', '', [$this, 'name_section'], 'shortcode-contact');
		add_settings_field('em-shortcode-setting-gfunc', 'Callback URL', [$this, 'input_setting'], 'shortcode-contact', 'em-shortcode-setting', ['gfunc', 'url to gfunc.']);
	}


	public static function sanitize($data) {
		if (!is_array($data)) return wp_kses_post($data);

		$d = [];
		foreach($data as $key => $value)
			$d[$key] = Axowl_settings_se::sanitize($value);

		return $d;
	}

	public function name_section() {
		echo '<h1>Contact form</h1>';
	}

	public function input_setting($d) {
		if (!isset($d[0])) return; 

		$opt = get_option('em_contact');

	    printf(
			'<input type="text" name="em_contact[%s]" value="%s">',
			$d[0],
			isset($opt[$d[0]]) ? $opt[$d[0]] : ''
		);

	}
}