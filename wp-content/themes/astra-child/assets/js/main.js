function ajusteMobile() {

    if (jQuery(window).width() > 992) {

        var height = 0;
        jQuery('ul.products .woocommerce-loop-product__title').each(function () {
            height_div = jQuery(this).height();
            if (height_div > height) {
                height = height_div;
            }
        });
        jQuery('ul.products .woocommerce-loop-product__title').each(function () {
            jQuery(this).css('min-height', height);
        });

    } else {

    }

    if (jQuery('ul.products')) {
        if (jQuery(window).width() > 991) {
            jQuery('.archive ul.products').attr('class', 'products columns-3');
        } else {
            jQuery('.archive ul.products').attr('class', 'products columns-2');
        }
    }

}
;

function owlServicosHome() {
    var $owl = jQuery('#owl-services-home');
    if ($owl) {
        $owl.owlCarousel({
            dots: false,
            nav: false,
            margin: 0,

            responsive: {
                0: {
                    dots: true,
                    items: 1,
                },
                480: {
                    dots: true,
                    items: 1
                },
                768: {
                    dots: true,
                    items: 1
                },
                991: {
                    dots: true,
                    items: 1
                },
                1200: {
                    items: 4
                },
            }
        });
    }
}
;
function owlRelatedProducts() {
    var $owl = jQuery("#owl-related");
    if ($owl) {
        $owl.owlCarousel({
            navText: ['<i class="fas fa-angle-left"></i>', '<i class="fas fa-angle-right"></i>'],
            autoHeight: true,
            autoPlay: false,
            dots: false,
            loop: true,
            margin: 30,
            nav: true,
            items: 4,
            responsive: {
                0: {
                    dots: true,
                    nav: false,
                    items: 2,
                },
                480: {
                    dots: true,
                    nav: false,
                    items: 2,
                },
                768: {
                    dots: true,
                    nav: false,
                    items: 2,
                },
                1200: {
                    items: 4,
                }
            }
        });
    }
}
;

function replaceStoreUrl() {
    try {
        let store_url_s = ajax_object.store_url.replace("https://", '');
        let store_url = store_url_s.replace("http://", '');

        jQuery('a').each(function (i, elem) {
            let url = jQuery(elem).attr('href');
            let nova_url = url.replace(/%store_url%/g, store_url);
            jQuery(elem).attr('href', nova_url);
        });
    } catch (e) {
        console.log('Erro função replaceStoreUrl()');
    }
}
;


jQuery(document).ready(function () {
    replaceStoreUrl();
    owlRelatedProducts();
    owlServicosHome();
    ajusteMobile();
});

jQuery(window).resize(function () {
    owlRelatedProducts();
    ajusteMobile();
}
);