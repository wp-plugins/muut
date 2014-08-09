<?php
/*
Plugin Name: Muut
Plugin URI: http://wordpress.org/plugins/muut/
Description: A complete re-imagining of what online discussions should be. Real&#8209;time forums and commenting for WordPress.
Version: 3.0.2.1
Author: Muut, Inc.
Author URI: http://muut.com
Text Domain: muut
*/

/* Copyright 2014 Muut, Inc. */

// Don't load directly.
if ( !defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Initializes the main plugin class.
 *
 * @return void
 * @author Paul Hughes
 * @since  3.0
 */
function muut_initialize_plugin() {
	if ( file_exists( dirname( __FILE__ ) . '/lib/muut.class.php' ) ) {
		require_once( dirname( __FILE__ ) . '/lib/muut.class.php' );
		if ( class_exists( 'Muut' ) ) {
			muut();
		} else {
			add_action( 'admin_notices', 'muut_show_plugin_load_fail_message' );
		}
	} else {
		add_action( 'admin_notices', 'muut_show_plugin_load_fail_message' );
	}
	$plugin_dir = trailingslashit( basename( dirname( __FILE__ ) ) );
	load_plugin_textdomain( 'muut', false, $plugin_dir . 'lang/');
}

function muut_show_plugin_load_fail_message() {
	if ( current_user_can( 'activate_plugins' ) ) {
		printf( __( '%sMuut%s plugin failed to initialize.%s', 'muut' ), '<div class="error"><p><b>', '</b>', '</p></div>' );
	}
}

function muut_run_activation_functions() {
	add_option( 'muut_plugin_just_activated', time() );
}

// Initialize the plugin.
add_action( 'plugins_loaded', 'muut_initialize_plugin' );

register_activation_hook( __FILE__, 'muut_run_activation_functions' );
