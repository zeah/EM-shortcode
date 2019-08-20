<?php 


final class EMS_contact_ajax {
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	} 

	public function __construct() {
		$this->hooks();
	}

	private function hooks() {
		add_action( 'wp_ajax_nopriv_con', [$this, 'from_form']);
		add_action( 'wp_ajax_con', [$this, 'from_form']);
	}

	public function from_form() {

		if (isset($_POST['phone'])
			|| !isset($_POST['name'])
			|| !isset($_POST['email'])
			|| !isset($_POST['message'])
			|| !$_POST['name']
			|| !$_POST['email']
			|| !$_POST['message'])
			exit;


		$msg = "\n\n#####################\n\n".'SIDE: '.$_POST['side']."\n".'NAVN: '.$_POST['name']."\nEPOST: ".$_POST['email']."\n\nMELDING:\n".$_POST['message']."\n\n#####################\n\n";


		$opt = get_option('em_contact');

		if (!isset($opt['gfunc']) || !$opt['gfunc']) {
			echo 'no url set.';
			exit;
		}
		
		echo 1;
		
		$response = wp_remote_post(trim($opt['gfunc']), [
			    'method'      => 'POST',
			    'timeout'     => 45,
			    'redirection' => 5,
			    'httpversion' => '1.0',
			    'blocking'    => true,
			    'headers'     => [],
				'body' => json_encode(['text' => $msg])
		]);


		exit;
	}
 }