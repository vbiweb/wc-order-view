=== WC Order View ===

Contributors: kggopal12
Donate link: https://kgopalkrishna.com
Tags: woocommerce, order, view
Requires at least: 5.0.0
Tested up to: 5.2.3
Stable tag: 1.2.0
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

This plugin gives a strict View-Only access to Woocommerce orders for specified user roles.

== Description ==

This plugin gives a strict view only access to woocommerce orders. All you need to do is create a new user role 'products_admin' using any role editor plugin available. All users with this role will be able to view orders, the items in the order, total amount paid including taxes and shipping, and the order notes all in read-only mode. This plugin also provides support for select 3rd party Woocommerce premium plugins like Woocommerce Subscriptions, PDF Invoices and Woocommerce API Manager. Planned features include CSV Exporting options and Custom Order actions available only for 'products_admin'

== Installation ==

1. Upload `wc-order-view` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==

= 1.0.0 =
* Initial release.

= 1.0.1 =
* Issue #2 - Fixed Date created value displayed for each order
* Issue #3 - Fixed the total orders count shown in all orders page

= 1.1.0 =
* Fix - Fixed cancelled orders not visible #5
* Feature - Support for 3rd party woocommerce plugins ( PDF Invoices, Woocommerce Subscriptions, Woocommerce API Mananger )
* Dev - New hooks added to the view order details page ( Details will be shared in a separate documentation )

= 1.2.0 =
* Feature - One click update option added in the plugins page
* Fix - Cancelled order status color doesn't match WooCommerce #8
* Fix - Invoice date for all orders showing same date in All orders list #9

== Features ==

1. Strict view only access to orders.
1. View each orders individually.
1. Support for 3rd party woocommerce plugins ( PDF Invoices, Woocommerce Subscriptions, Woocommerce API Mananger )
1. Automatic update notifications

== Planned Features ==

* Export orders in CSV format
* Custom order view actions

Here's a link to [WordPress](http://wordpress.org/ "Your favorite software") and one to [Markdown's Syntax Documentation][markdown syntax].
Titles are optional, naturally.

[markdown syntax]: http://daringfireball.net/projects/markdown/syntax
            "Markdown is what the parser uses to process much of the readme file"

Markdown uses email style notation for blockquotes and I've been told:
> Asterisks for *emphasis*. Double it up  for **strong**.

`<?php code(); // goes in backticks ?>`