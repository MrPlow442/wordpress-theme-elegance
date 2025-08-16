/**
 * Theme Initialization Script - Main entry point for the refactored theme
 * Initializes all modules and manages the global theme instance
 * 
 * @package Elegance
 * @version 2.0.0
 */

// Global theme instance
let eleganceThemeInstance = null;
let rootLogger = typeof Logger !== 'undefined' ? new Logger('ThemeInit', true) : console;

function initializeEleganceTheme(config = {}) {
    // Prevent multiple initializations
    if (eleganceThemeInstance) {
        rootLogger.warn('EleganceTheme: Already initialized');
        return eleganceThemeInstance;
    }

    try {
        LoggerFactory.init(config);
        // Create main theme instance
        eleganceThemeInstance = new EleganceTheme(config);

        // Create and register all modules
        const scrollNavigator = new ScrollNavigator(config, false);
        const backgroundManager = new BackgroundManager(config, true);
        const navigationManager = new NavigationManager(config, false);
        const animationManager = new AnimationManager(config, true);        

        // Register modules with the theme
        eleganceThemeInstance.registerModule(scrollNavigator);        
        eleganceThemeInstance.registerModule(backgroundManager);
        eleganceThemeInstance.registerModule(navigationManager);
        eleganceThemeInstance.registerModule(animationManager);        

        // Initialize the theme
        eleganceThemeInstance.init();

        // Make modules available globally for debugging (development only)
        if (config.debug) {
            window.eleganceModules = {
                theme: eleganceThemeInstance,
                scrollNavigator,                
                backgroundManager,
                navigationManager,
                animationManager                
            };
        }

        rootLogger.log('EleganceTheme: All modules initialized successfully');
        return eleganceThemeInstance;

    } catch (error) {
        rootLogger.error('EleganceTheme: Initialization failed', error);
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
        
        rootLogger.log('EleganceTheme: Destroyed');
    }
}

// Legacy scroll snap initialization (replaces initializeScrollSnap)
window.initializeScrollSnap = function(config) {
    rootLogger.log('EleganceTheme: Legacy initializeScrollSnap called');
    initializeEleganceTheme(config);
};

// Legacy blog page initialization (replaces initializeBlogPage)  
window.initializeBlogPage = function(config) {
    rootLogger.log('EleganceTheme: Legacy initializeBlogPage called');
    
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
    rootLogger.warn('EleganceTheme: initializeFullpage is deprecated, using scroll snap instead');
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
        rootLogger.log('EleganceTheme: Auto-initializing with WordPress config');
        initializeEleganceTheme(EleganceConfig);
    } else {
        rootLogger.log('EleganceTheme: No config found, waiting for manual initialization');
    }
});

if (typeof jQuery !== 'undefined') {
    jQuery(document).ready(function($) {
        // This ensures compatibility with the existing WordPress enqueue system
        // The theme will initialize via DOMContentLoaded above, but we maintain
        // jQuery ready as a fallback for any WordPress-specific timing issues
        
        if (!eleganceThemeInstance && typeof EleganceConfig !== 'undefined') {
            rootLogger.log('EleganceTheme: jQuery fallback initialization');
            initializeEleganceTheme(EleganceConfig);
        }
    });
}
   
window.initializeEleganceTheme = initializeEleganceTheme;
window.getEleganceTheme = getEleganceTheme;
window.destroyEleganceTheme = destroyEleganceTheme;

