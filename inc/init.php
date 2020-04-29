<?php
/**
 * Veintiv plugin file.
 *
 * @package veintiv
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Include Twenty Twenty specific files.
 */
if ( 'twentytwenty' === get_template() ) {
	require_once VEINTIV_PATH . 'inc/functions.php';
}
