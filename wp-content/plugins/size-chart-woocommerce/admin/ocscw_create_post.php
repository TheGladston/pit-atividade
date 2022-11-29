<?php

if (!defined('ABSPATH'))
  exit;

if (!class_exists('OCSCW_menu')) {

    class OCSCW_menu {

        protected static $instance;
     
        function OCSCW_create_menu() {
            $post_type = 'ocscw_size_chart';
            $singular_name = 'Size Chart';
            $plural_name = 'Size Charts';
            $slug = 'ocscw_size_chart';
            $labels = array(
                'name'               => _x( $plural_name, 'post type general name', 'size-chart-woocommerce' ),
                'singular_name'      => _x( $singular_name, 'post type singular name', 'size-chart-woocommerce' ),
                'menu_name'          => _x( $singular_name, 'admin menu name', 'size-chart-woocommerce' ),
                'name_admin_bar'     => _x( $singular_name, 'add new name on admin bar', 'size-chart-woocommerce' ),
                'add_new'            => __( 'Add New', 'size-chart-woocommerce' ),
                'add_new_item'       => __( 'Add New '.$singular_name, 'size-chart-woocommerce' ),
                'new_item'           => __( 'New '.$singular_name, 'size-chart-woocommerce' ),
                'edit_item'          => __( 'Edit '.$singular_name, 'size-chart-woocommerce' ),
                'view_item'          => __( 'View '.$singular_name, 'size-chart-woocommerce' ),
                'all_items'          => __( 'All '.$plural_name, 'size-chart-woocommerce' ),
                'search_items'       => __( 'Search '.$plural_name, 'size-chart-woocommerce' ),
                'parent_item_colon'  => __( 'Parent '.$plural_name.':', 'size-chart-woocommerce' ),
                'not_found'          => __( 'No Size Chart found.', 'size-chart-woocommerce' ),
                'not_found_in_trash' => __( 'No Size Chart found in Trash.', 'size-chart-woocommerce' )
            );

            $args = array(
                'labels'             => $labels,
                'description'        => __( 'Description', 'size-chart-woocommerce' ),
                'public'             => false,
                'publicly_queryable' => true,
                'show_ui'            => true,
                'show_in_menu'       => true,
                'query_var'          => true,
                'rewrite'            => array( 'slug' => $slug ),
                'capability_type'    => 'post',
                'has_archive'        => true,
                'hierarchical'       => false,
                'menu_position'      => null,
                'supports'           => array( 'title', 'editor', 'thumbnail' ),
                'menu_icon'          => 'dashicons-chart-pie'
            );
            register_post_type( $post_type, $args );
        }

      
        function OCSCW_add_meta_box() {
            add_meta_box(
                'OCSCW_metabox',
                __( 'All Size Chart Settings', 'size-chart-woocommerce' ),
                array($this, 'OCSCW_metabox_cb'),
                'ocscw_size_chart',
                'normal'
            );
        }


        function OCSCW_metabox_cb( $post ) {
            wp_nonce_field( 'ocscw_meta_save', 'ocscw_meta_save_nounce' );
            ?> 
            <div class="ocscw-container">
                <ul class="tabs">
                    <li class="tab-link current" data-tab="tab-default">
                        <?php echo __( 'Chart', 'size-chart-woocommerce' );?>
                    </li>
                    <li class="tab-link" data-tab="tab-general">
                        <?php echo __( 'Chart Show Settings', 'size-chart-woocommerce' );?>
                    </li>
                    <li class="tab-link" data-tab="tab-table">
                        <?php echo __( 'Table Settings', 'size-chart-woocommerce' );?>
                    </li>
                    <li class="tab-link" data-tab="tab-tab">
                        <?php echo __( 'Tab Settings', 'size-chart-woocommerce' );?>
                    </li>
                    <a class="ocscw_support_link" href="https://oceanwebguru.com/contact-us/" target="_blank"><?php echo __( 'Support', 'size-chart-woocommerce' );?></a>
                </ul>
                <div id="tab-default" class="tab-content current">
                    <h2><?php echo __( "Create Chart", 'size-chart-woocommerce' );?></h2>
                    <div class="ocscw_child_div">
                        <p class="ocscw_shortcode">Use this shortcode <strong>[ocscw_buttons product_id=id]</strong> (Ex. id = 1,2,3,4,5.... product ids) for any product.</p>
                        <?php
                            $table = get_post_meta( $post->ID, OCSCW_PREFIX.'size_chartdata', true); 
                            $table_array = $table;

                            if(!empty($table_array[0])) {

                                $totalrow = get_post_meta( $post->ID, OCSCW_PREFIX.'totalrow', true);
                                $totalcol = get_post_meta( $post->ID, OCSCW_PREFIX.'totalcol', true);
                                
                                echo '<table class="ocscw_chart_tbl">';
                                    echo '<input type="hidden" name="totalrow" value="'.$totalrow.'">';
                                    echo '<input type="hidden" name="totalcol" value="'.$totalcol.'">';

                                    $count = 0;

                                    $tr = '<tr><td><a class="addcolumn"><img src= "'. OCSCW_PLUGIN_DIR . '/includes/images/plus.png"></a></td>';

                                        for($j=0; $j<$totalcol-1; $j++) {
                                            $tr .='<td><a class="addcolumn"><img src= "'. OCSCW_PLUGIN_DIR . '/includes/images/plus.png"></a><a class="deletecolumn"><img src= "'. OCSCW_PLUGIN_DIR . '/includes/images/delete.png"></a></td>';   
                                        }
                                    $tr .= '<td></td></tr>';

                                    for($i=0; $i<$totalrow; $i++) {
                                        
                                        $tr .= "<tr>";
                                            $td = "";

                                            for($j=0; $j<$totalcol; $j++) {
                                                $td .='<td><input type="text" name="size_chartdata[]" value="'.htmlspecialchars($table_array[$count]).'"></td>';
                                                $count++;
                                            }
                                            if($count == $totalcol) {
                                                $td .='<td><a class="addrow"><img src= "'. OCSCW_PLUGIN_DIR . '/includes/images/plus.png"></a></td>';
                                            }else{
                                                $td .='<td><a class="addrow"><img src= "'. OCSCW_PLUGIN_DIR . '/includes/images/plus.png"></a><a class="deleterow"><img src= "'. OCSCW_PLUGIN_DIR . '/includes/images/delete.png"></a></td>';
                                            }
                                            
                                            $tr .= $td;
                                            
                                        $tr .= "</tr>";
                                    }
                                    echo $tr;
                                echo '</table>';
                            } else {
                                ?>
                                <table class="ocscw_chart_tbl">
                                    <input type="hidden" name="totalrow">
                                    <input type="hidden" name="totalcol">
                                    <tr>
                                        <td>
                                            <a class="addcolumn">
                                                <img src= " <?php echo OCSCW_PLUGIN_DIR . '/includes/images/plus.png' ?>">
                                            </a>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td><input type="text" name="size_chartdata[]"></td>
                                        <td>
                                            <a class="addrow">
                                                <img src= " <?php echo OCSCW_PLUGIN_DIR . '/includes/images/plus.png' ?>">
                                            </a>   
                                        </td>
                                    </tr>
                                </table> 
                                <?php
                            }
                        ?>
                    </div>
                </div>
                <div id="tab-general" class="tab-content">
                    <h2><?php echo __( "Popup Loader Setting", 'size-chart-woocommerce' );?></h2>
                    <div class="ocscw_child_div">
                        <table>
                            <tr>
                                <th>Popup Loader</th>
                                <td>
                                    <div class="loader_div">
                                        <?php $popup_loader = get_post_meta( $post->ID, OCSCW_PREFIX.'popup_loader', true); ?>
                                        <input type="radio" name="popup_loader" value="loader_1" <?php if($popup_loader == 'loader_1'){echo "checked";}?>>
                                        <img src="<?php echo OCSCW_PLUGIN_DIR;?>/includes/images/loader-1.gif" class="loader_admin">
                                        <input type="radio" name="popup_loader" value="loader_2" <?php if($popup_loader == 'loader_2'){echo "checked";}?>>
                                        <img src="<?php echo OCSCW_PLUGIN_DIR;?>/includes/images/loader-2.gif" class="loader_admin">
                                        <input type="radio" name="popup_loader" value="loader_3" <?php if($popup_loader == 'loader_3'){echo "checked";}?>>
                                        <img src="<?php echo OCSCW_PLUGIN_DIR;?>/includes/images/loader-3.gif" class="loader_admin">
                                        <input type="radio" name="popup_loader" value="loader_4" <?php if($popup_loader == 'loader_4'){echo "checked";}?>>
                                        <img src="<?php echo OCSCW_PLUGIN_DIR;?>/includes/images/loader-4.gif" class="loader_admin">
                                        <input type="radio" name="popup_loader" value="loader_5" <?php if($popup_loader == 'loader_5'){echo "checked";}?>>
                                        <img src="<?php echo OCSCW_PLUGIN_DIR;?>/includes/images/loader-5.gif" class="loader_admin">
                                        <input type="radio" name="popup_loader" value="loader_6" <?php if($popup_loader == 'loader_6'){echo "checked";}?>>
                                        <img src="<?php echo OCSCW_PLUGIN_DIR;?>/includes/images/loader-6.gif" class="loader_admin">
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <h2><?php echo __( "Single Product Page Setting", 'size-chart-woocommerce' );?></h2>
                    <div class="ocscw_child_div">
                        <table>
                            <tr>
                                <th>Apply Chart to All Products</th>
                                <?php $szchartpp_aply_all = get_post_meta( $post->ID, OCSCW_PREFIX.'szchartpp_aply_all', true); ?>
                                <td>
                                    <input type="checkbox" name="szchartpp_aply_all" value="enable" <?php if($szchartpp_aply_all == "enable") { echo "checked"; } ?>>
                                    <label>Apply to All</label>
                                </td>
                            </tr>
                            <tr>
                                <th>Show Chart</th>
                                <?php $btn_show = get_post_meta( $post->ID, OCSCW_PREFIX.'btn_show', true); ?>
                                <td>
                                    <div class="ocscw_swcrt_select">
                                        <input type="radio" name="btn_show" value="tab" <?php if($btn_show == "tab"){ echo "checked"; } ?>>In Product Tab    
                                    </div>
                                    <div class="ocscw_swcrt_select">
                                        <input type="radio" name="btn_show" value="popup" <?php if($btn_show == "popup"){ echo "checked"; } ?>>Popup
                                    </div>
                                    <div class="ocscw_swcrt_select">
                                        <input type="radio" name="btn_show" value="sidepopup" disabled="">Sidebar Popup
                                        <label class="ocscw_pro_link">Only available in pro version <a href="https://oceanwebguru.com/shop/size-chart-woocommerce-pro/" target="_blank">link</a></label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Chart Heading Text</th>
                                <?php $sub_title = get_post_meta( $post->ID, OCSCW_PREFIX.'sub_title', true); ?>
                                <td>
                                    <input type="text" name="sub_title" value="<?php echo $sub_title; ?>">
                                </td>
                            </tr> 
                        </table>
                    </div>
                    <div class="ocscw_tab_div" style="display: none;">
                        <h2><?php echo __( "Product Tab Setting", 'size-chart-woocommerce' );?></h2>
                        <div class="ocscw_child_div">
                            <table>
                                <tr>
                                    <th>Tab Label</th>
                                    <?php $tab_lbl = get_post_meta( $post->ID, OCSCW_PREFIX.'tab_lbl', true); ?>
                                    <td>
                                        <input type="text" name="tab_lbl" value="<?php echo $tab_lbl; ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tab Priority</th>
                                    <?php $tab_pririty = get_post_meta( $post->ID, OCSCW_PREFIX.'tab_pririty', true); ?>
                                    <td>
                                        <input type="text" name="tab_pririty" value="<?php echo $tab_pririty; ?>">
                                        <span class="ocscw_desc">Lower number means higher position in the order.</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="ocscw_popup_div" style="display: none;">
                        <h2><?php echo __( "Popup Button Setting", 'size-chart-woocommerce' );?></h2>
                        <div class="ocscw_child_div">
                            <table>
                                <tr>
                                    <th>Button Label</th>
                                    <?php $btn_lbl = get_post_meta( $post->ID, OCSCW_PREFIX.'btn_lbl', true); ?>
                                    <td>
                                        <input type="text" name="btn_lbl" value="<?php echo $btn_lbl; ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Button Text Color</th>
                                    <?php
                                    $btn_ft_clr = get_post_meta( $post->ID, OCSCW_PREFIX.'btn_ft_clr', true);
                                    
                                    if($btn_ft_clr == '') {
                                        $btn_ft_clr = '#ffffff';
                                    }
                                    ?>
                                    <td>
                                        <input type="text" class="color-picker" data-alpha="true" data-default-color="<?php echo $btn_ft_clr; ?>" name="btn_ft_clr" value="<?php echo $btn_ft_clr; ?>"/>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Button Border Radius</th>
                                    <?php $btn_brd_rd = get_post_meta( $post->ID, OCSCW_PREFIX.'btn_brd_rd', true); ?>
                                    <td>
                                        <input type="number" name="btn_brd_rd" value="<?php echo $btn_brd_rd; ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Button Background Color</th>
                                    <?php
                                    $btn_bg_clr = get_post_meta( $post->ID, OCSCW_PREFIX.'btn_bg_clr', true);

                                    if($btn_bg_clr == '') {
                                        $btn_bg_clr = '#000000';
                                    }
                                    ?>
                                    <td>
                                        <input type="text" class="color-picker" data-alpha="true" data-default-color="<?php echo $btn_bg_clr; ?>" name="btn_bg_clr" value="<?php echo $btn_bg_clr; ?>"/>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Button Position</th>
                                    <?php $btn_pos = get_post_meta( $post->ID, OCSCW_PREFIX.'btn_pos', true); ?>
                                    <td>
                                        <select name="btn_pos">
                                            <option value="before_add_cart" <?php if($btn_pos == "before_add_cart") { echo "selected"; } ?>>Before Add To Cart</option>
                                            <option value="after_add_cart" <?php if($btn_pos == "after_add_cart") { echo "selected"; } ?>>After Add To Cart</option>
                                            <option value="before_summry_text" <?php if($btn_pos == "before_summry_text") { echo "selected"; } ?>>Before Summary Text</option>
                                            <option value="aftr_prod_meta" <?php if($btn_pos == "aftr_prod_meta") { echo "selected"; } ?>>After Product Meta</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Button Padding</th>
                                    <?php $btn_padding = get_post_meta( $post->ID, OCSCW_PREFIX.'btn_padding', true); ?>
                                    <td>
                                        <input type="text" name="btn_padding" value="<?php echo $btn_padding; ?>"/>
                                        <span class="ocscw_desc">add padding like ex. <strong>10px 15px</strong></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>



                    <h2><?php echo __( "Shop Page Setting", 'size-chart-woocommerce' );?></h2>
                    <div class="ocscw_child_div">
                        <table>
                            <tr>
                                <th>Show Size Chart Popup Button on Shop Page</th>
                                <?php
                                $szchartpp_shop_enbl = get_post_meta( $post->ID, OCSCW_PREFIX.'szchartpp_shop_enbl', true);
                                ?>
                                <td>
                                    <input type="checkbox" name="szchartpp_shop_enbl" value="enable" <?php if($szchartpp_shop_enbl == "enable") { echo "checked"; } ?>>
                                    <label>Enable</label>
                                </td>
                            </tr>
                            <tr>
                                <th>Show Chart</th>
                                <?php $shop_btn_show = get_post_meta( $post->ID, OCSCW_PREFIX.'shop_btn_show', true); ?>
                                <td>
                                    <div class="ocscw_swcrt_select">
                                        <input type="radio" name="shop_btn_show" value="popup" <?php if($shop_btn_show == "popup") { echo "checked"; } ?>>Popup
                                    </div>
                                    <div class="ocscw_swcrt_select">
                                        <input type="radio" name="shop_btn_show" value="sidepopup" disabled="">Sidebar Popup
                                        <label class="ocscw_pro_link">Only available in pro version <a href="https://oceanwebguru.com/shop/size-chart-woocommerce-pro/" target="_blank">link</a></label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Button Position</th>
                                <?php $shop_btn_pos = get_post_meta( $post->ID, OCSCW_PREFIX.'shop_btn_pos', true); ?>
                                <td>
                                    <div class="ocscw_swcrt_select">
                                        <input type="radio" name="shop_btn_pos" value="before_add_cart" <?php if($shop_btn_pos == "before_add_cart") { echo "checked"; } ?>>Before Add To Cart
                                    </div>
                                    <div class="ocscw_swcrt_select">
                                        <input type="radio" name="shop_btn_pos" value="after_add_cart" disabled="">After Add To Cart
                                        <label class="ocscw_pro_link">Only available in pro version <a href="https://oceanwebguru.com/shop/size-chart-woocommerce-pro/" target="_blank">link</a></label>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>



                    <h2><?php echo __( "User Setting", 'size-chart-woocommerce' );?></h2>
                    <div class="ocscw_child_div">
                        <table>
                            <tr>
                                <th>Show Chart only to Logged in Users</th>
                                <?php
                                ?>
                                <td>
                                    <input type="checkbox" name="alw_gust_usr" disabled="">
                                    <strong>Enable</strong>
                                    <label class="ocscw_pro_link">Only available in pro version <a href="https://oceanwebguru.com/shop/size-chart-woocommerce-pro/" target="_blank">link</a></label>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div id="tab-table" class="tab-content">
                    <h2><?php echo __( "table Setting", 'size-chart-woocommerce' );?></h2>
                    <div class="ocscw_child_div">
                        <table>
                            <tr>
                                <th>Table Head Background Color</th>
                                <?php
                                $tbl_head_bg_clr = get_post_meta( $post->ID, OCSCW_PREFIX.'tbl_head_bg_clr', true); 
                                if($tbl_head_bg_clr == '') {
                                    $tbl_head_bg_clr = '#e9ebed';
                                }
                                ?>
                                <td class="color_disable">
                                    <input type="text" class="color-picker" data-alpha="true" data-default-color="<?php echo $tbl_head_bg_clr; ?>" name="tbl_head_bg_clr" value="<?php echo $tbl_head_bg_clr; ?>"/>
                                    <label class="ocscw_pro_link">Only available in pro version <a href="https://oceanwebguru.com/shop/size-chart-woocommerce-pro/" target="_blank">link</a></label>
                                </td>
                            </tr>
                            <tr>
                                <th>Table Head Font Color</th>
                                <?php
                                $tbl_head_ft_clr = get_post_meta( $post->ID, OCSCW_PREFIX.'tbl_head_ft_clr', true); 
                                if($tbl_head_ft_clr == '') {
                                    $tbl_head_ft_clr = '#000000';
                                }
                                ?>
                                <td class="color_disable">
                                    <input type="text" class="color-picker" data-alpha="true" data-default-color="<?php echo $tbl_head_ft_clr; ?>" name="tbl_head_ft_clr" value="<?php echo $tbl_head_ft_clr; ?>"/>
                                    <label class="ocscw_pro_link">Only available in pro version <a href="https://oceanwebguru.com/shop/size-chart-woocommerce-pro/" target="_blank">link</a></label>
                                </td>
                            </tr>
                            <tr>
                                <th>Table Even Row Color</th>
                                <?php
                                $tbl_even_row_clr = get_post_meta( $post->ID, OCSCW_PREFIX.'tbl_even_row_clr', true);
                                if($tbl_even_row_clr == '') {
                                    $tbl_even_row_clr = '#d6d8db';
                                }
                                ?>
                                <td class="color_disable">
                                    <input type="text" class="color-picker" data-alpha="true" data-default-color="<?php echo $tbl_even_row_clr; ?>" name="tbl_even_row_clr" value="<?php echo $tbl_even_row_clr; ?>"/>
                                    <label class="ocscw_pro_link">Only available in pro version <a href="https://oceanwebguru.com/shop/size-chart-woocommerce-pro/" target="_blank">link</a></label>
                                </td>
                            </tr>
                            <tr>
                                <th>Table Odd Raw Color</th>
                                <?php
                                $tbl_odd_row_clr = get_post_meta( $post->ID, OCSCW_PREFIX.'tbl_odd_row_clr', true); 
                                if($tbl_odd_row_clr == '') {
                                    $tbl_odd_row_clr = '#e9ebed';
                                }
                                ?>
                                <td class="color_disable">
                                    <input type="text" class="color-picker" data-alpha="true" data-default-color="<?php echo $tbl_odd_row_clr; ?>" name="tbl_odd_row_clr" value="<?php echo $tbl_odd_row_clr; ?>"/>
                                    <label class="ocscw_pro_link">Only available in pro version <a href="https://oceanwebguru.com/shop/size-chart-woocommerce-pro/" target="_blank">link</a></label>
                                </td>
                            </tr>
                            <tr>
                                <th>Table Data Row Font Color</th>
                                <?php
                                $tbl_dtrow_font_clr = get_post_meta( $post->ID, OCSCW_PREFIX.'tbl_dtrow_font_clr', true); 
                                if($tbl_dtrow_font_clr == '') {
                                    $tbl_dtrow_font_clr = '#000000';
                                }
                                ?>
                                <td class="color_disable">
                                    <input type="text" class="color-picker" data-alpha="true" data-default-color="<?php echo $tbl_dtrow_font_clr; ?>" name="tbl_dtrow_font_clr" value="<?php echo $tbl_dtrow_font_clr; ?>"/>
                                    <label class="ocscw_pro_link">Only available in pro version <a href="https://oceanwebguru.com/shop/size-chart-woocommerce-pro/" target="_blank">link</a></label>
                                </td>
                            </tr>
                            <tr>
                                <th>Table Border</th>
                                <?php
                                $tbl_border = get_post_meta( $post->ID, OCSCW_PREFIX.'tbl_border', true);
                                ?>
                                <td>
                                    <input type="text" name="tbl_border" value="<?php echo $tbl_border; ?>" placeholder="2px solid #ff0000"/>
                                    <span class="ocscw_desc">add border like ex. <strong>2px solid #ff0000</strong></span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div id="tab-tab" class="tab-content">
                    <h2><?php echo __( "Tab Setting", 'size-chart-woocommerce' );?></h2>
                    <div class="ocscw_child_div">
                        <table>
                            <tr>
                                <th>Show Tab Wise Content</th>
                                <?php $show_tab = get_post_meta( $post->ID, OCSCW_PREFIX.'show_tab', true); ?>
                                <td>
                                    <input type="checkbox" name="show_tab" <?php if($show_tab == "on"){ echo "checked"; } ?>>
                                </td>
                            </tr>
                            <tr>
                                <th>Chart Tab Name</th>
                                <?php $chart_tab_name = get_post_meta( $post->ID, OCSCW_PREFIX.'chart_tab_name', true); ?>
                                <td>
                                    <input type="text" name="chart_tab_name" value="<?php echo $chart_tab_name; ?>">
                                </td>
                            </tr>
                            <tr>
                                <th>Description Tab Name</th>
                                <?php $dis_tab_name = get_post_meta( $post->ID, OCSCW_PREFIX.'dis_tab_name', true); ?>
                                <td>
                                    <input type="text" name="dis_tab_name" value="<?php echo $dis_tab_name; ?>">
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <?php
        }


        function OCSCW_recursive_sanitize_text_field($array) {
            if(!empty($array)) {
                foreach ( $array as $key => &$value ) {
                    if ( is_array( $value ) ) {
                        $value = $this->OCSCW_recursive_sanitize_text_field($value);
                    } else {
                        $value = sanitize_text_field( $value );
                    }
                }
            }
            return $array;
        }


        function OCSCW_meta_save( $post_id, $post ) {
         
            if ($post->post_type != 'ocscw_size_chart') { return; }
         
            if ( !current_user_can( 'edit_post', $post_id )) return;
            $is_autosave = wp_is_post_autosave($post_id);
            $is_revision = wp_is_post_revision($post_id);
            $is_valid_nonce = (isset($_POST['ocscw_meta_save_nounce']) && wp_verify_nonce( $_POST['ocscw_meta_save_nounce'], 'ocscw_meta_save' )? 'true': 'false');

            if ( $is_autosave || $is_revision || !$is_valid_nonce ) return;

            $size_chartdata     = $this->OCSCW_recursive_sanitize_text_field( $_REQUEST['size_chartdata'] );
            $totalrow           = sanitize_text_field( $_REQUEST['totalrow'] );
            $totalcol           = sanitize_text_field( $_REQUEST['totalcol'] );
            $btn_show           = sanitize_text_field( $_REQUEST['btn_show'] );
            $shop_btn_show      = sanitize_text_field( $_REQUEST['shop_btn_show'] );
            $sub_title          = sanitize_text_field( $_REQUEST['sub_title'] );
            $tab_lbl            = sanitize_text_field( $_REQUEST['tab_lbl'] );
            $tab_pririty        = sanitize_text_field( $_REQUEST['tab_pririty'] );
            $btn_lbl            = sanitize_text_field( $_REQUEST['btn_lbl'] );
            $btn_ft_clr         = sanitize_text_field( $_REQUEST['btn_ft_clr'] );
            $btn_bg_clr         = sanitize_text_field( $_REQUEST['btn_bg_clr'] );
            $btn_pos            = sanitize_text_field( $_REQUEST['btn_pos'] );
            $shop_btn_pos       = sanitize_text_field( $_REQUEST['shop_btn_pos'] );
            $btn_padding        = sanitize_text_field( $_REQUEST['btn_padding'] );
            $tbl_head_bg_clr    = sanitize_text_field( $_REQUEST['tbl_head_bg_clr'] );
            $tbl_head_ft_clr    = sanitize_text_field( $_REQUEST['tbl_head_ft_clr'] );
            $tbl_even_row_clr   = sanitize_text_field( $_REQUEST['tbl_even_row_clr'] );
            $tbl_odd_row_clr    = sanitize_text_field( $_REQUEST['tbl_odd_row_clr'] );
            $tbl_dtrow_font_clr = sanitize_text_field( $_REQUEST['tbl_dtrow_font_clr'] );
            $tbl_border         = sanitize_text_field( $_REQUEST['tbl_border'] );
            $show_tab           = sanitize_text_field( $_REQUEST['show_tab'] );
            $chart_tab_name     = sanitize_text_field( $_REQUEST['chart_tab_name'] );
            $dis_tab_name       = sanitize_text_field( $_REQUEST['dis_tab_name'] );
            $btn_brd_rd         = sanitize_text_field( $_REQUEST['btn_brd_rd'] );
            $popup_loader       = sanitize_text_field( $_REQUEST['popup_loader'] );
            
            if(isset($_REQUEST['szchartpp_shop_enbl']) && $_REQUEST['szchartpp_shop_enbl'] == 'enable') {
                $szchartpp_shop_enbl = sanitize_text_field($_REQUEST['szchartpp_shop_enbl']); 
            } else {
                $szchartpp_shop_enbl = 'disable';
            }

            if(isset($_REQUEST['szchartpp_aply_all']) && $_REQUEST['szchartpp_aply_all'] == 'enable') {
                $szchartpp_aply_all = sanitize_text_field($_REQUEST['szchartpp_aply_all']); 
            } else {
                $szchartpp_aply_all = 'disable';
            }
            
            update_post_meta( $post_id, OCSCW_PREFIX.'szchartpp_aply_all', $szchartpp_aply_all);
            update_post_meta( $post_id, OCSCW_PREFIX.'size_chartdata', $size_chartdata);
            update_post_meta( $post_id, OCSCW_PREFIX.'totalrow', $totalrow );
            update_post_meta( $post_id, OCSCW_PREFIX.'totalcol', $totalcol );
            update_post_meta( $post_id, OCSCW_PREFIX.'btn_show', $btn_show );
            update_post_meta( $post_id, OCSCW_PREFIX.'shop_btn_show', $shop_btn_show );
            update_post_meta( $post_id, OCSCW_PREFIX.'sub_title', $sub_title );
            update_post_meta( $post_id, OCSCW_PREFIX.'tab_lbl', $tab_lbl );
            update_post_meta( $post_id, OCSCW_PREFIX.'tab_pririty', $tab_pririty );
            update_post_meta( $post_id, OCSCW_PREFIX.'btn_lbl', $btn_lbl );
            update_post_meta( $post_id, OCSCW_PREFIX.'btn_ft_clr', $btn_ft_clr );
            update_post_meta( $post_id, OCSCW_PREFIX.'btn_bg_clr', $btn_bg_clr );
            update_post_meta( $post_id, OCSCW_PREFIX.'btn_pos', $btn_pos );
            update_post_meta( $post_id, OCSCW_PREFIX.'shop_btn_pos', $shop_btn_pos );
            update_post_meta( $post_id, OCSCW_PREFIX.'btn_padding', $btn_padding );
            update_post_meta( $post_id, OCSCW_PREFIX.'tbl_head_bg_clr', $tbl_head_bg_clr);
            update_post_meta( $post_id, OCSCW_PREFIX.'tbl_head_ft_clr', $tbl_head_ft_clr);
            update_post_meta( $post_id, OCSCW_PREFIX.'tbl_even_row_clr',$tbl_even_row_clr);
            update_post_meta( $post_id, OCSCW_PREFIX.'tbl_odd_row_clr', $tbl_odd_row_clr);
            update_post_meta( $post_id, OCSCW_PREFIX.'tbl_dtrow_font_clr', $tbl_dtrow_font_clr);
            update_post_meta( $post_id, OCSCW_PREFIX.'tbl_border', $tbl_border);
            update_post_meta( $post_id, OCSCW_PREFIX.'show_tab', $show_tab);
            update_post_meta( $post_id, OCSCW_PREFIX.'chart_tab_name', $chart_tab_name);
            update_post_meta( $post_id, OCSCW_PREFIX.'dis_tab_name', $dis_tab_name);
            update_post_meta( $post_id, OCSCW_PREFIX.'btn_brd_rd', $btn_brd_rd);
            update_post_meta( $post_id, OCSCW_PREFIX.'szchartpp_shop_enbl', $szchartpp_shop_enbl);
            update_post_meta( $post_id, OCSCW_PREFIX.'popup_loader', $popup_loader );
        }


        function OCSCW_add_pages() {
            add_submenu_page(
                'edit.php?post_type=ocscw_size_chart',
                __( 'Import Sample Size Charts', 'size-chart-woocommerce' ),
                __( 'Import Sample Size Charts', 'size-chart-woocommerce' ),
                'manage_options',
                'ocscw-import-sample-size-charts',
                array($this, 'OCSCW_pages_callback'),
                100
            );
        }


        function OCSCW_pages_callback() {
            $url = admin_url()."edit.php?post_type=ocscw_size_chart&action=ocscwimport_chart";
            ?>
            <div class="wrap">
                <div class="ocscw_import_main">
                    <h2>Import Sample Size Charts</h2>
                    <?php
                    if(isset($_REQUEST['import']) && $_REQUEST['import'] == 'success') {
                        echo "<div class='ocscw_notice_success'><p>Sample size charts imported successfully.</p></div>";
                    }
                    ?>
                    <form method="post" enctype="multipart/form-data" class="ocscw_import">
                        <?php wp_nonce_field( 'ocscw_import_nonce_action', 'ocscw_import_nonce_field' ); ?>                      
                        <div class="ocscw_importbox">
                            <h3>Import sample size charts</h3>
                            <input type="hidden" name="ocscw_import_action" value="ocscw_import_size_charts">
                            <input type="submit" value="One Click Import">
                        </div>
                        <p class="description">Import sample size charts (premade sizecharts created by us) to better understand size charts options and you can simply edit them with your size charts.</p>
                    </form>
                </div>
            </div>
            <?php
        }


        function OCSCW_create_chart() {
            if( current_user_can('administrator') ) { 
                if(isset($_REQUEST['ocscw_import_action']) && $_REQUEST['ocscw_import_action'] == 'ocscw_import_size_charts') {
                    if(!isset( $_POST['ocscw_import_nonce_field'] ) || !wp_verify_nonce( $_POST['ocscw_import_nonce_field'], 'ocscw_import_nonce_action' )) {
                        
                        echo 'Sorry, your nonce did not verify.';
                        exit;

                    } else {
                        $post_array = array(
                            "Women’s Shoes Size Chart" => array(
                                'content' => '<p><strong>How to measure</strong></p>',
                                'chart'  => Array(
                                            'US'  ,'Euro'  ,'UK'  ,'Inches'  ,'CM',
                                            '4'   ,'35'    ,'2'   ,'8.1875'  ,'20.8',
                                            '4.5' ,'35'    ,'2.5' ,'8.375'   ,'21.3',
                                            '5'   ,'35-36' ,'3'   ,'8.5'     ,'21.6',
                                            '5.5' ,'36'    ,'3.5' ,'8.75'    ,'22.2',
                                            '6'   ,'36-37' ,'4'   ,'8.875'   ,'22.5',
                                            '6.5' ,'37'    ,'4.5' ,'9.0625'  ,'23',
                                            '7'   ,'37-38' ,'5'   ,'9.25'    ,'23.5',
                                            '7.5' ,'38'    ,'5.5' ,'9.375'   ,'23.8',
                                            '8'   ,'38-39' ,'6'   ,'9.5'     ,'24.1',
                                            '8.5' ,'39'    ,'6.5' ,'9.6875'  ,'24.6',
                                            '9'   ,'39-40' ,'7'   ,'9.875'   ,'25.1',
                                            '9.5' ,'40'    ,'7.5' ,'10'      ,'25.4',
                                            '10'  ,'40-41' ,'8'   ,'10.1875' ,'25.9',
                                            '10.5','41'    ,'8.5' ,'10.3125' ,'26.2',
                                            '11'  ,'41-42' ,'9'   ,'10.5'    ,'26.7',
                                            '11.5','42'    ,'9.5' ,'10.6875' ,'27.1',
                                            '12'  ,'42-43' ,'10'  ,'10.875'  ,'27.6',

                                         ) ,
                                'totalrow'  => '18',
                                'totalcol'  => '5', 
                                'btn_show'=> 'popup',
                                'sub_title'=> "Women's Shoes Chart",
                                'tab_lbl'=> 'Size Chart',
                                'btn_lbl'=> "Women's Shoes Chart",
                                'btn_ft_clr'=> "#ffffff",
                                'btn_bg_clr'=> "#000000",
                                'btn_pos'=> "before_add_cart",
                                'tbl_head_bg_clr'=> "#000000",
                                'tbl_head_ft_clr'=> "#ffffff",
                                'tbl_even_row_clr'=> "#ebe9eb",
                                'tbl_odd_row_clr'=> "#ffffff",
                                'tbl_dtrow_font_clr'=>"#000000",
                                'show_tab'=> "on",
                                'chart_tab_name'=>  "Size Chart",
                                'dis_tab_name'=>  "How to Measure",
                                'btn_brd_rd'=>  "5",
                                'image'=>'women-shoes-size-image.jpg',
                                'szchartpp_shop_enbl'=> 'disable',
                                'shop_btn_show'=> 'popup',
                                'shop_btn_pos'=> 'before_add_cart',
                                'szchartpp_aply_all'=> 'disable',
                                'tab_pririty'=> '50',
                                'border'=> 'none',
                                'btn_padding'=> '10px 15px'
                            ),
                            "Men’s Shoes Size Chart" => array(
                                'content' => '<p><strong>How to measure</strong></p>',
                                'chart'  => Array(
                                            'US'   ,'Euro'  ,'UK'   ,'Inches'  ,'CM',
                                            '6'    ,'39'    ,'5.5'  ,'9.25'    ,'23.5',
                                            '6.5'  ,'39'    ,'6'    ,'9.5'     ,'24.1',
                                            '7'    ,'40'    ,'6.5'  ,'9.625'   ,'24.4',
                                            '7.5'  ,'40-41' ,'7'    ,'9.75'    ,'24.8',
                                            '8'    ,'41'    ,'7.5'  ,'9.9375'  ,'25.4',
                                            '8.5'  ,'41-42' ,'8'    ,'10.125'  ,'25.7',
                                            '9'    ,'42'    ,'8.5'  ,'10.25'   ,'26',
                                            '9.5'  ,'42-43' ,'9'    ,'10.4375' ,'26.7',
                                            '10'   ,'43'    ,'9.5'  ,'10.5625' ,'27',
                                            '10.5' ,'43-44' ,'10'   ,'10.75'   ,'27.3',
                                            '11'   ,'44'    ,'10.5' ,'10.9375' ,'27.9',
                                            '11.5' ,'44-45' ,'11'   ,'11.125'  ,'28.3',
                                            '12'   ,'45'    ,'11.5' ,'11.25'   ,'28.6',
                                            '13'   ,'46'    ,'12.5' ,'11.5625' ,'29.4',
                                            '14'   ,'47'    ,'13.5' ,'11.875'  ,'30.2',
                                            '15'   ,'48'    ,'14.5' ,'12.1875' ,'31',
                                            '16'   ,'49'    ,'15.5' ,'12.5'    ,'31.8',

                                         ) ,
                                'totalrow'  => '18',
                                'totalcol'  => '5', 
                                'btn_show'=> 'popup',
                                'sub_title'=> "Men's Shoes Chart",
                                'tab_lbl'=> 'Size Chart',
                                'btn_lbl'=> "Men's Shoes Chart",
                                'btn_ft_clr'=> "#ffffff",
                                'btn_bg_clr'=> "#000000",
                                'btn_pos'=> "after_add_cart",
                                'tbl_head_bg_clr'=> "#000000",
                                'tbl_head_ft_clr'=> "#ffffff",
                                'tbl_even_row_clr'=> "#ebe9eb",
                                'tbl_odd_row_clr'=> "#ffffff",
                                'tbl_dtrow_font_clr'=>"#000000",
                                'show_tab'=> "on",
                                'chart_tab_name'=>  "Size Chart",
                                'dis_tab_name'=>  "How to Measure",
                                'btn_brd_rd'=>  "5",
                                'image'=>'mens-shoes-size-chart.png',
                                'szchartpp_shop_enbl'=> 'disable',
                                'shop_btn_show'=> 'popup',
                                'shop_btn_pos'=> 'before_add_cart',
                                'szchartpp_aply_all'=> 'disable',
                                'tab_pririty'=> '50',
                                'border'=> 'none',
                                'btn_padding'=> '10px 15px'
                            ),
                            "Women’s Cloth Size Chart" => array(
                                'content' => '<p><strong>How to measure</strong></p>
                                            <ul>
                                              <li>
                                                <strong>Chest : </strong><br>
                                                Measure around the fullest part of the bust, keeping the tape parallel to the floor.
                                              </li>
                                              <li>
                                                <strong>Waist : </strong><br>
                                                Measure around the narrowest point, keeping the tape parallel to the floor.
                                              </li>
                                              <li>
                                                <strong>Hip : </strong><br>
                                                Stand with feet together and measure around the fullest point of the hip, keep the tape parallel to the floor.
                                              </li>
                                              <li>
                                                <strong>Inseam : </strong><br>
                                                Measure inside length of leg from your crotch to the bottom of ankle.
                                              </li>
                                            </ul>',
                                'chart'  => Array(
                                            'UK SIZE'  , 'BUST'   ,'BUST'   ,'WAIST'  ,'WAIST', 'HIPS'   , 'HIPS',
                                            'N/A'      , 'INCHES' ,'CM'     ,'INCHES' ,'CM'   , 'INCHES' , 'CM',
                                            '4'        , '31'     ,'78'     ,'24'     ,'60'   , '33'     , '83.5',
                                            '6'        , '32'     ,'80.5'   ,'25'     ,'62.5' , '34'     , '86',
                                            '8'        , '33'     ,'83'     ,'26'     ,'65'   , '35'     , '88.5',
                                            '10'       , '35'     ,'88'     ,'28'     ,'70'   , '37'     , '93.5',
                                            '12'       , '37'     ,'93'     ,'30'     ,'75'   , '39'     , '98.5',
                                            '14'       , '39'     ,'98'     ,'31'     ,'80'   , '41'     , '103.5',
                                            '16'       , '41'     ,'103'    ,'33'     ,'85'   , '43'     , '108.5',
                                            '18'       , '44'     ,'110.5'  ,'36'     ,'92.5' , '46'     , '116',
                                         ) ,
                                'totalrow'  => '10',
                                'totalcol'  => '7', 
                                'btn_show'=> 'popup',
                                'sub_title'=> "Women’s Cloth Chart",
                                'tab_lbl'=> 'Size Chart',
                                'btn_lbl'=> "Women’s Cloth Chart",
                                'btn_ft_clr'=> "#ffffff",
                                'btn_bg_clr'=> "#000000",
                                'btn_pos'=> "after_add_cart",
                                'tbl_head_bg_clr'=> "#000000",
                                'tbl_head_ft_clr'=> "#ffffff",
                                'tbl_even_row_clr'=> "#ebe9eb",
                                'tbl_odd_row_clr'=> "#ffffff",
                                'tbl_dtrow_font_clr'=>"#000000",
                                'show_tab'=> "on",
                                'chart_tab_name'=>  "Size Chart",
                                'dis_tab_name'=>  "How to Measure",
                                'btn_brd_rd'=>  "5",
                                'image'=>'cloth_size_chart.png',
                                'szchartpp_shop_enbl'=> 'disable',
                                'shop_btn_show'=> 'popup',
                                'shop_btn_pos'=> 'before_add_cart',
                                'szchartpp_aply_all'=> 'disable',
                                'tab_pririty'=> '50',
                                'border'=> 'none',
                                'btn_padding'=> '10px 15px'
                            ),
                            "Men’s Waistcoats Size Chart" => array(
                                'content' => '<p><strong>How to measure</strong></p>
                                            <strong>Chest :</strong>
                                            Measure around the fullest part, place the tape close under the arms and make sure the tape is flat across the back.',
                                'chart'  => Array(
                                            'CHEST MEASUREMENT'   , 'INCHES' ,  'CM',
                                            '32'                  , '32'     ,  '81',
                                            '34'                  , '34'     ,  '86',
                                            '36'                  , '36'     ,  '91',
                                            '38'                  , '38'     ,  '96',
                                            '40'                  , '40'     ,  '101',
                                            '42'                  , '42'     ,  '106',
                                            '44'                  , '44'     ,  '111',
                                            '46'                  , '46'     ,  '116',
                                         ) ,
                                'totalrow'  => '9',
                                'totalcol'  => '3', 
                                'btn_show'=> 'tab',
                                'sub_title'=> "Men’s Waistcoats Chart",
                                'tab_lbl'=> 'Size Chart',
                                'btn_lbl'=> "Men’s Waistcoats Chart",
                                'btn_ft_clr'=> "#ffffff",
                                'btn_bg_clr'=> "#000000",
                                'btn_pos'=> "after_add_cart",
                                'tbl_head_bg_clr'=> "#000000",
                                'tbl_head_ft_clr'=> "#ffffff",
                                'tbl_even_row_clr'=> "#ebe9eb",
                                'tbl_odd_row_clr'=> "#ffffff",
                                'tbl_dtrow_font_clr'=>"#000000",
                                'show_tab'=> "on",
                                'chart_tab_name'=>  "Size Chart",
                                'dis_tab_name'=>  "How to Measure",
                                'btn_brd_rd'=>  "5",
                                'image'=>'mens-waistcoats.jpg',
                                'szchartpp_shop_enbl'=> 'disable',
                                'shop_btn_show'=> 'popup',
                                'shop_btn_pos'=> 'before_add_cart',
                                'szchartpp_aply_all'=> 'disable',
                                'tab_pririty'=> '50',
                                'border'=> 'none',
                                'btn_padding'=> '10px 15px'
                            ),
                            "Women’s Jeans And Jeggings Size Chart" => array(
                                'content' => '<p><strong>How to measure</strong></p>
                                            <ul>
                                              <li>
                                                <strong>Waist : </strong><br>
                                                Measure around your natural waistline,keeping the tape bit loose.
                                              </li>
                                              <li>
                                                <strong>Hips : </strong><br>
                                                Measure around the fullest part of your body at the top of your leg.
                                              </li>
                                              <li>
                                                <strong>Inseam : </strong><br>
                                                Wearing pants that fit well, measure from the crotch seam to the bottom of the leg.
                                              </li>
                                            </ul>',
                                'chart'  => Array(
                                            'Size'    , 'Waist'  ,  'Hip',
                                            '24'      , '24'     ,  '35',
                                            '25'      , '25'     ,  '36',
                                            '26'      , '26'     ,  '37',
                                            '27'      , '27'     ,  '38',
                                            '28'      , '28'     ,  '39',
                                            '29'      , '29'     ,  '40',
                                            '30'      , '30'     ,  '41',
                                            '31'      , '31'     ,  '42',
                                            '32'      , '32'     ,  '43',
                                            '33'      , '33'     ,  '44',
                                            '34'      , '34'     ,  '45',
                                         ) ,
                                'totalrow'  => '12',
                                'totalcol'  => '3', 
                                'btn_show'=> 'tab',
                                'sub_title'=> "Women’s Jeans Chart",
                                'tab_lbl'=> 'Size Chart',
                                'btn_lbl'=> "Women’s Jeans Chart",
                                'btn_ft_clr'=> "#ffffff",
                                'btn_bg_clr'=> "#000000",
                                'btn_pos'=> "after_add_cart",
                                'tbl_head_bg_clr'=> "#000000",
                                'tbl_head_ft_clr'=> "#ffffff",
                                'tbl_even_row_clr'=> "#ebe9eb",
                                'tbl_odd_row_clr'=> "#ffffff",
                                'tbl_dtrow_font_clr'=>"#000000",
                                'show_tab'=> "on",
                                'chart_tab_name'=>  "Size Chart",
                                'dis_tab_name'=>  "How to Measure",
                                'btn_brd_rd'=>  "5",
                                'image'=>'women-jeans-size-chart.png',
                                'szchartpp_shop_enbl'=> 'disable',
                                'shop_btn_show'=> 'popup',
                                'shop_btn_pos'=> 'before_add_cart',
                                'szchartpp_aply_all'=> 'disable',
                                'tab_pririty'=> '50',
                                'border'=> 'none',
                                'btn_padding'=> '10px 15px'
                            ),
                            "Men’s Jeans & Trousers Size Chart" => array(
                                'content' => '<p><strong>How to measure</strong></p>
                                            <p>To choose the correct size for you, measure your body as follows:</p>
                                            <ul>
                                              <li>
                                                <strong>Waist : </strong><br>
                                                Measure around natural waistline.
                                              </li>
                                              <li>
                                                <strong>Inside leg : </strong><br>
                                                Measure from top of inside leg at crotch to ankle bone.
                                              </li>
                                            </ul>',
                                'chart'  => Array(
                                            'Size'    , 'Waist'  ,  'Hip',  'Inseam'  ,  'Outseam',
                                            '28'      , '28'     ,  '32' ,   '30'     ,  '41',
                                            '30'      , '28.5'   ,  '34' ,   '30'     ,  '42',
                                            '32'      , '30.5'   ,  '36' ,   '30'     ,  '43',
                                            '34'      , '32.5'   ,  '38' ,   '32'     ,  '44',
                                            '36'      , '34.5'   ,  '40' ,   '34'     ,  '45',
                                         ) ,
                                'totalrow'  => '6',
                                'totalcol'  => '5', 
                                'btn_show'=> 'tab',
                                'sub_title'=> "Men’s Jeans Chart",
                                'tab_lbl'=> 'Size Chart',
                                'btn_lbl'=> "Men’s Jeans Chart",
                                'btn_ft_clr'=> "#ffffff",
                                'btn_bg_clr'=> "#000000",
                                'btn_pos'=> "after_add_cart",
                                'tbl_head_bg_clr'=> "#000000",
                                'tbl_head_ft_clr'=> "#ffffff",
                                'tbl_even_row_clr'=> "#ebe9eb",
                                'tbl_odd_row_clr'=> "#ffffff",
                                'tbl_dtrow_font_clr'=>"#000000",
                                'show_tab'=> "on",
                                'chart_tab_name'=>  "Size Chart",
                                'dis_tab_name'=>  "How to Measure",
                                'btn_brd_rd'=>  "5",
                                'image'=>'mens-jeans-and-trousers.jpg',
                                'szchartpp_shop_enbl'=> 'disable',
                                'shop_btn_show'=> 'popup',
                                'shop_btn_pos'=> 'before_add_cart',
                                'szchartpp_aply_all'=> 'disable',
                                'tab_pririty'=> '50',
                                'border'=> 'none',
                                'btn_padding'=> '10px 15px'
                            ),
                            "Women’s Dress Size Chart" => array(
                                'content' => '<p><strong>How to measure</strong></p>
                                            <ul>
                                              <li>
                                                <strong>Chest : </strong><br>
                                                Measure under your arms, around the fullest part of the your chest.
                                              </li>
                                              <li>
                                                <strong>Waist : </strong><br>
                                                Measure around your natural waistline, keeping the tape a bit loose.
                                              </li>
                                              <li>
                                                <strong>Hips : </strong><br>
                                                Measure around the fullest part of your body at the top of your leg.
                                              </li>
                                            </ul>',
                                'chart'  => Array(
                                            'SIZE'  , 'NUMERIC SIZE'   , 'BUST'   , 'WAIST'   , 'HIP',
                                            'XXXS'  , '000'            , '30'     , '23'      , '33',
                                            'XXS'   , '00'             , '31.5'   , '24'      , '34',
                                            'XS'    , '0'              , '32.5'   , '25'      , '35',
                                            'XS'    , '2'              , '33.5'   , '26'      , '36',
                                            'S'     , '4'              , '34.5'   , '27'      , '37',
                                            'S'     , '6'              , '35.5'   , '28'      , '38',
                                            'M'     , '8'              , '36.5'   , '29'      , '39',
                                            'M'     , '10'             , '37.5'   , '30'      , '40',
                                            'L'     , '12'             , '39'     , '31.5'    , '41.5',
                                            'L'     , '14'             , '40.5'   , '33'      , '43',
                                            'XL'    , '16'             , '42'     , '34.5'    , '44.5',
                                            'XL'    , '18'             , '44'     , '36'      , '46.5',
                                            'XXL'   , '20'             , '46'     , '37.5'    , '48.5',
                                         ) ,
                                'totalrow'  => '14',
                                'totalcol'  => '5', 
                                'btn_show'=> 'popup',
                                'sub_title'=> "Women’s Dress Chart",
                                'tab_lbl'=> 'Size Chart',
                                'btn_lbl'=> "Women’s Dress Chart",
                                'btn_ft_clr'=> "#ffffff",
                                'btn_bg_clr'=> "#000000",
                                'btn_pos'=> "after_add_cart",
                                'tbl_head_bg_clr'=> "#000000",
                                'tbl_head_ft_clr'=> "#ffffff",
                                'tbl_even_row_clr'=> "#ebe9eb",
                                'tbl_odd_row_clr'=> "#ffffff",
                                'tbl_dtrow_font_clr'=>"#000000",
                                'show_tab'=> "on",
                                'chart_tab_name'=>  "Size Chart",
                                'dis_tab_name'=>  "How to Measure",
                                'btn_brd_rd'=>  "5",
                                'image'=>'women-dress-size-chart.png',
                                'szchartpp_shop_enbl'=> 'disable',
                                'shop_btn_show'=> 'popup',
                                'shop_btn_pos'=> 'before_add_cart',
                                'szchartpp_aply_all'=> 'disable',
                                'tab_pririty'=> '50',
                                'border'=> 'none',
                                'btn_padding'=> '10px 15px'
                            ),
                            "Men’s Shirts Size Chart" => array(
                                'content' => '<p><strong>How to measure</strong></p>
                                            <p>To choose the correct size for you, measure your body as follows:</p>
                                            <ul>
                                              <li>
                                                <strong>Chest : </strong><br>
                                                Measure around the fullest part, place the tape close under the arms and make sure the tape is flat across the back.
                                              </li>
                                            </ul>',
                                'chart'  => Array(
                                            'TO FIT CHEST SIZE' , 'INCHES'  , 'CM'     , 'TO FIT NECK SIZE','INCHES','CM',
                                            'XXXS'              , '30-32'   , '76-81'  , 'XXXS'            ,'14'    ,'36',
                                            'XXS'               , '32-34'   , '81-86'  , 'XXS'             ,'14.5'  ,'37.5',
                                            'XS'                , '34-36'   , '86-91'  , 'XS'              ,'15'    ,'38.5',
                                            'S'                 , '36-38'   , '91-96'  , 'S'               ,'15.5'  ,'39.5',
                                            'M'                 , '38-40'   , '96-101' , 'M'               ,'16'    ,'41.5',
                                            'L'                 , '40-42'   , '101-106', 'L'               ,'17'    ,'43.5',
                                            'XL'                , '42-44'   , '106-111', 'XL'              ,'17.5'  ,'45.5',
                                            'XXL'               , '44-46'   , '111-116', 'XXL'             ,'18.5'  ,'47.5',
                                            'XXXL'              , '46-48 '  , '116-121', 'XXXL'            ,'19.5'  ,'49.5',
                                         ) ,
                                'totalrow'  => '10',
                                'totalcol'  => '6', 
                                'btn_show'=> 'popup',
                                'sub_title'=> "Men’s Shirts Chart",
                                'tab_lbl'=> 'Size Chart',
                                'btn_lbl'=> "Men’s Shirts Chart",
                                'btn_ft_clr'=> "#ffffff",
                                'btn_bg_clr'=> "#000000",
                                'btn_pos'=> "after_add_cart",
                                'tbl_head_bg_clr'=> "#000000",
                                'tbl_head_ft_clr'=> "#ffffff",
                                'tbl_even_row_clr'=> "#ebe9eb",
                                'tbl_odd_row_clr'=> "#ffffff",
                                'tbl_dtrow_font_clr'=>"#000000",
                                'show_tab'=> "on",
                                'chart_tab_name'=>  "Size Chart",
                                'dis_tab_name'=>  "How to Measure",
                                'btn_brd_rd'=>  "5",
                                'image'=>'mens-shirts.jpg',
                                'szchartpp_shop_enbl'=> 'disable',
                                'shop_btn_show'=> 'popup',
                                'shop_btn_pos'=> 'before_add_cart',
                                'szchartpp_aply_all'=> 'disable',
                                'tab_pririty'=> '50',
                                'border'=> 'none',
                                'btn_padding'=> '10px 15px'
                            ),
                            "Women’s T-shirt / Tops Size Chart" => array(
                                'content' => '<p><strong>How to measure</strong></p>
                                            <ul>
                                              <li>
                                                <strong>Chest : </strong><br>
                                                Measure under your arms, around the fullest part of the your chest.
                                              </li>
                                              <li>
                                                <strong>Waist : </strong><br>
                                                Measure around your natural waistline, keeping the tape a bit loose.
                                              </li>
                                            </ul>',
                                'chart'  => Array(
                                            'UK SIZE'  , 'BUST'   ,'BUST'   ,'WAIST'  ,'WAIST', 'HIPS'   , 'HIPS',
                                            'N/A'      , 'INCHES' ,'CM'     ,'INCHES' ,'CM'   , 'INCHES' , 'CM',
                                            '4'        , '31'     ,'78'     ,'24'     ,'60'   , '33'     , '83.5',
                                            '6'        , '32'     ,'80.5'   ,'25'     ,'62.5' , '34'     , '86',
                                            '8'        , '33'     ,'83'     ,'26'     ,'65'   , '35'     , '88.5',
                                            '10'       , '35'     ,'88'     ,'28'     ,'70'   , '37'     , '93.5',
                                            '12'       , '37'     ,'93'     ,'30'     ,'75'   , '39'     , '98.5',
                                            '14'       , '39'     ,'98'     ,'31'     ,'80'   , '41'     , '103.5',
                                            '16'       , '41'     ,'103'    ,'33'     ,'85'   , '43'     , '108.5',
                                            '18'       , '44'     ,'110.5'  ,'36'     ,'92.5' , '46'     , '116',
                                         ) ,
                                'totalrow'  => '10',
                                'totalcol'  => '7', 
                                'btn_show'=> 'popup',
                                'sub_title'=> "Women’s T-shirt Chart",
                                'tab_lbl'=> 'Size Chart',
                                'btn_lbl'=> "Women’s T-shirt Chart",
                                'btn_ft_clr'=> "#ffffff",
                                'btn_bg_clr'=> "#000000",
                                'btn_pos'=> "after_add_cart",
                                'tbl_head_bg_clr'=> "#000000",
                                'tbl_head_ft_clr'=> "#ffffff",
                                'tbl_even_row_clr'=> "#ebe9eb",
                                'tbl_odd_row_clr'=> "#ffffff",
                                'tbl_dtrow_font_clr'=>"#000000",
                                'show_tab'=> "on",
                                'chart_tab_name'=>  "Size Chart",
                                'dis_tab_name'=>  "How to Measure",
                                'btn_brd_rd'=>  "5",
                                'image'=>'women-t-shirt-top.png',
                                'szchartpp_shop_enbl'=> 'disable',
                                'shop_btn_show'=> 'popup',
                                'shop_btn_pos'=> 'before_add_cart',
                                'szchartpp_aply_all'=> 'disable',
                                'tab_pririty'=> '50',
                                'border'=> 'none',
                                'btn_padding'=> '10px 15px'
                            ),
                            "Men’s T-Shirts & Polo Shirts Size Chart" => array(
                                'content' => '<p><strong>How to measure</strong></p>
                                            <p>To choose the correct size for you, measure your body as follows:</p>
                                            <ul>
                                              <li>
                                                <strong>Chest : </strong><br>
                                                Measure around the fullest part, place the tape close under the arms and make sure the tape is flat across the back.
                                              </li>
                                            </ul>',
                                'chart'  => Array(
                                            'TO FIT CHEST SIZE' , 'INCHES'  , 'CM'     ,
                                            'XXXS'              , '30-32'   , '76-81'  ,
                                            'XXS'               , '32-34'   , '81-86'  ,
                                            'XS'                , '34-36'   , '86-91'  ,
                                            'S'                 , '36-38'   , '91-96'  ,
                                            'M'                 , '38-40'   , '96-101' ,
                                            'L'                 , '40-42'   , '101-106',
                                            'XL'                , '42-44'   , '106-111',
                                            'XXL'               , '44-46'   , '111-116',
                                            'XXXL'              , '46-48 '  , '116-121',
                                         ) ,
                                'totalrow'  => '10',
                                'totalcol'  => '3', 
                                'btn_show'=> 'popup',
                                'sub_title'=> "Men’s T-Shirts Chart",
                                'tab_lbl'=> 'Size Chart',
                                'btn_lbl'=> "Men’s T-Shirts Chart",
                                'btn_ft_clr'=> "#ffffff",
                                'btn_bg_clr'=> "#000000",
                                'btn_pos'=> "after_add_cart",
                                'tbl_head_bg_clr'=> "#000000",
                                'tbl_head_ft_clr'=> "#ffffff",
                                'tbl_even_row_clr'=> "#ebe9eb",
                                'tbl_odd_row_clr'=> "#ffffff",
                                'tbl_dtrow_font_clr'=>"#000000",
                                'show_tab'=> "on",
                                'chart_tab_name'=>  "Size Chart",
                                'dis_tab_name'=>  "How to Measure",
                                'btn_brd_rd'=>  "5",
                                'image'=>'mens-tshirts-and-polo-shirts.jpg',
                                'szchartpp_shop_enbl'=> 'disable',
                                'shop_btn_show'=> 'popup',
                                'shop_btn_pos'=> 'before_add_cart',
                                'szchartpp_aply_all'=> 'disable',
                                'tab_pririty'=> '50',
                                'border'=> 'none',
                                'btn_padding'=> '10px 15px'
                            ),
                        );
    
                        if(!empty($post_array)) {
                            foreach ($post_array as $key => $value) {

                                $customPost = get_page_by_title($key, OBJECT, 'ocscw_size_chart');

                                if(is_null($customPost)) {

                                    $new_post = array(
                                             'post_title'   => $key,
                                             'post_status'  => 'publish',
                                             'post_type'    => 'ocscw_size_chart',
                                             'post_content' => $value['content']
                                          );
                                    $post_id = wp_insert_post($new_post);

                                    update_post_meta( $post_id, OCSCW_PREFIX.'size_chartdata', $value['chart'] );
                                    update_post_meta( $post_id, OCSCW_PREFIX.'totalrow', $value['totalrow'] );
                                    update_post_meta( $post_id, OCSCW_PREFIX.'totalcol', $value['totalcol'] );
                                    update_post_meta( $post_id, OCSCW_PREFIX.'btn_show', $value['btn_show'] );
                                    update_post_meta( $post_id, OCSCW_PREFIX.'sub_title', $value['sub_title'] );
                                    update_post_meta( $post_id, OCSCW_PREFIX.'tab_lbl', $value['tab_lbl'] );
                                    update_post_meta( $post_id, OCSCW_PREFIX.'btn_lbl', $value['btn_lbl'] );
                                    update_post_meta( $post_id, OCSCW_PREFIX.'btn_ft_clr', $value['btn_ft_clr'] );
                                    update_post_meta( $post_id, OCSCW_PREFIX.'btn_bg_clr', $value['btn_bg_clr'] );
                                    update_post_meta( $post_id, OCSCW_PREFIX.'btn_pos', $value['btn_pos'] );
                                    update_post_meta( $post_id, OCSCW_PREFIX.'tbl_head_bg_clr',$value['tbl_head_bg_clr']);
                                    update_post_meta( $post_id, OCSCW_PREFIX.'tbl_head_ft_clr',$value['tbl_head_ft_clr']);
                                    update_post_meta( $post_id, OCSCW_PREFIX.'tbl_even_row_clr',$value['tbl_even_row_clr']);
                                    update_post_meta( $post_id, OCSCW_PREFIX.'tbl_odd_row_clr',$value['tbl_odd_row_clr']);
                                    update_post_meta( $post_id, OCSCW_PREFIX.'show_tab',$value['show_tab']);
                                    update_post_meta( $post_id, OCSCW_PREFIX.'chart_tab_name', $value['chart_tab_name']);
                                    update_post_meta( $post_id, OCSCW_PREFIX.'dis_tab_name', $value['dis_tab_name']);
                                    update_post_meta( $post_id, OCSCW_PREFIX.'btn_brd_rd', $value['btn_brd_rd']);
                                    update_post_meta( $post_id, OCSCW_PREFIX.'szchartpp_shop_enbl', $value['szchartpp_shop_enbl']);
                                    update_post_meta( $post_id, OCSCW_PREFIX.'shop_btn_show', $value['shop_btn_show']);
                                    update_post_meta( $post_id, OCSCW_PREFIX.'shop_btn_pos', $value['shop_btn_pos']);
                                    update_post_meta( $post_id, OCSCW_PREFIX.'szchartpp_aply_all', $value['szchartpp_aply_all']);
                                    update_post_meta( $post_id, OCSCW_PREFIX.'tab_pririty', $value['tab_pririty']);
                                    update_post_meta( $post_id, OCSCW_PREFIX.'btn_padding', $value['btn_padding']);

                                    $IMGFileName = $value['image'];
                                    $dirPath = OCSCW_PLUGIN_AB_PATH."includes/images/";
                                    $IMGFilePath = $dirPath.$IMGFileName;
                                    $upload = wp_upload_bits($IMGFileName , null, file_get_contents($IMGFilePath, FILE_USE_INCLUDE_PATH));
                                    $imageFile = $upload['file'];
                                    $wpFileType = wp_check_filetype($imageFile, null);
                                    $attachment = array(
                                        'post_mime_type' => $wpFileType['type'],  // file type
                                        'post_title' => sanitize_file_name($IMGFileName), // sanitize and use image name as file name
                                        'post_content' => '',  // could use the image description here as the content
                                        'post_status' => 'inherit'
                                    );

                                    //insert and return attachment id
                                    $attachmentId = wp_insert_attachment( $attachment, $imageFile, $post_id );
                                    $success = set_post_thumbnail( $post_id, $attachmentId );
                                }
                            }
                        }

                        $url = admin_url().'edit.php?post_type=ocscw_size_chart&page=ocscw-import-sample-size-charts&import=success';

                        wp_redirect($url);
                        exit;
                    }
                }
            }
        }


        function OCSCW_clone_post_link( $actions, $post ) {
            if ($post->post_type=='ocscw_size_chart' && current_user_can('edit_posts')) {
                $actions['clone'] = '<a href="' . wp_nonce_url('admin.php?action=ocscw_clone_post_as_draft&post=' . $post->ID, basename(__FILE__), 'clone_nonce' ) . '" title="Clone this Size Chart" rel="permalink">Clone</a>';
            }
            return $actions;
        }


        function ocscw_clone_post_as_draft() {
            global $wpdb;
            if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'ocscw_clone_post_as_draft' == $_REQUEST['action'] ) ) ) {
                wp_die('No post to duplicate has been supplied!');
            }
         
            if ( !isset( $_GET['clone_nonce'] ) || !wp_verify_nonce( $_GET['clone_nonce'], basename( __FILE__ ) ) )
                return;
         
            $post_id = (isset($_GET['post']) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );
            $post = get_post( $post_id );
         
            $current_user = wp_get_current_user();
            $new_post_author = $current_user->ID;
         
            if (isset( $post ) && $post != null) {
         
                $args = array(
                    'comment_status' => $post->comment_status,
                    'ping_status'    => $post->ping_status,
                    'post_author'    => $new_post_author,
                    'post_content'   => $post->post_content,
                    'post_excerpt'   => $post->post_excerpt,
                    'post_name'      => $post->post_name,
                    'post_parent'    => $post->post_parent,
                    'post_password'  => $post->post_password,
                    'post_status'    => 'draft',
                    'post_title'     => $post->post_title.' - Copy',
                    'post_type'      => $post->post_type,
                    'to_ping'        => $post->to_ping,
                    'menu_order'     => $post->menu_order
                );
         
                $new_post_id = wp_insert_post( $args );
         
                $taxonomies = get_object_taxonomies($post->post_type);
                
                if(!empty($taxonomies)) {
                    foreach ($taxonomies as $taxonomy) {
                        $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
                        wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
                    }
                }
         
                $post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
                if (count($post_meta_infos)!=0) {
                    $sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
                    if(!empty($post_meta_infos)) {
                        foreach ($post_meta_infos as $meta_info) {
                            $meta_key = $meta_info->meta_key;
                            if( $meta_key == '_wp_old_slug' ) continue;
                            $meta_value = addslashes($meta_info->meta_value);
                            $sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
                        }
                    }
                    $sql_query.= implode(" UNION ALL ", $sql_query_sel);
                    $wpdb->query($sql_query);
                }

                wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
                exit;
            } else {
                wp_die('Post creation failed, could not find original post: ' . $post_id);
            }
        }


        function init() {
            add_action( 'init', array($this, 'OCSCW_create_menu'));
            add_action( 'init', array($this, 'OCSCW_create_chart'));
            add_action( 'add_meta_boxes', array($this, 'OCSCW_add_meta_box'));
            add_action( 'edit_post', array($this, 'OCSCW_meta_save'), 10, 2);
            add_action( 'admin_menu', array($this, 'OCSCW_add_pages'));
            add_filter( 'post_row_actions', array($this, 'OCSCW_clone_post_link'), 10, 2 );
            add_action( 'admin_action_ocscw_clone_post_as_draft', array($this, 'ocscw_clone_post_as_draft' ));
        }


        public static function instance() {
            if (!isset(self::$instance)) {
                self::$instance = new self();
                self::$instance->init();
            }
            return self::$instance;
        }
    }
    OCSCW_menu::instance();
}


