<?php
/**
 * Relevanssi Lightspeed
 *
 * /relevanssi.php
 *
 * @package Relevanssi Lightspeed
 * @author  Mikko Saari
 * @license https://wordpress.org/about/gpl/ GNU General Public License
 * @see     https://www.relevanssi.com/
 *
 * @wordpress-plugin
 * Plugin Name: Relevanssi Lightspeed
 * Plugin URI: https://www.relevanssi.com/
 * Description: This Lightspeed plugin replaces WordPress search with a relevance-sorting search.
 * Version: 0.1
 * Author: Mikko Saari
 * Author URI: https://www.mikkosaari.fi/
 * Text Domain: relevanssilightspeed
 */

/*
	Copyright 2020 Mikko Saari  (email: mikko@mikkosaari.fi)

	This file is part of Relevanssi Lightspeed, a search plugin for WordPress.

	Relevanssi Lightspeed is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	Relevanssi Lightspeed is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Relevanssi Lightspeed.  If not, see <http://www.gnu.org/licenses/>.
*/

add_filter( 'posts_search', 'relevanssi_lightspeed_posts_search', 10, 2 );
add_filter( 'posts_search_orderby', 'relevanssi_lightspeed_posts_search_orderby', 10, 2 );
add_filter( 'posts_request', 'relevanssi_lightspeed_posts_request', 10, 2 );

/**
 * Adds the MATCH AGAINST query to the posts_search filter hook.
 *
 * @param string   $search Search SQL for WHERE clause.
 * @param WP_Query $query  The current WP_Query object.
 *
 * @return string The modified SQL search query.
 */
function relevanssi_lightspeed_posts_search( $search, $query ) {
	if ( isset( $query->query['s'] ) ) {
		$search = "AND MATCH(post_title,post_excerpt,post_content,relevanssi_ls_data) AGAINST('" . $query->query['s'] . "' IN BOOLEAN MODE)";
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
function relevanssi_lightspeed_posts_search_orderby( $orderby, $query ) {
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
function relevanssi_lightspeed_posts_request( $request, $query ) {
	if ( isset( $query->query['s'] ) ) {
		$request = str_replace(
			'FROM',
			", MATCH(post_title,post_excerpt,post_content,relevanssi_ls_data) AGAINST('" . $query->query['s'] . "' IN BOOLEAN MODE) AS relevance FROM",
			$request
		);
	}
	return $request;
}
