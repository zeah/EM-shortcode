<?php 


final class EMS_rating_ajax {
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	} 

	public function __construct() {
		add_action( 'wp_ajax_nopriv_rating', [$this, 'from_form']);
		add_action( 'wp_ajax_rating', [$this, 'from_form']);

		add_action( 'wp_ajax_sett', [$this, 'from_settings']);
	}

	public function from_form() {
		check_ajax_referer('sdlfkj92309urasladfk239', 'security');

		if (!isset($_POST['rating'])
			|| !isset($_POST['nr'])
			|| (!isset($_POST['name']) && !$_POST['name'])
			|| (!isset($_POST['text']) && !$_POST['text'])) {
			echo 500;
			wp_die();
		}

		if (!get_post_status($_POST['nr'])) {
			echo 404;
			wp_die();
		}

		$m = get_post_meta($_POST['nr'], EMS_rating_shortcode::$meta);
		if (isset($m[0])) $m = $m[0];
		else $m = [];

		$m[] = [
			'rating' => $_POST['rating'],
			'name' => preg_replace('/^(?:.{1, 15})(.*)/', '', $_POST['name']),
			'text' => preg_replace('/^(?:.{1, 50})(.*)/', '', $_POST['text']),
			'date' => date('d-m-y'),
			'id' => uniqid(),
			'status' => 'pending'
		];

		update_post_meta($_POST['nr'], EMS_rating_shortcode::$meta, $m);

		$opt = get_option('em_review');

		$msg = 'new review: '.$_POST['loc']."\n".home_url() . '/wp-admin/options-general.php?page=em-review-page';

		if (isset($opt['slack']) && $opt['slack']) {
			$response = wp_remote_post(trim($opt['slack']), [
			    'method'      => 'POST',
			    'timeout'     => 45,
			    'redirection' => 5,
			    'httpversion' => '1.0',
			    'blocking'    => true,
			    'headers'     => [],
				'body' => json_encode(['text' => $msg])
			]);

			print_r($response);
		}

		echo 200;
		wp_die();
	}


	public function from_settings() {
		check_ajax_referer('sdlfkj92309urasladfk239', 'security');

		$button = $_POST['button'];
		$id = $_POST['id'];
		$postid = $_POST['postid'];

		$g_meta = get_post_meta($postid, EMS_rating_shortcode::$meta);
		if (isset($g_meta[0])) $g_meta = $g_meta[0];
		else wp_die();

		$meta = [];
		foreach($g_meta as $m)
			$meta[] = $m;

		for ($i = 0; $i < sizeof($meta); $i++) 
			if ($meta[$i]['id'] == $id) {
				if ($button == 'delete') unset($meta[$i]);
				else $meta[$i]['status'] = $button;
			}

		$meta_new = [];
		foreach($meta as $m)
			$meta_new[] = $m;

		print_r($meta_new);

		update_post_meta($postid, EMS_rating_shortcode::$meta, $meta_new);

		wp_die();
	}

}