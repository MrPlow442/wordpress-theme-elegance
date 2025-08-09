/**
 * Theme Core Module - Main application class
 * Handles initialization and coordination of all theme modules
 * 
 * @package Elegance
 * @version 2.0.0
 */

class EleganceTheme {
    constructor(config = {}) {
        this.logger = new Logger('EleganceTheme', config.debug);
        this.config = config;
        this.modules = new Map();
        this.isInitialized = false;                
        this.handleWindowLoad = this.handleWindowLoad.bind(this);
        this.handleDOMContentLoaded = this.handleDOMContentLoaded.bind(this);
    }

    async init() {
        if (this.isInitialized) {
            logger.warn('EleganceTheme: Already initialized');
            return;
        }

        try {            
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', this.handleDOMContentLoaded);
            } else {
                this.handleDOMContentLoaded();
            }
            
            if (document.readyState === 'complete') {
                this.handleWindowLoad();
            } else {
                window.addEventListener('load', this.handleWindowLoad);
            }

            this.isInitialized = true;
            logger.log('EleganceTheme: Initialized successfully');
        } catch (error) {
            logger.error('EleganceTheme: Initialization failed', error);
        }
    }

    registerModule(name, module) {
        if (this.modules.has(name)) {
            logger.warn(`EleganceTheme: Module '${name}' already exists`);
            return;
        }

        this.modules.set(name, module);
        logger.log(`EleganceTheme: Module '${name}' registered`);
    }

    getModule(name) {
        return this.modules.get(name) || null;
    }

    async initializeModules() {
        const initPromises = [];

        for (const [name, module] of this.modules) {
            if (typeof module.init === 'function') {
                try {
                    const initResult = module.init(this.config);
                    if (initResult instanceof Promise) {
                        initPromises.push(initResult);
                    }
                    logger.log(`EleganceTheme: Module '${name}' initialized`);
                } catch (error) {
                    logger.error(`EleganceTheme: Module '${name}' initialization failed`, error);
                }
            }
        }
        
        if (initPromises.length > 0) {
            await Promise.all(initPromises);
        }
    }

    async handleDOMContentLoaded() {
        await this.initializeModules();
        this.bindGlobalEvents();
    }

    handleWindowLoad() {
        this.hidePreloader();
        this.triggerModuleEvent('windowLoaded');
    }

    bindGlobalEvents() {        
        document.addEventListener('click', this.handleGlobalClick.bind(this));
        window.addEventListener('resize', this.handleWindowResize.bind(this));
    }

    handleGlobalClick(event) {        
        const target = event.target;
        
        if (target.matches('.navbar-toggle')) {
            event.preventDefault();
            this.triggerModuleEvent('navbarToggle', { target, event });
        }
        
        if (target.matches('.menu-trigger')) {
            event.preventDefault();
            this.triggerModuleEvent('menuTrigger', { target, event });
        }
    }

    handleWindowResize() {
        this.triggerModuleEvent('windowResize', {
            width: window.innerWidth,
            height: window.innerHeight
        });
    }

    triggerModuleEvent(eventName, data = null) {
        for (const [name, module] of this.modules) {
            const methodName = `on${eventName.charAt(0).toUpperCase() + eventName.slice(1)}`;
            
            if (typeof module[methodName] === 'function') {
                try {
                    module[methodName](data);
                } catch (error) {
                    logger.error(`EleganceTheme: Module '${name}' event '${eventName}' failed`, error);
                }
            }
        }
    }

    hidePreloader() {
        const preloader = document.querySelector('.preloader');
        if (preloader) {
            preloader.style.transition = 'opacity 0.5s ease-out';
            preloader.style.opacity = '0';
            
            setTimeout(() => {
                preloader.style.display = 'none';
            }, 500);
        }
    }

    isSmallScreen() {
        return window.innerWidth <= 767;
    }

    getWindowDimensions() {
        return {
            width: window.innerWidth,
            height: window.innerHeight
        };
    }

    destroy() {        
        document.removeEventListener('DOMContentLoaded', this.handleDOMContentLoaded);
        window.removeEventListener('load', this.handleWindowLoad);
        document.removeEventListener('click', this.handleGlobalClick);
        window.removeEventListener('resize', this.handleWindowResize);
        
        for (const [name, module] of this.modules) {
            if (typeof module.destroy === 'function') {
                try {
                    module.destroy();
                    logger.log(`EleganceTheme: Module '${name}' destroyed`);
                } catch (error) {
                    logger.error(`EleganceTheme: Module '${name}' destruction failed`, error);
                }
            }
        }

        this.modules.clear();
        this.isInitialized = false;
    }
}

window.EleganceTheme = EleganceTheme;
