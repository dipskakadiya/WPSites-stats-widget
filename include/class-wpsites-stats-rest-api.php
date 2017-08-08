<?php
/**
 * Class-wpsites-stats-rest-api.php
 *
 * @package wpsites_stats/
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPSites_Stats_Rest_Api' ) ) {
	/**
	 * Class WPSites_Stats_Rest_Api
	 */
	class WPSites_Stats_Rest_Api extends WP_REST_Controller {

		/**
		 * Stats api namespace
		 *
		 * @var string
		 */
		protected $namespace = 'wpsites/v1';

		/**
		 * Singleton class object
		 *
		 * @var object
		 */
		protected static $instance;

		/**
		 * WPSites_Stats_Rest_Api constructor.
		 */
		function __construct() {
			/** Nothing here */
		}

		/**
		 * Create singleton class object
		 *
		 * @return mixed
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) ) {
				$class          = __CLASS__;
				self::$instance = new $class;
				self::$instance->hooks(); // run the hooks.
			}

			return self::$instance;
		}

		/**
		 * List of hook for stats rest api
		 */
		public function hooks() {
			add_action( 'rest_api_init', array( $this, 'custom_rest_endpoint' ) );
		}

		/**
		 * Register stats rest api endpoint
		 */
		function custom_rest_endpoint() {
			register_rest_route(
				$this->namespace, '/stats', array(
					array(
						'methods'  => WP_REST_Server::READABLE,
						'callback' => array( $this, 'get_stats' ),
						'args' => array(
							'totalcount' => array(
								'type'              => 'integer',
								'enum'              => array( '1', '0' ),
								'sanitize_callback' => 'absint',
								'validate_callback' => function ( $param, $request, $key ) {
									return is_numeric( $param );
								},
							),
						),
					),
				)
			);
		}

		/**
		 * Get sites stats information
		 *
		 * @return mixed|WP_REST_Response
		 */
		public function get_stats() {
			$sites_stats = array();

			$blog_id = get_current_blog_id();

			if ( ! empty( $_REQUEST['totalcount'] ) && 1 === (int) $_REQUEST['totalcount'] ) {

				$blog_stats = get_transient( 'wpsites_stats_all_site' );

				if ( false === $blog_stats ) {

					$args  = array(
						'archived' => 0,
						'deleted'  => 0,
						'public'   => 1,
						'fields'   => 'ids',
					);
					$blogs = get_sites( $args );

					$blog_stats = array(
						'post'    => 0,
						'page'    => 0,
						'comment' => 0,
						'users'   => 0,
					);

					foreach ( $blogs as $blog_id ) {
						if ( is_multisite() ) {
							switch_to_blog( $blog_id );
						}

						$count_posts        = wp_count_posts( 'post' );
						$blog_stats['post'] += $count_posts->publish;

						$count_page         = wp_count_posts( 'page' );
						$blog_stats['page'] += $count_page->publish;

						$count_comments        = wp_count_comments();
						$blog_stats['comment'] += $count_comments->total_comments;

						$count_user          = (object) count_users();
						$blog_stats['users'] += $count_user->total_users;

						if ( is_multisite() ) {
							restore_current_blog();
						}
					}
					set_transient( 'wpsites_stats_all_site', $blog_stats, MINUTE_IN_SECONDS );
				}
			} else {

				$blog_stats = get_transient( 'wpsites_stats_' . $blog_id );

				if ( false === $blog_stats ) {
					$blog_stats = array();

					$count_posts        = wp_count_posts( 'post' );
					$blog_stats['post'] = $count_posts->publish;

					$count_page         = wp_count_posts( 'page' );
					$blog_stats['page'] = $count_page->publish;

					$count_comments        = wp_count_comments();
					$blog_stats['comment'] = $count_comments->total_comments;

					$count_user          = (object) count_users();
					$blog_stats['users'] = $count_user->total_users;

					set_transient( 'wpsites_stats_' . $blog_id, $blog_stats, MINUTE_IN_SECONDS );
				}
			}

			$blog_stats = apply_filters( 'wpsite_rest_stats', $blog_stats );

			return rest_ensure_response( $blog_stats );
		}
	}
}
