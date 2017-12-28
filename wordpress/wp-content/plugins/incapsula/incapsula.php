<?php
/*
Plugin Name: Incapsula
Plugin URI: http://wordpress.org/extend/plugins/incapsula/
Description: The Incapsula plugin will make sure that any reference to the user IP in your WordPress will use the correct IP address when your site is safeguarded by Incapsula
Version: 1.4
Author: Incapsula
Author URI: http://www.incapsula.com
*/
/**
 * This file changes the value of $_SERVER['REMOTE_ADDR'].
 * The value will be changed whenever the Incap-Client-IP header exists.
 * The $_SERVER['REMOTE_ADDR'] value will remain unchanged if no header was sent or if its value is invalid.
 * This plugin supports WordPress 2.8 and up.
 */

//name of HTTP header with an initial IP
define('HEADER_NAME','HTTP_INCAP_CLIENT_IP');
add_action("init", "incap_set_ip",-9999999);

function incap_set_ip()
{
	try
	{
		//echo('incap header: ['.$_SERVER[HEADER_NAME].']'.'<br />');
		
		//stop process if there is no header
		if (empty($_SERVER[HEADER_NAME])) throw new Exception('No header defined', 1);
		
		//validate header value
		if (function_exists('filter_var'))
		{
			$ip = filter_var($_SERVER[HEADER_NAME], FILTER_VALIDATE_IP);
			if (false === $ip) throw new Exception('The value is not a valid IP address', 2);
		}
		else
		{
			$ip = trim($_SERVER[HEADER_NAME]);
			if (false === preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $ip)) throw new Exception('The value is not a valid IP address', 2);
		}
		//
		//At this point the initial IP value is exist and validated
		//echo('IP: ['.$ip.']'.'<br />');
		$_SERVER['REMOTE_ADDR'] = $ip;
	}
	catch (Exception $e)
	{
		//echo('exeption: ['.$e.']'.'<br />');	
	}
}

?>