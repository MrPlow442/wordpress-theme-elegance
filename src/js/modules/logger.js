/**
 * Logger - Centralized logging system for the theme
 * 
 * @package Elegance
 * @version 2.0.0
 */

export class LoggerFactory {
    static #initialized = false;    
    static #debug = false;
    
    static init(debug) {
        this.#debug = debug ?? false;        
        this.#initialized = true;
    }

    static createLogger(moduleName, mute = false) {                
        if (!this.#initialized) {
            console.error(`Tried to create a logger for ${moduleName} before factory was initialized`);
            return;
        }
        return new Logger({moduleName, isDebugEnabled: this.#debug, mute});
    }
}

export class Logger {
    constructor({ moduleName = '', isDebugEnabled = false, mute = false}) {
        this.moduleName = moduleName ? `[${moduleName}] ` : '';
        this.isDebugEnabled = isDebugEnabled;
        this.logPrefix = '[Elegance]';
        this.isMuted = mute;
    }

    mute() {
        this.isMuted = true;
    }
    
    unmute() {
        this.isMuted = false;
    }
    
    setDebug(debug) {
        this.isDebugEnabled = debug;
    }

    log(...data) {
        if (this.isMuted || !this.isDebugEnabled) {
            return;
        }
        console.log(this.logPrefix, this.moduleName, ...data);
    }

    warn(...data) {
        if (this.isMuted) {
            return;
        }
        console.warn(this.logPrefix, this.moduleName, ...data);
    }

    error(...data) {
        if (this.isMuted) {
            return;
        }
        console.error(this.logPrefix, this.moduleName, ...data);
    }

    info(...data) {
        if (this.isMuted || !this.isDebugEnabled) {
            return;
        }
        console.info(this.logPrefix, this.moduleName, ...data);
    }

    debug(...data) {
        if (this.isMuted || !this.isDebugEnabled) {
            return;
        }
        console.debug(this.logPrefix, this.moduleName, ...data);
    }
}