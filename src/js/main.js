/**
 * Theme Initialization Script - Main entry point for the refactored theme
 * Initializes all modules and manages the global theme instance
 * 
 * @package Elegance
 * @version 2.0.0
 */

import 'bootstrap';

import './modules/constants.js';
import { LoggerFactory, Logger } from './modules/logger.js';
import { EleganceTheme } from './modules/theme-core.js';

import { AnimationManager } from './modules/animation-manager.js';
import { BackgroundManager } from './modules/background-manager.js';
import { NavigationManager } from './modules/navigation-manager.js';
import { ScrollNavigator } from './modules/scroll-navigator.js';
import { SwiperManager } from './modules/swiper-manager.js';

let eleganceThemeInstance = null;
let rootLogger = new Logger({ moduleName: 'ThemeInit' });

function initializeEleganceTheme(config = {}) {    
    if (eleganceThemeInstance) {
        rootLogger.warn('EleganceTheme: Already initialized');
        return eleganceThemeInstance;
    }

    try {
        rootLogger.setDebug(config.debug);
        LoggerFactory.init(config.debug);        
        eleganceThemeInstance = new EleganceTheme(config);
                
        const scrollNavigator = new ScrollNavigator(config);
        const swiperManager = new SwiperManager(config);
        const backgroundManager = new BackgroundManager(config);
        const navigationManager = new NavigationManager(config);
        const animationManager = new AnimationManager(config);        

        eleganceThemeInstance.registerModule(scrollNavigator); 
        eleganceThemeInstance.registerModule(swiperManager);       
        eleganceThemeInstance.registerModule(backgroundManager);
        eleganceThemeInstance.registerModule(navigationManager);
        eleganceThemeInstance.registerModule(animationManager);        

        eleganceThemeInstance.init();

        if (config.debug) {
            window.eleganceModules = {
                theme: eleganceThemeInstance,
                scrollNavigator,             
                swiperManager,   
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
        
        if (window.eleganceModules) {
            delete window.eleganceModules;
        }
        
        rootLogger.log('EleganceTheme: Destroyed');
    }
}
  
window.initializeBlogPage = function(config) {
    rootLogger.log('EleganceTheme: Legacy initializeBlogPage called');
        
    if (!eleganceThemeInstance) {
        const theme = new EleganceTheme(config);
        const backgroundManager = new BackgroundManager();
        
        theme.registerModule('backgroundManager', backgroundManager);
        theme.init();
        
        eleganceThemeInstance = theme;
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

