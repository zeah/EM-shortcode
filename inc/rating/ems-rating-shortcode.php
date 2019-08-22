<?php 

require_once 'ems-rating-ajax.php';
require_once 'ems-rating-settings.php';

final class EMS_rating_shortcode {
	private static $instance = null;

	public static function get_instance() {
		if (self::$instance === null) self::$instance = new self();

		return self::$instance;
	} 

	public function __construct() {

		EMS_rating_ajax::get_instance();
		EMS_rating_settings::get_instance();

		if (!shortcode_exists('rating')) add_shortcode('rating', [$this, 'add_shortcode']);
		else add_shortcode('ems_rating', [$this, 'add_shortcode']);


		if (!shortcode_exists('rating-overview')) add_shortcode('rating-overview', [$this, 'add_shortcode_overview']);
		else add_shortcode('ems-rating-overview', [$this, 'add_shortcode_overview']);
	}

	public function add_shortcode($atts, $content = null) {

		add_action('wp_footer', [$this, 'add_footer']);
		add_action('wp_head', [$this, 'add_head']);
		wp_enqueue_script('jquery');

		global $post;

		if (!$post) return;

		$meta = get_post_meta($post->ID, 'em_rating_test');

		if (isset($meta[0])) $meta = $meta[0];
		else $meta = false;

		// return print_r($meta, true);

		$size = $meta ? sizeof($meta) : '0';
		$rating = 0;
		$sum = 0;

		// wp_die('<xmp>'.print_r($meta, true).'</xmp>');
		
		if ($meta) {
			foreach ($meta as $m)
				$sum += $m['rating'];

			$rating = round($sum / $size);
		}

		switch ($rating) {
			case 6: $stars = $this->stars(6); break;
			case 5: $stars = $this->stars(5); break;
			case 4: $stars = $this->stars(4); break;
			case 3: $stars = $this->stars(3); break;
			case 2: $stars = $this->stars(2); break;
			case 1: $stars = $this->stars(1); break;
			default: $stars = $this->stars(0);
		}


		return sprintf(
			'<div class="em-rating-container">
			    <div class="em-rating-stars" data-sum="%s" data-size="%s">%s<span class="em-rating-count-container">(<span class="em-rating-count">%s</span>)</span></div>
			    <div class="em-rating-review">%s</div>
			    <div class="em-rating-write">
			    	<form>
			    	<select class="em-rating-starsgiven">
			    		<option value="6">6 stjerner</option>
			    		<option value="5">5 stjerner</option>
			    		<option value="4">4 stjerner</option>
			    		<option value="3">3 stjerner</option>
			    		<option value="2">2 stjerner</option>
			    		<option value="1">1 stjerne</option>
			    	</select>
			    	<input class="em-rating-name" type="text" placeholder="Navn">
			    	<textarea class="em-rating-text" placeholder="Din text"></textarea>
			    	<button class="em-rating-send" type="button">send</button>
			    	<button class="em-rating-close" type="button">close</button>
			    	</form>
			    </div>
			</div>',
			$sum,
			$size,
			$stars,
			$size,
			'Skriv en review'
		);
	}

	public function add_shortcode_overview($atts, $content = null) {
		global $post;

		$meta = get_post_meta($post->ID, 'em_rating_test');

		// wp_die('<xmp>'.print_r($meta, true).'</xmp>');
		

		if (isset($meta[0])) $meta = $meta[0];
		else return;

		$html = '';

		$count = 12;

		if (isset($atts['count'])) $count = $atts['count'];

		if (sizeof($meta) < $count) $count = sizeof($meta);

		// wp_die('<xmp>'.print_r($count, true).'</xmp>');
		

		for ($i = 0; $i < $count; $i++) {
		// foreach ($meta as $m) {
			$m = $meta[$i];

			$html .= sprintf(
				'<div class="em-ro-inner">
					<div class="em-ro-name">%s</div>
					<div class="em-ro-rating">%s</div>
					<div class="em-ro-text">%s</div>
				</div>',
				$m['name'],
				$this->stars($m['rating']),
				$m['text']
			);	
		}

		return sprintf(
			'<div class="em-ro-container">%s</div>', $html
		);
	}

	public function add_head() {
		printf(
			"<style>
				.em-rating-container {
					display: inline-block;
					position: relative;

					padding: .5rem;
					z-index: 999;
				}

				.em-rating-star {
					fill: hsl(120, 100%%, 30%%);
					filter: drop-shadow( 0 0 1px #333);
				}

				.em-rating-blank-star {
					fill: hsl(0, 0%%, 100%%);
					filter: drop-shadow( 0 0 1px #333);
				}

				.em-rating-count-container {
					position: relative;
					bottom: 4px;
					left: 10px;
					color: #666;
				}

				.em-rating-review {
					font-size: 1.4rem;
					cursor: pointer;
				}

				.em-rating-write {
					display: none;
					position: absolute;
					background-color: #fff;
					top: 0;
					left: 0;
					right: -150px;

					padding: 2rem;
					border: solid 1px #ccc;
				} 

				.em-rating-starsgiven {
					width: 100%%;

					font-size: 2.4rem;
				}

				.em-rating-name {
					font-size: 1.6rem;
					margin: 2rem 0;
					width: 100%%;
					padding: .5rem;
				}

				.em-rating-text {
					font-size: 1.6rem;
					width: 100%%;
					resize: vertical;
					height: 100px;

					font-family: Arial;
					padding: .5rem;
				}

				.em-rating-send {
					font-size: 2.6rem;
					float: right;

				}

				.em-rating-close {
					font-size: 2.6rem;

				}

				.em-ro-container {
					display: flex;
					flex-wrap: wrap;
				}

				.em-ro-inner {
					border: solid 2px hsl(120, 50%%, 50%%);
					margin: 0 1rem 1rem 0;

					width: 30rem;
					height: 15rem;
					background-color: hsl(120, 50%%, 99%%)
				}

				.em-ro-name {
					background-color: hsl(120, 50%%, 50%%);
					color: white;

					padding: .5rem;

					font-weight: 700;
					text-transform: capitalize;
				}

				.em-ro-rating {
					padding: .5rem;
				}

				.em-ro-text {
					padding: .5rem;
				}

			</style>");
	}

	public function add_footer() {
		global $post;
		printf("<script>
				var emajax = '%s';
				jQuery(function($) {

					var star = $('.em-rating-star').first();
					var blankStar = $('.em-rating-blank-star').first();

					// console.log(star);

					$('.em-rating-review').click(function() {
						$('.em-rating-write').fadeIn(200);
					});

					$('.em-rating-close').click(function() {
						$('.em-rating-write').hide();
					});

					$('.em-rating-send').one('click', function() {

						var starsGiven = $('.em-rating-starsgiven').val();

						$.post(emajax, {
							action: 'rating',
							rating: starsGiven,
							name: $('.em-rating-name').val(),
							text: $('.em-rating-text').val(),
							nr: %s
						}, function(data) {	
							console.log(data);

							var inner = $('.em-ro-inner').first();

							if (inner) {
								var clone = inner.clone();

								var stars = clone.find('.em-ro-rating');
								stars.empty();

								for (var i = 0; i < 6; i++) {
									if (i < starsGiven) stars.append(star.clone());
									else stars.append(blankStar.clone());
								}

								clone.find('.em-ro-name').text($('.em-rating-name').val());
								// clone.find('.em-ro-rating').html(stars);
								clone.find('.em-ro-text').text($('.em-rating-text').val());


								$('.em-ro-container').prepend(clone);
								$(clone).hide();

								$(clone).show(1000);

							}
							// copy .em-ro-inner -> change name, stars and text and add one new
							// update average star rating
							// update review counter
						});

						$('.em-rating-count').text(parseInt($('.em-rating-count').text()) + 1);
						$('.em-rating-write, .em-rating-review').hide();
					});
				});

			</script>",
			admin_url('admin-ajax.php'),
			$post->ID
		);
		// wp_localize_script('contact-js', 'emurl', ['ajax_url' => admin_url( 'admin-ajax.php')]);

	}


	private function stars($nr) {
		// wp_die('<xmp>'.print_r($nr, true).'</xmp>');
		$star = sprintf('<svg class="%1$s-star" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path class="%1$s-star-path" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/><path d="M0 0h24v24H0z" fill="none"/></svg>', 'em-rating');
		$star_blank = sprintf('<svg class="%1$s-star" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path class="%1$s-star-path" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/><path d="M0 0h24v24H0z" fill="none"/></svg>', 'em-rating-blank');

		$stars = '';
		switch ($nr) {
			case 6: $stars = $star.$star.$star.$star.$star.$star; break;
			case 5: $stars = $star.$star.$star.$star.$star.$star_blank; break;
			case 4: $stars = $star.$star.$star.$star.$star_blank.$star_blank; break;
			case 3: $stars = $star.$star.$star.$star_blank.$star_blank.$star_blank; break;
			case 2: $stars = $star.$star.$star_blank.$star_blank.$star_blank.$star_blank; break;
			case 1: $stars = $star.$star_blank.$star_blank.$star_blank.$star_blank.$star_blank; break;
			default: $stars = $star_blank.$star_blank.$star_blank.$star_blank.$star_blank.$star_blank.$star_blank;
		}

		// wp_die('<xmp>'.print_r($stars, true).'</xmp>');
		return $stars;
	}
}