/**
 * Custom theme JavaScript functionality
 * 
 * @package Elegance
 * @version 1.0.0
 */

( function( $, window, document, undefined ) {
	'use strict';

	var EleganceTheme = {
		_scrollTolerance: 5,
		_navButtonUpdateDelay: 350,
		_verticalScrollConfig: {
			behavior: 'smooth',
			block: 'start'
		},

		_configureHorizontalScroll: function(left) {
			return { 
				left: left, 
				behavior: 'smooth' 
			};
		},

		init: function() {
			this.bindEvents();			
		},

		bindEvents: function() {
			// TODO: lower jQuery dependency here
			$( window ).on( 'load', this.handleWindowLoad );
			$( document ).on( 'click', '.navbar-toggle', this.handleNavbarToggle );						
			$( document ).on( 'click', '.menu-trigger', this.handleMenuTrigger );
			$( document ).on( 'click', '.side-menu .navbar-nav li a', this.handleSideMenuClick );

			// navigation bindings
			document
				.querySelectorAll('.navigation-menu > li > a')
				.forEach(link => link.addEventListener('click', this.handleNavigationClick));

			document
				.querySelector('.next-section')
				?.addEventListener('click', this.handleNextSection);

			// horizontal navigation bindings
			this.initHorizontalScrolling('#notices');			
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

		initHorizontalScrolling: function(selector, config = {}) {
			if (!selector) {
				return;
			}

			const defaultConfig = {
				autoScroll: false,
				autoScrollDelay: 2000,
				showNavButtons: true,
				showDots: true
			};

			config = { ...defaultConfig, ...config };

			const container = document.querySelector(selector + ' .horizontal-scroll-container')
			const navContainer = container.parentElement.querySelector('.horizontal-scroll-nav-top');
			const navProps = {					
				navContainer: navContainer,
				previousButton: navContainer?.querySelector('.scroll-prev'),
				nextButton: navContainer?.querySelector('.scroll-next'),					
				dots: navContainer?.querySelectorAll('.scroll-dot'),
				slides: container.querySelectorAll('.horizontal-slide'),
				currentSlide: 0
			};
			
			navProps.previousButton?.addEventListener('click', () => {
				if (navProps.currentSlide <= 0) {						
					return;
				}								
				--navProps.currentSlide;
				container.scrollBy(this._configureHorizontalScroll(-container.offsetWidth));
				setTimeout(() => this.updateNavState(container, navProps), this._navButtonUpdateDelay);									
			});

			navProps.nextButton?.addEventListener('click', () => {
				if (navProps.currentSlide >= navProps.slides?.length) {
					return;
				}
				++navProps.currentSlide;
				container.scrollBy(this._configureHorizontalScroll(container.offsetWidth));
				setTimeout(() => this.updateNavState(container, navProps), this._navButtonUpdateDelay);
			});

			navProps.dots?.forEach((dot, index) => {
				dot.addEventListener('click', () => {
					const direction = index - navProps.currentSlide;
					navProps.currentSlide = index;
					container.scrollBy(this._configureHorizontalScroll(direction * container.offsetWidth));
					setTimeout(() => this.updateNavState(container, navProps), this._navButtonUpdateDelay);
				});
			});
			
			this.updateNavState(container, navProps);
		},

		updateNavState: function (container, navProps) {
			const currentSlide = navProps.currentSlide;
			const slidesCount = navProps.slides?.length;
			navProps.dots?.forEach((dot, index) => {
				dot.classList.toggle('active', index === currentSlide);
			});

			navProps.previousButton.disabled = currentSlide === 0;
			navProps.nextButton.disabled = currentSlide === slidesCount - 1; 			
		},

		isHorizontalSlideAtBegininning: function ( container ) {
			return container.scrollLeft <= this._scrollTolerance;
		},

		isHorizontalSlideAtEnd: function ( container ) {
			return Math.ceil(container.scrollLeft + container.offsetWidth) >= container.scrollWidth - this._scrollTolerance;
		},

		hideElementOnCondition: function ( element, condition ) {
			const conditionFulfilled = condition();
			element.style.visibility = conditionFulfilled ? 'hidden' : 'visible';
			element.style.opacity = conditionFulfilled ? 0 : 1;
			element.style.transition = conditionFulfilled ? 'visibility 0s 500ms, opacity 500ms linear' : 'opacity 500ms linear';
		},

		handleNavbarToggle: function( event ) {
			event.preventDefault();
			$( '.navbar-collapse' ).slideToggle( 300 );
		},

		handleNavigationClick: function( event ) {
			event.preventDefault();
			$( '.navbar-collapse' ).slideUp( 300 );
			const targetId = this.getAttribute('href');
			if (!targetId.startsWith('#')) {
				return;
			}

			const targetSection = document.querySelector(targetId);
			if (targetSection) {
                targetSection.scrollIntoView(this._verticalScrollConfig);
            }
		},
		
		handleNextSection: function ( event ) {
			const sections = document.querySelectorAll('.snap-section');
			const currentScroll = window.pageYOffset;
			let currentSection = 0;

			sections.forEach((section, index) => {
                if (section.offsetTop <= currentScroll + 100) {
                    currentSection = index;
                }
            });
            
            if (currentSection < sections.length - 1) {
                sections[currentSection + 1].scrollIntoView(this._verticalScrollConfig);
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
