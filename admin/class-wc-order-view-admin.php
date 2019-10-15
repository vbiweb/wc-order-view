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
		add_submenu_page('wc-order-view', 'Order View - Settings', 'Settings', 'products_admin', 'wc-order-view-settings', array( $this , 'wc_order_view_settings_page' ));

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

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wc-order-view-admin-display.php';
		
	}

	/**
	 * Hook to register the settings fields for WC Order View.
	 *
	 * @since    1.0.0
	 */
	public function register_wc_order_view_settings() {

		//general tab settings
		add_settings_section("wc_order_view_general_settings", "General Settings", null, "wc-order-view-settings");

		add_settings_field("wcov_display_mode", "Display Mode", array( $this, "wcov_display_mode_callback" ), "wc-order-view-settings", "wc_order_view_general_settings");
		add_settings_field("wcov_display_billing_info", "Billing Details", array( $this, "wcov_display_billing_info_callback" ), "wc-order-view-settings", "wc_order_view_general_settings");
		add_settings_field("wcov_display_shipping_info", "Shipping Details", array( $this, "wcov_display_shipping_info_callback" ), "wc-order-view-settings", "wc_order_view_general_settings");
		add_settings_field("wcov_display_order_notes", "Order Notes", array( $this, "wcov_display_order_notes_callback" ), "wc-order-view-settings", "wc_order_view_general_settings");

		register_setting( 'wc_order_view_general_settings' , 'wcov_display_mode');
		register_setting( 'wc_order_view_general_settings' , 'wcov_display_billing_info');
		register_setting( 'wc_order_view_general_settings' , 'wcov_display_shipping_info');
		register_setting( 'wc_order_view_general_settings' , 'wcov_display_order_notes');

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
