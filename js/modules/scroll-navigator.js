/**
 * Scroll Navigator Module - Unified scroll navigation for vertical and horizontal containers
 * Handles multiple scroll containers with individual configurations and callbacks
 * 
 * @package Elegance
 * @version 2.0.0
 */

class ScrollNavigator extends EleganceModule {
    static DIRECTION = {
        HORIZONTAL: 'horizontal',
        VERTICAL: 'vertical'
    };

    constructor(themeConfig = {}, silence = false) {
        super('ScrollNavigator', themeConfig, silence);

        this.containers = new Map();
        this.activeContainer = null;

        this.config = {
            containerSelector: {
                [ScrollNavigator.DIRECTION.VERTICAL]: '.scroll-container',
                [ScrollNavigator.DIRECTION.HORIZONTAL]: '.horizontal-scroll-container'
            },
            slideSelector: {
                [ScrollNavigator.DIRECTION.VERTICAL]: '.vertical-slide',
                [ScrollNavigator.DIRECTION.HORIZONTAL]: '.horizontal-slide'
            },
            ...this.themeConfig.scrollNavigator
        };        
    }

    init() {
        this.registerAllContainers();
        this.bindNavigatorElements();
    }

    registerAllContainers() {
        this.#registerContainersByDirection(ScrollNavigator.DIRECTION.VERTICAL);
        this.#registerContainersByDirection(ScrollNavigator.DIRECTION.HORIZONTAL);
    }

    #registerContainersByDirection(direction) {        
        document
            .querySelectorAll(`${this.config.containerSelector[direction]}[data-scroll-container-id]`)
            .forEach(element => this.registerContainer(element, direction));
    }

    registerContainer(element, direction) {
        const id = element.dataset.scrollContainerId;
        if (!id) {
            this.logger.warn(`Attempted registration of a container with an empty id`, element);
            return;
        }

        if (this.containers.has(id)) {
            this.logger.warn(`Attempted registration of a container with an existing id ${id}`)
            return;
        }    

        const selector = this.config.slideSelector[direction];                
        const slides = Array.from(element.querySelectorAll(selector));

        if (slides.length === 0) {
            this.logger.warn(`No sections found in ${direction} container:`, element);
            return;
        }

        this.logger.log('Merged config: ', config);
        const scrollContainer = new ScrollContainer({
            element: element,
            slides: slides,
            direction: direction,            
            onActiveChange: this.setActiveContainer.bind(this),            
        });
        scrollContainer.init();

        this.containers.set(id, scrollContainer);

        this.logger.log(`Registered ${direction} container with ${slides.length} sections`, scrollContainer);
    }

    bindNavigatorElements() {
        document.querySelectorAll('[data-scroll-to]').forEach(element => element.addEventListener('click', (event) => {        
            event.preventDefault();

            const element = event.target;
            const slideId = element.dataset.scrollTo;
            const containerId = element.dataset.scrollToContainer;
            this.navigateToSlideById(slideId, containerId);
        }));

        window.addEventListener('hashchange', () => {
            const hash = window.location.hash.substring(1);
            if (hash) {
                this.navigateToSlideById(hash);
            }
        });
    }    

    navigateToSlideByIndex(slideIndex, containerId) {
        if (!containerId) {
            this.logger.warn(`Cannot scroll to index: ${slideIndex} due to missing containerId`);
            return;
        }

        const targetContainer = this.containers.get(containerId);

        if (!targetContainer) {
            this.logger.warn(`Cannot scroll to index: ${slideIndex} as given containerId: ${containerId} is not registered`);
            return;
        }

        targetContainer.navigateToSlideByIndex(slideIndex);
    }

    navigateToSlideById(slideId, containerId = null) {
        let targetContainer = null;
        if (containerId && this.containers.has(containerId)) {
            targetContainer = this.containers.get(containerId);
        } else {
            this.logger.log(`Container id wrong or not present: ${containerId} so we're searching for ${slideId} in all containers`);
            targetContainer = this.containers.values().find(container => container.hasSlideWithId(slideId));            
        }

        if (!targetContainer) {
            this.logger.warn(`No slide found for slideId: ${slideId} and containerId: ${containerId}`);
            return;
        }

        targetContainer.navigateToSlideById(slideId);
    }

    setActiveContainer(container) {
        if (this.activeContainer === container) {
            return;
        }

        const previousContainer = this.activeContainer;
        this.activeContainer = container;

        this.logger.log('Active container changed from: ', previousContainer, ' to: ', container);
        EleganceTheme.triggerEvent(EVENTS.SCROLL_NAVIGATOR.ACTIVE_CONTAINER_CHANGE, {
            previousContainer,
            activeContainer: container
        });
    }
}

class ScrollContainer {
    constructor({ element, direction, slides, onActiveChange }) {      
        const defaultContainerConfig = {
            scrollDetectorConfig: {
                intersectionThreshold: 0.5,
                intersectionRootMargin: '-10px',
                scrollDetectionDelay: 100
            },                
            scrollBehavior: 'smooth',                
            scrollDetectionDelay: 100,
            scrollAnimationDuration: 600
        };        

        this.config = {
            ...defaultContainerConfig,
            ...element.dataset
        }

        this.id = element.dataset.scrollContainerId;
        this.logger = LoggerFactory.createLogger("[ScrollNavigator]" + `[Container:${this.id}]`);        
        this.element = element;
        this.slides = slides;        
        this.direction = direction;                

        // this.logger.log('Container default config: ', defaultContainerConfig, ' dataset: ', element.dataset);
        this.logger.log('Config', this.config);
        
        this.currentSlideIndex = 0;
        this.currentSlide = slides[0] ?? null;        
        this.autoScrollTimer = null;

        this.programScrolling = false;
        this.programScrollTimeout = null;
        this.scrollDetector = null;

        this.onActiveChange = onActiveChange;                   
    }    

    init() {
        this.setupScrollDetection();
        this.logger.log('ScrollContainer initialized')
    }

    setupScrollDetection() {
        this.scrollDetector = new ScrollDetector(this.logger, {
            container: this,
            config: this.config.scrollDetectorConfig,
            onSlideChange: this.handleUserSlideChange.bind(this)            
        });
    }

    getIndexOfSlideById(slideId) {
        return this.slides.findIndex(slide => slide.dataset.scrollSlideId === slideId);
    }

    hasSlideWithId(slideId) {
        return this.getIndexOfSlideById(slideId) !== -1;
    }

    navigateToSlideById(slideId) {        
        if(!this.hasSlideWithId(slideId)) {
            this.logger.warn(`Given slide id ${slideId} not found on this container`);
            return;
        }

        this.navigateToSlideByIndex(this.getIndexOfSlideById(slideId));
    }

    navigateToSlideByIndex(targetIndex) {
        if (targetIndex < 0 || targetIndex >= this.slides.length) {
            this.logger.warn(`Given slide index ${targetIndex} is not a valid index for this container [0-${this.slides.length - 1}]`);
            return;
        }

        if (targetIndex === this.currentSlideIndex) {
            this.logger.log(`Container is already on slide ${targetIndex}`);
            return;
        }

        const fromSlide = this.currentSlide;
        const fromIndex = this.currentSlideIndex;
        const targetSlide = this.slides[targetIndex];

        this.isProgramScrolling = true;

        targetSlide.scrollIntoView({
            behavior: this.config.scrollBehavior,
            block: 'start',
            inline: 'start'
        });
        
        this.#handleProgramScrollingTimeout();

        this.#setCurrentSlideAndTriggerChangeEvent(fromSlide, fromIndex, targetSlide, targetIndex, SCROLLED_BY.PROGRAM);        
    }

    #handleProgramScrollingTimeout() {
        clearTimeout(this.programScrollTimeout);
        this.logger.log(`Creating program scroll timeout which will last ${this.config.scrollAnimationDuration + 50}`);
        this.programScrollTimeout = setTimeout(() => {
            this.isProgramScrolling = false;
        }, this.config.scrollAnimationDuration + 50);
    }

    #setCurrentSlideAndTriggerChangeEvent(fromSlide, fromIndex, toSlide, toIndex, scrolledBy) {
        this.currentSlideIndex = toIndex;
        this.currentSlide = toSlide;     
        this.#triggerSlideChangeEvent(fromSlide, fromIndex, toSlide, toIndex, scrolledBy);
    }

    #triggerSlideChangeEvent(fromSlide, fromIndex, toSlide, toIndex, scrolledBy) {
        this.logger.log(`${EVENTS.SCROLL_NAVIGATOR.SLIDE_CHANGE} called here with these params`, 
            {
                container: this,
                fromSlide: fromSlide,
                fromIndex: fromIndex,
                toSlide: toSlide,
                toIndex: toIndex,
                scrolledBy: scrolledBy
            }
        );

        EleganceTheme.triggerEvent(EVENTS.SCROLL_NAVIGATOR.SLIDE_CHANGE, {
            container: this,
            fromSlide: fromSlide,
            fromIndex: fromIndex,
            toSlide: toSlide,
            toIndex: toIndex,
            scrolledBy: scrolledBy
        });               
    }
    
    handleUserSlideChange(targetIndex) {
        if (this.isProgramScrolling) {
            this.logger.log('Ignoring user slide change during program navigation');
            return;
        }

        if (targetIndex === this.currentSlideIndex) {
            return;
        }

        const fromSlide = this.currentSlide;
        const fromIndex = this.currentSlideIndex;
        const targetSlide = this.slides[targetIndex];

        this.logger.log(`User scroll detected ${fromIndex} -> ${targetIndex}`);

        this.#setCurrentSlideAndTriggerChangeEvent(fromSlide, fromIndex, targetSlide, targetIndex, SCROLLED_BY.USER);        
    }
}

class ScrollDetector {
    constructor(logger, { container, config = {}, onSlideChange }) {
        const defaultConfig = {
            intersectionThreshold: 0.5,
            intersectionRootMargin: '-10px',
            scrollDetectionDelay: 100
        }

        this.config = {
            ...defaultConfig,
            ...config
        }

        this.logger = logger;
        this.container = container;        
        this.onSlideChange = onSlideChange;        
        
        this.intersectionObserver = null;
        this.scrollTimeout = null;
        this.intersectionTimeout = null;
        this.lastDetectedIndex = container.currentSlideIndex;

        if ('IntersectionObserver' in window) {
            this.setupIntersectionObserver();
        } else {
            this.logger.warn('IntersectionObserver not supported, using scroll fallback');
            this.setupScrollFallback();
        }
    }    

    setupIntersectionObserver() {
        this.intersectionObserver = new IntersectionObserver(
            (entries) => this.handleIntersectionEntries(entries),
            {
                root: this.container.element,
                threshold: this.config.intersectionThreshold,
                rootMargin: this.config.intersectionRootMargin
            }
        );
        
        this.container.slides.forEach(slide => {
            this.intersectionObserver.observe(slide);
        });

        this.logger.log('IntersectionObserver setup complete');
    }

    handleIntersectionEntries(entries) {        
        if (this.container.isProgramScrolling) {
            return;
        }

        let maxIntersectionRatio = 0;
        let mostVisibleSlide = null;
        let mostVisibleIndex = -1;

        entries.forEach(entry => {
            if (entry.isIntersecting && entry.intersectionRatio > maxIntersectionRatio) {
                maxIntersectionRatio = entry.intersectionRatio;
                mostVisibleSlide = entry.target;
                mostVisibleIndex = this.container.slides.indexOf(entry.target);
            }
        });

        if (mostVisibleSlide && mostVisibleIndex !== -1) {            
            clearTimeout(this.intersectionTimeout);
            this.intersectionTimeout = setTimeout(() => {                
                if (!this.container.isProgramScrolling && 
                    mostVisibleIndex !== this.lastDetectedIndex) {
                    
                    this.lastDetectedIndex = mostVisibleIndex;
                    this.onSlideChange(mostVisibleIndex);
                }
            }, this.config.scrollDetectionDelay);
        }
    }

    setupScrollFallback() {
        this.container.element.addEventListener('scroll', () => {            
            if (this.container.isProgramScrolling) {
                return;
            }

            clearTimeout(this.scrollTimeout);
            this.scrollTimeout = setTimeout(() => {
                this.detectSlideFromScrollPosition();
            }, this.config.scrollDetectionDelay);
        });
    }

    detectSlideFromScrollPosition() {
        let targetIndex = 0;
        const element = this.container.element;
        const slides = this.container.slides;

        switch(this.container.direction) {
            case ScrollNavigator.DIRECTION.VERTICAL:
                const scrollTop = element.scrollTop;
                const containerHeight = element.clientHeight;
                const viewportCenter = scrollTop + (containerHeight / 2);

                for (let i = 0; i < slides.length; i++) {
                    const slide = slides[i];
                    const slideTop = slide.offsetTop;
                    const slideBottom = slideTop + slide.offsetHeight;

                    if (viewportCenter >= slideTop && viewportCenter < slideBottom) {
                        targetIndex = i;
                        break;
                    }
                }
                break;

            case ScrollNavigator.DIRECTION.HORIZONTAL:
                const scrollLeft = element.scrollLeft;
                const slideWidth = element.clientWidth;
                targetIndex = Math.round(scrollLeft / slideWidth);
                break;

            default:
                this.logger.error('Unknown direction:', this.container.direction);
                return;
        }

        if (targetIndex !== this.lastDetectedIndex) {
            this.lastDetectedIndex = targetIndex;
            this.onSlideChange(targetIndex);
        }
    }

    destroy() {
        if (this.intersectionObserver) {
            this.intersectionObserver.disconnect();
            this.intersectionObserver = null;
        }

        clearTimeout(this.scrollTimeout);
        clearTimeout(this.intersectionTimeout);
    }
}