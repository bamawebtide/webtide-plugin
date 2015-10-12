<?php
	
//! Print messages before the form is outputted via shortcode
add_filter( 'gform_get_form_filter', 'ua_webtide_print_message_before_forms', 100, 2 );
function ua_webtide_print_message_before_forms( $form_string, $form ) {
	
	// Make sure we have a form ID
	if ( ! ( isset( $form ) && isset( $form[ 'id' ] ) ) ) {
		return $form_string;
	}
	
	// Dependent upon form ID
	switch( $form[ 'id' ] ) {
		
		case '6':
		
			// If they're a WebTide member, they receive the updates automatically
			if ( IS_WEBTIDE_MEMBER ) {
				
				return '<div class="gf_browser_chrome gform_wrapper" id="gform_wrapper_6">
					<div class="gform_heading">
						<h3 class="gform_title">Subscribe to receive job posting updates</h3>
                        <span class="gform_description" style="margin-bottom:0;">Job updates will be automatically sent to the WebTide mailing list so there\'s no need for WebTide members to subscribe.</span>
                    </div>
                </div>';
	                
			}
			
			break;
			
	}
	
	return $form_string;
	
}
	
//! Filter the form content
add_filter( 'gform_get_form_filter', 'ua_webtide_filter_get_form', 0, 2 );
function ua_webtide_filter_get_form( $form_string, $form ) {
	global $ua_mybama_cas_auth, $user_first_name;
	
	// Making sure forms show up for admins so they can see/test the form
	if ( current_user_can( 'manage_options' ) ) {
		return $form_string;
	}
				
	// Filter depending on form ID
	switch( $form[ 'id' ] ) {
		
		// Request Membership
		case 2:
			
			// If the user is already a member, then they don't need the form
			if ( IS_WEBTIDE_MEMBER ) {
				
				// Build message
				$webtide_member_message = NULL;
				
				// If we have their name, add their name
				if ( $user_first_name )
					$webtide_member_message .= "Hey there, {$user_first_name}. ";
				
				$webtide_member_message .= "There's no need for you to fill out this form because you're already a member.";
				
				return '<p class="red"><strong>' . $webtide_member_message . '</strong></p>';
			
			// They must be authenticated to fill out the form
			} else if ( ! IS_MYBAMA_AUTHENTICATED ) {
				
				// Get the login URL
				$login_url = $ua_mybama_cas_auth->get_login_url();
				
				return '<p class="red"><strong>You must be employed by The University of Alabama to request WebTide membership. Please authenticate yourself by signing in to myBama.</strong></p>' . '<a href="' . $login_url . '" class="button">Sign in to myBama</a>';
				
			}
			
			break;
			
		// Submit A Job Posting
		case 3:
		
			// They must be authenticated to fill out the form
			if ( ! ( IS_WEBTIDE_MEMBER || IS_MYBAMA_AUTHENTICATED ) ) {
				
				// Get the login URL
				$login_url = $ua_mybama_cas_auth->get_login_url();
				
				return '<p class="red"><strong>You must be employed by The University of Alabama to submit a job posting. Please authenticate yourself by signing in to myBama.</strong></p>' . '<a href="' . $login_url . '" class="button">Sign in to myBama</a>';
				
			}
		
			break;
			
		// Submit An Event
		case 4:
		
			// They must be authenticated to fill out the form
			if ( ! ( IS_WEBTIDE_MEMBER || IS_MYBAMA_AUTHENTICATED ) ) {
				
				// Get the login URL
				$login_url = $ua_mybama_cas_auth->get_login_url();
				
				return '<p class="red"><strong>You must be employed by The University of Alabama to submit an event. Please authenticate yourself by signing in to myBama.</strong></p>' . '<a href="' . $login_url . '" class="button">Sign in to myBama</a>';
				
			}
		
			break;
			
	}

	return $form_string;
	
}
	
//! Filter the value of field forms
add_filter( 'gform_field_value', 'ua_webtide_filter_form_field_value', 10, 3 );
function ua_webtide_filter_form_field_value( $value, $field, $field_name ) {
	global $ua_mybama_cas_auth, $username, $user_first_name, $user_last_name, $user_email, $user_department, $user_job_title;

	switch( $field_name ) {
		
		// We can use this for conditional logic for other fields
		case 'is_webtide_member':
		
			if ( IS_WEBTIDE_MEMBER ) {
				return 'yes';
			}
			
			break;
			
		// We can use this for conditional logic for other fields
		case 'is_mybama_authenticated':
		
			if ( IS_MYBAMA_AUTHENTICATED ) {
				return 'yes';
			}
				
			break;
		
		case 'username':
			
			if ( isset( $username ) && ! empty( $username ) ) {
				return $username;
			}
				
			break;
			
		case 'telephone':
		
			// Get the telephone
			if ( $telephone = $ua_mybama_cas_auth->get_user_attribute( $field_name ) ) {
				
				// Strip away country code
				$telephone = preg_replace( '/^\+1/i', '', $telephone );
				
				// Strip away non-numbers
				$telephone = preg_replace( '/[^0-9\+]/i', '', $telephone );
				
				// Format number
				return preg_replace( '/^([0-9]{3})([0-9]{3})([0-9]{4})$/i', '(\1) \2-\3', $telephone );
				
			}
		
		case 'first_name':
		case 'last_name':
		case 'email':
		case 'department':
		case 'job_title':
		case 'address':
		
			if ( isset( ${"user_{$field_name}"} ) && ! empty( ${"user_{$field_name}"} ) ) {
				return ${"user_{$field_name}"};
			} else if ( $user_attribute = $ua_mybama_cas_auth->get_user_attribute( $field_name ) ) {
				return $user_attribute;
			}

			break;
			
		case 'full_name':
		
			if ( isset( $user_first_name ) ) {
				
				if ( isset( $user_last_name ) ) {
					return "{$user_first_name} {$user_last_name}";
				} else {
					return $user_first_name;
				}
					
			}
		
			break;
				
		case 'employment_status':
		
			if ( IS_WEBTIDE_MEMBER ) {
				return 'Yes, I am a web professional at UA';
			}
				
		case 'work_for_college':
		
			// If WebTide Member, see if they have college info stored
			if ( IS_WEBTIDE_MEMBER && ( $user_college = get_user_meta( get_current_user_id(), 'webtide_college', true ) ) ) {
				return 'Yes';
			}
		
			break;
			
		case 'college':
		
			// If WebTide Member, see if they have college info stored
			if ( IS_WEBTIDE_MEMBER && ( $user_college = get_user_meta( get_current_user_id(), 'webtide_college', true ) ) ) {
				
				// Tweak some of the names
				switch( $user_college ) {
					
					case 'College of Engineering':
						return 'Engineering';
						break;
						
				}
				
				return $user_college;
			
			}
			
			break;
		
		case 'github':
		
			// If WebTide Member, see if they have github info stored
			if ( IS_WEBTIDE_MEMBER && ( $user_github = get_user_meta( get_current_user_id(), 'webtide_github', true ) ) ) {
				return $user_github;
			}
				
		case 'twitter':
		
			// If WebTide Member, see if they have twitter info stored
			if ( IS_WEBTIDE_MEMBER && ( $user_twitter = get_user_meta( get_current_user_id(), 'webtide_twitter', true ) ) ) {
				return $user_twitter;
			}
		
			break;
			
	}
	
	return $value;
	
}