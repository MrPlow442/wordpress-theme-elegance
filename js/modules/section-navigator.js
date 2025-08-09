/**
 * Section Navigation Module - Handles scroll-snap section navigation
 * Replaces fullpage.js functionality with modern CSS scroll-snap
 * 
 * @package Elegance
 * @version 2.0.0
 */

class SectionNavigator extends EleganceModule {    
    constructor(config) {
        super('SectionNavigator', config);
        this.sections = [];
        this.currentSectionIndex = 0;
        this.currentSection = null;
        this.isScrolling = false;
        this.scrollTimeout = null;
        
        this.config = {
            sectionSelector: '.snap-section',
            scrollBehavior: 'smooth',
            scrollDetectionDelay: 100,
            scrollAnimationDuration: 600
        };

        this.handleScroll = this.handleScroll.bind(this);
        this.handleHashChange = this.handleHashChange.bind(this);
    }

    init(globalConfig = {}) {
        this.config = { ...this.config, ...globalConfig.sectionNavigator };
        
        this.findSections();
        
        if (this.sections.length === 0) {
            logger.warn('SectionNavigator: No sections found');
            return;
        }

        this.setInitialSection();
        this.bindEvents();
        
        logger.log(`SectionNavigator: Initialized with ${this.sections.length} sections`);
    }

    findSections() {
        this.sections = Array.from(document.querySelectorAll(this.config.sectionSelector));
        logger.log('SectionNavigator: Found sections:', this.sections.map(s => s.id || s.dataset.section));
    }

    setInitialSection() {
        let initialIndex = 0;
        
        if (window.location.hash) {
            const hashSection = window.location.hash.substring(1);
            const foundIndex = this.sections.findIndex(section => 
                section.id === hashSection || section.dataset.section === hashSection
            );
            
            if (foundIndex !== -1) {
                initialIndex = foundIndex;
            }
        }
        
        this.updateCurrentSection(initialIndex, false);
    }

    bindEvents() {
        window.addEventListener('scroll', this.handleScroll);
        window.addEventListener('hashchange', this.handleHashChange);
                
        this.bindNavigationClicks();
        this.bindNextSectionButton();
    }
    
    bindNavigationClicks() {
        const navLinks = document.querySelectorAll('.navigation-menu > li > a, .navbar-nav li > a');
        
        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            
            if (href && href.startsWith('#')) {
                link.addEventListener('click', (event) => {
                    event.preventDefault();
                    
                    const sectionId = href.substring(1);
                    this.goToSection(sectionId);
                                        
                    this.closeMobileNav();
                });
            }
        });
    }
    
    bindNextSectionButton() {
        const nextButton = document.querySelector('.next-section');
        
        if (nextButton) {
            nextButton.addEventListener('click', (event) => {
                event.preventDefault();
                this.nextSection();
            });
        }
    }

    handleScroll() {
        if (this.isScrolling) {
            return;
        }
        
        clearTimeout(this.scrollTimeout);
        
        this.scrollTimeout = setTimeout(() => {
            this.detectSectionChange();
        }, this.config.scrollDetectionDelay);
    }

    handleHashChange() {
        if (this.isScrolling) {
            return;
        }
        
        const hash = window.location.hash.substring(1);
        
        if (hash) {
            this.goToSection(hash);
        }
    }

    detectSectionChange() {
        const scrollTop = window.pageYOffset;
        const viewportHeight = window.innerHeight;
        const viewportCenter = scrollTop + (viewportHeight / 2);
        
        let newSectionIndex = 0;
                
        for (let i = 0; i < this.sections.length; i++) {
            const section = this.sections[i];
            const rect = section.getBoundingClientRect();
            const sectionTop = scrollTop + rect.top;
            const sectionBottom = sectionTop + rect.height;
            
            if (viewportCenter >= sectionTop && viewportCenter <= sectionBottom) {
                newSectionIndex = i;
                break;
            }
        }
                
        if (newSectionIndex !== this.currentSectionIndex) {
            this.updateCurrentSection(newSectionIndex, true);
        }
    }

    updateCurrentSection(newIndex, triggerEvents = true) {
        const previousIndex = this.currentSectionIndex;
        const previousSection = this.currentSection;
        
        this.currentSectionIndex = newIndex;
        this.currentSection = this.sections[newIndex];
        
        const sectionId = this.getSectionId(this.currentSection);
        
        if (triggerEvents && previousIndex !== newIndex) {
            this.onSectionChange(previousSection, this.currentSection, sectionId);
        }
        
        this.updateNavigation(sectionId);
        this.updateUrlHash(sectionId);
    }

    onSectionChange(previousSection, currentSection, sectionId) {
        logger.log('SectionNavigator: Section changed to', sectionId);
        
        // Trigger background change event
        this.triggerEvent(EVENTS.SECTION_NAVIGATOR.SECTION_CHANGE, {
            previousSection,
            currentSection,
            sectionId,
            index: this.currentSectionIndex
        });
    }

    goToSection(sectionId) {
        const sectionIndex = this.sections.findIndex(section => 
            section.id === sectionId || section.dataset.section === sectionId
        );
        
        if (sectionIndex === -1) {
            logger.warn(`SectionNavigator: Section '${sectionId}' not found`);
            return;
        }
        
        this.goToSectionByIndex(sectionIndex);
    }

    goToSectionByIndex(index) {
        if (index < 0 || index >= this.sections.length) {
            return;
        }
        
        const targetSection = this.sections[index];
                
        this.isScrolling = true;
                
        targetSection.scrollIntoView({
            behavior: this.config.scrollBehavior,
            block: 'start'
        });
                
        setTimeout(() => {
            this.updateCurrentSection(index, true);
            this.isScrolling = false;
        }, this.config.scrollAnimationDuration);
    }

    nextSection() {
        if (this.currentSectionIndex < this.sections.length - 1) {
            this.goToSectionByIndex(this.currentSectionIndex + 1);
        }
    }

    previousSection() {
        if (this.currentSectionIndex > 0) {
            this.goToSectionByIndex(this.currentSectionIndex - 1);
        }
    }

    updateNavigation(sectionId) {        
        document.querySelectorAll('.navbar-nav li').forEach(item => {
            item.classList.remove('active');
        });
        
        const desktopActiveLink = document.querySelector(`.navbar-nav a[href="#${sectionId}"]`);
        if (desktopActiveLink) {
            desktopActiveLink.parentElement.classList.add('active');
        }
        
        document.querySelectorAll('.navigation-menu li').forEach(item => {
            item.classList.remove('active');
        });
        
        const mobileActiveLink = document.querySelector(`.navigation-menu a[href="#${sectionId}"]`);
        if (mobileActiveLink) {
            mobileActiveLink.parentElement.classList.add('active');
        }
    }

    updateUrlHash(sectionId) {
        const newHash = `#${sectionId}`;
        
        if (window.location.hash === newHash) {
            return;
        }
        
        const url = window.location.pathname + window.location.search + newHash;
        history.replaceState(null, '', url);
    }

    closeMobileNav() {
        const navCollapse = document.querySelector('.navbar-collapse');
        if (navCollapse && navCollapse.style.display === 'block') {
            navCollapse.style.display = 'none';
        }
    }

    getSectionId(section) {
        return section.id || section.dataset.section || `section-${this.sections.indexOf(section)}`;
    }

    triggerEvent(eventName, data) {
        const event = new CustomEvent(eventName, {
            detail: data
        });
        
        document.dispatchEvent(event);
    }

    onNavbarToggle(data) {
        const navCollapse = document.querySelector('.navbar-collapse');
        if (navCollapse) {
            const isVisible = navCollapse.style.display === 'block';
            navCollapse.style.display = isVisible ? 'none' : 'block';
        }
    }

    destroy() {
        window.removeEventListener('scroll', this.handleScroll);
        window.removeEventListener('hashchange', this.handleHashChange);
        clearTimeout(this.scrollTimeout);
        
        logger.log('SectionNavigator: Destroyed');
    }
}

window.SectionNavigator = SectionNavigator;

