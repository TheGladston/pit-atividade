<?php

if (!defined('ABSPATH'))
  exit;

if (!class_exists('OCSCW_front')) {

    class OCSCW_front {

        protected static $instance;

        function ocscw_get_chart_ids($product_id) {
            $chart_ids = get_post_meta( $product_id, OCSCW_PREFIX.'product_sizechart', true );
                
            if(!empty($chart_ids)) {
                $chart_ids = $chart_ids;
            } else {
                $chart_ids = array();
            }

            $product_terms = wp_get_object_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );

            if(!empty($product_terms)) {
                $product_terms = $product_terms;
            } else {
                $product_terms = array();
            }

            $product = wc_get_product( $product_id );
            $product_attrs = array();
            foreach ($product->get_attributes() as $key => $value) {
                $product_attributes = wc_get_product_terms( $product_id, $key , array( 'fields' => 'ids' ));
                $product_attrs[] = $product_attributes;
            }

            $assign_prod_ids   = array();

            $args = array(
                'post_type'        =>   'ocscw_size_chart',
                'posts_per_page'   =>   -1,
                'post_status'      =>   'publish',
                );

            $query = new WP_Query( $args );
            if ( $query->have_posts() ) {
                while ( $query->have_posts() ) {
                    $query->the_post();

                    $assign_products = get_post_meta( get_the_ID(), OCSCW_PREFIX.'asprods_select2_posts',true );

                    $assign_terms = get_post_meta( get_the_ID(), OCSCW_PREFIX.'asprodcats_select2_posts',true );

                    $assign_attr = get_post_meta( get_the_ID(), OCSCW_PREFIX.'asprodattrs_select2_posts',true );

                    $szchartpp_aply_all = get_post_meta( get_the_ID(), OCSCW_PREFIX.'szchartpp_aply_all',true );

                    if($szchartpp_aply_all == 'enable') {
                        $assign_prod_ids[] = get_the_ID();
                    } else {
                        if(!empty($assign_products)) {
                            foreach ($assign_products as $prod_key => $prod_value) {
                                if($prod_value == $product_id) {
                                    $assign_prod_ids[] = get_the_ID();
                                }
                            }
                        }

                        if(!empty($assign_terms)) {
                            foreach ($assign_terms as $term_key => $term_value) {
                                
                                if (in_array($term_value, $product_terms)) {
                                    $assign_prod_ids[] = get_the_ID();
                                }
                                
                            }
                        }

                        if(!empty($assign_attr)) {
                            foreach ($assign_attr as $attr_key => $attr_value) {
                                foreach ($product_attrs as $product_attrskey => $product_attrsvalue) {
                                    if (in_array($attr_value, $product_attrsvalue)) {
                                        $assign_prod_ids[] = get_the_ID();
                                    }
                                }
                            }
                        }
                    }
                }
            }
            wp_reset_query();
            
            $chart_ids_merge = array_merge($chart_ids, $assign_prod_ids);
            $chart_unique = array_unique($chart_ids_merge);

            return $chart_unique;
        }


        function ocscw_header_action() {
            if(is_product()) {

                $OCSCW_object = get_queried_object();
                $product_id   = $OCSCW_object->ID;
                $product      = wc_get_product( $product_id );

                $chart_ids = $this->ocscw_get_chart_ids($product_id);

                if(!empty($chart_ids)) {

                    foreach ($chart_ids as $key => $chart_id) {
                        $btn_tab      = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_show', true );
                        $btn_pos      = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_pos', true );

                        if($btn_tab == "tab") {
                            add_filter( 'woocommerce_product_tabs', array( $this, 'ocscw_add_tab' ) ); 
                        } elseif ($btn_tab == "popup") {
                            if($btn_pos == "after_add_cart" ) {
                            
                                if ( ! $product->is_in_stock()) {
                                    add_action('woocommerce_product_meta_start', array( $this, 'ocscw_button_after' ),0);
                                } else {
                                    add_action('woocommerce_after_add_to_cart_form', array( $this, 'ocscw_button_after' ));
                                }

                            } elseif ($btn_pos == "before_add_cart") {

                                if( ! $product->is_in_stock()) {
                                    add_action('woocommerce_single_product_summary', array( $this, 'ocscw_button_before' ));
                                } else {
                                    if ( $product->is_type( 'variable' ) ) {
                                        add_action('woocommerce_single_variation', array( $this, 'ocscw_button_before' ));
                                    } else {
                                        add_action('woocommerce_before_add_to_cart_form', array( $this, 'ocscw_button_before' ));
                                    }
                                }
                            } elseif ($btn_pos == "before_summry_text") {
                                add_action('woocommerce_single_product_summary', array( $this, 'ocscw_button_before_summry_text' ));
                            } elseif($btn_pos == "aftr_prod_meta") {
                                add_action('woocommerce_product_meta_end', array( $this, 'ocscw_button_aftr_prod_meta' ));
                            }
                        }
                    }
                }
            }

            if(is_shop() || is_product_category() || is_product_tag()) {
                add_action('woocommerce_after_shop_loop_item', array( $this, 'ocscw_shop_page_popup_button_after' ), 11);
                
                add_action('woocommerce_after_shop_loop_item', array( $this, 'ocscw_shop_page_popup_button_before' ), 9);
            }
        }


        function ocscw_shop_page_popup_button_after() {
            global $post;
            
            $chart_ids = $this->ocscw_get_chart_ids($post->ID);

            if(!empty($chart_ids)) {

                foreach ($chart_ids as $key => $chart_id) {
                    $szchartpp_shop_enbl      = get_post_meta( $chart_id, OCSCW_PREFIX.'szchartpp_shop_enbl', true );
                    $btn_tab      = get_post_meta( $chart_id, OCSCW_PREFIX.'shop_btn_show', true );
                    $btn_pos      = get_post_meta( $chart_id, OCSCW_PREFIX.'shop_btn_pos', true );

                    if($szchartpp_shop_enbl == 'enable') {
                        if ($btn_tab == "popup") {
                            if($btn_pos == "after_add_cart" ) {
                                $btn_lbl    = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_lbl', true );
                                $btn_ft     = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_ft_clr', true );
                                $btn_bg     = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_bg_clr', true );
                                $btn_brd_rd = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_brd_rd', true );
                                $btn_padding = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_padding', true );
                                $popup_loader = get_post_meta( $chart_id, OCSCW_PREFIX.'popup_loader', true );
                                if ($popup_loader == "loader_1" || empty($popup_loader)) {
                                    $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-1.gif';
                                }elseif ($popup_loader == "loader_2") {
                                    $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-2.gif';
                                }elseif ($popup_loader == "loader_3") {
                                    $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-3.gif';
                                }elseif ($popup_loader == "loader_4") {
                                    $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-4.gif';
                                }elseif ($popup_loader == "loader_5") {
                                    $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-5.gif';
                                }elseif ($popup_loader == "loader_6") {
                                    $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-6.gif';
                                }
                                $style      = "color:".$btn_ft.";background-color:".$btn_bg.";border-radius:".$btn_brd_rd."px;padding:".$btn_padding.";";
                                ?>
                                <div class="ocscw_btn">
                                    <button class="ocscw_open" data-id="<?php echo $post->ID; ?>" data-image="<?php echo $loader;?>" data-cid="<?php echo $chart_id; ?>" style="<?php echo $style; ?>">
                                        <?php echo $btn_lbl; ?>
                                    </button>
                                </div>
                                <?php
                            }
                        }
                    }
                }
            }
        }


        function ocscw_shop_page_popup_button_before() {
            global $post;
            
            $chart_ids = $this->ocscw_get_chart_ids($post->ID);

            if(!empty($chart_ids)) {

                foreach ($chart_ids as $key => $chart_id) {
                    $szchartpp_shop_enbl      = get_post_meta( $chart_id, OCSCW_PREFIX.'szchartpp_shop_enbl', true );
                    $btn_tab      = get_post_meta( $chart_id, OCSCW_PREFIX.'shop_btn_show', true );
                    $btn_pos      = get_post_meta( $chart_id, OCSCW_PREFIX.'shop_btn_pos', true );

                    if($szchartpp_shop_enbl == 'enable') {
                        if ($btn_tab == "popup") {
                            if ($btn_pos == "before_add_cart") {
                                $btn_lbl    = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_lbl', true );
                                $btn_ft     = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_ft_clr', true );
                                $btn_bg     = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_bg_clr', true );
                                $btn_brd_rd = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_brd_rd', true );
                                $btn_padding = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_padding', true );
                                $popup_loader = get_post_meta( $chart_id, OCSCW_PREFIX.'popup_loader', true );
                                if ($popup_loader == "loader_1" || $popup_loader == " ") {
                                    $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-1.gif';
                                }elseif ($popup_loader == "loader_2") {
                                    $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-2.gif';
                                }elseif ($popup_loader == "loader_3") {
                                    $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-3.gif';
                                }elseif ($popup_loader == "loader_4") {
                                    $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-4.gif';
                                }elseif ($popup_loader == "loader_5") {
                                    $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-5.gif';
                                }elseif ($popup_loader == "loader_6") {
                                    $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-6.gif';
                                }
                                $style      = "color:".$btn_ft.";background-color:".$btn_bg.";border-radius:".$btn_brd_rd."px;padding:".$btn_padding.";";
                                ?>
                                <div class="ocscw_btn">
                                    <button class="ocscw_open" data-id="<?php echo $post->ID; ?>" data-image="<?php echo $loader;?>" data-cid="<?php echo $chart_id; ?>" style="<?php echo $style; ?>">
                                        <?php echo $btn_lbl; ?>
                                    </button>
                                </div>
                                <?php
                            }
                        }
                    }
                }
            }
        }


        function ocscw_add_tab( $tabs ) {
            $product_id = get_the_ID();
            $product    = wc_get_product( $product_id );

            $chart_ids = $this->ocscw_get_chart_ids($product_id);

            if(!empty($chart_ids)) {
                foreach ($chart_ids as $key => $chart_id) {
                    $btn_tab      = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_show', true );
                    if($btn_tab == "tab") {
                        $tab_lbl        = get_post_meta( $chart_id, OCSCW_PREFIX.'tab_lbl', true );
                        $tab_pririty    = get_post_meta( $chart_id, OCSCW_PREFIX.'tab_pririty', true );

                        $tabs['desc_tab'] = array(
                            'title'     => __( $tab_lbl, 'woocommerce' ),
                            'priority'  => $tab_pririty,
                            'callback'  => array( $this, 'ocscw_tab_content' )
                        );
                    }
                }
            }
            return $tabs;
        }


        function ocscw_tab_content() {
            $product_id         = get_the_ID();
            $product            = wc_get_product( $product_id );
            $chart_ids          = $this->ocscw_get_chart_ids($product_id);
            
            if(!empty($chart_ids)) {
                foreach ($chart_ids as $key => $chart_id) {
                    $btn_tab      = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_show', true );
                    if($btn_tab == "tab") {
                        $chart_title            = get_post_meta( $chart_id, OCSCW_PREFIX.'sub_title', true );
                        $size_chartdata         = get_post_meta( $chart_id, OCSCW_PREFIX.'size_chartdata', true );
                        $totalrow               = get_post_meta( $chart_id, OCSCW_PREFIX.'totalrow', true );
                        $totalcol               = get_post_meta( $chart_id, OCSCW_PREFIX.'totalcol', true );
                        $show_tab               = get_post_meta( $chart_id, OCSCW_PREFIX.'show_tab', true );
                        $chart_tab_name         = get_post_meta( $chart_id, OCSCW_PREFIX.'chart_tab_name', true);
                        $dis_tab_name           = get_post_meta( $chart_id, OCSCW_PREFIX.'dis_tab_name', true);
                        $tbl_head_bg_clr        = get_post_meta( $chart_id, OCSCW_PREFIX.'tbl_head_bg_clr', true);
                        $tbl_head_ft_clr        = get_post_meta( $chart_id, OCSCW_PREFIX.'tbl_head_ft_clr', true);
                        $tbl_even_row_clr       = get_post_meta( $chart_id, OCSCW_PREFIX.'tbl_even_row_clr', true);
                        $tbl_odd_row_clr        = get_post_meta( $chart_id, OCSCW_PREFIX.'tbl_odd_row_clr', true);
                        $tbl_dtrow_font_clr     = get_post_meta( $chart_id, OCSCW_PREFIX.'tbl_dtrow_font_clr', true);
                        $tbl_border             = get_post_meta( $chart_id, OCSCW_PREFIX.'tbl_border', true);
                        $table_array            = $size_chartdata;
                        echo '<div class="ocscw_tableclass">';
                            echo '<div class="ocscw_sizechart_tab_content">';
                                echo '<div class="ocscw_tab_header">';
                                    echo '<h1>'.$chart_title.'</h1>';
                                echo '</div>';
                                echo '<div class="ocscw_tab_body">';
                                    echo '<div class="ocscw_tab_data">';
                                        echo '<div class="ocscw_tab_padding_div">';
                                            if($show_tab == "on"){
                                                ?>
                                                    <ul class="ocscw_front_tabs">
                                                        <li class="tab-link current" data-tab="tab-default">
                                                            <?php echo __( $chart_tab_name, 'size-chart-woocommerce' );?>
                                                        </li>
                                                        <li class="tab-link" data-tab="tab-general">
                                                            <?php echo __( $dis_tab_name , 'size-chart-woocommerce' );?>
                                                        </li>
                                                    </ul>
                                                    <div id="tab-default" class="ocscw_front_tab_content current">
                                                        <div class="ocscw_child_div">
                                                            <?php
                                                                echo '<table>';
                                                                    $count = 0;
                                                                    for($i=0;$i<$totalrow;$i++){
                                                                        echo "<tr>";
                                                                            
                                                                            for($j=0;$j<$totalcol;$j++){
                                                                                echo "<td>".$table_array[$count]."</td>";
                                                                                $count++;
                                                                            }

                                                                        echo "</tr>";
                                                                    }
                                                                echo '</table>'; 
                                                            ?>
                                                        </div>
                                                    </div>
                                                    <div id="tab-general" class="ocscw_front_tab_content">
                                                        <div class="ocscw_child_div">
                                                            <?php echo get_post_field('post_content', $chart_id);
                                                             
                                                             ?> 
                                                            <img src="<?php echo get_the_post_thumbnail_url($chart_id ,'full'); ?>" />
                                                        </div>
                                                    </div>
                                                <?php
                                            }else{
                                                echo get_post_field('post_content', $chart_id);
                                                ?>
                                                    <img src="<?php echo get_the_post_thumbnail_url($chart_id ,'full'); ?>" />
                                                <?php
                                                echo '<table>';
                                                    $count = 0;
                                                    for($i=0;$i<$totalrow;$i++){
                                                        echo "<tr>";

                                                
                                                            for($j=0;$j<$totalcol;$j++){
                                                                echo "<td>".$table_array[$count]."</td>";
                                                                $count++;
                                                            }

                                                        echo "</tr>";
                                                    }
                                                echo '</table>';
                                            } 
                                        echo '</div>';
                                    echo '</div>';
                                echo '</div>';
                            echo '</div>';
                        echo '</div>';
                        ?>
                        <style type="text/css">
                            .ocscw_tableclass table {
                                border: <?php echo $tbl_border; ?>;
                            }
                            .ocscw_tableclass tr {
                                color: <?php echo $tbl_dtrow_font_clr; ?>;
                            }
                            .ocscw_tableclass tr:nth-child(even) {
                                background: <?php echo $tbl_even_row_clr; ?>;
                            }
                            .ocscw_tableclass tr:nth-child(odd) {
                                background: <?php echo $tbl_odd_row_clr; ?>;;
                            }
                            .ocscw_tableclass tr:nth-child(1) {
                                background: <?php echo $tbl_head_bg_clr; ?>;
                                color: <?php echo $tbl_head_ft_clr; ?>;
                                font-weight: 700;
                                text-transform: capitalize;
                                padding-left: 10px;
                            }
                        </style>
                        <?php
                    }
                }
            }
        }


        function ocscw_button_after() {
            $product_id = get_the_ID();
            $product    = wc_get_product( $product_id );
            $chart_ids = $this->ocscw_get_chart_ids($product_id);

            if(!empty($chart_ids)) {
                foreach ($chart_ids as $key => $chart_id) {
                    $btn_tab      = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_show', true );
                    $btn_pos      = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_pos', true );
                    if($btn_tab == "popup" && $btn_pos == "after_add_cart") {
                        $btn_lbl    = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_lbl', true );
                        $btn_ft     = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_ft_clr', true );
                        $btn_bg     = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_bg_clr', true );
                        $btn_brd_rd = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_brd_rd', true );
                        $btn_padding = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_padding', true );
                        $popup_loader = get_post_meta( $chart_id, OCSCW_PREFIX.'popup_loader', true );
                        if ($popup_loader == "loader_1" || $popup_loader == " ") {
                            $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-1.gif';
                        }elseif ($popup_loader == "loader_2") {
                            $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-2.gif';
                        }elseif ($popup_loader == "loader_3") {
                            $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-3.gif';
                        }elseif ($popup_loader == "loader_4") {
                            $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-4.gif';
                        }elseif ($popup_loader == "loader_5") {
                            $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-5.gif';
                        }elseif ($popup_loader == "loader_6") {
                            $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-6.gif';
                        }
                        $style      = "color:".$btn_ft.";background-color:".$btn_bg.";border-radius:".$btn_brd_rd."px;padding:".$btn_padding.";";
                        ?>
                        <div class="ocscw_btn">
                            <button class="ocscw_open" data-id="<?php echo $product_id; ?>" data-image="<?php echo $loader;?>" data-cid="<?php echo $chart_id; ?>" style="<?php echo $style; ?>">
                                <?php echo $btn_lbl; ?>
                            </button>
                        </div>
                        <?php
                    }
                }
            }
        }


        function ocscw_button_before() {
            $product_id = get_the_ID();
            $product    = wc_get_product( $product_id );
            $chart_ids = $this->ocscw_get_chart_ids($product_id);

            if(!empty($chart_ids)) {
                foreach ($chart_ids as $key => $chart_id) {
                    $btn_tab      = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_show', true );
                    $btn_pos      = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_pos', true );
                    if($btn_tab == "popup" && $btn_pos == "before_add_cart") {
                        $btn_lbl    = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_lbl', true );
                        $btn_ft     = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_ft_clr', true );
                        $btn_bg     = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_bg_clr', true );
                        $btn_brd_rd = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_brd_rd', true );
                        $btn_padding = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_padding', true );
                        $popup_loader = get_post_meta( $chart_id, OCSCW_PREFIX.'popup_loader', true );
                        if ($popup_loader == "loader_1" || $popup_loader == " ") {
                            $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-1.gif';
                        }elseif ($popup_loader == "loader_2") {
                            $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-2.gif';
                        }elseif ($popup_loader == "loader_3") {
                            $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-3.gif';
                        }elseif ($popup_loader == "loader_4") {
                            $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-4.gif';
                        }elseif ($popup_loader == "loader_5") {
                            $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-5.gif';
                        }elseif ($popup_loader == "loader_6") {
                            $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-6.gif';
                        }
                        $style      = "color:".$btn_ft.";background-color:".$btn_bg.";border-radius:".$btn_brd_rd."px;padding:".$btn_padding.";";
                        ?>
                        <div class="ocscw_btn">
                            <button class="ocscw_open" data-id="<?php echo $product_id; ?>" data-image="<?php echo $loader;?>" data-cid="<?php echo $chart_id; ?>" style="<?php echo $style; ?>">
                                <?php echo $btn_lbl; ?>
                            </button>
                        </div>
                        <?php
                    }
                }
            }
        }


        function ocscw_button_before_summry_text() {
            $product_id = get_the_ID();
            $product    = wc_get_product( $product_id );
            $chart_ids = $this->ocscw_get_chart_ids($product_id);

            if(!empty($chart_ids)) {
                foreach ($chart_ids as $key => $chart_id) {
                    $btn_tab      = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_show', true );
                    $btn_pos      = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_pos', true );
                    if($btn_tab == "popup" && $btn_pos == "before_summry_text") {
                        $btn_lbl    = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_lbl', true );
                        $btn_ft     = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_ft_clr', true );
                        $btn_bg     = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_bg_clr', true );
                        $btn_brd_rd = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_brd_rd', true );
                        $btn_padding = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_padding', true );
                        $popup_loader = get_post_meta( $chart_id, OCSCW_PREFIX.'popup_loader', true );
                        if ($popup_loader == "loader_1" || $popup_loader == " ") {
                            $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-1.gif';
                        }elseif ($popup_loader == "loader_2") {
                            $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-2.gif';
                        }elseif ($popup_loader == "loader_3") {
                            $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-3.gif';
                        }elseif ($popup_loader == "loader_4") {
                            $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-4.gif';
                        }elseif ($popup_loader == "loader_5") {
                            $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-5.gif';
                        }elseif ($popup_loader == "loader_6") {
                            $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-6.gif';
                        }
                        $style      = "color:".$btn_ft.";background-color:".$btn_bg.";border-radius:".$btn_brd_rd."px;padding:".$btn_padding.";";
                        ?>
                        <div class="ocscw_btn">
                            <button class="ocscw_open" data-id="<?php echo $product_id; ?>" data-image="<?php echo $loader;?>" data-cid="<?php echo $chart_id; ?>" style="<?php echo $style; ?>">
                                <?php echo $btn_lbl; ?>
                            </button>
                        </div>
                        <?php
                    }
                }
            }
        }


        function ocscw_button_aftr_prod_meta() {
            $product_id = get_the_ID();
            $product    = wc_get_product( $product_id );
            $chart_ids = $this->ocscw_get_chart_ids($product_id);

            if(!empty($chart_ids)) {
                foreach ($chart_ids as $key => $chart_id) {
                    $btn_tab      = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_show', true );
                    $btn_pos      = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_pos', true );
                    if($btn_tab == "popup" && $btn_pos == "aftr_prod_meta") {
                        $btn_lbl    = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_lbl', true );
                        $btn_ft     = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_ft_clr', true );
                        $btn_bg     = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_bg_clr', true );
                        $btn_brd_rd = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_brd_rd', true );
                        $btn_padding = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_padding', true );
                        if ($popup_loader == "loader_1" || $popup_loader == " ") {
                            $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-1.gif';
                        }elseif ($popup_loader == "loader_2") {
                            $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-2.gif';
                        }elseif ($popup_loader == "loader_3") {
                            $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-3.gif';
                        }elseif ($popup_loader == "loader_4") {
                            $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-4.gif';
                        }elseif ($popup_loader == "loader_5") {
                            $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-5.gif';
                        }elseif ($popup_loader == "loader_6") {
                            $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-6.gif';
                        }
                        $style      = "color:".$btn_ft.";background-color:".$btn_bg.";border-radius:".$btn_brd_rd."px;padding:".$btn_padding.";";
                        ?>
                        <div class="ocscw_btn">
                            <button class="ocscw_open" data-id="<?php echo $product_id; ?>" data-image="<?php echo $loader;?>" data-cid="<?php echo $chart_id; ?>" style="<?php echo $style; ?>">
                                <?php echo $btn_lbl; ?>
                            </button>
                        </div>
                        <?php
                    }
                }
            }
        }


        function ocscw_popup_div_footer() {
            ?>
            <div id="ocscw_sizechart_popup" class="ocscw_sizechart_main">
            </div>
            <div class="ocscw_schart_sidpp_overlay"></div>
            <?php
        }


        function ocscw_sizechart() {
            $product_id             = sanitize_text_field($_REQUEST['product_id']);
            $product                = wc_get_product( $product_id );
            $chart_id               = sanitize_text_field($_REQUEST['chart_id']);

            $chart_title            = get_post_meta( $chart_id, OCSCW_PREFIX.'sub_title', true );
            $size_chartdata         = get_post_meta( $chart_id, OCSCW_PREFIX.'size_chartdata', true );
            $totalrow               = get_post_meta( $chart_id, OCSCW_PREFIX.'totalrow', true );
            $totalcol               = get_post_meta( $chart_id, OCSCW_PREFIX.'totalcol', true );
            $show_tab               = get_post_meta( $chart_id, OCSCW_PREFIX.'show_tab', true );
            $chart_tab_name         = get_post_meta( $chart_id, OCSCW_PREFIX.'chart_tab_name', true);
            $dis_tab_name           = get_post_meta( $chart_id, OCSCW_PREFIX.'dis_tab_name', true);
            $tbl_head_bg_clr        = get_post_meta( $chart_id, OCSCW_PREFIX.'tbl_head_bg_clr', true);
            $tbl_head_ft_clr        = get_post_meta( $chart_id, OCSCW_PREFIX.'tbl_head_ft_clr', true);
            $tbl_even_row_clr       = get_post_meta( $chart_id, OCSCW_PREFIX.'tbl_even_row_clr', true);
            $tbl_odd_row_clr        = get_post_meta( $chart_id, OCSCW_PREFIX.'tbl_odd_row_clr', true);
            $tbl_dtrow_font_clr     = get_post_meta( $chart_id, OCSCW_PREFIX.'tbl_dtrow_font_clr', true);
            $tbl_border             = get_post_meta( $chart_id, OCSCW_PREFIX.'tbl_border', true);
            $table_array            = $size_chartdata;

            echo '<div id="ocscw_schart_popup_cls" class="ocscw_schart_popup_cls"></div>';
            echo '<div class="ocscw_tableclass">';
                echo '<div class="ocscw_sizechart_content">';
                    echo '<div class="ocscw_popup_header">';
                        echo '<h1>'.$chart_title.'</h1>';
                        echo '<span class="ocscw_popup_close">';
                        echo '<svg height="365.696pt" viewBox="0 0 365.696 365.696" width="365.696pt" xmlns="http://www.w3.org/2000/svg">
                    <path d="m243.1875 182.859375 113.132812-113.132813c12.5-12.5 12.5-32.765624 0-45.246093l-15.082031-15.082031c-12.503906-12.503907-32.769531-12.503907-45.25 0l-113.128906 113.128906-113.132813-113.152344c-12.5-12.5-32.765624-12.5-45.246093 0l-15.105469 15.082031c-12.5 12.503907-12.5 32.769531 0 45.25l113.152344 113.152344-113.128906 113.128906c-12.503907 12.503907-12.503907 32.769531 0 45.25l15.082031 15.082031c12.5 12.5 32.765625 12.5 45.246093 0l113.132813-113.132812 113.128906 113.132812c12.503907 12.5 32.769531 12.5 45.25 0l15.082031-15.082031c12.5-12.503906 12.5-32.769531 0-45.25zm0 0"/>
                </svg>';
                        echo '</span>';
                    echo '</div>';
                    echo '<div class="ocscw_popup_body">';
                        echo '<div class="ocscw_popup_data">';
                            echo '<div class="ocscw_popup_padding_div">';
                                if($show_tab == "on"){
                                    ?>
                                        <ul class="ocscw_front_tabs">
                                            <li class="tab-link current" data-tab="tab-default">
                                                <?php echo __( $chart_tab_name, 'size-chart-woocommerce' );?>
                                            </li>
                                            <li class="tab-link" data-tab="tab-general">
                                                <?php echo __( $dis_tab_name , 'size-chart-woocommerce' );?>
                                            </li>
                                        </ul>
                                        <div id="tab-default" class="ocscw_front_tab_content current">
                                            <div class="ocscw_child_div">
                                                <?php
                                                    echo '<table>';
                                                        $count = 0;
                                                        for($i=0;$i<$totalrow;$i++){
                                                            echo "<tr>";
                                                                
                                                                for($j=0;$j<$totalcol;$j++){
                                                                    echo "<td>".$table_array[$count]."</td>";
                                                                    $count++;
                                                                }

                                                            echo "</tr>";
                                                        }
                                                    echo '</table>'; 
                                                ?>
                                            </div>
                                        </div>
                                        <div id="tab-general" class="ocscw_front_tab_content">
                                            <div class="ocscw_child_div">
                                                <?php echo get_post_field('post_content', $chart_id); ?> 
                                                <img src="<?php echo get_the_post_thumbnail_url($chart_id ,'full'); ?>" />
                                            </div>
                                        </div>
                                    <?php
                                }else{
                                    echo get_post_field('post_content', $chart_id);
                                    ?>
                                        <img src="<?php echo get_the_post_thumbnail_url($chart_id ,'full'); ?>" />
                                    <?php
                                    echo '<table>';
                                        $count = 0;
                                        for($i=0;$i<$totalrow;$i++){
                                            echo "<tr>";

                                    
                                                for($j=0;$j<$totalcol;$j++){
                                                    echo "<td>".$table_array[$count]."</td>";
                                                    $count++;
                                                }

                                            echo "</tr>";
                                        }
                                    echo '</table>';
                                } 
                            echo '</div>';
                        echo '</div>';
                    echo '</div>';
                echo '</div>';
            echo '</div>';
            ?>
            <style type="text/css">
                #ocscw_sizechart_popup .ocscw_tableclass table {
                    border: <?php echo $tbl_border; ?>;
                }
                #ocscw_sizechart_popup .ocscw_tableclass tr {
                    color: <?php echo $tbl_dtrow_font_clr; ?>;
                }
                #ocscw_sizechart_popup .ocscw_tableclass table tr:nth-child(even) {
                    background: <?php echo $tbl_even_row_clr; ?>;
                }
                #ocscw_sizechart_popup .ocscw_tableclass table tr:nth-child(odd) {
                    background: <?php echo $tbl_odd_row_clr; ?>;;
                }
                #ocscw_sizechart_popup .ocscw_tableclass table tr:nth-child(1) {
                    background: <?php echo $tbl_head_bg_clr; ?>;
                    color: <?php echo $tbl_head_ft_clr; ?>;
                    font-weight: 700;
                    text-transform: capitalize;
                    padding-left: 10px;
                }
            </style>
            <?php
            
            exit();
        }


        function ocscw_sdpp_sizechart() {
            $product_id             = sanitize_text_field($_REQUEST['product_id']);
            $product                = wc_get_product( $product_id );
            $chart_id               = sanitize_text_field($_REQUEST['chart_id']);

            $chart_title            = get_post_meta( $chart_id, OCSCW_PREFIX.'sub_title', true );
            $size_chartdata         = get_post_meta( $chart_id, OCSCW_PREFIX.'size_chartdata', true );
            $totalrow               = get_post_meta( $chart_id, OCSCW_PREFIX.'totalrow', true );
            $totalcol               = get_post_meta( $chart_id, OCSCW_PREFIX.'totalcol', true );
            $show_tab               = get_post_meta( $chart_id, OCSCW_PREFIX.'show_tab', true );
            $chart_tab_name         = get_post_meta( $chart_id, OCSCW_PREFIX.'chart_tab_name', true);
            $dis_tab_name           = get_post_meta( $chart_id, OCSCW_PREFIX.'dis_tab_name', true);
            $tbl_head_bg_clr        = get_post_meta( $chart_id, OCSCW_PREFIX.'tbl_head_bg_clr', true);
            $tbl_head_ft_clr        = get_post_meta( $chart_id, OCSCW_PREFIX.'tbl_head_ft_clr', true);
            $tbl_even_row_clr       = get_post_meta( $chart_id, OCSCW_PREFIX.'tbl_even_row_clr', true);
            $tbl_odd_row_clr        = get_post_meta( $chart_id, OCSCW_PREFIX.'tbl_odd_row_clr', true);
            $tbl_dtrow_font_clr     = get_post_meta( $chart_id, OCSCW_PREFIX.'tbl_dtrow_font_clr', true);
            $tbl_border             = get_post_meta( $chart_id, OCSCW_PREFIX.'tbl_border', true);
            $table_array            = $size_chartdata;

            echo '<div class="ocscw_schart_sdpopup_close">';
            echo '<svg height="365.696pt" viewBox="0 0 365.696 365.696" width="365.696pt" xmlns="http://www.w3.org/2000/svg">
                    <path d="m243.1875 182.859375 113.132812-113.132813c12.5-12.5 12.5-32.765624 0-45.246093l-15.082031-15.082031c-12.503906-12.503907-32.769531-12.503907-45.25 0l-113.128906 113.128906-113.132813-113.152344c-12.5-12.5-32.765624-12.5-45.246093 0l-15.105469 15.082031c-12.5 12.503907-12.5 32.769531 0 45.25l113.152344 113.152344-113.128906 113.128906c-12.503907 12.503907-12.503907 32.769531 0 45.25l15.082031 15.082031c12.5 12.5 32.765625 12.5 45.246093 0l113.132813-113.132812 113.128906 113.132812c12.503907 12.5 32.769531 12.5 45.25 0l15.082031-15.082031c12.5-12.503906 12.5-32.769531 0-45.25zm0 0"/>
                </svg>';
            echo '</div>';
            echo '<div class="ocscw_sdpp_table">';
                echo '<div class="ocscw_sdpp_szchart_content">';
                    echo '<div class="ocscw_sdpp_popup_header">';
                        echo '<h1>'.$chart_title.'</h1>';
                    echo '</div>';
                    echo '<div class="ocscw_sdpp_popup_body">';
                        echo '<div class="ocscw_popup_data">';
                            echo '<div class="ocscw_sdpp_padding_div">';
                                if($show_tab == "on") {
                                    ?>
                                        <ul class="ocscw_sdpp_front_tabs">
                                            <li class="tab-link current" data-tab="tab-default">
                                                <?php echo __( $chart_tab_name, 'size-chart-woocommerce' );?>
                                            </li>
                                            <li class="tab-link" data-tab="tab-general">
                                                <?php echo __( $dis_tab_name , 'size-chart-woocommerce' );?>
                                            </li>
                                        </ul>
                                        <div id="tab-default" class="ocscw_sdpp_frtab_content current">
                                            <div class="ocscw_sdpp_child_div">
                                                <?php
                                                    echo '<table>';
                                                        $count = 0;
                                                        for($i=0;$i<$totalrow;$i++){
                                                            echo "<tr>";
                                                                
                                                                for($j=0;$j<$totalcol;$j++){
                                                                    echo "<td>".$table_array[$count]."</td>";
                                                                    $count++;
                                                                }

                                                            echo "</tr>";
                                                        }
                                                    echo '</table>'; 
                                                ?>
                                            </div>
                                        </div>
                                        <div id="tab-general" class="ocscw_sdpp_frtab_content">
                                            <div class="ocscw_child_div">
                                                <?php echo get_post_field('post_content', $chart_id); ?> 
                                                <img src="<?php echo get_the_post_thumbnail_url($chart_id ,'full'); ?>" />
                                            </div>
                                        </div>
                                    <?php
                                } else {
                                    echo get_post_field('post_content', $chart_id);
                                    ?>
                                        <img src="<?php echo get_the_post_thumbnail_url($chart_id ,'full'); ?>" />
                                    <?php
                                    echo '<table>';
                                        $count = 0;
                                        for($i=0;$i<$totalrow;$i++) {
                                            echo "<tr>";

                                    
                                                for($j=0;$j<$totalcol;$j++){
                                                    echo "<td>".$table_array[$count]."</td>";
                                                    $count++;
                                                }

                                            echo "</tr>";
                                        }
                                    echo '</table>';
                                } 
                            echo '</div>';
                        echo '</div>';
                    echo '</div>';
                echo '</div>';
            echo '</div>';
            ?>
            <style type="text/css">
                .ocscw_sdpp_child_div table {
                    border: <?php echo $tbl_border; ?>;
                }
                .ocscw_sdpp_table tr {
                    color: <?php echo $tbl_dtrow_font_clr; ?>;
                }
                .ocscw_sdpp_table tr:nth-child(even) {
                    background: <?php echo $tbl_even_row_clr; ?>;
                }
                .ocscw_sdpp_table tr:nth-child(odd) {
                    background: <?php echo $tbl_odd_row_clr; ?>;;
                }
                .ocscw_sdpp_table tr:nth-child(1) {
                    background: <?php echo $tbl_head_bg_clr; ?>;
                    color: <?php echo $tbl_head_ft_clr; ?>;
                    font-weight: 700;
                    text-transform: capitalize;
                    padding-left: 10px;
                }
            </style>
            <?php

            exit;
        }

        function ocscw_custom_shortcode_button( $atts = '' ) {  

            $value = shortcode_atts( array(
                'product_id' => ''
            ), $atts );
            if (!empty($value['product_id'])) {
                $pro_id = $value['product_id'];
            }else{
                $pro_id = get_the_ID();
            }

            $chart_ids = $this->ocscw_get_chart_ids($pro_id);
            ob_start();

            if(!empty($chart_ids)) {

                foreach ($chart_ids as $key => $chart_id) {
                    $szchartpp_shop_enbl      = get_post_meta( $chart_id, OCSCW_PREFIX.'szchartpp_shop_enbl', true );
                    $btn_tab      = get_post_meta( $chart_id, OCSCW_PREFIX.'shop_btn_show', true );
                    $alw_mobile      = get_post_meta( $chart_id, OCSCW_PREFIX.'alw_mobile', true );
                    $alw_gust_usr = get_post_meta( $chart_id, OCSCW_PREFIX.'alw_gust_usr', true );
                     //print_r($alw_mobile);
                    // exit;
                    $showCouponField ='true';
                    if(wp_is_mobile()) {
                        if($alw_mobile != "on") {
                            $showCouponField = 'false';
                        }
                    }
                    if( $showCouponField == 'true'){
                        $btn_lbl    = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_lbl', true );
                        $btn_ft     = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_ft_clr', true );
                        $btn_bg     = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_bg_clr', true );
                        $btn_brd_rd = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_brd_rd', true );
                        $btn_padding = get_post_meta( $chart_id, OCSCW_PREFIX.'btn_padding', true );
                        $popup_loader = get_post_meta( $chart_id, OCSCW_PREFIX.'popup_loader', true );
                        if ($popup_loader == "loader_1" || empty($popup_loader)) {
                            $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-1.gif';
                        }elseif ($popup_loader == "loader_2") {
                            $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-2.gif';
                        }elseif ($popup_loader == "loader_3") {
                            $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-3.gif';
                        }elseif ($popup_loader == "loader_4") {
                            $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-4.gif';
                        }elseif ($popup_loader == "loader_5") {
                            $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-5.gif';
                        }elseif ($popup_loader == "loader_6") {
                            $loader = OCSCW_PLUGIN_DIR.'/includes/images/loader-6.gif';
                        }
                        $style      = "color:".$btn_ft.";background-color:".$btn_bg.";border-radius:".$btn_brd_rd."px;padding:".$btn_padding.";";
                        if($alw_gust_usr != "on") {
                            if ($btn_tab == "popup") {
                                ?>
                                <div class="ocscw_btn">
                                    <button class="ocscw_open" data-id="<?php echo $pro_id; ?>" data-image="<?php echo $loader;?>" data-cid="<?php echo $chart_id; ?>" style="<?php echo $style; ?>">
                                        <?php echo $btn_lbl; ?>
                                    </button>
                                </div>
                                <?php
                            }
                        } else {
                            if(is_user_logged_in()) {
                                if($btn_tab == "popup") {
                                    ?>
                                    <div class="ocscw_btn">
                                        <button class="ocscw_open" data-id="<?php echo $pro_id; ?>" data-image="<?php echo $loader;?>" data-cid="<?php echo $chart_id; ?>" style="<?php echo $style; ?>">
                                            <?php echo $btn_lbl; ?>
                                        </button>
                                    </div>
                                    <?php
                                }
                            }
                        }
                    }
                }
            }
            $content = ob_get_clean();

            return $content;
        }

        function init() {
            add_action( 'wp_head', array( $this, 'ocscw_header_action' ));
            add_action( 'wp_footer', array( $this, 'ocscw_popup_div_footer' ));
            add_action( 'wp_ajax_ocscw_sizechart', array( $this, 'ocscw_sizechart' ));
            add_action( 'wp_ajax_nopriv_ocscw_sizechart', array( $this, 'ocscw_sizechart' ));
            add_action( 'wp_ajax_ocscw_sdpp_sizechart', array( $this, 'ocscw_sdpp_sizechart' ));
            add_action( 'wp_ajax_nopriv_ocscw_sdpp_sizechart', array( $this, 'ocscw_sdpp_sizechart' ));
            add_shortcode('ocscw_buttons', array( $this, 'ocscw_custom_shortcode_button'));
        }

        public static function instance() {
            if (!isset(self::$instance)) {
                self::$instance = new self();
                self::$instance->init();
            }
            return self::$instance;
        }

    }

    OCSCW_front::instance();
}