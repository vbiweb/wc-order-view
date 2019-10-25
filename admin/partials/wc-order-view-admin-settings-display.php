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
	<form method="post" action="options.php">
        <?php
           //settings_fields("wc_order_view_general_settings");

           settings_fields("wc_order_view_settings");

           do_settings_sections("wc-order-view-settings");
             
           submit_button();
        ?>
    </form>
</div>
