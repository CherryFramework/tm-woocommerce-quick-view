<?php
/**
 * Settings management class.
 *
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'TM_Woo_Quick_View_Settings' ) ) {

	/**
	 * Define TM_Woo_Quick_View_Settings class
	 */
	class TM_Woo_Quick_View_Settings {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Constructor for the class
		 */
		function __construct() {

			add_action( 'woocommerce_settings_start', array( $this, 'register_settings' ) );
			add_action( 'woocommerce_settings_tm_woo_quick_view', array( $this, 'render_settings_page' ) );
			add_action( 'woocommerce_update_options_tm_woo_quick_view', array( $this, 'update_options' ) );

			// register filter hooks
			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'register_settings_tab' ), PHP_INT_MAX );
		}

		public function get_settings() {

			return apply_filters( 'tw_woo_quick_view_settings', array(
				array(
					'id'    => 'general-options',
					'type'  => 'title',
					'title' => __( 'General Options', 'tm-woocommerce-quick-view' ),
				),
				array(
					'type'    => 'checkbox',
					'id'      => 'tm_woo_quick_view_on',
					'title'   => __( 'Enable Quick View button', 'tm-woocommerce-quick-view' ),
					'desc'    => __( 'Show/Hide Quick View button', 'tm-woocommerce-quick-view' ),
					'default' => 'yes'
				),
				array(
					'type'    => 'checkbox',
					'id'      => 'tm_woo_quick_view_nav',
					'title'   => __( 'Enable product navigation mode', 'tm-woocommerce-quick-view' ),
					'desc'    => __( 'Show/Hide prev and next product buttons', 'tm-woocommerce-quick-view' ),
					'default' => 'yes'
				),
				array( 'type' => 'sectionend', 'id' => 'general-options' )
			) );
		}


		/**
		 * Registers plugin settings in the WooCommerce settings array.
		 *
		 * @since 1.0.0
		 * @action woocommerce_settings_start
		 *
		 * @global array $woocommerce_settings WooCommerce settings array.
		 */
		public function register_settings() {

			global $woocommerce_settings;

			$woocommerce_settings['tm_woo_quick_view'] = $this->get_settings();
		}

		/**
		 * Registers WooCommerce settings tab which will display the plugin settings.
		 *
		 * @since 1.0.0
		 * @filter woocommerce_settings_tabs_array PHP_INT_MAX
		 *
		 * @param array $tabs The array of already registered tabs.
		 * @return array The extended array with the plugin tab.
		 */
		public function register_settings_tab( $tabs ) {

			$tabs['tm_woo_quick_view'] = esc_html__( 'TM Quick View', 'tm-woocommerce-quick-view' );

			return $tabs;
		}

		/**
		 * Renders plugin settings tab.
		 *
		 * @since 1.0.0
		 * @action woocommerce_settings_tm_woocompare_list
		 *
		 * @global array $woocommerce_settings The aggregate array of WooCommerce settings.
		 * @global string $current_tab The current WooCommerce settings tab.
		 */
		public function render_settings_page() {

			global $woocommerce_settings, $current_tab;

			if ( function_exists( 'woocommerce_admin_fields' ) ) {

				woocommerce_admin_fields( $woocommerce_settings[$current_tab] );
			}
		}

		/**
		 * Updates plugin settings after submission.
		 *
		 * @since 1.0.0
		 * @action woocommerce_update_options_tm_woocompare_list
		 */
		public function update_options() {

			if ( function_exists( 'woocommerce_update_options' ) ) {

				woocommerce_update_options( $this->get_settings() );
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
 * Returns instance of TM_Woo_Quick_View_Settings
 *
 * @return object
 */
function tm_woo_quick_view_settings() {
	return TM_Woo_Quick_View_Settings::get_instance();
}

tm_woo_quick_view_settings();
