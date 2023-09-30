<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'cartaway_stg_oc' );

/** Database username */
define( 'DB_USER', 'cartaway_stg_oc' );

/** Database password */
define( 'DB_PASSWORD', 'ExnFO@?9C9}q' );

/** Database hostname */
define( 'DB_HOST', 'localhost:3306' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', 'Z(1FB_o%5-0FYA8Gd~9z/O6Z10*_V9K&K|19X2NtO*IA39wMiWe(570:+cGk*w)c');
define('SECURE_AUTH_KEY', ']gqcyi8iKR*3CiLZ%cYQ6Z6178q&[0!4v5v|n-WS2tSE!5eW8286o4+K]urvR8@9');
define('LOGGED_IN_KEY', '9O9m*Iq99/f@t2Hf|733k40HIpN[gz1t2!TY&+OQ3Rr8:RJ2~Pu;WC55z8~|zl2A');
define('NONCE_KEY', '6f@Rz134u76-JnF3~L!M-XW34E1Th/jsce5nGiw7sb4;!8TZ#@9g75r4WUDc-sdZ');
define('AUTH_SALT', 'b&p8q3V2!_8@w3P508gk9#*is[#f6JEcB3e_0jA)86*W@vD|O80QT3L/%8r*695S');
define('SECURE_AUTH_SALT', 'fK|:Ue4WYCzf/fInvK42Q1ih&ZJaM9)T0]#dYl!~)b:@;!:[2Sd0E(9|(77KrkOY');
define('LOGGED_IN_SALT', '9F/Tj#X@5&1:)382u7z5M7ZJ|m+g-e6_hL-_I*]ABe/yzt4_gO@3@2s-Y~@9iC4K');
define('NONCE_SALT', '*@220aJ2)FxH]349G(lX(7dFs%|_Ao3&Xk;)oG5E7fL7a-7f-E07zLk~-n6XW41s');


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'oc_';


/* Add any custom values between this line and the "stop editing" line. */

define('WP_ALLOW_MULTISITE', true);
/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
