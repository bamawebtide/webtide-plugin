<?php
	
// Will check for pages that are for members only
add_action( 'wp', 'ua_webtide_check_for_members_only', 0 );
function ua_webtide_check_for_members_only( $wp ) {
	global $post;
	
	// Only do for front end
	if ( is_admin() ) {
		return;
	}
	
	// Is this page for members only?
	// Make sure we're viewing a single post and have a post ID
	// Can't return early because we need to set the constant
	$is_members_only_page = is_singular() && isset( $post ) && isset( $post->ID ) && $post->ID > 0 && strcasecmp( 'yes', get_post_meta( $post->ID, 'is_members_only_page', true ) ) == 0 ? true : false;
		
	// Is the user a WebTide member?
	define( 'IS_WEBTIDE_MEMBERS_ONLY_PAGE', $is_members_only_page );
	
	// Is this page isn't for members only, get out of here
	if ( ! $is_members_only_page )
		return;
	
	// If user isn't logged in, so force authentication
	if ( ! current_user_can( 'is_webtide_member' ) )
		auth_redirect();
	
}

// Block search results of members only pages for non-members
add_filter( 'posts_clauses', 'ua_webtude_user_filter_search_results', 1000, 2 );
function ua_webtude_user_filter_search_results( $clauses, $query ) {
	global $wpdb;
	
	// Not in the admin
	if ( is_admin() ) {
		return $clauses;
	}
	
	// If we're running a search and the user isnt a member...
	if ( $query->is_search() && ! current_user_can( 'is_webtide_member' ) ) {
		
		// GROUP BY post ID to clear up duplicates
		$clauses[ 'groupby' ] = "{$wpdb->posts}.ID";
		
		// LEFT JOIN to get post meta
		$clauses[ 'join' ] .= " LEFT JOIN {$wpdb->postmeta} is_members_only_page ON is_members_only_page.post_id = {$wpdb->posts}.ID AND is_members_only_page.meta_key = 'is_members_only_page'";
		
		// Set up the WHERE - sets false for members only pages
		$clauses[ 'where' ] .= " AND IF ( is_members_only_page.meta_value IS NOT NULL AND is_members_only_page.meta_value LIKE 'yes', false, true )";
		
	}
	
	return $clauses;
	
}
	
//! Get list of all WebTide members
function get_webtide_members() {
	
	// Will hold WebTide members
	$webtide_members = array();
	
	// Get all users
	if ( $all_users = get_users( array(
		'orderby'	=> 'meta_value',
		'order'		=> 'ASC',
		'meta_key'	=> 'last_name',
		) ) ) {
			
		foreach( $all_users as $this_user ) {
			
			// This means they're a WebTide member
			if ( $this_user->has_cap( 'is_webtide_member' ) ) {
				
				// Add to list of WebTide members
				$webtide_members[] = $this_user;
				
			}
			
		}
		
	}
	
	return ! empty( $webtide_members ) ? $webtide_members : false;
	
}

// Get member notification
function get_ua_webtide_member_notification() {
	
	// Will hold notification string
	$member_notification_str = NULL;
	
	// Are there any overriding notification? Get the most recent
	if ( ( $member_notification = get_posts( array(
		'post_type' 		=> 'member_notifications',
		'posts_per_page' 	=> 1,
		'meta_key'			=> 'override_other_notifications',
		'meta_value'		=> 'yes',
		'orderby'          	=> 'post_date',
		'order'            	=> 'DESC',
		'suppress_filters' 	=> false ) ) )
		&& is_array( $member_notification )
		&& $member_notification = reset( $member_notification ) ) {
		
		// Set content
		$member_notification_str = isset( $member_notification->post_content ) ? $member_notification->post_content : NULL;
		
	}
	
	// If there's a webtide meeting today AND non-meeting event(s)
								
	// else if there's non-meeting event(s) today
	
	// Do we have a meeting today? That takes first priority
	if ( ! $member_notification_str && ( $next_monthly_meeting = ua_webtide_get_next_monthly_meeting() )
		&& ( $start_date_str = isset( $next_monthly_meeting->EventStartDate ) ? $next_monthly_meeting->EventStartDate : NULL )
		&& strtotime( $start_date_str ) !== false ) {
			
		// Set the start date
		$start_date = new DateTime( $start_date_str, new DateTimeZone( 'America/Chicago' ) );
			
		// What's the current date in our timezone?
		$current_date = new DateTime( NULL, new DateTimeZone( 'America/Chicago' ) );
		
		// What's the difference?
		if ( $time_until_meeting = $current_date->diff( $start_date ) ) {
		
			// Will lunch be served?
			$will_lunch_be_served = strcasecmp( 'yes', get_post_meta( $next_monthly_meeting->ID, 'will_lunch_be_served', true ) ) == 0;
					
			// Build time string
			$meeting_start_time_str = $start_date->format( 'g' );
				
			// What are the minutes?
			$meeting_start_minute = $start_date->format( 'i' );
						
			// Don't include the minutes on the hour
			if ( '00' != $meeting_start_minute )
				$meeting_start_time_str .= ":{$meeting_start_minute}";
						
			// Add meridian
			$meeting_start_time_str .= $start_date->format( ' a' );
			
			// Get the meeting title - limit words and make sure it doesn't have 4 ellipses
			$meeting_title = preg_replace( '/[\.]{4}$/i', '...', wp_trim_words( get_the_title( $next_monthly_meeting->ID ), 11, '...' ) );
					
			// If it's this week...
			if ( ! $time_until_meeting->invert && $time_until_meeting->d < 4 ) {
				
				// Start with the closest notification first - its today!
				if ( $time_until_meeting->d == 0 && $time_until_meeting->h < 12 ) {
					
					// Build the notification
					$member_notification_str = '<strong>IT\'S WEBTIDE DAY!</strong> We\'re looking forward to seeing everyone at ' . $meeting_start_time_str . ' for "<a href="' . get_permalink( $next_monthly_meeting->ID ) . '">' . $meeting_title . '</a>".';
				
				// It's tomorrow...
				} else if ( $time_until_meeting->d == 0 || ( $time_until_meeting->d == 1 && $time_until_meeting->h < 12 ) ) {
					
					// Build the notification
					$member_notification_str = '<strong>IT\'S WEBTIDE WEEK!</strong> We\'re looking forward to seeing everyone tomorrow at ' . $meeting_start_time_str . ' for "<a href="' . get_permalink( $next_monthly_meeting->ID ) . '">' . $meeting_title . '</a>".';
					
				// If it's this week...
				} else {
					
					// Build the notification
					$member_notification_str = '<strong>IT\'S WEBTIDE WEEK!</strong> We\'re looking forward to seeing everyone on ' . $start_date->format( 'l' ) . ' at ' . $meeting_start_time_str . ' for "<a href="' . get_permalink( $next_monthly_meeting->ID ) . '">' . $meeting_title . '</a>".';
					
				}
				
				// Add whether lunch will be served
				$member_notification_str .= ' <em><strong>Lunch will ' . ( ! $will_lunch_be_served ? ' not ' : NULL ) . 'be served.</strong></em>';
			}
			
		}
		
	} 
	
	// If still no notification, get the most recent member notification
	if ( ! $member_notification_str
		&& ( $member_notification = get_posts( array(
		'post_type' 		=> 'member_notifications',
		'posts_per_page' 	=> 1,
		'orderby'          	=> 'post_date',
		'order'            	=> 'DESC',
		'suppress_filters' 	=> false ) ) )
		&& is_array( $member_notification )
		&& $member_notification = reset( $member_notification ) ) {
		
		// Set content
		$member_notification_str = isset( $member_notification->post_content ) ? $member_notification->post_content : NULL;
		
	}
	
	// If still no notification, and the user is myBama authenticated, show message about members
	if ( ! $member_notification_str && ! IS_WEBTIDE_MEMBER && IS_MYBAMA_AUTHENTICATED ) {
		
		$member_notification_str = '<strong>If you\'re a WebTide member</strong>, and seeing this message, it means you\'re not registered in our system. Please <a href="https://webtide.ua.edu/contact/">contact us</a> and let us know.';
		
	}
	
	return apply_filters( 'ua_webtide_member_notification', $member_notification_str );
	
}

// Get member notification status
function get_ua_webtide_member_notification_status( $post_id ) {
	
	// Get the stored status
	$status = strtolower( get_post_meta( $post_id, 'status', true ) );
	
	// There are only 2 options: active and inactive
	if ( ! in_array( $status, array( 'active', 'inactive' ) ) ) {
		$status = 'inactive';
	}
		
	return $status;
	
}

//! Filter the member notifications permalink
add_filter( 'post_type_link', 'ua_webtide_filter_member_notifications_permalink', 10, 4 );
function ua_webtide_filter_member_notifications_permalink( $post_link, $post, $leavename, $sample ) {
	
	// Make sure its a member notifications post type
	if ( ! ( 'member_notifications' == $post->post_type ) ) {
		return $post_link;
	}
		
	// Link to the members page
	return get_permalink( get_page_by_path( 'members' ) );
	
}

//! Filter the queries to block inactive and out of date notifications
add_filter( 'posts_clauses', 'ua_webtide_filter_member_notifications_clauses', 100, 2 );
function ua_webtide_filter_member_notifications_clauses( $pieces, $query ) {
	global $wpdb;
	
	// Don't run in the admin
	if ( is_admin() ) {
		return $pieces;
	}
		
	// Only doing for notifications
	if ( ! ( ( $post_type = $query->get( 'post_type' ) ) && 'member_notifications' == $post_type ) ) {
		return $pieces;
	}
		
	// Add meta queries for status, start date and end date
	
	// What's the current date in our timezone?
	$current_date = new DateTime( NULL, new DateTimeZone( 'America/Chicago' ) );
	$current_date_str = $current_date->format( 'Ymd' );
	
	// Add INNER JOIN to get active status
	$pieces[ 'join' ] .= " INNER JOIN {$wpdb->postmeta} statusmeta ON ( wp_posts.ID = statusmeta.post_id ) AND statusmeta.meta_key = 'status' AND CAST( statusmeta.meta_value AS CHAR ) LIKE 'active'";
	
	// Add LEFT JOIN to get start date
	$pieces[ 'join' ] .= " LEFT JOIN {$wpdb->postmeta} sdmeta ON ( wp_posts.ID = sdmeta.post_id ) AND sdmeta.meta_key = 'notification_start_date'";
	
	// Add WHERE to test start date
	$pieces[ 'where' ] .= " AND IF ( sdmeta.meta_value != '', CAST( sdmeta.meta_value AS UNSIGNED ) <= {$current_date_str}, TRUE )";
	
	// Add LEFT JOIN to get end date
	$pieces[ 'join' ] .= " LEFT JOIN {$wpdb->postmeta} edmeta ON ( wp_posts.ID = edmeta.post_id ) AND edmeta.meta_key = 'notification_end_date'";
	
	// Add WHERE to test end date
	$pieces[ 'where' ] .= " AND IF ( edmeta.meta_value != '', CAST( edmeta.meta_value AS UNSIGNED ) >= {$current_date_str}, TRUE )";
	
	return $pieces;
	
}

//! Lets us know if the user is a WebTide member
//  Is not always the same as being myBama authenticated
function is_webtide_member() {
	
	// Their user role must have this capability
	return current_user_can( 'is_webtide_member' );
	
}

//! Checks to see if someone is logged in/authenticated and gets their data
add_action( 'plugins_loaded', 'ua_webtide_get_user_data', 100 );
function ua_webtide_get_user_data() {
	global $ua_mybama_cas_auth, $current_user,
		$username, $user_first_name, $user_last_name,
		$user_email, $user_department, $user_job_title;
	
	// Only do for front end
	if ( is_admin() ) {
		return;
	}
		
	// Is the user a WebTide member?
	define( 'IS_WEBTIDE_MEMBER', is_webtide_member() );
	
	// Get current user info
	get_currentuserinfo();
	
	// If they're a WebTide member, get info from WP
	// Otherwise, if myBama authenticated, get from myBama
	
	// Get user name
	$username = IS_WEBTIDE_MEMBER && isset( $current_user->user_login ) ? $current_user->user_login : ( IS_MYBAMA_AUTHENTICATED && isset( $ua_mybama_cas_auth ) ? $ua_mybama_cas_auth->get_username() : NULL );
		
	// Get the user's first name
	$user_first_name = IS_WEBTIDE_MEMBER && isset( $current_user->user_firstname ) ? $current_user->user_firstname : ( IS_MYBAMA_AUTHENTICATED && isset( $ua_mybama_cas_auth ) ? $ua_mybama_cas_auth->get_user_attribute( 'firstname' ) : NULL );
		
	// If no first name, set user name
	if ( ! $user_first_name )
		$user_first_name = $username;
		
	// Get the user's last name
	$user_last_name = IS_WEBTIDE_MEMBER && isset( $current_user->user_lastname ) ? $current_user->user_lastname : ( IS_MYBAMA_AUTHENTICATED && isset( $ua_mybama_cas_auth ) ? $ua_mybama_cas_auth->get_user_attribute( 'lastname' ) : NULL );
	
	// Get the user's email
	$user_email = IS_WEBTIDE_MEMBER && isset( $current_user->user_email ) ? $current_user->user_email : ( IS_MYBAMA_AUTHENTICATED && isset( $ua_mybama_cas_auth ) ? $ua_mybama_cas_auth->get_user_attribute( 'email' ) : NULL );
	
	// Get the user's department
	$user_department = IS_WEBTIDE_MEMBER && ( $webtide_department = get_user_meta( $current_user->ID, 'webtide_department', true ) ) ? $webtide_department : ( IS_MYBAMA_AUTHENTICATED && isset( $ua_mybama_cas_auth ) ? $ua_mybama_cas_auth->get_user_attribute( 'department' ) : NULL );
	
	// Get the user's job title
	$user_job_title = IS_WEBTIDE_MEMBER && ( $webtide_job_title = get_user_meta( $current_user->ID, 'webtide_job_title', true ) ) ? $webtide_job_title : ( IS_MYBAMA_AUTHENTICATED && isset( $ua_mybama_cas_auth ) ? $ua_mybama_cas_auth->get_user_attribute( 'title' ) : NULL );
	
}

//! This hook allows us to update user data when they login via SSO
add_action( 'ua_mybama_cas_auth_sso_user_logged_in', 'ua_webtide_sso_user_logged_in', 0, 2 );
function ua_webtide_sso_user_logged_in( $user_data, $is_new_user ) {
	global $ua_mybama_cas_auth;
	
	// Make sure we have a user ID
	if ( ! ( $user_id = isset( $user_data ) && isset( $user_data->ID ) && $user_data->ID > 0 ? $user_data->ID : NULL ) ) {
		return;
	}
	
	// Make sure we have myBama user attributes
	if ( ! ( $user_attributes = isset( $ua_mybama_cas_auth ) ? $ua_mybama_cas_auth->get_user_attributes() : NULL ) ) {
		return;
	}
	
	// myBama key => WP user meta key
	$fields_to_update = array(
		'title'		=> 'webtide_job_title',
		'address'	=> 'webtide_office_location',
		'department'=> 'webtide_department',
		'telephone'	=> 'webtide_office_phone',
		);
	
	// Go through each field and update
	foreach( $fields_to_update as $myBama_key => $user_meta_key ) {
		
		// No need to update if it doesnt exist
		if ( ! ( isset( $user_attributes[ $myBama_key ] ) && ! empty( $user_attributes[ $myBama_key ] ) ) ) {
			continue;
		}
			
		// Only update the user data doesn't already exist
		if ( ! get_user_meta( $user_id, $user_meta_key, true ) ) {
			update_user_meta($user_id, $user_meta_key, $user_attributes[ $myBama_key ]);
		}
		
	}
	
}

//! Add instructions for membership requests
add_action( 'gform_entry_detail_sidebar_middle', 'ua_webtide_add_membership_request_instructions', 0, 2 );
function ua_webtide_add_membership_request_instructions( $form, $lead ) {

    // Only for the membership request form
    if ( 2 != $lead[ 'form_id' ] ) {
		return;
	}

    ?><div class="postbox" id="ua-webtide-member-instructions-container" style="background:rgba(162,0,0,0.08);">
        <h3 class="hndle" style="cursor:default;">
            <span>For New WebTide Members</span>
        </h3>

        <div class="inside">
            <ol>
                <li style="list-style: decimal;">Activate the user in the “Entry” box above.</li>
                <li style="list-style: decimal;">Add their myBama user name to the <a href="https://webtide.ua.edu/wp-admin/options-general.php?page=ua-mybama-cas-auth" target="_blank">CAS WordPress Login Whitelist</a>.</li>
                <li style="list-style: decimal;">Add them to the <a href="https://listserv.ua.edu/" target="_blank">LISTSERV</a>.</li>
                <li style="list-style: decimal;">Do they want to be <a href="https://webtide.slack.com/admin/invites/" target="_blank">added to Slack</a>? <strong>Invite them.</strong></li>
                <li style="list-style: decimal;">Do they have <a href="https://twitter.com/bamawebtide/" target="_blank">a Twitter account</a>? <strong>Follow them.</strong></li>
                <li style="list-style: decimal;">Do they have <a href="https://github.com/bamawebtide" target="_blank">a GitHub account</a>? <strong>Invite them.</strong></li>
                <li style="list-style: decimal;">Add to <a href="https://docs.google.com/spreadsheets/d/1-D68zgggcAYSa2PH8EMzv7TXfUQBwa0InS7BhUMzY0Q/edit?usp=sharing" target="_blank">Group Members spreadsheet</a>.</li>
                <li style="list-style: decimal;">Send them <a href="https://docs.google.com/document/d/1FNNZpO_AjoafL5UkADOtt8yUpZJ27FVdyc1GVPNjWXk/edit?usp=sharing" target="_blank">the welcome email</a>! <strong>Be sure to update the template.</strong></li>
            </ol>
            <p style="background:rgba(162,0,0,0.85); color:#fff; padding:5px 5px 5px 8px;"><strong><em>Be sure to take note of all steps taken in the "Notes" area below.</em></strong></p>
        </div>
    </div><?php

}