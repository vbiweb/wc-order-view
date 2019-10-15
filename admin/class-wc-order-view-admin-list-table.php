<?php

/**
 * Order View List Table Class
 *
 * @author K Gopal Krishna
 * @copyright   Copyright (c) Visual BI Solutions
 * @since 1.0.0
 *
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


 if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}



class WC_Order_View_ListTable extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Order', 'wc-order-view' ), //singular name of the listed records
			'plural'   => __( 'Orders', 'wc-order-view' ), //plural name of the listed records
			'ajax'     => false //should this table support ajax?
		] );

	}

	/**
	 * Retrieve customer’s data from the database
	 *
	 * @param 	int $per_page
	 * @param 	int $page_number
	 *
	 * @return 	mixed
	 * @since   1.0.0
	 */
	public function get_orders( $per_page = 5, $page_number = 1 ) {

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

			$search_meta[] = array ( 'key' => '_billing_first_name', value => $_REQUEST[ 's' ], 'compare' => 'LIKE'  );
			$search_meta[] = array ( 'key' => '_billing_last_name', value => $_REQUEST[ 's' ], 'compare' => 'LIKE'  );
			$search_meta[] = array ( 'key' => '_billing_email', value => $_REQUEST[ 's' ], 'compare' => 'LIKE'  );
			$search_meta[] = array ( 'key' => '_billing_company', value => $_REQUEST[ 's' ], 'compare' => 'LIKE'  );
			$search_meta[] = array ( 'key' => '_invoice_number_display', value => $_REQUEST[ 's' ], 'compare' => 'LIKE'  );

			$meta_query[] = $search_meta;
		}		

		$args = array (
			'posts_per_page'   => $per_page,
			'offset'           => ( $page_number - 1 ) * $per_page,
			'orderby'          => 'date',
			'order'            => 'DESC',
			'post_type'        => 'shop_order',
			'post_status'      => ( isset ( $_REQUEST[ 'post_status' ] ) && $_REQUEST[ 'post_status' ] != "" ) ? $_REQUEST[ 'post_status' ] : array( 'wc-completed', 'wc-on-hold' ),
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

		$orders = array();

		foreach ( $result as $result_item ) {
			
			$order_item = array();

			$order = new WC_Order( $result_item->ID );

			$billing_address = apply_filters( 'woocommerce_order_formatted_billing_address', $order->get_address( 'billing' ), $order );
        	$billing_address = WC()->countries->get_formatted_address( $billing_address, ', ' );

        	$shipping_address = apply_filters( 'woocommerce_order_formatted_shipping_address', $order->get_address( 'shipping' ), $order );
        	$shipping_address = WC()->countries->get_formatted_address( $shipping_address, ', ' );

			$order_item[ 'order_id' ] 		  = $order->get_id();
			$order_item[ 'name' ] 			  = $order->get_billing_first_name() . " " . $order->get_billing_last_name();
			$order_item[ 'date' ] 			  = $result_item->post_date;
			$order_item[ 'status' ]			  = $result_item->post_status;
			$order_item[ 'billing_address' ]  = $billing_address ? $billing_address : "–";
			$order_item[ 'shipping_address' ] = $shipping_address ? $shipping_address : "–";
			$order_item[ 'total' ] 			  = $order->get_total();
			
			$orders[] = $order_item;

		}

		return $orders;

	}


	/**
	 * Returns the count of records in the database.
	 *
	 * @return int
	 * @since  1.0.0
	 */
	public function record_count() {

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
			'post_type'        => 'shop_order',
			'post_status'      => ( isset ( $_REQUEST[ 'post_status' ] ) && $_REQUEST[ 'post_status' ] != "" ) ? $_REQUEST[ 'post_status' ] : array( 'wc-completed', 'wc-on-hold' ),
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
		
		$result = count ( get_posts ( $args ) );

		return $result;

	}


	/** Text displayed when no customer data is available */
	public function no_items() {

		_e( 'No orders avaliable.', 'wc-order-view' );

	}

	/**
	 *  Associative array of columns
	 *
	 * @return array
	 * @since  1.0.0
	 */
	function get_columns() {
		
		$columns = array(
			'cb' 				=> __( '<input type="checkbox" />', 'wc-order-view' ),
			'order_id'			=> __( 'Order', 'wc-order-view' ),
			'name'    			=> __( 'Name', 'wc-order-view' )
		);

		$columns[ 'date' ] 				= __( 'Date', 'wc-order-view' );
		$columns[ 'status' ] 			= __( 'Status', 'wc-order-view' );
		$columns[ 'billing_address' ] 	= __( 'Billing', 'wc-order-view' );
		$columns[ 'shipping_address' ] 	= __( 'Ship To', 'wc-order-view' );
		$columns[ 'total' ] 			= __( 'Total', 'wc-order-view' );


		return $columns;

	}


	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 * @since   1.0.0
	 */
	public function get_sortable_columns() {

		$sortable_columns = array(
			'order_id' => array( 'order_id', true ),
			'date' => array( 'date', true ),
			'total' => array( 'total', true )
		);

		return $sortable_columns;

	}


	/**
	 * Hidden Columns.
	 *
	 * @return array
	 * @since   1.0.0
	 */
	public function get_hidden_columns() {

		$hidden_columns = array( 'name', 'billing_address', 'shipping_address' );

		return $hidden_columns;

	}

	/**
	 * Render a column when no column specific method exists.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 * @since   1.0.0
	 */
	public function column_default( $item, $column_name ) {
		
		switch ( $column_name ) {
			case 'order_id' :
			case 'name' :
			case 'status' :
			case 'date' :
			case 'billing_address' :
			case 'shipping_address' :
			case 'total' :
				return $item[ $column_name ];
				break;
			default :
				//return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}

	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 * @since   1.0.0
	 */
	function column_cb( $item ) {
		
		return sprintf( '<input type="checkbox" name="bulk-export[]" value="%s" />', $item[ 'order_id' ] );
	
	}

	/**
	 * Render the order column
	 *
	 * @param array $item
	 *
	 * @return string
	 * @since   1.0.0
	 */
	function column_order_id( $item ) {
		
		$order_id  = "";
		$order_id .= '<a href="#" class="order-preview" data-order-id="' . $item[ 'order_id' ] . '" title="Preview">Preview</a>';
		$order_id .= '<a href="#" class="order-view"><strong>#' . $item[ 'order_id' ] .' '. $item[ 'name' ] . '</strong></a>';

		return $order_id;

	}

	/**
	 * Render the order status column
	 *
	 * @param array $item
	 *
	 * @return string
	 * @since   1.0.0
	 */
	function column_status( $item ) {

		$order_statuses = wc_get_order_statuses();
		
		$status  = "";
		$status .= '<mark class="order-status status-' . substr( $item[ 'status' ], 3) . ' tips">';
		$status .= '<span>' . $order_statuses[ $item[ 'status' ] ] . '</span>';
		$status .= '</mark>';

		return $status;

	}

	/**
	 * Render the order totals column
	 *
	 * @param array $item
	 *
	 * @return string
	 * @since   1.0.0
	 */
	function column_total( $item ) {

		$payment_method = get_post_meta ( $item[ 'order_id' ], '_payment_method_title', true );

		$price  = "";
		$price .= '<div class="tooltip">' . wc_price( $item[ 'total' ] ) . '<span class="tooltiptext">via ' . $payment_method . '</span></div>';

		return  $price;

	}	

	/**
	 * Render the order date column
	 *
	 * @param array $item
	 *
	 * @return string
	 * @since   1.0.0
	 */
	function column_date( $item ) {

		$date = '<time datetime="' . date( "c", strtotime( $item[ 'date' ] ) ) . '" title="' . date( "F j, Y g:i A", strtotime( $item[ 'date' ] ) ) . '">' . date( "M j, Y", strtotime( $item[ 'date' ] ) ) . '</time>';

		return $date;

	}

	/**
	 * Render the billing column
	 *
	 * @param array $item
	 *
	 * @return string
	 * @since   1.0.0
	 */
	function column_billing_address( $item ) {

		$billing_address  = $item[ 'billing_address' ];
		$billing_address .= '<span class="description"> via ' . get_post_meta ( $item[ 'order_id' ], '_payment_method_title', true ) . '</span>';

		return $billing_address;

	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 * @since   1.0.0
	 */
	public function get_bulk_actions() {
		
		$actions = array(
			'bulk-export' => 'Export to CSV'
		);

		return $actions;

	}

	/**
	 * Sorts the result based on orderby parameters
	 *
	 * @return int
	 * @since   1.0.0
	 */
	function usort_reorder( $a, $b ) {
		
		// If no sort, default to title
		$orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'order_id';
		
		// If no order, default to asc
		$order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'desc';
		
		// Determine sort order
		if ( $orderby == 'date' ) {
			$result = strcmp( strtotime( $a[ $orderby ] ), strtotime( $b[ $orderby ] ) );
		} else {
			$result = strcmp( $a[ $orderby ] , $b[ $orderby ] );
		}
		// Send final sort direction to usort
		return ( $order === 'asc' ) ? $result : -$result;

	}

	/**
	 * Handles data query and filter, sorting, and pagination.
	 *
	 * @since   1.0.0
	 */
	public function prepare_items() {

		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		//$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'orders_per_page', 5 );
		$current_page = $this->get_pagenum();
		$total_items  = $this->record_count();

		$this->set_pagination_args( array(
		'total_items' => $total_items, //WE have to calculate the total number of items
		'per_page'    => $per_page //WE have to determine how many items to show on a page
		) );

		$orders = $this->get_orders( $per_page, $current_page );

		usort( $orders, array( &$this, 'usort_reorder' ) );

		$this->items = $orders;

	}


	public function extra_tablenav( $which ) {

		global $wpdb;

		$query = "SELECT DISTINCT DATE_FORMAT ( post_date, '%Y%m' ) AS monthyear FROM wp_posts WHERE post_type = 'shop_order' ORDER BY post_date DESC";
		$monthyears = $wpdb->get_results($query);

		$users = get_users();

		$available_gateways = WC()->payment_gateways->get_available_payment_gateways();
	    
	    if ( 'top' === $which ) : ?>
	    	
	    	<div class="alignleft actions">
	    		<label class="screen-reader-text" for="filter-by-date">Filter by date</label>
	    		<select name="m">
	    			<option value="" <?php echo ( $_REQUEST[ 'm' ] == "" ) ? 'selected' : ''; ?> >All Dates</option>
	    			<?php
	    				if( count( $monthyears ) > 0 ) {
	    					foreach ( $monthyears as $monthyear ) {
	    						if( $_REQUEST[ 'm' ] == $monthyear->monthyear ) {
	    							echo '<option value="'. $monthyear->monthyear .'" selected >'. date( "F Y", strtotime( $monthyear->monthyear . "01" ) ) .'</option>';	
	    						} else {
	    							echo '<option value="'. $monthyear->monthyear .'" >'. date( "F Y", strtotime( $monthyear->monthyear . "01" ) ) .'</option>';		
	    						}
	    					}	
	    				}
	    			?>
	    		</select>
	    		<select id="wc-order-view-customer-search" name="_customer_user">
	    			<option></option>
	    			<?php
	    				foreach ( $users as $user ) {
	    					$first_name  = get_user_meta( $user->ID, 'billing_first_name', true );
	    					$last_name   = get_user_meta( $user->ID, 'billing_last_name', true );

	    					$user_option = $first_name . ' ' . $last_name . ' (#' . $user->ID . ' – ' . $user->user_email . ')';

	    					if( $_REQUEST[ '_customer_user' ] == $user->ID ) {
	    						echo '<option value="'. $user->ID .'" selected >'. $user_option .'</option>';	
	    					} else {
	    						echo '<option value="'. $user->ID .'" >'. $user_option .'</option>';
	    					}
	    				}
	    			?>
	    		</select>
	    		<select name="_payment_method">
	    			<option value="" <?php echo ( $_REQUEST[ '_payment_method' ] == "" ) ? 'selected' : ''; ?> >Any Payment Method</option>
	    			<?php 
	    				foreach ( $available_gateways as $payment_gateway => $gateway_info ) {
	    					if( $_REQUEST[ '_payment_method' ] == $payment_gateway ) {
	    						echo '<option value="'. $payment_gateway .'" selected >'. $gateway_info->title .'</option>';	
	    					} else {
	    						echo '<option value="'. $payment_gateway .'" >'. $gateway_info->title .'</option>';
	    					}	
	    				}
	    			?>
	    			<option value="other" <?php echo ( $_REQUEST[ '_payment_method' ] == "other" ) ? 'selected' : ''; ?> >Other</option>
	    		</select>
	    		<input type="submit" name="filter_action" id="wc-order-view-fliter" class="button" value="Filter">
	    	</div>

	    <?php endif;

	}

}