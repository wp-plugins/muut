<?php
/*
 * Plugin Name:   Muut
 * Plugin URI:    https://muut.com/docs/wordpress.html
 * Author URI:    https://muut.com
 * Description:   Realtime forums and commenting for WordPress.
 * Version:       2.0.16
 * Author:        Muut Inc
 * Text Domain:   muut
 * License:       MIT
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) { die; }

require_once( plugin_dir_path( __FILE__ ) . 'class-muut.php' );

/*

// Register hooks
register_activation_hook( __FILE__, array( 'Muut', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Muut', 'deactivate' ) );

*/


Muut::get_instance();