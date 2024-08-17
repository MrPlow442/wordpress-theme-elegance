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
    changeBackgroundElement(element2, sourceUrl);
    setVisibility(element2, 'visible');
}

function preloadImages(urls) {
    urls.forEach(function(url) {
        if (url) {
            var img = new Image();
            img.src = url;
        }
    });
}
