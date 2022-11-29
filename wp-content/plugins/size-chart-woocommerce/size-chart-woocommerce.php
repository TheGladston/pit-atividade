<?php 
/**
* Plugin Name: Size Chart WooCommerce
* Description: This plugin allows create Size Chart plugin.
* Version: 1.0.1
* Copyright: 2020
* Text Domain: size-chart-woocommerce
* Domain Path: /languages 
*/

if (!defined('ABSPATH')) {
   die('-1');
}
if (!defined('OCSCW_PLUGIN_NAME')) {
   define('OCSCW_PLUGIN_NAME', 'Size Chart WooCommerce');
}
if (!defined('OCSCW_PLUGIN_VERSION')) {
   define('OCSCW_PLUGIN_VERSION', '1.0.0');
}
if (!defined('OCSCW_PLUGIN_FILE')) {
   define('OCSCW_PLUGIN_FILE', __FILE__);
}
if (!defined('OCSCW_PLUGIN_DIR')) {
   define('OCSCW_PLUGIN_DIR',plugins_url('', __FILE__));
}
if(!defined('OCSCW_PLUGIN_AB_PATH')) {
   define('OCSCW_PLUGIN_AB_PATH',plugin_dir_path( __FILE__ ));
}
if (!defined('OCSCW_BASE_NAME')) {
    define('OCSCW_BASE_NAME', plugin_basename(OCSCW_PLUGIN_FILE));
}
if (!defined('OCSCW_DOMAIN')) {
   define('OCSCW_DOMAIN', 'size-chart-woocommerce');
}
if (!defined('OCSCW_PREFIX')) {
   define('OCSCW_PREFIX', 'ocscw_');
}


if (!class_exists('OCSCW')) {
   class OCSCW {

      protected static $OCSCW_instance;
      function __construct() {
         include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
         add_action('admin_init', array($this, 'OCSCW_check_plugin_state'));
      }


      function OCSCW_check_plugin_state() {
         if ( ! ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) ) {
            set_transient( get_current_user_id() . 'OCSCWerror', 'message' );
         }
      }


      function OCSCW_load_script_style() {
         wp_enqueue_style( 'OCSCW_front_style', OCSCW_PLUGIN_DIR . '/includes/css/ocscw_front_style.css', false, '1.0.0' );
         wp_enqueue_script( 'OCSCW_front_script', OCSCW_PLUGIN_DIR . '/includes/js/ocscw_front_script.js', false, '1.0.0' );
         $ocscw_img_array = OCSCW_PLUGIN_DIR;
         wp_localize_script( 'OCSCW_front_script', 'ocscw_object', array(
         															'ocscw_ajax_url' => admin_url('admin-ajax.php'),
         															'ocscw_object_name' => $ocscw_img_array	
         ) );
      }


      function OCSCW_load_admin_script_style() {
        wp_enqueue_style( 'OCSCW_admin_style', OCSCW_PLUGIN_DIR . '/includes/css/ocscw_back_style.css', false, '1.0.0' );
        wp_enqueue_script( 'OCSCW_admin_script', OCSCW_PLUGIN_DIR . '/includes/js/ocscw_back_script.js', array( 'jquery', 'select2') );
        $ocscw_img_array = OCSCW_PLUGIN_DIR;
        wp_localize_script( 'OCSCW_admin_script', 'ocscw_objectback', array(
        																	'ocscw_object_name' => $ocscw_img_array,
        ) );
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker-alpha', OCSCW_PLUGIN_DIR . '/includes/js/wp-color-picker-alpha.js', array( 'wp-color-picker' ), '1.0.0', true );
        wp_enqueue_style('OCSCW_select2', OCSCW_PLUGIN_DIR . '/includes/css/select2.min.css' );
        wp_enqueue_script('OCSCW_select2', OCSCW_PLUGIN_DIR . '/includes/js/select2.min.js', array('jquery') );
      }


      function OCSCW_show_notice() {
         if ( get_transient( get_current_user_id() . 'OCSCWerror' ) ) {

            deactivate_plugins( plugin_basename( __FILE__ ) );
            delete_transient( get_current_user_id() . 'OCSCWerror' );

            echo '<div class="error"><p> This plugin is deactivated because it require <a href="plugin-install.php?tab=search&s=woocommerce">WooCommerce</a> plugin installed and activated.</p></div>';
         }
      }


      function OCSCW_plugin_row_meta( $links, $file ) {
            if ( OCSCW_BASE_NAME === $file ) {
                $row_meta = array(
                    'rating'    =>  '<a href="https://oceanwebguru.com/size-chart-woocommerce/" target="_blank">Documentation</a> | <a href="https://oceanwebguru.com/contact-us/" target="_blank">Support</a> | <a href="https://wordpress.org/support/plugin/size-chart-woocommerce/reviews/?filter=5" target="_blank"><img src="'.OCSCW_PLUGIN_DIR.'/includes/images/star.png" class="ocscw_rating_div"></a>',
                );

                return array_merge( $links, $row_meta );
            }
            return (array) $links;
      }

      function init() {
         add_action('admin_notices', array($this, 'OCSCW_show_notice'));
         add_action('admin_enqueue_scripts', array($this, 'OCSCW_load_admin_script_style'));
         add_action('wp_enqueue_scripts',  array($this, 'OCSCW_load_script_style'));
         add_filter( 'plugin_row_meta', array( $this, 'OCSCW_plugin_row_meta' ), 10, 2 );
      }

      function includes() {
         include_once('admin/ocscw_create_post.php');
         include_once('admin/ocscw_product_mb.php');
         include_once('admin/ocscw_charts_mb.php');
         include_once('admin/ocscw_charts_kit.php');
         include_once('front/ocscw_front.php');
      }

      public static function OCSCW_create_post() {
         if ( ! current_user_can( 'activate_plugins' ) ) return;

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

         foreach ($post_array as $key => $value) {

            $customPost = get_page_by_title($key, OBJECT, 'ocscw_size_chart');

            if(!is_null($customPost)) {
               return;
            }


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

      public static function OCSCW_instance() {
         if (!isset(self::$OCSCW_instance)) {
            self::$OCSCW_instance = new self();
            self::$OCSCW_instance->includes();
            self::$OCSCW_instance->init();
         }
         return self::$OCSCW_instance;
      }
   }
   add_action('plugins_loaded', array('OCSCW', 'OCSCW_instance'));
   register_activation_hook( OCSCW_PLUGIN_FILE, array('OCSCW', 'OCSCW_create_post' ));
}

add_action( 'plugins_loaded', 'OCSCW_load_textdomain' );
 
function OCSCW_load_textdomain() {
    load_plugin_textdomain( 'size-chart-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}

function OCSCW_load_my_own_textdomain( $mofile, $domain ) {
    if ( 'size-chart-woocommerce' === $domain && false !== strpos( $mofile, WP_LANG_DIR . '/plugins/' ) ) {
        $locale = apply_filters( 'plugin_locale', determine_locale(), $domain );
        $mofile = WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) . '/languages/' . $domain . '-' . $locale . '.mo';
    }
    return $mofile;
}
add_filter( 'load_textdomain_mofile', 'OCSCW_load_my_own_textdomain', 10, 2 );