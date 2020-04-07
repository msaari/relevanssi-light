<?php
/**
 * Relevanssi Light settings menu
 *
 * /relevanssi-light-menu.php
 *
 * @package Relevanssi Light
 * @author  Mikko Saari
 * @license https://wordpress.org/about/gpl/ GNU General Public License
 * @see     https://www.relevanssi.com/light/
 */

add_action( 'admin_menu', 'relevanssi_light_add_admin_menu' );

/**
 * Adds the admin menu.
 *
 * Hooks on to `admin_menu` to add the options page.
 *
 * @see add_options_page()
 */
function relevanssi_light_add_admin_menu() {
	add_options_page(
		'Relevanssi Light',
		'Relevanssi Light',
		'manage_options',
		'relevanssi_light',
		'relevanssi_light_options_page'
	);
}

/**
 * Renders the options page.
 *
 * Relevanssi Light doesn't have plenty of options at the moment. That is
 * unlikely to change in the future.
 */
function relevanssi_light_options_page() {
	if ( ! empty( $_REQUEST['process'] ) ) {
		check_admin_referer( plugin_basename( __FILE__ ), 'relevanssi_light' );
		relevanssi_light_process();
	}
	?>
	<div class="wrap">
	<?php

	wp_nonce_field( plugin_basename( __FILE__ ), 'relevanssi_light' );

	$nonce = array(
		'relevanssi_light_process_nonce' => wp_create_nonce( 'relevanssi_light_process_nonce' ),
	);

	wp_enqueue_script(
		'relevanssi_light_js',
		plugin_dir_url( __FILE__ ) . 'js/relevanssi-light.js',
		array( 'jquery' ),
		1,
		true
	);
	wp_localize_script( 'relevanssi_light_js', 'nonce', $nonce );

	wp_enqueue_style(
		'relevanssi_light_admin_css',
		plugin_dir_url( __FILE__ ) . 'css/relevanssi_light_admin.css',
		array(),
		1
	);

	?>
		<h2>Relevanssi Light</h2>

		<div id="relevanssi_light_container">
			<div id="relevanssi_light_main">
		<h3>Process posts</h3>

		<p>Relevanssi Light is fully automatic for the most part. All changes to
			the posts are automatically updated to the search index by the
			database server. You don't have to do anything about it, Relevanssi
			Light will just work.</p>

		<p>However, if you want to add custom data to your posts for Relevanssi
			Light to use (for example custom field content, taxonomy terms or
			something like that), those changes do not take effect until the
			next time the post is updated.</p>

		<p>After you've made changes to the post update data actions, you can
			click the "Process all posts" button below to have Relevanssi Light
			process all your posts to update the data.</p>

		<form method='post'>
			<div class='progress'>
				<progress id="relevanssi_light_process" max="100" value="0">0%</progress>
			</div>
			<input type="button" id='process' class='button button-primary' value='Process all posts' />
		</form>
			</div>
			<div id="relevanssi_light_sidebar">
			<div class="relevanssi_light_sidebar_box">
	<h2>Relevanssi</h2>
	</div>

			</div>
		</div>
	</div>
	<?php
}
