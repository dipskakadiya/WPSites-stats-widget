<?php
/**
 * Class-wpsites-stats-widget-api.php
 *
 * @package wpsites_stats/
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPSites_Stats_Widget_Api' ) ) {
	/**
	 * Class WPSites_Stats_Widget_Api
	 */
	class WPSites_Stats_Widget_Api extends WP_Widget {
		/**
		 * Register widget with WordPress.
		 */
		function __construct() {
			parent::__construct(
				'wpsites_stats_widget',
				esc_html__( 'WP Sites Stats', 'wpsites_stats' ),
				array(
					'description' => esc_html__( 'WP Sites Stats Widget', 'wpsites_stats' ),
				)
			);
		}

		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param array $args Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		public function widget( $args, $instance ) {
			echo $args['before_widget'];
			if ( ! empty( $instance['title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
			}

			$stats_url   = get_rest_url() . 'wpsites/v1' . '/stats?';
			$api_args    = array(
				'timeout' => 5,
			);
			$blogs_stats = wp_remote_get( $stats_url, $args );
			if ( ! empty( $blogs_stats['body'] ) ) {
				$blogs_stats = json_decode( $blogs_stats['body'] );
			}

			if ( ! empty( $instance['stats_site'] ) && 'all' === $instance['stats_site'] ) {
				$blogs_args = array(
					'archived' => 0,
					'deleted'  => 0,
					'public'   => 1,
					'fields'   => 'ids',
				);
				$blogs      = get_sites( $blogs_args );
			} else {
				$blogs = array( get_current_blog_id() );
			}
			?>
			<div id="wpsite-stats-content"
				 data-blog_stats="<?php echo ( empty( $instance['stats_site'] ) && 'all' === $instance['stats_site'] ) ? 'all' : get_current_blog_id(); ?>">
				<?php
				foreach ( $blogs as $blog_id ) {
					$stats = new stdClass;
					if ( ! empty( $blogs_stats ) ) {
						$stats = $blogs_stats->{$blog_id};
					}
					?>
					<ul id="wpsite-stats-<?php echo esc_attr( $blog_id ); ?>">
						<?php
						if ( ! empty( $instance['stats_site'] ) && 'all' == $instance['stats_site'] ) {
							$blog_info = get_blog_details( $blog_id );
							?>
							<li class="blog-name"><?php echo $blog_info->blogname; ?></li>
							<?php
						}
						?>
						<li id="post-count" class="post-count">
							<div>
								<i class="dashicons dashicons-admin-post"></i>
								<span class="count">
								<?php echo ( ! empty( $stats->post ) ) ? esc_html( $stats->post ) : 0; ?>
							</span>
								<span class="label">Post</span>
							</div>
						</li>
						<li id="page-count" class="page-count">
							<i class="dashicons dashicons-admin-page"></i>
							<span class="count">
							<?php echo ( ! empty( $stats->page ) ) ? esc_html( $stats->page ) : 0; ?>
						</span>
							<span class="label">Page</span>
						</li>
						<li id="comment-count" class="comment-count">
							<i class="dashicons dashicons-admin-comments"></i>
							<span class="count">
							<?php echo ( ! empty( $stats->comment ) ) ? esc_html( $stats->comment ) : 0; ?>
						</span>
							<span class="label">Comment</span>
						</li>
						<li id="users-count" class="users-count">
							<i class="dashicons dashicons-admin-users"></i>
							<span class="count">
							<?php echo ( ! empty( $stats->users ) ) ? esc_html( $stats->users ) : 0; ?>
						</span>
							<span class="label">Users</span>
						</li>
						<?php do_action( 'wpsite_stats_content', $instance, $blog_id ); ?>
					</ul>
					<?php
				}
				?>

			</div>
			<?php
			echo $args['after_widget'];
			wp_enqueue_script( 'wpsits-stats-script' );
			wp_enqueue_style( 'wpsits-stats-style' );
		}

		/**
		 * Back-end widget form.
		 *
		 * @see WP_Widget::form()
		 *
		 * @param array $instance Previously saved values from database.
		 *
		 * @return string|void
		 */
		public function form( $instance ) {
			$title      = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Sites Stats', 'wpsites_stats' );
			$stats_site = ! empty( $instance['stats_site'] ) ? $instance['stats_site'] : 'current';
			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'wpsites_stats' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
					   name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text"
					   value="<?php echo esc_attr( $title ); ?>">
			</p>
			<?php
			if ( is_multisite() ) {
				?>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'stats_site_current' ) ); ?>">
						<input class="widefat"
							   id="<?php echo esc_attr( $this->get_field_id( 'stats_site_current' ) ); ?>"
							   name="<?php echo esc_attr( $this->get_field_name( 'stats_site' ) ); ?>" type="radio"
							<?php checked( 'current', $stats_site ); ?> value="current">
						<?php esc_attr_e( 'Stats for current site.', 'wpsites_stats' ); ?>
					</label>
					<label for="<?php echo esc_attr( $this->get_field_id( 'stats_site_all' ) ); ?>">
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'stats_site_all' ) ); ?>"
							   name="<?php echo esc_attr( $this->get_field_name( 'stats_site' ) ); ?>" type="radio"
							<?php checked( 'all', $stats_site ); ?> value="all">
						<?php esc_attr_e( 'Stats for all subsites.', 'wpsites_stats' ); ?>
					</label>
				</p>
				<?php
			}
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @see WP_Widget::update()
		 *
		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.
		 *
		 * @return array Updated safe values to be saved.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance               = array();
			$instance['title']      = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
			$instance['stats_site'] = ( ! empty( $new_instance['stats_site'] ) ) ? strip_tags( $new_instance['stats_site'] ) : 'current';

			return $instance;
		}
	}
}
