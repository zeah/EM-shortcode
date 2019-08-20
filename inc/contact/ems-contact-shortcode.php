<?php 

require_once 'ems-contact-settings.php';
require_once 'ems-contact-ajax.php';

define('SHORTCODE_CONTACT_URL', plugin_dir_url(__FILE__));

final class EMS_contact_shortcode {
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	} 

	public function __construct() {
		EMS_contact_settings::get_instance();
		EMS_contact_ajax::get_instance();

		$this->hooks();
	}

	private function hooks() {
		if (!shortcode_exists('contact')) add_shortcode('contact', [$this, 'add_shortcode']);
		else add_shortcode('em-contact', [$this, 'add_shortcode']);
	}


	private function atts($atts, $name, $default) {
		return isset($atts[$name]) ? $atts[$name] : $default;
	}

	public function add_shortcode($atts, $content = null) {
		add_action('wp_enqueue_scripts', [$this, 'add_css']);

		return sprintf(
			'<form class="em-contact-form"%s>
				<input class="em-contact-phone" type="hidden" name="phone">
				<h2 class="em-contact-title"><span class="%s">%s</span></h2>
				<div class="em-contact-thanks">%s</div>
				<div class="em-contact-part em-contact-part-name%9$s">
				    <h4 class="em-contact-part-title">%s</h4>
					<input class="em-contact-name em-contact-input" type="text" name="name">
				</div>
				<div class="em-contact-part em-contact-part-email%9$s">
				    <h4 class="em-contact-part-title">%s</h4>
					<input class="em-contact-email em-contact-input" type="text" name="email">
				</div>
				<div class="em-contact-part em-contact-part-message%9$s">
				    <h4 class="em-contact-part-title">%s</h4>
					<textarea class="em-contact-message em-contact-input" name="message"></textarea>
				</div>
				<div class="em-contact-part em-contact-part-button%9$s">
					<button class="em-contact-button" type="button">%s</button>
				</div>
			</form>',

			isset($atts['style']) ? ' style="'.$atts['style'].'"' : '',
			isset($atts['slide']) ? ' em-contact-title-slide' : '',
			$this->atts($atts, 'title', 'Kontakt oss'),
			$this->atts($atts, 'thanks', 'Takk for din kontakt. Vi vil kontakte deg snarest på e-post.'),
			$this->atts($atts, 'name', 'Navn'),
			$this->atts($atts, 'email', 'Epost'),
			$this->atts($atts, 'message', 'Melding'),
			$this->atts($atts, 'button', 'Send Melding'),
			isset($atts['slide']) ? ' em-contact-slide' : ''

		);
	}

	public function add_css() {
        wp_enqueue_style('contact-style', SHORTCODE_CONTACT_URL.'assets/css/pub/em-contact.css', [], '1.0.0', '(min-width: 951px)');
        wp_enqueue_style('contact-mobile', SHORTCODE_CONTACT_URL.'assets/css/pub/em-contact-mobile.css', [], '1.0.0', '(max-width: 950px)');
        wp_enqueue_script('contact-js', SHORTCODE_CONTACT_URL.'assets/js/pub/em-contact.js', ['jquery'], '1.0.2');
		wp_localize_script('contact-js', 'emurl', ['ajax_url' => admin_url( 'admin-ajax.php')]);
	}
}