=== Plugin Name ===
Contributors: (this should be a list of wordpress.org userid's)
Donate link: https://kgopalkrishna.com
Tags: woocommerce, order, view
Requires at least: 5.0.0
Tested up to: 5.2.3
Stable tag: 1.1.0
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

This plugin gives a strict View-Only access to Woocommerce orders for specified user roles.

== Description ==

This is the long description.  No limit, and you can use Markdown (as well as in the following sections).

For backwards compatibility, if this section is missing, the full length of the short description will be used, and
Markdown parsed.

A few notes about the sections above:

*   "Contributors" is a comma separated list of wp.org/wp-plugins.org usernames
*   "Tags" is a comma separated list of tags that apply to the plugin
*   "Requires at least" is the lowest version that the plugin will work on
*   "Tested up to" is the highest version that you've *successfully used to test the plugin*. Note that it might work on
higher versions... this is just the highest one you've verified.
*   Stable tag should indicate the Subversion "tag" of the latest stable version, or "trunk," if you use `/trunk/` for
stable.

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
* Issue #5 - Fixed cancelled orders not visible
* Feature - Support for 3rd party woocommerce plugins ( PDF Invoices, Woocommerce Subscriptions, Woocommerce API Mananger )
* Dev - New hooks added to the view order details page ( Details will be shared in a separate documentation )

== Features ==

1. Strict view only access to orders.
1. View each orders individually.

== Planned Features ==

* hooks and filters for adding custom content in view order page
* Export orders in CSV format
* Custom order view actions

Here's a link to [WordPress](http://wordpress.org/ "Your favorite software") and one to [Markdown's Syntax Documentation][markdown syntax].
Titles are optional, naturally.

[markdown syntax]: http://daringfireball.net/projects/markdown/syntax
            "Markdown is what the parser uses to process much of the readme file"

Markdown uses email style notation for blockquotes and I've been told:
> Asterisks for *emphasis*. Double it up  for **strong**.

`<?php code(); // goes in backticks ?>`