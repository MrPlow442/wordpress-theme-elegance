/**
 * Custom theme JavaScript functionality
 * 
 * @package Elegance
 * @version 1.0.0
 */

( function( $, window, document, undefined ) {
	'use strict';

	var EleganceTheme = {
		init: function() {
			this.bindEvents();			
		},

		bindEvents: function() {
			$( window ).on( 'load', this.handleWindowLoad );
			$( document ).on( 'click', '.navbar-toggle', this.handleNavbarToggle );
			$( document ).on( 'click', '.navigation-menu > li > a', this.handleNavigationClick );
			$( document ).on( 'click', '.next-section', this.handleNextSection );
			$( document ).on( 'click', '.menu-trigger', this.handleMenuTrigger );
			$( document ).on( 'click', '.side-menu .navbar-nav li a', this.handleSideMenuClick );
		},

		/**
		 * Handle window load event
		 */
		handleWindowLoad: function() {
			$( '.preloader' ).fadeOut();
			
			EleganceTheme.initAnimations();
		},


		initAnimations: function() {
			$( '.animated-row' ).each( function() {
				var $row = $( this );
				
				$row.find( '.animate' ).each( function( index ) {
					var $item = $( this );
					var animation = $item.data( 'animate' );
					
					if ( ! animation ) {
						return;
					}
					
					$item.on( 'inview', function( event, isInView ) {
						if ( isInView ) {
							setTimeout( function() {
								$item.addClass( 'animated ' + animation ).removeClass( 'animate' );
							}, index * 50 );
						} else if ( ! EleganceTheme.isSmallScreen() ) {
							$item.removeClass( 'animated ' + animation ).addClass( 'animate' );
						}
					} );
				} );
			} );
		},

		handleNavbarToggle: function( event ) {
			event.preventDefault();
			$( '.navbar-collapse' ).slideToggle( 300 );
		},

		handleNavigationClick: function() {
			$( '.navbar-collapse' ).slideUp( 300 );
		},

		handleNextSection: function( event ) {
			event.preventDefault();
			
			if ( typeof fullpage_api !== 'undefined' && fullpage_api.moveSectionDown ) {
				fullpage_api.moveSectionDown();
			}
		},

		handleMenuTrigger: function( event ) {
			event.preventDefault();
			$( 'body' ).toggleClass( 'sidemenu-open' );
		},

		handleSideMenuClick: function() {
			$( 'body' ).removeClass( 'sidemenu-open' );
		},

		isSmallScreen: function() {
			return $( window ).width() <= 767;
		},

		getWindowWidth: function() {
			return $( window ).width();
		},

		getWindowHeight: function() {
			return $( window ).height();
		}
	};
	
	$( document ).ready( function() {
		EleganceTheme.init();
	} );
	
	window.EleganceTheme = EleganceTheme;

} )( jQuery, window, document );
