/**
 * Horizontal Scroll Manager Module - Handles horizontal scrolling sections
 * Provides smooth horizontal navigation with touch and mouse support
 * 
 * @package Elegance
 * @version 2.0.0
 */

class HorizontalScrollManager extends EleganceModule {
    constructor(themeConfig = {}, silence = false) {
        super('HorizontalScrollManager', themeConfig, silence);
        this.scrollContainers = new Map();
        
        this.config = {
            autoScroll: false,
            autoScrollDelay: 2000,
            showNavButtons: true,
            showDots: true,
            scrollBehavior: 'smooth',
            navButtonUpdateDelay: 350,
            scrollTolerance: 5,
            ...this.themeConfig.horizontalScroll
        };

        this.handleContainerScroll = this.handleContainerScroll.bind(this);
        this.handleTouchStart = this.handleTouchStart.bind(this);
        this.handleTouchMove = this.handleTouchMove.bind(this);
        this.handleTouchEnd = this.handleTouchEnd.bind(this);
    }

    init() {                
        this.autoInitializeContainers();
        
        this.logger.log(`HorizontalScrollManager: Initialized with ${this.scrollContainers.size} containers`);
    }

    autoInitializeContainers() {
        const containers = document.querySelectorAll('.horizontal-scroll-container');
        
        containers.forEach(container => {
            const section = container.closest('.vertical-slide, section');
            const sectionId = section ? (section.id || section.dataset.section) : container.id;
            
            if (sectionId) {
                this.initializeContainer(sectionId, {});
            }
        });
    }

    initializeContainer(sectionSelector, config = {}) {
        const containerConfig = { ...this.config, ...config };
        
        const section = sectionSelector.startsWith('#') ? 
            document.querySelector(sectionSelector) : 
            document.getElementById(sectionSelector) || document.querySelector(`#${sectionSelector}`);
            
        if (!section) {
            this.logger.warn(`HorizontalScrollManager: Section '${sectionSelector}' not found`);
            return;
        }

        const container = section.querySelector('.horizontal-scroll-container');
        const navContainer = section.querySelector('.horizontal-scroll-nav-top');
        
        if (!container) {
            this.logger.warn(`HorizontalScrollManager: Container not found in section '${sectionSelector}'`);
            return;
        }

        const containerData = {
            section: section,
            container: container,
            navContainer: navContainer,
            config: containerConfig,
            currentSlide: 0,
            slides: Array.from(container.querySelectorAll('.horizontal-slide')),
            isScrolling: false,
            touchStartX: 0,
            touchCurrentX: 0,
            autoScrollTimer: null,
            previousButton: navContainer?.querySelector('.scroll-prev'),
            nextButton: navContainer?.querySelector('.scroll-next'),
            dots: Array.from(navContainer?.querySelectorAll('.scroll-dot') || [])
        };

        this.scrollContainers.set(sectionSelector, containerData);

        this.setupContainerEvents(containerData);
        this.updateNavigation(containerData);
        
        if (containerConfig.autoScroll) {
            this.startAutoScroll(containerData);
        }

        this.logger.log(`HorizontalScrollManager: Initialized container '${sectionSelector}' with ${containerData.slides.length} slides`);
    }

    setupContainerEvents(containerData) {
        const { container, previousButton, nextButton, dots } = containerData;

        // Navigation button events
        if (previousButton) {
            previousButton.addEventListener('click', () => {
                this.goToPreviousSlide(containerData);
            });
        }

        if (nextButton) {
            nextButton.addEventListener('click', () => {
                this.goToNextSlide(containerData);
            });
        }

        // Dot navigation events
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                this.goToSlide(containerData, index);
            });
        });

        // Container scroll events
        container.addEventListener('scroll', () => {
            this.handleContainerScroll(containerData);
        });

        // Touch events for mobile swiping
        container.addEventListener('touchstart', this.handleTouchStart);
        container.addEventListener('touchmove', this.handleTouchMove);
        container.addEventListener('touchend', this.handleTouchEnd);

        // Mouse wheel horizontal scrolling
        container.addEventListener('wheel', (event) => {
            this.handleWheelScroll(containerData, event);
        });
    }

    handleContainerScroll(containerData) {
        if (containerData.isScrolling) return;

        clearTimeout(containerData.scrollUpdateTimeout);
        
        containerData.scrollUpdateTimeout = setTimeout(() => {
            this.updateCurrentSlideFromScroll(containerData);
            this.updateNavigation(containerData);
        }, this.config.navButtonUpdateDelay);
    }

    handleTouchStart(event) {
        const containerData = this.getContainerFromElement(event.currentTarget);
        if (!containerData) return;

        containerData.touchStartX = event.touches[0].clientX;
        containerData.touchCurrentX = containerData.touchStartX;
    }

    handleTouchMove(event) {
        const containerData = this.getContainerFromElement(event.currentTarget);
        if (!containerData) return;

        containerData.touchCurrentX = event.touches[0].clientX;
        
        // Prevent default scrolling if horizontal movement is detected
        const deltaX = Math.abs(containerData.touchCurrentX - containerData.touchStartX);
        if (deltaX > 10) {
            event.preventDefault();
        }
    }

    handleTouchEnd(event) {
        const containerData = this.getContainerFromElement(event.currentTarget);
        if (!containerData) return;

        const deltaX = containerData.touchCurrentX - containerData.touchStartX;
        const minSwipeDistance = 50;

        if (Math.abs(deltaX) > minSwipeDistance) {
            if (deltaX > 0) {
                this.goToPreviousSlide(containerData);
            } else {
                this.goToNextSlide(containerData);
            }
        }
    }

    handleWheelScroll(containerData, event) {
        if (Math.abs(event.deltaY) > Math.abs(event.deltaX)) {
            event.preventDefault();
            
            containerData.container.scrollBy({
                left: event.deltaY,
                behavior: 'auto'
            });
        }
    }

    getContainerFromElement(element) {
        for (const [key, containerData] of this.scrollContainers) {
            if (containerData.container === element) {
                return containerData;
            }
        }
        return null;
    }

    updateCurrentSlideFromScroll(containerData) {
        const { container } = containerData;
        const slideWidth = container.offsetWidth;
        const scrollLeft = container.scrollLeft;
        
        const newSlideIndex = Math.round(scrollLeft / slideWidth);
        
        if (newSlideIndex !== containerData.currentSlide) {
            containerData.currentSlide = Math.max(0, Math.min(newSlideIndex, containerData.slides.length - 1));
        }
    }

    goToSlide(containerData, slideIndex) {
        if (slideIndex < 0 || slideIndex >= containerData.slides.length) {
            return;
        }

        const { container } = containerData;
        const slideWidth = container.offsetWidth;
        const targetScrollLeft = slideIndex * slideWidth;

        containerData.isScrolling = true;
        containerData.currentSlide = slideIndex;

        container.scrollTo({
            left: targetScrollLeft,
            behavior: this.config.scrollBehavior
        });

        setTimeout(() => {
            containerData.isScrolling = false;
            this.updateNavigation(containerData);
        }, this.config.navButtonUpdateDelay);

        if (containerData.config.autoScroll) {
            this.restartAutoScroll(containerData);
        }
    }

    goToNextSlide(containerData) {
        const nextIndex = containerData.currentSlide + 1;
        
        if (nextIndex < containerData.slides.length) {
            this.goToSlide(containerData, nextIndex);
        }
    }

    goToPreviousSlide(containerData) {
        const prevIndex = containerData.currentSlide - 1;
        
        if (prevIndex >= 0) {
            this.goToSlide(containerData, prevIndex);
        }
    }

    updateNavigation(containerData) {
        const { currentSlide, slides, previousButton, nextButton, dots } = containerData;
        
        if (previousButton) {
            previousButton.disabled = currentSlide === 0;
            previousButton.style.opacity = currentSlide === 0 ? '0.5' : '1';
        }
        
        if (nextButton) {
            const isLastSlide = currentSlide === slides.length - 1;
            nextButton.disabled = isLastSlide;
            nextButton.style.opacity = isLastSlide ? '0.5' : '1';
        }
        
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentSlide);
        });
    }

    startAutoScroll(containerData) {
        if (containerData.autoScrollTimer) {
            clearInterval(containerData.autoScrollTimer);
        }

        containerData.autoScrollTimer = setInterval(() => {
            const nextIndex = (containerData.currentSlide + 1) % containerData.slides.length;
            this.goToSlide(containerData, nextIndex);
        }, containerData.config.autoScrollDelay);
    }

    stopAutoScroll(containerData) {
        if (containerData.autoScrollTimer) {
            clearInterval(containerData.autoScrollTimer);
            containerData.autoScrollTimer = null;
        }
    }

    restartAutoScroll(containerData) {
        this.stopAutoScroll(containerData);
        
        if (containerData.config.autoScroll) {
            this.startAutoScroll(containerData);
        }
    }

    removeContainer(sectionSelector) {
        const containerData = this.scrollContainers.get(sectionSelector);
        
        if (containerData) {
            this.stopAutoScroll(containerData);
            this.scrollContainers.delete(sectionSelector);
            
            this.logger.log(`HorizontalScrollManager: Removed container '${sectionSelector}'`);
        }
    }

    getContainerStats(sectionSelector) {
        const containerData = this.scrollContainers.get(sectionSelector);
        
        if (containerData) {
            return {
                currentSlide: containerData.currentSlide,
                totalSlides: containerData.slides.length,
                autoScrollEnabled: containerData.config.autoScroll,
                isScrolling: containerData.isScrolling
            };
        }
        
        return null;
    }

    destroy() {
        this.scrollContainers.forEach(containerData => {
            this.stopAutoScroll(containerData);
        });
        
        this.scrollContainers.clear();
        
        this.logger.log('HorizontalScrollManager: Destroyed');
    }
}

window.HorizontalScrollManager = HorizontalScrollManager;

