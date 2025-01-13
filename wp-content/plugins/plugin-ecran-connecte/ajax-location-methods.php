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
	if(getCurrentUser()) {
		$currentUserId = get_current_user_id();

		if ( ! $currentUserId ) {
			error_log( 'Aucun utilisateur actif' );
			return;
		}

		$model = new Location();
		$user  = $model->checkIfUserIdExists( $currentUserId );

		if ( $user !== false ) {
			$longitude = $user->getLongitude();
			$latitude  = $user->getLatitude();
			error_log( "Coordonnées trouvées pour l'utilisateur {$currentUserId} : {$longitude}, {$latitude}" );
		} else {
			$longitude = 5.4510;
			$latitude  = 43.5156;
			error_log( "Aucune localisation trouvée pour l'utilisateur {$currentUserId}, coordonnées par défaut utilisées." );
		}

		wp_enqueue_script( 'weather_script_ecran', TV_PLUG_PATH . 'public/js/weather.js', array( 'jquery' ), '1.0', true );
		wp_enqueue_script( 'searchLocationTV_script_ecran', TV_PLUG_PATH . 'public/js/searchLocationTV.js', array( 'jquery' ), '1.0', true );

		wp_localize_script( 'weather_script_ecran', 'weatherValues', array(
			'long' => $longitude,
			'lat'  => $latitude
		) );
	}
}

add_action('wp_enqueue_scripts', 'injectLocationValues');

/**
 * Handle AJAX request for weather data.
 *
 * @return void
 */
function handleWeatherAjaxData(): void {
	if(getCurrentUser()) {
		check_ajax_referer( 'locationNonce', 'nonce' );

		error_log("Données reçues : " . var_export($_POST, true));

		if( ! isset( $_POST['longitude'] ) || ! isset( $_POST['latitude'] )){
			wp_send_json_error('Erreur dans la récupération des données (longitude/latitude manquantes) !');
		}

		$longitude = sanitize_text_field( $_POST['longitude']);
		$latitude  = sanitize_text_field( $_POST['latitude']);
		$id_user   = sanitize_text_field($_POST['currentUserId']);

		if ( $longitude === null || $latitude === null ) {
			wp_send_json_error(['message' => 'Données manquantes ou invalides pour ajouter la position'], 400);
		}

		$location = new Location();

		$location->setLongitude( $longitude );
		$location->setLatitude( $latitude );
		$location->setIdUser( $id_user );

		$userExists = $location->checkIfUserIdExists( $id_user );

		if (!$userExists) {
			$location->insert();

			wp_send_json_success( [
				'message'   => 'Les coordonnées de l\'utilisateur ont été enregistrées avec succès',
				'longitude' => $longitude,
				'latitude'  => $latitude,
				'id_user'   => $id_user,
			] );
		} else {
			$location->update();

			wp_send_json_success( [
				'message'   => 'Les coordonnées de l\'utilisateur ont été mises à jour correctement.',
				'longitude' => $longitude,
				'latitude'  => $latitude,
				'id_user'   => $id_user,
			] );
		}
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
	if(getCurrentUser()){
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
}

add_action('wp_enqueue_scripts', 'loadLocAjaxIfUserHasNoLoc');

function getCurrentUser(): bool{
	$current_user = wp_get_current_user();
	if(in_array('television', $current_user->roles)) return true;
	return false;
}