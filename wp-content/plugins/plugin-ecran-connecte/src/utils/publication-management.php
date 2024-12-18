<?php

namespace Utils;

use WP_Query;

function get_page_by_title_V2($title) {
	// Define the arguments what to retrieve.
	$args = array(
		'post_type'         => 'page',
		'title'             => $title,
		'post_status'       => 'publish',
		'posts_per_page'    => 1
	);

	// Execute the WP Query.
	$my_query = new WP_Query($args);

	// Start a loop.
	if ( $my_query->have_posts() ) {
		$my_query->the_post();
		return get_post();
	}

	// Reset to the main query (IMPORTANT).
	wp_reset_postdata();
}
