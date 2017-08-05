/*jshint -W065 */
jQuery( document ).ready( function() {

	var blogId = jQuery( '#wpsite-stats-content' ).data( 'blog_stats' );
	var wpsitesstats = {
		init: function() {
			setTimeout( wpsitesstats.heartbeat_states, 1000 );
		},
		render_stats: function( blogId, key, value ) {
			var _selector = 'ul#wpsite-stats-' + blogId + ' li#' + key + '-count span.count';
			var _this = jQuery( _selector );
      var isUpdated = false;

      if ( _this.length > 0 ) {
        isUpdated = ( parseInt( _this.text() ) !== parseInt( value ) );
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
			jQuery.ajax({
				/* jshint ignore:start */
				'url': wpSitsStats.stats_url,
				/* jshint ignore:end */
				'success': function( data ) {
          var stats = '';
					if ( data ) {

						if ( 'all' === blogId ) {
							jQuery.each( data, function( blogId, stats ) {

								jQuery.each( stats, function( key, value ) {

									wpsitesstats.render_stats( blogId, key, value );

								});

							});
						} else {
							stats = data[blogId];

							jQuery.each( stats, function( key, value ) {

								wpsitesstats.render_stats( blogId, key, value );

							});

						}

					}

					/* jshint ignore:start */
					setTimeout( wpsitesstats.heartbeat_states, wpSitsStats.stats_interval );
					/* jshint ignore:end */
				}
			});
		}
	};
	wpsitesstats.init();
});
