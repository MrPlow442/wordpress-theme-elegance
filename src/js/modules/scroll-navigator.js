/**
 * Scroll Navigator Module - Unified scroll navigation for vertical and horizontal containers
 * Handles multiple scroll containers with individual configurations and callbacks
 * 
 * @package Elegance
 * @version 2.0.0
 */
import { EleganceModule } from './module.js';
import { LoggerFactory } from './logger.js';
import { MODULES, SCROLL_NAVIGATOR, EVENTS } from './constants.js';

export class ScrollNavigator extends EleganceModule {
    constructor(themeConfig = {}, silence = false) {
        super(MODULES.SCROLL_NAVIGATOR, themeConfig, silence);

        this.containers = new Map();        

        this.config = {
            containerSelector: {
                [SCROLL_NAVIGATOR.DIRECTION.VERTICAL]: '.scroll-container',
                [SCROLL_NAVIGATOR.DIRECTION.HORIZONTAL]: '.horizontal-scroll-container'
            },
            slideSelector: {
                [SCROLL_NAVIGATOR.DIRECTION.VERTICAL]: '.vertical-slide',
                [SCROLL_NAVIGATOR.DIRECTION.HORIZONTAL]: '.horizontal-slide'
            },            
            ...this.themeConfig.scrollNavigator
        };        
    }

    init() {
        this.registerContainersOnPage();        
    }

    postInit() {
        this.containers.values().forEach(container => container.postInit());
    }

    registerContainersOnPage() {
        this.#registerContainersByDirection(SCROLL_NAVIGATOR.DIRECTION.VERTICAL);
        this.#registerContainersByDirection(SCROLL_NAVIGATOR.DIRECTION.HORIZONTAL);
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
        const scrollContainer = new ScrollContainer(this.mute, {
            element: element,
            slides: slides,
            direction: direction                   
        });
        scrollContainer.init();

        this.containers.set(id, scrollContainer);

        this.logger.log(`Registered ${direction} container with ${slides.length} sections`, scrollContainer);
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

    getContainerCurrentSlideState(containerId) {
        return this.containers.get(containerId)?.getCurrentState();
    }
}

class ScrollContainer {
    constructor(mute, { element, direction, slides }) {      
        const defaultConfig = {
            scrollDetectorConfig: {
                intersectionThreshold: 0.5,
                intersectionRootMargin: '-10px',
                scrollDetectionDelay: 100
            },                
            scrollBehavior: 'smooth',                
            scrollDetectionDelay: 100,
            scrollAnimationDuration: 600,
            showNavButtons: false,
            showDots: false,            
            autoScroll: false,
            autoScrollDelay: 3000
        };        

        this.id = element.dataset.scrollContainerId;
        this.logger = LoggerFactory.createLogger("[ScrollNavigator]" + `[Container:${this.id}]`, mute);        
        this.element = element;
        this.slides = slides;        
        this.direction = direction;                
        
        this.config = this.#mergeConfig(direction, defaultConfig, element.dataset);        
        this.logger.log('Config', this.config);
        
        this.isVisible = false;
        this.currentSlideIndex = 0;
        this.autoScrollTimer = null;

        this.programScrolling = false;
        this.programScrollTimeout = null;
        this.scrollDetector = null;

        this.ui = null;        
    }    

    init() {
        this.#setupScrollDetection();
        this.#setupScrollUI();        
        this.#startAutoScroll();
        this.logger.log('ScrollContainer initialized')
    }

    postInit() {
        this.#triggerSlideChangeEvent(
            this.#slideStateDataFromIndex(null), 
            this.#slideStateDataFromIndex(0), 
            SCROLL_NAVIGATOR.SCROLLED_BY.PROGRAM
        );
    }

    getIndexOfSlideById(slideId) {
        return this.slides.findIndex(slide => slide.dataset.scrollSlideId === slideId);
    }

    hasSlideWithId(slideId) {
        return this.getIndexOfSlideById(slideId) !== -1;
    }

    isAtFirst() {
        return this.currentSlideIndex === 0;    
    }

    isAtLast() {
        return this.currentSlideIndex === this.slides.length - 1;
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
             
        const fromIndex = this.currentSlideIndex;
        const targetSlide = this.slides[targetIndex];        

        this.isProgramScrolling = true;

        targetSlide.scrollIntoView({
            behavior: this.config.scrollBehavior,
            block: 'start',
            inline: 'start'
        });
        
        this.#handleProgramScrollingTimeout();

        const fromSlideData = this.#slideStateDataFromIndex(fromIndex);
        const toSlideData = this.#slideStateDataFromIndex(targetIndex);

        this.#updateAndTriggerChangeEvent(fromSlideData, toSlideData, SCROLL_NAVIGATOR.SCROLLED_BY.PROGRAM);        
    }

    #slideStateDataFromIndex(index) {
        if (index === null || 
            index === undefined || 
            index < 0 ||
            index > this.slides.length) {
            return {
                index: null,
                slide: null,
                id: null
            };
        }

        const slide = this.slides[index];
        const slideId = slide?.dataset?.scrollSlideId;

        return {
            index: index,
            slide: slide,
            id: slideId
        };
    }

    getCurrentState() {
        return this.#slideStateDataFromIndex(this.currentSlideIndex);
    }

    navigateToNextSlide() {
        if (this.isAtLast()) {
            this.navigateToSlideByIndex(0);
            return;
        }

        this.navigateToSlideByIndex(this.currentSlideIndex + 1);
    }

    navigateToPreviousSlide() {
        if (this.isAtFirst()) {
            this.navigateToSlideByIndex(this.slides.length - 1);
            return;
        }

        this.navigateToSlideByIndex(this.currentSlideIndex - 1);
    }
    
    #handleUserSlideChange(targetIndex) {
        if (this.isProgramScrolling) {
            this.logger.log('Ignoring user slide change during program navigation');
            return;
        }

        if (targetIndex === this.currentSlideIndex) {
            return;
        }
             
        const fromIndex = this.currentSlideIndex;        

        this.logger.log(`User scroll detected ${fromIndex} -> ${targetIndex}`);

        const fromSlideData = this.#slideStateDataFromIndex(fromIndex);
        const toSlideData = this.#slideStateDataFromIndex(targetIndex);

        this.#updateAndTriggerChangeEvent(fromSlideData, toSlideData, SCROLL_NAVIGATOR.SCROLLED_BY.USER);        
    }

    #mergeConfig(direction, defaultConfig, dataset) {
        const config = {...defaultConfig};

        ['showNavButtons', 'showDots', 'autoScroll'].forEach(key => {
            if (dataset[key] === undefined) {                
                return;
            }
            config[key] = dataset[key] === 'true';
        });

        ['autoScrollDelay'].forEach(key => {
            if (dataset[key] === undefined) {
                return;
            }
            config[key] = parseInt(dataset[key]);
        });        

        ['navPosition', 'scrollBehavior'].forEach(key => {
            if (dataset[key] === undefined) {
                return;
            }
            config[key] = dataset[key];
        });

        this.#configureDefaultPosition(direction, config);

        return config;
    }

    #configureDefaultPosition(direction, config) {
        let navPosition = config['navPosition'];
        const isHorizontal = direction === SCROLL_NAVIGATOR.DIRECTION.HORIZONTAL;
        const isVertical = direction === SCROLL_NAVIGATOR.DIRECTION.VERTICAL;
        if (navPosition === undefined || navPosition === null) {
            navPosition = isHorizontal ? SCROLL_NAVIGATOR.NAV_POSITION.TOP : SCROLL_NAVIGATOR.NAV_POSITION.LEFT;
        }
        const horizontalPositions = [SCROLL_NAVIGATOR.NAV_POSITION.TOP, SCROLL_NAVIGATOR.NAV_POSITION.BOTTOM];
        const verticalPositions = [SCROLL_NAVIGATOR.NAV_POSITION.LEFT, SCROLL_NAVIGATOR.NAV_POSITION.RIGHT];
        const isValidHorizontal = isHorizontal && horizontalPositions.includes(navPosition);
        const isValidVertical = isVertical && verticalPositions.includes(navPosition);
        const defaultHorizontal = horizontalPositions[0];
        const defaultVertical = verticalPositions[0];

        if (!isValidHorizontal || !isValidVertical) {
            const defaultPosition = isHorizontal ? defaultHorizontal : defaultVertical;
            this.logger.log(`Assigned navigation elements position ${navPosition} is not valid for ${direction} direction! Using default (${defaultPosition}) instead`);
            config['navPosition'] = defaultPosition;
        }
    }

    #setupScrollDetection() {
        this.scrollDetector = new ScrollDetector(this.logger, {
            container: this,
            config: this.config.scrollDetectorConfig,
            onSlideChange: this.#handleUserSlideChange.bind(this),
            onVisibilityChange: this.#handleVisibilityChange.bind(this)            
        });
    }

    #handleVisibilityChange(isVisible, intersectionRatio = 0) {
        const wasVisible = this.isVisible;
        this.isVisible = isVisible;
        this.intersectionRatio = intersectionRatio;
        
        if (wasVisible === isVisible) {
            return;
        }

        this.logger.log(`Container ${this.id} visibility changed: ${wasVisible} -> ${isVisible}`);                                    
        EleganceTheme.triggerEvent(EVENTS.SCROLL_NAVIGATOR.CONTAINER_VISIBILITY_CHANGE, {
            container: this,
            isVisible: isVisible,
            intersectionRatio: intersectionRatio
        });
        
        if (!this.config.autoScroll) {
            return;
        }

        if (isVisible) {
            this.#startAutoScroll();
        } else {
            this.#stopAutoScroll();
        }        
    }

    #setupScrollUI() {
        this.ui = new ScrollUI(this.logger, this, this.config);
        this.ui.init();
    }

    #startAutoScroll() {
        if (!this.config.autoScroll) {
            return;
        }
        
        clearInterval(this.autoScrollTimer);

        this.logger.log(`Starting auto-scroll for container ${this.id} with delay ${this.config.autoScrollDelay}ms`);

        this.autoScrollTimer = setInterval(() => {
            this.navigateToNextSlide();
        }, this.config.autoScrollDelay);
    }

    #stopAutoScroll() {
        if (!this.autoScrollTimer) {
            return;
        }

        clearInterval(this.autoScrollTimer);
        this.autoScrollTimer = null;
        this.logger.log(`Stopped auto-scroll for container ${this.id}`);
    }

    #updateUI() {
        if (!this.ui) {
            return;
        }
        this.ui.update(this.currentSlideIndex);
    }

    #handleProgramScrollingTimeout() {
        clearTimeout(this.programScrollTimeout);
        this.logger.log(`Creating program scroll timeout which will last ${this.config.scrollAnimationDuration + 50}`);
        this.programScrollTimeout = setTimeout(() => {
            this.isProgramScrolling = false;
        }, this.config.scrollAnimationDuration + 50);
    }

    #updateAndTriggerChangeEvent(fromSlideData, toSlideData, scrolledBy) {
        this.currentSlideIndex = toSlideData.index;        
        this.#updateUI();     
        this.#triggerSlideChangeEvent(fromSlideData, toSlideData, scrolledBy);
    }

    #triggerSlideChangeEvent(fromSlideData, toSlideData, scrolledBy) {
        this.logger.log(`${EVENTS.SCROLL_NAVIGATOR.SLIDE_CHANGE} called here with these params`, 
            {
                container: this,
                fromSlideData: fromSlideData,                
                toSlideData: toSlideData,                
                scrolledBy: scrolledBy
            }
        );

        EleganceTheme.triggerEvent(EVENTS.SCROLL_NAVIGATOR.SLIDE_CHANGE, {
            container: this,
            fromSlideData: fromSlideData,                
            toSlideData: toSlideData,                
            scrolledBy: scrolledBy
        });               
    }

    #getCurrentSlide() {
        if (this.currentSlideIndex === null || this.currentSlideIndex < 0 || this.currentSlideIndex > this.slides.length) {
            return null;
        }
        return this.slides[this.currentSlideIndex];
    }
}

export class ScrollDetector {
    constructor(logger, { container, config = {}, onSlideChange, onVisibilityChange }) {
        const defaultConfig = {
            intersectionThreshold: 0.5,
            intersectionRootMargin: '-10px',
            scrollDetectionDelay: 100,
            containerVisibilityThreshold: 0.3,
            containerVisiblityRootMargin: '50px',
        }

        this.config = {
            ...defaultConfig,
            ...config
        }

        this.logger = logger;
        this.container = container;        
        this.onSlideChange = onSlideChange;
        this.onVisibilityChange = onVisibilityChange;
        
        this.slideIntersectionObserver = null;
        this.scrollTimeout = null;
        this.slideIntersectionTimeout = null;
        this.lastDetectedIndex = container.currentSlideIndex;

        this.containerVisibilityObserver = null;
        this.containerVisibilityTimeout = null;

        if ('IntersectionObserver' in window) {
            this.#setupIntersectionObserver();
            this.#setupContainerVisibilityObserver();
        } else {
            this.logger.log('IntersectionObserver not supported, using scroll fallback');
            this.#setupScrollFallback();
            this.#setupContainerVisibilityFallback();
        }
    }    

    #setupIntersectionObserver() {
        this.slideIntersectionObserver = new IntersectionObserver(
            (entries) => this.#handleIntersectionEntries(entries),
            {
                root: this.container.element,
                threshold: this.config.intersectionThreshold,
                rootMargin: this.config.intersectionRootMargin
            }
        );
        
        this.container.slides.forEach(slide => {
            this.slideIntersectionObserver.observe(slide);
        });

        this.logger.log('IntersectionObserver setup complete');
    }

    #handleIntersectionEntries(entries) {        
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
            clearTimeout(this.slideIntersectionTimeout);
            this.slideIntersectionTimeout = setTimeout(() => {                
                if (!this.container.isProgramScrolling && 
                    mostVisibleIndex !== this.lastDetectedIndex) {
                    
                    this.lastDetectedIndex = mostVisibleIndex;
                    this.onSlideChange(mostVisibleIndex);
                }
            }, this.config.scrollDetectionDelay);
        }
    }

        #setupScrollFallback() {
        this.container.element.addEventListener('scroll', () => {            
            if (this.container.isProgramScrolling) {
                return;
            }

            clearTimeout(this.scrollTimeout);
            this.scrollTimeout = setTimeout(() => {
                this.#detectSlideFromScrollPosition();
            }, this.config.scrollDetectionDelay);
        });
    }

    #detectSlideFromScrollPosition() {
        let targetIndex = 0;
        const element = this.container.element;
        const slides = this.container.slides;

        switch(this.container.direction) {
            case SCROLL_NAVIGATOR.DIRECTION.VERTICAL:
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

            case SCROLL_NAVIGATOR.DIRECTION.HORIZONTAL:
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

    #setupContainerVisibilityObserver() {
        this.containerVisibilityObserver = new IntersectionObserver(
            (entries) => this.#handleContainerVisibilityEntries(entries),
            {
                threshold: this.config.containerVisibilityThreshold,
                rootMargin: this.config.containerVisiblityRootMargin
            }
        );

        this.containerVisibilityObserver.observe(this.container.element);
        this.logger.log('Container visibility observer setup complete');
    }

    #handleContainerVisibilityEntries(entries) {
        entries.forEach(entry => {
            const isVisible = entry.isIntersecting;
            const intersectionRatio = entry.intersectionRatio;
            
            this.logger.log(`Container ${this.container.id} visibility: ${isVisible} (${Math.round(intersectionRatio * 100)}%)`);
                        
            this.onVisibilityChange(isVisible, intersectionRatio);
        });
    }

    #setupContainerVisibilityFallback() {        
        const checkVisibility = () => {
            clearTimeout(this.containerVisibilityTimeout);
            this.containerVisibilityTimeout = setTimeout(() => {
                this.#detectContainerVisibilityFallback();
            }, this.config.scrollDetectionDelay);
        };

        window.addEventListener('scroll', checkVisibility);
        window.addEventListener('resize', checkVisibility);
                
        setTimeout(() => this.#detectContainerVisibilityFallback(), 100);
        
        this.logger.log('Container visibility fallback setup complete');
    }

    #detectContainerVisibilityFallback() {
        const containerRect = this.container.element.getBoundingClientRect();
        const windowHeight = window.innerHeight;
        const windowWidth = window.innerWidth;
        
        // Check if container is in viewport
        const isInViewportY = containerRect.top < windowHeight && containerRect.bottom > 0;
        const isInViewportX = containerRect.left < windowWidth && containerRect.right > 0;
        const isInViewport = isInViewportY && isInViewportX;
        
        if (!isInViewport) {
            this.onVisibilityChange(false, 0);
            return;
        }
        
        // Calculate intersection ratio manually
        const visibleTop = Math.max(0, containerRect.top);
        const visibleBottom = Math.min(windowHeight, containerRect.bottom);
        const visibleLeft = Math.max(0, containerRect.left);
        const visibleRight = Math.min(windowWidth, containerRect.right);
        
        const visibleHeight = Math.max(0, visibleBottom - visibleTop);
        const visibleWidth = Math.max(0, visibleRight - visibleLeft);
        const visibleArea = visibleHeight * visibleWidth;
        const totalArea = containerRect.height * containerRect.width;
        
        const intersectionRatio = totalArea > 0 ? visibleArea / totalArea : 0;
        const isVisible = intersectionRatio >= this.config.containerVisibilityThreshold;
        
        this.logger.log(`Container ${this.container.id} visibility (fallback): ${isVisible} (${Math.round(intersectionRatio * 100)}%)`);
        this.onVisibilityChange(isVisible, intersectionRatio);
    }

    destroy() {
        if (this.slideIntersectionObserver) {
            this.slideIntersectionObserver.disconnect();
            this.slideIntersectionObserver = null;
        }

        if (this.containerVisibilityObserver) {
            this.containerVisibilityObserver.disconnect();
            this.containerVisibilityObserver = null;
        }

        clearTimeout(this.scrollTimeout);
        clearTimeout(this.slideIntersectionTimeout);
        clearTimeout(this.containerVisibilityTimeout);
    }
}

export class ScrollUI {
    constructor(logger, container, config) {
        this.container = container;
        this.config = config;
        this.logger = logger;
        
        this.elements = {
            container: null,
            previousButton: null,
            nextButton: null,
            dots: []
        };
    }

    init() {
        if (!this.config.showDots && !this.config.showNavButtons) {
            return;
        }
        this.#createNavigationContainer();
        this.#createControls();
        this.#attachEvents();
        this.update(this.container.currentSlideIndex);
    }

    #createNavigationContainer() {
        const navContainer = document.createElement('div');
        navContainer.classList.add(
            'scroll-nav',
            `scroll-nav-${this.container.direction}`, 
            `scroll-nav-${this.config.navPosition}`
        );        
        this.#positionNavigationContainer(navContainer);
        this.elements.container = navContainer;
    }

    #positionNavigationContainer(navContainer) {
        const containerParent = this.container.element.parentElement;
        
        switch (this.container.direction) {
            case SCROLL_NAVIGATOR.DIRECTION.VERTICAL:
                let positioningParent = this.#ensurePositioningWrapper(containerParent);
                switch(this.config.navPosition) {
                    case SCROLL_NAVIGATOR.NAV_POSITION.RIGHT:
                        navContainer.classList.add('scroll-nav-right');
                        positioningParent.appendChild(navContainer);
                        break;
                    case SCROLL_NAVIGATOR.NAV_POSITION.LEFT:
                        navContainer.classList.add('scroll-nav-left');
                        positioningParent.insertBefore(navContainer, this.container.element);
                        break;
                }                                
                break;

            case SCROLL_NAVIGATOR.DIRECTION.HORIZONTAL:
                switch(this.config.navPosition) {
                    case SCROLL_NAVIGATOR.NAV_POSITION.BOTTOM:
                        containerParent.insertBefore(navContainer, this.container.element.nextSibling);
                        break;
                    case SCROLL_NAVIGATOR.NAV_POSITION.TOP:
                        containerParent.insertBefore(navContainer, this.container.element);
                        break;
                }                
                break;
        }
    }

    #ensurePositioningWrapper(containerParent) {        
        const existingWrapper = containerParent.querySelector(`[data-scroll-nav-wrapper="${this.container.id}"]`);
        if (existingWrapper) {
            return existingWrapper;
        }
        
        const computedStyle = window.getComputedStyle(containerParent);
        if (computedStyle.position === 'relative' || computedStyle.position === 'absolute') {
            return containerParent;
        }
        
        const wrapper = document.createElement('div');
        wrapper.classList.add('scroll-nav-positioning-wrapper');
        wrapper.setAttribute('data-scroll-nav-wrapper', this.container.id);
        
        containerParent.insertBefore(wrapper, this.container.element);
        wrapper.appendChild(this.container.element);
        
        return wrapper;
    }

    #createControls() {       
        const controlsContainer = document.createElement('div');
        controlsContainer.className = 'scroll-nav-controls';
        
        let previousButton = null;
        let nextButton = null;
        let dotsContainer = null;
        
        if (this.config.showNavButtons) {
            const buttons = this.#createButtons();
            previousButton = buttons.previous;
            nextButton = buttons.next;
        }
        
        if (this.config.showDots) {
            dotsContainer = this.#createDots();
        }
        
        if (previousButton) {
            controlsContainer.appendChild(previousButton);
        }
        if (dotsContainer) {
            controlsContainer.appendChild(dotsContainer);
        }
        if (nextButton) {
            controlsContainer.appendChild(nextButton);
        }

        this.elements.container.appendChild(controlsContainer);
    }

    #createButtons() {
        const prevButton = document.createElement('button');
        prevButton.classList.add('scroll-btn', 'scroll-btn-prev')        
        const prevIcon = document.createElement('i');
        prevIcon.classList.add('fa');
        switch(this.container.direction) {
            case SCROLL_NAVIGATOR.DIRECTION.HORIZONTAL:
                prevIcon.classList.add('fa-chevron-left');
                break;
            case SCROLL_NAVIGATOR.DIRECTION.VERTICAL:
                prevIcon.classList.add('fa-chevron-up');
                break;
        }        
        prevButton.appendChild(prevIcon);

        const nextButton = document.createElement('button');
        nextButton.classList.add('scroll-btn', 'scroll-btn-next')        
        const nextIcon = document.createElement('i');
        nextIcon.classList.add('fa');
        switch(this.container.direction) {
            case SCROLL_NAVIGATOR.DIRECTION.HORIZONTAL:
                nextIcon.classList.add('fa-chevron-right');
                break;
            case SCROLL_NAVIGATOR.DIRECTION.VERTICAL:
                nextIcon.classList.add('fa-chevron-down');
                break;
        }              
        nextButton.appendChild(nextIcon);
        
        this.elements.previousButton = prevButton;
        this.elements.nextButton = nextButton;

        return {
            previous: prevButton,
            next: nextButton
        };
    }

    #createDots() {
        const dotsContainer = document.createElement('div');
        dotsContainer.className = 'scroll-dots';

        this.container.slides.forEach((_, index) => {
            const dotButton = document.createElement('button');
            dotButton.classList.add('scroll-dot');
            dotButton.classList.toggle('active', index === 0);                      
            dotButton.dataset.slideIndex = index;
            dotsContainer.appendChild(dotButton);
            this.elements.dots.push(dotButton);
        });

        return dotsContainer;
    }

    #attachEvents() {
        if (this.elements.previousButton) {
            this.elements.previousButton.addEventListener('click', () => {
                this.container.navigateToPreviousSlide();
            });
        }
        if (this.elements.nextButton) {
            this.elements.nextButton.addEventListener('click', () => {
                this.container.navigateToNextSlide();
            });
        }
        this.elements.dots.forEach(dot => {
            dot.addEventListener('click', () => {
                this.container.navigateToSlideByIndex(Number(dot.dataset.slideIndex));
            });
        });
    }

    update(activeIndex) {
        if (this.elements.dots.length) {
            this.elements.dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === activeIndex);
            });
        }
        if (this.elements.previousButton) {
            this.elements.previousButton.disabled = this.container.isAtFirst();
            this.elements.previousButton.classList.toggle('disabled', this.container.isAtFirst());
        }
        if (this.elements.nextButton) {
            this.elements.nextButton.disabled = this.container.isAtLast();
            this.elements.nextButton.classList.toggle('disabled', this.container.isAtLast());
        }
    }

    destroy() {
       if (this.elements.container) {            
            const wrapper = this.elements.container.closest('[data-scroll-nav-wrapper]');
            if (wrapper && wrapper.dataset.scrollNavWrapper === this.container.id) {
                const container = wrapper.querySelector(this.container.element.tagName);
                wrapper.parentElement.insertBefore(container, wrapper);
                wrapper.remove();
            } else {
                this.elements.container.remove();
            }
        }
        this.elements = { container: null, previousButton: null, nextButton: null, dots: [] };
    }
}
