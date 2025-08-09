/**
 * Module base class - Provides common functionality for all theme modules
 * 
 * @package Elegance
 * @version 2.0.0
 */

class EleganceModule {
    constructor(name, config) {
        if (new.target === EleganceModule) {
            throw new Error("Cannot instantiate abstract class EleganceModule directly");
        }

        this.name = name;        
        this.logger = config ? new Logger(name, config.debug) : new Logger(name);        
    }
}