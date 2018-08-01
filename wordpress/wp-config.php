<?php
ini_set('display_errors',0);
if (!isset($_SERVER['HTTP_HOST'])) {
    $SERVER_NAME = php_uname('n');
    if (!empty($SERVER_NAME)) {
      $_SERVER['HTTP_HOST'] = $SERVER_NAME;
    }
}

switch($_SERVER['HTTP_HOST'])
{
    case 'benlido.com':
    case 'www.benlido.com':
    case 'ip-172-31-39-199':
        define('SERVER_ENVIRONMENT','PROD');
        break;
    case 'dev.benlido.com':
    case 'ip-172-31-45-93':
        define('SERVER_ENVIRONMENT','DEV');
        break;
    default:
        define('SERVER_ENVIRONMENT','LOCAL');
        break;
}

switch(SERVER_ENVIRONMENT) {
    case "PROD":
        // NOTE: we are temporarily using a local mysql database to save money until we have e-commerce ready to go
        define('DB_HOST', 'localhost');
        define('DB_NAME', 'ben_lido_live');
        define('DB_USER', 'benlido');
        define('DB_PASSWORD', 'benlido');
        define("WP_CACHE", true);
	    define('WP_CACHE_KEY_SALT', $_SERVER['SERVER_NAME'] );
        break;

    case "DEV":
        define('DB_HOST', 'localhost');
        define('DB_NAME', 'ben_lido_dev');
        define('DB_USER', 'benlido');
        define('DB_PASSWORD', 'benlido');
        break;

    case "LOCAL":
        define('DB_HOST', 'localhost');
        define('DB_NAME', 'ben-lido');
        define('DB_USER', 'root');
        define('DB_PASSWORD', '');
        define( 'WP_DEBUG', false );
        define( 'WP_DEBUG_DISPLAY', false );
        define( 'WP_DEBUG_LOG', true );
        break;
}

// ** MySQL settings ** //

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define('AUTH_KEY',         '&#1`K TAj`Tq1aC85,IJ(` 9X3zqnX~SyoD, y)~8pEI![R8@M`]DRaN7{21JP__');
define('SECURE_AUTH_KEY',  '/6a5t;ELijtU34x30ZV:,l9?AMk:|p6p@+H/5w3~-N3RVU%o*+M-7AXm2Vyc,vtX');
define('LOGGED_IN_KEY',    'I&TUb[Lm*z8?J0&/Kezd(|M&62_kxJe+t-A_%9`|JuetS=$is||NAj1|fA=hJG2P');
define('NONCE_KEY',        '+u==/[QCn])dN$|}~fq.n|.bp9ntQ!h+Y9(X,rA`sZl]=iqTFaI1sVvWtI1a^kh@');
define('AUTH_SALT',        '-vMoBS|Ke5?})Ncj[Udy#zj$Tho4#EZdEdX3v) el@u~KC6,9C+n6KQzjA;J0Ns&');
define('SECURE_AUTH_SALT', 'r:ga,TOMa>Ez.-Y^`1`MY0~<=?ms|0:bUMf,{|)M16eN|sOZ,qG{9m##BafKQaXa');
define('LOGGED_IN_SALT',   'lkbPQVV]iba-|~Wd]u]3P%#a%Jm_k2CpAJMBPz|6@G^YnF:.h`pV+v^GRrmb72`F');
define('NONCE_SALT',       'Uy%.CQ-]|_Am58U#0LVSi1bkU_1>`li~a4V~Ne.]ktng^)##jarh]JDA=xWOjkol');


$table_prefix = 'wp_';





/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
