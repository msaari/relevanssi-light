=== Relevanssi Light ===
Contributors: msaari
Donate link: https://www.relevanssi.com/light/
Tags: search, fulltext
Requires at least: 5.0
Tested up to: 6.1
Requires PHP: 7.2
Stable tag: 1.2.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Relevanssi Light is a simple, quick and effective search improvement that replaces the default WP search with a fulltext index search.

== Description ==

Relevanssi Light replaces the default WP search with a search that uses the fulltext indexing capabilities of the database. It requires a modern version of MySQL or MariaDB.

Relevanssi Light is very powerful, as it leverages your database to do all the hard work. Even large sites will return relevant results quickly and efficiently. Indexing is fully automatic and always handled by your database server.

Thanks to Otto Kekäläinen (ottok) for the original idea, development push, and all the feedback.

Feedback is welcome. I follow the support forums here, but you can also post an issue on [the Relevanssi Light GitHub page](https://github.com/msaari/relevanssi-light). That's where the active development happens.

= Comparison between Relevanssi Light and Relevanssi =

Relevanssi Light is a simple, easy-to-use tool with limited capabilities. It provides very fast search results with better quality, but with little room for customization and adjustment.

Relevanssi is a full-fledged search solution that offers lots of features and full control over the search index and the search results. It generates useful excerpts with the search terms highlighted, can provide "Did you mean" suggestions, keeps logs and much more. Indexing and searching is much slower than with Relevanssi Light.

Relevanssi Premium adds more features to Relevanssi: it can index user profiles, taxonomy terms, and PDF content, generate related posts lists and more.

== Installation ==

Relevanssi Light requires a database that supports fulltext indexing. Recent versions of MySQL and MariaDB should be fine.

1. Install the plugin
1. Activate the plugin
1. You're done!

Relevanssi Light really is this simple to use. No changes are required to your templates or other configuration. Relevanssi Light automatically adjusts the database queries to use the fulltext index it creates.

Activating the plugin for the first time may cause a timeout. That's just inconvenient, not a real problem: all the database changes still happened and everything should work just fine.

Relevanssi Light is kept very lean on purpose. There are few settings to adjust. If you like adjusting settings, [Relevanssi](https://wordpress.org/plugins/relevanssi/) offers lots of settings to adjust.

= Natural language vs Boolean mode =

Fulltext indexing offers two modes of operation. In Natural language mode there are no special operators and searches consists of simple keywords. In Boolean mode, special operators can be used. For a list of these, see [Full-Text Index Overview](https://mariadb.com/kb/en/full-text-index-overview/) in MariaBD Knowledge Base.

Relevanssi Light uses Natural language mode, as it's the better choice for large majority of cases. If you need to use Boolean mode, you can enable it by adding this to your theme `functions.php`:

`add_filter( 'relevanssi_light_boolean_mode', '__return_true' );`

= Including custom field content and more =

By default Relevanssi Light includes post titles, post content and excerpts in the fulltext index. Sometimes it's necessary to include other content, for example custom fields. Relevanssi Light facilitates this by adding a new column, `relevanssi_light_data` to the `wp_posts` database tables. Contents of this column are added to the fulltext index.

Relevanssi Light has a method of adding custom field content to this column. It is done by providing a list of custom field names with the `relevanssi_light_custom_fields` filter hook. For example, in order to include the custom fields `_sku` and `seo_meta_desc` in the index, you could add this to the theme `functions.php`:

`add_filter( 'relevanssi_light_custom_fields', function( $fields ) { return array( '_sku', 'seo_meta_desc' ); } );`

Now when posts are saved, the custom fields will be added in the index. NOTE: This is not automatically applied to all existing posts, only when the post is saved.

For more complicated cases, you can override the default `relevanssi_light_update_post_data()` function Relevanssi Light uses (it's a pluggable function; see the source code for more details). For even more complicated cases, I would recommend using [Relevanssi](https://wordpress.org/plugins/relevanssi/), which will give you a lot more power to control what is indexed.

== Changelog ==
= 1.2.2 =
* Fixes a minor SQL injection vulnerability.

= 1.2.1 =
* Fixes errors in admin searches.

= 1.2 =
* Makes the 'Process all posts' cover all posts, not just the post type `post`.

= 1.1 =
* Fixes the network activation. Now when Relevanssi Light is network activated on a multisite installation, the database changes required are implemented on all network sites (as soon as someone visits the site admin dashboard).

= 1.0 =
* Adds an settings page to show information about the plugin.
* Database alterations are done asynchronously to avoid plugin activation stalling.
* Uninstalling the plugin in multisite context now works.

= 0.1 =
* First release, minimum viable product!

== Upgrade notice ==
= 1.2.2 =
* Fixes a minor SQL injection vulnerability.

= 1.2.1 =
* Fixes errors in admin searches.

= 1.2 =
* Makes the 'Process all posts' cover all posts, not just the post type `post`.

= 1.1 =
* Fix for the network activation on multisite.

= 1.0 =
* First proper release.

= 0.1 =
* First release.