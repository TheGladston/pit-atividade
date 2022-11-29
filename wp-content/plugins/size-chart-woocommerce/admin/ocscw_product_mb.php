<?php
if (!defined('ABSPATH'))
  exit;

if (!class_exists('OCSCW_product_mb')) {

    class OCSCW_product_mb {

        protected static $instance;

        function OCSCW_add_metabox_product() {
            add_meta_box(
                'SizeChart_metabox',
                __( 'All Size Chart', 'size-chart-woocommerce' ),
                array($this, 'OCSCW_metabox_size'),
                'product',
                'side'
            );
        }


        function OCSCW_metabox_size( $post ) {
            wp_nonce_field( 'ocscw_productmeta_save', 'ocscw_productmeta_save_nounce' );
            $args = array(
                'post_type' => 'ocscw_size_chart',
                'posts_per_page' => -1
            );
                     
            $my_query = get_posts( $args );

            ?>
            <select id="ocscw_selectchart" name="product_sizechart[]" multiple="multiple" style="width:100%;max-width:25em;">
                <?php 
                    $selected_chart = get_post_meta( $post->ID, OCSCW_PREFIX.'product_sizechart', true);
                    
                    if(!empty($selected_chart)) {
                        foreach ($selected_chart as $key => $value) {
                            echo '<option value="'.$value.'" selected>'.get_the_title( $value ).'</option>';
                        }
                    }
                ?>
            </select>
            <?php
        }


        function OCSCW_recursive_sanitize_text_field($array) {
            if(!empty($array)) {
                foreach ( $array as $key => &$value ) {
                    if ( is_array( $value ) ) {
                        $value = $this->OCSCW_recursive_sanitize_text_field($value);
                    }else{
                        $value = sanitize_text_field( $value );
                    }
                }
            }
            return $array;
        }


        function OCSCW_save_metabox_product( $post_id, $post ) {
            if ($post->post_type != 'product') {return;}
         
            if ( !current_user_can( 'edit_post', $post_id )) return;
            $is_autosave = wp_is_post_autosave($post_id);
            $is_revision = wp_is_post_revision($post_id);
            $is_valid_nonce = (isset($_POST['ocscw_productmeta_save_nounce']) && wp_verify_nonce( $_POST['ocscw_productmeta_save_nounce'], 'ocscw_productmeta_save' )? 'true': 'false');

            if ( $is_autosave || $is_revision || !$is_valid_nonce ) return;

            $product_sizechart  =  $this->OCSCW_recursive_sanitize_text_field( $_REQUEST['product_sizechart'] );
            update_post_meta( $post_id, OCSCW_PREFIX.'product_sizechart',$product_sizechart);
        }


        function ocscw_search_chart() {
            $return = array();
            $search_results = new WP_Query( array( 
                's'=> $_GET['q'], // the search query
                'post_status' => 'publish',
                'post_type' => 'ocscw_size_chart',
                'posts_per_page' => -1,
            ) );

            if( $search_results->have_posts() ) :
                while( $search_results->have_posts() ) : $search_results->the_post();   
                    $productc = wc_get_product( $search_results->post->ID );
                   
                    $title = $search_results->post->post_title;
                    $return[] = array( $search_results->post->ID, $title);
                        
                endwhile;
            endif;
            echo json_encode( $return );
            die;
        }


        function init() {
            add_action( 'add_meta_boxes', array($this, 'OCSCW_add_metabox_product'));
            add_action( 'edit_post', array($this, 'OCSCW_save_metabox_product'), 10, 2);
            add_action( 'wp_ajax_nopriv_ocscw_search_chart',array($this, 'ocscw_search_chart') );
            add_action( 'wp_ajax_ocscw_search_chart', array($this, 'ocscw_search_chart') );
        }


        public static function instance() {
            if (!isset(self::$instance)) {
                self::$instance = new self();
                self::$instance->init();
            }
            return self::$instance;
        }
    }
    OCSCW_product_mb::instance();
}