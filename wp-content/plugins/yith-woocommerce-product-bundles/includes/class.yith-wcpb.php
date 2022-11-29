<?php
/**
 * Main class
 *
 * @author  YITH
 * @package YITH\ProductBundles
 */

defined( 'YITH_WCPB' ) || exit;

if ( ! class_exists( 'YITH_WCPB' ) ) {
	/**
	 * YITH WooCommerce Product Bundles
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCPB {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCPB
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Plugin version
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $version = YITH_WCPB_VERSION;

		/**
		 * The admin instance.
		 *
		 * @var YITH_WCPB_Admin|YITH_WCPB_Admin_Premium
		 */
		public $admin;

		/**
		 * The frontend instance.
		 *
		 * @var YITH_WCPB_Frontend|YITH_WCPB_Frontend_Premium
		 */
		public $frontend;

		/**
		 * The compatibility instance.
		 *
		 * @var YITH_WCPB_Compatibility_Premium|YITH_WCPB_Compatibility
		 */
		public $compatibility;

		/**
		 * The bundle product version
		 *
		 * @var string
		 * @since 1.4.0
		 */
		protected $bundle_product_version = '1.4.0';

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WCPB|YITH_WCPB_Premium
		 */
		public static function get_instance() {
			/**
			 * The class.
			 *
			 * @var YITH_WCPB|YITH_WCPB_Premium $self
			 */
			$self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

			return ! is_null( $self::$instance ) ? $self::$instance : $self::$instance = new $self();
		}

		/**
		 * Constructor
		 */
		protected function __construct() {

			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

			if ( is_admin() ) {
				$this->admin = yith_wcpb_admin();
			}

			$this->frontend = yith_wcpb_frontend();

			$this->compatibility = YITH_WCPB_Compatibility::get_instance();

			add_filter( 'product_type_selector', array( $this, 'product_type_selector' ) );
		}

		/**
		 * Add Product Bundle type to product types.
		 *
		 * @param array $types The product types.
		 *
		 * @see    wc_get_product_types() function.
		 * @since  1.4.11
		 */
		public function product_type_selector( $types ) {
			$types['yith_bundle'] = _x( 'Product Bundle', 'Admin: type of product', 'yith-woocommerce-product-bundles' );

			return $types;
		}


		/**
		 * Load Plugin Framework
		 *
		 * @return void
		 * @since  1.0
		 * @access public
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once $plugin_fw_file;
				}
			}
		}

		/**
		 * Retrieve the bundle product version
		 *
		 * @return string
		 * @since 1.4.0
		 */
		public function get_bundle_product_version() {
			return $this->bundle_product_version;
		}
	}
}

/**
 * Unique access to instance of YITH_WCPB class
 *
 * @return YITH_WCPB|YITH_WCPB_Premium
 * @since 1.0.0
 */
function yith_wcpb() {
	return YITH_WCPB::get_instance();
}
