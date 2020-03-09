<?php
/**
 * Relevanssi Light
 *
 * /relevanssi-light.php
 *
 * @package Relevanssi Light
 * @author  Mikko Saari
 * @license https://wordpress.org/about/gpl/ GNU General Public License
 * @see     https://www.relevanssi.com/light/
 *
 * @wordpress-plugin
 * Plugin Name: Relevanssi Light
 * Plugin URI: https://www.relevanssi.com/light/
 * Description: Replaces the default WP search with a fulltext index search.
 * Version: 0.1
 * Author: Mikko Saari
 * Author URI: https://www.mikkosaari.fi/
 * Text Domain: relevanssilight
 */

/*
	Copyright 2020 Mikko Saari  (email: mikko@mikkosaari.fi)

	This file is part of Relevanssi Light, a search plugin for WordPress.

	Relevanssi Light is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	Relevanssi Light is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Relevanssi Light.  If not, see <http://www.gnu.org/licenses/>.
*/

add_action( 'init', 'relevanssi_light_init' );
add_action( 'wp_insert_post', 'relevanssi_light_update_post_data' );

register_activation_hook( __FILE__, 'relevanssi_light_install' );

/**
 * Adds the required filters.
 *
 * Includes a check for the MySQL version number. If the version number is too
 * low, won't add the filters. If the version number is good, filters are added
 * and no more checks for the version number are made in the future.
 */
function relevanssi_light_init() {
	$options = get_option(
		'relevanssi_light',
		array(
			'mysql_version_good' => false,
		)
	);

	if ( ! $options['mysql_version_good'] ) {
		if ( relevanssi_light_is_mysql_good() ) {
			$options['mysql_version_good'] = true;
			update_option( 'relevanssi_light', $options );
		}
	}

	if ( $options['mysql_version_good'] ) {
		add_filter( 'posts_search', 'relevanssi_light_posts_search', 10, 2 );
		add_filter( 'posts_search_orderby', 'relevanssi_light_posts_search_orderby', 10, 2 );
		add_filter( 'posts_request', 'relevanssi_light_posts_request', 10, 2 );
	}
}

/**
 * Checks whether the DB version is at least MySQL 5.6 or MariaDB 10.0.5.
 *
 * Fulltext indexing is not available for MySQL versions under 5.6. Not that you
 * should be using them for WordPress anyway...
 *
 * @return boolean True if version is at least 5.6, false otherwise.
 */
function relevanssi_light_is_mysql_good() {
	global $wpdb;
	$db_version = $wpdb->get_var( 'SELECT VERSION()' );
	if ( stripos( $db_version, 'mariadb' ) !== false ) {
		list( $version, $useless ) = explode( '-', $db_version, 2 );
		if ( version_compare( $version, '10.0.5', '>=' ) ) {
			return true;
		}
	}
	if ( version_compare( $wpdb->db_version(), '5.6', '>=' ) ) {
		return true;
	}
	return false;
}

/**
 * Makes the required changes to the database.
 *
 * Adds a longtext column `relevanssi_light_data` to the `wp_posts` database
 * table and the fulltext index `relevanssi_light_fulltext` which includes the
 * `post_title`, `post_content`, `post_excerpt` and `relevanssi_light_data`
 * columns.
 *
 * @global object $wpdb The WP database interface.
 */
function relevanssi_light_install() {
	global $wpdb;

	$sql = "ALTER TABLE $wpdb->posts ADD COLUMN `relevanssi_light_data` LONGTEXT";
	$wpdb->query( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery

	$sql = "ALTER TABLE $wpdb->posts ADD FULLTEXT `relevanssi_light_fulltext` (`post_title`, `post_content`, `post_excerpt`, `relevanssi_light_data` )";
	$wpdb->query( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery
}

/**
 * Adds the MATCH AGAINST query to the posts_search filter hook.
 *
 * @param string   $search Search SQL for WHERE clause.
 * @param WP_Query $query  The current WP_Query object.
 *
 * @return string The modified SQL search query.
 */
function relevanssi_light_posts_search( $search, $query ) {
	$mode = '';
	/**
	 * Sets the mode for the fulltext search. Defaults to NATURAL LANGUAGE.
	 *
	 * @param boolean If true, enables BOOLEAN MODE.
	 */
	if ( apply_filters( 'relevanssi_light_boolean_mode', false ) ) {
		$mode = 'IN BOOLEAN MODE';
	}
	if ( isset( $query->query['s'] ) ) {
		$search = "AND MATCH(post_title,post_excerpt,post_content,relevanssi_light_data) AGAINST('" . $query->query['s'] . "' $mode)";
	}
	return $search;
}

/**
 * Adds the relevance orderby to the posts_search_orderby filter hook.
 *
 * @param string   $orderby The ORDER BY clause.
 * @param WP_Query $query   The current WP_Query object.
 *
 * @return string The modified ORDER BY clause.
 */
function relevanssi_light_posts_search_orderby( $orderby, $query ) {
	if ( isset( $query->query['s'] ) ) {
		$orderby = 'relevance DESC';
	}
	return $orderby;
}

/**
 * Adds the MATCH AGAINST query to the post query.
 *
 * Adds the MATCH AGAINST query to the main query as a relevance column for
 * the ORDER BY to use.
 *
 * @param string   $request The complete SQL query.
 * @param WP_Query $query   The current WP_Query object.
 *
 * @return string The modified SQL search query.
 */
function relevanssi_light_posts_request( $request, $query ) {
	$mode = '';
	/**
	 * Sets the mode for the fulltext search. Defaults to NATURAL LANGUAGE.
	 *
	 * @param boolean If true, enables BOOLEAN MODE.
	 */
	if ( apply_filters( 'relevanssi_light_boolean_mode', false ) ) {
		$mode = 'IN BOOLEAN MODE';
	}
	if ( isset( $query->query['s'] ) ) {
		$request = str_replace(
			'FROM',
			", MATCH(post_title,post_excerpt,post_content,relevanssi_light_data) AGAINST('" . $query->query['s'] . "' $mode) AS relevance FROM",
			$request
		);
	}
	return $request;
}

if ( ! function_exists( 'relevanssi_light_update_post_data' ) ) {
	/**
	 * Reads custom field content and updates the relevanssi_light_data with it
	 *
	 * This is a pluggable function, so feel free to write your own. This
	 * function uses the relevanssi_light_custom_fields filter hook to adjust
	 * the custom fields chosen to be added to the field and thus to the index.
	 *
	 * @param int $post_id The post ID.
	 */
	function relevanssi_light_update_post_data( $post_id ) {
		global $wpdb;

		/**
		 * Filters an array of custom field names to include in the fulltext
		 * index.
		 *
		 * A small trick: if you want to include all custom fields, pass an
		 * empty string in the array, and nothing else.
		 *
		 * @param array An array of custom field names.
		 */
		$custom_fields = apply_filters( 'relevanssi_light_custom_fields', array() );
		if ( empty( $custom_fields ) ) {
			$wpdb->update(
				$wpdb->posts,
				array( 'relevanssi_light_data' => '' ),
				array( 'ID' => $post_id ),
				array( '%s' ),
				array( '%d' )
			);
			return;
		}
		$extra_content = array_reduce(
			$custom_fields,
			function ( $content, $field ) use ( $post_id ) {
				$values = get_post_meta( $post_id, $field, false );
				array_walk_recursive(
					$values,
					function ( $value ) use ( &$content ) {
						$content .= ' ' . $value;
					}
				);
				return $content;
			},
			''
		);
		if ( $extra_content ) {
			$wpdb->update(
				$wpdb->posts,
				array( 'relevanssi_light_data' => $extra_content ),
				array( 'ID' => $post_id ),
				array( '%s' ),
				array( '%d' )
			);
		}
	}
}
