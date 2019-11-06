<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://kgopalkrishna.com
 * @since             1.0.0
 * @package           Wc_Order_View
 *
 * @wordpress-plugin
 * Plugin Name:       Woocommerce Order View
 * Plugin URI:        https://kgopalkrishna.com/wc-order-view/
 * Description:       This plugin gives a strict View-Only access to Woocommerce orders for specified user roles.
 * Version:           1.2.0
 * Author:            K Gopal Krishna
 * Author URI:        https://kgopalkrishna.com
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       wc-order-view
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WC_ORDER_VIEW_VERSION', '1.2.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wc-order-view-activator.php
 */
function activate_wc_order_view() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-order-view-activator.php';
	Wc_Order_View_Activator::activate();
}

/**
 * The code that runs during plugin update.
 * This action is documented in includes/class-wc-order-view-updater.php
 */
function update_wc_order_view() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-order-view-updater.php';
	$updater = new Wc_Order_View_Updater( __FILE__ );
	$updater->set_username( 'vbiweb' );
	$updater->set_repository( 'wc-order-view' );
	$updater->initialize();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wc-order-view-deactivator.php
 */
function deactivate_wc_order_view() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-order-view-deactivator.php';
	Wc_Order_View_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wc_order_view' );
register_deactivation_hook( __FILE__, 'deactivate_wc_order_view' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wc-order-view.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wc_order_view() {

	$plugin = new Wc_Order_View();
	$plugin->run();

}
run_wc_order_view();
