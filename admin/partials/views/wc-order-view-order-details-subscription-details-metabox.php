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

	<div id="woocommerce-subscription-schedule" class="postbox ">
		<button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Subscription Schedule</span><span class="toggle-indicator" aria-hidden="true"></span></button>
		<h2 class="hndle ui-sortable-handle"><span>Subscription Schedule</span></h2>
		<div class="inside">
			<?php 

			$is_subscription_screen = wcs_is_subscription( $post->ID );

			if ( $is_subscription_screen ) {
				$the_subscription = wcs_get_subscription( $post->ID );
			} elseif ( wcs_order_contains_subscription( $post->ID, array( 'parent', 'renewal' ) ) ) {
				$the_subscription = array_pop( wcs_get_subscriptions_for_order( $post->ID, array( 'order_type' => array( 'parent', 'renewal' ) ) ) );
			}

			?>
			<div class="wc-metaboxes-wrapper">

				<?php do_action( 'wcs_subscription_schedule_after_billing_schedule', $the_subscription ); ?>
				<table>
					<tbody>
						<tr id="billing-schedule">
							<td><strong><?php esc_html_e( 'Recurring:', 'woocommerce-subscriptions' ); ?></strong></td>
							<td><?php printf( '%s %s', esc_html( wcs_get_subscription_period_interval_strings( $the_subscription->get_billing_interval() ) ), esc_html( wcs_get_subscription_period_strings( 1, $the_subscription->get_billing_period() ) ) ); ?></td>
						</tr>
						<?php foreach ( wcs_get_subscription_date_types() as $date_key => $date_label ) : ?>
							<?php $internal_date_key = wcs_normalise_date_type_key( $date_key ) ?>
							<?php if ( false === wcs_display_date_type( $date_key, $the_subscription ) ) : ?>
								<?php continue; ?>
							<?php endif;?>
							<tr id="subscription-<?php echo esc_attr( $date_key ); ?>-date" class="date-fields">
								<td><strong><?php echo esc_html( $date_label ); ?>:</strong></td>
								<td><?php echo esc_html( $the_subscription->get_date_to_display( $internal_date_key ) ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
<?php }