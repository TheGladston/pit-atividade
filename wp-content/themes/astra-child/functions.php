<?php
// wp_set_password ('password', 1);
//
// Recommended way to include parent theme styles.
// (Please see https://developer.wordpress.org/themes/advanced-topics/child-themes/
//  

function theme_enqueue_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style'));
    wp_enqueue_style('fonteawesome', get_stylesheet_directory_uri() . '/assets/css/all.min.css');
    wp_enqueue_script('main', get_stylesheet_directory_uri() . '/assets/js/main.js', array('jquery'), '', true);
    wp_localize_script('main', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php'), 'store_url' => get_bloginfo('url')));
    wp_enqueue_script('ass', 'https://assinaturaweb.s3-sa-east-1.amazonaws.com/brt-assinatura-2022.min.js', array('jquery'), '', true);
}

add_action('wp_enqueue_scripts', 'theme_enqueue_styles', 100);

global $options;
$options = get_option('p2h_theme_options');
$functions_path = get_theme_file_path() . '/functions/';
require_once ($functions_path . 'theme-options.php');

/* SLIDER */
require_once (get_theme_file_path() . '/slider/slider.php');

/* NEWSLETTER */
require_once (get_theme_file_path() . '/newsletter/newsletter.php');

/* DEPOIMENTO */
require_once (get_theme_file_path() . '/depoimento/depoimento.php');

if (!function_exists('printr')) {

    function printr($obj, $label = null) {
        $debug = (isset($_GET['debug'])) ? $_GET['debug'] : '';
        if ($debug == 1) :
            print '<pre>';
            if ($label) {
                print $label;
                print "\n";
            }
            print_r($obj);
            print '</pre>';
        endif;
    }

}

/** Remove a Verificação de Força */
function fa_remove_password_strength() {
    wp_dequeue_script('wc-password-strength-meter');
    wp_deregister_script('wc-password-strength-meter');
}

add_action('wp_enqueue_scripts', 'fa_remove_password_strength', 99999);

function services_home() {
    ?>
    <div id="owl-services-home" class="services-home-wrapper owl-carousel owl-theme">
        <div class="services-item">
            <div class="info">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icones/ico-1.png" class="img-responsive" alt="" />
                <span class="title">frete grátis</span>
                <span class="text">via motoboy para Betim</span>
            </div>
        </div>
        <div class="services-item">
            <div class="info">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icones/ico-2.png" class="img-responsive" alt="" />
                <span class="title">FRETE FIXO de r$10,00</span>
                <span class="text">via motoboy para BH e região</span>
            </div>
        </div>
        <div class="services-item">
            <div class="info">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icones/ico-3.png" class="img-responsive" alt="" />
                <span class="title">PARCELE SUAS COMPRAS</span>
                <span class="text">em até 12x sem juros</span>
            </div>
        </div>
        <div class="services-item">
            <div class="info">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icones/ico-4.png" class="img-responsive" alt="" />
                <span class="title">5% de desconto</span>
                <span class="text">pagando com o PIX</span>
            </div>
        </div>
    </div>
    <?php
}

add_shortcode('services_home', 'services_home');
add_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

function price_after() {
    do_action('show-installments-home');
}

add_action('uael_woo_products_price_after', 'price_after');
add_action('astra_woo_shop_price_after', 'price_after', 10);

/* Função para trocar %store_url% pela url da loja */

function replace_url_link_personalizado_menu_items($atts, $item, $args) {

    $store_url = get_bloginfo('url');
    $url_home = explode('/', $store_url);

    $atts['href'] = str_replace('%store_url%', $url_home[2], $atts['href']);

    return $atts;
}

add_filter('nav_menu_link_attributes', 'replace_url_link_personalizado_menu_items', 10, 3);

function woocommerce_add_to_cart_button_text_archives() {
    return 'COMPRAR';
}

add_filter('woocommerce_product_add_to_cart_text', 'woocommerce_add_to_cart_button_text_archives');
