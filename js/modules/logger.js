/**
 * Logger - Centralized logging system for the theme
 * 
 * @package Elegance
 * @version 2.0.0
 */

class LoggerFactory {    
    static debug = false;
    
    static init(themeConfig = {}) {
        this.debug = themeConfig.debug ?? false;        
    }

    static createLogger(moduleName, silence = false) {        
        if (silence) {
            return new NoOpLogger();
        }
        return new Logger(moduleName, this.debug);
    }
}

class Logger {
    constructor(moduleName = '', isDebugEnabled = false) {
        this.moduleName = moduleName ? `[${moduleName}] ` : '';
        this.isDebugEnabled = isDebugEnabled;
        this.logPrefix = '[Elegance]';
    }

    log(...data) {
        if (this.isDebugEnabled) {
            console.log(this.logPrefix, this.moduleName, ...data);
        }
    }

    warn(...data) {
        console.warn(this.logPrefix, this.moduleName, ...data);
    }

    error(...data) {
        console.error(this.logPrefix, this.moduleName, ...data);
    }

    info(...data) {
        if (this.isDebugEnabled) {
            console.info(this.logPrefix, this.moduleName, ...data);
        }        
    }
}

class NoOpLogger {
    log(...data) {
        // No operation logger, does nothing
    }

    warn(...data) {
        // No operation logger, does nothing
    }

    error(...data) {
        // No operation logger, does nothing
    }

    info(...data) {
        // No operation logger, does nothing
    }
}