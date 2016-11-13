( function( $ ) {

	window.BPAS = {
		init: function() {
			this.bpasShareActivity();
		},
		bpasShareActivity: function() {
			$( 'body' ).on( 'click', '.bpas-show-share-options', function( e ) {
				e.preventDefault();

				var that = $( this );

				that.next( '.bpas-share-options-wrapper' ).removeClass( 'hide' ).addClass( 'show' );
			} );

			$( 'body' ).on( 'click', '.bpas-cancel', function( e ) {
				e.preventDefault();

				var that = $( this );

				that.parent( '.bpas-share-options-wrapper' ).removeClass( 'show' ).addClass( 'hide' );
			} );

			$( 'body' ).on( 'click', '.bp-activity-share', function() {
				var that = $( this );

				that.addClass( 'loading' );

				var parent 	  = that.closest( '.activity-item' );
				var parent_id = parent.attr( 'id' ).substr( 9, parent.attr( 'id' ).length );
				var nonce  	  = $( this ).attr( 'href' ).split( '_wpnonce=' );
				var share_to  = that.prev( '.bpas-share-options' ).val();
				var ajaxdata  = {
					action: 'bp_share_activity',
					'cookie': bp_get_cookies(),
					'act_id': parent_id,
					'share_to': share_to,
					'_wpnonce': nonce[1]
				};

				$.post( ajaxurl, ajaxdata, function( response ) {
					that.removeClass( 'loading' );
					that.parent( '.bpas-share-options-wrapper' ).removeClass( 'show' ).addClass( 'hide' );
				} );

				return false;
			} );
		}
	};

	$( document ).ready( function() {
		window.BPAS.init();
	} );

} ) ( jQuery );
