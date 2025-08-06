/**
 * Theme functions for background management and fullpage functionality
 * 
 * @package Elegance
 * @version 1.0.0
 */

( function( window, document, undefined ) {
	'use strict';


	var BackgroundManager = {

		setVisibility: function( element, visibility ) {
			if ( !element || !visibility ) {
				return;
			}
			
			var validStates = [ 'hidden', 'visible' ];
			if ( validStates.indexOf( visibility ) === -1 ) {
				return;
			}

			if ( element.classList.contains( visibility ) ) {
				return;
			}

			var inverse = 'hidden' === visibility ? 'visible' : 'hidden';
			
			if ( element.classList.contains( inverse ) ) {
				element.classList.remove( inverse );
			}
			
			element.classList.add( visibility );
		},


		isVideo: function( element ) {
			return element && 'video' === element.tagName.toLowerCase();
		},

		isImage: function( element ) {
			return element && 'img' === element.tagName.toLowerCase();
		},

		getSource: function( element ) {
			if ( !element ) {
				return undefined;
			}
			
			if ( this.isVideo( element ) ) {
				return element.querySelector( 'source' ).src;				
			}
			
			return element.src;
		},

		setSource: function( element, sourceUrl ) {
			if ( !element || !sourceUrl ) {
				return;
			}
			
			if ( this.isVideo( element ) ) {
				var sourceElement = element.querySelector( 'source' );				
				if ( ! sourceElement ) {
					sourceElement = document.createElement( 'source' );
					sourceElement.type = 'video/mp4';
					element.appendChild( sourceElement );
				}
				sourceElement.src = sourceUrl;				
				// Reload video to apply new source
				element.load();
			} else {
				element.src = sourceUrl;
			}
		},

		changeBackgroundElement: function( element, url ) {
			if ( !element || !url || this.getSource( element ) === url ) {
				return;
			}
			
			element.style.opacity = 0;

			// Change source and fade in after transition
			setTimeout( function() {
				BackgroundManager.setSource( element, url );
				element.style.opacity = 1;
				BackgroundManager.setVisibility( element, 'visible' );
			}, 200 );
		},

		swapElementDisplay: function( toHide, toShow, sourceUrl ) {
			if ( toHide ) {
				this.setVisibility( toHide, 'hidden' );
			}
			
			if ( toShow ) {
				this.setElementDisplay( toShow, sourceUrl );
			}
		},

		setElementDisplay: function( element, sourceUrl ) {
			if ( !element ) {
				return;
			}
			
			this.changeBackgroundElement( element, sourceUrl );
			this.setVisibility( element, 'visible' );
		},

		preloadImages: function( urls ) {
			if ( ! Array.isArray( urls ) ) {
				return;
			}
			
			urls.forEach( function( url ) {
				if ( url ) {
					var img = new Image();
					img.src = url;
				}
			} );
		},


		showDefault: function( config, elements ) {
			if ( !config || !elements ) {
				return;
			}
			
			if ( !config.defaultVideoUrl && !config.defaultImageUrl ) {
				return;
			}

			if ( elements.backgroundVideoElement && config.defaultVideoUrl ) {
				this.swapElementDisplay( 
					elements.backgroundImageElement, 
					elements.backgroundVideoElement, 
					config.defaultVideoUrl 
				);
			} else if ( elements.backgroundImageElement && config.defaultImageUrl ) {
				this.swapElementDisplay( 
					elements.backgroundVideoElement, 
					elements.backgroundImageElement, 
					config.defaultImageUrl 
				);
			}
		},


		showImage: function( elements, imageUrl ) {
			if ( !elements || !imageUrl ) {
				return;
			}

			this.swapElementDisplay( 
				elements.backgroundVideoElement, 
				elements.backgroundImageElement, 
				imageUrl 
			);
		}
	};


	function initializeFullpage( config ) {
		if ( !config ) {
			console.warn( 'EleganceTheme: No config provided for fullpage initialization' );
			return;
		}

		console.log('Config: ', config);
		
		var elements = {
			backgroundVideoElement: document.getElementById( config.videoElementId ),
			backgroundImageElement: document.getElementById( config.imageElementId )
		};
		
		var pageImages = [];
		if ( config.pageInfo && Array.isArray( config.pageInfo ) ) {
			pageImages = config.pageInfo
				.filter( function( page ) {
					return page && page.hasThumbnail && page.thumbnail;
				} )
				.map( function( page ) {
					return page.thumbnail;
				} );
		}
		
		BackgroundManager.preloadImages( pageImages );
				
		BackgroundManager.showDefault( config, elements );

		/*
		 		
		var fullpageElement = document.querySelector( '.fullpage-default' );
		
		if ( fullpageElement && 'function' === typeof window.fullpage ) {
			var fullpageOptions = {
				licenseKey: 'C7F41B00-5E824594-9A5EFB99-B556A3D5',
				anchors: config.anchorsJson || [],
				menu: '#nav',
				lazyLoad: true,
				navigation: true,
				slidesNavigation: true,
				navigationPosition: 'right',
				scrollOverflow: true,
				scrollOverflowReset: true,
				responsiveWidth: 768,
				responsiveHeight: 600,
				responsiveSlides: true,
				onLeave: function( origin, destination ) {
					if ( !destination || ! destination.item ) {
						return;
					}
					
					var section = destination.item;
					var sectionName = section.getAttribute( 'data-section' );

					if ( !sectionName || !config.pageInfo ) {
						BackgroundManager.showDefault( config, elements );
						return;
					}

					var matchingPage = config.pageInfo.find( function( page ) {
						return page && page.name === sectionName;
					} );
					
					if ( matchingPage && matchingPage.hasThumbnail && matchingPage.thumbnail ) {
						BackgroundManager.showImage( elements, matchingPage.thumbnail );
					} else {
						BackgroundManager.showDefault( config, elements );
					}
				}
			};
			
			try {
				window.eleganceFullpage = new window.fullpage( '.fullpage-default', fullpageOptions );
			} catch ( error ) {
				console.error( 'EleganceTheme: Error initializing fullPage.js:', error );
			}
		}

		 */
	}

	function initializeBlogPage( config ) {
		if ( ! config ) {
			return;
		}
		
		var elements = {
			backgroundImageElement: document.getElementById( config.imageElementId ),
			backgroundVideoElement: null
		};

		BackgroundManager.showDefault( config, elements );
	}

	function safeInitialize( functionName, initFunction, config ) {
		try {
			if ( 'function' === typeof initFunction ) {
				initFunction( config );
			}
		} catch ( error ) {
			console.error( 'EleganceTheme: Error in ' + functionName + ':', error );
		}
	}
	
	window.initializeFullpage = function( config ) {
		safeInitialize( 'initializeFullpage', initializeFullpage, config );
	};

	window.initializeBlogPage = function( config ) {
		safeInitialize( 'initializeBlogPage', initializeBlogPage, config );
	};
	
	window.EleganceBackgroundManager = BackgroundManager;

} )( window, document );
