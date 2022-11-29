<?php
/**
 * Related Products
 *
 * @see 	https://docs.woocommerce.com/document/template-structure/
 * @author 	WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

global $product, $woocommerce_loop, $ros_opt;

$_delay = 0;
$_delay_item = (isset($ros_opt['delay_overlay']) && (int) $ros_opt['delay_overlay']) ? (int) $ros_opt['delay_overlay'] : 100;

$related = function_exists('wc_get_related_products') ? wc_get_related_products($product->get_id(), 12) : $product->get_related(12);
$related = array_merge($product->upsell_ids, $related);

if (sizeof($related) > 0):

    $args = apply_filters('woocommerce_related_products_args', array(
        'post_type' => 'product',
        'ignore_sticky_posts' => 1,
        'no_found_rows' => 1,
        'orderby' => $orderby,
        'post__in' => $related,
        'post__not_in' => array($product->get_id())
    ));

    $products = new WP_Query($args);

    if ($products->have_posts()) :
        ?>

        <div class="related">
            <div class="title-block text-center">
                <div class="titulo-home">
                    <h5>Produtos relacionados</h5>
                </div>
            </div>
            <div class="ast-container">
                <ul id="owl-related" class="products elementor-grid owl-carousel owl-theme">
                    <?php while ($products->have_posts()) : $products->the_post(); ?>
                        <?php wc_get_template('content-product.php', array('_delay' => $_delay, 'wrapper' => 'div')); ?>
                        <?php $_delay += $_delay_item; ?>	
                    <?php endwhile; ?>
                </ul> 
            </div> 
        </div>
        <?php
    endif;
    wp_reset_postdata();
endif;

