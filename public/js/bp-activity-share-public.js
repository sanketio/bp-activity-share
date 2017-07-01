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

				var that             = $( this ),
				    activity_wrapper = that.parents( '.activity' );

				activity_wrapper.find( '.bpas-share-options-wrapper' ).removeClass( 'show' ).addClass( 'hide' );
				activity_wrapper.find( '.bpas-share-options-wrapper a' ).show();
				activity_wrapper.find( '.bp-activity-share-custom' ).hide();
				activity_wrapper.find( '.bpas-share-options, .bpas-custom-share-options' ).val( 'bpas-sitewide-activity' );

			} );

			$( 'body' ).on( 'click', '.bp-activity-share', function() {

				var that        = $( this ),
				    parent      = that.closest( '.activity-item' ),
				    parent_id   = parent.attr( 'id' ).substr( 9, parent.attr( 'id' ).length ),
				    nonce       = $( this ).attr( 'href' ).split( '_wpnonce=' ),
				    share_to    = parent.find( '.bpas-share-options' ).val(),
				    custom_text = '';

				that.addClass( 'loading' );

				if ( 'bpas-share-custom' === share_to ) {

					share_to    = parent.find( '.bpas-custom-share-options' ).val();
					custom_text = parent.find( '.bpas-custom-text' ).val();
				}

				var ajaxdata  = {
					action:        'bp_share_activity',
					'cookie':      bp_get_cookies(),
					'act_id':      parent_id,
					'share_to':    share_to,
					'_wpnonce':    nonce[1],
					'custom_text': custom_text
				};

				$.post( ajaxurl, ajaxdata, function( response ) {

					that.removeClass( 'loading' );
					parent.find( '.bpas-share-options-wrapper' ).removeClass( 'show' ).addClass( 'hide' );
					parent.find( '.bpas-share-options, .bpas-custom-share-options' ).val( 'bpas-sitewide-activity' );
					parent.find( '.bp-activity-share-custom' ).hide();

				} );

				return false;

			} );

			$( 'body' ).on( 'change', '.bpas-share-options', function() {

				var that                  = $( this ),
				    share_option          = that.val(),
				    old_option_wrapper    = that.closest( '.bpas-share-options-wrapper' ),
				    custom_option_wrapper = that.closest( '.activity-content' ).siblings( '.bp-activity-share-custom' );

				if ( 'bpas-share-custom' === share_option ) {

					custom_option_wrapper.show();
					$( 'a', old_option_wrapper ).hide();

				} else {

					custom_option_wrapper.hide();
					$( 'a', old_option_wrapper ).show();
				}

			} );
		}
	};

	$( document ).ready( function() {

		window.BPAS.init();

	} );

} ) ( jQuery );
