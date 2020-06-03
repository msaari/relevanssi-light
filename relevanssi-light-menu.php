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
		<h1>Relevanssi Light</h1>

		<div id="relevanssi_light_container">
			<div id="relevanssi_light_main">

			<p>Relevanssi Light is simple to use. No changes are
				required to your templates or other configuration. Relevanssi
				Light automatically adjusts the database queries to use the
				fulltext index it creates.</p>

			<p>Relevanssi Light is kept very lean on purpose. There are few
				settings to adjust. If you like adjusting settings, <a
				href="https://wordpress.org/plugins/relevanssi/">Relevanssi</a>
				offers lots of settings to adjust.</p>

			<h3>Natural language vs Boolean mode</h3>

			<p>Fulltext indexing offers two modes of operation. In Natural
				language mode there are no special operators and searches
				consists of simple keywords. In Boolean mode, special operators
				can be used. For a list of these, see <a
				href="https://mariadb.com/kb/en/full-text-index-overview/">Full-Text
				Index Overview</a> in MariaBD Knowledge Base.</p>

			<p>Relevanssi Light uses Natural language mode, as it's the better
				choice for large majority of cases. If you need to use Boolean
				mode, you can enable it by adding this to your theme
				<code>functions.php</code>:</p>

			<p><code>add_filter( 'relevanssi_light_boolean_mode', '__return_true' );</code></p>

			<h3>Including custom field content and more</h3>

			<p>By default Relevanssi Light includes post titles, post content
				and excerpts in the fulltext index. Sometimes it's necessary to
				include other content, for example custom fields. Relevanssi
				Light facilitates this by adding a new column,
				<code>relevanssi_light_data</code> to the <code>wp_posts</code>
				database tables. Contents of this column are added to the
				fulltext index.</p>

			<p>Relevanssi Light has a method of adding custom field content to
				this column. It is done by providing a list of custom field
				names with the <code>relevanssi_light_custom_fields</code>
				filter hook. For example, in order to include the custom fields
				<code>_sku</code> and <code>seo_meta_desc</code> in the index,
				you could add this to the theme <code>functions.php</code>:</p>

			<p><code>add_filter( 'relevanssi_light_custom_fields', function( $fields ) { return array( '_sku', 'seo_meta_desc' ); } );</code></p>

			<p>Now when posts are saved, the custom fields will be added in the
				index. NOTE: This is not automatically applied to all existing
				posts, only when the post is saved.</p>

			<p>For more complicated cases, you can override the default
				<code>relevanssi_light_update_post_data()</code> function
				Relevanssi Light uses (it's a pluggable function; see the source
				code for more details). For even more complicated cases, I would
				recommend using <a href="https://wordpress.org/plugins/relevanssi/">Relevanssi</a>,
				which will give you a lot more power to control what is indexed.</p>

			<h3>Process posts</h3>

			<p>After you've made changes to the post update data actions, you can
				click the "Process all posts" button below to have Relevanssi Light
				process all your posts to update the data.</p>

			<form method='post'>
				<input type="button" id='process' class='button button-primary' value='Process all posts' />
				<div class='progress'>
					<progress id="relevanssi_light_process" max="100" value="0">0%</progress>
				</div>
			</form>

			<h3>Feedback and Credits</h3>

			<p>Relevanssi Light is written by <a href="https://www.mikkosaari.fi/">Mikko
			Saari</a>.</p>

			<p>If you have any questions, please post them to the <a
			href="https://wordpress.org/support/plugin/relevanssi-light/">Relevanssi
			Light support forums</a>. The plugin development happens
			<a href="https://github.com/msaari/relevanssi-light">on GitHub</a>;
			feel free to <a href="https://github.com/msaari/relevanssi-light/issues">post
			issues</a> there if you have technical questions.</p>

			</div>
			<div id="relevanssi_light_sidebar">
				<div class="relevanssi_light_sidebar_box">
					<h2>Relevanssi</h2>

					<p>Do you need more customization capabilities? The regular
						Relevanssi has a ton of options and ways of adjusting
						the search. It is not as fast as Relevanssi Light is and
						requires a lot more database space, but it gives you
						plenty of ways to customize the search experience.</p>

					<p><a href="https://wordpress.org/plugins/relevanssi/">Find
					Relevanssi here</a>.</p>
				</div>

				<div class="relevanssi_light_sidebar_box">
					<h2>Relevanssi Premium</h2>

					<p>For even more features, try <a
						href="https://www.relevanssi.com/">Relevanssi
						Premium</a>. Relevanssi Premium includes:</p>

					<ul>
						<li>Searching for PDF content</li>
						<li>Multisite searching</li>
						<li>Searching user profiles</li>
						<li>Related posts</li>
						<li>...and much more!</li>
					</ul>

					<p><a href="https://www.relevanssi.com/buy-premium/">Buy
					Relevanssi Premium here</a>.</p>
				</div>
			</div>
		</div>
	</div>
	<?php
}
