<?php
/**
 * Wp-sites-stats-widget.php
 *
 * @package wpsites_stats/
 */

/**
 Plugin Name: Wp Sites Stats Widget
 Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
 Description: A brief description of the Plugin.
 Version: 1.0
 Author: dipesh
 Author URI: http://URI_Of_The_Plugin_Author
 License: A "Slug" license name e.g. GPL2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WPSITES_STATS_PLUGIN_DIR' ) ) {
	define( 'WPSITES_STATS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'WPSITES_STATS_PLUGIN_URL' ) ) {
	define( 'WPSITES_STATS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * Get `WPSites_Stats_Core` global object
 *
 * @return mixed
 */
function wpsites_stats() {
	global $wpsites_stats;

	return $wpsites_stats;
}

/**
 * Init Wp Sites Stats Widget plugin
 */
function wpsites_stats_init() {
	global $wpsites_stats;

	include 'include/class-wpsites-stats-core.php';

	// load the main class.
	$wpsites_stats = new WPSites_Stats_Core();
}
add_action( 'plugins_loaded', 'wpsites_stats_init', 10 );
