<?php 


final class EMS_rating_settings {
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	} 

	public function __construct() {
		$this->hooks();
	}

	private function hooks() {
		add_action('admin_menu', [$this, 'add_menu']);
		add_action('admin_init', [$this, 'register_options']);

		add_action('admin_enqueue_scripts', [$this, 'sands']);
	}

	public function add_menu() {

		add_submenu_page(
			'options-general.php',
			'Review Feature Settings',
			'Review',
			'manage_options',
			'em-review-page',
			[$this, 'review']
		);

	}

	public function register_options() {
		register_setting('em-review-setting', 'em_review', ['sanitize_callback' => [$this, 'sanitize']]);

		add_settings_section('em-review-section', '', [$this, 'section'], 'em-review-page');
		add_settings_field('em-review-slack', 'slack webhook', [$this, 'input'], 'em-review-page', 'em-review-section', 'slack');
	}

	public function review() {
		echo '<div style="padding: 30px;"><code>[rating close="Avbryt" send="Send" stjerner="stjerner" stjerne="stjerne" text="Skriv en anmeldelse" color="hsl(120, 50%, 50%)" type="Product (structured data @type)"]</code></div>';
		echo '<div style="padding: 30px;"><code>[rating-overview title="Les hva andre har sagt" style="(css)" inner="(css)" count=15 date=false]</code></div>';
		echo '<form action="options.php" method="POST">';
		settings_fields('em-review-setting');
		do_settings_sections('em-review-page');
		submit_button('save');
		echo '</form>';


		$posts = get_posts(['numberposts' => -1, 'post_type' => ['page', 'post']]);

		$html = '';
		foreach ($posts as $post) {
			$meta = get_post_meta($post->ID, EMS_rating_shortcode::$meta);
			if ($meta) {
				$html .= sprintf('<h3>Page: %s</h3>', $post->post_title);
				$html .= '<div style="display: flex; flex-wrap: wrap;">';
				$h = '';
				foreach ($meta[0] as $m)
					$h = sprintf(
							'<div style="margin: 0 10px 10px 0;" data-id="%s">
								<div class="em-rating-control" style="display: flex;" data-id="%s">
									<button type="button" class="em-rating-button" style="cursor:pointer;" data-val="approve" class="em-rating-approve">Approve</button>
									<button type="button" class="em-rating-button" style="cursor:pointer;" data-val="hide" class="em-rating-hide">Hide</button>
									<button type="button" class="em-rating-button" style="cursor:pointer;" data-val="delete" class="em-rating-delete">Delete</button>
								</div>
								<div class="em-rating-name" style="padding: 5px; background-color: hsl(120, 50%%, 60%%);  border: solid 1px hsl(120, 50%%, 60%%); border-top: none; border-bottom: none;">%s%s</div>
								<div class="em-rating-stars" style="padding: 5px; border: solid 1px hsl(120, 50%%, 60%%); border-top: none; border-bottom: none;">%s</div>
								<div class="em-rating-text" style="padding: 5px; border: solid 1px hsl(120, 50%%, 60%%); border-top: none;">%s</div>
							</div>',
							$post->ID,
							isset($m['id']) ? $m['id'] : '',
							$m['name'],
							' (<span class="em-rating-status">'.$m['status'].'</span>)',
							$m['rating'],
							$m['text']) . $h;

				$html .= $h.'</div>';
			}
		}

		echo $html;
	}

	public function section() {
		echo '<h2>Review Settings</h2>';
	}

	public function input($d) {

		$opt = get_option('em_review');

		printf(
			'<input type="text" name="em_review[%s]" value="%s">',
			$d,
			isset($opt[$d]) ? $opt[$d] : ''			
		);


		// echo 'hi';
	}

	public function sands() {
		$screen = get_current_screen();

		if ($screen->id == 'settings_page_em-review-page') {
			wp_enqueue_script('em-rating-js', SHORTCODE_REVIEW_URL . 'assets/js/admin/em-rating.js', ['jquery'], '1.0.0');
			wp_localize_script('em-rating-js', 'emurl', ['ajax_url' => admin_url( 'admin-ajax.php'), 'sec' => wp_create_nonce('sdlfkj92309urasladfk239')]);
		}

	}


	public static function sanitize($data) {
		if (!is_array($data)) return wp_kses_post($data);

		$d = [];
		foreach($data as $key => $value)
			$d[$key] = self::sanitize($value);

		return $d;
	}
}