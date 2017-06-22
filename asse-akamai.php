<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://as-stash.axelspringer.de/projects/WPPL/repos/asse-akamai
 * @since             0.0.2
 * @package           AsseAkamai
 * @author            Sebastian Döll <sebastian.doell@axelspringer.de>
 *
 * @wordpress-plugin
 * Plugin Name:       Asse Akamai
 * Plugin URI:        https://as-stash.axelspringer.de/projects/WPPL/repos/asse-akamai
 * Description:       Akamai for Asse WordPress Plugin.
 * Version:           0.0.1
 * Author:            Axel Springer
 * Author URI:        https://www.axelspringer.de
 * Text Domain:       asse-akamai
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'ASSE_AKAMAI_VERSION' ) ) {
  define( 'ASSE_AKAMAI_VERSION', '0.0.2' );
}

if ( ! defined( 'ASSE_AKAIMAI_MIN_WORDPRESS' ) ) {
  define( 'ASSE_AKAIMAI_MIN_WORDPRESS', '4.7-alpha' );
}

if ( ! defined( 'ASSE_AKAMAI_MIN_PHP' ) ) {
  define( 'ASSE_AKAMAI_MIN_PHP', '5.5' );
}

if ( ! defined( 'ASSE_AKAMAI_PLUGIN_URL' ) ) {
  define( 'ASSE_AKAMAI_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( version_compare( $GLOBALS['wp_version'], ASSE_AKAIMAI_MIN_WORDPRESS, '<' ) ) {
  add_action( 'admin_notices', function () {
    echo  '<div class="notice notice-error">' .
          __( 'Error: "ASSE Akamai" requires a newer version of WordPress to be running.', 'asse-akamai' ) .
          '<br/>' . __( 'Minimal version of WordPress required: ', 'asse-akamai' ) . '<strong>' . ASSE_AKAIMAI_MIN_WORDPRESS . '</strong>' .
		      '<br/>' . __( 'Your WordPress version: ', 'asse-akamai' ) . '<strong>' . $GLOBALS['wp_version'] . '</strong>' .
		      '</div>';
  } );

	return false;
}

if ( version_compare( phpversion(), ASSE_AKAMAI_MIN_PHP, '<' ) ) {
  add_action( 'admin_notices', function () {
    echo  '<div class="notice notice-error">' .
          __( 'Error: "ASSE Akamai" requires a newer version of PHP to be running.', 'asse-akamai' ) .
          '<br/>' . __( 'Minimal version of PHP required: ', 'asse-akamai' ) . '<strong>' . ASSE_AKAMAI_MIN_PHP . '</strong>' .
		      '<br/>' . __( 'Your server\'s PHP version: ', 'asse-akamai' ) . '<strong>' . phpversion() . '</strong>' .
		      '</div>';
  } );

	return false;
}

require_once 'vendor/akamai-open/edgegrid-auth/src/Authentication.php';
require_once 'vendor/akamai-open/edgegrid-auth/src/Authentication/Timestamp.php';
require_once 'vendor/akamai-open/edgegrid-auth/src/Authentication/Nonce.php';
require_once 'vendor/akamai-open/edgegrid-auth/src/Authentication/Exception.php';
require_once 'vendor/akamai-open/edgegrid-auth/src/Authentication/Exception/ConfigException.php';
require_once 'vendor/akamai-open/edgegrid-auth/src/Authentication/Exception/SignerException.php';
require_once 'vendor/akamai-open/edgegrid-auth/src/Authentication/Exception/SignerException/InvalidSignDataException.php';

// includes
require plugin_dir_path( __FILE__ ) . 'includes/class-asse-akamai.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-asse-akamai-settings.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-asse-akamai-settings-section.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-asse-akamai-settings-field.php';

// activate
register_activation_hook( __FILE__, 'AsseAkamai::activate' );

// deactivate
register_deactivation_hook( __FILE__, 'AsseAkamai::deactivate' );

// run
$asse_akamai = new AsseAkamai();
