<?php
/**
 * Assets management class.
 *
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'TM_Woo_Quick_View_Assets' ) ) {

	/**
	 * Define TM_Woo_Quick_View_Assets class
	 */
	class TM_Woo_Quick_View_Assets {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Single page products list.
		 *
		 * @var array
		 */
		private static $products_list = array();

		/**
		 * CSS links array
		 *
		 * @var array
		 */
		public static $css = array();

		/**
		 * Constructor for the class
		 */
		function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
			add_action( 'wp_footer', array( $this, 'print_product_ids' ) );
			add_action( 'wp_footer', array( $this, 'print_css_tag' ) );
		}

		/**
		 * Add product id into list
		 *
		 * @param  int $id Product ID.
		 * @return void
		 */
		public function add_product( $id ) {
			self::$products_list[] = $id;
		}

		/**
		 * Print products list into JS variable
		 *
		 * @return void
		 */
		public function print_product_ids() {
			$this->print_script_var( 'tmQuickViewIds', self::$products_list );
		}

		/**
		 * Print JS variable into script tag
		 *
		 * @param  string $name variable name.
		 * @param  mixed  $data variable value.
		 * @return void
		 */
		public function print_script_var( $name, $data ) {
			printf(
				"<script type=\"text/javascript\">/* <![CDATA[ */\n var %s = %s\n/* ]]> */\n</script>",
				$name,
				json_encode( $data )
			);
		}

		/**
		 * Register plugin assets
		 *
		 * @return void
		 */
		public function register_assets() {

			$handle = 'tm-woo-quick-view';

			wp_register_script(
				$handle,
				tm_woo_quick_view()->plugin_url( 'public/assets/js/tm-quick-view.js' ),
				array( 'jquery' ),
				'1.0.0',
				true
			);

			wp_localize_script( $handle, 'tmQuickViewData', array(
				'ajaxurl'  => esc_url( admin_url( 'admin-ajax.php' ) ),
				'handle'   => $handle,
				'loader'   => tm_woo_quick_view_render()->popup_loader( false ),
				'isSingle' => is_product(),
			) );

			wp_register_style(
				$handle,
				tm_woo_quick_view()->plugin_url( 'public/assets/css/tm-quick-view.css' ),
				array(),
				'1.0.0'
			);

		}

		/**
		 * Initalize plugin assets
		 *
		 * @return void
		 */
		public function init_assets() {

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				return;
			}

			global $wp_styles;
			$handle = 'tm-woo-quick-view';
			ob_start();
			$wp_styles->do_item( $handle );
			self::$css[] = ob_get_clean();
			wp_enqueue_script( $handle );
		}

		/**
		 * Print CSS tags array
		 *
		 * @return void
		 */
		public function print_css_tag() {
			$this->print_script_var( 'tmQuickViewCSS', self::$css );
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
 * Returns instance of TM_Woo_Quick_View_Assets
 *
 * @return object
 */
function tm_quick_view_assets() {
	return TM_Woo_Quick_View_Assets::get_instance();
}

tm_quick_view_assets();
