<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://kgopalkrishna.com
 * @since      1.0.0
 *
 * @package    Wc_Order_View
 * @subpackage Wc_Order_View/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap order-view-admin">
	<h2>
		<span class="main_title" tabindex="1">Orders</span>
	</h2>
	<form method="get">
		<input type="hidden" name="page" value="<?php echo $_REQUEST[ 'page' ]; ?>" />
		<?php
			
			if ( ! empty( $_REQUEST['_wp_http_referer'] ) ) {
				wp_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
				exit;
			}

			$count_orders = ( array ) wp_count_posts( 'shop_order' );
			$order_statuses = wc_get_order_statuses();
			
			$total_count = 0; 

			foreach ( $count_orders as $order_status => $count_order ) {
				if ( $count_order <= 0 ) {
					unset( $count_orders[ $order_status ] );
				}

				$total_count += $count_order;
			}

			end($count_orders);
			$last_status = key($count_orders);

			$current_user = wp_get_current_user();

			$args = array (
				'post_type'    => 'shop_order',
				'author' 	   => $current_user->ID,
				'post_status'  => array( 'wc-completed', 'wc-on-hold' )
			);

			$user_posts = get_posts( $args );

			$user_posts_count = count( $user_posts );

		?>
		<?php if ( $total_count > 0 ) : ?>
			<ul class="subsubsub">
				<li class="all">
					<a <?php echo ( isset( $_REQUEST[ 'all_posts' ] ) && $_REQUEST[ 'all_posts' ] == "1" ) ? 'class="current"' : ''; ?> <?php echo ( ( !isset( $_REQUEST[ 'post_status' ] ) || $_REQUEST[ 'post_status' ] == "" ) && ( !isset( $_REQUEST[ 'author' ] ) || $_REQUEST[ 'author' ] == "" ) ) ? 'class="current"' : ''; ?> href="admin.php?page=wc-order-view&all_posts=1">All </a> 
					(<?php echo $total_count; ?>)
					<?php echo ( !empty( $count_orders ) ) ? "|" : ""; ?>
				</li>
				<li class="mine">
					<a <?php echo ( isset( $_REQUEST[ 'author' ] ) && $_REQUEST[ 'author' ] == $current_user->ID ) ? 'class="current"' : ''; ?> href="admin.php?page=wc-order-view&author=<?php echo $current_user->ID; ?>">Mine </a> 
					(<?php echo $user_posts_count; ?>)
					<?php echo ( !empty( $count_orders ) ) ? "|" : ""; ?>
				</li>
				<?php foreach ( $count_orders as $order_status => $count_order ) : ?> 
					<li class="<?php echo $order_status; ?>">
						<a <?php echo ( $_REQUEST[ 'post_status' ] == $order_status ) ? 'class="current"' : ''; ?> href="admin.php?page=wc-order-view&post_status=<?php echo $order_status; ?>">
							<?php echo $order_statuses[ $order_status ]; ?>
						</a> 
						(<?php echo $count_order; ?>) <?php echo ( $order_status !== $last_status ) ? "|" : ""; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<?php

			$this->wc_order_view_list_table->prepare_items();
			$this->wc_order_view_list_table->search_box( 'Search Orders', 'order_search_top' );
			$this->wc_order_view_list_table->display();

		?>
	</form>
	</div>
</div>
