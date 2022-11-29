function testeAlert() {
    var height = 0;
    jQuery('#owl-depoimento .client-inner p:first-child').each(function () {
        height_div = jQuery(this).height();
        if (height_div > height) {
            height = height_div;
        }
    });
    jQuery('#owl-depoimento .client-inner p:first-child').each(function () {
        jQuery(this).css('min-height', height);
    });
}

(function ($) {
    var autoplay = $(this).data('autoplay');
    $(window).load(function () {
        $('#owl-depoimento').owlCarousel({
            navText: ['<i class="fas fa-angle-left"></i>', '<i class="fas fa-angle-right"></i>'],
            autoplayHoverPause: true,
            autoplayTimeout: 4000,
            dots: false,
            loop: true,
            nav: true,
            onInitialized: testeAlert,
            responsive: {
                0: {
                    nav: false,
                    dots: true,
                    items: 1,
                },
                600: {
                    nav: false,
                    dots: true,
                    items: 1,
                },
                1000: {
                    items: 3,
                }
            }
        });
    });


})(jQuery);
