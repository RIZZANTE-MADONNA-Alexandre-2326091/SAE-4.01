<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d’installation. Vous n’avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en « wp-config.php » et remplir les
 * valeurs.
 *
 * Ce fichier contient les réglages de configuration suivants :
 *
 * Réglages MySQL
 * Préfixe de table
 * Clés secrètes
 * Langue utilisée
 * ABSPATH
 *
 * @link https://fr.wordpress.org/support/article/editing-wp-config-php/.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define( 'DB_NAME', 'tvconnecteeamu_bd' );

/** Utilisateur de la base de données MySQL. */
define( 'DB_USER', '279750' );

/** Mot de passe de la base de données MySQL. */
define( 'DB_PASSWORD', 'TvStage2019' );

/** Adresse de l’hébergement MySQL. */
define( 'DB_HOST', 'mysql-tvconnecteeamu.alwaysdata.net' );

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/**
 * Type de collation de la base de données.
 * N’y touchez que si vous savez ce que vous faites.
 */
define( 'DB_COLLATE', '' );

/**#@+
 * Clés uniques d’authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clés secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n’importe quel moment, afin d’invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'MPf)d$hdN} VUpwG))<0ysjo]V;J^+t?M|)n%tXpS*=o^w8rgc4/O;17bD&o/}]n' );
define( 'SECURE_AUTH_KEY',  'M;6)1@}oO:AK3l>xM~r%JI70s#2a0wWL?  L5JHUK1&P:98n&d8pfl]57yKr:[33' );
define( 'LOGGED_IN_KEY',    'i7VX8|p!)YxcHejk&*CJ=oId6,ySbk17k6S?)|Tj8V3,]TMB]5=TSdGfT%j<ZjTt' );
define( 'NONCE_KEY',        'S E8w909iV>7t]Ukd8U0>Y;h^X~oLcPSB#?MsxV8~CGFaMLfj:#n6SQ$>&n?Ry(G' );
define( 'AUTH_SALT',        ';h6nYn#X%Pe!cMqD#Q+Q6~,Fr+Blf2U[u)A@oZ=)v!2POKhS$4sD^] |#9:*b[]b' );
define( 'SECURE_AUTH_SALT', 'Y%K^,*^J3~;UMt`<WRK^K#cEy]DK:>&_tr5;$xxu*xlu16P75Ck%+-AMnD9){8tS' );
define( 'LOGGED_IN_SALT',   'sk9Gv)sZaz:E qEPBc{Y+&*jMP=lyIbkW?|ASxp-Q3~WqB|Gk[0,>ft*(<@JcPs-' );
define( 'NONCE_SALT',       '}3G9[L{]*n9ubsmv2m79[@Jhm>Tce[e[P-4[2&BmjZL8:@eJ#<1<$=d|$5byep,x' );
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N’utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés !
 */
$table_prefix = 'wp_';

/**
 * Pour les développeurs : le mode déboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l’affichage des
 * notifications d’erreurs pendant vos essais.
 * Il est fortement recommandé que les développeurs d’extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 *
 * Pour plus d’information sur les autres constantes qui peuvent être utilisées
 * pour le déboguage, rendez-vous sur le Codex.
 *
 * @link https://fr.wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* C’est tout, ne touchez pas à ce qui suit ! Bonne publication. */

/** Chemin absolu vers le dossier de WordPress. */
if ( ! defined( 'ABSPATH' ) )
  define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once( ABSPATH . 'wp-settings.php' );
