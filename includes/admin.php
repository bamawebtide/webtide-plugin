<?php
	
//! Add custom columns
add_filter( 'manage_posts_columns', 'ua_webtide_manage_posts_columns', 1, 2 );
function ua_webtide_manage_posts_columns( $columns, $post_type ) {
		
	// Create columns to add after the title
	$columns_to_add_after_title = array();
	
	// Dependent on post type
	switch( $post_type ) {
		
		case 'member_notifications':
		
			$columns_to_add_after_title = array(
				'status'	=> 'Status',
				'override'	=> 'Will Override',
				'start_date'=> 'Start Date',
				'end_date'	=> 'End Date',
				);
		
			break;
			
	}
	
	// If we have columns to add
	if ( $columns_to_add_after_title ) {
		
		// Create new columns
		$new_columns = array();
	
		foreach( $columns as $key => $name ) {
				
			// Add the original columns
			$new_columns[ $key ] = $name;	
		
			// Add our columns after the title
			if ( 'title' == $key )
				$new_columns = array_merge( $new_columns, $columns_to_add_after_title );
			
		}
		
		return $new_columns;
		
	}
	
	return $columns;
	
}
	
//! Populate custom columns
add_action( 'manage_posts_custom_column', 'ua_webtide_populate_posts_columns', 1, 2 );
function ua_webtide_populate_posts_columns( $column_name, $post_id ) {
		
	switch( $column_name ) {
		
		case 'start_date':
		
			// Get start date
			if ( ( $start_date_str = get_post_meta( $post_id, 'notification_start_date', true ) )
				&& ( $start_date = new DateTime( $start_date_str, new DateTimeZone( 'America/Chicago' ) ) ) ) {
					
				echo $start_date->format( 'n/d/Y' );
				
			} else {
				
				?><em>There is no set start date.</em><?php
					
			}
			
			break;
			
		case 'end_date':
		
			// Get end date
			if ( ( $end_date_str = get_post_meta( $post_id, 'notification_end_date', true ) )
				&& ( $end_date = new DateTime( $end_date_str, new DateTimeZone( 'America/Chicago' ) ) ) ) {
					
				echo $end_date->format( 'n/d/Y' );
				
			} else {
				
				?><em>There is no set end date.</em><?php
					
			}
			
			break;				
		
		case 'status':
		
			// Get the stored status
			if ( $status = get_ua_webtide_member_notification_status( $post_id ) ) {
				
				if ( 'active' == $status ) {
					
					?><span style="color:green;">Active</span><?php
						
				} else {
					
					?><span style="color:red;">Inactive</span><?php
						
				}
				
			}
		
			break;
			
		case 'override':
		
			if ( ( $override_other_notifications = get_post_meta( $post_id, 'override_other_notifications', true ) )
				&& strcasecmp( 'yes', $override_other_notifications ) == 0 ) {
				
				?><span style="color:red;">Will override other notifications</span><?php
					
			} else {
				
				echo 'No';
				
			}
			
			break;
			
	}
	
}
	
//! Save user custom meta fields
add_action( 'personal_options_update', 'ua_webtide_save_user_meta_fields', 0 );
add_action( 'edit_user_profile_update', 'ua_webtide_save_user_meta_fields', 0 );
function ua_webtide_save_user_meta_fields( $user_id ) {
	
	// check_admin_referer() is run before this action so we're good to go
	
	// Make sure our WebTide array is set
	if ( ! ( $webtide_data = isset( $_POST[ 'webtide' ] ) && ! empty( $_POST[ 'webtide' ] ) ? $_POST[ 'webtide' ] : NULL ) ) {
		return;
	}
		
	// Go through the array and update
	foreach( $webtide_data as $meta_key => $meta_value ) {
		
		// Sanitize according to key
		switch( $meta_key ) {
			
			case 'github':
				
				// Remove any weird characters
				// Username may only contain alphanumeric characters or dashes and cannot begin or end with a dash
				$meta_value = preg_replace( '/[^a-z0-9\-]/i', '', $meta_value );
				break;
				
			case 'twitter':
				
				// Remove any weird characters
				// A username can only contain alphanumeric characters (letters A-Z, numbers 0-9) with the exception of underscores
				$meta_value = preg_replace( '/[^a-z0-9\_]/i', '', $meta_value );
				break;
				
		}
		
		// Update the user meta
		update_user_meta( $user_id, "webtide_{$meta_key}", $meta_value );
		
	}
	
}

//! Add user custom meta fields to profile page
add_action( 'show_user_profile', 'ua_webtide_add_user_meta_fields', 0 );
add_action( 'edit_user_profile', 'ua_webtide_add_user_meta_fields', 0 );
function ua_webtide_add_user_meta_fields( $profile_user ) {

	// Get user information
	$user_job_title = get_user_meta( $profile_user->ID, 'webtide_job_title', true );
	$user_college = get_user_meta( $profile_user->ID, 'webtide_college', true );
	$user_department = get_user_meta( $profile_user->ID, 'webtide_department', true );
	$user_office_location = get_user_meta( $profile_user->ID, 'webtide_office_location', true );
	$user_office_phone = get_user_meta( $profile_user->ID, 'webtide_office_phone', true );
	$user_twitter = get_user_meta( $profile_user->ID, 'webtide_twitter', true );
	$user_github = get_user_meta( $profile_user->ID, 'webtide_github', true );
	
	$user_professional_areas = get_user_meta( $profile_user->ID, 'webtide_professional_areas', true );
	$user_professional_areas_other = get_user_meta( $profile_user->ID, 'webtide_professional_areas_other', true );
	
	?><div style="background:#b5cbd4; padding:20px; margin:1em 0 0 0;">
		<h3 style="margin-top:0;">WebTide Information</h3>
		<table class="form-table">
			<tr>
				<th><label for="webtide_job_title">Job Title</label></th>
				<td><input type="text" name="webtide[job_title]" id="webtide_job_title" value="<?php echo $user_job_title; ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th><label for="webtide_college">College</label></th>
				<td><input type="text" name="webtide[college]" id="webtide_college" value="<?php echo $user_college; ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th><label for="webtide_department">Department</label></th>
				<td><input type="text" name="webtide[department]" id="webtide_department" value="<?php echo $user_department; ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th><label for="webtide_office_location">Office Location</label></th>
				<td><input type="text" name="webtide[office_location]" id="webtide_office_location" value="<?php echo $user_office_location; ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th><label for="webtide_office_phone">Office Phone</label></th>
				<td><input type="text" name="webtide[office_phone]" id="webtide_office_phone" value="<?php echo $user_office_phone; ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th><label for="webtide_twitter">Twitter Handle</label></th>
				<td><input type="text" name="webtide[twitter]" id="webtide_twitter" value="<?php echo $user_twitter; ?>" class="regular-text" /><p class="description">e.g. "bamawebtide". No need to include the @.</p></td>
			</tr>
			<tr>
				<th><label for="webtide_github">GitHub Username</label></th>
				<td><input type="text" name="webtide[github]" id="webtide_github" value="<?php echo $user_github; ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th><label for="webtide_professional_areas">What areas do you dabble in?</label></th>
				<td>
					<fieldset><?php
						
						$the_areas = array(
							'Graphic Design',
							'Front-End Web Design / Development',
							'Back-End Web Development / Programming',
							'Database Administration',
							'Server Administration',
							'Content Strategy / Writing',
							'WordPress Development',
							'Video Production',
							'Social Media',
							);
							
						foreach( $the_areas as $this_area ) {
							
							?><label title="<?php echo $this_area; ?>"><input type="checkbox" name="webtide[professional_areas][]" value="<?php echo $this_area; ?>"<?php checked( isset( $user_professional_areas ) && in_array( $this_area, $user_professional_areas ) ); ?> /> <span><?php echo $this_area; ?></span></label><br /><?php
								
						}
						?><label for="webtide_professional_areas_other">Other:</label>&nbsp;&nbsp;<input type="text" name="webtide[professional_areas_other]" id="webtide_professional_areas_other" value="<?php echo $user_professional_areas_other; ?>" class="regular-text" />
					</fieldset>
				</td>
			</tr>
		</table>
	</div><?php
	
}