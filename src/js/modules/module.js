/**
 * Module base class - Provides common functionality for all theme modules
 * 
 * @package Elegance
 * @version 2.0.0
 */

import { LoggerFactory } from './logger.js';

export class EleganceModule {    
    constructor(name, themeConfig = {}, mute = false) {        
        if (new.target === EleganceModule) {
            throw new Error("Cannot instantiate abstract class EleganceModule directly");
        }

        if (!name || typeof name !== 'string') {
            throw new Error("Module name must be a non-empty string");
        }

        this.name = name;        
        this.themeConfig = themeConfig;
        this.mute = mute;
        this.logger = LoggerFactory.createLogger(name, mute);        
        this.registry = null;
    }

    init() {}

    postInit() {}

    getModule(name) {
        if (!this.registry) {
            this.logger.log('No registry available, this module might not be fully initialized');
            return null;
        }

        return this.registry.getModule(name);
    }
}