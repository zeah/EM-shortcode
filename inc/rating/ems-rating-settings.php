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
			'Rating Feature Settings',
			'Rating',
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
		echo '<div style="padding: 15px;"><code>[rating style="(css)" close="Avbryt" send="Send" stjerner="stjerner" stjerne="stjerne" text="Skriv en anmeldelse" placeholder-name="Navn (maks 15 tegn)" placeholder-text="Din tekst (maks 40 tegn)" color="hsl(120, 50%, 50%)" color-box="hsl(120, 50%, 50%)" color-star="hsl(120, 50%, 50%)" write=true type="Product (structured data @type)" name="post_title (name of what\'s reviewed)" url="(url of what\'s reviewed)"]</code></div>';
		echo '<div style="padding: 15px;"><code>[rating-overview title="Les hva andre har sagt" style="(css)" inner="(css)" count=15 date=false]</code></div>';
		echo '<button type="button" style="margin: 15px;" class="button em-rating-o-button">Hvis forklaring</button>
			  <div style="display: none; padding: 1rem" class="em-rating-o-container">
			  <ul>
			  	<li>style</li>
			  	<li>close</li>
			  	<li>send</li>
			  	<li>stjerner</li>
			  	<li>stjerne</li>
			  	<li>text</li>
			  	<li>placeholder-name</li>
			  	<li>placeholder-text</li>
			  	<li>color</li>
			  	<li>color-box</li>
			  	<li>color-star</li>
			  	<li>write</li>
			  	<li>type</li>
			  	<li>name</li>
			  	<li>url</li>
			  </ul>

			  <ul>
			    <li>title</li>
			    <li>style</li>
			    <li>inner</li>
			    <li>count</li>
			    <li>date</li>
			  </ul>
			  </div>	
		';
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
		echo '<h2>Rating Settings</h2>';
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