<?php
if (!defined('ABSPATH'))
  exit;

if (!class_exists('OCSCW_charts_mb')) {

    class OCSCW_charts_mb {

        protected static $instance;

        function OCSCW_asprods_asprodcats_metabox_for_select2() {
            add_meta_box( 'asprods_select2', __( 'Assign Products', 'size-chart-woocommerce' ), array($this, 'OCSCW_asprods_display_select2_metabox'), 'ocscw_size_chart', 'side');

            add_meta_box( 'asprodcats_select2', __( 'Assign Categories', 'size-chart-woocommerce' ), array($this, 'OCSCW_asprodcats_display_select2_metabox'), 'ocscw_size_chart', 'side');

            add_meta_box( 'asprodattrs_select2', __( 'Assign Attributes', 'size-chart-woocommerce-pro' ), array($this, 'OCSCW_asprodattrs_display_select2_metabox'), 'ocscw_size_chart', 'side');
        }

        function OCSCW_asprods_display_select2_metabox( $post_object ) {
 
            $html = '';
         
            $appended_posts = get_post_meta( $post_object->ID, OCSCW_PREFIX.'asprods_select2_posts',true );
         
            $html .= '<p><select id="asprods_select2_posts" name="asprods_select2_posts[]" multiple="multiple" style="width:99%;max-width:25em;">';
         
            if( !empty($appended_posts) ) {
                foreach( $appended_posts as $post_id ) {
                    $title = get_the_title( $post_id );
                    $title = ( mb_strlen( $title ) > 50 ) ? mb_substr( $title, 0, 49 ) . '...' : $title;
                    $html .=  '<option value="' . $post_id . '" selected="selected">' . $title . '</option>';
                }
            }
            $html .= '</select></p>';
         
            echo $html;
        }

        function OCSCW_asprods_save_metaboxdata( $post_id, $post ) {
 
            if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
         
            if ( $post->post_type == 'ocscw_size_chart' ) {
                if( isset( $_POST['asprods_select2_posts'] ) ) {
                    update_post_meta( $post_id, OCSCW_PREFIX.'asprods_select2_posts', $_POST['asprods_select2_posts'] );
                }
                else {
                    delete_post_meta( $post_id, OCSCW_PREFIX.'asprods_select2_posts' );
                }
            }
            return $post_id;
        }

        function OCSCW_asprods_get_posts_ajax_callback() {

            $return = array();

            $search_results = new WP_Query( array( 
                'post_type' => 'product',
                's'=> $_GET['q'],
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'posts_per_page' => 50
            ) );
            if( $search_results->have_posts() ) :
                while( $search_results->have_posts() ) : $search_results->the_post();   
                    $title = ( mb_strlen( $search_results->post->post_title ) > 50 ) ? mb_substr( $search_results->post->post_title, 0, 49 ) . '...' : $search_results->post->post_title;
                    $return[] = array( $search_results->post->ID, $title );
                endwhile;
            endif;
            echo json_encode( $return );
            die;
        }

        function OCSCW_asprodcats_display_select2_metabox( $post_object ) {
 
            $html = '';
         
            $appended_terms = get_post_meta( $post_object->ID, OCSCW_PREFIX.'asprodcats_select2_posts',true );

            $html .= '<p><select id="asprodcats_select2_posts" name="asprodcats_select2_posts[]" multiple="multiple" style="width:99%;max-width:25em;">';

            if( !empty($appended_terms) ) {
                foreach( $appended_terms as $term_id ) {
                    $term_name = get_term( $term_id )->name;
                    $term_name = ( mb_strlen( $term_name ) > 50 ) ? mb_substr( $term_name, 0, 49 ) . '...' : $term_name;
                    $html .=  '<option value="' . $term_id . '" selected="selected">' . $term_name . '</option>';
                }
            }
            $html .= '</select></p>';
         
            echo $html;
        }

        function OCSCW_asprodcats_save_metaboxdata( $post_id, $post ) {
 
            if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
         
            if ( $post->post_type == 'ocscw_size_chart' ) {
                if( isset( $_POST['asprodcats_select2_posts'] ) ) {
                    update_post_meta( $post_id, OCSCW_PREFIX.'asprodcats_select2_posts', $_POST['asprodcats_select2_posts'] );
                }
                else {
                    delete_post_meta( $post_id, OCSCW_PREFIX.'asprodcats_select2_posts' );
                }
            }
            return $post_id;
        }

        function OCSCW_asprodcats_get_posts_ajax_callback() {

            $return = array();

            $product_categories = get_terms( 'product_cat', $cat_args );

            if( !empty($product_categories) ) {
                foreach ($product_categories as $key => $category) {
                    $category->term_id;
                    $title = ( mb_strlen( $category->name ) > 50 ) ? mb_substr( $category->name, 0, 49 ) . '...' : $category->name;
                    $return[] = array( $category->term_id, $title );
                }
            }

            echo json_encode( $return );
            die;
        }

        function OCSCW_asprodattrs_display_select2_metabox( $post_object ) {
 
            $html = '';
         
            $appended_terms = get_post_meta( $post_object->ID, OCSCW_PREFIX.'asprodattrs_select2_posts',true );

            $html .= '<p><select id="asprodattrs_select2_posts" name="asprodattrs_select2_posts[]" multiple="multiple" style="width:99%;max-width:25em;">';

            if( !empty($appended_terms) ) {
                foreach( $appended_terms as $term_id ) {
                    $term_name = get_term( $term_id )->name;
                    $term_name = ( mb_strlen( $term_name ) > 50 ) ? mb_substr( $term_name, 0, 49 ) . '...' : $term_name;
                    $html .=  '<option value="' . $term_id . '" selected="selected">' . $term_name . '</option>';
                }
            }
            $html .= '</select></p>';
         
            echo $html;
        }

        function OCSCW_asprodattrs_save_metaboxdata( $post_id, $post ) {
 
            if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
         
            if ( $post->post_type == 'ocscw_size_chart' ) {
                if( isset( $_POST['asprodattrs_select2_posts'] ) ) {
                    update_post_meta( $post_id, OCSCW_PREFIX.'asprodattrs_select2_posts', $_POST['asprodattrs_select2_posts'] );
                }
                else {
                    delete_post_meta( $post_id, OCSCW_PREFIX.'asprodattrs_select2_posts' );
                }
            }
            return $post_id;
        }

        function OCSCW_asprodattrs_get_posts_ajax_callback() {

            $attribute_taxonomies = wc_get_attribute_taxonomies();
            if ( $attribute_taxonomies ) {
                foreach ($attribute_taxonomies as $tax) {
                    if (taxonomy_exists(wc_attribute_taxonomy_name($tax->attribute_name))) {
                        $taxonomy_terms[$tax->attribute_id] = get_terms( wc_attribute_taxonomy_name($tax->attribute_name), 'orderby=name' );
                    }
                }
            }

            $product_attributes = array();
            foreach ( $taxonomy_terms as $key => $attribute ) {
                foreach($attribute as $dfg){
                    $title = ( mb_strlen( $dfg->name ) > 50 ) ? mb_substr( $dfg->name, 0, 49 ) . '...' : $dfg->name;
                    $product_attributes[] = array($dfg->term_id , $title);
                }
            }

            echo json_encode( $product_attributes );
            die;
        }

        function OCSCW_support_and_rating_notice() {
		    $screen = get_current_screen();
		    if( 'ocscw_size_chart' == $screen->post_type
		        && 'edit' == $screen->base ) {
		        ?>
		        <div class="ocscw_ratesup_notice_main">
                    <div class="ocscw_rateus_notice">
                        <div class="ocscw_rtusnoti_left">
                            <h3>Rate Us</h3>
                            <label>If you like our plugin, </label>
                            <a target="_blank" href="https://wordpress.org/support/plugin/size-chart-woocommerce/reviews/?filter=5#new-post">
                                <label>Please vote us</label>
                            </a>
                            <label>, so we can contribute more features for you.</label>
                        </div>
                        <div class="ocscw_rtusnoti_right">
                            <img src="<?php echo OCSCW_PLUGIN_DIR; ?>/includes/images/review.png" class="ocscw_review_icon">
                        </div>
                    </div>
                    <div class="ocscw_support_notice">
                        <div class="ocscw_rtusnoti_left">
                            <h3>Having Issues?</h3>
                            <label>You can contact us at</label>
                            <a target="_blank" href="https://oceanwebguru.com/contact-us/">
                                <label>Our Support Forum</label>
                            </a>
                        </div>
                        <div class="ocscw_rtusnoti_right">
                            <img src="<?php echo OCSCW_PLUGIN_DIR; ?>/includes/images/support.png" class="ocscw_review_icon">
                        </div>
                    </div>
                </div>
                <div class="ocscw_donate_main">
                   <img src="<?php echo OCSCW_PLUGIN_DIR; ?>/includes/images/coffee.svg">
                   <h3>Buy me a Coffee !</h3>
                   <p>If you like this plugin, buy me a coffee and help support this plugin !</p>
                   <div class="ocscw_donate_form">
                      <a class="button button-primary ocscw_donate_btn" href="https://www.paypal.com/paypalme/shayona163/" data-link="https://www.paypal.com/paypalme/shayona163/" target="_blank">Buy me a coffee !</a>
                   </div>
                </div>
		        <?php
		    }
		}

        function init() {
            add_action( 'admin_menu', array($this, 'OCSCW_asprods_asprodcats_metabox_for_select2' ));
            add_action( 'save_post', array($this, 'OCSCW_asprods_save_metaboxdata'), 10, 2 );
            add_action( 'wp_ajax_nopriv_OCSCW_asprods_get_posts',array($this, 'OCSCW_asprods_get_posts_ajax_callback') );
            add_action( 'wp_ajax_OCSCW_asprods_get_posts', array($this, 'OCSCW_asprods_get_posts_ajax_callback') );

            add_action( 'save_post', array($this, 'OCSCW_asprodcats_save_metaboxdata'), 10, 2 );
            add_action( 'wp_ajax_nopriv_OCSCW_asprodcats_get_posts',array($this, 'OCSCW_asprodcats_get_posts_ajax_callback') );
            add_action( 'wp_ajax_OCSCW_asprodcats_get_posts', array($this, 'OCSCW_asprodcats_get_posts_ajax_callback') );

            add_action( 'save_post', array($this, 'OCSCW_asprodattrs_save_metaboxdata'), 10, 2 );
            add_action( 'wp_ajax_nopriv_OCSCW_asprodattrs_get_posts',array($this, 'OCSCW_asprodattrs_get_posts_ajax_callback') );
            add_action( 'wp_ajax_OCSCW_asprodattrs_get_posts', array($this, 'OCSCW_asprodattrs_get_posts_ajax_callback') );

            add_action( 'admin_notices', array($this, 'OCSCW_support_and_rating_notice' ));
        }

        public static function instance() {
            if (!isset(self::$instance)) {
                self::$instance = new self();
                self::$instance->init();
            }
            return self::$instance;
        }
    }
    OCSCW_charts_mb::instance();
}