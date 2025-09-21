/**
 * Theme Core Module - Main application class
 * Handles initialization and coordination of all theme modules
 * 
 * @package Elegance
 * @version 2.0.0
 */

import { LoggerFactory } from './logger.js';
import { EleganceModule } from './module.js';

export class EleganceTheme {
    constructor(config = {}) {
        this.logger = LoggerFactory.createLogger('EleganceTheme');
        this.config = config;
        this.moduleRegistry = new EleganceModuleRegistry(this.logger);        
        this.isInitialized = false;                
        this.handleWindowLoad = this.handleWindowLoad.bind(this);
        this.handleDOMContentLoaded = this.handleDOMContentLoaded.bind(this);
    }

    async init() {
        if (this.isInitialized) {
            this.logger.warn('EleganceTheme: Already initialized');
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
            this.logger.log('EleganceTheme: Initialized successfully');
        } catch (error) {
            this.logger.error('EleganceTheme: Initialization failed', error);
        }
    }

    static triggerEvent(eventName, data) {
        const event = new CustomEvent(eventName, { detail: data });
        document.dispatchEvent(event);
    }

    static bindEvent(eventName, callback) {
        document.addEventListener(eventName, callback);
    }

    static unbindEvent(eventName, callback) {
        document.removeEventListener(eventName, callback);
    }

    registerModule(module) {
        this.moduleRegistry.registerModule(module);
    }

    getModule(name) {
        this.moduleRegistry.getModule(name);
    }

    async handleDOMContentLoaded() {
        await this.moduleRegistry.initializeModules();
        await this.moduleRegistry.postInitializeModules();
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
        this.logger.log('Global click event on', target);
        
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
        for (const [name, module] of this.moduleRegistry.getModules()) {
            const methodName = `on${eventName.charAt(0).toUpperCase() + eventName.slice(1)}`;
            
            if (typeof module[methodName] === 'function') {
                try {
                    module[methodName](data);
                } catch (error) {
                    this.logger.error(`EleganceTheme: Module '${name}' event '${eventName}' failed`, error);
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

        this.moduleRegistry.destroy();
        this.isInitialized = false;
    }
}

export class EleganceModuleRegistry {
    constructor(logger) {
        this.logger = logger;
        this.modules = new Map();
        this.modulesInitialized = false;
    }

    registerModule(module) {
        if (module instanceof EleganceModule === false) {
            this.logger.error(`EleganceTheme: Module '${name}' is not an instance of EleganceModule`);
            return;
        }

        const name = module.name;
        if (this.modules.has(name)) {
            this.logger.warn(`EleganceTheme: Module '${name}' already exists`);
            return;
        }

        module.registry = this;
        this.modules.set(name, module);
        this.logger.log(`EleganceTheme: Module '${name}' registered`);
    }

    getModule(name) {
        return this.modules.get(name) || null;
    }

    getModules() {
        return this.modules;
    }

    async initializeModules() {
        if (this.modulesInitialized) {
            return;
        }

        const initPromises = [];

        for (const [name, module] of this.modules) {
            if (module instanceof EleganceModule) {
                try {
                    this.logger.log(`EleganceTheme: Initializing module '${name}'`);
                    const initResult = module.init();
                    if (initResult instanceof Promise) {
                        initPromises.push(initResult);
                    }
                    this.logger.log(`EleganceTheme: Module '${name}' initialized`);
                } catch (error) {
                    this.logger.error(`EleganceTheme: Module '${name}' initialization failed`, error);
                }
            }
        }
        
        if (initPromises.length > 0) {
            await Promise.all(initPromises);
        }

        this.modulesInitialized = true;
    }

    async postInitializeModules() {
        if (!this.modulesInitialized) {
            this.logger.log('Trying to run postInit before modules were initialized');
            return;
        }

        const postInitPromises = [];

        for (const [name, module] of this.modules) {
            if (module instanceof EleganceModule) {
                try {
                    this.logger.log(`EleganceTheme: Post Initializing module '${name}'`);
                    const initResult = module.postInit();
                    if (initResult instanceof Promise) {
                        postInitPromises.push(initResult);
                    }
                    this.logger.log(`EleganceTheme: Module '${name}' post initialized`);
                } catch (error) {
                    this.logger.error(`EleganceTheme: Module '${name}' post initialization failed`, error);
                }
            }
        }
        
        if (postInitPromises.length > 0) {
            await Promise.all(postInitPromises);
        }
    }

    destroy() {
        for (const [name, module] of this.modules) {
            if (typeof module.destroy === 'function') {
                try {
                    module.destroy();
                    this.logger.log(`EleganceTheme: Module '${name}' destroyed`);
                } catch (error) {
                    this.logger.error(`EleganceTheme: Module '${name}' destruction failed`, error);
                }
            }
        }

        this.modules.clear();
        this.modulesInitialized = false;
    }
}

window.EleganceTheme = EleganceTheme;
