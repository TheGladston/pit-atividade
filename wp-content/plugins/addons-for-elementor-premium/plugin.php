<?php

namespace LivemeshAddons;

// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;


if (!class_exists('Livemesh_Elementor_Addons')) :

    /**
     * Main Livemesh_Elementor_Addons Class
     *
     */
    final class Livemesh_Elementor_Addons {

        /** Singleton *************************************************************/

        private static $instance;

        /**
         * Main Livemesh_Elementor_Addons Instance
         *
         * Insures that only one instance of Livemesh_Elementor_Addons exists in memory at any one
         * time. Also prevents needing to define globals all over the place.
         */
        public static function instance() {

            if (!isset(self::$instance) && !(self::$instance instanceof Livemesh_Elementor_Addons)) {

                self::$instance = new Livemesh_Elementor_Addons;

                self::$instance->setup_debug_constants();

                self::$instance->includes();

                self::$instance->hooks();

                self::$instance->template_hooks();

            }
            return self::$instance;
        }


        /**
         * Throw error on object clone
         *
         * The whole idea of the singleton design pattern is that there is a single
         * object therefore, we don't want the object to be cloned.
         */
        public function __clone() {
            // Cloning instances of the class is forbidden
            _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'livemesh-el-addons'), '4.6');
        }

        /**
         * Disable unserializing of the class
         *
         */
        public function __wakeup() {
            // Unserializing instances of the class is forbidden
            _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'livemesh-el-addons'), '4.6');
        }

        private function setup_debug_constants() {

            $enable_debug = false;

            $settings = get_option('lae_settings');

            if ($settings && isset($settings['lae_enable_debug']) && $settings['lae_enable_debug'] == "true")
                $enable_debug = true;

            // Enable script debugging
            if (!defined('LAE_SCRIPT_DEBUG')) {
                define('LAE_SCRIPT_DEBUG', $enable_debug);
            }

            // Minified JS file name suffix
            if (!defined('LAE_JS_SUFFIX')) {
                if ($enable_debug)
                    define('LAE_JS_SUFFIX', '');
                else
                    define('LAE_JS_SUFFIX', '.min');
            }
        }

        /**
         * Include required files
         *
         */
        private function includes() {


            require_once LAE_PLUGIN_DIR . 'includes/helper-functions.php';
            require_once LAE_PLUGIN_DIR . 'includes/query-functions.php';

            if (lae_fs()->can_use_premium_code__premium_only()) {

                require_once LAE_PLUGIN_DIR . 'includes/blocks/blocks-init.php';
            }


            if (is_admin()) {
                require_once LAE_PLUGIN_DIR . 'admin/admin-init.php';
            }

            if (!function_exists('is_plugin_active')) {
                include_once(ABSPATH . 'wp-admin/includes/plugin.php');
            }

            /* Ensure WPML String Translation plugin is active */
            if (is_plugin_active('wpml-string-translation/plugin.php'))
                require_once LAE_PLUGIN_DIR . 'i18n/wpml-compatibility-init.php';

            /* Initialize the theme builder templates - Requires elementor pro plugin */
            if (is_plugin_active('elementor-pro/elementor-pro.php')) {
                require_once LAE_PLUGIN_DIR . 'includes/theme-builder/init.php';
            }

        }

        /**
         * Load Plugin Text Domain
         *
         * Looks for the plugin translation files in certain directories and loads
         * them to allow the plugin to be localised
         */
        public function load_plugin_textdomain() {

            $lang_dir = apply_filters('lae_el_addons_lang_dir', trailingslashit(LAE_PLUGIN_DIR . 'languages'));

            // Traditional WordPress plugin locale filter
            $locale = apply_filters('plugin_locale', get_locale(), 'livemesh-el-addons');
            $mofile = sprintf('%1$s-%2$s.mo', 'livemesh-el-addons', $locale);

            // Setup paths to current locale file
            $mofile_local = $lang_dir . $mofile;

            if (file_exists($mofile_local)) {
                // Look in the /wp-content/plugins/livemesh-el-addons/languages/ folder
                load_textdomain('livemesh-el-addons', $mofile_local);
            }
            else {
                // Load the default language files
                load_plugin_textdomain('livemesh-el-addons', false, $lang_dir);
            }

            return false;
        }

        /**
         * Setup the default hooks and actions
         */
        private function hooks() {

            add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));

            // Initialize string translation of plugin elements after String Translation plugin is loaded
            add_action('wpml_st_loaded', array($this, 'init_wpml_compatibility'));

            // Filter to exclude images from lazy load using https://wordpress.org/plugins/sg-cachepress/
            add_filter('sgo_lazy_load_exclude_classes', array($this, 'exclude_images_with_specific_class'));

            add_action('elementor/widgets/widgets_registered', array($this, 'include_widgets'));

            add_action('elementor/editor/after_enqueue_styles', array($this, 'enqueue_editor_styles'), 10);

            add_action('elementor/frontend/after_register_scripts', array($this, 'register_frontend_scripts'), 10);

            add_action('elementor/frontend/after_register_styles', array($this, 'register_frontend_styles'), 10);

            add_action('elementor/frontend/after_enqueue_styles', array($this, 'enqueue_frontend_styles'), 10);

            add_action('elementor/init', array($this, 'add_elementor_category'));
        }


        function exclude_images_with_specific_class($classes) {
            // Add the class name that you want to exclude from lazy load.
            $classes[] = 'skip-lazy';

            return $classes;
        }

        function init_wpml_compatibility() {

            // Run WPML String Translation dependent actions
            new \LivemeshAddons\i18n\LAE_WPML_Compatibility_Init();

        }

        private function template_hooks() {

            if (lae_fs()->can_use_premium_code__premium_only()) {
                $addons = array('clients', 'carousel', 'heading', 'odometers', 'piecharts', 'posts_grid', 'posts_carousel', 'pricing_table', 'services', 'stats_bars', 'team_members', 'testimonials', 'testimonials_slider', 'accordion', 'button', 'faq', 'features', 'gallery', 'gallery_carousel', 'icon_list', 'image_slider', 'posts_block', 'services_carousel', 'slider', 'tabs');
            }
            else {
                $addons = array('clients', 'carousel', 'heading', 'odometers', 'piecharts', 'posts_grid', 'posts_carousel', 'pricing_table', 'services', 'stats_bars', 'team_members', 'testimonials', 'testimonials_slider');
            }

            foreach ($addons as $addon) {

                add_filter('lae_' . $addon . '_output', function ($default_output, $settings) use ($addon) {

                    // Replace underscores with dashes for template file names
                    $template_name = str_replace('_', '-', $addon);

                    $output = lae_get_template_part($template_name, $settings);

                    if ($output !== null)
                        return $output;

                    return $default_output;
                }, 10, 2);
            };
        }

        public function add_elementor_category() {

            \Elementor\Plugin::instance()->elements_manager->add_category(
                'livemesh-addons',
                array(
                    'title' => __('Livemesh Addons', 'livemesh-el-addons'),
                    'icon' => 'fa fa-plug',
                ),
                1);
        }

        public function localize_array($array = array()) {

            // If WooCommerce is activated
            if (lae_fs()->can_use_premium_code__premium_only()) {

                if (class_exists('WooCommerce')) {
                    $array['is_cart'] = is_cart();
                    $array['cart_url'] = apply_filters('woocommerce_add_to_cart_redirect', wc_get_cart_url());
                    $array['cart_redirect_after_add'] = get_option('woocommerce_cart_redirect_after_add');

                    // Add the View Cart here to translate for quick view
                    $array['view_cart'] = esc_html__('View cart', 'livemesh-el-addons');

                    // Grouped product button text in the quick view
                    $array['grouped_text'] = esc_attr__('View products', 'livemesh-el-addons');

                }

            }

            $array['custom_css'] = lae_get_option('lae_custom_css', '');

            return $array;
        }

        /**
         * Load Frontend Scripts
         *
         */
        public function register_frontend_scripts() {

            // Use minified libraries if LAE_SCRIPT_DEBUG is turned off
            $suffix = (defined('LAE_SCRIPT_DEBUG') && LAE_SCRIPT_DEBUG) ? '' : '.min';

            wp_register_script('lae-waypoints', LAE_PLUGIN_URL . 'assets/js/jquery.waypoints' . $suffix . '.js', array('jquery'), LAE_VERSION, true);

            wp_register_script('isotope.pkgd', LAE_PLUGIN_URL . 'assets/js/isotope.pkgd' . $suffix . '.js', array('jquery'), LAE_VERSION, true);

            wp_register_script('imagesloaded.pkgd', LAE_PLUGIN_URL . 'assets/js/imagesloaded.pkgd' . $suffix . '.js', array('jquery'), LAE_VERSION, true);

            wp_register_script('jquery-stats', LAE_PLUGIN_URL . 'assets/js/jquery.stats' . $suffix . '.js', array('jquery'), LAE_VERSION, true);

            wp_register_script('jquery-flexslider', LAE_PLUGIN_URL . 'assets/js/jquery.flexslider' . $suffix . '.js', array('jquery'), LAE_VERSION, true);

            wp_register_script('lae-frontend-scripts', LAE_PLUGIN_URL . 'assets/js/lae-frontend' . $suffix . '.js', array('jquery'), LAE_VERSION, true);

            wp_register_script('lae-widgets-scripts', LAE_PLUGIN_URL . 'assets/js/lae-widgets' . $suffix . '.js', array('lae-waypoints'), LAE_VERSION, true);

            if (lae_fs()->can_use_premium_code__premium_only()) {

                wp_register_script('jquery-fancybox', LAE_PLUGIN_URL . 'assets/js/premium/jquery.fancybox' . $suffix . '.js', array('jquery'), LAE_VERSION, true);

                wp_register_script('jquery-nivo', LAE_PLUGIN_URL . 'assets/js/premium/jquery.nivo.slider' . $suffix . '.js', array('jquery'), LAE_VERSION, true);

                wp_register_script('responsiveslides', LAE_PLUGIN_URL . 'assets/js/premium/responsiveslides' . $suffix . '.js', array('jquery'), LAE_VERSION, true);

                wp_register_script('jquery-powertip', LAE_PLUGIN_URL . 'assets/js/premium/jquery.powertip' . $suffix . '.js', array('jquery'), LAE_VERSION, true);

                wp_register_script('lae-frontend-premium-scripts', LAE_PLUGIN_URL . 'assets/js/premium/lae-frontend' . $suffix . '.js', array('jquery'), LAE_VERSION, true);

                wp_register_script('lae-blocks-scripts', LAE_PLUGIN_URL . 'assets/js/premium/lae-blocks' . $suffix . '.js', array('jquery'), LAE_VERSION, true);

                wp_register_script('lae-widgets-premium-scripts', LAE_PLUGIN_URL . 'assets/js/premium/lae-widgets' . $suffix . '.js', array('jquery'), LAE_VERSION, true);

                if (class_exists('WooCommerce')) {
                    wp_register_script('lae-wc-quick-view', LAE_PLUGIN_URL . 'assets/js/premium/wc-quick-view' . $suffix . '.js', array('jquery'), LAE_VERSION, true);
                }

                /* Do not attach to widget scripts since they are enqueued really late for some reason */
                $ajax_params = array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'block_nonce' => wp_create_nonce('lae-block-nonce')
                );
                wp_localize_script('lae-frontend-scripts', 'lae_ajax_object', $ajax_params);

            }

            $array = $this->localize_array();

            wp_localize_script('lae-frontend-scripts', 'lae_js_vars', $array);

        }

        /**
         * Load Frontend Styles
         *
         */
        public function enqueue_editor_styles() {

            wp_enqueue_style('lae-icomoon-styles', LAE_PLUGIN_URL . 'assets/css/icomoon.css', array(), LAE_VERSION);
        }

        /**
         * Load Frontend Styles
         *
         */
        public function register_frontend_styles() {

            wp_register_style('lae-animate-styles', LAE_PLUGIN_URL . 'assets/css/animate.css', array(), LAE_VERSION);

            wp_register_style('lae-sliders-styles', LAE_PLUGIN_URL . 'assets/css/sliders.css', array(), LAE_VERSION);

            wp_register_style('lae-icomoon-styles', LAE_PLUGIN_URL . 'assets/css/icomoon.css', array(), LAE_VERSION);

            wp_register_style('lae-frontend-styles', LAE_PLUGIN_URL . 'assets/css/lae-frontend.css', array(), LAE_VERSION);

            if (lae_fs()->can_use_premium_code__premium_only()) {

                wp_register_style('fancybox', LAE_PLUGIN_URL . 'assets/css/premium/jquery.fancybox.css', array(), LAE_VERSION);

                wp_register_style('lae-premium-sliders-styles', LAE_PLUGIN_URL . 'assets/css/premium/sliders.css', array(), LAE_VERSION);

                wp_register_style('lae-premium-frontend-styles', LAE_PLUGIN_URL . 'assets/css/premium/lae-frontend.css', array('lae-frontend-styles'), LAE_VERSION);

                wp_register_style('lae-blocks-styles', LAE_PLUGIN_URL . 'assets/css/premium/lae-blocks.css', array('lae-premium-frontend-styles'), LAE_VERSION);

                wp_register_style('lae-widgets-styles', LAE_PLUGIN_URL . 'assets/css/lae-widgets.css', array('lae-blocks-styles'), LAE_VERSION);

                wp_register_style('lae-premium-widgets-styles', LAE_PLUGIN_URL . 'assets/css/premium/lae-widgets.css', array('lae-widgets-styles'), LAE_VERSION);

            }
            else {

                wp_register_style('lae-widgets-styles', LAE_PLUGIN_URL . 'assets/css/lae-widgets.css', array('lae-frontend-styles'), LAE_VERSION);
            }

        }

        /**
         * Load Frontend Styles
         *
         */
        public function enqueue_frontend_styles() {

            wp_enqueue_style('lae-animate-styles');

            wp_enqueue_style('lae-sliders-styles');

            wp_enqueue_style('lae-icomoon-styles');

            if (lae_fs()->can_use_premium_code__premium_only()) {

                wp_enqueue_style('fancybox');

                wp_enqueue_style('lae-premium-sliders-styles');
            }

            wp_enqueue_style('lae-frontend-styles');

            wp_enqueue_style('lae-widgets-styles');

            if (lae_fs()->can_use_premium_code__premium_only()) {

                wp_enqueue_style('lae-premium-frontend-styles');

                wp_enqueue_style('lae-blocks-styles');

                wp_enqueue_style('lae-premium-widgets-styles');
            }
        }

        /**
         * Include required files
         *
         */
        public function include_widgets() {

            $widgets_manager = \Elementor\Plugin::instance()->widgets_manager;

            /* Load Elementor Addon Elements */

            $deactivate_element_team_members = lae_get_option('lae_deactivate_element_team', false);
            if (!$deactivate_element_team_members) {

                require_once LAE_ADDONS_DIR . 'team-members.php';

                $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Team_Widget());

            }

            $deactivate_element_testimonials = lae_get_option('lae_deactivate_element_testimonials', false);
            if (!$deactivate_element_testimonials) {

                require_once LAE_ADDONS_DIR . 'testimonials.php';

                $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Testimonials_Widget());

            }

            $deactivate_element_testimonials_slider = lae_get_option('lae_deactivate_element_testimonials_slider', false);
            if (!$deactivate_element_testimonials_slider) {

                require_once LAE_ADDONS_DIR . 'testimonials-slider.php';

                $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Testimonials_Slider_Widget());

            }

            $deactivate_element_tab_slider = lae_get_option('lae_deactivate_element_tab_slider', false);
            if (!$deactivate_element_tab_slider) {

                require_once LAE_ADDONS_DIR . 'tab-slider.php';

                $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Tab_Slider_Widget());

            }

            $deactivate_element_stats_bar = lae_get_option('lae_deactivate_element_stats_bar', false);
            if (!$deactivate_element_stats_bar) {

                require_once LAE_ADDONS_DIR . 'stats-bars.php';

                $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Stats_Bars_Widget());

            }

            $deactivate_element_piecharts = lae_get_option('lae_deactivate_element_piecharts', false);
            if (!$deactivate_element_piecharts) {

                require_once LAE_ADDONS_DIR . 'piecharts.php';

                $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Piecharts_Widget());

            }

            $deactivate_element_odometers = lae_get_option('lae_deactivate_element_odometers', false);
            if (!$deactivate_element_odometers) {

                require_once LAE_ADDONS_DIR . 'odometers.php';

                $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Odometers_Widget());

            }

            $deactivate_element_services = lae_get_option('lae_deactivate_element_services', false);
            if (!$deactivate_element_services) {

                require_once LAE_ADDONS_DIR . 'services.php';

                $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Services_Widget());

            }

            $deactivate_element_heading = lae_get_option('lae_deactivate_element_heading', false);
            if (!$deactivate_element_heading) {

                require_once LAE_ADDONS_DIR . 'heading.php';

                $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Heading_Widget());

            }


            $deactivate_element_clients = lae_get_option('lae_deactivate_element_clients', false);
            if (!$deactivate_element_clients) {

                require_once LAE_ADDONS_DIR . 'clients.php';

                $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Clients_Widget());
            }

            $deactivate_element_pricing_table = lae_get_option('lae_deactivate_element_pricing_table', false);
            if (!$deactivate_element_pricing_table) {

                require_once LAE_ADDONS_DIR . 'pricing-table.php';

                $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Pricing_Table_Widget());

            }

            $deactivate_element_posts_carousel = lae_get_option('lae_deactivate_element_posts_carousel', false);
            if (!$deactivate_element_posts_carousel) {

                require_once LAE_ADDONS_DIR . 'posts-carousel.php';

                $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Posts_Carousel_Widget());
            }

            $deactivate_element_carousel = lae_get_option('lae_deactivate_element_carousel', false);
            if (!$deactivate_element_carousel) {

                require_once LAE_ADDONS_DIR . 'carousel.php';

                $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Carousel_Widget());
            }

            if (lae_fs()->can_use_premium_code__premium_only()) {

                $deactivate_element_portfolio = lae_get_option('lae_deactivate_element_portfolio', false);
                if (!$deactivate_element_portfolio) {

                    require_once LAE_PREMIUM_ADDONS_DIR . 'portfolio.php';

                    $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Portfolio_Widget());
                }
            }
            else {
                $deactivate_element_portfolio = lae_get_option('lae_deactivate_element_portfolio', false);
                if (!$deactivate_element_portfolio) {

                    require_once LAE_ADDONS_DIR . 'portfolio.php';

                    $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Portfolio_Widget());
                }
            }


            if (lae_fs()->can_use_premium_code__premium_only()) {

                /* --------------- Pro Elements ------------------ */


                $deactivate_element_accordion = lae_get_option('lae_deactivate_element_accordion', false);
                if (!$deactivate_element_accordion) {

                    require_once LAE_PREMIUM_ADDONS_DIR . 'accordion.php';

                    $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Accordion_Widget());
                }

                $deactivate_element_tabs = lae_get_option('lae_deactivate_element_tabs', false);
                if (!$deactivate_element_tabs) {

                    require_once LAE_PREMIUM_ADDONS_DIR . 'tabs.php';

                    $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Tabs_Widget());

                }

                $deactivate_element_services_carousel = lae_get_option('lae_deactivate_element_services_carousel', false);
                if (!$deactivate_element_services_carousel) {

                    require_once LAE_PREMIUM_ADDONS_DIR . 'services-carousel.php';

                    $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Services_Carousel_Widget());

                }

                $deactivate_element_posts_block = lae_get_option('lae_deactivate_element_posts_block', false);
                if (!$deactivate_element_posts_block) {

                    require_once LAE_PREMIUM_ADDONS_DIR . 'posts-block.php';

                    $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Posts_Block_Widget());

                }

                $deactivate_element_button = lae_get_option('lae_deactivate_element_button', false);
                if (!$deactivate_element_button) {

                    require_once LAE_PREMIUM_ADDONS_DIR . 'button.php';

                    $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Button_Widget());

                }

                $deactivate_element_faq = lae_get_option('lae_deactivate_element_faq', false);
                if (!$deactivate_element_faq) {

                    require_once LAE_PREMIUM_ADDONS_DIR . 'faq.php';

                    $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_FAQ_Widget());
                }

                $deactivate_element_features = lae_get_option('lae_deactivate_element_features', false);
                if (!$deactivate_element_features) {

                    require_once LAE_PREMIUM_ADDONS_DIR . 'features.php';

                    $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Features_Widget());

                }

                $deactivate_element_image_slider = lae_get_option('lae_deactivate_element_image_slider', false);
                if (!$deactivate_element_image_slider) {

                    require_once LAE_PREMIUM_ADDONS_DIR . 'image-slider.php';

                    $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Image_slider_Widget());
                }

                $deactivate_element_gallery = lae_get_option('lae_deactivate_element_gallery', false);
                if (!$deactivate_element_gallery) {

                    require_once LAE_PREMIUM_ADDONS_DIR . 'gallery.php';

                    $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Gallery_Widget());
                }

                $deactivate_element_twitter_grid = lae_get_option('lae_deactivate_element_twitter_grid', false);
                if (!$deactivate_element_twitter_grid) {

                    require_once LAE_PREMIUM_ADDONS_DIR . 'twitter-grid.php';

                    $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Twitter_Grid_Widget());
                }

                $deactivate_element_youtube_grid = lae_get_option('lae_deactivate_element_youtube_grid', false);
                if (!$deactivate_element_youtube_grid) {

                    require_once LAE_PREMIUM_ADDONS_DIR . 'youtube-grid.php';

                    $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_YouTube_Grid_Widget());
                }

                $deactivate_element_vimeo_grid = lae_get_option('lae_deactivate_element_vimeo_grid', false);
                if (!$deactivate_element_vimeo_grid) {

                    require_once LAE_PREMIUM_ADDONS_DIR . 'vimeo-grid.php';

                    $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Vimeo_Grid_Widget());
                }

                $deactivate_element_instagram_grid = lae_get_option('lae_deactivate_element_instagram_grid', false);
                if (!$deactivate_element_instagram_grid) {

                    require_once LAE_PREMIUM_ADDONS_DIR . 'instagram-grid.php';

                    $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Instagram_Grid_Widget());
                }

                $deactivate_element_gallery_carousel = lae_get_option('lae_deactivate_element_gallery_carousel', false);
                if (!$deactivate_element_gallery_carousel) {

                    require_once LAE_PREMIUM_ADDONS_DIR . 'gallery-carousel.php';

                    $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Gallery_Carousel_Widget());
                }

                $deactivate_element_icon_list = lae_get_option('lae_deactivate_element_icon_list', false);
                if (!$deactivate_element_icon_list) {

                    require_once LAE_PREMIUM_ADDONS_DIR . 'icon-list.php';

                    $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Icon_List_Widget());
                }

                $deactivate_element_slider = lae_get_option('lae_deactivate_element_slider', false);
                if (!$deactivate_element_slider) {

                    require_once LAE_PREMIUM_ADDONS_DIR . 'slider.php';

                    $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Slider_Widget());
                }

            }

        }

    }

    /**
     * The main function responsible for returning the one true Livemesh_Elementor_Addons
     * Instance to functions everywhere.
     *
     * Use this function like you would a global variable, except without needing
     * to declare the global.
     *
     * Example: <?php $lae = LAE(); ?>
     */
    function LAE() {
        return Livemesh_Elementor_Addons::instance();
    }

    // Get LAE Running
    LAE();

endif; // End if class_exists check


