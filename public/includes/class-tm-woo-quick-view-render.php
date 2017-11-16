<?php
/**
 * Frontend render class
 *
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'TM_Woo_Quick_View_Render' ) ) {

	/**
	 * Define TM_Woo_Quick_View_Render class
	 */
	class TM_Woo_Quick_View_Render {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Trigger popup init state
		 *
		 * @since 1.0.0
		 * @var boolean
		 */
		private static $popup_initalized = false;

		/**
		 * Constructor for the class
		 */
		function __construct() {

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				$this->init_button();
			} else {
				add_action( 'wp', array( $this, 'init_button' ) );
			}
		}

		/**
		 * Initalize Quick View button.
		 *
		 * @return void
		 */
		public function init_button() {

			if ( ! $this->is_popup_enabled() ) {
				return null;
			}

			$hook_data = $this->get_hook_data();
			add_action( $hook_data['hook'], $hook_data['render'], $hook_data['priority'] );
		}

		/**
		 * Initalize Quick View popup
		 *
		 * @return null
		 */
		public function init_popup() {

			if ( ! $this->is_popup_enabled() ) {
				return null;
			}

			if ( true === self::$popup_initalized ) {
				return null;
			}

			wp_enqueue_script( 'underscore' );
			wp_enqueue_script( 'wp-util' );
			wp_enqueue_script( 'zoom' );
			wp_enqueue_script( 'wc-single-product' );
			wp_enqueue_script( 'wc-add-to-cart' );
			wp_enqueue_script( 'wc-add-to-cart-variation' );

			add_action( 'wp_footer', array( $this, 'render_popup' ) );
			tm_quick_view_assets()->init_assets();

			self::$popup_initalized = true;

		}

		/**
		 * Check if popup enabled
		 *
		 * @return boolean
		 */
		public function is_popup_enabled() {
			$enabled = get_option( 'tm_woo_quick_view_on', 'yes' );
			return ( 'yes' === $enabled );
		}

		/**
		 * Render Quick View popup.
		 *
		 * @return void
		 */
		public function render_popup() {

			$popup_template = $this->locate_template( 'quick-view-popup.php' );

			wc_get_template( 'single-product/add-to-cart/variation.php' );

			if ( $popup_template ) {
				include $popup_template;
			}

		}

		/**
		 * Show close button.
		 *
		 * @return void
		 */
		public function close_button() {

			$template = $this->locate_template( 'quick-view-close.php' );

			if ( $template ) {
				include $template;
			}

		}

		/**
		 * Show prev/next buttons
		 *
		 * @return void
		 */
		public function prev_next_buttons() {

			$show_buttons = get_option( 'tm_woo_quick_view_nav', 'yes' );

			if ( 'yes' !== $show_buttons ) {
				return;
			}

			$template = $this->locate_template( 'quick-view-prev-next.php' );

			if ( $template ) {
				include $template;
			}
		}

		/**
		 * Render Quick view button.
		 *
		 * @return void
		 */
		public function render_button() {

			$this->init_popup();

			$button_template = $this->locate_template( 'quick-view-button.php' );

			tm_quick_view_assets()->add_product( get_the_id() );

			if ( $button_template ) {
				include $button_template;
			}

		}

		/**
		 * Show popup loader.
		 *
		 * @return void|string
		 */
		public function popup_loader( $echo = true ) {

			$loader_template = $this->locate_template( 'quick-view-loader.php' );

			if ( ! $loader_template ) {
				return;
			}

			ob_start();
			include $loader_template;
			$loader = ob_get_clean();

			if ( true === $echo ) {
				echo $loader;
			} else {
				return $loader;
			}

		}

		/**
		 * Returns string with required data attributes.
		 *
		 * @return string
		 */
		public function button_data_attr() {

			$result  = ' data-action="quick-view-button"';
			$result .= ' data-product="' . get_the_id() . '"';

			return $result;
		}

		/**
		 * Locate plugin template file
		 *
		 * @param  string $template Template name
		 * @return string
		 */
		public function locate_template( $template = null ) {

			$theme_path  = 'woocommerce/';
			$plugin_path = 'templates/';

			$file = locate_template( array( $theme_path . $template ) );

			if ( ! $file ) {
				$file = tm_woo_quick_view()->plugin_path( $plugin_path . $template );
			}

			if ( file_exists( $file ) ) {
				return $file;
			} else {
				return null;
			}

		}

		/**
		 * Returns hook name for quick view button.
		 *
		 * @return array
		 */
		public function get_hook_data() {
			return apply_filters( 'tm_woo_quick_view_button_hook', array(
				'hook'     => 'woocommerce_after_shop_loop_item',
				'priority' => 10,
				'render'   => array( $this, 'render_button' ),
			) );
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
 * Returns instance of TM_Woo_Quick_View_Render
 *
 * @return object
 */
function tm_woo_quick_view_render() {
	return TM_Woo_Quick_View_Render::get_instance();
}

tm_woo_quick_view_render();
