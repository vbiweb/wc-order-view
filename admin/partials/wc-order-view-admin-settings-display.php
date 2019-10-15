<?php

/**
 * Provide a admin area view for the plugin settings
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
<div class="wrap order-view-admin-settings">
	<h2>
		<span class="main_title" tabindex="1">WC Order View - Settings</span>
	</h2>
	<div id="icon-themes" class="icon32"><br></div>
	<h2 class="nav-tab-wrapper">
	<?php
		foreach( $tabs as $tab => $name ) {
		    $class = ( $tab == $current_tab ) ? ' nav-tab-active' : '';
		    echo "<a class='nav-tab$class' href='?page=wc-order-view-settings&tab=$tab'>$name</a>";
		}
	?>
	</h2>

	<?php if ( $current_tab == "general" ) : ?>

		<form method="post" action="options.php">
            <?php
               settings_fields("wc_order_view_general_settings");
 
               do_settings_sections("wc-order-view-settings");
                 
               submit_button();
            ?>
        </form>

	<?php elseif ( $current_tab == "export" ) : ?>

		<h3>Export Settings</h3>

	<?php elseif ( $current_tab == "third-party-plugins" ) : ?>

		<h3>Third Party Plugins Support</h3>

		<?php print_r( get_option( 'active_plugins' ) ); ?>

		<?php print_r( get_plugins() ); ?>

	<?php endif; ?>
</div>
