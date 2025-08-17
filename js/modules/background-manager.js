/**
 * Background Manager Module - Handles dynamic background images and videos
 * Provides centralized background management for the theme
 * 
 * @package Elegance
 * @version 2.0.0
 */

class BackgroundManager extends EleganceModule {
    constructor(themeConfig = {}, silence = false) {
        super(MODULES.BACKGROUND_MANAGER, themeConfig, silence);
        this.elements = {
            videoElement: null,
            imageElement: null
        };

        this.config = {
            transitionDuration: 200,
            videoElementId: null,
            imageElementId: null,
            defaultVideoUrl: null,
            defaultImageUrl: null,
            pageInfo: [],
            ...this.themeConfig
        };
        this.logger.log('Theme Config:', this.themeConfig, ' Background Config:', this.config);
        this.preloadedImages = new Set();
        
        this.handleSlideChange = this.handleSlideChange.bind(this);
    }

    init() {
        this.findBackgroundElements();
        this.preloadImages();
        this.showDefaultBackground();
        this.bindEvents();
        
        this.logger.log('BackgroundManager: Initialized');
    }

    findBackgroundElements() {
        if (this.config.videoElementId) {
            this.elements.videoElement = document.getElementById(this.config.videoElementId);
        }
        
        if (this.config.imageElementId) {
            this.elements.imageElement = document.getElementById(this.config.imageElementId);
        }
        
        if (!this.elements.videoElement && !this.elements.imageElement) {
            this.logger.warn('BackgroundManager: No background elements found');
        }
    }

    preloadImages() {
        if (!Array.isArray(this.config.pageInfo)) {
            return;
        }

        const imageUrls = this.config.pageInfo
            .filter(page => page && page.hasThumbnail && page.thumbnail)
            .map(page => page.thumbnail);

        this.preloadImageUrls(imageUrls);
    }

    preloadImageUrls(urls) {
        if (!Array.isArray(urls)) {
            return;
        }

        urls.forEach(url => {
            if (url && !this.preloadedImages.has(url)) {
                const img = new Image();
                img.onload = () => {
                    this.preloadedImages.add(url);
                    this.logger.log(`BackgroundManager: Preloaded image ${url}`);
                };
                img.onerror = () => {
                    this.logger.warn(`BackgroundManager: Failed to preload image ${url}`);
                };
                img.src = url;
            }
        });
    }

    bindEvents() {
        EleganceTheme.bindEvent(EVENTS.SCROLL_NAVIGATOR.SLIDE_CHANGE, this.handleSlideChange);        
    }

    handleSlideChange({ detail }) {
        this.logger.log('Slide Change Event received: ', detail);        
        if (detail.container.id !== SCROLL_NAVIGATOR.MAIN_CONTAINER_ID) {
            return;
        }

        const slideId = detail.toSlideData.id;
        this.updateBackgroundForSlide(slideId);
    }

    updateBackgroundForSlide(slideId) {        
        const matchingPage = this.config.pageInfo.find(page => 
            page && page.name === slideId
        );

        if (matchingPage && matchingPage.hasThumbnail && matchingPage.thumbnail) {
            this.showImage(matchingPage.thumbnail);
            this.logger.log(`BackgroundManager: Changed background to ${matchingPage.thumbnail}`);
        } else {
            this.showDefaultBackground();
            this.logger.log(`BackgroundManager: Changed to default background for section ${slideId}`);
        }
    }

    showDefaultBackground() {
        if (this.elements.videoElement && this.config.defaultVideoUrl) {
            this.showVideo(this.config.defaultVideoUrl);
        } else if (this.elements.imageElement && this.config.defaultImageUrl) {
            this.showImage(this.config.defaultImageUrl);
        }
    }

    showImage(imageUrl) {
        if (!imageUrl) {
            this.logger.warn('BackgroundManager: No image URL provided');
            return;
        }

        this.hideElement(this.elements.videoElement);
        this.showElementWithSource(this.elements.imageElement, imageUrl);
    }

    showVideo(videoUrl) {
        if (!videoUrl) {
            this.logger.warn('BackgroundManager: No video URL provided');
            return;
        }

        this.hideElement(this.elements.imageElement);
        this.showElementWithSource(this.elements.videoElement, videoUrl);
    }

    showElementWithSource(element, sourceUrl) {
        if (!element || !sourceUrl) {
            return;
        }
        
        if (this.getElementSource(element) === sourceUrl) {
            this.setElementVisibility(element, 'visible');
            return;
        }
        
        element.style.opacity = '0';

        setTimeout(() => {
            this.setElementSource(element, sourceUrl);
            element.style.opacity = '1';
            this.setElementVisibility(element, 'visible');
        }, this.config.transitionDuration);
    }

    hideElement(element) {
        if (!element) {
            return;
        }

        this.setElementVisibility(element, 'hidden');
    }

    setElementVisibility(element, visibility) {
        if (!element) {
            return;
        }

        const validStates = ['hidden', 'visible'];
        if (!validStates.includes(visibility)) {
            this.logger.warn(`BackgroundManager: Invalid visibility state '${visibility}'`);
            return;
        }

        if (element.classList.contains(visibility)) {
            return;
        }

        const oppositeState = visibility === 'hidden' ? 'visible' : 'hidden';
        if (element.classList.contains(oppositeState)) {
            element.classList.remove(oppositeState);
        }

        element.classList.add(visibility);
    }

    getElementSource(element) {
        if (!element) {
            return null;
        }

        if (this.isVideoElement(element)) {
            const sourceElement = element.querySelector('source');
            return sourceElement ? sourceElement.src : null;
        }

        if (this.isImageElement(element)) {
            return element.src || null;
        }

        return null;
    }

    setElementSource(element, sourceUrl) {
        if (!element || !sourceUrl) {
            return;
        }

        if (this.isVideoElement(element)) {
            this.setVideoSource(element, sourceUrl);
        } else if (this.isImageElement(element)) {
            this.setImageSource(element, sourceUrl);
        }
    }

    setVideoSource(videoElement, sourceUrl) {
        let sourceElement = videoElement.querySelector('source');
        
        if (!sourceElement) {
            sourceElement = document.createElement('source');
            sourceElement.type = 'video/mp4';
            videoElement.appendChild(sourceElement);
        }

        sourceElement.src = sourceUrl;
        videoElement.load();
    }

    setImageSource(imageElement, sourceUrl) {
        imageElement.src = sourceUrl;
    }

    isVideoElement(element) {
        return element && element.tagName.toLowerCase() === 'video';
    }

    isImageElement(element) {
        return element && element.tagName.toLowerCase() === 'img';
    }

    addPageConfig(pageConfig) {
        if (!pageConfig || !pageConfig.name) {
            this.logger.warn('BackgroundManager: Invalid page config');
            return;
        }

        this.config.pageInfo.push(pageConfig);

        if (pageConfig.hasThumbnail && pageConfig.thumbnail) {
            this.preloadImageUrls([pageConfig.thumbnail]);
        }
    }

    removePageConfig(pageName) {
        const index = this.config.pageInfo.findIndex(page => page.name === pageName);
        
        if (index !== -1) {
            this.config.pageInfo.splice(index, 1);
            this.logger.log(`BackgroundManager: Removed page config for '${pageName}'`);
        }
    }

    destroy() {
        // document.removeEventListener('sectionNavigator:sectionChange', this.handleSectionChange);
        this.preloadedImages.clear();
        
        this.logger.log('BackgroundManager: Destroyed');
    }
}

window.BackgroundManager = BackgroundManager;
