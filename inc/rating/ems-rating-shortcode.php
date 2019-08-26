<?php 

require_once 'ems-rating-ajax.php';
require_once 'ems-rating-settings.php';

define('SHORTCODE_REVIEW_URL', plugin_dir_url(__FILE__));


final class EMS_rating_shortcode {
	private static $instance = null;

	private $css_added = false;

	public static $meta = 'em_rating_test_3';

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

		// wp_die('<xmp>'.print_r(date('d-m-y'), true).'</xmp>');
		

		wp_enqueue_script('jquery');
		add_action('wp_footer', [$this, 'add_footer'], 100);

		$this->add_css($atts);


		// add_action('wp_head', [$this, 'add_css']);

		global $post;

		if (!$post) return;

		$meta = get_post_meta($post->ID, self::$meta);

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

		$thing = isset($atts['type']) ? $atts['type'] : 'Product';
		$name = isset($atts['name']) ? $atts['name'] : $post->post_title;
		$url = isset($atts['url']) ? $atts['url'] : false;

		add_action('wp_footer', function() use ($rating, $size, $thing, $name, $url) {
			$json = [
				'@context' => 'https://schema.org/',
				'@type' => 'WebPage',
				'aggregateRating' => [
					'@type' => 'AggregateRating',
					'itemReviewed' => [
						'@type' => $thing,
						'name' => $name
					],
					'ratingValue' => $rating,
					'bestRating' => '6',
					'worstRating' => '1',
					'ratingCount' => $size
				]
			];

			if ($url) $json['aggregateRating']['itemReviewed']['url'] = $url;

			printf(
				'<script type="application/ld+json">%s</script>',
				json_encode($json, JSON_PRETTY_PRINT)
			);
		});


		return sprintf(
			'<div class="em-rating-container"%10$s>
			    <div class="em-rating-stars" data-sum="%s" data-size="%s">%s<span class="em-rating-count-container">(<span class="em-rating-count">%s</span>)</span></div>
			    <div class="em-rating-review"%11$s>%s</div>
			    <div class="em-rating-write">
			    	<form>
			    	<select class="em-rating-starsgiven">
			    		<option value="6">6 %6$s</option>
			    		<option value="5">5 %6$s</option>
			    		<option value="4">4 %6$s</option>
			    		<option value="3">3 %6$s</option>
			    		<option value="2">2 %6$s</option>
			    		<option value="1">1 %7$s</option>
			    	</select>
			    	<input class="em-rating-name" type="text" placeholder="%12$s">
			    	<textarea class="em-rating-text" placeholder="%13$s"></textarea>
			    	<button class="em-rating-button em-rating-send" type="button">%8$s</button>
			    	<button class="em-rating-button em-rating-close" type="button">%9$s</button>
			    	</form>
			    </div>
			</div>',
			$sum,
			$size,
			$stars,
			$size,
			isset($atts['text']) ? $atts['text'] : 'Skriv en anmeldelse',
			isset($atts['stjerner']) ? $atts['stjerner'] : 'stjerner',
			isset($atts['stjerne']) ? $atts['stjerne'] : 'stjerne',
			isset($atts['send']) ? $atts['send'] : 'Send',
			isset($atts['close']) ? $atts['close'] : 'Avbryt',
			isset($atts['style']) ? ' style="'.$atts['style'].'"' : '',
			isset($atts['write']) ? ' style="display: none;"' : '',
			isset($atts['placeholder-name']) ? $atts['placeholder-name'] : 'Navn (maks 15 tegn)',
			isset($atts['placeholder-text']) ? $atts['placeholder-text'] : 'Din tekst (maks 40 tegn)'
		);
	}

	public function add_shortcode_overview($atts, $content = null) {
		global $post;
		// add_action('wp_head', [$this, 'add_css']);
		// 
		$this->add_css($atts);

		// if (!has_shortcode($post->post_content, 'rating')) return;

		$meta = get_post_meta($post->ID, self::$meta);

		if (!isset($meta[0])) return;
		$meta = $meta[0];
		if (!$meta) return;

		$html = '';

		$count = 12;

		if (isset($atts['count'])) $count = $atts['count'];

		if (sizeof($meta) < $count) $count = sizeof($meta);

		$c = 0;
		foreach ($meta as $m) {
			if ($c >= $count) break;
			if ($m['status'] != 'approve') continue;
			$c++;

			// if (strpos(' ', $m['name']) !== 0) 
			$m['name'] = preg_replace('/(.*?\s\w)(.*)/', '$1', $m['name']);

			$html = sprintf(
				'<div class="em-ro-inner"%s>
					<div class="em-ro-name">%s</div>
					<div class="em-ro-rating">%s</div>
					<div class="em-ro-text">%s</div>
					%s
				</div>',
				isset($atts['inner']) ? ' style="'.$atts['inner'].'"' : '',
				$m['name'],
				$this->stars($m['rating']),
				$m['text'],
				(isset($m['date']) && isset($atts['date']) && $atts['date'] == 'true') ? '<div class="em-ro-date">'.$m['date'].'</div>' : ''
			) . $html;	
		}

		// wp_die('<xmp>'.print_r($html, true).'</xmp>');

		if (!$html) return;

		return sprintf(
			'<h2 class="em-ro-title">%s</h2><div class="em-ro-container"%s>%s</div>',
			isset($atts['title']) ? $atts['title'] : 'Les hva andre har sagt',
			isset($atts['style']) ? ' style="'.$atts['style'].'"' : '', 
			$html
		);
	}

	public function add_css($atts) {

		if ($this->css_added) return;
		$this->css_added = true;


		$css = '<style>
					.em-rating-container {
						display: inline-block;
						position: relative;
						z-index: 999;
					}

					.em-rating-star {
						fill: %3$s;
						filter: drop-shadow( 0 0 1px #888);
					}

					.em-rating-blank-star {
						fill: hsl(0, 0%%, 100%%);
						filter: drop-shadow( 0 0 1px #888);
					}

					.em-rating-count-container {
						position: relative;
						bottom: 8px;
						left: 10px;
						color: #666;
						font-size: 2rem
					}

					.em-rating-review {
						font-size: 1.6rem;
						cursor: pointer;
					}

					.em-rating-write {
						display: none;
						position: absolute;
						background-color: %2$s;
						top: 0;
						left: 0;
						right: -150px;

						padding: 2rem;
					} 

					.em-rating-starsgiven {
						width: 100%%;

						padding: .5rem;
						font-size: 1.6rem;
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

					.em-rating-button {
						cursor: pointer;
							
						padding: .5rem 1rem;
						font-size: 1.6rem;


						border: solid 2px hsl(197, 71%%, 73%%);
						background-color: white;
					}

					.em-rating-button:hover {
						border: solid 2px hsl(197, 31%%, 63%%);
					}

					.em-rating-send {
						float: right;

					}

					.em-rating-close {
						float: left;
					}

					.em-ro-title {
						margin: 0;
						margin-top: 30px;
						user-select: none;
					}

					.em-ro-container {
						display: flex;
						flex-wrap: wrap;
						user-select: none;
					}


					.em-ro-inner {
						position: relative;
						width: 30rem;
						height: 15rem;
						margin: 0 1rem 1rem 0;

						border: solid 2px %1$s;
					}

					.em-ro-name {
						padding: .5rem;
						background-color: %1$s;

						color: white;
						font-weight: 700;
						text-transform: capitalize;
					}

					.em-ro-rating {
						padding: .5rem;
					}

					.em-ro-text {
						padding: .5rem;
					}

					.em-ro-date {
						position: absolute;
						bottom: 0;
						right: 5px;

						font-size: 1.2rem;
					}

					@media only screen and (max-width: 850px) {

						.em-rating-container {
							width: 100%%;
							text-align: center;
						}

						.em-rating-write {
							left: 1rem;
							right: 1rem;
						}

						.em-ro-container {
							justify-content: center;
						}
					}
				</style>';
		
		if (!did_action('wp_head'))
			add_action('wp_head', function() use ($atts, $css) {
				printf($css, 
					isset($atts['color']) ? $atts['color'] : 'hsl(120, 50%, 50%)',
					isset($atts['color-box']) ? $atts['color-box'] : (isset($atts['color']) ? $atts['color'] : 'hsl(120, 50%, 50%)'),
					isset($atts['color-star']) ? $atts['color-star'] : (isset($atts['color']) ? $atts['color'] : 'hsl(120, 50%, 50%)')
				);
			});
		else
			add_action('wp_footer', function() use ($atts, $css) {
				printf(
					'<script>jQuery(function($) { $("head").append("%s")})</script>;',
					preg_replace('/\r|\n|\s{2,}/', '', sprintf($css, isset($atts['color']) ? $atts['color'] : 'hsl(120, 50%, 50%)'))
				);
			});
	}

	public function add_footer() {
		global $post;
		printf("<script>
				var emajax = '%s';
				jQuery(function($) {

					var star = $('.em-rating-star').first();
					var blankStar = $('.em-rating-blank-star').first();

					$('.em-rating-review').click(function() {
						$(this).siblings('.em-rating-write').fadeIn(200);
					});

					$('.em-rating-close').click(function() {
						$('.em-rating-write').hide();
					});

					$('.em-rating-send').click(function() {

						var starsGiven = $('.em-rating-starsgiven').val();
						var name = $('.em-rating-name').val();
						var text = $('.em-rating-text').val();

						if (name.length > 15 || text.length > 40) return;
						if (!name.length || !text.length) return; 

						$(this).off('click');

						$.post(emajax, {
							action: 'rating',
							security: '%s',
							rating: starsGiven,
							name: name,
							text: text,
							loc: location.href,
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
			wp_create_nonce('sdlfkj92309urasladfk239'),
			$post->ID
		);
	}

	public function struc($count = 0, $rating = 0, $thing = 'Product', $name = null) {

		$json = [
			'@context' => 'https://schema.org/',
			'@type' => 'WebPage',
			'aggreateRating' => [
				'@type' => 'AggregateRating',
				'itemReviewed' => [
					'@type' => $thing,
					'name' => $name
				],
				'ratingValue' => $rating,
				'bestRating' => '6',
				'worstRating' => '1',
				'ratingCount' => $count
			]
		];

		printf(
			'<script type="application/ld+json">%s</script>',
			json_encode($json, JSON_PRETTY_PRINT)
		);

	}


	private function stars($nr) {
		$star = sprintf('<svg class="%1$s-star" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path class="%1$s-star-path" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/><path d="M0 0h24v24H0z" fill="none"/></svg>', 'em-rating');
		$star_blank = sprintf('<svg class="%1$s-star" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path class="%1$s-star-path" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/><path d="M0 0h24v24H0z" fill="none"/></svg>', 'em-rating-blank');

		$stars = '';
		switch ($nr) {
			case 6: $stars = $star.$star.$star.$star.$star.$star; break;
			case 5: $stars = $star.$star.$star.$star.$star.$star_blank; break;
			case 4: $stars = $star.$star.$star.$star.$star_blank.$star_blank; break;
			case 3: $stars = $star.$star.$star.$star_blank.$star_blank.$star_blank; break;
			case 2: $stars = $star.$star.$star_blank.$star_blank.$star_blank.$star_blank; break;
			case 1: $stars = $star.$star_blank.$star_blank.$star_blank.$star_blank.$star_blank; break;
			default: $stars = $star_blank.$star_blank.$star_blank.$star_blank.$star_blank.$star_blank;
		}

		return $stars;
	}
}