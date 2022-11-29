<?php
/**
 * The plugin gutenberg block Initializer.
 *
 * @link       https://shapedplugin.com/
 * @since      3.4.6
 *
 * @package    Logo_Carousel_Free
 * @subpackage Logo_Carousel_Free/Admin
 * @author     ShapedPlugin <support@shapedplugin.com>
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Logo_Carousel_Free_Gutenberg_Block_Init' ) ) {
	/**
	 * Logo_Carousel_Free_Gutenberg_Block_Init class.
	 */
	class Logo_Carousel_Free_Gutenberg_Block_Init {
		/**
		 * Custom Gutenberg Block Initializer.
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'splcf_gutenberg_shortcode_block' ) );
			add_action( 'enqueue_block_editor_assets', array( $this, 'splcf_block_editor_assets' ) );
		}

		/**
		 * Register block editor script for backend.
		 */
		public function splcf_block_editor_assets() {
			wp_enqueue_script(
				'sp-logo-carousel-free-shortcode-block',
				plugins_url( '/GutenbergBlock/build/index.js', dirname( __FILE__ ) ),
				array( 'jquery' ),
				SP_LC_VERSION,
				true
			);

			/**
			 * Register block editor css file enqueue for backend.
			 */
			$setting_data     = get_option( '_sp_lcpro_options' );
			$lcpro_swiper_css = isset( $setting_data['lcpro_swiper_css'] ) ? $setting_data['lcpro_swiper_css'] : true;
			$font_awesome_css = isset( $setting_data['lcpro_fontawesome_css'] ) ? $setting_data['lcpro_fontawesome_css'] : true;
			$lcpro_swiper_js  = isset( $setting_data['lcpro_swiper_js'] ) ? $setting_data['lcpro_swiper_js'] : true;
			// CSS Files.
			if ( $lcpro_swiper_css ) {
				wp_enqueue_style( 'sp-lc-swiper', SP_LC_URL . 'public/assets/css/swiper-bundle.min.css', array(), SP_LC_VERSION );
			}
			if ( $font_awesome_css ) {
				wp_enqueue_style( 'sp-lc-font-awesome', SP_LC_URL . 'public/assets/css/font-awesome.min.css', array(), SP_LC_VERSION );
			}
			wp_enqueue_style( 'sp-lc-style', SP_LC_URL . 'public/assets/css/style.min.css', array(), SP_LC_VERSION );
		}

		/**
		 * Shortcode list.
		 *
		 * @return array
		 */
		public function splcf_post_list() {
			$shortcodes = get_posts(
				array(
					'post_type'      => 'sp_lc_shortcodes',
					'post_status'    => 'publish',
					'posts_per_page' => 9999,
				)
			);

			if ( count( $shortcodes ) < 1 ) {
				return array();
			}

			return array_map(
				function ( $shortcode ) {
					return (object) array(
						'id'    => absint( $shortcode->ID ),
						'title' => esc_html( $shortcode->post_title ),
					);
				},
				$shortcodes
			);
		}

		/**
		 * Register Gutenberg shortcode block.
		 */
		public function splcf_gutenberg_shortcode_block() {
			/**
			 * Register block editor js file enqueue for backend.
			 */
			$setting_data    = get_option( '_sp_lcpro_options' );
			$lcpro_swiper_js = isset( $setting_data['lcpro_swiper_js'] ) ? $setting_data['lcpro_swiper_js'] : true;
			if ( $lcpro_swiper_js ) {
				wp_register_script( 'sp-lc-swiper-js', SP_LC_URL . 'public/assets/js/swiper-bundle.min.js', array( 'jquery' ), SP_LC_VERSION, true );
			}
			wp_register_script( 'sp-lc-script', SP_LC_URL . 'public/assets/js/splc-script.min.js', array( 'jquery' ), SP_LC_VERSION, true );

			wp_localize_script(
				'sp-lc-script',
				'sp_logo_carousel_free_g',
				array(
					'path'          => SP_LC_URL,
					'loadScript'    => SP_LC_URL . 'public/assets/js/splc-script.min.js',
					'url'           => admin_url( 'post-new.php?post_type=sp_lc_shortcodes' ),
					'shortCodeList' => $this->splcf_post_list(),
				)
			);

			/**
			 * Register Gutenberg block on server-side.
			 */
			register_block_type(
				'sp-logo-carousel-pro/shortcode',
				array(
					'attributes'      => array(
						'shortcode'          => array(
							'type'    => 'string',
							'default' => '',
						),
						'showInputShortcode' => array(
							'type'    => 'boolean',
							'default' => true,
						),
						'is_admin'           => array(
							'type'    => 'boolean',
							'default' => is_admin(),
						),
						'preview'            => array(
							'type'    => 'boolean',
							'default' => false,
						),
					),
					'example'         => array(
						'attributes' => array(
							'preview' => true,
						),
					),
					// Enqueue blocks.editor.build.js in the editor only.
					'editor_script'   => array(
						'sp-lc-swiper-js',
						'sp-lc-script',
					),
					// Enqueue blocks.editor.build.css in the editor only.
					'editor_style'    => array(),
					'render_callback' => array( $this, 'sp_logo_carousel_render_shortcode' ),
				)
			);
		}

		/**
		 * Render callback.
		 *
		 * @param string $attributes Shortcode.
		 * @return string
		 */
		public function sp_logo_carousel_render_shortcode( $attributes ) {

			$class_name = '';
			if ( ! empty( $attributes['className'] ) ) {
				$class_name = 'class="' . $attributes['className'] . '"';
			}

			if ( ! $attributes['is_admin'] ) {
				return '<div ' . $class_name . ' >' . do_shortcode( '[logocarousel id="' . sanitize_text_field( $attributes['shortcode'] ) . '"]' ) . '</div>';
			}

			return '<div id="' . uniqid() . '" ' . $class_name . '>' . do_shortcode( '[logocarousel id="' . sanitize_text_field( $attributes['shortcode'] ) . '"]' ) . '</div>';
		}
	}
}
