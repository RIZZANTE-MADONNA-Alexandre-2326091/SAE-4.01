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
define( 'DB_NAME', 'saeecransconnectes_wordpress' );

/** Database username */
define( 'DB_USER', '392322_wordpress' );

/** Database password */
define( 'DB_PASSWORD', 'wordpress_pswd' );

/** Database hostname */
define( 'DB_HOST', 'mysql-saeecransconnectes.alwaysdata.net' );

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
define( 'AUTH_KEY',          '}YpL1f@h2g*29K5r^gVj3h;u^~V=7EUO+K{;Hh(I2M*JMDar&sFhv nWIaY@$#>S' );
define( 'SECURE_AUTH_KEY',   '.THR.`d~!FAYp[:o#OqOBN<_k `.sRdT}<Flz)=1-x`@%vw<,F.;kP=QuQ}Hvb,=' );
define( 'LOGGED_IN_KEY',     'J[fq)X?<!rb?|f)cE>K;`ct8BJjF=uA?Ksu&eQ*wdoHKtGXk}K_rx%SeL3D7ZV%1' );
define( 'NONCE_KEY',         '!P a&%pek{fHp~X0{==)p,/hz/Tv^Od&ZC6Bt$|V-pKe{UQ5o!G0VFGqP(WcaAD=' );
define( 'AUTH_SALT',         '8xG*6B6FcHGUr<I:L3RN{h#1PuigQMSk@(A<,`g,X >:Iem1Z_bU GyG[rfcGA0e' );
define( 'SECURE_AUTH_SALT',  'Q*:I3=[rb|f- #;|8Uw,M$nRpU+P!C8U3#[$fY:]B<*7^6nDMI!_vbWPB^Tm`l<;' );
define( 'LOGGED_IN_SALT',    'K8f^vFM!KCH``VH _ E/DH.u1>b<,eX0ig^y.Vp_sDO@KlHl(lW#K4H7fERb(j{;' );
define( 'NONCE_SALT',        '9&*OT#C[L)op1H[1X5bJ~){oQ;}HuN cL{3LhC2Nmzv^1gF:*9yrfdkzwNO+R;@>' );
define( 'WP_CACHE_KEY_SALT', 'W7X<dRjZ*y~GEQzK%s@,m^8X$pE,LKOxe:N2AWN<iE~t283~4?O_ZH9(lb5!2KGy' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
