<?php

// Add query vars
add_filter( 'query_vars', 'ua_webtide_wp_plugins_add_query_vars' );
function ua_webtide_wp_plugins_add_query_vars( $vars ) {

	// Add our variable for detecting to get the WordPress update response
	$vars[] = 'get_wp_plugin_update_response';

	// Lets us know what our current version is for checking for updates
	$vars[] = 'current_version';

	return $vars;

}

// Get the plugin response when trying to view a plugin's response
add_action( 'wp', 'ua_webtide_wp_plugins_show_response_info' );
function ua_webtide_wp_plugins_show_response_info( $query ) {
	global $wp_query, $wpdb;

	// Not for the admin or login page
	if ( is_admin() || ( 'wp-login.php' == $GLOBALS[ 'pagenow' ] ) ) {
		return;
	}

	// Only if we want the plugin update response - holds the plugin ID
	if ( ! ( $wordpress_plugin_slug = $wp_query->get( 'get_wp_plugin_update_response' ) ) ) {
		return;
	}

	// Make sure we have a current version
	if ( ! ( $current_version = $wp_query->get( 'current_version' ) ) ) {
		return;
	}

	// Get the WordPress plugin id
	if ( ! ( $wordpress_plugin_id = $wpdb->get_var( "SELECT ID FROM {$wpdb->posts} posts INNER JOIN {$wpdb->postmeta} meta ON meta.post_id = posts.ID AND meta.meta_key = 'wordpress_plugin_slug' AND meta.meta_value = '{$wordpress_plugin_slug}' WHERE posts.post_type = 'repo-items' AND posts.post_status = 'publish'" ) ) ) {
		return;
	}

	// Make sure it's not hosted in the WordPress repo
	if ( strcasecmp( get_post_meta( $wordpress_plugin_id, 'is_hosted_in_wordpress_repo', true ), 'yes' ) == 0 ) {
		return;
	}

	// Make sure we have repo data
	if ( ! ( $rkv_repo_data = get_post_meta( $wordpress_plugin_id, '_rkv_repo_data', true ) ) ) {
		return;
	}

	// Will hold updated version and package
	$valid_plugin_update = array();

	// Will hold the repo URL - use permalink as default
	$wordpress_plugin_repo_url = get_permalink( $wordpress_plugin_id );

	// Set the new version
	$valid_plugin_update[ 'new_version' ] = isset( $rkv_repo_data[ 'version' ] ) ? $rkv_repo_data[ 'version' ] : false;

	// Set the package
	$valid_plugin_update[ 'package' ] = isset( $rkv_repo_data[ 'package' ] ) ? $rkv_repo_data[ 'package' ] : false;

	// Set the changelog, if we have one
	$valid_plugin_update[ 'changelog' ] = isset( $rkv_repo_data[ 'changelog' ] ) ? $rkv_repo_data[ 'changelog' ] : false;

	// Is this plugin hosted on GitHub?
	if ( strcasecmp( get_post_meta( $wordpress_plugin_id, 'is_hosted_on_github', true ), 'yes' ) == 0 ) {

		// Make sure we have a repo and an owner
		if ( ( $github_repo = get_post_meta( $wordpress_plugin_id, 'github_repo', true ) )
			&& ( $github_repo_owner = get_post_meta( $wordpress_plugin_id, 'github_repo_owner', true ) ) ) {

			// Define the repo URL
			$wordpress_plugin_repo_url = "https://github.com/{$github_repo_owner}/{$github_repo}/";

		}

	// See if we have any updates
	} else if ( $wordpress_plugin_updates = get_field( 'wordpress_plugin_updates', $wordpress_plugin_id ) ) {

		// Define the repo URL
		$wordpress_plugin_repo_url = get_post_meta( $wordpress_plugin_id, 'wordpress_plugin_repo_url', true );

		// Loop through and find a valid update
		foreach ( $wordpress_plugin_updates as $update_info ) {

			// Check the version no
			if ( isset( $update_info[ 'wordpress_plugin_update_version_no' ] )
			     && floatval( $update_info[ 'wordpress_plugin_update_version_no' ] ) > floatval( $current_version )
			     && isset( $update_info[ 'wordpress_plugin_update_package' ] ) ) {

				// If we already have a valid plugin update, make sure this one is more recent
				if ( isset( $valid_plugin_update ) && isset( $valid_plugin_update[ 'new_version' ] )
				     && floatval( $valid_plugin_update[ 'new_version' ] ) > floatval( $update_info[ 'wordpress_plugin_update_version_no' ] ) ) {
					continue;
				}

				// Set the new version
				$valid_plugin_update[ 'new_version' ] = $update_info[ 'wordpress_plugin_update_version_no' ];

				// Set the package
				$valid_plugin_update[ 'package' ] = $update_info[ 'wordpress_plugin_update_package' ];

				// Set the changelog
				$valid_plugin_update[ 'changelog' ] = $update_info[ 'wordpress_plugin_update_changelog' ];

			}

		}

	}

	// If no valid updates at this point, redirect to resources section
	if ( ! $valid_plugin_update || empty( $valid_plugin_update ) ) {

		// Redirect to Resources section
		wp_redirect( 'https://webtide.ua.edu/resources/' );
		exit;

	} else {

		// Create the response
		$response = array(
			'slug'      => $wordpress_plugin_slug,
			'plugin'    => "{$wordpress_plugin_slug}/{$wordpress_plugin_slug}.php",
		);

		// Add the URL
		if ( $wordpress_plugin_repo_url ) {
			$response[ 'url' ] = $wordpress_plugin_repo_url;
		}

		// Add the update info
		$response = array_merge( $response, $valid_plugin_update );

		// Send the response
		wp_send_json( $response );

	}

}