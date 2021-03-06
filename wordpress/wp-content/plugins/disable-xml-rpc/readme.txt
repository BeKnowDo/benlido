=== Plugin Name ===
Contributors: solvethenet, philerb
Donate link: http://www.philerb.com/wp-plugins/appreciation/
Tags: xmlrpc
Requires at least: 3.5
Tested up to: 5.0.2
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin disables XML-RPC API in WordPress 3.5+, which is enabled by default.

== Description ==

Pretty simply, this plugin disables the XML-RPC API on a WordPress site running 3.5 or above.

Beginning in 3.5, XML-RPC is enabled by default. Additionally, the option to disable/enable XML-RPC was removed. For various reasons, site owners may wish to disable this functionality. This plugin provides an easy way to do so.

== Installation ==

1. Upload the disable-xml-rpc directory to the `/wp-content/plugins/` directory in your WordPress installation
1. Activate the plugin through the 'Plugins' menu in WordPress
1. XML-RPC is now disabled!

To re-enable XML-RPC, just deactivate the plugin through the 'Plugins' menu.

== Frequently Asked Questions ==

= Is there an admin interface for this plugin? =

No. This plugin is as simple as XML-RPC is off (plugin activated) or XML-RPC is on (plugin is deactivated).

= How do I know if the plugin is working? =

There are two easy methods for checking if XML-RPC is off:

1. First, try using an XML-RPC client, like the official WordPress mobile apps. The WordPress mobile app should tell you that "XML-RPC services are disabled on this site" if the plugin is activated.
1. Or you can try the XML-RPC Validator, written by Danilo Ercoli of the Automattic Mobile Team - the tool is available at [http://xmlrpc.eritreo.it/](http://xmlrpc.eritreo.it/) with a blog post about it at [http://daniloercoli.com/2012/05/15/wordpress-xml-rpc-endpoint-validator/](http://daniloercoli.com/2012/05/15/wordpress-xml-rpc-endpoint-validator/). Keep in mind that you want the validator to fail and tell you that XML-RPC services are disabled.

= Something doesn't seem to be working correctly =

If the plugin is activated, but XML-RPC appears to still be working ... OR ... the plugin is deactivated, but XML-RPC is not working, then it's possible that another plugin or theme function is affecting the xmlrpc_enabled filter. Additionally, server configurations could be blocking XML-RPC (i.e. blocking access to xmlrpc.php in the .htaccess file).

== Screenshots ==

== Changelog ==

= 1.0.1 =
* Blank lines removed from the plugin file.

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.0.1 =
* Blank lines removed from the plugin file.
