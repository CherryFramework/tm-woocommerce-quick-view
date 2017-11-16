( function( $, settings, ids, styles ) {

	'use strict';

	var tmQuickView = {
		css: {
			btn: '[data-action="quick-view-button"]',
			popup: '.tm-quick-view-popup',
			closeBtn: '.tm-quick-view-popup__close',
			overlay: '.tm-quick-view-popup__overlay',
			prev: '.quick-view-prev',
			next: '.quick-view-next',
			close: '.quick-view-close',
		},
		popup: null,
		content: null,

		init: function() {

			$( document ).on( 'click.tmQuickView', tmQuickView.css.btn, tmQuickView.initPopup )
				.on( 'click.tmQuickView', tmQuickView.css.prev, tmQuickView.initDirectionButtons )
				.on( 'click.tmQuickView', tmQuickView.css.next, tmQuickView.initDirectionButtons )
				.on( 'click.tmQuickView', tmQuickView.css.closeBtn, tmQuickView.closePopup )
				.on( 'click.tmQuickView', tmQuickView.css.close, tmQuickView.closePopup )
				.on( 'click.tmQuickView', tmQuickView.css.overlay, tmQuickView.closePopup );

			tmQuickView.prepareStyles();

		},

		prepareObjects: function() {
			if ( null === tmQuickView.popup ) {
				tmQuickView.popup = $( tmQuickView.css.popup );
			}
			if ( null === tmQuickView.content ) {
				tmQuickView.content = $( '.tm-quick-view-popup__content', tmQuickView.popup );
			}
		},

		closePopup: function( event ) {

			var timeout;

			tmQuickView.popup.toggleClass( 'hide-animation show-animation' );

			clearTimeout( timeout );
			timeout = setTimeout( function() {
				tmQuickView.popup.removeClass( 'hide-animation' );
				tmQuickView.content.html( settings.loader );
			}, 500 );
		},

		prepareStyles: function() {

			var $head = $( 'head' );

			$.each( styles, function( index, style ) {
				$head.append( style );
			});
		},

		initPopup: function( event ) {

			var $button = $( this ),
				pid     = $button.data( 'product' ),
				data    = {
					action: 'tm_woo_quick_view',
					product: pid,
				};

			tmQuickView.prepareObjects();
			tmQuickView.prepareDirectionButtons( pid );

			event.preventDefault();

			$.ajax({
				url: settings.ajaxurl,
				type: 'get',
				dataType: 'json',
				data: data,
			}).done( function( response ) {
				tmQuickView.content.html( response.data.content );
				tmQuickView.reinitDefaultWooActions();
				$( document ).trigger( 'tm-woo-quick-view-on-show' );
			});

			tmQuickView.popup.addClass( 'show-animation' );

		},

		initDirectionButtons: function( event ) {

			if ( $( this ).hasClass( 'disabled' ) ) {
				return;
			}

			tmQuickView.content.html( settings.loader );
			tmQuickView.initPopup.call( this, event );
		},

		prepareDirectionButtons: function( pid ) {

			var curIndex = ids.indexOf( pid ),
				nxIndex  = -1,
				prIndex  = -1;

			if ( 0 > curIndex ) {
				$( tmQuickView.css.prev ).addClass( 'disabled' );
				$( tmQuickView.css.next ).addClass( 'disabled' );
				return;
			}

			nxIndex = curIndex + 1;
			prIndex = curIndex - 1;

			if ( prIndex < 0 ) {
				$( tmQuickView.css.prev ).addClass( 'disabled' );
			} else {
				$( tmQuickView.css.prev ).removeClass( 'disabled' ).data( 'product', ids[ prIndex ] );
			}

			if ( curIndex === ids.length - 1 ) {
				$( tmQuickView.css.next ).addClass( 'disabled' );
			} else {
				$( tmQuickView.css.next ).removeClass( 'disabled' ).data( 'product', ids[ nxIndex ] );
			}
		},

		reinitDefaultWooActions: function() {

			$( '.variations_form' ).each( function() {
				$( this ).wc_variation_form();
			});

			if ( $( '.woocommerce-product-gallery' ).length ) {
				$( '.woocommerce-product-gallery' ).each( function() {
					$( this ).wc_product_gallery();
				} );
			}
		}
	};

	tmQuickView.init();

}( jQuery, window.tmQuickViewData, window.tmQuickViewIds, window.tmQuickViewCSS ) );
