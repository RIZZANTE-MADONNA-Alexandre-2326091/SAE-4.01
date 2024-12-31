<?php

/*
 * WEATHER AJAX FUNCTIONS
 */

use Models\Location;

/**
 * Injects location values into JavaScript scripts using wp_localize_script.
 *
 * @return void
 */
function injectLocationValues():void {
	$model = new Location();

	if($user = $model->checkIfUserIdExists(get_current_user_id())) {
		$longitude = $user->getLongitude();
		$latitude = $user->getLatitude();
	}else{
		$longitude = 5.4510;
		$latitude = 43.5156;
	}

	wp_enqueue_script('weather_script_ecran', TV_PLUG_PATH . 'public/js/weather.js', array('jquery'), '1.0', true);
	wp_enqueue_script( 'searchLocationTV_script_ecran', TV_PLUG_PATH . 'public/js/searchLocationTV.js', array( 'jquery' ), '1.0', true );

	wp_localize_script( 'weather_script_ecran', 'weatherValues', array(
		'long' => $longitude,
		'lat' => $latitude
	));
}

add_action('wp_enqueue_scripts', 'injectLocationValues');

/**
 * Handle AJAX request for weather data.
 *
 * @return void
 */
function handleWeatherAjaxData(): void {
	check_ajax_referer('locationNonce', 'nonce');

	$longitude = isset($_POST['longitude']) ? floatval($_POST['longitude']) : null;
	$latitude = isset($_POST['latitude']) ? floatval($_POST['latitude']) : null;
	$id_user = get_current_user_id();

	if ( $longitude === null || $latitude === null) {
		wp_send_json_error(array( 'message' => 'Données manquantes ou invalides pour ajouter la position' ), 400 );
	}

	$location = new Location();

	$location->setLongitude( $longitude );
	$location->setLatitude( $latitude );
	$location->setIdUser( $id_user );

	$location->insert();

	wp_send_json_success( array(
		'message'   => 'Nouvelle position ajoutée avec succès dans la base de données',
		'currentUserId' => $id_user,
		'longitude' => $longitude,
		'latitude'  => $latitude
	));

	try {
		// Insère les données dans la base de données
		$insertedId = $location->insert();

		wp_send_json_success([
			'message' => 'User location data saved successfully',
			'id' => $insertedId,
			'longitude' => $longitude,
			'latitude' => $latitude
		]);
	} catch (Exception $e) {
		wp_send_json_error(['message' => 'Error while saving data: ' . $e->getMessage()]);
	}
}

add_action( 'wp_ajax_handleWeatherAjaxData', 'handleWeatherAjaxData' );
add_action('wp_ajax_nopriv_handleWeatherAjaxData', 'handleWeatherAjaxData');

/**
 * Loads location AJAX script if the user has no associated location.
 *
 * @return void
 */
function loadLocAjaxIfUserHasNoLoc(): void{
	$model = new Location();

	if(is_user_logged_in() && is_front_page() &&
	   !$model->checkIfUserIdExists(get_current_user_id())){

		add_action('wp_enqueue_scripts', 'locationScript');

		wp_localize_script( 'searchLocationTV_script_ecran', 'locationValues', array(
			'ajaxUrl' => admin_url('admin-ajax.php'),
			'ajaxNonce' => wp_create_nonce('locationNonce'),
			'currentUserId' => get_current_user_id()
		));
	}
}

add_action('wp_enqueue_scripts', 'loadLocAjaxIfUserHasNoLoc');