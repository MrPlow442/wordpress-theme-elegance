/**
 * Navigation Manager Module - Handles all navigation interactions
 * Mobile menu, side menu, and general navigation behaviors
 * 
 * @package Elegance
 * @version 2.0.0
 */

class NavigationManager extends EleganceModule {
    constructor(themeConfig = {}, silence = false) {
        super(MODULES.NAVIGATION_MANAGER, themeConfig, silence);
        this.state = {
            mobileMenuOpen: false,
            sideMenuOpen: false
        };
        
        this.config = {
            slideAnimationDuration: 300,
            mobileBreakpoint: 767,
            desktopNavigationSelector: '.navbar-nav li',
            mobileNavigationSelector: '.navigation-menu li',
            ...this.themeConfig.navigation
        };

        this.desktopNavigation = null;
        this.mobileNavigation = null;
        this.scrollNavigationElements = new Map();
        this.handleMenuTrigger = this.#handleMenuTrigger.bind(this);
        this.handleSideMenuClick = this.#handleSideMenuClick.bind(this);
        this.handleWindowResize = this.#handleWindowResize.bind(this);
    }

    init() {
        this.#initNavigationsAndHash();
        this.#bindEvents();        
        this.logger.log('Initialized');
    }

    #initNavigationsAndHash() {        
        this.desktopNavigation = new EleganceNavigation({
            selector: this.config.desktopNavigationSelector,
            onClick: this.#onNavigationItemClick.bind(this)
        });
        this.mobileNavigation = new EleganceNavigation({
            selector: this.config.mobileNavigationSelector,
            onClick: this.#onNavigationItemClick.bind(this)
        });
        const slideState = this.getModule(MODULES.SCROLL_NAVIGATOR)
                            .getContainerCurrentSlideState(SCROLL_NAVIGATOR.MAIN_CONTAINER_ID);
        
        if (!slideState || !slideState.id) {
            return;
        }

        const slideId = slideState.id;
        this.desktopNavigation.setActive(slideId);
        this.mobileNavigation.setActive(slideId);
        this.#setWindowHash(slideId);        
    }

    #onNavigationItemClick(event, navItem, slideId, containerId) {        
        this.logger.log(`Navigating to ${containerId}:${slideId}`);
        this.getModule(MODULES.SCROLL_NAVIGATOR).navigateToSlideById(slideId, containerId);
    }

    #bindEvents() {
        const menuTriggers = document.querySelectorAll('.menu-trigger');
        menuTriggers.forEach(trigger => {
            trigger.addEventListener('click', this.#handleMenuTrigger);
        });

        const sideMenuLinks = document.querySelectorAll('.side-menu .navbar-nav li a');
        sideMenuLinks.forEach(link => {
            link.addEventListener('click', this.#handleSideMenuClick);
        });
        window.addEventListener('resize', this.#handleWindowResize.bind(this));

        this.logger.log('Binding Slide Change Event');
        EleganceTheme.bindEvent(EVENTS.SCROLL_NAVIGATOR.SLIDE_CHANGE, this.#handleSlideChange.bind(this));
        window.addEventListener('hashchange', this.#handleHashChange.bind(this));
    }

    #handleSlideChange({detail}) {
        this.logger.log('Slide Change Event received: ', detail);
        if (detail.container.id !== SCROLL_NAVIGATOR.MAIN_CONTAINER_ID) {
            return;
        }
        const slideId = detail.toSlideData.id;
        this.desktopNavigation.setActive(slideId);
        this.mobileNavigation.setActive(slideId);
        this.#setWindowHash(slideId);
    }

    #handleHashChange(_) {
        const slideId = window.location.hash.substring(1);
        if (!slideId) {
            return;
        }        
        this.desktopNavigation.setActive(slideId);
        this.mobileNavigation.setActive(slideId);
    } 

    #setWindowHash(slideId) {
        const hash = `#${slideId}`;
        if (window.location.hash === hash) {
            return;
        }

        const url = window.location.pathname + window.location.search + hash;
        history.replaceState(null, '', url);
    }

    onNavbarToggle(data) {
        const { _, event } = data;
        
        event.preventDefault();
        this.toggleMobileMenu();
    }

    onMenuTrigger(data) {
        const { target, event } = data;
        
        event.preventDefault();
        this.toggleSideMenu();
    }

    #handleMenuTrigger(event) {
        event.preventDefault();
        this.toggleSideMenu();
    }

    #handleSideMenuClick(event) {
        this.closeSideMenu();
    }

    #handleWindowResize() {
        const width = window.innerWidth;
        
        if (width > this.config.mobileBreakpoint && this.state.mobileMenuOpen) {
            this.closeMobileMenu();
        }
    }

    toggleMobileMenu() {
        if (this.state.mobileMenuOpen) {
            this.closeMobileMenu();
        } else {
            this.openMobileMenu();
        }
    }

    openMobileMenu() {
        const navCollapse = document.querySelector('.navbar-collapse');
        
        if (navCollapse) {
            this.slideDown(navCollapse, this.config.slideAnimationDuration);
            this.state.mobileMenuOpen = true;
                        
            document.body.classList.add('mobile-menu-open');
            
            EleganceTheme.triggerEvent(EVENTS.NAVIGATION_MANAGER.MOBILE_MENU_OPENED);
        }
    }

    closeMobileMenu() {
        const navCollapse = document.querySelector('.navbar-collapse');
        
        if (navCollapse) {
            this.slideUp(navCollapse, this.config.slideAnimationDuration);
            this.state.mobileMenuOpen = false;
            
            document.body.classList.remove('mobile-menu-open');
            
            EleganceTheme.triggerEvent(EVENTS.NAVIGATION_MANAGER.MOBILE_MENU_CLOSED);
        }
    }

    toggleSideMenu() {
        if (this.state.sideMenuOpen) {
            this.closeSideMenu();
        } else {
            this.openSideMenu();
        }
    }

    openSideMenu() {
        document.body.classList.add('sidemenu-open');
        this.state.sideMenuOpen = true;
        
        EleganceTheme.triggerEvent(EVENTS.NAVIGATION_MANAGER.SIDE_MENU_OPENED);
    }

    closeSideMenu() {
        document.body.classList.remove('sidemenu-open');
        this.state.sideMenuOpen = false;
        
        EleganceTheme.triggerEvent(EVENTS.NAVIGATION_MANAGER.SIDE_MENU_CLOSED);
    }

    slideDown(element, duration = 300) {
        if (!element) return;

        element.style.display = 'block';
        element.style.height = '0';
        element.style.overflow = 'hidden';
        element.style.transition = `height ${duration}ms ease`;

        element.offsetHeight;

        element.style.height = 'auto';
        const targetHeight = element.offsetHeight;

        element.style.height = '0';

        requestAnimationFrame(() => {
            element.style.height = `${targetHeight}px`;

            setTimeout(() => {
                element.style.height = 'auto';
                element.style.overflow = '';
                element.style.transition = '';
            }, duration);
        });
    }

    slideUp(element, duration = 300) {
        if (!element) return;

        element.style.height = `${element.offsetHeight}px`;
        element.style.overflow = 'hidden';
        element.style.transition = `height ${duration}ms ease`;

        element.offsetHeight;

        element.style.height = '0';

        setTimeout(() => {
            element.style.display = 'none';
            element.style.height = '';
            element.style.overflow = '';
            element.style.transition = '';
        }, duration);
    }

    isMobileMenuOpen() {
        return this.state.mobileMenuOpen;
    }

    isSideMenuOpen() {
        return this.state.sideMenuOpen;
    }

    closeAllMenus() {
        this.closeMobileMenu();
        this.closeSideMenu();
    }

    destroy() {
        const menuTriggers = document.querySelectorAll('.menu-trigger');
        menuTriggers.forEach(trigger => {
            trigger.removeEventListener('click', this.handleMenuTrigger);
        });

        const sideMenuLinks = document.querySelectorAll('.side-menu .navbar-nav li a');
        sideMenuLinks.forEach(link => {
            link.removeEventListener('click', this.handleSideMenuClick);
        });

        window.removeEventListener('resize', this.handleWindowResize);

        this.closeAllMenus();
        
        this.logger.log('NavigationManager: Destroyed');
    }
}

class EleganceNavigation {
    constructor({selector, onClick}) {
        this.navItems = new Map();
        this.onClick = onClick;    

        document.querySelectorAll(selector).forEach(item => {            
            const attributeSelector = '[data-scroll-to]';
            const actionElement = item.matches(attributeSelector) ? item : item.querySelector(attributeSelector);

            if (!actionElement) {
                return;
            }

            const name = actionElement.dataset.scrollTo;
            if (!name) {
                return;
            }

            if (this.navItems.has(name)) {                
                return;
            }

            this.navItems.set(name, new EleganceNavigationItem({
                name: name,
                stateElement: item,
                actionElement: actionElement
            }));

            actionElement.addEventListener('click', (event) => this.#handleClickEvent(event));            
        });
    }

    #handleClickEvent(event) {
        event.preventDefault();
        const element = event.target;
        const slideId = element.dataset.scrollTo;
        const containerId = element.dataset.scrollToContainer;        
        this.onClick(event, this.navItems.get(slideId), slideId, containerId);
        this.setActive(slideId);
    }

    #handleHashChange(event) {
        const slideId = window.location.hash.substring(1);
        if (!slideId) {
            return;
        }
        this.onHashChange(event, this.navItems.get(slideId), slideId);
        this.setActive(slideId);
    } 

    clearActiveAll() {
        this.navItems.values().forEach(item => item.removeActive());
    }

    isActive(name) {
        return this.navItems.get(name)?.isActive() ?? false;
    }

    setActive(name) {
        if (!this.navItems.has(name) || this.isActive(name)) {
            return;
        }
        this.clearActiveAll();
        this.navItems.get(name).setActive();        
    }
}

class EleganceNavigationItem {    
    constructor({name, stateElement, actionElement}) {
        this.name = name;    
        this.stateElement = stateElement;
        this.actionElement = actionElement;        
    }

    isActive() {
        return this.stateElement && this.stateElement.classList.contains('active');
    }

    setActive() {
        if (this.isActive()) {
            return;
        }
        this.stateElement?.classList.add('active');        
    }

    removeActive() {
        if (!this.isActive()) {
            return;
        }
        this.stateElement.classList.remove('active');        
    }
}

window.NavigationManager = NavigationManager;
