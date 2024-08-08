(function($) {
    wp.customize('gradient_color_1', function(value) {
        value.bind(function(newval) {
            document.documentElement.style.setProperty('--gradient-color-1', newval);
            updateGradient();
        });
    });
    wp.customize('gradient_color_2', function(value) {
        value.bind(function(newval) {
            document.documentElement.style.setProperty('--gradient-color-2', newval);
            updateGradient();
        });
    });

    function updateGradient() {
        var color1 = getComputedStyle(document.documentElement).getPropertyValue('--gradient-color-1').trim();
        var color2 = getComputedStyle(document.documentElement).getPropertyValue('--gradient-color-2').trim();
        var gradientStyle = `linear-gradient(45deg, ${color1}, ${color2})`;
        var filterStyle = `progid:DXImageTransform.Microsoft.gradient(startColorstr='${color1}', endColorstr='${color2}', GradientType=1)`;

        document.querySelectorAll('.linear-gradient').forEach(function(element) {
            element.style.background = gradientStyle;
            element.style.filter = filterStyle;
        });
    }

    // Initial call to apply the gradient on page load
    updateGradient();
})(jQuery);