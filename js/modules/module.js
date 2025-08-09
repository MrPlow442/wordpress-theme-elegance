/**
 * Module base class - Provides common functionality for all theme modules
 * 
 * @package Elegance
 * @version 2.0.0
 */

class EleganceModule {
    constructor(name, themeConfig = {}) {        
        if (new.target === EleganceModule) {
            throw new Error("Cannot instantiate abstract class EleganceModule directly");
        }

        if (!name || typeof name !== 'string') {
            throw new Error("Module name must be a non-empty string");
        }

        this.name = name;        
        this.themeConfig = themeConfig;
        this.logger = themeConfig ? new Logger(name, themeConfig.debug) : new Logger(name);
    }


}