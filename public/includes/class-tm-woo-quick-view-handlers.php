<?php
/**
 * Ajax handlers class
 *
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'TM_Woo_Quick_View_Handlers' ) ) {

	/**
	 * Define TM_Woo_Quick_View_Handlers class
	 */
	class TM_Woo_Quick_View_Handlers {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Trigger if quick view request currently processing.
		 *
		 * @var boolean
		 */
		public $is_quick_view = false;

		/**
		 * Constructor for the class
		 */
		function __construct() {

			add_action( 'wp_ajax_tm_woo_quick_view', array( $this, 'quick_view' ) );
			add_action( 'wp_ajax_nopriv_tm_woo_quick_view', array( $this, 'quick_view' ) );

			add_action( 'tm_woo_quick_view_before_content', array( $this, 'remove_actions' ) );

			// 3rd party hacks
			add_action( 'tm_woo_quick_view_before_content', array( $this, 'extensions' ) );
		}

		/**
		 * Main quick view handler
		 *
		 * @return void
		 */
		public function quick_view() {

			if ( empty( $_REQUEST['product'] ) ) {
				wp_send_json_error( array(
					'content' => esc_html__( 'Product ID not passed', 'tm-woocommerce-quick-view' ),
				) );
			}

			$pid      = intval( $_REQUEST['product'] );
			$raw_post = get_post( $pid );

			if ( ! $raw_post || is_wp_error( $raw_post ) || 'product' !== $raw_post->post_type ) {
				wp_send_json_error( array(
					'content' => esc_html__( 'Invalid Product ID was passed', 'tm-woocommerce-quick-view' ),
				) );
			}

			global $post, $product;

			$factory = new WC_Product_Factory();
			$product = $factory->get_product( $pid );
			$post    = $raw_post;
			$product = $product;

			$this->is_quick_view = true;

			ob_start();

			do_action( 'tm_woo_quick_view_before_content' );

			$template = tm_woo_quick_view_render()->locate_template( 'quick-view-content.php' );

			if ( ! file_exists( $template ) ) {
				wp_send_json_error( array(
					'content' => esc_html__( 'Template file not found', 'tm-woocommerce-quick-view' ),
				) );
			}

			include $template;

			do_action( 'tm_woo_quick_view_after_content' );

			$content = ob_get_clean();

			wp_send_json_success( array(
				'content' => $content,
			) );

		}

		/**
		 * Remove unnecessary actions
		 *
		 * @return void
		 */
		public function remove_actions() {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
			remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
		}

		/**
		 * Adds better compatibility with some 3rd party plugins.
		 *
		 * @return void
		 */
		public function extensions() {
			if ( function_exists( 'toastie_wc_smsb_form_code' ) ) {
				remove_action( 'woocommerce_single_product_summary', 'toastie_wc_smsb_form_code', 31 );
				remove_action( 'woocommerce_single_product_summary', 'toastie_wc_smsb_form_code', 45 );
			}
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}
	}

}

/**
 * Returns instance of TM_Woo_Quick_View_Handlers
 *
 * @return object
 */
function tm_woo_quick_view_handlers() {
	return TM_Woo_Quick_View_Handlers::get_instance();
}

tm_woo_quick_view_handlers();
