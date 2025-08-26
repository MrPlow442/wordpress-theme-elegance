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
            targetContainer = Array.from(this.containers.values()).find(container => container.hasSlideWithId(slideId));
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
            scrollBehavior: 'smooth',
            scrollAnimationDuration: 600,
            showNavButtons: false,
            showDots: false,
            autoScroll: false,
            autoScrollDelay: 3000,
            slideInViewThreshold: 0.1,    // 10% visible = inView
            slideActiveThreshold: 0.5     // 50% visible = active slide
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

        this.slideStates = new Map(); // slideIndex -> {inView: boolean, isActive: boolean}

        this.isProgramScrolling = false;
        this.programScrollTimeout = null;

        this.ui = null;

        this.slides.forEach((slide, index) => {
            this.slideStates.set(index, { inView: false, isActive: index === 0 });
        });
    }

    init() {
        this.#setupScrollDetection();
        this.#setupScrollUI();
        this.#setupContainerVisibility();
        this.logger.log('ScrollContainer initialized')
    }

    postInit() {
        this.#checkAllSlidesVisibility();

        if (this.isVisible) {
            this.#startAutoScroll();
        }
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
        if (!this.hasSlideWithId(slideId)) {
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
        const fromSlideData = this.#slideStateDataFromIndex(fromIndex);
        const toSlideData = this.#slideStateDataFromIndex(targetIndex);

        this.#triggerSlideLeaveEvent(fromSlideData, toSlideData, SCROLL_NAVIGATOR.SCROLLED_BY.PROGRAM);

        this.isProgramScrolling = true;

        const targetSlide = this.slides[targetIndex];
        targetSlide.scrollIntoView({
            behavior: this.config.scrollBehavior,
            block: 'start',
            inline: 'start'
        });

        this.currentSlideIndex = targetIndex;
        this.#updateSlideStates(fromIndex, targetIndex);
        this.#updateUI();

        this.#triggerSlideInViewEvent(toSlideData, SCROLL_NAVIGATOR.SCROLLED_BY.PROGRAM);

        this.#handleProgramScrollingTimeout();
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

    getCurrentState() {
        return this.#slideStateDataFromIndex(this.currentSlideIndex);
    }

    #slideStateDataFromIndex(index) {
        if (index === null ||
            index === undefined ||
            index < 0 ||
            index >= this.slides.length) {
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

    #setupScrollDetection() {
        this.element.addEventListener('scroll', () => {
            if (!this.isProgramScrolling) {
                this.#checkAllSlidesVisibility();
            }
        }, { passive: true });
    }

    #checkAllSlidesVisibility() {
        this.logger.log('Calling checkAllSlidesVisiblity');
        const containerRect = this.element.getBoundingClientRect();
        let newActiveSlideIndex = this.currentSlideIndex;
        let maxActiveVisibility = 0;

        this.slides.forEach((slide, index) => {
            const slideRect = slide.getBoundingClientRect();
            const visibilityRatio = this.#calculateVisibilityRatio(slideRect, containerRect);
            
            const previousState = this.slideStates.get(index);
            const isInView = visibilityRatio >= this.config.slideInViewThreshold;
            const isActive = visibilityRatio >= this.config.slideActiveThreshold;

            this.logger.log(`Previous state for slide ${index}`, previousState);
            this.logger.log(`Setting state for slide ${index} to `, { inView: isInView, isActive });
            this.slideStates.set(index, { inView: isInView, isActive });

            if (visibilityRatio > maxActiveVisibility) {
                maxActiveVisibility = visibilityRatio;
                newActiveSlideIndex = index;
            }

            if (previousState.inView !== isInView) {
                if (isInView) {
                    this.logger.log(`Slide ${index} entered viewport (${Math.round(visibilityRatio * 100)}% visible)`);
                    this.#triggerSlideInViewEvent(
                        this.#slideStateDataFromIndex(index), 
                        SCROLL_NAVIGATOR.SCROLLED_BY.USER
                    );
                } else {
                    const destinationIndex = this.#determineDestinationSlide(index);
                    this.logger.log(`Slide ${index} left viewport, going to slide ${destinationIndex}`);
                    this.#triggerSlideLeaveEvent(
                        this.#slideStateDataFromIndex(index),
                        this.#slideStateDataFromIndex(destinationIndex),
                        SCROLL_NAVIGATOR.SCROLLED_BY.USER
                    );
                }
            }
        });

        if (newActiveSlideIndex !== this.currentSlideIndex) {
            this.logger.log(`Active slide changed from ${this.currentSlideIndex} to ${newActiveSlideIndex}`);
            this.currentSlideIndex = newActiveSlideIndex;
            this.#updateUI();
        }
    }

    #determineDestinationSlide(leavingIndex) {
        let bestIndex = leavingIndex;
        let bestVisibility = 0;

        const containerRect = this.element.getBoundingClientRect();
        
        this.slides.forEach((slide, index) => {
            if (index === leavingIndex) {
                return;
            }
            
            const slideRect = slide.getBoundingClientRect();
            const visibilityRatio = this.#calculateVisibilityRatio(slideRect, containerRect);
            
            if (visibilityRatio > bestVisibility) {
                bestVisibility = visibilityRatio;
                bestIndex = index;
            }
        });

        return bestIndex;
    }

    #calculateVisibilityRatio(slideRect, containerRect) {
        const intersectionTop = Math.max(slideRect.top, containerRect.top);
        const intersectionBottom = Math.min(slideRect.bottom, containerRect.bottom);
        const intersectionLeft = Math.max(slideRect.left, containerRect.left);
        const intersectionRight = Math.min(slideRect.right, containerRect.right);
        
        const intersectionHeight = Math.max(0, intersectionBottom - intersectionTop);
        const intersectionWidth = Math.max(0, intersectionRight - intersectionLeft);
        const intersectionArea = intersectionHeight * intersectionWidth;
        
        const slideArea = slideRect.width * slideRect.height;
        return slideArea > 0 ? intersectionArea / slideArea : 0;
    }

    #updateSlideStates(fromIndex, toIndex) {
        if (fromIndex !== null && fromIndex >= 0 && fromIndex < this.slides.length) {
            const fromState = this.slideStates.get(fromIndex);
            this.slideStates.set(fromIndex, { ...fromState, isActive: false });
        }
        
        const toState = this.slideStates.get(toIndex);
        this.slideStates.set(toIndex, { ...toState, isActive: true });
    }

    #setupContainerVisibility() {
        const checkVisibility = () => {
            this.#detectContainerVisibility();
        };

        window.addEventListener('scroll', checkVisibility, { passive: true });
        window.addEventListener('resize', checkVisibility);

        setTimeout(() => this.#detectContainerVisibility(), 100);
    }

    #detectContainerVisibility() {
        const containerRect = this.element.getBoundingClientRect();
        const windowHeight = window.innerHeight;
        const windowWidth = window.innerWidth;

        const isInViewportY = containerRect.top < windowHeight && containerRect.bottom > 0;
        const isInViewportX = containerRect.left < windowWidth && containerRect.right > 0;
        const isInViewport = isInViewportY && isInViewportX;

        if (!isInViewport) {
            this.#handleVisibilityChange(false, 0);
            return;
        }

        const visibleTop = Math.max(0, containerRect.top);
        const visibleBottom = Math.min(windowHeight, containerRect.bottom);
        const visibleLeft = Math.max(0, containerRect.left);
        const visibleRight = Math.min(windowWidth, containerRect.right);

        const visibleHeight = Math.max(0, visibleBottom - visibleTop);
        const visibleWidth = Math.max(0, visibleRight - visibleLeft);
        const visibleArea = visibleHeight * visibleWidth;
        const totalArea = containerRect.height * containerRect.width;

        const intersectionRatio = totalArea > 0 ? visibleArea / totalArea : 0;
        const threshold = 0.3;
        const isVisible = intersectionRatio >= threshold;

        this.#handleVisibilityChange(isVisible, intersectionRatio);
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
        this.programScrollTimeout = setTimeout(() => {
            this.isProgramScrolling = false;
        }, this.config.scrollAnimationDuration + 50);
    }

    #triggerSlideLeaveEvent(fromSlideData, toSlideData, scrolledBy) {
        this.logger.log(`onSlideLeave: slide ${fromSlideData.index} -> slide ${toSlideData.index}`, {
            container: this,
            fromSlideData: fromSlideData,
            toSlideData: toSlideData,
            scrolledBy: scrolledBy
        });

        EleganceTheme.triggerEvent(EVENTS.SCROLL_NAVIGATOR.SLIDE_LEAVE, {
            container: this,
            fromSlideData: fromSlideData,
            toSlideData: toSlideData,
            scrolledBy: scrolledBy
        });
    }

    #triggerSlideInViewEvent(slideData, scrolledBy) {
        this.logger.log(`slideInView: slide ${slideData.index}`, {
            container: this,
            slideData: slideData,
            scrolledBy: scrolledBy
        });

        EleganceTheme.triggerEvent(EVENTS.SCROLL_NAVIGATOR.SLIDE_IN_VIEW, {
            container: this,
            slideData: slideData,
            scrolledBy: scrolledBy
        });
    }

    #mergeConfig(direction, defaultConfig, dataset) {
        const config = { ...defaultConfig };

        ['showNavButtons', 'showDots', 'autoScroll'].forEach(key => {
            if (dataset[key] === undefined) {
                return;
            }
            config[key] = dataset[key] === 'true';
        });

        ['autoScrollDelay', 'slideInViewThreshold', 'slideActiveThreshold'].forEach(key => {
            if (dataset[key] === undefined) {
                return;
            }
            const value = parseFloat(dataset[key]);
            if (!isNaN(value)) {
                config[key] = value;
            }
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

    destroy() {
        this.#stopAutoScroll();
        
        clearTimeout(this.programScrollTimeout);
        
        if (this.ui) {
            this.ui.destroy();
        }
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
                switch (this.config.navPosition) {
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
                switch (this.config.navPosition) {
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
        switch (this.container.direction) {
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
        switch (this.container.direction) {
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