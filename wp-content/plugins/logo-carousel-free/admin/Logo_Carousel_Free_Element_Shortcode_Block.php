<?php
/**
 * Elementor ShortCode Block.
 *
 * @since       3.4.7
 * @package    Logo_Carousel_Free
 * @subpackage Logo_Carousel_Free/Admin
 * @author     ShapedPlugin <support@shapedplugin.com>
 */

/**
 * Logo_Carousel_Free_Element_Shortcode_Block
 */
class Logo_Carousel_Free_Element_Shortcode_Block {

	/**
	 * Instance
	 *
	 * @since  3.4.7
	 *
	 * @access private
	 * @static
	 *
	 * @var Logo_Carousel_Free_Element_Shortcode_Block The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since  3.4.7
	 *
	 * @access public
	 * @static
	 *
	 * @return Elementor_Test_Extension An instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor
	 *
	 * @since  3.4.7
	 *
	 * @access public
	 */
	public function __construct() {
		$this->on_plugins_loaded();
		add_action( 'wp_enqueue_scripts', array( $this, 'splcf_block_enqueue_scripts' ) );
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'splcf_element_shortcode_block_icon' ) );
	}

	/**
	 * Elementor block icon.
	 *
	 * @since     3.4.7
	 * @return void
	 */
	public function splcf_element_shortcode_block_icon() {
		wp_enqueue_style( 'sp_lcf_element_block_icon', SP_LC_URL . 'admin/assets/css/fontello.css', array(), SP_LC_VERSION, 'all' );
	}

	/**
	 * Register the JavaScript for the elementor block area.
	 *
	 * @since     3.4.7
	 */
	public function splcf_block_enqueue_scripts() {
		/**
		* Register element editor script for backend.
		*/
		wp_enqueue_style( 'sp-lc-swiper', SP_LC_URL . 'public/assets/css/swiper-bundle.min.css', array(), SP_LC_VERSION );
		wp_enqueue_style( 'sp-lc-font-awesome', SP_LC_URL . 'public/assets/css/font-awesome.min.css', array(), SP_LC_VERSION );
		wp_enqueue_style( 'sp-lc-style', SP_LC_URL . 'public/assets/css/style.min.css', array(), SP_LC_VERSION );

		wp_enqueue_script( 'sp-lc-swiper-js', SP_LC_URL . 'public/assets/js/swiper-bundle.min.js', array( 'jquery' ), SP_LC_VERSION, true );
		wp_enqueue_script( 'sp-lc-script', SP_LC_URL . 'public/assets/js/splc-script.min.js', array( 'jquery' ), SP_LC_VERSION, true );
	}

	/**
	 * On Plugins Loaded
	 *
	 * Checks if Elementor has loaded, and performs some compatibility checks.
	 * If All checks pass, inits the plugin.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since  3.4.7
	 *
	 * @access public
	 */
	public function on_plugins_loaded() {
		add_action( 'elementor/init', array( $this, 'init' ) );
	}

	/**
	 * Initialize the plugin
	 *
	 * Load the plugin only after Elementor (and other plugins) are loaded.
	 * Load the files required to run the plugin.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since  3.4.7
	 *
	 * @access public
	 */
	public function init() {
		// Add Plugin actions.
		add_action( 'elementor/widgets/register', array( $this, 'init_widgets' ) );
	}

	/**
	 * Init Widgets
	 *
	 * Include widgets files and register them
	 *
	 * @since  3.4.7
	 *
	 * @access public
	 */
	public function init_widgets() {
		// Register widget.
		require_once SP_LC_PATH . 'admin/ElementBlock/Logo_Carousel_Free_Element_Shortcode_Widget.php';
		\Elementor\Plugin::instance()->widgets_manager->register( new Logo_Carousel_Free_Element_Shortcode_Widget() );

	}

}
Logo_Carousel_Free_Element_Shortcode_Block::instance();
