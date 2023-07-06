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
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'awamp' );
/** MySQL database username */
define( 'DB_USER', 'root' );
/** MySQL database password */
define( 'DB_PASSWORD', '' );
/** MySQL hostname */
define( 'DB_HOST', 'localhost' );
/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );
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
define( 'AUTH_KEY',         'pU8s^Fo/ VfLX*K8,*]@ymYd<pYvexBzw5BB!/}9doKjf]E2+B>/@IMPf8+]7 mk' );
define( 'SECURE_AUTH_KEY',  'B$iOa6$`~pK_HYRoP#Q^kePhmtE|e X]%gZ~>ngY`|U{)YE2].lo}z)co 4>8x(#' );
define( 'LOGGED_IN_KEY',    'X0KR&i8gD_4|Hpwg4!WppEL+1cmd-ezJdJAyh93!Q{LV;;[}-,f$9)JI)Wj>Mny ' );
define( 'NONCE_KEY',        'vBWu$;}NsG8H:5>|2ov~hEtOlh-!,(>._$IRk]wJU[}p93KphCQT;U|dXuX+zr^u' );
define( 'AUTH_SALT',        'Eu#~;lXxq%x93}QOZ/z[^4-A[?O~%Rr;K(Q,(k5[q3A$h#gf;(}X[-cdLR*dXa/Y' );
define( 'SECURE_AUTH_SALT', '{]@:nMNw8/E!GM3nA)r70zwNTL+O<,EA2r6L#p9=_.[X/$)d|kj5#2SgO2:GFiV^' );
define( 'LOGGED_IN_SALT',   'xgl4|0>H=~]9#ZcPqsERqT=[]W>`/xrixdyE/K[_ccFPsw&;-HKGn^ub?=uaN.P,' );
define( 'NONCE_SALT',       '$D&3%{w`jiFK.VMl5t^UNdBb 1qM_1c${(@A5SguFIj/arMXGIq.S=S.7Qo{ITcJ' );
/**#@-*/
/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';
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
define( 'WP_DEBUG', false );
/* Add any custom values between this line and the "stop editing" line. */
/* That's all, stop editing! Happy publishing. */
/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}
/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';