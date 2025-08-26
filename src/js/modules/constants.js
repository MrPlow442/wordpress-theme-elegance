export const EVENTS = {
    SCROLL_NAVIGATOR: { 
        CONTAINER_VISIBILITY_CHANGE: 'scrollNavigator:containerVisibilityChange',
        /**
         * Triggered when a slide is about to leave the viewport
         * params: container, slideData, scrolledBy
         */
        SLIDE_LEAVE: 'scrollNavigator:onSlideLeave',
        /**
         * Triggered when a slide enters the viewport and becomes active
         * params: container, slideData, scrolledBy
         */
        SLIDE_IN_VIEW: 'scrollNavigator:slideInView'        
    }, 
    NAVIGATION_MANAGER: {
        MOBILE_MENU_OPENED: 'navigationManager:mobileMenuOpened',
        MOBILE_MENU_CLOSED: 'navigationManager:mobileMenuClosed',
        SIDE_MENU_OPENED: 'navigationManager:sideMenuOpened',
        SIDE_MENU_CLOSED: 'navigationManager:sideMenuClosed'
    }
};

export const MODULES = {
    SCROLL_NAVIGATOR: 'ScrollNavigator',
    NAVIGATION_MANAGER: 'NavigationManager',
    BACKGROUND_MANAGER: 'BackgroundManager',
    ANIMATION_MANAGER: 'AnimationManager'
}

export const SCROLL_NAVIGATOR = {
    DIRECTION: {
        HORIZONTAL: 'horizontal',
        VERTICAL: 'vertical'
    },
    SCROLLED_BY: {
        PROGRAM: 'program',
        USER: 'user'
    },
    NAV_POSITION: {
        TOP: 'top',
        BOTTOM: 'bottom',
        LEFT: 'left',
        RIGHT: 'right'
    },
    MAIN_CONTAINER_ID: 'main'
}