<?php
/**
 * Plugin Name: Customizer for Twenty Twenty
 * Plugin URI: https://github.com/matiasmann/plus-twenty-twenty
 * Description: The Twenty Twenty theme is awesome. What if we make it awesomer? Twenty Twenty Plus provides lots of options to customize the theme use the WordPress Customizer and built in functions. 
 * Author: Matias Mann
 * Author URI: https://github.com/matiasmann
 * Version: 1.0.1
 * Text Domain: veintiv
 * License: GPLv2 or later
 *
 * @package veintiv
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Setup plugin constants.
 */
define( 'VEINTIV_VERSION', '1.0.1' );
define( 'VEINTIV_PATH', trailingslashit( plugin_dir_path(__FILE__) ) );
define( 'VEINTIV_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'VEINTIV_ASSETS_URI', VEINTIV_URI . 'dist' );
define( 'VEINTIV_PLUGIN_BASE', plugin_basename( __FILE__ ) );

/**
 * Load the Veintiv plugin.
 */
require_once VEINTIV_PATH . 'inc/init.php';
