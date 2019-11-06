<?php

/**
 * Displays the API Manager keys metabox.
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

$is_subscription_screen = wcs_is_subscription( $post->ID );

if( get_option( 'wcov_api_manager' ) == 'enabled' && ! $is_subscription_screen && in_array( "woocommerce-api-manager/woocommerce-api-manager.php" , $active_plugins ) ) { ?>

	<div id="wc_am_api_keys" class="postbox ">
		<button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: API Key Products</span><span class="toggle-indicator" aria-hidden="true"></span></button>
		<h2 class="hndle ui-sortable-handle"><span>API Key Products</span></h2>
		<div class="inside">
			<?

			$original_order_check   = false;
			$is_renewal             = false;

			if ( WC_AM_SUBSCRIPTION()->is_wc_subscriptions_active() ) {
				
				$is_renewal = WC_AM_SUBSCRIPTION()->is_subscription_renewal_order( $post->ID );

				$original_order_check   = get_post_meta( $post->ID, '_original_order', true );
				$sub_status             = WC_AM_SUBSCRIPTION()->get_user_subscription_status( $post->ID );

				if ( $is_renewal || ( ! WC_AM_SUBSCRIPTION()->is_user_subscription_active( $sub_status ) && ! empty( $original_order_check ) ) ) {
					?><p style="padding:0 8px;"><?php _e( 'This is a subscription renewal order. Delete any API Keys or activations that exist on this order.', 'wc-order-view' ) ?></p><?php
				}
			}

			$customer_user_id = get_post_meta( $post->ID, '_customer_user', true );

			if ( ! empty( $customer_user_id ) ) {

				$user_id = $customer_user_id;

			}

			if ( ! empty( $user_id ) ) {
				$user_orders = WC_AM_HELPERS()->get_users_data( $user_id );
				$i = 0;
				?>
				<div class="api_order_licence_keys wc-metaboxes-wrapper">
					<?php
						// Sort API Keys from low to high value
						ksort( $user_orders );

						foreach ( $user_orders as $order_key => $data ) {
							// Get the old order_key or the new API License Key
							$order_data_key = ( ! empty( $data['api_key'] ) ) ? $data['api_key'] : $data['order_key'];

							if ( ! empty( $data ) ) {
								// Get the order_key portion of the API License Key
								$order_key_prefix           = WC_AM_HELPERS()->get_uniqid_prefix( $order_data_key, '_am_' );
								$post_meta_order_key_prefix = WC_AM_HELPERS()->get_uniqid_prefix( $order_key, '_am_' );

								/**
								 * Get the order_key portion of the API License Key and match that with the post meta order_key
								 * Also match the post meta post_id with the order_id, which are the same thing with a different name
								 */
								if ( $order_key_prefix == $post_meta_order_key_prefix && $post->ID == $data['order_id'] ) {
								// Get activation info
								$current_info       = WC_AM_HELPERS()->get_users_activation_data( $data['user_id'], $data['order_key'] );
								$active_activations = 0;

						    	if ( ! empty( $current_info ) ) foreach ( $current_info as $key => $activations ) {
						    		if ( $activations['activation_active'] == 1 && $order_data_key == $activations['order_key'] ) {
										$active_activations++;
						    		}
						    	}

						    	$num_activations = ( $active_activations  > 0 ) ? $active_activations : 0;

								// Activations limit or unlimited
								if ( $data['is_variable_product'] == 'no' && $data['_api_activations_parent'] != '' ) {
									$activations_limit = absint( $data['_api_activations_parent'] );
								} elseif ( $data['is_variable_product'] =='no' && $data['_api_activations_parent'] == '' ) {
									$activations_limit = 'unlimited';
								} elseif ( $data['is_variable_product'] == 'yes' && $data['_api_activations'] != '' ) {
									$activations_limit = absint( $data['_api_activations'] );
								} elseif ( $data['is_variable_product'] == 'yes' && $data['_api_activations'] == '' ) {
									$activations_limit = 'unlimited';
								}

								// Software Title
								if ( $data['is_variable_product'] == 'no' ) {
									$software_title = sanitize_text_field( $data['_api_software_title_parent'] );
								} elseif ( $data['is_variable_product'] == 'yes' ) {
									$software_title = sanitize_text_field( $data['_api_software_title_var'] );
								} else {
									$software_title = sanitize_text_field( $data['software_title'] );
								}

								$i++;

								?>

								<div class="wc-metaboxes">
									<div class="wc-metabox closed">
										<h3 class="fixed">
											<div class="handlediv" title="<?php _e( 'Click to toggle', 'wc-order-view' ); ?>"></div>
											<strong><?php printf( __( 'Software : %s | Activations: %s out of %s | API Access: %s | Version: %s | API Key: %s', 'wc-order-view' ), $software_title, $num_activations, $activations_limit, $data['_api_update_permission'], $data['current_version'], '<span class="am_tooltip" title="' . $order_data_key . '"> &hellip; ' . WC_AM_HELPERS()->get_last_string_characters( $order_data_key, 4 ) . '</span>' ); ?></strong>
										</h3>
										<table cellpadding="0" cellspacing="0" class="wc-metabox-content" style="display: none;">
											<tbody>
												<tr>
													<td>
														<label><?php ( defined( 'WPLANG' == 'en_GB' ) && WPLANG == 'en_GB' ) ? _e( 'API Licence Key:', 'wc-order-view' ) : _e( 'API License Key:', 'wc-order-view' ); ?></label>
														<input type="text" class="short am_expand_text_box" name="api_key[<?php echo $i; ?>]" value="<?php echo $order_data_key; ?>" readonly />
													</td>
													<td>
														<label><?php _e( 'Activation Limit', 'wc-order-view' ); ?>:</label>
													<?php
													if ( $data['is_variable_product'] =='no' ) :
													?>
														<input type="text" class="short" name="_api_activations_parent[<?php echo $i; ?>]" value="<?php echo $data['_api_activations_parent'] ?>" placeholder="<?php _e( 'Unlimited', 'wc-order-view' ); ?>" readonly />
													<?php
													elseif ( $data['is_variable_product'] == 'yes' ) :
													?>
														<input type="text" class="short" name="_api_activations[<?php echo $i; ?>]" value="<?php echo $data['_api_activations'] ?>" placeholder="<?php _e( 'Unlimited', 'wc-order-view' ); ?>" readonly />
													<?php
													endif;
													?>
													</td>
													<td>
														<label><?php _e( 'API Access Permission', 'wc-order-view' ); ?>:</label>
														<input type="checkbox" class="am_checkbox" name="_api_update_permission[<?php echo $i; ?>]" value="yes" <?php checked( $data['_api_update_permission'], 'yes' ); ?> readonly />
													</td>
												</tr>
												<tr>
													<td>
														<label><?php _e( 'Software Title', 'wc-order-view' ); ?>:</label>
													<?php
													if ( $data['is_variable_product'] =='no' ) :
													?>
														<input type="text" class="am_tooltip short am_expand_text_box" title="The Software Title should not be changed, because it must match the Software Title in the API form for the product, otherwise the API Manager will not work for this product on this customer order." name="_api_software_title_parent[<?php echo $i; ?>]" value="<?php echo $data['_api_software_title_parent']; ?>" placeholder="<?php _e( 'Required', 'wc-order-view' ); ?>" readonly />
													<?php
													elseif ( $data['is_variable_product'] == 'yes' ) :
													?>
														<input type="text" class="am_tooltip short am_expand_text_box" title="The Software Title should not be changed, because it must match the Software Title in the API form for the product, otherwise the API Manager will not work for this product on this customer order." name="_api_software_title_var[<?php echo $i; ?>]" value="<?php echo $data['_api_software_title_var']; ?>" placeholder="<?php _e( 'Required', 'wc-order-view' ); ?>" readonly />
													<?php
													endif;
													?>
													</td>
													<td>
														<label><?php ( defined( 'WPLANG' == 'en_GB' ) && WPLANG == 'en_GB' ) ? _e( 'API Licence Email:', 'wc-order-view' ) : _e( 'API License Email:', 'wc-order-view' ); ?></label>
														<input type="text" class="short" name="license_email[<?php echo $i; ?>]" value="<?php echo $data['license_email']; ?>" placeholder="<?php _e( 'Email Required', 'wc-order-view' ); ?>" readonly />
													</td>
													<td>
														<label><?php _e( 'Software Version', 'wc-order-view' ); ?>:</label>
														<input type="text" class="short" name="current_version[<?php echo $i; ?>]" value="<?php echo $data['current_version']; ?>" readonly />
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>

								<?php

								} // end if order_key and post_id match for this order
							} // end if $data
						} // end foreach
					?>
				</div>
				<?php
			} // end f $user_id ?>
		</div>
	</div>

<?php }