<?php
/**
 * Class-wpsites-stats-core.php
 *
 * @package wpsites_stats/
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPSites_Stats_Core' ) ) {
	/**
	 * Class WPSites_Stats_Core
	 */
	class WPSites_Stats_Core {
		/**
		 * Plugin version
		 *
		 * @var string
		 */
		var $plugin_version;

		/**
		 * Plugin classes object
		 *
		 * @var object
		 */
		var $c;

		/**
		 * WPSites_Stats_Core constructor.
		 */
		function __construct() {
			$this->plugin_version = '1.0';
			$this->c              = new stdClass();

			$this->autoload_classes();
			$this->hooks();
		}

		/**
		 * Add hooks for public functionality
		 */
		function hooks() {
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		}

		/**
		 * Add textdomain for stats plugin
		 */
		function load_textdomain() {
			load_plugin_textdomain( 'wpsites_stats', false, dirname( plugin_basename( __FILE__ ) ) . '/langs/' );
		}

		/**
		 * Autoload wpsites stats plugin files
		 */
		function autoload_classes() {
			global $wpsites_stats;

			// include file exept class file.
			$filepaths = array();
			foreach ( $filepaths as $filepath ) {
				$path = WPSITES_STATS_PLUGIN_DIR . $filepath;
				if ( file_exists( $path ) ) {
					include $path;
				}
			}

			// include class file.
			spl_autoload_register(
				function ( $class_name ) {
					$classpaths = array(
						'include/' . 'class-' . strtolower( str_replace( '_', '-', $class_name ) ) . '.php',
					);

					foreach ( $classpaths as $classpath ) {
						$path = WPSITES_STATS_PLUGIN_DIR . $classpath;
						if ( file_exists( $path ) ) {
							include $path;
							break;
						}
					}
				}
			);

			$this->c->wpsites_stats_rest_api = WPSites_Stats_Rest_Api::instance();
			$this->c->wp_sites_stats_public = new WPSites_Stats_Public();

		}
	}
}
