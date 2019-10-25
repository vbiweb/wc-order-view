<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://kgopalkrishna.com
 * @since      1.0.0
 *
 * @package    Wc_Order_View
 * @subpackage Wc_Order_View/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wc_Order_View
 * @subpackage Wc_Order_View/admin
 * @author     K Gopal Krishna <kggopal12@gmail.com>
 */
class Wc_Order_View_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The list table object for Order_view.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      WC_Order_View_ListTable    $wc_order_view_list_table    The list table object for Order_view.
	 */
	public $wc_order_view_list_table;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wc_Order_View_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wc_Order_View_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wc-order-view-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wc_Order_View_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wc_Order_View_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_register_script('wc_order_view_select2', "https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.full.min.js", array('jquery'), '4.0.7', true);
		wp_enqueue_script('wc_order_view_select2');

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wc-order-view-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Setting screen options hook.
	 *
	 * @since    1.0.0
	 */
	public static function set_screen( $status, $option, $value ) {

		return $value;

	}

	/**
	 * Admin menu hook for adding WC Order View menu.
	 *
	 * @since    1.0.0
	 */
	public function wc_order_view_menus() {

		$hook = add_menu_page( 'WC Orders', 'WC Orders', 'products_admin', 'wc-order-view', array ( $this, 'wc_order_view_page') , 'dashicons-list-view', 55 );
		$hook = add_submenu_page('wc-order-view', 'All Orders', 'All Orders', 'products_admin', 'wc-order-view', array( $this , 'wc_order_view_page' ));
		add_submenu_page('wc-order-view', 'Order View - Settings', 'Settings', 'administrator', 'wc-order-view-settings', array( $this , 'wc_order_view_settings_page' ));

		add_action( "load-$hook", array( $this, 'screen_option' ) );

	}

	/**
	 * Screen options for WC Order View List Table.
	 *
	 * @since    1.0.0
	 */
	public function screen_option() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wc-order-view-admin-list-table.php';

		$option = 'per_page';
		$args   = array(
			'label'   => 'Orders',
			'default' => 5,
			'option'  => 'orders_per_page'
		);

		add_screen_option( $option, $args );

		$this->wc_order_view_list_table = new WC_Order_View_ListTable();

	}

	/**
	 * WC Order View main page containing the List Table.
	 *
	 * @since    1.0.0
	 */
	public function wc_order_view_page() {

		if( isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] == "view" ) {

			$post = new WP_Post( $_GET[ 'order_id' ] );

			$order = new WC_Order( $_GET[ 'order_id' ] );

			$user = $order->get_user();

			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wc-order-view-order-details-display.php';
		
		} else {

			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wc-order-view-admin-display.php';

		}
		
	}

	/**
	 * Hook to register the settings fields for WC Order View.
	 *
	 * @since    1.0.0
	 */
	public function register_wc_order_view_settings() {

		add_settings_section("wc_order_view_general_settings", "General Settings", null, "wc-order-view-settings");

		add_settings_field("wcov_display_mode", "Display Mode", array( $this, "wcov_display_mode_callback" ), "wc-order-view-settings", "wc_order_view_general_settings");
		add_settings_field("wcov_display_billing_info", "Billing Details", array( $this, "wcov_display_billing_info_callback" ), "wc-order-view-settings", "wc_order_view_general_settings");
		add_settings_field("wcov_display_shipping_info", "Shipping Details", array( $this, "wcov_display_shipping_info_callback" ), "wc-order-view-settings", "wc_order_view_general_settings");
		add_settings_field("wcov_display_order_notes", "Order Notes", array( $this, "wcov_display_order_notes_callback" ), "wc-order-view-settings", "wc_order_view_general_settings");

		register_setting( 'wc_order_view_settings' , 'wcov_display_mode');
		register_setting( 'wc_order_view_settings' , 'wcov_display_billing_info');
		register_setting( 'wc_order_view_settings' , 'wcov_display_shipping_info');
		register_setting( 'wc_order_view_settings' , 'wcov_display_order_notes');

		add_settings_section("wc_order_view_third_party_plugins_settings", "Third Party Plugins Support", null, "wc-order-view-settings");
	
		add_settings_field("wcov_pdf_invoices", "PDF Invoices", array( $this, "wcov_pdf_invoices_callback" ), "wc-order-view-settings", "wc_order_view_third_party_plugins_settings");
		add_settings_field("wcov_subscriptions", "Woocommerce Subscriptions", array( $this, "wcov_subscriptions_callback" ), "wc-order-view-settings", "wc_order_view_third_party_plugins_settings");
		add_settings_field("wcov_api_manager", "Woocommerce API Manager", array( $this, "wcov_api_manager_callback" ), "wc-order-view-settings", "wc_order_view_third_party_plugins_settings");

		register_setting( 'wc_order_view_settings' , 'wcov_pdf_invoices');
		register_setting( 'wc_order_view_settings' , 'wcov_subscriptions');
		register_setting( 'wc_order_view_settings' , 'wcov_api_manager');

	}

	/**
	 * Callback to display the "Dispaly Mode" setting.
	 *
	 * @since    1.0.0
	 */
	public function wcov_display_mode_callback() {

		$current_option = get_option( 'wcov_display_mode' );

		if( $current_option == "" ) {
			update_option( 'wcov_display_mode', 'page' );
			$current_option = get_option( 'wcov_display_mode' );			
		} 

		?>
			<div class="switch-field">
				<input type="radio" id="wcov_display_mode_page" name="wcov_display_mode" value="page" <?php echo ( $current_option == "page" ) ? "checked" : ""; ?> />
				<label for="wcov_display_mode_page">Page</label>
				<input type="radio" id="wcov_display_mode_popup" name="wcov_display_mode" value="popup" <?php echo ( $current_option == "popup" ) ? "checked" : ""; ?> />
				<label for="wcov_display_mode_popup">Popup</label>
			</div>
		<?php

	}

	/**
	 * Callback to display the "Billing Details" setting.
	 *
	 * @since    1.0.0
	 */
	public function wcov_display_billing_info_callback() {

		$current_option = get_option( 'wcov_display_billing_info' );

		if( $current_option == "" ) {
			update_option( 'wcov_display_billing_info', 'show' );
			$current_option = get_option( 'wcov_display_billing_info' );			
		} 

		?>
			<div class="switch-field">
				<input type="radio" id="wcov_display_billing_info_show" name="wcov_display_billing_info" value="show" <?php echo ( $current_option == "show" ) ? "checked" : ""; ?> />
				<label for="wcov_display_billing_info_show">Show</label>
				<input type="radio" id="wcov_display_billing_info_hide" name="wcov_display_billing_info" value="hide" <?php echo ( $current_option == "hide" ) ? "checked" : ""; ?> />
				<label for="wcov_display_billing_info_hide">Hide</label>
			</div>
		<?php

	}

	/**
	 * Callback to display the "Shipping Details" setting.
	 *
	 * @since    1.0.0
	 */
	public function wcov_display_shipping_info_callback() {

		$current_option = get_option( 'wcov_display_shipping_info' );

		if( $current_option == "" ) {
			update_option( 'wcov_display_shipping_info', 'show' );
			$current_option = get_option( 'wcov_display_shipping_info' );			
		} 

		?>
			<div class="switch-field">
				<input type="radio" id="wcov_display_shipping_info_show" name="wcov_display_shipping_info" value="show" <?php echo ( $current_option == "show" ) ? "checked" : ""; ?> />
				<label for="wcov_display_shipping_info_show">Show</label>
				<input type="radio" id="wcov_display_shipping_info_hide" name="wcov_display_shipping_info" value="hide" <?php echo ( $current_option == "hide" ) ? "checked" : ""; ?> />
				<label for="wcov_display_shipping_info_hide">Hide</label>
			</div>
		<?php

	}

	/**
	 * Callback to display the "Order Notes" setting.
	 *
	 * @since    1.0.0
	 */
	public function wcov_display_order_notes_callback() {

		$current_option = get_option( 'wcov_display_order_notes' );

		if( $current_option == "" ) {
			update_option( 'wcov_display_order_notes', 'show' );
			$current_option = get_option( 'wcov_display_order_notes' );			
		} 

		?>
			<div class="switch-field">
				<input type="radio" id="wcov_display_order_notes_show" name="wcov_display_order_notes" value="show" <?php echo ( $current_option == "show" ) ? "checked" : ""; ?> />
				<label for="wcov_display_order_notes_show">Show</label>
				<input type="radio" id="wcov_display_order_notes_hide" name="wcov_display_order_notes" value="hide" <?php echo ( $current_option == "hide" ) ? "checked" : ""; ?> />
				<label for="wcov_display_order_notes_hide">Hide</label>
			</div>
		<?php

	}

	/**
	 * Callback to enable "PDF Invoices" setting.
	 *
	 * @since    1.0.0
	 */
	public function wcov_pdf_invoices_callback() {

		$active_plugins = get_option( 'active_plugins' );

		if( ! in_array( "woocommerce-pdf-invoice/woocommerce-pdf-invoice.php" , $active_plugins ) ) {
			?>
				This plugin is currently inactive / not installed in your site. To know more about this plugin please visit <a href="https://woocommerce.com/products/pdf-invoices/">PDF Invoices</a>
			<?php
		} else {
			$current_option = get_option( 'wcov_pdf_invoices' );

			if( $current_option == "" ) {
				update_option( 'wcov_pdf_invoices', 'disabled' );
				$current_option = get_option( 'wcov_pdf_invoices' );			
			} 

			?>
				<div class="switch-field">
					<input type="radio" id="wcov_pdf_invoices_enabled" name="wcov_pdf_invoices" value="enabled" <?php echo ( $current_option == "enabled" ) ? "checked" : ""; ?> />
					<label for="wcov_pdf_invoices_enabled">Enabled</label>
					<input type="radio" id="wcov_pdf_invoices_disabled" name="wcov_pdf_invoices" value="disabled" <?php echo ( $current_option == "disabled" ) ? "checked" : ""; ?> />
					<label for="wcov_pdf_invoices_disabled">Disabled</label>
				</div>
			<?php
		}
	}

	/**
	 * Callback to enable "Subscriptions" setting.
	 *
	 * @since    1.0.0
	 */
	public function wcov_subscriptions_callback() {

		$active_plugins = get_option( 'active_plugins' );

		if( ! in_array( "woocommerce-subscriptions/woocommerce-subscriptions.php" , $active_plugins ) ) {
			?>
				This plugin is currently inactive / not installed in your site. To know more about this plugin please visit <a href="https://woocommerce.com/products/woocommerce-subscriptions/">WooCommerce Subscriptions</a>
			<?php
		} else {
			$current_option = get_option( 'wcov_subscriptions' );

			if( $current_option == "" ) {
				update_option( 'wcov_subscriptions', 'disabled' );
				$current_option = get_option( 'wcov_subscriptions' );			
			} 

			?>
				<div class="switch-field">
					<input type="radio" id="wcov_subscriptions_enabled" name="wcov_subscriptions" value="enabled" <?php echo ( $current_option == "enabled" ) ? "checked" : ""; ?> />
					<label for="wcov_subscriptions_enabled">Enabled</label>
					<input type="radio" id="wcov_subscriptions_disabled" name="wcov_subscriptions" value="disabled" <?php echo ( $current_option == "disabled" ) ? "checked" : ""; ?> />
					<label for="wcov_subscriptions_disabled">Disabled</label>
				</div>
			<?php
		}
	}

	/**
	 * Callback to enable "API Manager" setting.
	 *
	 * @since    1.0.0
	 */
	public function wcov_api_manager_callback() {

		$active_plugins = get_option( 'active_plugins' );

		if( ! in_array( "woocommerce-api-manager/woocommerce-api-manager.php" , $active_plugins ) ) {
			?>
				This plugin is currently inactive / not installed in your site. To know more about this plugin please visit <a href="https://woocommerce.com/products/woocommerce-api-manager/">WooCommerce API Manager</a>
			<?php
		} else {
			$current_option = get_option( 'wcov_api_manager' );

			if( $current_option == "" ) {
				update_option( 'wcov_api_manager', 'disabled' );
				$current_option = get_option( 'wcov_api_manager' );			
			} 

			?>
				<div class="switch-field">
					<input type="radio" id="wcov_api_manager_enabled" name="wcov_api_manager" value="enabled" <?php echo ( $current_option == "enabled" ) ? "checked" : ""; ?> />
					<label for="wcov_api_manager_enabled">Enabled</label>
					<input type="radio" id="wcov_api_manager_disabled" name="wcov_api_manager" value="disabled" <?php echo ( $current_option == "disabled" ) ? "checked" : ""; ?> />
					<label for="wcov_api_manager_disabled">Disabled</label>
				</div>
			<?php
		}
	}

	/**
	 * WC Order View settings page that controls enabling and disabling of certain key features.
	 *
	 * @since    1.0.0
	 */
	public function wc_order_view_settings_page() {

		$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';
        
        $tabs = array( 'general' => 'General', 'export' => 'Export', 'third-party-plugins' => 'Third Party Plugins' );

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wc-order-view-admin-settings-display.php';

	}

}
