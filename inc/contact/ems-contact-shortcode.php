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


	public function add_shortcode($atts, $content = null) {
		add_action('wp_enqueue_scripts', [$this, 'add_css']);

		return sprintf(
			'<form class="em-contact-form"%s>
				<input class="em-contact-phone" type="hidden" name="phone">
				<h2 class="em-contact-title">Kontakt oss</h2>
				<div class="em-contact-thanks">Takk for din kontakt. Vi vil kontakte deg snarest på e-post.</div>
				<div class="em-contact-part em-contact-part-name">
				    <h4 class="em-contact-part-title">Navn</h4>
					<input class="em-contact-name em-contact-input" type="text" name="name">
				</div>
				<div class="em-contact-part em-contact-part-email">
				    <h4 class="em-contact-part-title">Epost</h4>
					<input class="em-contact-email em-contact-input" type="text" name="email">
				</div>
				<div class="em-contact-part em-contact-part-message">
				    <h4 class="em-contact-part-title">Melding</h4>
					<textarea class="em-contact-message em-contact-input" name="message"></textarea>
				</div>
				<div class="em-contact-part em-contact-part-button">
					<button class="em-contact-button" type="button">Send Melding</button>
				</div>
			</form>',

			isset($atts['style']) ? ' style="'.$atts['style'].'"' : ''
		);
	}

	public function add_css() {
        wp_enqueue_style('contact-style', SHORTCODE_CONTACT_URL.'assets/css/pub/em-contact.css', [], '1.0.0', '(min-width: 951px)');
        wp_enqueue_style('contact-mobile', SHORTCODE_CONTACT_URL.'assets/css/pub/em-contact-mobile.css', [], '1.0.0', '(max-width: 950px)');
        wp_enqueue_script('contact-js', SHORTCODE_CONTACT_URL.'assets/js/pub/em-contact.js', ['jquery'], '1.0.0', '(max-width: 950px)');
		wp_localize_script('contact-js', 'emurl', ['ajax_url' => admin_url( 'admin-ajax.php')]);
	}
}