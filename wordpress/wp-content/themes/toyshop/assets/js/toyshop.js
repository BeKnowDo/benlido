/**
 * toyshop.js
 *
 * Javascript used by the Toyshop theme.
 */
( function() {
	jQuery( window ).load( function() {
		jQuery( 'body' ).addClass( 'loaded' );
		jQuery( '.site' ).addClass( 'animated fadeIn' );
		jQuery( '.single_add_to_cart_button, .checkout-button' ).addClass( 'animated bounce' );

		// The star rating on single product pages
		var value = jQuery( '.star-rating > span' ).width();
		jQuery( '.woocommerce-product-rating .star-rating > span' ).css( 'width', 0 );
		jQuery( '.woocommerce-product-rating .star-rating > span' ).animate({
			width: value
		}, 1500, function() {
		// Animation complete.
		});

		// Animate tabs
		jQuery( '.woocommerce-tabs ul.tabs li a' ).click( function () {
        	var destination = jQuery( this ).attr( 'href' );
        	jQuery( '.woocommerce-tabs' ).find( destination ).addClass( 'animated bounceInUp' );
    	});

		if ( jQuery( window ).width() > 767 ) {

			// Animate product headings
			jQuery( '.site-main ul.products li.product:not(.product-category)' ).hover( function () {
	        	jQuery( this ).find( '.button' ).addClass( 'animated bounceIn' );
	    	}, function() {
	    		jQuery( this ).find( '.button' ).removeClass( 'bounceIn' );
	    	});

	    	// Product button positioning
			jQuery( '.site-main ul.products li.product .button' ).each(function() {
				var button 	= jQuery( this );
				var height 	= button.outerHeight();
				var width 	= button.outerWidth();

				button.css( 'margin-top', -height/2 ).css( 'margin-left', -width/2 );
			});

			/**
			 * The homepage product tabs
			 */

			// Create an empty `ul` for the tabs
			if ( jQuery( '.storefront-product-categories' ).size() ) {
				jQuery( '.storefront-product-categories' ).after( '<ul class="tabs"></ul>' );
			} else {
				jQuery( '.storefront-product-section' ).first().before( '<ul class="tabs"></ul>' );
			}

			// Create the list items based on the titles in each of the product sections
			jQuery( '.storefront-product-section:not(".storefront-product-categories"):not(".storefront-reviews"):not(".storefront-blog"):not(".storefront-homepage-contact-section") .section-title' ).each( function(i) {
			    var current = jQuery(this);
			    current.attr( 'section' );
			    jQuery( 'ul.tabs' ).append( '<li class="' + current.html().toLowerCase() + '"><a href="#section' +
			        i + '">' +
			        current.html() + '</a></li>' );
			});

			// Now hide the actual section titles
			jQuery( '.storefront-product-section:not(".storefront-product-categories"):not(".storefront-reviews"):not(".storefront-blog"):not(".storefront-homepage-contact-section") .section-title' ).hide();

			// Give the first tab an active class
			jQuery( 'ul.tabs li:first-child a' ).addClass( 'active' );

			// Assign an id to each of the product sections for internal linking
			jQuery( '.storefront-product-section:not(".storefront-product-categories"):not(".storefront-reviews"):not(".storefront-blog"):not(".storefront-homepage-contact-section")' ).each( function(i) {
			    var current = jQuery(this);
			    current.attr( 'id', 'section' );
			    jQuery( this ).attr( 'id', 'section' + i );
			});

			// The tabs
			jQuery( '.storefront-product-section:not(".storefront-product-categories"):not(".storefront-reviews"):not(".storefront-blog"):not(".storefront-homepage-contact-section")' ).hide();
			jQuery( '.storefront-product-section:not(".storefront-product-categories"):not(".storefront-reviews"):not(".storefront-blog"):not(".storefront-homepage-contact-section"):first' ).show();

			jQuery( '.tabs li' ).click(function( e ) {
				e.preventDefault();
			    jQuery( '.tabs li a' ).removeClass( 'active' );
			    jQuery( this ).find( 'a' ).addClass( 'active' );
			    jQuery( '.storefront-product-section:not(".storefront-product-categories"):not(".storefront-reviews"):not(".storefront-blog"):not(".storefront-homepage-contact-section")' ).hide();

			    var indexer = jQuery( this ).index(); //gets the current index of (this) which is .tabs li
			    jQuery( '.storefront-product-section:not(".storefront-product-categories"):eq( ' + indexer + ' )' ).fadeIn(); //uses whatever index the link has to open the corresponding box
			});

	    }
	});

} )();
