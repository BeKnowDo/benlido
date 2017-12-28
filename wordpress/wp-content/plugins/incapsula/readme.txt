=== Incapsula ===
Contributors: Incapsula
Tags: Incapsula, CDN, comment, ip, performance, speed, security, SQL Injection, spam, x-forwarded-for
Requires at least: 2.8
Tested up to: 3.6
Stable tag: 1.4

This plugin ensures that your WordPress website runs optimally using Incapsula.
You get the user's real IP address from Incapsula Data Centers.

== Description ==

By using this plugin you will have no change to your originating IPs when using Incapsula. Incapsula acts as a reverse proxy and all incoming connections to your website first pass through one of Incapsula's servers. This plugin will ensure that you continue to see the real originating IP of your website visitors.

The plugin is **highly recommended** for WordPress users using the Incapsula service.

HOW IT WORKS:

* The plugin sets the value of $_SERVER['REMOTE_ADDR'] according to the client IP value reported by the Incapsula proxy.
* The plugin will execute with a high priority, to make sure it runs as early as possible.

ABOUT INCAPSULA:

Incapsula gives any website the security and performance previously only available to the website elite. Through a simple DNS settings change, website traffic is seamlessly routed through Incapsula's global network of high-powered servers. Incoming traffic is intelligently profiled, in real-time, blocking even the latest web threats from sophisticated SQL injection attacks to malicious bots and intruding comment spammers. Meanwhile, outgoing traffic is accelerated and optimized for faster load times, keeping welcome visitors speeding through.

Sign up here for Incapsula: [Incapsula Sign up](http://www.incapsula.com/sign-up/?src=13)

== Installation ==

1. Unzip the file into the Plugins directory
2. Login to wp-admin
3. Go to the "Plugins" menu and activate "Incapsula".

== Changelog ==

= 1.4 =
* Instead of fiddling with the plugins' order on the $active_plugins record as before, the plugin now runs on the "init" hook, with a very high priority (set to -9999999).
 
= 1.3 =
* Ensured support for Wordpress 3.6.
* No major change was applied.
 
= 1.2 =
* First Stable Version