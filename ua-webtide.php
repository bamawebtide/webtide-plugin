<?php

/**
 * Plugin Name:		UA WebTide
 * Plugin URI:		https://webtide.ua.edu
 * Description:		Holds all of the plugin functionality for the WebTide website.
 * Version:         1.0
 * Author:			Rachel Carden
 * Author URI:      https://webtide.ua.edu
 * License:      	GPL-2.0+
 * License URI:    	http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Include some files
foreach( array( 'admin', 'events', 'forms', 'jobs', 'resources', 'seo', 'shortcodes', 'social', 'user', 'widgets', 'wp-plugins-api' ) as $includes_file ) {
	require_once plugin_dir_path( __FILE__ ) . "includes/{$includes_file}.php";
}

//! Hide the admin bar if the user can't access the admin/dashboard
add_action( 'after_setup_theme', 'ua_webtide_remove_admin_bar' );
function ua_webtide_remove_admin_bar() {
   
   if ( ! current_user_can( 'read' ) ) {
	   show_admin_bar( false );
   }

}

//! Hide Query Monitor if no admin bar
add_filter( 'qm/process', function( $process_qm, $is_admin_bar_showing ) {
    return $is_admin_bar_showing;
}, 10, 2 );

//! Add login styles
add_action( 'login_head', 'ua_webtide_add_login_styles' );
function ua_webtide_add_login_styles() {
	
	// Enqueue the login stylesheet
	wp_enqueue_style( 'ua-webtide-login', plugin_dir_url( __FILE__ ) . 'css/login.min.css', array(), NULL );
	
}

//! Filter the login page header URL to link to the site
add_filter( 'login_headerurl', 'ua_webtide_filter_login_headerurl' );
function ua_webtide_filter_login_headerurl( $login_header_url ) {
	
	// Change it to the site's home page
	return get_bloginfo( 'url' );
	
}

// Filter the WPSEO title
add_filter( 'wpseo_title', 'ua_webtide_filter_wpseo_title', 100 );
function ua_webtide_filter_wpseo_title( $title ) {
	
	if ( is_front_page() )
		return get_bloginfo( 'name' );
		
	return $title;
	
}

// Returns true if the current URL matches the link
function is_viewing_ua_webtide_link( $link, $starts_with = false ) {
	
	// Build the current URL
	$current_url = ! ( isset( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] == 'on' ) ? ( 'http://' . $_SERVER[ 'SERVER_NAME' ] ) : ( 'https://' . $_SERVER[ 'SERVER_NAME' ] );

	// Add on the request path
	$current_url .= isset( $_SERVER[ 'REQUEST_URI' ] ) ? $_SERVER[ 'REQUEST_URI' ] : NULL;
	
	// If true, this means to test the start of the URL, is helping for testing parent permalinks
	if ( $starts_with ) {
		
		// Do the URLs match?
		return preg_match( '/^' . preg_replace( '/([^a-z])/i', '\\\\\1', $link ) . '/i', $current_url );
		
	}
	
	// Do the URLs match?
	return preg_match( '/^' . preg_replace( '/([^a-z])/i', '\\\\\1', $link ) . '$/i', $current_url );
	
}

// Runs an "if menu" condition
function is_viewing_ua_webtide_link_menu_condition( $item ) {
	
	return isset( $item->url ) ? is_viewing_ua_webtide_link( $item->url ) : false;

}

// Add conditions to the "If Menu" plugin conditions list
add_filter( 'if_menu_conditions', 'ua_webtide_add_if_menu_conditions', 100 );
function ua_webtide_add_if_menu_conditions( $conditions ) {
	
	// Is user myBama authenticated?
	$conditions[] = array(
		'name'		=>	'User is myBama authenticated',
		'condition'	=>	'is_ua_mybama_cas_authenticated',
		);
		
	// Is user a WebTide member?
	$conditions[] = array(
		'name'		=>	'User is WebTide member',
		'condition'	=>	'is_webtide_member',
		);
	
	// Are we viewing the page?
	$conditions[] = array(
		'name'		=>	'Viewing the menu item',
		'condition'	=>	'is_viewing_ua_webtide_link_menu_condition',
		);

	return $conditions;
	
}