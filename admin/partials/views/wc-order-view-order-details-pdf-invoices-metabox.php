<?php

/**
 * Displays the API Manager keys metabox.
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

if( get_option( 'wcov_pdf_invoices' ) == "enabled" ) : ?>
	<div id="woocommerce-invoice_details" class="postbox ">
		<button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Invoice Details</span><span class="toggle-indicator" aria-hidden="true"></span></button>
		<h2 class="hndle ui-sortable-handle"><span>Invoice Details</span></h2>
		<div class="inside">
			<div class="invoice_details_group">
				<ul class="totals">
					<li class="left">
						<label>Invoice Number: </label><?php echo get_post_meta( $order->get_id(), '_invoice_number_display', true ); ?>
					</li>
					<li class="right">
						<label>Invoice Date: </label><?php echo get_post_meta( $order->get_id(), '_invoice_date', true ); ?>
					</li>
					<li class="left">
						<a href="<?php echo admin_url( 'post.php?post=' . $order->get_id() . '&action=edit&pdfid=' . $order->get_id() ) ?>" >Download Invoice</a>
					</li>
				</ul>
			</div>
			<div class="clear"></div>
		</div>
	</div>
<?php endif;