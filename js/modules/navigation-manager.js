/**
 * Navigation Manager Module - Handles all navigation interactions
 * Mobile menu, side menu, and general navigation behaviors
 * 
 * @package Elegance
 * @version 2.0.0
 */

class NavigationManager extends EleganceModule {
    constructor(themeConfig = {}) {
        super('NavigationManager', themeConfig);
        this.state = {
            mobileMenuOpen: false,
            sideMenuOpen: false
        };
        
        this.config = {
            slideAnimationDuration: 300,
            mobileBreakpoint: 767,
            ...this.themeConfig.navigation
        };

        this.handleMenuTrigger = this.handleMenuTrigger.bind(this);
        this.handleSideMenuClick = this.handleSideMenuClick.bind(this);
        this.handleWindowResize = this.handleWindowResize.bind(this);
    }

    init() {            
        this.bindEvents();        
        this.logger.log('NavigationManager: Initialized');
    }

    bindEvents() {
        const menuTriggers = document.querySelectorAll('.menu-trigger');
        menuTriggers.forEach(trigger => {
            trigger.addEventListener('click', this.handleMenuTrigger);
        });

        const sideMenuLinks = document.querySelectorAll('.side-menu .navbar-nav li a');
        sideMenuLinks.forEach(link => {
            link.addEventListener('click', this.handleSideMenuClick);
        });

        window.addEventListener('resize', this.handleWindowResize);
    }

    onNavbarToggle(data) {
        const { target, event } = data;
        
        event.preventDefault();
        this.toggleMobileMenu();
    }

    onMenuTrigger(data) {
        const { target, event } = data;
        
        event.preventDefault();
        this.toggleSideMenu();
    }

    handleMenuTrigger(event) {
        event.preventDefault();
        this.toggleSideMenu();
    }

    handleSideMenuClick(event) {
        this.closeSideMenu();
    }

    onWindowResize(data) {
        const { width } = data;
        
        if (width > this.config.mobileBreakpoint && this.state.mobileMenuOpen) {
            this.closeMobileMenu();
        }
    }

    handleWindowResize() {
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
            
            this.triggerEvent(EVENTS.NAVIGATION_MANAGER.MOBILE_MENU_OPENED);
        }
    }

    closeMobileMenu() {
        const navCollapse = document.querySelector('.navbar-collapse');
        
        if (navCollapse) {
            this.slideUp(navCollapse, this.config.slideAnimationDuration);
            this.state.mobileMenuOpen = false;
            
            document.body.classList.remove('mobile-menu-open');
            
            this.triggerEvent(EVENTS.NAVIGATION_MANAGER.MOBILE_MENU_CLOSED);
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
        
        this.triggerEvent(EVENTS.NAVIGATION_MANAGER.SIDE_MENU_OPENED);
    }

    closeSideMenu() {
        document.body.classList.remove('sidemenu-open');
        this.state.sideMenuOpen = false;
        
        this.triggerEvent(EVENTS.NAVIGATION_MANAGER.SIDE_MENU_CLOSED);
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

    triggerEvent(eventName, data = null) {
        const event = new CustomEvent(eventName, {
            detail: data
        });
        
        document.dispatchEvent(event);
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

window.NavigationManager = NavigationManager;
