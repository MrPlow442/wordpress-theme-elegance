/**
 * Swiper Configuration Utilities
 * Handles data attribute parsing and default configurations
 * 
 * @package Elegance
 * @version 2.0.0
 */

import {
    Navigation,
    Pagination,
    Scrollbar,
    Keyboard,
    Mousewheel,
    Autoplay,
    EffectFade,
    EffectCreative,
    EffectCoverflow,
    EffectFlip,
    EffectCards,
    EffectCube,
    Controller,
    Thumbs,
    FreeMode,
    Grid,
    Manipulation,
    Zoom,
    Virtual,
    HashNavigation,
    History,
    A11y,
    Parallax
} from 'swiper/modules';

export class SwiperConfig {
    static #DATA_PREFIX = 'swiper';
    static #AVAILABLE_MODULES = [
        Navigation, Pagination, Scrollbar, Keyboard, Mousewheel, Autoplay,
        EffectFade, EffectCreative, EffectCoverflow, EffectFlip, EffectCards, EffectCube,
        Controller, Thumbs, FreeMode, Grid, Manipulation, Zoom, Virtual,
        HashNavigation, History, A11y, Parallax
    ];
    /**
     * Parse data attributes from element and convert to Swiper configuration
     */
    static parseDataAttributes(element) {
        const config = {};
        const dataset = element.dataset;

        config.modules = [...this.#AVAILABLE_MODULES];

        Object.keys(dataset).forEach(key => {
            if (!key.startsWith(this.#DATA_PREFIX)) {
                return;
            }

            const configKey = this.dataAttributeToConfigKey(key);
            const value = this.parseDataValue(dataset[key], configKey);
            this.setNestedConfigValue(config, configKey, value);
        });

        this.processSpecialConfigurations(config, element);

        return config;
    }

    static dataAttributeToConfigKey(dataKey) {
        // Convert data-swiper-slides-per-view to slidesPerView
        return dataKey
            .replace(new RegExp(`^${this.#DATA_PREFIX}`), '')
            .replace(/([A-Z])/g, (_, letter, index) => index === 0 ? letter.toLowerCase() : letter)
            .replace(/-([a-z])/g, (_, letter) => letter.toUpperCase());
    }

    static parseDataValue(value, configKey) {
        // Handle boolean values
        if (value === 'true') return true;
        if (value === 'false') return false;

        // Handle null/undefined
        if (value === 'null' || value === 'undefined') return null;

        // Handle numbers
        if (/^\d+$/.test(value)) return parseInt(value, 10);
        if (/^\d*\.\d+$/.test(value)) return parseFloat(value);

        // Handle JSON objects/arrays
        if ((value.startsWith('{') && value.endsWith('}')) ||
            (value.startsWith('[') && value.endsWith(']'))) {
            try {
                return JSON.parse(value);
            } catch (e) {
                console.error(`Failed to parse JSON value for ${configKey}:`, value);
                return value;
            }
        }

        // Handle special string values
        if (configKey === 'effect' || configKey === 'direction' || configKey === 'paginationType') {
            return value;
        }

        // Handle selector strings (keep as string if starts with . or #)
        if (value.startsWith('.') || value.startsWith('#') || value.startsWith('[')) {
            return value;
        }

        return value;
    }

    static setNestedConfigValue(config, path, value) {
        const keys = path.split('.');
        let current = config;

        for (let i = 0; i < keys.length - 1; i++) {
            const key = keys[i];
            if (!current[key] || typeof current[key] !== 'object') {
                current[key] = {};
            }
            current = current[key];
        }

        current[keys[keys.length - 1]] = value;
    }

    static processSpecialConfigurations(config, element) {
        // Handle navigation configuration
        if (config.navigation === true || config.navigationEnabled === true) {
            const nextEl = element.querySelector('.swiper-button-next') ||
                document.querySelector(`[data-swiper-target="${element.dataset.swiperId}"] .swiper-button-next`);
            const prevEl = element.querySelector('.swiper-button-prev') ||
                document.querySelector(`[data-swiper-target="${element.dataset.swiperId}"] .swiper-button-prev`);

            config.navigation = {
                nextEl: nextEl,
                prevEl: prevEl,
                ...config.navigation
            };
        }

        // Handle pagination configuration
        if (config.pagination === true || config.paginationEnabled === true) {
            const paginationEl = element.querySelector('.swiper-pagination') ||
                document.querySelector(`[data-swiper-target="${element.dataset.swiperId}"] .swiper-pagination`);

            config.pagination = {
                el: paginationEl,
                type: config.paginationType || 'bullets',
                clickable: true,
                ...config.pagination
            };
        }

        // Handle scrollbar configuration
        if (config.scrollbar === true || config.scrollbarEnabled === true) {
            const scrollbarEl = element.querySelector('.swiper-scrollbar') ||
                document.querySelector(`[data-swiper-target="${element.dataset.swiperId}"] .swiper-scrollbar`);

            config.scrollbar = {
                el: scrollbarEl,
                draggable: config.scrollbarDraggable || false,
                ...config.scrollbar
            };
        }

        // Handle keyboard configuration
        if (config.keyboard === true || config.keyboardEnabled === true) {
            config.keyboard = {
                enabled: true,
                onlyInViewport: config.keyboardOnlyInViewport !== false,
                pageUpDown: config.keyboardPageUpDown !== false,
                ...config.keyboard
            };
        }

        // Handle mousewheel configuration
        if (config.mousewheel === true || config.mousewheelEnabled === true) {
            config.mousewheel = {
                enabled: true,
                releaseOnEdges: config.mousewheelReleaseOnEdges !== false,
                sensitivity: config.mousewheelSensitivity || 1,
                ...config.mousewheel
            };
        }

        // Handle autoplay configuration
        if (config.autoplay === true || config.autoplayEnabled === true) {
            config.autoplay = {
                delay: config.autoplayDelay || 3000,
                disableOnInteraction: config.autoplayDisableOnInteraction !== false,
                pauseOnMouseEnter: config.autoplayPauseOnMouseEnter === true,
                reverseDirection: config.autoplayReverseDirection === true,
                ...config.autoplay
            };
        }

        // Handle breakpoints
        if (config.breakpoints && typeof config.breakpoints === 'string') {
            try {
                config.breakpoints = JSON.parse(config.breakpoints);
            } catch (e) {
                console.warn('Failed to parse breakpoints configuration:', config.breakpoints);
                delete config.breakpoints;
            }
        }

        // Add event handlers
        config.on = {
            slideChange: this.handleSlideChange,
            transitionStart: this.handleTransitionStart,
            transitionEnd: this.handleTransitionEnd,
            ...config.on
        };
    }
}
