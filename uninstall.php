<?php
/**
 * /uninstall.php
 *
 * @package Relevanssi Light
 * @author  Mikko Saari
 * @license https://wordpress.org/about/gpl/ GNU General Public License
 * @see     https://www.relevanssi.com/light/
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

global $wpdb;

$sql = "ALTER TABLE $wpdb->posts DROP COLUMN `relevanssi_light_data`";
$wpdb->query( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery

$sql = "ALTER TABLE $wpdb->posts DROP INDEX `relevanssi_light_fulltext`";
$wpdb->query( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery

delete_option( 'relevanssi_light' );
