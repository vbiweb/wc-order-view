<?php

/**
 * Displays the Subscription Related orders metabox.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://kgopalkrishna.com
 * @since      1.1.0
 *
 * @package    Wc_Order_View
 * @subpackage Wc_Order_View/admin/partials/views
 */

/**
 * Objects available in this context.
 *
 * @var      WP_Post     $post     The wordpress post object of the woocommerce order in this context .
 * @var      WC_Order    $order    The woocommerce order object initialized with the current order id.
 * @var      WP_User     $user     The wordpress user object initialized with the user associated with the current order.
 */

defined( 'ABSPATH' ) || exit;

if( get_option( 'wcov_subscriptions' ) == 'enabled' ) { ?>

	<div id="subscription_renewal_orders" class="postbox ">
		<button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Related Orders</span><span class="toggle-indicator" aria-hidden="true"></span></button>
		<h2 class="hndle ui-sortable-handle"><span>Related Orders</span></h2>
		<div class="inside">
			<?php 

			$subscriptions = array();
			$orders        = array();
			$is_subscription_screen = wcs_is_subscription( $post->ID );

			// On the subscription page, just show related orders
			if ( $is_subscription_screen ) {
				$this_subscription = wcs_get_subscription( $post->ID );
				$subscriptions[]   = $this_subscription;
			} elseif ( wcs_order_contains_subscription( $post->ID, array( 'parent', 'renewal' ) ) ) {
				$subscriptions = wcs_get_subscriptions_for_order( $post->ID, array( 'order_type' => array( 'parent', 'renewal' ) ) );
			}

			// First, display all the subscriptions
			foreach ( $subscriptions as $subscription ) {
				wcs_set_objects_property( $subscription, 'relationship', __( 'Subscription', 'wc-order-view' ), 'set_prop_only' );
				$orders[] = $subscription;
			}

			//Resubscribed
			$initial_subscriptions = array();

			if ( $is_subscription_screen ) {

				$initial_subscriptions = wcs_get_subscriptions_for_resubscribe_order( $this_subscription );

				$resubscribe_order_ids = WCS_Related_Order_Store::instance()->get_related_order_ids( $this_subscription, 'resubscribe' );

				foreach ( $resubscribe_order_ids as $wc_order_id ) {
					$wc_order    = wc_get_order( $wc_order_id );
					$relation = wcs_is_subscription( $wc_order ) ? _x( 'Resubscribed Subscription', 'relation to order', 'wc-order-view' ) : _x( 'Resubscribe Order', 'relation to order', 'wc-order-view' );
					wcs_set_objects_property( $wc_order, 'relationship', $relation, 'set_prop_only' );
					$orders[] = $wc_order;
				}
			} else if ( wcs_order_contains_subscription( $post->ID, array( 'resubscribe' ) ) ) {
				$initial_subscriptions = wcs_get_subscriptions_for_order( $post->ID, array( 'order_type' => array( 'resubscribe' ) ) );
			}

			foreach ( $initial_subscriptions as $subscription ) {
				wcs_set_objects_property( $subscription, 'relationship', _x( 'Initial Subscription', 'relation to order', 'wc-order-view' ), 'set_prop_only' );
				$orders[] = $subscription;
			}

			// Now, if we're on a single subscription or renewal order's page, display the parent orders
			if ( 1 == count( $subscriptions ) ) {
				foreach ( $subscriptions as $subscription ) {
					if ( $subscription->get_parent_id() ) {
						$wc_order = $subscription->get_parent();
						wcs_set_objects_property( $wc_order, 'relationship', _x( 'Parent Order', 'relation to order', 'wc-order-view' ), 'set_prop_only' );
						$orders[] = $wc_order;
					}
				}
			}

			// Finally, display the renewal orders
			foreach ( $subscriptions as $subscription ) {

				foreach ( $subscription->get_related_orders( 'all', 'renewal' ) as $wc_order ) {
					wcs_set_objects_property( $wc_order, 'relationship', _x( 'Renewal Order', 'relation to order', 'wc-order-view' ), 'set_prop_only' );
					$orders[] = $wc_order;
				}
			}

			$orders = apply_filters( 'woocommerce_subscriptions_admin_related_orders_to_display', $orders, $subscriptions, $post );

			?>
			<div class="woocommerce_subscriptions_related_orders">
				<table>
					<thead>
						<tr>
							<th><?php esc_html_e( 'Order Number', 'woocommerce-subscriptions' ); ?></th>
							<th><?php esc_html_e( 'Relationship', 'woocommerce-subscriptions' ); ?></th>
							<th><?php esc_html_e( 'Date', 'woocommerce-subscriptions' ); ?></th>
							<th><?php esc_html_e( 'Status', 'woocommerce-subscriptions' ); ?></th>
							<th><?php echo esc_html_x( 'Total', 'table heading', 'woocommerce-subscriptions' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php 

						foreach ( $orders as $wc_order ) {

							if ( wcs_get_objects_property( $wc_order, 'id' ) == $post->ID ) {
								continue;
							}
							
							$order_post = wcs_get_objects_property( $wc_order, 'post' );

							?>
							<tr>
								<td>
									<a href="<?php echo esc_url( admin_url ( 'admin.php?page=wc-order-view&action=view&order_id=' . wcs_get_objects_property( $wc_order, 'id' ) ) ); ?>">
										<?php echo sprintf( esc_html_x( '#%s', 'hash before order number', 'wc-order-view' ), esc_html( $wc_order->get_order_number() ) ); ?>
									</a>
								</td>
								<td>
									<?php echo esc_html( wcs_get_objects_property( $wc_order, 'relationship' ) ); ?>
								</td>
								<td>
									<?php
									$timestamp_gmt = wcs_get_objects_property( $wc_order, 'date_created' )->getTimestamp();
									if ( $timestamp_gmt > 0 ) {
										// translators: php date format
										$t_time          = get_the_time( _x( 'Y/m/d g:i:s A', 'post date', 'wc-order-view' ), $order_post );
										$date_to_display = ucfirst( wcs_get_human_time_diff( $timestamp_gmt ) );
									} else {
										$t_time = $date_to_display = __( 'Unpublished', 'wc-order-view' );
									} ?>
									<abbr title="<?php echo esc_attr( $t_time ); ?>">
										<?php echo esc_html( apply_filters( 'post_date_column_time', $date_to_display, $order_post ) ); ?>
									</abbr>
								</td>
								<td>
									<?php
									if ( wcs_is_subscription( $wc_order ) ) {
									    echo esc_html( wcs_get_subscription_status_name( $wc_order->get_status( 'view' ) ) );
									} else {
									    echo esc_html( wc_get_order_status_name( $wc_order->get_status( 'view' ) ) );
									}
									?>
								</td>
								<td>
									<span class="amount"><?php echo wp_kses( $wc_order->get_formatted_order_total(), array( 'small' => array(), 'span' => array( 'class' => array() ), 'del' => array(), 'ins' => array() ) ); ?></span>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
<?php }