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

		wp_register_script( 'wc_order_view_select2', "https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.full.min.js", array( 'jquery' ), '4.0.7', true );
		wp_enqueue_script( 'wc_order_view_select2' );

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
			'default' => 20,
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

			if( isset( $_GET[ 'order_id' ] ) ) {

				$post = get_post( $_GET[ 'order_id' ] );

				$order = new WC_Order( $_GET[ 'order_id' ] );

				$user = $order->get_user();

				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wc-order-view-order-details-display.php';

			}
		
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


	/**
	 * Method to export all orders in CSV Format
	 *
	 * @since    1.3.0
	 */
	public function bulk_export_to_csv() {

		if( ( isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] == "wcov-export" ) || ( isset( $_GET[ 'action2' ] ) && $_GET[ 'action2' ] == "wcov-export" ) ) {

			$export_ids = esc_sql( $_GET['bulk-export'] );

			$orders = array();

			$active_plugins = get_option( 'active_plugins' );

			if( empty( $export_ids ) ) {

				$meta_query = array();
				$meta_query['relation'] = "AND";

				if ( isset ( $_REQUEST[ '_customer_user' ] ) && $_REQUEST[ '_customer_user' ] != "" ) {
					$meta_query[] = array ( 'key' => '_customer_user', value => $_REQUEST[ '_customer_user' ] );
				}

				if ( isset ( $_REQUEST[ '_payment_method' ] ) && $_REQUEST[ '_payment_method' ] != "" ) {
					$meta_query[] = array ( 'key' => '_payment_method', value => $_REQUEST[ '_payment_method' ] );
				}

				if ( isset ( $_REQUEST[ 's' ] ) && $_REQUEST[ 's' ] != "" ) {
					$search_meta = array();
					$search_meta[ 'relation' ] = "OR";

					$search_meta[] = array ( 'key' => '_billing_first_name', value => $_REQUEST[ 's' ], 'compare' => 'LIKE' );
					$search_meta[] = array ( 'key' => '_billing_last_name', value => $_REQUEST[ 's' ], 'compare' => 'LIKE' );
					$search_meta[] = array ( 'key' => '_billing_email', value => $_REQUEST[ 's' ], 'compare' => 'LIKE' );
					$search_meta[] = array ( 'key' => '_billing_company', value => $_REQUEST[ 's' ], 'compare' => 'LIKE' );
					$search_meta[] = array ( 'key' => '_invoice_number_display', value => $_REQUEST[ 's' ], 'compare' => 'LIKE' );

					$meta_query[] = $search_meta;
				}		

				$args = array (
					'numberposts'      => -1,
					'post_type'        => 'shop_order',
					'post_status'      => ( isset ( $_REQUEST[ 'post_status' ] ) && $_REQUEST[ 'post_status' ] != "" ) ? $_REQUEST[ 'post_status' ] : array_keys( wc_get_order_statuses() ),
					'meta_query'       => $meta_query
				);

				if( isset ( $_REQUEST[ 'author' ] ) && $_REQUEST[ 'author' ] != "" ) {
					$args[ 'author' ] = $_REQUEST[ 'author' ];
				}

				if ( isset ( $_REQUEST[ 'm' ] ) && $_REQUEST[ 'm' ] != "" ) {
					$year  = date( "Y", strtotime ( $_REQUEST[ 'm' ] . "01" ) );
					$month = date( "m", strtotime ( $_REQUEST[ 'm' ] . "01" ) );

					$date_query = array(
				        array(
				            'year'  => $year,
					        'month' => $month
						),
					);

					$args[ 'date_query' ] = $date_query;
					
				}
				
				$result = get_posts ( $args );

				foreach ( $result as $result_item ) {
					
					$order_item = array();

					$order = new WC_Order( $result_item->ID );
					$order_items = $order->get_items( 'line_item' );

					$billing_address = apply_filters( 'woocommerce_order_formatted_billing_address', $order->get_address( 'billing' ), $order );
		        	$billing_address = WC()->countries->get_formatted_address( $billing_address, ', ' );

		        	$shipping_address = apply_filters( 'woocommerce_order_formatted_shipping_address', $order->get_address( 'shipping' ), $order );
		        	$shipping_address = WC()->countries->get_formatted_address( $shipping_address, ', ' );

		        	$products = array();

		        	foreach ( $order_items as $item ) {
		        		$products[] = $item->get_quantity() . ' x [' . $item->get_name() . ']';
		        	}

					$order_item[ 'Order ID' ] 		  = $order->get_id();
					$order_item[ 'Date' ] 			  = date( "d-m-Y", strtotime( $order->get_date_completed() ) );
					$order_item[ 'Order Status' ]	  = ucfirst( $order->get_status() );
					$order_item[ 'First Name' ] 	  = $order->get_billing_first_name();
					$order_item[ 'Last Name' ] 	  	  = $order->get_billing_last_name();
					$order_item[ 'Email' ] 	  		  = $order->get_billing_email();
					$order_item[ 'Phone' ] 	  		  = $order->get_billing_phone();
					$order_item[ 'Company' ] 	  	  = $order->get_billing_company();
					$order_item[ 'Billing Address' ]  = $billing_address ? $billing_address : "";
					$order_item[ 'Shipping Address' ] = $shipping_address ? $shipping_address : "";
					$order_item[ 'Products' ] 		  = implode("; ", $products);
					$order_item[ 'Payment Method' ]	  = $order->get_payment_method_title();
					$order_item[ 'Total' ] 			  = $order->get_total();

					if( get_option( 'wcov_subscriptions' ) == "enabled" && in_array( "woocommerce-subscriptions/woocommerce-subscriptions.php" , $active_plugins ) ) {

						$subscription_relationship = "";

						if ( wcs_order_contains_subscription( $order->get_id(), 'renewal' ) ) {
							$subscription_relationship = "Renewal Order";
						} elseif ( wcs_order_contains_subscription( $order->get_id(), 'resubscribe' ) ) {
							$subscription_relationship = "Resubscribtion Order";
						} elseif ( wcs_order_contains_subscription( $order->get_id(), 'parent' ) ) {
							$subscription_relationship = "Parent Order";
						}

						$order_item[ 'Subscription Relationship' ] = $subscription_relationship;

					}

					if( get_option( 'wcov_pdf_invoices' ) == "enabled" && in_array( "woocommerce-pdf-invoice/woocommerce-pdf-invoice.php" , $active_plugins ) ) {

						$order_item[ 'Invoice Number' ] = get_post_meta( $order->get_id(), '_invoice_number_display', true );

					}
					
					$orders[] = $order_item;

				}

			} else {

				foreach ( $export_ids as $export_id ) {
					
					$order_item = array();

					$order = new WC_Order( $export_id );
					$order_items = $order->get_items( 'line_item' );

					$billing_address = apply_filters( 'woocommerce_order_formatted_billing_address', $order->get_address( 'billing' ), $order );
		        	$billing_address = WC()->countries->get_formatted_address( $billing_address, ', ' );

		        	$shipping_address = apply_filters( 'woocommerce_order_formatted_shipping_address', $order->get_address( 'shipping' ), $order );
		        	$shipping_address = WC()->countries->get_formatted_address( $shipping_address, ', ' );

		        	$products = array();

		        	foreach ( $order_items as $item ) {
		        		$products[] = $item->get_quantity() . ' x [' . $item->get_name() . ']';
		        	}

					$order_item[ 'Order ID' ] 		  = $order->get_id();
					$order_item[ 'Date' ] 			  = date( "d-m-Y", strtotime( $order->get_date_completed() ) );
					$order_item[ 'Order Status' ]	  = ucfirst( $order->get_status() );
					$order_item[ 'First Name' ] 	  = $order->get_billing_first_name();
					$order_item[ 'Last Name' ] 	  	  = $order->get_billing_last_name();
					$order_item[ 'Email' ] 	  		  = $order->get_billing_email();
					$order_item[ 'Phone' ] 	  		  = $order->get_billing_phone();
					$order_item[ 'Company' ] 	  	  = $order->get_billing_company();
					$order_item[ 'Billing Address' ]  = $billing_address ? $billing_address : "";
					$order_item[ 'Shipping Address' ] = $shipping_address ? $shipping_address : "";
					$order_item[ 'Products' ] 		  = implode("; ", $products);
					$order_item[ 'Payment Method' ]	  = $order->get_payment_method_title();
					$order_item[ 'Total' ] 			  = $order->get_total();

					if( get_option( 'wcov_subscriptions' ) == "enabled" && in_array( "woocommerce-subscriptions/woocommerce-subscriptions.php" , $active_plugins ) ) {

						$subscription_relationship = "";

						if ( wcs_order_contains_subscription( $order->get_id(), 'renewal' ) ) {
							$subscription_relationship = "Renewal Order";
						} elseif ( wcs_order_contains_subscription( $order->get_id(), 'resubscribe' ) ) {
							$subscription_relationship = "Resubscribtion Order";
						} elseif ( wcs_order_contains_subscription( $order->get_id(), 'parent' ) ) {
							$subscription_relationship = "Parent Order";
						}

						$order_item[ 'Subscription Relationship' ] = $subscription_relationship;

					}

					if( get_option( 'wcov_pdf_invoices' ) == "enabled" && in_array( "woocommerce-pdf-invoice/woocommerce-pdf-invoice.php" , $active_plugins ) ) {

						$order_item[ 'Invoice Number' ] = get_post_meta( $order->get_id(), '_invoice_number_display', true );

					}
					
					$orders[] = $order_item;

				}

			}

			$date = date("d-m-Y");

			$header = array( 'Order ID', 'Date', 'Order Status', 'First Name', 'Last Name', 'Email', 'Phone', 'Company', 'Billing Address', 'Shipping Address', 'Products', 'Payment Method', 'Total' );

			if( get_option( 'wcov_subscriptions' && in_array( "woocommerce-subscriptions/woocommerce-subscriptions.php" , $active_plugins ) ) == "enabled" ) {
				$header[] = "Subscription Relationship";
			}

			if( get_option( 'wcov_pdf_invoices' ) == "enabled" && in_array( "woocommerce-pdf-invoice/woocommerce-pdf-invoice.php" , $active_plugins ) ) {
				$header[] = "Invoice Number";
			}

			header("Content-Type: application/csv");
			header("Content-Disposition: attachment;Filename=wc-order-view-export-$date.csv");

			$export_file = fopen('php://output', 'w+');

			fputcsv( $export_file, $header );

			foreach ( $orders as $order ) {
				
				$record = array();

				foreach ( $header as $field ) {
					
					$record[] = $order[ $field ];				

				}

				fputcsv( $export_file, $record );

			}

			fclose($export_file);
			
			die();

		}

	}

}
