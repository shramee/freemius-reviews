<?php
/*
Plugin Name: Freemius Reviews
Description: Shows plugins/theme reviews from Freemius
Version: 1.0
Author: Shramee Srivastav
Author URI: http://shramee.com
Author Email: shramee.srivastav@gmail.com
*/

function fsrevs_get_reviews() {
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

function fsrevs_reviews() {
	$reviews = get_transient( 'fsrevs_reviews' );
	if ( empty( $reviews ) ) {
		$reviews = fsrevs_get_reviews();
		set_transient( 'fsrevs_reviews', $reviews, DAY_IN_SECONDS * 7 );
	}
	ob_start();
	echo '<pre>';
	var_dump( $reviews );
	echo '</pre>';
	return ob_get_clean();
}

add_shortcode( 'freemius-reviews', 'fsrevs_reviews' );