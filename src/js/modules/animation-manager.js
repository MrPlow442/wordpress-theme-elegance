/**
 * Animation Manager Module - Handles scroll-based animations and effects 
 * 
 * @package Elegance
 * @version 2.0.0
 */

import { EleganceModule } from './module.js';
import { EVENTS, MODULES } from './constants.js';
import { EleganceTheme } from './theme-core.js';

export class AnimationManager extends EleganceModule {
    constructor(themeConfig = {}, silence = false) {
        super(MODULES.ANIMATION_MANAGER, themeConfig, silence);
        this.animatedElements = new Map();
        this.paused = false;

        this.config = {
            rootMargin: '0px 0px -50px 0px',
            threshold: 0.1,
            animationDelay: 50,
            resetOnDesktop: true,
            mobileBreakpoint: 767,
            ...this.themeConfig.animation
        };

        this.handleSlideInView = this.handleSlideInView.bind(this);
        this.handleSlideLeave = this.handleSlideLeave.bind(this);
    }

    init() {
        this.#findAnimatedElements();
        this.logger.log(`Initialized with ${this.animatedElements.size} animated elements`);
    }

    postInit() {
        this.#bindEvents();
    }

    handleSlideInView({ detail }) {
        this.logger.log('Slide In View event received: ', detail);
        const { slide } = detail.slideData;

        this.#findAnimationElementsIn(slide)
            .filter(e => !e.isAnimated)
            .forEach(e => {
                this.animateElement(e);
            });
    }

    handleSlideLeave({ detail }) {
        this.logger.log('Slide Leave event received: ', detail);
        if (!this.#shouldResetAnimation()) {
            return;
        }

        const { slide } = detail.fromSlideData;

        this.#findAnimationElementsIn(slide)
            .filter(e => e.isAnimated)
            .forEach(e => {
                this.resetElement(e);
            });
    }

    animateElementBySelector(selector, animation) {
        const element = document.querySelector(selector);

        if (element) {
            element.classList.add('animated', animation);
            element.classList.remove('animate');

            this.logger.log(`AnimationManager: Manually animated element '${selector}' with '${animation}'`);
        }
    }

    addAnimatedElement(element, animation, delay = 0) {
        if (!element || !animation) {
            this.logger.warn('AnimationManager: Invalid element or animation provided');
            return;
        }

        const elementData = {
            element: element,
            animation: animation,
            index: delay,
            isAnimated: false
        };

        element.dataset.animate = animation;
        element.classList.add('animate');

        this.animatedElements.set(element, elementData);

        this.logger.log(`AnimationManager: Added new animated element with '${animation}'`);
    }

    removeAnimatedElement(element) {
        if (this.animatedElements.has(element)) {
            this.animatedElements.delete(element);
            this.logger.log('AnimationManager: Removed animated element');
        }
    }

    animateElement(elementData) {
        const { element, animation, index } = elementData;

        setTimeout(() => {
            element.classList.add('animated', animation);
            element.classList.remove('animate');
            elementData.isAnimated = true;

            this.logger.log(`AnimationManager: Animated element `, element, ` with '${animation}'`);
        }, index * this.config.animationDelay);
    }

    resetElement(elementData) {
        const { element, animation } = elementData;

        element.classList.remove('animated', animation);
        element.classList.add('animate');
        elementData.isAnimated = false;

        this.logger.log(`AnimationManager: Reset element `, element, ` animation '${animation}'`);
    }

    pauseAnimations() {
        this.#unbindEvents();
        this.paused = true;
        this.logger.log('AnimationManager: Paused all animations');
    }

    resumeAnimations() {
        if (!this.paused) {
            return;
        }
        this.#bindEvents();
        this.logger.log('AnimationManager: Resumed animations');
    }

    resetAllAnimations() {
        this.animatedElements.forEach((elementData) => {
            this.resetElement(elementData);
        });

        this.logger.log('AnimationManager: Reset all animations');
    }

    getStats() {
        const total = this.animatedElements.size;
        const animated = Array.from(this.animatedElements.values()).filter(data => data.isAnimated).length;
        const pending = total - animated;

        return {
            total,
            animated,
            pending
        };
    }

    #bindEvents() {
        EleganceTheme.bindEvent(EVENTS.SCROLL_NAVIGATOR.SLIDE_IN_VIEW, this.handleSlideInView);
        EleganceTheme.bindEvent(EVENTS.SCROLL_NAVIGATOR.SLIDE_LEAVE, this.handleSlideLeave);
    }

    #unbindEvents() {
        EleganceTheme.unbindEvent(EVENTS.SCROLL_NAVIGATOR.SLIDE_IN_VIEW, this.handleSlideInView);
        EleganceTheme.unbindEvent(EVENTS.SCROLL_NAVIGATOR.SLIDE_LEAVE, this.handleSlideLeave);
    }

    #findAnimatedElements() {
        const animatedRows = document.querySelectorAll('.animated-row');

        animatedRows.forEach(row => {
            const animateElements = row.querySelectorAll('.animate');

            if (animateElements.length > 0) {
                const rowData = {
                    row: row,
                    elements: Array.from(animateElements),
                    isAnimated: false
                };

                animateElements.forEach((element, index) => {
                    const animation = element.dataset.animate;

                    if (animation) {
                        const elementData = {
                            ...rowData,
                            element: element,
                            animation: animation,
                            index: index
                        };

                        this.animatedElements.set(element, elementData);
                    }
                });
            }
        });
    }

    #findAnimationElementsIn(element) {
        return Array.from(element.querySelectorAll('[data-animate]'))
            .map(e => this.animatedElements.get(e))
            .filter(e => e !== null);
    }

    #shouldResetAnimation() {
        return this.config.resetOnDesktop && !this.#isSmallScreen();
    }

    #isSmallScreen() {
        return window.innerWidth <= this.config.mobileBreakpoint;
    }

    destroy() {
        this.animatedElements.clear();

        this.logger.log('AnimationManager: Destroyed');
    }
}


window.AnimationManager = AnimationManager;

