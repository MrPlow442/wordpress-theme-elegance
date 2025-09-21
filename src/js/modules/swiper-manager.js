/**
 * SwiperManager - Data-attribute driven Swiper initialization and management
 * Automatically discovers and initializes Swiper instances based on data attributes
 * 
 * @package Elegance
 * @version 2.0.0
 */

import { Swiper } from 'swiper';
import { SwiperConfig } from '../util/swiper-config.js';
import { EleganceModule } from '../modules/module.js';
import { MODULES } from '../modules/constants.js';

export class SwiperManager extends EleganceModule {
  constructor(themeConfig = {}) {
    super(MODULES.SWIPER_MANAGER, themeConfig);

    this.swipers = new Map();
    this.isInitialized = false;    

    this.config = {
      selector: '[data-swiper-id]',
      ...this.themeConfig.swiperManager
    };

    // Bind methods
    this.handleSlideChange = this.handleSlideChange.bind(this);
    this.handleTransitionStart = this.handleTransitionStart.bind(this);
    this.handleTransitionEnd = this.handleTransitionEnd.bind(this);
  }

  init() {
    this.initializeSwipers();
    this.isInitialized = true;
    this.logger.log('SwiperManager: Initialized with', this.swipers.size, 'instances');
  }

  postInit() {    
  }

  initializeSwipers() {
    const swiperElements = document.querySelectorAll(this.config.selector);
    
    swiperElements.forEach(element => {
      this.initializeSwiperInstance(element);

    });
  }

  initializeSwiperInstance(element) {
    const swiperId = element.dataset.swiperId;
    const config = SwiperConfig.parseDataAttributes(element);        
    this.logger.log(`Parsed config for ${swiperId}`, config);
    
    try {
      // Create Swiper instance      
      const swiperInstance = new Swiper(element, config);      
            
      // Store reference
      this.swipers.set(swiperId, {
        instance: swiperInstance,
        element: element,
        config: config,
        id: swiperId
      });

      this.logger.log('Instantiated swiper', this.swipers.get(swiperId));
      this.logger.log(`Initialized Swiper: ${swiperId}`, config);

    } catch (error) {
      this.logger.error(`Failed to initialize Swiper for element:`, element, error);
    }
  }

  handleSlideChange(swiper) {
    const swiperData = this.findSwiperDataByInstance(swiper);
    if (!swiperData) {
      return;
    }        
    this.logger.log(`Slide changed in ${swiperData.id}:`, swiper.previousIndex, '->', swiper.activeIndex);
  }

  handleTransitionStart(swiper) {
    const swiperData = this.findSwiperDataByInstance(swiper);
    if (!swiperData) return;

    swiperData.element.classList.add('swiper-transitioning');
  }

  handleTransitionEnd(swiper) {
    const swiperData = this.findSwiperDataByInstance(swiper);
    if (!swiperData) return;

    swiperData.element.classList.remove('swiper-transitioning');
  }

  triggerSlideEvents(swiper, activeIndex, previousIndex, eventType) {
    const swiperData = this.findSwiperDataByInstance(swiper);
    if (!swiperData) return;

  }

  getSlideData(swiper, index) {
    if (index < 0 || index >= swiper.slides.length) {
      return {
        index: null,
        slide: null,
        id: null
      };
    }

    const slide = swiper.slides[index];
    const slideId = slide?.dataset?.slideId || slide?.id;

    return {
      index: index,
      slide: slide,
      id: slideId
    };
  }

  findSwiperDataByInstance(instance) {
    for (const [id, data] of this.swipers) {
      if (data.instance === instance) {
        return data;
      }
    }
    return null;
  }

  // Public API methods
  getSwiperById(id) {
    return this.swipers.get(id)?.instance || null;
  }

  slideTo(slideIndex, swiperId) {
    const swiper = this.getSwiperById(swiperId);
    if (swiper) {
      swiper.slideTo(slideIndex);
    }
  }

  slideNext(swiperId) {
    const swiper = this.getSwiperById(swiperId);
    if (swiper) {
      swiper.slideNext();
    }
  }

  slidePrev(swiperId) {
    const swiper = this.getSwiperById(swiperId);
    if (swiper) {
      swiper.slidePrev();
    }
  }

  updateSwiper(swiperId) {
    const swiper = this.getSwiperById(swiperId);
    if (swiper) {
      swiper.update();
    }
  }

  updateAllSwipers() {
    this.swipers.forEach(data => {
      data.instance.update();
    });
  }

  navigateToSlideById(slideId, containerId) {
    const swiper = this.getSwiperById(containerId);
    if (!swiper) {
      return;
    }

    const slideIndex = Array.from(swiper.slides).findIndex(slide => 
      slide.dataset.slideId === slideId || slide.id === slideId
    );

    if (slideIndex !== -1) {
      swiper.slideTo(slideIndex);
    }
  }

  getContainerCurrentSlideState(containerId) {
    const swiper = this.getSwiperById(containerId);
    if (!swiper) {
      return null;
    }

    return this.getSlideData(swiper, swiper.activeIndex);
  }

  destroy() {
    // Destroy all Swiper instances
    this.swipers.forEach(data => {
      if (data.instance && typeof data.instance.destroy === 'function') {
        data.instance.destroy(true, true);
      }
    });

    this.swipers.clear();    
    this.isInitialized = false;

    this.logger.log('SwiperManager: Destroyed all instances');
  }
}
