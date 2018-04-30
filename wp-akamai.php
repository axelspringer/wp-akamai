<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/axelspringer/wp-akamai
 * @since             0.0.1
 * @package           AxelSpringer\WP\Akamai
 * @author            Sebastian Döll <sebastian.doell@axelspringer.de>
 *
 * @wordpress-plugin
 * Plugin Name:       WordPress Akamai
 * Plugin URI:        https://github.com/axelspringer/wp-akamai
 * Description:       A companion plugin for Akamai + WordPress.
 * Version:           0.3.11
 * Author:            Axel Springer
 * Author URI:        https://www.axelspringer.de
 * Text Domain:       wp-akamai
 */

defined( 'ABSPATH' ) || exit;

// make sure we don't expose any info if called directly
if ( ! function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

// respect composer autoload
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	$loader = require_once __DIR__ . '/vendor/autoload.php';
	$loader->addPsr4( 'AxelSpringer\\WP\\Akamai\\', __DIR__ . '/src' );
}

use \AxelSpringer\WP\Akamai\__WP__ as WP;
use \AxelSpringer\WP\Akamai\__PLUGIN__ as Plugin;
use \AxelSpringer\WP\Akamai\Plugin as Akamai;

// bootstrap
if ( ! defined( WP::VERSION ) )
	define( WP::VERSION, Plugin::VERSION );

if ( ! defined( WP::URL ) )
	define( WP::URL, plugin_dir_url( __FILE__ ) );

if ( ! defined( WP::SLUG ) )
    define( WP::SLUG, Plugin::SLUG );

// activate
register_activation_hook( __FILE__, '\AxelSpringer\WP\Akamai\Plugin::activation' );

// deactivate
register_deactivation_hook( __FILE__, '\AxelSpringer\WP\Akamai\Plugin::activation' );

// run
global $wp_akamai;
$wp_akamai = new Akamai( WP_AKAMAI_SLUG, WP_AKAMAI_VERSION, __FILE__ );
