( function( $ ) {

	window.BPAS = {
		init: function() {
			this.bpasShareActivity();
		},
		bpasShareActivity: function() {
			$( '.bp-activity-share' ).on( 'click', function() {
				var that = $( this );

				that.addClass( 'loading' );

				var parent 	  = that.closest( '.activity-item' );
				var parent_id = parent.attr( 'id' ).substr( 9, parent.attr( 'id' ).length );
				var nonce  	  = $( this ).attr( 'href' ).split( '_wpnonce=' );
				var ajaxdata  = {
					action: 'bp_share_activity',
					'cookie': bp_get_cookies(),
					'act_id': parent_id,
					'_wpnonce': nonce[1]
				};

				$.post( ajaxurl, ajaxdata, function( response ) {
					that.removeClass( 'loading' );
				} );

				return false;
			} );
		}
	};

	$( document ).ready( function() {
		window.BPAS.init();
	} );

} ) ( jQuery );
