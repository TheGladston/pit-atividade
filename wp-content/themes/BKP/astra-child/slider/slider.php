<?php

function setup_theme_child() {
    add_image_size('owl-slider-desktop', 1920, 455, true);
    add_image_size('owl-slider-mobile', 992, 660, true);
}

add_action('after_setup_theme', 'leetheme_setup');

function main_scripts_home_slider() {
    $home_options = get_option('home_slider_option');
    wp_enqueue_script('home-slider', get_stylesheet_directory_uri() . "/slider/owl/owl.carousel.min.js", array('jquery'), false);

    wp_enqueue_style('home-slider-owl', get_stylesheet_directory_uri() . '/slider/owl/assets/owl.carousel.min.css', false);
    wp_enqueue_style('home-slider-theme', get_stylesheet_directory_uri() . '/slider/owl/assets/owl.theme.default.min.css', false);
    wp_enqueue_style('home-slider', get_stylesheet_directory_uri() . '/slider/css/home-slider.min.css', false);
}

add_action('wp_enqueue_scripts', 'main_scripts_home_slider');

function slide_register() {

    $labels = array(
        'name' => _x('Slides Home', 'post type general name'),
        'singular_name' => _x('Slide', 'post type singular name'),
        'add_new' => _x('Novo slide', 'funcionario item'),
        'add_new_item' => __('Adicionar Novo'),
        'edit_item' => __('Editar'),
        'new_item' => __('Novo slide'),
        'view_item' => __('Ver slide'),
        'search_items' => __('Procurar slide'),
        'not_found' => __('Nothing found'),
        'not_found_in_trash' => __('Nothing found in Trash'),
        'parent_item_colon' => ''
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'query_var' => true,
        'menu_icon' => 'dashicons-laptop',
        'rewrite' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'menu_position' => 4,
        'supports' => array('title', 'editor', 'thumbnail')
    );

    register_post_type('slide', $args);
    flush_rewrite_rules();
}

add_action('init', 'slide_register');

function slide_meta() {
    global $post;
    $custom = get_post_custom($post->ID);
    $mobile = $custom["mobile"][0];
    $slider_order = $custom["slider_order"][0];
    $url_slide = $custom["url_slide"][0];
    ?>
    <p>
        <label>Slide mobile?:</label><br />
        <label><input type="radio" name="mobile" value="desktop" <?php echo ($mobile == 'desktop') ? 'checked' : ''; ?> />Desktop</label>&nbsp;&nbsp;
        <label><input type="radio" name="mobile" value="mobile" <?php echo ($mobile == 'mobile') ? 'checked' : ''; ?> />Mobile</label>
    </p>
    <p>
        <label>Ordem do slide</label><br />
        <input type="number" name="slider_order" value="<?php echo ($slider_order != '') ? $slider_order : 1; ?>" />
    </p>
    <p>
        <label>Url do slide</label><br />
        <input style="width: 100%;" name="url_slide" value="<?php echo $url_slide; ?>" <?php echo ($url_slide == '') ? 'placeholder="Ex.: ' . get_bloginfo('url') . '/contato"' : ''; ?> />
    </p>
    <?php
}

function admin_init_slider() {
    add_meta_box("slide_meta", "Informações do slide", "slide_meta", "slide", "side", "low");
}

add_action("admin_init", "admin_init_slider");

function save_details_slide($post_id, $post) {
    update_post_meta($post->ID, "mobile", $_POST["mobile"]);
    update_post_meta($post->ID, "url_slide", $_POST["url_slide"]);
    update_post_meta($post->ID, "slider_order", $_POST["slider_order"]);
}

add_action('save_post_slide', 'save_details_slide', 10, 2);

function get_the_post_thumbnail_src($img) {
    return (preg_match('~\bsrc="([^"]++)"~', $img, $matches)) ? $matches[1] : '';
}

function is_mobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

function slider_home() {
    if (is_front_page() || is_page('home')) :
        $is_mobile = (is_mobile()) ? 'mobile' : 'desktop';
        query_posts(array(
            'post_type' => 'slide',
            'meta_query' => array(
                array(
                    'key' => 'mobile',
                    'value' => $is_mobile,
                    'compare' => '=',
                )
            ),
            'order' => 'ASC',
            'meta_key' => 'slider_order',
            'orderby' => 'meta_value_num',
            'post_status' => 'publish',
        ));
        if (have_posts()) :
            ?>
            <section class="slider">
                <div id="owl-slider" class="owl-carousel owl-theme">
                    <?php
                    while (have_posts()) : the_post();
                        global $post;
                        $subtitulo = get_post_meta($post->ID, "subtitulo", true);
                        $url_slide = get_post_meta($post->ID, 'url_slide', true);
                        ?>
                        <div class="item-slider">
                            <div class="slide-owl-wrap">
                                <?php echo ($url_slide != '') ? '<a href="' . $url_slide . '">' : ''; ?>
                                <img src="<?php echo get_the_post_thumbnail_src(get_the_post_thumbnail($post->ID, 'owl-slider-' . $is_mobile)); ?>" alt="<?php the_title(); ?>" />
                                <?php echo ($url_slide != '') ? '</a>' : ''; ?>
                            </div>
                        </div>
                        <?php
                    endwhile;
                    wp_reset_query();
                    ?>
                </div>
            </section>
            <script>
                jQuery(document).ready(function () {
                    jQuery("#owl-slider").owlCarousel({
                        navText: ['<i class="fas fa-chevron-left"></i>', '<i class="fas fa-chevron-right"></i>'],
                        autoplayHoverPause: true,
                        autoplayTimeout: 4000,
                        smartSpeed: 1000,
                        autoplay: true,
                        dots: true,
                        loop: true,
                        nav: false,
                        items: 1,
                        responsive: {
                            0: {
                                nav: false,
                            },
                            991: {
                                nav: false,
                            },
                            1200: {
                                nav: true,
                            },
                        }
                    });
                });
            </script>
            <?php
        endif;
    endif;
}

add_shortcode('home_slider', 'slider_home');
