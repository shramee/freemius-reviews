<?php
/*
Plugin Name: Freemius Testimonials
Description: Shows plugins/theme reviews from Freemius
Version: 1.0
Author: Shramee Srivastav
Author URI: http://shramee.com
Author Email: shramee.srivastav@gmail.com
Domain: fs-testimonial
*/

class FS_Testimonials {

	public function __construct() {
		add_shortcode( 'freemius-reviews', [ $this, 'reviews' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ] );
	}

	public function scripts() {
		wp_enqueue_style( 'fmt-script', plugin_dir_url( __FILE__ ) . '/front.css', '' );
	}

	/**
	 * @param array $params
	 *
	 * @return string
	 */
	function reviews( $params = [] ) {

		$params = $params ? $params : [];

		$compress = '';

		if ( isset( $params['compress'] ) || in_array( 'compress', $params ) ) {
			$compress = 'compress';
		}

		$reviews = get_transient( 'fsrevs_reviews' );
		if ( empty( $reviews ) ) {
			$reviews = self::get_reviews();
			set_transient( 'fsrevs_reviews', $reviews, DAY_IN_SECONDS * 7 );
		}
		ob_start();

		echo '<pre>';
		var_dump( $reviews->reviews[0] );
		echo '</pre>';

		if ( $reviews && $reviews->reviews ) {
			$this->render_reviews( $reviews->reviews, $compress );
		}

		return ob_get_clean();
	}

	static function get_reviews() {
		if ( ! class_exists( 'Freemius_API' ) ) {
			include 'freemius/Freemius.php';
		}
		// Init SDK.
		$api = new Freemius_Api(
			'developer', //scope
			179, //id
			'pk_f9246386252febea4fecf3bdbdf92', //public key
			'sk_W.$_NJ(QYhbqxX=@Ot$[OmjXky>-g' //secret key
		);

		// Get all products.
		$result = $api->Api( '/plugins/269/reviews.json?is_featured=true' );

		return $result;
	}

	/**
	 * Get reviews and renders the shortcode
	 *
	 * @param array $reviews
	 */
	function render_reviews( $reviews, $compress = '' ) {
		?>
		<div id="fs-testimonials" class="<?php echo $compress ?>">
			<div class="fs-testimonials-outer-wrap">
				<div class="fs-testimonials-wrap">
					<?php
					foreach ( $reviews as $r ) {
						$this->review_html( $r );
					} ?>
				</div>
			</div>
			<?php if ( $compress ) { ?>
				<div
					onclick="jQuery(this).closest('#fs-testimonials').toggleClass('compress-expanded')" class="compress-toggle"
					data-more="<?php _e( 'More testimonials', 'fs-testimonial' ) ?>"
					data-less="<?php _e( 'Less testimonials', 'fs-testimonial' ) ?>">
				</div>
			<?php } ?>
		</div>
		<?php

	}

	/**
	 * @param stdClass $r Review object
	 */
	function review_html( $r ) {
		$r->picture = $r->picture ? $r->picture : 'http://1.gravatar.com/avatar/d28eae9f3dcdcba08ac685b112b006aa?s=128&d=mm&f=y&r=g';
		?>
		<div class="testimonial" data-index="6" data-id="145" aria-hidden="true">
			<div class="quote-container">
				<ul class="rate">
					<?php
					for ( $i = 1; $i < $r->rate + 1; $i += 20 ) {
						echo '<li><i class="fa fa-star"></i></li>';
					}
					?>
				</ul>
				<h4 title="Just perfect!"><?php echo $r->title ?></h4>
				<blockquote><p><?php echo $r->text ?></p></blockquote>
				<img class="profile-pic" src="<?php echo $r->picture ?>">
			</div>
			<strong class="name"><?php echo $r->name ?></strong>
		</div>
		<?php
	}
}

new FS_Testimonials();