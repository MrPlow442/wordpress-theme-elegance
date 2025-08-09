/**
 * Custom theme JavaScript functionality
 * 
 * @package Elegance
 * @version 1.0.0
 */

( function( $, window, document, undefined ) {
	'use strict';

	var EleganceSectionNavigator = {
        _sections: [],
        _currentSectionIndex: 0,
        _currentSection: null,
        _isScrolling: false,

        init: function() {
            // Get all sections
            this._sections = Array.from(document.querySelectorAll('.snap-section'));
            
            if (this._sections.length === 0) {
                console.warn('No .snap-section elements found');
                return;
            }

            console.log('Found sections:', this._sections.map(s => s.id));

            // Set initial section from hash or default to first
            this._setInitialSection();
            
            // Bind scroll event listener for natural scrolling
            this._bindScrollListener();
            
            console.log('EleganceSectionNavigator initialized');
        },

        _setInitialSection: function() {
            let initialIndex = 0;
            
            if (window.location.hash) {
                const hashSection = window.location.hash.substring(1);
                const foundIndex = this._sections.findIndex(section => 
                    section.id === hashSection || section.getAttribute('data-section') === hashSection
                );
                if (foundIndex !== -1) {
                    initialIndex = foundIndex;
                }
            }
            
            this._currentSectionIndex = initialIndex;
            this._currentSection = this._sections[initialIndex];
            
            const sectionId = this._currentSection.id || this._currentSection.getAttribute('data-section');
            this._updateUrlHash(sectionId);
            this._updateActiveNavigation(sectionId);
            
            // Set initial background without transition
            setTimeout(() => {
                this._updateBackground(sectionId);
            }, 100);
        },

        _bindScrollListener: function() {
            let scrollTimeout;
            
            window.addEventListener('scroll', () => {
                if (this._isScrolling) return; // Ignore during programmatic scrolling
                
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(() => {
                    this._detectSectionChange();
                }, 100);
            });
        },

        _detectSectionChange: function() {
            const scrollTop = window.pageYOffset;
            const viewportHeight = window.innerHeight;
            const viewportCenter = scrollTop + (viewportHeight / 2);
            
            // Find which section the center of viewport is in
            let newSectionIndex = 0;
            
            for (let i = 0; i < this._sections.length; i++) {
                const section = this._sections[i];
                const rect = section.getBoundingClientRect();
                const sectionTop = scrollTop + rect.top;
                const sectionBottom = sectionTop + rect.height;
                
                if (viewportCenter >= sectionTop && viewportCenter <= sectionBottom) {
                    newSectionIndex = i;
                    break;
                }
            }
            
            // If section changed, trigger onLeave
            if (newSectionIndex !== this._currentSectionIndex) {
                this._triggerSectionChange(this._currentSectionIndex, newSectionIndex);
            }
        },

        _triggerSectionChange: function(fromIndex, toIndex) {
            const origin = this._sections[fromIndex];
            const destination = this._sections[toIndex];
            
            const originId = origin.id || origin.getAttribute('data-section');
            const destinationId = destination.id || destination.getAttribute('data-section');
            
            console.log(`Section change: ${originId} -> ${destinationId}`);
            
            // Update current section
            this._currentSectionIndex = toIndex;
            this._currentSection = destination;
            
            // Call our onLeave equivalent
            this._onLeave(origin, destination);
        },

        _onLeave: function(origin, destination) {
            const destinationId = destination.id || destination.getAttribute('data-section');
            
            console.log('onLeave triggered:', {
                from: origin.id || origin.getAttribute('data-section'),
                to: destinationId
            });
            
            // Update URL and navigation
            this._updateUrlHash(destinationId);
            this._updateActiveNavigation(destinationId);
                        
            this._updateBackground(destinationId);
        },

        _updateUrlHash: function(sectionId) {
            const sectionHash = `#${sectionId}`;
            if (window.location.hash === sectionHash) {
                return;
            }
            const url = window.location.pathname + window.location.search + sectionHash;
            history.replaceState(null, '', url);
        },

        _updateActiveNavigation: function(sectionId) {
            // Desktop nav
            document.querySelectorAll('.navbar-nav li').forEach(item => {
                item.classList.remove('active');
            });
            
            const activeLink = document.querySelector(`.navbar-nav a[href="#${sectionId}"]`);
            if (activeLink) {
                activeLink.parentElement.classList.add('active');
            }

            // Mobile nav
            document.querySelectorAll('.navigation-menu li').forEach(item => {
                item.classList.remove('active');
            });
            
            const mobileActiveLink = document.querySelector(`.navigation-menu a[href="#${sectionId}"]`);
            if (mobileActiveLink) {
                mobileActiveLink.parentElement.classList.add('active');
            }
        },

        _updateBackground: function(sectionId) {
            if (typeof EleganceConfig === 'undefined' || typeof EleganceBackgroundManager === 'undefined') {
                return;
            }

            const elements = {
                backgroundVideoElement: document.getElementById(EleganceConfig.videoElementId),
                backgroundImageElement: document.getElementById(EleganceConfig.imageElementId)
            };

            const matchingPage = EleganceConfig.pageInfo.find(page => page && page.name === sectionId);

            if (matchingPage && matchingPage.hasThumbnail && matchingPage.thumbnail) {
                EleganceBackgroundManager.showImage(elements, matchingPage.thumbnail);
                console.log('Background changed to:', matchingPage.thumbnail);
            } else {
                EleganceBackgroundManager.showDefault(EleganceConfig, elements);
                console.log('Background changed to default for section:', sectionId);
            }
        },

        goToSection: function(sectionId) {
            const sectionIndex = this._sections.findIndex(section => 
                section.id === sectionId || section.getAttribute('data-section') === sectionId
            );
            
            if (sectionIndex === -1) {
                console.warn('Section not found:', sectionId);
                return;
            }
            
            this.goToSectionByIndex(sectionIndex);
        },

        goToSectionByIndex: function(index) {
            if (index < 0 || index >= this._sections.length) {
                return;
            }
            
            const origin = this._sections[this._currentSectionIndex];
            const destination = this._sections[index];
            
            // Set scrolling flag
            this._isScrolling = true;
            
            // Scroll to section
            destination.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
            
            // Update section and trigger onLeave
            setTimeout(() => {
                this._currentSectionIndex = index;
                this._currentSection = destination;
                this._onLeave(origin, destination);
                this._isScrolling = false;
            }, 600); // Wait for scroll animation
        },

        next: function() {
            if (this._currentSectionIndex < this._sections.length - 1) {
                this.goToSectionByIndex(this._currentSectionIndex + 1);
            }
        },

        prev: function() {
            if (this._currentSectionIndex > 0) {
                this.goToSectionByIndex(this._currentSectionIndex - 1);
            }
        }
    };

	var EleganceTheme = {
		_scrollTolerance: 5,
		_navButtonUpdateDelay: 350,		
		_navigator: EleganceSectionNavigator,
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
			this._navigator.init();
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

			const sectionId = targetId.substring(1);
			EleganceTheme._navigator.goToSection(sectionId);			
		},
		
		handleNextSection: function ( event ) {
			EleganceTheme._navigator.next();
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
