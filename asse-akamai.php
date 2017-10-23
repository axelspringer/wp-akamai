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
 * Version:           0.4.0
 * Author:            Axel Springer
 * Author URI:        https://www.axelspringer.de
 * Text Domain:       asse-akamai
 */

defined( 'ABSPATH' ) || exit;

use \Asse\Plugin\Akamai;

// composer
require_once( __DIR__ . '/vendor/autoload.php');

// activate
register_activation_hook( __FILE__, '\Asse\Plugin\Akamai::activate' );

// deactivate
register_deactivation_hook( __FILE__, '\Asse\Plugin\Akamai::deactivate' );

// run
$asse_akamai = new Akamai( 'asse_akmai', '0.4.0', __FILE__ );
