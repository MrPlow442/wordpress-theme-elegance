/**
 * Theme Initialization Script - Main entry point for the refactored theme
 * Initializes all modules and manages the global theme instance
 * 
 * @package Elegance
 * @version 2.0.0
 */

// Global theme instance
let eleganceThemeInstance = null;
let logger = typeof Logger !== 'undefined' ? new Logger('ThemeInit', true) : console;

function initializeEleganceTheme(config = {}) {
    // Prevent multiple initializations
    if (eleganceThemeInstance) {
        logger.warn('EleganceTheme: Already initialized');
        return eleganceThemeInstance;
    }

    try {        
        // Create main theme instance
        eleganceThemeInstance = new EleganceTheme(config);

        // Create and register all modules
        const sectionNavigator = new SectionNavigator(config);
        const backgroundManager = new BackgroundManager(config);
        const navigationManager = new NavigationManager(config);
        const animationManager = new AnimationManager(config);
        const horizontalScrollManager = new HorizontalScrollManager(config);

        // Register modules with the theme
        eleganceThemeInstance.registerModule('sectionNavigator', sectionNavigator);
        eleganceThemeInstance.registerModule('backgroundManager', backgroundManager);
        eleganceThemeInstance.registerModule('navigationManager', navigationManager);
        eleganceThemeInstance.registerModule('animationManager', animationManager);
        eleganceThemeInstance.registerModule('horizontalScrollManager', horizontalScrollManager);

        // Initialize the theme
        eleganceThemeInstance.init();

        // Make modules available globally for debugging (development only)
        if (config.debug) {
            window.eleganceModules = {
                theme: eleganceThemeInstance,
                sectionNavigator,
                backgroundManager,
                navigationManager,
                animationManager,
                horizontalScrollManager
            };
        }

        logger.log('EleganceTheme: All modules initialized successfully');
        return eleganceThemeInstance;

    } catch (error) {
        logger.error('EleganceTheme: Initialization failed', error);
        return null;
    }
}

function getEleganceTheme() {
    return eleganceThemeInstance;
}

function destroyEleganceTheme() {
    if (eleganceThemeInstance) {
        eleganceThemeInstance.destroy();
        eleganceThemeInstance = null;
        
        // Clean up global debugging variables
        if (window.eleganceModules) {
            delete window.eleganceModules;
        }
        
        logger.log('EleganceTheme: Destroyed');
    }
}

// Legacy scroll snap initialization (replaces initializeScrollSnap)
window.initializeScrollSnap = function(config) {
    logger.log('EleganceTheme: Legacy initializeScrollSnap called');
    initializeEleganceTheme(config);
};

// Legacy blog page initialization (replaces initializeBlogPage)  
window.initializeBlogPage = function(config) {
    logger.log('EleganceTheme: Legacy initializeBlogPage called');
    
    // For blog pages, we only need background manager
    if (!eleganceThemeInstance) {
        const theme = new EleganceTheme(config);
        const backgroundManager = new BackgroundManager();
        
        theme.registerModule('backgroundManager', backgroundManager);
        theme.init();
        
        eleganceThemeInstance = theme;
    }
};

// Legacy fullpage initialization (deprecated, redirects to scroll snap)
window.initializeFullpage = function(config) {
    logger.warn('EleganceTheme: initializeFullpage is deprecated, using scroll snap instead');
    initializeEleganceTheme(config);
};

// Maintain legacy background manager reference
window.EleganceBackgroundManager = {
    showDefault: function(config, elements) {
        const theme = getEleganceTheme();
        if (theme) {
            const bgManager = theme.getModule('backgroundManager');
            if (bgManager) {
                bgManager.showDefaultBackground();
            }
        }
    },
    
    showImage: function(elements, imageUrl) {
        const theme = getEleganceTheme();
        if (theme) {
            const bgManager = theme.getModule('backgroundManager');
            if (bgManager) {
                bgManager.showImage(imageUrl);
            }
        }
    }
};

// Auto-initialize when DOM is ready if EleganceConfig is available
document.addEventListener('DOMContentLoaded', function() {
    // Check if WordPress has provided configuration
    if (typeof EleganceConfig !== 'undefined') {
        logger.log('EleganceTheme: Auto-initializing with WordPress config');
        initializeEleganceTheme(EleganceConfig);
    } else {
        logger.log('EleganceTheme: No config found, waiting for manual initialization');
    }
});

if (typeof jQuery !== 'undefined') {
    jQuery(document).ready(function($) {
        // This ensures compatibility with the existing WordPress enqueue system
        // The theme will initialize via DOMContentLoaded above, but we maintain
        // jQuery ready as a fallback for any WordPress-specific timing issues
        
        if (!eleganceThemeInstance && typeof EleganceConfig !== 'undefined') {
            logger.log('EleganceTheme: jQuery fallback initialization');
            initializeEleganceTheme(EleganceConfig);
        }
    });
}
   
window.initializeEleganceTheme = initializeEleganceTheme;
window.getEleganceTheme = getEleganceTheme;
window.destroyEleganceTheme = destroyEleganceTheme;

