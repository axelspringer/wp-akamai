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
 * @since             0.0.1
 * @package           AsseAkamai
 * @author            Sebastian Döll <sebastian.doell@axelspringer.de>
 *
 * @wordpress-plugin
 * Plugin Name:       Asse Akamai
 * Plugin URI:        https://as-stash.axelspringer.de/projects/WPPL/repos/asse-akamai
 * Description:       Akamai for Asse WordPress Plugin.
 * Version:           0.2.3
 * Author:            Axel Springer
 * Author URI:        https://www.axelspringer.de
 * Text Domain:       asse-akamai
 */

defined( 'ABSPATH' ) || exit;

// composer
require_once( __DIR__ . '/vendor/autoload.php');

// globals
if ( ! defined( 'ASSE_AKAMAI_VERSION' ) ) {
  define( 'ASSE_AKAMAI_VERSION', '0.2.3' );
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

if ( ! defined( 'ASSE_AKAMAI_PLUGIN_DIR' ) ) {
  define( 'ASSE_AKAMAI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

// timber
$timber               = new \Timber\Timber();
$timber_context       = array();
\Timber::$locations[] = ASSE_AKAMAI_PLUGIN_DIR . 'templates/';

if ( version_compare( $GLOBALS['wp_version'], ASSE_AKAIMAI_MIN_WORDPRESS, '<' ) ) {
  add_action( 'admin_notices', function () {
    $timber_context = array(
      'wp_version'      => $GLOBALS['wp_version'],
      'wp_version_min'  => ASSE_AKAIMAI_MIN_WORDPRESS
    );
    Timber::render( 'notice-wp-version.twig', $timber_context );
  } );

	return false;
}

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
