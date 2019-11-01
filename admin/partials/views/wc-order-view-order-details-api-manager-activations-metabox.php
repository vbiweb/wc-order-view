<?php

/**
 * Displays the API Manager activations metabox.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://kgopalkrishna.com
 * @since      1.0.1
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

if( get_option( 'wcov_api_manager' ) == 'enabled' ) { ?>

	<div id="wc_am_api_key_activations" class="postbox ">
		<button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: API Key Activations</span><span class="toggle-indicator" aria-hidden="true"></span></button>
		<h2 class="hndle ui-sortable-handle"><span>API Key Activations</span></h2>
		<div class="inside">
			<?php 

			$post_data = WC_AM_ARRAY()->get_meta_query_flattened( 'postmeta', $post->ID );

			if ( ! empty( $post_data ) ) {
				$post_meta_order_key    = ( isset( $post_data['_order_key'] ) ) ? $post_data['_order_key'] : '';
				$user_id                = ( isset( $post_data['_customer_user'] ) ) ? $post_data['_customer_user'] : '';
				$email                  = ( isset( $post_data['_billing_email'] ) ) ? $post_data['_billing_email'] : '';
			}

			if ( ! empty( $user_id ) && ! empty( $post_meta_order_key ) ) {
				$activations = WC_AM_HELPERS()->get_users_activation_data( $user_id, $post_meta_order_key );

				// Sort activations according to most recent activation time
				if ( $activations ) {
					foreach ( $activations as $key => $value ) {
						$time[$key] = $value['activation_time'];

					}

					if ( is_array( $time ) ) {
						array_multisort( $time, SORT_DESC, $activations );
					}

				}

				$num_activations = count( $activations );

			}

			if ( ! empty( $post_meta_order_key ) && ! empty( $user_id ) && ! empty( $email ) && $num_activations > 0 ) {
				?>

				<div class="woocommerce_order_items_wrapper">
					<table id="activations-table" class="woocommerce_order_items" cellspacing="0">
						<thead>
					    	<tr>
								<th><?php _e( 'API Key', 'wc-order-view' ) ?></th>
								<th><?php _e( 'Instance', 'wc-order-view' ) ?></th>
								<th><?php _e( 'Version', 'wc-order-view' ) ?></th>
								<th><?php _e( 'Software Title', 'wc-order-view' ) ?></th>
								<th><?php _e( 'Status', 'wc-order-view' ) ?></th>
								<th><?php _e( 'Date &amp; Time', 'wc-order-view' ) ?></th>
								<th><?php _e( 'Domain/Platform', 'wc-order-view' ) ?></th>
							</tr>
						</thead>
						<tbody>
					    	<?php $i = 1; foreach ( $activations as $activation ) : $i++ ?>
								<tr<?php if ( $i % 2 == 1 ) echo ' class="alternate"' ?>>
									<td ><?php echo sanitize_text_field( $activation['order_key'] ) ?></td>
									<td ><?php echo sanitize_text_field( $activation['instance'] ) ?></td>
									<td><?php echo ( ! empty( $activation['software_version'] ) ) ? sanitize_text_field( $activation['software_version'] ) : ''; ?></td>
									<td><?php echo sanitize_text_field( $activation['product_id'] ); ?></td>
									<?php
										// Get the user order info
										$data = WC_AM_HELPERS()->get_order_info_by_email_with_order_key( $email, $activation['order_key'] );
									?>
									<td class="activation_active"><?php echo ( $activation['activation_active'] && $data['_api_update_permission'] == 'yes' ) ? __( 'Active', 'wc-order-view' ) : __( 'Inactive', 'wc-order-view' ) ?></td>
									<td><?php echo date_i18n( __( 'M j\, Y \a\t h:i a', 'wc-order-view' ), strtotime( $activation['activation_time'] ) ) ?></td>
									<td><a href="<?php echo esc_url( $activation['activation_domain'] ) ?>" target="_blank"><?php echo WC_AM_HELPERS()->remove_url_prefix( $activation['activation_domain'] ) ?></a></td>
					      		</tr>
					    	<?php endforeach; ?>
						</tbody>
					</table>
				</div>

				<?php
			} else {
				?><p style="padding:0 8px;"><?php _e( 'No activations yet', 'wc-order-view' ) ?></p><?php
			}

			?>
		</div>
	</div>
<?php }