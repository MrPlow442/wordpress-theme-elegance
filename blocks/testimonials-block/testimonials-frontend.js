// build/frontend.js
(function ($, document) {
    $(document).ready(function() {
        $(window).on('load', function() {
            if ($('.testimonials-slider').length) {
                $('.testimonials-slider').owlCarousel({
                    loop: true,
                    nav: false,
                    dots: true,
                    items: 1,
                    margin: 30,
                    autoplay: true,
                    smartSpeed: 700,
                    autoplayTimeout: 30000,
                    responsive: {
                        0: {
                            items: 1,
                            margin: 0
                        },
                        768: {
                            items: 1
                        }
                    }
                });
            }
        });
    });
})(jQuery, document);