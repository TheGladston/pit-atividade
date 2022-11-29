(function ($) {

    var LAE_Grid_Helper = function ($scope) {

        this._$grid = $scope.find('.lae-block').eq(0);

    };

    LAE_Grid_Helper.prototype = {

        _$grid: null,

        loadMoreOnScroll: function () {

            var $waypoint = null;

            var self = this;

            self._$grid.find('.lae-pagination a.lae-load-more').not('.lae-disabled').each(function () {

                var load_more_button = $(this);

                $waypoint = self._$grid.livemeshWaypoint(function (direction) {

                    load_more_button.click();

                    this.destroy();

                }, {
                    offset: 'bottom-in-view'
                });
            });

            return $waypoint;
        },

        setupInfiniteScroll: function () {

            var self = this;

            var $waypoint = self.loadMoreOnScroll();

            var innerBlockElem = self._$grid.find('.lae-block-inner')[0];

            var observer = new MutationObserver(function (mutations) {

                var hasUpdates = false;

                for (var index = 0; index < mutations.length; index++) {
                    var mutation = mutations[index];

                    if (mutation.type === 'childList' && mutation.addedNodes.length) {
                        hasUpdates = true;

                        break;
                    }
                }

                if (hasUpdates) {

                    // Before scheduling another scroll handler through waypoint, destroy existing unfired waypoint
                    // this occurs when user clicks on the filters without clicking the load more button
                    if ($waypoint)
                        $waypoint[0].destroy();

                    $waypoint = self.loadMoreOnScroll();

                }

            });

            // Observe block for any modifications to the DOM
            observer.observe(innerBlockElem, {attributes: false, childList: true, characterData: false, subtree: true});

        },
    };

    /* ----------------- Accordion ------------------ */

    var LAE_Accordion = function ($scope) {

        this.accordion = $scope.find('.lae-accordion').eq(0);
        // toggle elems
        this.panels = this.accordion.find('.lae-panel');

        if (this.accordion.data('toggle') == true)
            this.toggle = true;

        if (this.accordion.data('expanded') == true)
            this.expanded = true;

        // init events
        this._init();
    };

    LAE_Accordion.prototype = {

        accordion: null,
        panels: null,
        toggle: false,
        expanded: false,
        current: null,

        _init: function () {

            var self = this;

            if (this.expanded && this.toggle) {

                // Display all panels
                this.panels.each(function () {

                    var $panel = jQuery(this);

                    self._show($panel);

                });
            }

            this.panels.find('.lae-panel-title').click(function (event) {

                event.preventDefault();

                var $panel = jQuery(this).parent();

                // Do not disturb existing location URL if you are going to close an accordion panel that is currently open
                if (!$panel.hasClass('lae-active')) {

                    var target = $panel.attr("id");

                    history.pushState ? history.pushState(null, null, "#" + target) : window.location.hash = "#" + target;

                } else {
                    var target = $panel.attr("id");

                    if (window.location.hash == '#' + target)
                        history.pushState ? history.pushState(null, null, '#') : window.location.hash = "#";
                }

                self._show($panel);
            });
        },

        _show: function ($panel) {

            if (this.toggle) {
                if ($panel.hasClass('lae-active')) {
                    this._close($panel);
                } else {
                    this._open($panel);
                }
            } else {
                // if the $panel is already open, close it else open it after closing existing one
                if ($panel.hasClass('lae-active')) {
                    this._close($panel);
                    this.current = null;
                } else {
                    this._close(this.current);
                    this._open($panel);
                    this.current = $panel;
                }
            }

        },

        _open: function ($panel) {

            if ($panel !== null) {
                $panel.children('.lae-panel-content').slideDown(300);
                $panel.addClass('lae-active');
            }

        },

        _close: function ($panel) {

            if ($panel !== null) {
                $panel.children('.lae-panel-content').slideUp(300);
                $panel.removeClass('lae-active');
            }

        },
    };

    /* ------------------------------- Tabs ------------------------------------------- */

    /* Credit for tab styles - http://tympanus.net/codrops/2014/09/02/tab-styles-inspiration/ */

    var LAE_Tabs = function ($scope) {

        this.tabs = $scope.find('.lae-tabs').eq(0);

        this._init();
    };

    LAE_Tabs.prototype = {

        tabs: null,
        tabNavs: null,
        items: null,

        _init: function () {

            // tabs elems
            this.tabNavs = this.tabs.find('.lae-tab');

            // content items
            this.items = this.tabs.find('.lae-tab-pane');

            // show first tab item
            this._show(0);

            // init events
            this._initEvents();

            // make the tab responsive
            this._makeResponsive();

        },

        _show: function (index) {

            // Clear out existing tab
            this.tabNavs.removeClass('lae-active');
            this.items.removeClass('lae-active');

            this.tabNavs.eq(index).addClass('lae-active');
            this.items.eq(index).addClass('lae-active');

            this._triggerResize();

        },

        _initEvents: function ($panel) {

            var self = this;

            this.tabNavs.click(function (event) {

                event.preventDefault();

                var $anchor = jQuery(this).children('a').eq(0);

                var target = $anchor.attr('href').split('#').pop();

                self._show(self.tabNavs.index(jQuery(this)));

                history.pushState ? history.pushState(null, null, "#" + target) : window.location.hash = "#" + target;

            });
        },

        _makeResponsive: function () {

            var self = this;

            /* Trigger mobile layout based on an user chosen browser window resolution */
            var mediaQuery = window.matchMedia('(max-width: ' + self.tabs.data('mobile-width') + 'px)');
            if (mediaQuery.matches) {
                self.tabs.addClass('lae-mobile-layout');
            }
            mediaQuery.addListener(function (mediaQuery) {
                if (mediaQuery.matches)
                    self.tabs.addClass('lae-mobile-layout');
                else
                    self.tabs.removeClass('lae-mobile-layout');
            });

            /* Close/open the mobile menu when a tab is clicked and when menu button is clicked */
            this.tabNavs.click(function (event) {
                event.preventDefault();
                self.tabs.toggleClass('lae-mobile-open');
            });

            this.tabs.find('.lae-tab-mobile-menu').click(function (event) {
                event.preventDefault();
                self.tabs.toggleClass('lae-mobile-open');
            });
        },

        _triggerResize: function () {

            if (typeof (Event) === 'function') {
                // modern browsers
                window.dispatchEvent(new Event('resize'));
            } else {
                // for IE and other old browsers
                // causes deprecation warning on modern browsers
                var evt = window.document.createEvent('UIEvents');
                evt.initUIEvent('resize', true, false, window, 0);
                window.dispatchEvent(evt);
            }
        }
    };

    var WidgetLAEAccordionHandler = function ($scope, $) {

        new LAE_Accordion($scope);

    };

    var WidgetLAETabsHandler = function ($scope, $) {

        new LAE_Tabs($scope);

    };

    var WidgetLAEPortfolioHandler = function ($scope, $) {

        var $blockElem = $scope.find('.lae-block');

        var rtl = $blockElem.attr('dir') === 'rtl';

        if ($blockElem.find('.lae-module').length !== 0) {

            /* Enable isotope only for built-in grid and not for custom elementor grid */

            var currentBlockObj = laeGrids.getBlockObjById($blockElem.data('block-uid'));

            /* ----------- Init Isotope on Grid  -------------- */

            var layoutMode = currentBlockObj.settings['layout_mode'];

            // layout Isotope after all images have loaded
            var $blockElemInner = $blockElem.find('.lae-block-inner');

            $blockElemInner.isotope({
                itemSelector: '.lae-block-column',
                layoutMode: layoutMode,
                originLeft: !rtl,
                transitionDuration: '0.8s',
            });

            // layout Isotope after each image loads
            $blockElemInner.imagesLoaded().progress( function() {
                $blockElemInner.isotope('layout');
            });

        }
        else if ($blockElem.find('[data-elementor-type="livemesh_grid"]') !== 0) {
            /* Treat like the posts block grid for custom grid skin using elementor grid */
            var currentBlockObj = laeBlocks.getBlockObjById($blockElem.data('block-uid'));
        }
        else {
            return; // no items to display or load and hence don't continue
        }

        /* ----------- Reorganize Filters when device width changes -------------- */

        /* https://stackoverflow.com/questions/24460808/efficient-way-of-using-window-resize-or-other-method-to-fire-jquery-functions */
        var laeResizeTimeout;

        $(window).resize(function () {

            if (!!laeResizeTimeout) {
                clearTimeout(laeResizeTimeout);
            }

            laeResizeTimeout = setTimeout(function () {

                currentBlockObj.organizeFilters();

            }, 200);
        });

        /* -------------- Taxonomy Filter --------------- */

        $scope.find('.lae-taxonomy-filter .lae-filter-item a, .lae-block-filter .lae-block-filter-item a').on('click', function (e) {

            e.preventDefault();

            currentBlockObj.handleFilterAction($(this));

            return false;
        });

        var pagination = currentBlockObj.settings['pagination'];

        /* ------------------- Pagination ---------------------- */

        $scope.find('.lae-pagination a.lae-page-nav').on('click', function (e) {

            e.preventDefault();

            currentBlockObj.handlePageNavigation($(this));

        });


        /*---------------- Load More Button --------------------- */

        $scope.find('.lae-pagination a.lae-load-more').on('click', function (e) {

            e.preventDefault();

            currentBlockObj.handleLoadMore($(this));

        });


        /*---------------- Load On Scroll --------------------- */


        if (pagination == 'infinite_scroll') {

            var helper = new LAE_Grid_Helper($scope);

            helper.setupInfiniteScroll();

        }

        /* ---------------------- Init Lightbox ---------------------- */

        currentBlockObj.initLightbox($blockElem);

    };

    var WidgetLAEPostsBlockHandler = function ($scope, $) {

        var $blockElem = $scope.find('.lae-block');

        if ($blockElem.find('.lae-module').length === 0) {
            return; // no items to display or load and hence don't continue
        }

        var $blockElemInner = $blockElem.find('.lae-block-inner');

        var currentBlockObj = laeBlocks.getBlockObjById($blockElem.data('block-uid'));

        /* ----------- Reorganize Filters when device width changes -------------- */

        /* https://stackoverflow.com/questions/24460808/efficient-way-of-using-window-resize-or-other-method-to-fire-jquery-functions */
        var laeResizeTimeout;

        $(window).resize(function () {

            if (!!laeResizeTimeout) {
                clearTimeout(laeResizeTimeout);
            }

            laeResizeTimeout = setTimeout(function () {

                currentBlockObj.organizeFilters();

            }, 200);
        });

        /* -------------- Taxonomy Filter --------------- */

        $scope.find('.lae-taxonomy-filter .lae-filter-item a, .lae-block-filter .lae-block-filter-item a').on('click', function (e) {

            e.preventDefault();

            currentBlockObj.handleFilterAction($(this));

            return false;
        });

        var pagination = currentBlockObj.settings['pagination'];

        /* ------------------- Pagination ---------------------- */

        $scope.find('.lae-pagination a.lae-page-nav').on('click', function (e) {

            e.preventDefault();

            currentBlockObj.handlePageNavigation($(this));

        });

        /*---------------- Load More Button --------------------- */

        $scope.find('.lae-pagination a.lae-load-more').on('click', function (e) {

            e.preventDefault();

            currentBlockObj.handleLoadMore($(this));

        });

        /*---------------- Load On Scroll --------------------- */


        if (pagination == 'infinite_scroll') {

            var helper = new LAE_Grid_Helper($scope);

            helper.setupInfiniteScroll();

        }

        /* ---------------------- Init Lightbox ---------------------- */

        currentBlockObj.initLightbox($scope);

    };

    var WidgetLAEImageSliderHandler = function ($scope, $) {

        var slider_elem = $scope.find('.lae-image-slider').eq(0);

        var rtl = slider_elem.attr('dir') === 'rtl';

        var slider_type = slider_elem.data('slider-type');

        var settings = slider_elem.data('settings');

        var animation = settings['slide_animation'] || "slide";

        var direction = settings['direction'] || "horizontal";

        var slideshow_speed = parseInt(settings['slideshow_speed']) || 5000;

        var animation_speed = parseInt(settings['animation_speed']) || 600;

        var pause_on_action = settings['pause_on_action'];

        var pause_on_hover = settings['pause_on_hover'];

        var direction_nav = settings['direction_nav'];

        var control_nav = settings['control_nav'];

        var slideshow = settings['slideshow'];

        var thumbnail_nav = settings['thumbnail_nav'];

        var randomize = settings['randomize'];

        var loop = settings['loop'];

        if (slider_type == 'flex') {

            var carousel_id, slider_id;

            var $parent_slider = slider_elem.find('.lae-flexslider');

            if (thumbnail_nav) {

                control_nav = false; // disable control nav if thumbnail slider is desired
                randomize = false; // thumbnail slider does not work right when randomize is enabled

                carousel_id = $parent_slider.attr('data-carousel');
                slider_id = $parent_slider.attr('id');

                jQuery('#' + carousel_id).flexslider({
                    selector: ".lae-slides > .lae-slide",
                    namespace: "lae-flex-",
                    animation: "slide",
                    controlNav: false,
                    animationLoop: true,
                    slideshow: false,
                    itemWidth: 120,
                    itemMargin: 5,
                    rtl: rtl,
                    asNavFor: ('#' + slider_id)
                });
            }

            $parent_slider.flexslider({
                selector: ".lae-slides > .lae-slide",
                animation: animation,
                direction: direction,
                slideshowSpeed: slideshow_speed,
                animationSpeed: animation_speed,
                namespace: "lae-flex-",
                pauseOnAction: pause_on_action,
                pauseOnHover: pause_on_hover,
                controlNav: control_nav,
                directionNav: direction_nav,
                prevText: "Previous<span></span>",
                nextText: "Next<span></span>",
                smoothHeight: false,
                animationLoop: loop,
                slideshow: slideshow,
                easing: "swing",
                randomize: randomize,
                animationLoop: loop,
                rtl: rtl,
                sync: (carousel_id ? '#' + carousel_id : '')
            });
        } else if (slider_type == 'nivo') {

            // http://docs.dev7studios.com/article/13-nivo-slider-settings

            slider_elem.find('.nivoSlider').nivoSlider({
                effect: 'random',                 // Specify sets like: 'fold,fade,sliceDown'
                slices: 15,                       // For slice animations
                boxCols: 8,                       // For box animations
                boxRows: 4,                       // For box animations
                animSpeed: animation_speed,       // Slide transition speed
                pauseTime: slideshow_speed,       // How long each slide will show
                startSlide: 0,                    // Set starting Slide (0 index)
                directionNav: direction_nav,      // Next & Prev navigation
                controlNav: control_nav,          // 1,2,3... navigation
                controlNavThumbs: thumbnail_nav,  // Use thumbnails for Control Nav
                pauseOnHover: pause_on_hover,     // Stop animation while hovering
                manualAdvance: !slideshow,        // Force manual transitions
                prevText: 'Prev',                 // Prev directionNav text
                nextText: 'Next',                 // Next directionNav text
                randomStart: false,           // Start on a random slide
                beforeChange: function () {
                },       // Triggers before a slide transition
                afterChange: function () {
                },        // Triggers after a slide transition
                slideshowEnd: function () {
                },       // Triggers after all slides have been shown
                lastSlide: function () {
                },          // Triggers when last slide is shown
                afterLoad: function () {
                }           // Triggers when slider has loaded
            });
        } else if (slider_type == 'slick') {

            slider_elem.find('.lae-slickslider').slick({
                autoplay: slideshow, // Should the slider move by itself or only be triggered manually?
                speed: animation_speed, // How fast (in milliseconds) Slick Slider should animate between slides.
                autoplaySpeed: slideshow_speed, // If autoplay is set to true, how many milliseconds should pass between moving the slides?
                dots: control_nav, // Do you want to generate an automatic clickable navigation for each slide in your slider?
                arrows: direction_nav, // Do you want to add left/right arrows to your slider?
                fade: (animation == "fade"), // How should Slick Slider animate each slide?
                adaptiveHeight: false, // Should Slick Slider animate the height of the container to match the current slide's height?
                pauseOnHover: pause_on_hover, // Pause Autoplay on Hover
                slidesPerRow: 1, // With grid mode intialized via the rows option, this sets how many slides are in each grid row. dver
                slidesToShow: 1, // # of slides to show
                slidesToScroll: 1, // # of slides to scroll
                vertical: (direction == "vertical"), // Vertical slide mode
                infinite: loop, // Infinite loop sliding
                rtl: rtl,
                useTransform: true // Use CSS3 transforms

            });
        } else if (slider_type == 'responsive') {

            // http://responsiveslides.com/

            slider_elem.find('.rslides').responsiveSlides({
                auto: slideshow,             // Boolean: Animate automatically, true or false
                speed: animation_speed,            // Integer: Speed of the transition, in milliseconds
                timeout: slideshow_speed,          // Integer: Time between slide transitions, in milliseconds
                pager: control_nav,           // Boolean: Show pager, true or false
                nav: direction_nav,             // Boolean: Show navigation, true or false
                random: randomize,          // Boolean: Randomize the order of the slides, true or false
                pause: pause_on_hover,           // Boolean: Pause on hover, true or false
                pauseControls: false,    // Boolean: Pause when hovering controls, true or false
                prevText: "Previous",   // String: Text for the "previous" button
                nextText: "Next",       // String: Text for the "next" button
                maxwidth: "",           // Integer: Max-width of the slideshow, in pixels
                navContainer: "",       // Selector: Where controls should be appended to, default is after the 'ul'
                manualControls: "",     // Selector: Declare custom pager navigation
                namespace: "rslides",   // String: Change the default namespace used
                before: function () {
                },   // Function: Before callback
                after: function () {
                }     // Function: After callback
            });
        }


    };

    var WidgetLAEIconListHandler = function ($scope, $) {


        $scope.find('.lae-icon-list-item').powerTip({
            placement: 'n' // north-east tooltip position
        });


    };

    var WidgetLAEGalleryCarouselHandler = function ($scope, $) {

        /* ----------------- Lightbox Support ------------------ */

        $scope.fancybox({
            selector: '.lae-gallery-carousel-item:not(.slick-cloned) a.lae-lightbox-item:not(.elementor-clickable),.lae-gallery-carousel-item:not(.slick-cloned) a.lae-video-lightbox', // the selector for gallery item
            loop: true,
            buttons: [
                "zoom",
                "share",
                "slideShow",
                "fullScreen",
                //"download",
                "thumbs",
                "close"
            ],
            thumbs: {
                autoStart: false, // Display thumbnails on opening
                hideOnClose: true, // Hide thumbnail grid when closing animation starts
                axis: "x" // Vertical (y) or horizontal (x) scrolling
            },
            caption: function (instance, item) {

                var caption = $(this).attr('title') || '';

                var description = $(this).data('description') || '';

                if (description !== '') {
                    caption += '<div class="lae-fancybox-description">' + description + '</div>';
                }

                return caption;
            }
        });

    };


    var WidgetLAEGalleryHandler = function ($scope, $) {

        var $blockElem = $scope.find('.lae-block');

        var rtl = $blockElem.attr('dir') === 'rtl';

        if ($blockElem.find('.lae-module').length === 0) {
            return; // no items to display or load and hence don't continue
        }

        var currentBlockObj = laeGalleries.getBlockObjById($blockElem.data('block-uid'));

        /* ----------- Init Isotope on Grid  -------------- */

        var layoutMode = currentBlockObj.settings['layout_mode'];

        // layout Isotope after all images have loaded
        var $blockElemInner = $blockElem.find('.lae-block-inner');

        $blockElemInner.isotope({
            itemSelector: '.lae-block-column',
            layoutMode: layoutMode,
            originLeft: !rtl,
            transitionDuration: '0.8s',
            masonry: {
                columnWidth: '.lae-grid-sizer'
            }
        });

        $blockElemInner.imagesLoaded(function () {
            $blockElemInner.isotope('layout');
        });

        // Relayout on inline full screen video and back
        $(document).on('webkitfullscreenchange mozfullscreenchange fullscreenchange', function (e) {
            $blockElemInner.isotope('layout');
        });

        /* ----------- Reorganize Filters when device width changes -------------- */

        /* https://stackoverflow.com/questions/24460808/efficient-way-of-using-window-resize-or-other-method-to-fire-jquery-functions */
        var laeResizeTimeout;

        $(window).resize(function () {

            if (!!laeResizeTimeout) {
                clearTimeout(laeResizeTimeout);
            }

            laeResizeTimeout = setTimeout(function () {

                currentBlockObj.organizeFilters();

            }, 200);
        });

        /* -------------- Taxonomy Filter --------------- */

        $scope.find('.lae-taxonomy-filter .lae-filter-item a, .lae-block-filter .lae-block-filter-item a').on('click', function (e) {

            e.preventDefault();

            currentBlockObj.handleFilterAction($(this));

            return false;
        });

        var pagination = currentBlockObj.settings['pagination'];

        /* ------------------- Pagination ---------------------- */

        $scope.find('.lae-pagination a.lae-page-nav').on('click', function (e) {

            e.preventDefault();

            currentBlockObj.handlePageNavigation($(this));

        });

        /*---------------- Load More Button --------------------- */

        $scope.find('.lae-pagination a.lae-load-more').on('click', function (e) {

            e.preventDefault();

            currentBlockObj.handleLoadMore($(this));

        });

        /*---------------- Load On Scroll --------------------- */

        if (pagination == 'infinite_scroll') {

            var helper = new LAE_Grid_Helper($scope);

            helper.setupInfiniteScroll();

        }

        /* ---------------------- Init Lightbox ---------------------- */

        currentBlockObj.initLightbox($blockElem);

    };

    var WidgetLAETwitterGridHandler = function ($scope, $) {

        var $blockElem = $scope.find('.lae-block');

        var rtl = $blockElem.attr('dir') === 'rtl';

        if ($blockElem.find('.lae-module').length === 0) {
            return; // no items to display or load and hence don't continue
        }

        var currentBlockObj = laeTwitterGrids.getBlockObjById($blockElem.data('block-uid'));

        /* ----------- Init Isotope on Grid  -------------- */

        var layoutMode = currentBlockObj.settings['layout_mode'];

        // layout Isotope after all images have loaded
        var $blockElemInner = $blockElem.find('.lae-block-inner');

        $blockElemInner.isotope({
            itemSelector: '.lae-block-column',
            layoutMode: layoutMode,
            originLeft: !rtl,
            transitionDuration: '0.8s',
        });

        $blockElemInner.imagesLoaded(function () {
            $blockElemInner.isotope('layout');
        });

        // Relayout on inline full screen video and back
        $(document).on('webkitfullscreenchange mozfullscreenchange fullscreenchange', function (e) {
            $blockElemInner.isotope('layout');
        });


        /*---------------- Load More Button --------------------- */

        $scope.find('.lae-pagination a.lae-load-more').on('click', function (e) {

            e.preventDefault();

            currentBlockObj.handleLoadMore($(this));

        });

        /* ---------------------- Init Lightbox ---------------------- */

        currentBlockObj.initLightbox($blockElem);

    };



    var WidgetLAEYouTubeGridHandler = function ($scope, $) {

        var $blockElem = $scope.find('.lae-block');

        var rtl = $blockElem.attr('dir') === 'rtl';

        if ($blockElem.find('.lae-module').length === 0) {
            return; // no items to display or load and hence don't continue
        }

        var currentBlockObj = laeYouTubeGrids.getBlockObjById($blockElem.data('block-uid'));

        /* ----------- Init Isotope on Grid  -------------- */

        var layoutMode = currentBlockObj.settings['layout_mode'];

        // layout Isotope after all images have loaded
        var $blockElemInner = $blockElem.find('.lae-block-inner');

        $blockElemInner.isotope({
            itemSelector: '.lae-block-column',
            layoutMode: layoutMode,
            originLeft: !rtl,
            transitionDuration: '0.8s',
        });

        $blockElemInner.imagesLoaded(function () {
            $blockElemInner.isotope('layout');
        });

        // Relayout on inline full screen video and back
        $(document).on('webkitfullscreenchange mozfullscreenchange fullscreenchange', function (e) {
            $blockElemInner.isotope('layout');
        });


        /*---------------- Load More Button --------------------- */

        $scope.find('.lae-pagination a.lae-load-more').on('click', function (e) {

            e.preventDefault();

            currentBlockObj.handleLoadMore($(this));

        });

        /* ---------------------- Init Lightbox ---------------------- */

        currentBlockObj.initLightbox($blockElem);

        /* ----------------- Subscribe Button ---------------------- */

        // load Youtube subscribe button script
        if ($('.g-ytsubscribe').length) {
            tag = document.createElement('script');
            tag.src = 'https://apis.google.com/js/platform.js';
            tag.id  = 'lae-youtube-subscribe-api';
            script = document.getElementsByTagName('script')[0];
            script.parentNode.insertBefore(tag, script);
        }

    };

    var WidgetLAEVimeoGridHandler = function ($scope, $) {

        var $blockElem = $scope.find('.lae-block');

        var rtl = $blockElem.attr('dir') === 'rtl';

        if ($blockElem.find('.lae-module').length === 0) {
            return; // no items to display or load and hence don't continue
        }

        var currentBlockObj = laeVimeoGrids.getBlockObjById($blockElem.data('block-uid'));

        /* ----------- Init Isotope on Grid  -------------- */

        var layoutMode = currentBlockObj.settings['layout_mode'];

        // layout Isotope after all images have loaded
        var $blockElemInner = $blockElem.find('.lae-block-inner');

        $blockElemInner.isotope({
            itemSelector: '.lae-block-column',
            layoutMode: layoutMode,
            originLeft: !rtl,
            transitionDuration: '0.8s',
        });

        $blockElemInner.imagesLoaded(function () {
            $blockElemInner.isotope('layout');
        });

        // Relayout on inline full screen video and back
        $(document).on('webkitfullscreenchange mozfullscreenchange fullscreenchange', function (e) {
            $blockElemInner.isotope('layout');
        });


        /*---------------- Load More Button --------------------- */

        $scope.find('.lae-pagination a.lae-load-more').on('click', function (e) {

            e.preventDefault();

            currentBlockObj.handleLoadMore($(this));

        });

        /* ---------------------- Init Lightbox ---------------------- */

        currentBlockObj.initLightbox($blockElem);

    };

    var WidgetLAEInstagramGridHandler = function ($scope, $) {

        var $blockElem = $scope.find('.lae-block');

        var rtl = $blockElem.attr('dir') === 'rtl';

        if ($blockElem.find('.lae-module').length === 0) {
            return; // no items to display or load and hence don't continue
        }

        var currentBlockObj = laeInstagramGrids.getBlockObjById($blockElem.data('block-uid'));

        /* ----------- Init Isotope on Grid  -------------- */

        var layoutMode = currentBlockObj.settings['layout_mode'];

        // layout Isotope after all images have loaded
        var $blockElemInner = $blockElem.find('.lae-block-inner');

        $blockElemInner.isotope({
            itemSelector: '.lae-block-column',
            layoutMode: layoutMode,
            originLeft: !rtl,
            transitionDuration: '0.8s',
        });

        $blockElemInner.imagesLoaded(function () {
            $blockElemInner.isotope('layout');
        });

        // Relayout on inline full screen video and back
        $(document).on('webkitfullscreenchange mozfullscreenchange fullscreenchange', function (e) {
            $blockElemInner.isotope('layout');
        });


        /* ---------------------- Init Lightbox ---------------------- */

        currentBlockObj.initLightbox($blockElem);

    };

    var WidgetLAESliderHandler = function ($scope, $) {

        var slider_elem = $scope.find('.lae-slider').eq(0);

        var settings = slider_elem.data('settings');

        var $slider = slider_elem.find('.lae-flexslider');

        $slider.flexslider({
            selector: ".lae-slides > .lae-slide",
            animation: settings['slide_animation'],
            direction: settings['direction'],
            slideshowSpeed: settings['slideshow_speed'],
            animationSpeed: settings['animation_speed'],
            namespace: "lae-flex-",
            pauseOnAction: settings['pause_on_action'],
            pauseOnHover: settings['pause_on_hover'],
            controlNav: settings['control_nav'],
            directionNav: settings['direction_nav'],
            prevText: "Previous<span></span>",
            nextText: "Next<span></span>",
            smoothHeight: false,
            animationLoop: true,
            slideshow: settings['slideshow'],
            easing: "swing",
            randomize: settings['randomize'],
            animationLoop: settings['loop'],
            controlsContainer: "lae-slider"
        });


    };

    var WidgetLAECarouselHandler = function ($scope, $) {

        var carousel_elem = $scope.find('.lae-gallery-carousel, .lae-services-carousel').eq(0);

        if (carousel_elem.length > 0) {

            var rtl = carousel_elem.attr('dir') === 'rtl';

            var settings = carousel_elem.data('settings');

            var arrows = settings['arrows'];

            var dots = settings['dots'];

            var autoplay = settings['autoplay'];

            var autoplay_speed = parseInt(settings['autoplay_speed']) || 3000;

            var animation_speed = parseInt(settings['animation_speed']) || 300;

            var fade = settings['fade'];

            var pause_on_hover = settings['pause_on_hover'];

            var display_columns = parseInt(settings['display_columns']) || 4;

            var scroll_columns = parseInt(settings['scroll_columns']) || 4;

            var tablet_width = parseInt(settings['tablet_width']) || 800;

            var tablet_display_columns = parseInt(settings['tablet_display_columns']) || 2;

            var tablet_scroll_columns = parseInt(settings['tablet_scroll_columns']) || 2;

            var mobile_width = parseInt(settings['mobile_width']) || 480;

            var mobile_display_columns = parseInt(settings['mobile_display_columns']) || 1;

            var mobile_scroll_columns = parseInt(settings['mobile_scroll_columns']) || 1;

            carousel_elem.slick({
                arrows: arrows,
                dots: dots,
                infinite: true,
                autoplay: autoplay,
                autoplaySpeed: autoplay_speed,
                speed: animation_speed,
                fade: false,
                pauseOnHover: pause_on_hover,
                slidesToShow: display_columns,
                slidesToScroll: scroll_columns,
                rtl: rtl,
                responsive: [
                    {
                        breakpoint: tablet_width,
                        settings: {
                            slidesToShow: tablet_display_columns,
                            slidesToScroll: tablet_scroll_columns
                        }
                    },
                    {
                        breakpoint: mobile_width,
                        settings: {
                            slidesToShow: mobile_display_columns,
                            slidesToScroll: mobile_scroll_columns
                        }
                    }
                ]
            });
        }

    };

    // Make sure you run this code under Elementor..
    $(window).on('elementor/frontend/init', function () {

        elementorFrontend.hooks.addAction('frontend/element_ready/lae-accordion.default', WidgetLAEAccordionHandler);

        elementorFrontend.hooks.addAction('frontend/element_ready/lae-tabs.default', WidgetLAETabsHandler);

        elementorFrontend.hooks.addAction('frontend/element_ready/lae-portfolio.default', WidgetLAEPortfolioHandler);

        elementorFrontend.hooks.addAction('frontend/element_ready/lae-posts-block.default', WidgetLAEPostsBlockHandler);

        elementorFrontend.hooks.addAction('frontend/element_ready/lae-image-slider.default', WidgetLAEImageSliderHandler);

        elementorFrontend.hooks.addAction('frontend/element_ready/lae-icon-list.default', WidgetLAEIconListHandler);

        elementorFrontend.hooks.addAction('frontend/element_ready/lae-slider.default', WidgetLAESliderHandler);

        elementorFrontend.hooks.addAction('frontend/element_ready/lae-gallery-carousel.default', WidgetLAEGalleryCarouselHandler);

        elementorFrontend.hooks.addAction('frontend/element_ready/lae-services-carousel.default', WidgetLAECarouselHandler);

        elementorFrontend.hooks.addAction('frontend/element_ready/lae-gallery-carousel.default', WidgetLAECarouselHandler);

        elementorFrontend.hooks.addAction('frontend/element_ready/lae-gallery.default', WidgetLAEGalleryHandler);

        elementorFrontend.hooks.addAction('frontend/element_ready/lae-twitter-grid.default', WidgetLAETwitterGridHandler);

        elementorFrontend.hooks.addAction('frontend/element_ready/lae-youtube-grid.default', WidgetLAEYouTubeGridHandler);

        elementorFrontend.hooks.addAction('frontend/element_ready/lae-vimeo-grid.default', WidgetLAEVimeoGridHandler);

        elementorFrontend.hooks.addAction('frontend/element_ready/lae-instagram-grid.default', WidgetLAEInstagramGridHandler);

    });

})(jQuery);