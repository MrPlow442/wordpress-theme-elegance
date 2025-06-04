function setVisibility(element, visibility) {
    if (!element || !visibility || ['hidden', 'visible'].indexOf(visibility) === -1) {
        return;
    }
    if (element.classList.contains(visibility)) {
        return;
    }
    let inverse = visibility === 'hidden' ? 'visible' : 'hidden';
    if (element.classList.contains(inverse)) {
        element.classList.remove(inverse);
        element.classList.add(visibility);
    }
}

function isVideo(element) {
    return element.tagName.toLowerCase() === 'video';
}

function isImage(element) {
    return element.tagName.toLowerCase() === 'img';
}

function getSource(element) {
    if (isVideo(element)) {
        return element.querySelector('source').src;
    } else {
        return element.src;
    }
}

function setSource(element, sourceUrl) {
    if (isVideo(element)) {
        element.querySelector('source').src = sourceUrl;
    } else {
        element.src = sourceUrl;
    }
}

function changeBackgroundElement(element, url) {
    if (!element || !url || getSource(element) === url) {
        return;
    }
    element.style.opacity = 0;

    // Wait for the transition to complete before changing the image source
    setTimeout(function() {
        setSource(element, url);
        element.style.opacity = 1;
        setVisibility(element, 'visible');
    }, 200); // The duration should match the CSS transition duration
}

function swapElementDisplay(element1, element2, sourceUrl) {
    setVisibility(element1, 'hidden');
    setElementDisplay(element2, sourceUrl);
}

function setElementDisplay(element, sourceUrl) {
    changeBackgroundElement(element, sourceUrl);
    setVisibility(element, 'visible');
}

function preloadImages(urls) {
    urls.forEach(function(url) {
        if (url) {
            var img = new Image();
            img.src = url;
        }
    });
}


function showDefault(config, elements) {
    if (!config.defaultVideoUrl && !config.defaultImageUrl) {
        return;
    }

    if (config.defaultVideoUrl) {                        
        swapElementDisplay(elements.backgroundImageElement, elements.backgroundVideoElement, config.defaultVideoUrl);                    
    } else {                        
        swapElementDisplay(elements.backgroundVideoElement, elements.backgroundImageElement, config.defaultImageUrl);
    }
}

function showImage(elements, imageUrl) {
    if (!imageUrl) {
        return;
    }

    swapElementDisplay(elements.backgroundVideoElement, elements.backgroundImageElement, imageUrl);
}

function initializeFullpage(config) {
    const elements = {
        backgroundVideoElement: document.getElementById(config.videoElementId),
        backgroundImageElement: document.getElementById(config.imageElementId)
    };

    const pageImages = config.pageInfo
        .filter(i => i.hasThumbnail)
        .map(i => i.thumbnail);

    preloadImages(pageImages);

    showDefault(config, elements);

    if (document.querySelector('.fullpage-default')) {
        var myFullpage = new fullpage('.fullpage-default', {
            licenseKey: 'C7F41B00-5E824594-9A5EFB99-B556A3D5',
            anchors: config.anchorsJson,
            menu: '#nav',
            lazyLoad: true,
            navigation: true,
            slidesNavigation: true,
            navigationPosition: 'right',
            scrollOverflow: true,
            scrollOverflowReset: true,
            responsiveWidth: 768,
            responsiveHeight: 600,
            responsiveSlides: true,
            onLeave: function(origin, destination, direction) {
                var section = destination.item;
                var sectionName = section.getAttribute('data-section');

                const matchingPage = config.pageInfo.find(i => i.name === sectionName);
                if (matchingPage && matchingPage.hasThumbnail) {
                    showImage(elements, matchingPage.thumbnail);
                } else {
                    showDefault(config, elements);
                }
            }
        });
    }    
}

function initializeBlogPage(config) {
    const elements = {        
        backgroundImageElement: document.getElementById(config.imageElementId)
    };

    showDefault(config, elements);
}