<?php
/**
 * Relevanssi Light admin ajax code
 *
 * /relevanssi-light-admin-ajax.php
 *
 * @package Relevanssi Light
 * @author  Mikko Saari
 * @license https://wordpress.org/about/gpl/ GNU General Public License
 * @see     https://www.relevanssi.com/light/
 */

add_action( 'wp_ajax_relevanssi_light_get_chunks', 'relevanssi_light_get_chunks' );
add_action( 'wp_ajax_relevanssi_light_process_chunks', 'relevanssi_light_process_chunks' );

/**
 * Returns an array of all published post IDs on the site, chunked into chunks
 * of 100 post IDs.
 */
function relevanssi_light_get_chunks() {
	check_ajax_referer( 'relevanssi_light_process_nonce', 'security' );

	$args = array(
		'post_status' => 'publish',
		'numberposts' => -1,
		'fields'      => 'ids',
		'post_type'   => 'any',
	);

	$posts   = get_posts( $args );
	$chunked = array_chunk( $posts, 100 );

	echo wp_json_encode( $chunked );
	die();
}

/**
 * Processes post ID chunks.
 *
 * Each post ID in the chunk is passed with array_walk() to the
 * relevanssi_light_update_post_data() function.
 *
 * @see relevanssi_light_update_post_data
 */
function relevanssi_light_process_chunks() {
	check_ajax_referer( 'relevanssi_light_process_nonce', 'security' );

	array_walk( $_POST['chunk'], 'relevanssi_light_update_post_data' );
	$response = array(
		'data' => 'Processed ' . count( $_POST['chunk'] ) . ' posts.',
	);

	echo wp_json_encode( $response );
	die();
}
