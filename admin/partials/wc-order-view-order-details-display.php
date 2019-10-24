<?php

/**
 * Displays all information related to an order with view access alone.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://kgopalkrishna.com
 * @since      1.0.0
 *
 * @package    Wc_Order_View
 * @subpackage Wc_Order_View/admin/partials
 */

/**
 * Objects available in this context.
 *
 * @var      WC_Order    $order    The woocommerce order object initialized with the current order id.
 * @var      WP_User     $user     The wordpress user object initialized with the user associated with the current order.
 */

defined( 'ABSPATH' ) || exit;

if ( WC()->payment_gateways() ) {
	$payment_gateways = WC()->payment_gateways->payment_gateways();
} else {
	$payment_gateways = array();
}

$payment_method = $order->get_payment_method();

$payment_gateway     = wc_get_payment_gateway_by_order( $order );
$line_items          = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'line_item' ) );
$discounts           = $order->get_items( 'discount' );
$line_items_fee      = $order->get_items( 'fee' );
$line_items_shipping = $order->get_items( 'shipping' );
if ( wc_tax_enabled() ) {
	$order_taxes      = $order->get_taxes();
	$tax_classes      = WC_Tax::get_tax_classes();
	$classes_options  = wc_get_product_tax_class_options();
	$show_tax_columns = count( $order_taxes ) === 1;
}

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap order-view-order-details">
	<h2>
		<span class="main_title" tabindex="1">View Order</span> 
	</h2>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="postbox-container-1" class="postbox-container">
				<div id="side-sortables" class="meta-box-sortables ui-sortable">
					<div id="woocommerce-order-notes" class="postbox ">
						<button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Order notes</span><span class="toggle-indicator" aria-hidden="true"></span></button>
						<h2 class="hndle ui-sortable-handle"><span>Order notes</span></h2>
						<div class="inside">
							<?php 
								$args = array(
									'order_id' => $order->get_id(),
								);

								$notes = wc_get_order_notes( $args );
							?>
							<ul class="order_notes">
								<?php
								if ( $notes ) {
									foreach ( $notes as $note ) {
										$css_class   = array( 'note' );
										$css_class[] = $note->customer_note ? 'customer-note' : '';
										$css_class[] = 'system' === $note->added_by ? 'system-note' : '';
										$css_class   = apply_filters( 'woocommerce_order_note_class', array_filter( $css_class ), $note );
										?>
										<li rel="<?php echo absint( $note->id ); ?>" class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
											<div class="note_content">
												<?php echo wpautop( wptexturize( wp_kses_post( $note->content ) ) ); // @codingStandardsIgnoreLine ?>
											</div>
											<p class="meta">
												<abbr class="exact-date" title="<?php echo esc_attr( $note->date_created->date( 'Y-m-d H:i:s' ) ); ?>">
													<?php
													/* translators: %1$s: note date %2$s: note time */
													echo esc_html( sprintf( __( '%1$s at %2$s', 'wc-order-view' ), $note->date_created->date_i18n( wc_date_format() ), $note->date_created->date_i18n( wc_time_format() ) ) );
													?>
												</abbr>
												<?php
												if ( 'system' !== $note->added_by ) :
													/* translators: %s: note author */
													echo esc_html( sprintf( ' ' . __( 'by %s', 'wc-order-view' ), $note->added_by ) );
												endif;
												?>
											</p>
										</li>
										<?php
									}
								} else {
									?>
									<li><?php esc_html_e( 'There are no notes yet.', 'wc-order-view' ); ?></li>
									<?php
								}
								?>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<div id="postbox-container-2" class="postbox-container">
				<div id="normal-sortables" class="meta-box-sortables ui-sortable">
					<div id="woocommerce-order-data" class="postbox">
						<div class="inside">
							<div id="order_data" class="panel woocommerce-order-data">
								<h2 class="woocommerce-order-data__heading">
									<h2 class="woocommerce-order-data__heading">
										<?php
										/* translators: 1: order number */
										printf(
											esc_html__( 'Order #%1$s details', 'wc-order-view' ),
											esc_html( $order->get_order_number() )
										);
										?>
									</h2>
								</h2>
								<p class="woocommerce-order-data__meta order_number">
									<?php

										$meta_list = array();
										if ( $payment_method && 'other' !== $payment_method ) {
											/* translators: %s: payment method */
											$payment_method_string = sprintf(
												__( 'Payment via %s', 'wc-order-view' ),
												esc_html( isset( $payment_gateways[ $payment_method ] ) ? $payment_gateways[ $payment_method ]->get_title() : $payment_method )
											);
											if ( $transaction_id = $order->get_transaction_id() ) {
												$payment_method_string .= ' (' . esc_html( $transaction_id ) . ')';
											}
											$meta_list[] = $payment_method_string;
										}
										if ( $order->get_date_paid() ) {
											/* translators: 1: date 2: time */
											$meta_list[] = sprintf(
												__( 'Paid on %1$s @ %2$s', 'wc-order-view' ),
												wc_format_datetime( $order->get_date_paid() ),
												wc_format_datetime( $order->get_date_paid(), get_option( 'time_format' ) )
											);
										}
										if ( $ip_address = $order->get_customer_ip_address() ) {
											/* translators: %s: IP address */
											$meta_list[] = sprintf(
												__( 'Customer IP: %s', 'wc-order-view' ),
												'<span class="woocommerce-Order-customerIP">' . esc_html( $ip_address ) . '</span>'
											);
										}
										echo wp_kses_post( implode( '. ', $meta_list ) );

									?>
								</p>
								<div class="order_data_column_container">
									<div class="order_data_column">
										<h3>General</h3>
										<p class="form-field form-field-wide">
											<label for="order_date">Date Created:</label>
											<input class="date-picker" type="text" name="order_date" maxlength="10" value="<?php echo $order->get_date_created()->format ('Y-m-d'); ?>" readonly /> @ 
											<input class="hour" type="number" name="order_date_hour" min="0" max="23" step="1" value="<?php echo $order->get_date_created()->format ('H'); ?>" readonly /> :
											<input class="minute" type="number" name="order_date_minute" min="0" max="59" step="1" value="<?php echo $order->get_date_created()->format ('i'); ?>" readonly /> 
										</p>
										<p class="form-field form-field-wide wc-order-status">
											<label for="order_status">Status:</label>
											<input class="" type="text" name="order_status" value="<?php echo ucfirst( $order->get_status() ); ?>" readonly />
										</p>
										<p class="form-field form-field-wide wc-customer-user">
											<label for="customer_user">
												Customer:
												<a href="<?php echo admin_url( 'admin.php?page=wc-order-view&_customer_user=' . $user->ID ); ?>">View other orders →</a>
											</label>
											<input class="" type="text" name="order_status" value="<?php echo $user->first_name . ' ' . $user->last_name . ' (#' . $user->ID . ' - ' . $user->user_email . ')'  ?>" readonly />
										</p>
									</div>
									<div class="order_data_column">
										<h3>Billing</h3>
										<div class="address">
											<?php if( ! empty( $order->get_formatted_billing_address() ) ) : ?>
												<p><?php echo $order->get_formatted_billing_address(); ?></p>
											<?php else : ?>
												<p class="none_set"><strong>Address:</strong>No billing address set.</p>
											<?php endif; ?>
											<p>
												<strong>Email Address:</strong>
												<a href="mailto:<?php echo $order->get_billing_email(); ?>"><?php echo $order->get_billing_email(); ?></a>
											</p>
											<p>
												<strong>Phone:</strong>
												<a href="tel:<?php echo $order->get_billing_phone(); ?>"><?php echo $order->get_billing_phone(); ?></a>
											</p>
										</div>
									</div>
									<div class="order_data_column">
										<h3>Shipping</h3>
										<div class="address">
											<?php if( ! empty( $order->get_formatted_shipping_address() ) ) : ?>
												<p><?php echo $order->get_formatted_shipping_address(); ?></p>
											<?php else : ?>
												<p class="none_set"><strong>Address:</strong>No shipping address set.</p>
											<?php endif; ?>
										</div>
									</div>
								</div>
								<div class="clear"></div>
							</div>
						</div>
					</div>
					<div id="woocommerce-order-items" class="postbox">
						<div class="inside">
							<div class="woocommerce_order_items_wrapper">
								<table cellpadding="0" cellspacing="0" class="woocommerce_order_items">
									<thead>
										<tr>
											<th class="item sortable" colspan="2" data-sort="string-ins"><?php esc_html_e( 'Item', 'wc-order-view' ); ?></th>
											<?php do_action( 'woocommerce_admin_order_item_headers', $order ); ?>
											<th class="item_cost sortable" data-sort="float"><?php esc_html_e( 'Cost', 'wc-order-view' ); ?></th>
											<th class="quantity sortable" data-sort="int"><?php esc_html_e( 'Qty', 'wc-order-view' ); ?></th>
											<th class="line_cost sortable" data-sort="float"><?php esc_html_e( 'Total', 'wc-order-view' ); ?></th>
											<?php
											if( ! empty( $order_taxes ) ) :
												foreach ( $order_taxes as $tax_id => $tax_item ) :
													$tax_class      = wc_get_tax_class_by_tax_id( $tax_item['rate_id'] );
													$tax_class_name = isset( $classes_options[ $tax_class ] ) ? $classes_options[ $tax_class ] : __( 'Tax', 'wc-order-view' );
													$column_label   = ! empty( $tax_item['label'] ) ? $tax_item['label'] : __( 'Tax', 'wc-order-view' );
													/* translators: %1$s: tax item name %2$s: tax class name  */
													$column_tip = sprintf( esc_html__( '%1$s (%2$s)', 'wc-order-view' ), $tax_item['name'], $tax_class_name );
													?>
													<th class="line_tax tips" data-tip="<?php echo esc_attr( $column_tip ); ?>">
														<?php echo esc_attr( $column_label ); ?>
													</th>
													<?php
												endforeach;
											endif;
											?>
										</tr>
									</thead>
									<tbody id="order_line_items">
										<?php
										foreach ( $line_items as $item_id => $item ) {
											do_action( 'woocommerce_before_order_item_' . $item->get_type() . '_html', $item_id, $item, $order );
											
											$product      = $item->get_product();
											//$product_link = $product ? admin_url( 'post.php?post=' . $item->get_product_id() . '&action=edit' ) : '';
											$thumbnail    = $product ? apply_filters( 'woocommerce_admin_order_item_thumbnail', $product->get_image( 'thumbnail', array( 'title' => '' ), false ), $item_id, $item ) : '';
											$row_class    = apply_filters( 'woocommerce_admin_html_order_item_class', ! empty( $class ) ? $class : '', $item, $order );

											?>
											<tr class="item <?php echo esc_attr( $row_class ); ?>" data-order_item_id="<?php echo esc_attr( $item_id ); ?>">
												<td class="thumb">
													<?php echo '<div class="wc-order-item-thumbnail">' . wp_kses_post( $thumbnail ) . '</div>'; ?>
												</td>
												<td class="name" data-sort-value="<?php echo esc_attr( $item->get_name() ); ?>">
													<?php
													echo '<div class="wc-order-item-name">' . wp_kses_post( $item->get_name() ) . '</div>';
													if ( $product && $product->get_sku() ) {
														echo '<div class="wc-order-item-sku"><strong>' . esc_html__( 'SKU:', 'wc-order-view' ) . '</strong> ' . esc_html( $product->get_sku() ) . '</div>';
													}
													if ( $item->get_variation_id() ) {
														echo '<div class="wc-order-item-variation"><strong>' . esc_html__( 'Variation ID:', 'wc-order-view' ) . '</strong> ';
														if ( 'product_variation' === get_post_type( $item->get_variation_id() ) ) {
															echo esc_html( $item->get_variation_id() );
														} else {
															/* translators: %s: variation id */
															printf( esc_html__( '%s (No longer exists)', 'wc-order-view' ), esc_html( $item->get_variation_id() ) );
														}
														echo '</div>';
													}
													
													do_action( 'woocommerce_before_order_itemmeta', $item_id, $item, $product ); 
													
													$hidden_order_itemmeta = apply_filters(
														'woocommerce_hidden_order_itemmeta', array(
															'_qty',
															'_tax_class',
															'_product_id',
															'_variation_id',
															'_line_subtotal',
															'_line_subtotal_tax',
															'_line_total',
															'_line_tax',
															'method_id',
															'cost',
															'_reduced_stock',
														)
													); 
													?>
													<div class="view">
														<?php if ( $meta_data = $item->get_formatted_meta_data( '' ) ) : ?>
															<table cellspacing="0" class="display_meta">
																<?php
																foreach ( $meta_data as $meta_id => $meta ) :
																	if ( in_array( $meta->key, $hidden_order_itemmeta, true ) ) {
																		continue;
																	}
																	?>
																	<tr>
																		<th><?php echo wp_kses_post( $meta->display_key ); ?>:</th>
																		<td><?php echo wp_kses_post( force_balance_tags( $meta->display_value ) ); ?></td>
																	</tr>
																<?php endforeach; ?>
															</table>
														<?php endif; ?>
													</div>
													<?php do_action( 'woocommerce_after_order_itemmeta', $item_id, $item, $product ); ?>
												</td>

												<?php do_action( 'woocommerce_admin_order_item_values', $product, $item, absint( $item_id ) ); ?>

												<td class="item_cost" width="1%" data-sort-value="<?php echo esc_attr( $order->get_item_subtotal( $item, false, true ) ); ?>">
													<div class="view">
														<?php
														echo wc_price( $order->get_item_total( $item, false, true ), array( 'currency' => $order->get_currency() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
														?>
													</div>
												</td>
												<td class="quantity" width="1%">
													<div class="view">
														<?php
														echo '<small class="times">&times;</small> ' . esc_html( $item->get_quantity() );
														$refunded_qty = $order->get_qty_refunded_for_item( $item_id );
														if ( $refunded_qty ) {
															echo '<small class="refunded">-' . esc_html( $refunded_qty * -1 ) . '</small>';
														}
														?>
													</div>
												</td>
												<td class="line_cost" width="1%" data-sort-value="<?php echo esc_attr( $item->get_total() ); ?>">
													<div class="view">
														<?php
														echo wc_price( $item->get_total(), array( 'currency' => $order->get_currency() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
														if ( $item->get_subtotal() !== $item->get_total() ) {
															/* translators: %s: discount amount */
															echo '<span class="wc-order-item-discount">' . sprintf( esc_html__( '%s discount', 'wc-order-view' ), wc_price( wc_format_decimal( $item->get_subtotal() - $item->get_total(), '' ), array( 'currency' => $order->get_currency() ) ) ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
														}
														$refunded = $order->get_total_refunded_for_item( $item_id );
														if ( $refunded ) {
															echo '<small class="refunded">-' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
														}
														?>
													</div>
												</td>
												<?php
													$tax_data = wc_tax_enabled() ? $item->get_taxes() : false;
													if ( $tax_data ) {
														foreach ( $order_taxes as $tax_item ) {
															$tax_item_id       = $tax_item->get_rate_id();
															$tax_item_total    = isset( $tax_data['total'][ $tax_item_id ] ) ? $tax_data['total'][ $tax_item_id ] : '';
															$tax_item_subtotal = isset( $tax_data['subtotal'][ $tax_item_id ] ) ? $tax_data['subtotal'][ $tax_item_id ] : '';
															?>
															<td class="line_tax" width="1%">
																<div class="view">
																	<?php
																	if ( '' !== $tax_item_total ) {
																		echo wc_price( wc_round_tax_total( $tax_item_total ), array( 'currency' => $order->get_currency() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
																	} else {
																		echo '&ndash;';
																	}
																	$refunded = $order->get_tax_refunded_for_item( $item_id, $tax_item_id );
																	if ( $refunded ) {
																		echo '<small class="refunded">-' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
																	}
																	?>
																</div>
															</td>
															<?php
														}
													}
												?>
											</tr>
											<?php

											do_action( 'woocommerce_order_item_' . $item->get_type() . '_html', $item_id, $item, $order );
										}
										do_action( 'woocommerce_admin_order_items_after_line_items', $order->get_id() );
										?>
									</tbody>
									<tbody id="order_shipping_line_items">
										<?php
										$shipping_methods = WC()->shipping() ? WC()->shipping()->load_shipping_methods() : array();
										foreach ( $line_items_shipping as $item_id => $item ) {
											?>
											<tr class="shipping <?php echo ( ! empty( $class ) ) ? esc_attr( $class ) : ''; ?>" data-order_item_id="<?php echo esc_attr( $item_id ); ?>">
												<td class="thumb"><div></div></td>

												<td class="name">
													<div class="view">
														<?php echo esc_html( $item->get_name() ? $item->get_name() : __( 'Shipping', 'wc-order-view' ) ); ?>
													</div>
													<?php 
													do_action( 'woocommerce_before_order_itemmeta', $item_id, $item, null ); 
													
													$hidden_order_itemmeta = apply_filters(
														'woocommerce_hidden_order_itemmeta', array(
															'_qty',
															'_tax_class',
															'_product_id',
															'_variation_id',
															'_line_subtotal',
															'_line_subtotal_tax',
															'_line_total',
															'_line_tax',
															'method_id',
															'cost',
															'_reduced_stock',
														)
													); 
													?>
													<div class="view">
														<?php if ( $meta_data = $item->get_formatted_meta_data( '' ) ) : ?>
															<table cellspacing="0" class="display_meta">
																<?php
																foreach ( $meta_data as $meta_id => $meta ) :
																	if ( in_array( $meta->key, $hidden_order_itemmeta, true ) ) {
																		continue;
																	}
																	?>
																	<tr>
																		<th><?php echo wp_kses_post( $meta->display_key ); ?>:</th>
																		<td><?php echo wp_kses_post( force_balance_tags( $meta->display_value ) ); ?></td>
																	</tr>
																<?php endforeach; ?>
															</table>
														<?php endif; ?>
													</div>
													<?php do_action( 'woocommerce_after_order_itemmeta', $item_id, $item, null ); ?>
												</td>

												<?php do_action( 'woocommerce_admin_order_item_values', null, $item, absint( $item_id ) ); ?>

												<td class="item_cost" width="1%">&nbsp;</td>
												<td class="quantity" width="1%">&nbsp;</td>

												<td class="line_cost" width="1%">
													<div class="view">
														<?php
														echo wc_price( $item->get_total(), array( 'currency' => $order->get_currency() ) );
														$refunded = $order->get_total_refunded_for_item( $item_id, 'shipping' );
														if ( $refunded ) {
															echo '<small class="refunded">-' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>';
														}
														?>
													</div>
												</td>

												<?php
												if ( ( $tax_data = $item->get_taxes() ) && wc_tax_enabled() ) {
													foreach ( $order_taxes as $tax_item ) {
														$tax_item_id    = $tax_item->get_rate_id();
														$tax_item_total = isset( $tax_data['total'][ $tax_item_id ] ) ? $tax_data['total'][ $tax_item_id ] : '';
														?>
														<td class="line_tax" width="1%">
															<div class="view">
																<?php
																echo ( '' !== $tax_item_total ) ? wc_price( wc_round_tax_total( $tax_item_total ), array( 'currency' => $order->get_currency() ) ) : '&ndash;';
																$refunded = $order->get_tax_refunded_for_item( $item_id, $tax_item_id, 'shipping' );
																if ( $refunded ) {
																	echo '<small class="refunded">-' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>';
																}
																?>
															</div>
														</td>
														<?php
													}
												}
												?>
											</tr>
											<?php
										}
										do_action( 'woocommerce_admin_order_items_after_shipping', $order->get_id() );
										?>
									</tbody>
									<tbody id="order_fee_line_items">
										<?php
										foreach ( $line_items_fee as $item_id => $item ) {
											?>
											<tr class="fee <?php echo ( ! empty( $class ) ) ? esc_attr( $class ) : ''; ?>" data-order_item_id="<?php echo esc_attr( $item_id ); ?>">
												<td class="thumb"><div></div></td>

												<td class="name">
													<div class="view">
														<?php echo esc_html( $item->get_name() ? $item->get_name() : __( 'Fee', 'wc-orde-view' ) ); ?>
													</div>
													<?php do_action( 'woocommerce_after_order_fee_item_name', $item_id, $item, null ); ?>
												</td>

												<?php do_action( 'woocommerce_admin_order_item_values', null, $item, absint( $item_id ) ); ?>

												<td class="item_cost" width="1%">&nbsp;</td>
												<td class="quantity" width="1%">&nbsp;</td>

												<td class="line_cost" width="1%">
													<div class="view">
														<?php
														echo wc_price( $item->get_total(), array( 'currency' => $order->get_currency() ) );
														if ( $refunded = $order->get_total_refunded_for_item( $item_id, 'fee' ) ) {
															echo '<small class="refunded">-' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>';
														}
														?>
													</div>
												</td>

												<?php
												if ( ( $tax_data = $item->get_taxes() ) && wc_tax_enabled() ) {
													foreach ( $order_taxes as $tax_item ) {
														$tax_item_id    = $tax_item->get_rate_id();
														$tax_item_total = isset( $tax_data['total'][ $tax_item_id ] ) ? $tax_data['total'][ $tax_item_id ] : '';
														?>
														<td class="line_tax" width="1%">
															<div class="view">
																<?php
																echo ( '' !== $tax_item_total ) ? wc_price( wc_round_tax_total( $tax_item_total ), array( 'currency' => $order->get_currency() ) ) : '&ndash;';
																if ( $refunded = $order->get_tax_refunded_for_item( $item_id, $tax_item_id, 'fee' ) ) {
																	echo '<small class="refunded">-' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>';
																}
																?>
															</div>
														</td>
														<?php
													}
												}
												?>
											</tr>
											<?php
										}
										do_action( 'woocommerce_admin_order_items_after_fees', $order->get_id() );
										?>
									</tbody>
									<tbody id="order_refunds">
										<?php
										$refunds = $order->get_refunds();
										if ( $refunds ) {
											foreach ( $refunds as $refund ) {

												$who_refunded = new WP_User( $refund->get_refunded_by() );

												?>
												<tr class="refund <?php echo ( ! empty( $class ) ) ? esc_attr( $class ) : ''; ?>" data-order_refund_id="<?php echo esc_attr( $refund->get_id() ); ?>">
													<td class="thumb"><div></div></td>

													<td class="name">
														<?php
														if ( $who_refunded->exists() ) {
															printf(
																/* translators: 1: refund id 2: refund date 3: username */
																esc_html__( 'Refund #%1$s - %2$s by %3$s', 'woocommerce' ),
																esc_html( $refund->get_id() ),
																esc_html( wc_format_datetime( $refund->get_date_created(), get_option( 'date_format' ) . ', ' . get_option( 'time_format' ) ) ),
																sprintf(
																	'<abbr class="refund_by" title="%1$s">%2$s</abbr>',
																	/* translators: 1: ID who refunded */
																	sprintf( esc_attr__( 'ID: %d', 'woocommerce' ), absint( $who_refunded->ID ) ),
																	esc_html( $who_refunded->display_name )
																)
															);
														} else {
															printf(
																/* translators: 1: refund id 2: refund date */
																esc_html__( 'Refund #%1$s - %2$s', 'woocommerce' ),
																esc_html( $refund->get_id() ),
																esc_html( wc_format_datetime( $refund->get_date_created(), get_option( 'date_format' ) . ', ' . get_option( 'time_format' ) ) )
															);
														}
														?>
														<?php if ( $refund->get_reason() ) : ?>
															<p class="description"><?php echo esc_html( $refund->get_reason() ); ?></p>
														<?php endif; ?>
														
														<?php do_action( 'woocommerce_after_order_refund_item_name', $refund ); ?>
													</td>

													<?php do_action( 'woocommerce_admin_order_item_values', null, $refund, $refund->get_id() ); ?>

													<td class="item_cost" width="1%">&nbsp;</td>
													<td class="quantity" width="1%">&nbsp;</td>

													<td class="line_cost" width="1%">
														<div class="view">
															<?php
															echo wp_kses_post(
																wc_price( '-' . $refund->get_amount(), array( 'currency' => $refund->get_currency() ) )
															);
															?>
														</div>
													</td>

													<?php
													if ( wc_tax_enabled() ) :
														$total_taxes = count( $order_taxes );
														?>
														<?php for ( $i = 0;  $i < $total_taxes; $i++ ) : ?>
															<td class="line_tax" width="1%"></td>
														<?php endfor; ?>
													<?php endif; ?>

													<td class="wc-order-edit-line-item">&nbsp;</td>
												</tr>
												<?php

											}
											do_action( 'woocommerce_admin_order_items_after_refunds', $order->get_id() );
										}
										?>
									</tbody>
								</table>
							</div>
							<div class="wc-order-data-row wc-order-totals-items wc-order-items-editable">
								<?php
									$coupons = $order->get_items( 'coupon' );
									if ( $coupons ) :
									?>
									<div class="wc-used-coupons">
										<ul class="wc_coupon_list">
											<li><strong><?php esc_html_e( 'Coupon(s)', 'wc-order-view' ); ?></strong></li>
											<?php
											foreach ( $coupons as $item_id => $item ) :
												$class   = 'code';
												?>
												<li class="<?php echo esc_attr( $class ); ?>">
													<span class="tips" data-tip="<?php echo esc_attr( wc_price( $item->get_discount(), array( 'currency' => $order->get_currency() ) ) ); ?>">
														<span><?php echo esc_html( $item->get_code() ); ?></span>
													</span>
												</li>
											<?php endforeach; ?>
										</ul>
									</div>
								<?php endif; ?>
								<table class="wc-order-totals">
									<?php if ( 0 < $order->get_total_discount() ) : ?>
										<tr>
											<td class="label"><?php esc_html_e( 'Discount:', 'wc-order-view' ); ?></td>
											<td width="1%"></td>
											<td class="total">
												<?php echo wc_price( $order->get_total_discount(), array( 'currency' => $order->get_currency() ) ); // WPCS: XSS ok. ?>
											</td>
										</tr>
									<?php endif; ?>

									<?php do_action( 'woocommerce_admin_order_totals_after_discount', $order->get_id() ); ?>

									<?php if ( $order->get_shipping_methods() ) : ?>
										<tr>
											<td class="label"><?php esc_html_e( 'Shipping:', 'wc-order-view' ); ?></td>
											<td width="1%"></td>
											<td class="total">
												<?php
												$refunded = $order->get_total_shipping_refunded();
												if ( $refunded > 0 ) {
													echo '<del>' . wp_strip_all_tags( wc_price( $order->get_shipping_total(), array( 'currency' => $order->get_currency() ) ) ) . '</del> <ins>' . wc_price( $order->get_shipping_total() - $refunded, array( 'currency' => $order->get_currency() ) ) . '</ins>'; // WPCS: XSS ok.
												} else {
													echo wc_price( $order->get_shipping_total(), array( 'currency' => $order->get_currency() ) ); // WPCS: XSS ok.
												}
												?>
											</td>
										</tr>
									<?php endif; ?>

									<?php do_action( 'woocommerce_admin_order_totals_after_shipping', $order->get_id() ); ?>

									<?php if ( wc_tax_enabled() ) : ?>
										<?php foreach ( $order->get_tax_totals() as $code => $tax_total ) : ?>
											<tr>
												<td class="label"><?php echo esc_html( $tax_total->label ); ?>:</td>
												<td width="1%"></td>
												<td class="total">
													<?php
													$refunded = $order->get_total_tax_refunded_by_rate_id( $tax_total->rate_id );
													if ( $refunded > 0 ) {
														echo '<del>' . wp_strip_all_tags( $tax_total->formatted_amount ) . '</del> <ins>' . wc_price( WC_Tax::round( $tax_total->amount, wc_get_price_decimals() ) - WC_Tax::round( $refunded, wc_get_price_decimals() ), array( 'currency' => $order->get_currency() ) ) . '</ins>'; // WPCS: XSS ok.
													} else {
														echo wp_kses_post( $tax_total->formatted_amount );
													}
													?>
												</td>
											</tr>
										<?php endforeach; ?>
									<?php endif; ?>

									<?php do_action( 'woocommerce_admin_order_totals_after_tax', $order->get_id() ); ?>

									<tr>
										<td class="label"><?php esc_html_e( 'Total', 'wc-order-view' ); ?>:</td>
										<td width="1%"></td>
										<td class="total">
											<?php echo $order->get_formatted_order_total(); // WPCS: XSS ok. ?>
										</td>
									</tr>

									<?php do_action( 'woocommerce_admin_order_totals_after_total', $order->get_id() ); ?>

									<?php if ( $order->get_total_refunded() ) : ?>
										<tr>
											<td class="label refunded-total"><?php esc_html_e( 'Refunded', 'wc-order-view' ); ?>:</td>
											<td width="1%"></td>
											<td class="total refunded-total">-<?php echo wc_price( $order->get_total_refunded(), array( 'currency' => $order->get_currency() ) ); // WPCS: XSS ok. ?></td>
										</tr>
									<?php endif; ?>

									<?php do_action( 'woocommerce_admin_order_totals_after_refunded', $order->get_id() ); ?>
								</table>
								<div class="clear"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>