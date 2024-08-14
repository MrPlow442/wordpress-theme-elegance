// build/frontend.js
(function ($, document) {
    $(document).ready(function() {
        $(window).on('load', function() {
            if ($('.work-list').length) {
                $('.work-list').owlCarousel({
                    loop: false,
                    nav: false,
                    dots: true,
                    items: 3,
                    autoplay: true,
                    smartSpeed: 700,
                    autoplayTimeout: 4000,
                    responsive: {
                        0: {
                            items: 1,
                            margin: 0
                        },
                        576: {
                            items: 2,
                            margin: 20
                        },
                        992: {
                            items: 3,
                            margin: 30
                        }
                    }
                });
            }
        });
    });
})(jQuery, document);
