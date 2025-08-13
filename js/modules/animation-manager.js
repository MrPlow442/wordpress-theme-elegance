/**
 * Animation Manager Module - Handles scroll-based animations and effects
 * Manages animated elements with intersection observer for performance
 * 
 * @package Elegance
 * @version 2.0.0
 */

class AnimationManager extends EleganceModule {
    constructor(themeConfig = {}, silence = false) {
        super('AnimationManager', themeConfig, silence);
        this.observer = null;
        this.animatedElements = new Map();
        
        this.config = {
            rootMargin: '0px 0px -50px 0px',
            threshold: 0.1,
            animationDelay: 50,
            resetOnDesktop: true,
            mobileBreakpoint: 767,
            ...this.themeConfig.animation
        };        

        this.handleIntersection = this.handleIntersection.bind(this);
    }

    init() {
        this.setupIntersectionObserver();
        this.findAnimatedElements();
        
        this.logger.log(`AnimationManager: Initialized with ${this.animatedElements.size} animated elements`);
    }

    setupIntersectionObserver() {
        if ('IntersectionObserver' in window) {
            this.observer = new IntersectionObserver(
                this.handleIntersection,
                {
                    rootMargin: this.config.rootMargin,
                    threshold: this.config.threshold
                }
            );
        } else {
            this.logger.warn('AnimationManager: IntersectionObserver not supported, falling back to immediate animation');
            this.fallbackToImmediateAnimation();
        }
    }

    findAnimatedElements() {
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
                        
                        if (this.observer) {
                            this.observer.observe(element);
                        }
                    }
                });
            }
        });
    }

    handleIntersection(entries) {
        entries.forEach(entry => {
            const element = entry.target;
            const elementData = this.animatedElements.get(element);
            
            if (!elementData) return;

            if (entry.isIntersecting && !elementData.isAnimated) {
                this.animateElement(elementData);
            } else if (!entry.isIntersecting && this.shouldResetAnimation()) {
                this.resetElement(elementData);
            }
        });
    }

    animateElement(elementData) {
        const { element, animation, index } = elementData;
        
        setTimeout(() => {
            element.classList.add('animated', animation);
            element.classList.remove('animate');
            elementData.isAnimated = true;
            
            this.logger.log(`AnimationManager: Animated element with '${animation}'`);
        }, index * this.config.animationDelay);
    }

    resetElement(elementData) {
        const { element, animation } = elementData;
        
        element.classList.remove('animated', animation);
        element.classList.add('animate');
        elementData.isAnimated = false;
        
        this.logger.log(`AnimationManager: Reset element animation '${animation}'`);
    }

    shouldResetAnimation() {
        return this.config.resetOnDesktop && !this.isSmallScreen();
    }

    isSmallScreen() {
        return window.innerWidth <= this.config.mobileBreakpoint;
    }

    fallbackToImmediateAnimation() {
        setTimeout(() => {
            this.animatedElements.forEach((elementData) => {
                this.animateElement(elementData);
            });
        }, 100);
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
        
        if (this.observer) {
            this.observer.observe(element);
        }
        
        this.logger.log(`AnimationManager: Added new animated element with '${animation}'`);
    }

    removeAnimatedElement(element) {
        if (this.animatedElements.has(element)) {
            if (this.observer) {
                this.observer.unobserve(element);
            }
            
            this.animatedElements.delete(element);
            this.logger.log('AnimationManager: Removed animated element');
        }
    }

    pauseAnimations() {
        if (this.observer) {
            this.observer.disconnect();
        }
        
        this.logger.log('AnimationManager: Paused all animations');
    }

    resumeAnimations() {
        if (this.observer) {
            this.animatedElements.forEach((elementData) => {
                if (!elementData.isAnimated) {
                    this.observer.observe(elementData.element);
                }
            });
        }
        
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
            pending,
            observerSupported: !!this.observer
        };
    }

    onWindowResize(data) {
        if (this.isSmallScreen()) {
            this.animatedElements.forEach((elementData) => {
                if (elementData.isAnimated) {
                    const { element, animation } = elementData;
                    element.classList.add('animated', animation);
                    element.classList.remove('animate');
                }
            });
        }
    }

    destroy() {
        if (this.observer) {
            this.observer.disconnect();
            this.observer = null;
        }
        
        this.animatedElements.clear();
        
        this.logger.log('AnimationManager: Destroyed');
    }
}


window.AnimationManager = AnimationManager;

