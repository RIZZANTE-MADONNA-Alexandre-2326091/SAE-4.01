<?php

/*
 * WEATHER FUNCTIONS
 */

use Models\Location;


/**
 * Load weather script if the user is the television.
 *
 * @return void
 */
function loadWeatherScript(): void {
	if ( current_user_can( 'television' ) ) {
		wp_enqueue_script( 'weather_script_ecran', TV_PLUG_PATH . 'public/js/weather.js', array( 'jquery' ), '1.0', true );
		wp_enqueue_script( 'searchLocationTV_script_ecran', TV_PLUG_PATH . 'public/js/searchLocationTV', array( 'jquery' ), '1.0', true );
	}
}

add_action( 'wp_enqueue_scripts', 'loadWeatherScript' );

/**
 * Inject location values into JavaScript scripts using wp_localize_script.
 *
 * @return void
 */
function injectLocationValues() {
	$longitude = 5.4510;
	$latitude = 43.5156;

	wp_localize_script( 'weather_script_ecran', 'wetaherValues', array(
		'longitude' => $longitude,
		'latitude' => $latitude
	));

	wp_localize_script('searchLocationTV_script_ecran', 'locationValues', array(
		'ajaxUrl' => admin_url('admin-ajax.php')
	));
}

add_action('wp_enqueue_scripts', 'injectLocationValues');

/**
 * Handle AJAX request for weather data.
 */
function handleWeatherAjaxData() {
	if (is_front_page() && isset($_POST['longitude']) && isset($_POST['latitude'])) {
		$longitude = sanitize_text_field( $_POST['longitude'] );
		$latitude  = sanitize_text_field( $_POST['latitude'] );

		$location = new Location();

		$location->setLongitude( $longitude );
		$location->setLatitude( $latitude );

		$location->insert();

		wp_send_json_success(array(
			'message' => 'Ajout de la position dans la base de données',
			'longitude' => $longitude,
			'latitude' => $latitude
		));
	} else {
		wp_send_json_error(array( 'message' => 'Unauthorized user' ), 403);
	}
}

add_action( 'wp_ajax_handleWeatherAjaxData', 'handleWeatherAjaxData' );