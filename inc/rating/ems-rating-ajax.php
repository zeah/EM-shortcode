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
	}

	public function from_form() {

		if (!isset($_POST['rating'])
			|| !isset($_POST['nr'])
			|| (!isset($_POST['name']) && !$_POST['name'])
			|| (!isset($_POST['text']) && !$_POST['text'])) {
			echo 500;
			exit;
		}

		if (!post_exist($_POST['nr'])) {
			echo 404;
			exit;
		}

		$m = get_post_meta($_POST['nr'], 'em_rating_test');
		if (isset($m[0])) $m = $m[0];
		else $m = [];

		$m[] = [
			'rating' => $_POST['rating'],
			'name' => $_POST['name'],
			'text' => $_POST['text'],
			'status' => 'pending'
		];

		print_r($m);

		update_post_meta($_POST['nr'], 'em_rating_test', $m);		

		echo 200;
		exit;
	}
}