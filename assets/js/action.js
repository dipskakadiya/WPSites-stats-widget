/*jshint -W065 */
jQuery( document ).ready( function() {

	var wpsitesstats = {
		init: function() {
			setTimeout( wpsitesstats.heartbeat_states, 1000 );
		},
		render_stats: function( key, value ) {
			var _selector = 'ul#wpsite-stats li#' + key + '-count span.count';
			var _this = jQuery( _selector );
			var isUpdated = false;

			if ( _this.length > 0 ) {
				isUpdated =
						parseInt( _this.text() ) !== parseInt( value )
				;
				_this.text( value );

				if ( isUpdated ) {
					_this.css( 'font-weight', 'bold' );

					setTimeout( function() {
						_this.css( 'font-weight', 'normal' );
					}, 1000 );

				}

			}

		},
		heartbeat_states: function() {
			var blogStats = jQuery( '#wpsite-stats-content' ).
					data( 'blog_stats' );

			/* jshint ignore:start */
			var apiUrl = wpSitsStats.stats_url;
			/* jshint ignore:end */
			if ( 'all' === blogStats ) {
				/* jshint ignore:start */
				apiUrl = wpSitsStats.stats_url_total;
				/* jshint ignore:end */
			}

			jQuery.ajax( {
				/* jshint ignore:start */
				'url': apiUrl,
				/* jshint ignore:end */
				'success': function( stats ) {
					if ( stats ) {
						jQuery.each( stats, function( key, value ) {
							wpsitesstats.render_stats( key, value );
						} );
					}

					/* jshint ignore:start */
					setTimeout( wpsitesstats.heartbeat_states,
							wpSitsStats.stats_interval );
					/* jshint ignore:end */
				}
			} );
		}
	};
	wpsitesstats.init();
} );
