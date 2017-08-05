<?php
/**
 * Class-wpsites-stats-public.php
 *
 * @package wpsites_stats/
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WpSites_Stats_Public' ) ) {
	/**
	 * Class WpSites_Stats_Public
	 */
	class WpSites_Stats_Public {

		/**
		 * WpSites_Stats_Public constructor.
		 */
		function __construct() {
			$this->hooks();
		}

		/**
		 * Add hooks for stats widget
		 */
		public function hooks() {
			add_action( 'widgets_init', array( $this, 'register_stats_widget' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		/**
		 * Register stats widget
		 */
		public function register_stats_widget() {
			register_widget( 'WPSites_Stats_Widget_Api' );
		}

		/**
		 * Enqueue js and css for stats widget
		 */
		public function enqueue_scripts() {
			wp_register_script( 'wpsits-stats-script', WPSITES_STATS_PLUGIN_URL . 'assets/js/action.js', array( 'jquery' ), '1.0', true );
			wp_register_style( 'wpsits-stats-style', WPSITES_STATS_PLUGIN_URL . 'assets/css/style.css', array(), '1.0' );

			$stats_url   = get_rest_url() . 'wpsites/v1' . '/stats?';
			$wpsits_stats_array = array(
				'stats_url' => $stats_url,
				'stats_interval' => 60000,
			);
			wp_localize_script( 'wpsits-stats-script', 'wpSitsStats', $wpsits_stats_array );
		}
	}
}
