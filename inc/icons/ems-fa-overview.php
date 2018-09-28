<?php 
defined('ABSPATH') or die('Blank Space');


final class Ems_fa_overview {
	/* singleton */
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	}

	private function __construct() {
		$this->wp_hooks();
	}

	public function wp_hooks() {
		add_action('admin_menu', array($this, 'add_menu'));
		// add_action('admin_head-toplevel_page_em-fa-icons', array($this, 'add_head'));
		add_action('admin_enqueue_scripts', array($this, 'add_sands'));
		// add_action('admin_footer-toplevel_page_em-fa-icons', array($this, 'add_footer'));

	}

	public function add_sands() {

		$screen = get_current_screen();

		if ($screen->id != 'toplevel_page_em-fa-icons') return;

        wp_enqueue_style('em-icon-style', EM_SHORTCODE_PLUGIN_URL.'assets/css/admin/em-icon.css', array(), '1.0.0');
		wp_enqueue_script('em-icon-script', EM_SHORTCODE_PLUGIN_URL . '/assets/js/admin/em-icon.js', array( 'wp-color-picker' ), false, true);
	}

	public function add_head() {

		echo '<style>
				.em-icon-header {
					display: flex;
					justify-content: flex-end;
					padding: 30px;

					position: sticky;
					top: 32px;
				}

				.em-icon-scmaker {
					position: relative;
					display: none;
					/*flex-direction: column;*/
					align-items: center;
					justify-content: space-between;

					background-color: #fff;
					padding: 2rem 4rem;
					border: solid 2px #ccc;
					border-radius: 20px;	

					flex: 1;
					margin-right: 50px;
				}

				.em-icon-button {
					position: absolute;
					top: 0;
					left: 0;
					width: 100px;
					height: 60px;
					border: none;
					background-color: #666;
					border-top-left-radius: 18px;
					font-size: 26px;
					cursor: pointer;
					color: #fff;
				}

				.em-icon-button:focus {
					border: none;
					outline: none;
					box-shadow: none;
				}

				.em-icon-svgc {
					flex-basis: 30%;
					display: flex;
					justify-content: center;
				}

				.em-icon-shortcode {
					font-size: 22px;
					margin-right: auto;
				}

				.em-icon-sctext {
					font-size: 22px;
					width: 400px;
					border: none;
				}

				.em-icon-sizecontrol {
				}

				.em-icon-range {
					-webkit-appearance: none;
				    height: 200px;
				    border-radius: 5px;
				    background: #ccc;
				    outline: none;
				    writing-mode: bt-lr; /* IE */
				    -webkit-appearance: slider-vertical; /* WebKit */
				}

				.em-icon-input {
					font-size: 20px;
					height: 40px;
					box-shadow: none !important;
					border-radius: 50px;
					padding: 5px 20px 6px;
				}

			    .em-icon-input::-webkit-search-cancel-button:hover { 
			        cursor:pointer; 
			    }

				.em-icon-input:focus {
					outline: none !important;
					box-shadow: none !important;
					border-color: #ddd !important;
				}


				.em-svg { 
					vertical-align: middle; 
					width: 64px; 
					height: 64px; 
				} 
				.em-icon-container { 
					margin: 1rem; display: inline-flex; 
					flex-direction: column; 
					align-items: center; 
					margin: 0 5rem; 
					margin-bottom: 2rem;
					width: 9rem;
				}
				.em-icon-container:hover {
					background-color: #fff;
					cursor: pointer;
				}

				.em-icon-list {
					display: flex;
					flex-wrap: wrap;
					justify-content: left;
				}
				.em-icon-title {
					width: 100%;
					margin-top: 3rem;
				}

				#wpfooter {
					position: static;
				}

				.em-icon-scmaker .wp-color-result {
					display: none;
				}

			</style>';
	}

	// public function add_footer() {
	// 	echo '<script>

	// 		(() => {

	// 			let title = document.querySelectorAll(".em-icon-name");

	// 			if (!title) return;

	// 			let input = document.querySelector(".em-icon-input");

	// 			if (!input) return;

	// 			input.addEventListener("input", (e) => {

	// 				let v = e.target.value;

	// 				if (v.length == 0) {

	// 					for (let c of title)
	// 						c.parentNode.style.display = "flex";
	// 					return;
	// 				}


	// 				if (v.length < 3) return;

	// 				for (let c of title) { 
	// 					if (c.innerHTML.indexOf(v) == -1)
	// 						c.parentNode.style.display = "none";
	// 					else c.parentNode.style.display = "flex";
	// 				}
	// 			});

	// 			let maker = document.querySelector(".em-icon-scmaker");

	// 			if (!maker) return;

	// 			let container = document.querySelectorAll(".em-icon-container");
	// 			if (!container) return;

	// 			let sizeControl = document.createElement("div");

	// 			let sizeInput = document.createElement("input");
	// 			sizeInput.setAttribute("type", "range");

	// 			sizeControl.appendChild(sizeInput);

	// 			for (let c of container)
	// 				c.addEventListener("click", () => {
	// 					while (maker.firstChild) 
 // 						   maker.removeChild(maker.firstChild);

 // 						let svg = c.querySelector(".em-svg").cloneNode(true);

 // 						let path = svg.querySelector(".em-path");
 // 						path.setAttribute("fill", "#000");

	// 					maker.appendChild(svg);

	// 					let shortcode = document.createElement("div");
	// 					shortcode.appendChild(document.createTextNode("[icon "+c.querySelector(".em-icon-name").innerHTML+"]"));
	// 					shortcode.style.textAlign = "center";
	// 					maker.appendChild(shortcode);

	// 					maker.appendChild(sizeControl);
	// 				});


	// 		})();

	// 	</script>';
	// }

	public function add_menu() {
		add_menu_page('Icons', 'FA Icons', 'manage_options', 'em-fa-icons', array($this, 'add_page'), 'data:image/svg+xml;base64,' . base64_encode('<svg width="20" height="20" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg"><path fill="rgba(240,245,250,0.45)" d="M397.8 67.8c7.8 0 14.3 6.6 14.3 14.3v347.6c0 7.8-6.6 14.3-14.3 14.3H50.2c-7.8 0-14.3-6.6-14.3-14.3V82.2c0-7.8 6.6-14.3 14.3-14.3h347.6m0-35.9H50.2C22.7 32 0 54.7 0 82.2v347.6C0 457.3 22.7 480 50.2 480h347.6c27.5 0 50.2-22.7 50.2-50.2V82.2c0-27.5-22.7-50.2-50.2-50.2zm-58.5 139.2c-6 0-29.9 15.5-52.6 15.5-4.2 0-8.4-.6-12.5-2.4-19.7-7.8-37-13.7-59.1-13.7-20.3 0-41.8 6.6-59.7 13.7-1.8.6-3.6 1.2-4.8 1.8v-17.9c7.8-6 12.5-14.9 12.5-25.7 0-17.9-14.3-32.3-32.3-32.3s-32.3 14.3-32.3 32.3c0 10.2 4.8 19.7 12.5 25.7v212.1c0 10.8 9 19.7 19.7 19.7 9 0 16.1-6 18.5-13.7V385c.6-1.8.6-3 .6-4.8V336c1.2 0 2.4-.6 3-1.2 19.7-8.4 43-16.7 65.7-16.7 31.1 0 43 16.1 69.3 16.1 18.5 0 36.4-6.6 52-13.7 4.2-1.8 7.2-3.6 7.2-7.8V178.3c1.8-4.1-2.3-7.1-7.7-7.1z"/></svg>'));
	}

	public function add_page() {
		echo '<div class="em-icon-header"><div class="em-icon-scmaker"></div><input type="search" class="em-icon-input" placeholder="Search.."></div>';

		echo do_shortcode('[icon all]');
	}
}