<?php

//! Make sure the closing date is always in the yyyymmdd format, i.e. 20150803,
// to work with Gravity Forms -> Advanced Custom fields
add_filter( 'sanitize_post_meta_closing_date', function( $meta_value, $meta_key, $meta_type ) {

	// Convert to date
	if ( strtotime( $meta_value ) !== false ) {

		if ( $new_closing_date = new DateTime( $meta_value ) ) {
			return $new_closing_date->format( 'Ymd' );
		}

	}

	return $meta_value;

}, 100, 3 );

//! Add jobs meta boxes
add_action( 'add_meta_boxes', 'ua_webtide_add_jobs_meta_boxes', 10, 2 );
function ua_webtide_add_jobs_meta_boxes( $post_type, $post ) {
	
	// Add MailChimp job scubscription notification information
    add_meta_box( 'mc-wt-job-notifications', 'MailChimp Notifications', 'ua_webtide_print_jobs_meta_boxes', 'jobs', 'side', 'core' );
    
}

//! Print jobs meta boxes
function ua_webtide_print_jobs_meta_boxes( $post, $metabox ) {
	
	switch( $metabox[ 'id' ] ) {
		
		case 'mc-wt-job-notifications':
		
			// Get our notification time, if set, for the job postings subscription list
			if ( ( $notification_str = get_post_meta( $post->ID, '_mailchimp_webtide_job_subscription_notification', true ) )
				&& strtotime( $notification_str ) !== false ) {
					
				// Get the current date/time in Central time
				$right_now = new DateTime();
				$right_now->setTimeZone( new DateTimeZone( 'America/Chicago' ) );
				
				// Convert to central time
				$notification = new DateTime( $notification_str, new DateTimeZone( 'UTC' ) );
				$notification->setTimeZone( new DateTimeZone( 'America/Chicago' ) );
				
				?><p>This job <?php echo $notification > $right_now ? 'will be' : 'was'; ?> sent to the "UA Job Postings For Web Professionals" MailChimp mailing list on <strong><?php echo $notification->format( 'n/j/Y \a\t\ g:i a' ); ?></strong>.</p><?php
				
			} else {
				
				?><p style="color:red;">This job has not been sent to the "UA Job Postings For Web Professionals" MailChimp mailing list.</p><?php
					
			}
			
			// Get our notification time, if set, for the WebTide list
			if ( ( $notification_str = get_post_meta( $post->ID, '_mailchimp_webtide_job_webtide_notification', true ) )
				&& strtotime( $notification_str ) !== false ) {
					
				// Get the current date/time in Central time
				$right_now = new DateTime();
				$right_now->setTimeZone( new DateTimeZone( 'America/Chicago' ) );
				
				// Convert to central time
				$notification = new DateTime( $notification_str, new DateTimeZone( 'UTC' ) );
				$notification->setTimeZone( new DateTimeZone( 'America/Chicago' ) );
				
				?><p>This job <?php echo $notification > $right_now ? 'will be' : 'was'; ?> sent to the WebTide MailChimp mailing list on <strong><?php echo $notification->format( 'n/j/Y \a\t\ g:i a' ); ?></strong>.</p><?php
				
			} else {
				
				?><p style="color:red;">This job has not been sent to the WebTide MailChimp mailing list.</p><?php
					
			}
			
			break;
			
	}
	
}

// Runs when a job has been added to send the MailChimp job posts mailing list
add_action( 'wp_insert_post', 'ua_webtide_job_has_been_added', 100, 3 );
function ua_webtide_job_has_been_added( $post_id, $post, $update ) {

	// Send the campaign
	send_ua_webtide_job_campaign( $post_id, $post );
	
}

function send_ua_webtide_job_campaign( $post_id, $post = null ) {

	// Make sure we have a post
	if ( ! $post ) {
		$post = get_post( $post_id );
	}

	// Get the post type
	$post_type = isset( $post->post_type ) ? $post->post_type : NULL;

	// Only for jobs post types
	if ( 'jobs' != $post_type ) {
		return;
	}

	// Get the post status
	$post_status = isset( $post->post_status ) ? $post->post_status : NULL;

	// Only for 'publish' post status
	if ( 'publish' != $post_status ) {
		return;
	}

	// Get the job's closing date
	$closing_date = get_post_meta( $post_id, 'closing_date', true );

	// It must have a closing date
	if ( ! isset( $closing_date ) || empty( $closing_date ) ) {
		return;
	}

	// Only for jobs that haven't closed
	if ( strtotime( $closing_date ) < strtotime( 'now' ) ) {
		return;
	}

	// Check our post meta to see if the job posting subscription notification has been sent to the job postings list
	$webtide_job_subs_notif = get_post_meta( $post_id, '_mailchimp_webtide_job_subscription_notification', true );

	// Check our post meta to see if the job posting notification has been sent to WebTide
	//$webtide_job_webtide_notif = get_post_meta( $post_id, '_mailchimp_webtide_job_webtide_notification', true );

	// If it's not empty, then the notification(s) has already been sent
	if ( isset( $webtide_job_subs_notif ) && ! empty( $webtide_job_subs_notif ) ) {
		return;
	}

	// We're ready to send the notification!

	// Get our MailChimp API key
	if ( ! ( $mailchimp_api_key = ( $gforms_mailchimp_options = get_option( 'gravityformsaddon_gravityformsmailchimp_settings' ) ) && isset( $gforms_mailchimp_options[ 'apiKey' ] ) ? $gforms_mailchimp_options[ 'apiKey' ] : NULL ) ) {
		return;
	}

	// Include the MailChimp wrapper
	require_once plugin_dir_path( __FILE__ ) . "MailChimp.php";

	// Construct the MailChimp wrapper
	$mailchimp = new MailChimp( $mailchimp_api_key );

	// Get the current date/time in Central time
	$right_now = new DateTime();
	$right_now->setTimeZone( new DateTimeZone( 'America/Chicago' ) );

	// Get an hour from now in UTC
	$hour_from_now = new DateTime();
	$hour_from_now->setTimeZone( new DateTimeZone( 'UTC' ) );
	$hour_from_now->modify( '+1 hour' );

	// The ID for our job posts mailing list
	$mailchimp_job_postings_list_id = '78ac144d2b';

	// The ID for our WebTide mailing list
	//$mailchimp_webtide_list_id = 'ff3b753b4d';

	// The ID for our job posts mailing template
	$mailchimp_job_postings_template_id = '132313';

	// The title for our new campaign
	$mailchimp_campaign_title = 'New UA job posting for web professionals - ' . $right_now->format( 'n/j/Y' );

	// The subject for our new campaign email
	$email_subject = 'There is a new University of Alabama job posting for web professionals';

	// Build email body
	$email_body = NULL;

	// Build email body for other jobs, if they exist
	$other_jobs_email_body = NULL;

	// Get other jobs
	if ( $other_jobs = get_ua_webtide_open_jobs( $post_id ) ) {

		foreach( $other_jobs as $this_job ) {

			// Add the job posting to the email body
			$other_jobs_email_body .= get_ua_webtide_job_body_for_mailchimp( $this_job->ID );

		}

	}

	// If we have other jobs, then prefix "NEW POSTING" to new job
	if ( $other_jobs_email_body ) {

		// Start the email body with our new job posting
		$email_body = get_ua_webtide_job_body_for_mailchimp( $post_id, array(
			'header_prefix' => 'NEW POSTING: '
		) );

		// Add other jobs
		$email_body .= $other_jobs_email_body;

		// If no other jobs, we don't need the new posting header
	} else {

		// Start the email body with our new job posting
		$email_body = get_ua_webtide_job_body_for_mailchimp( $post_id );

	}

	// Define the campaign arguments
	$campaign_args = array(
		'type'	=> 'regular',
		'options' => array( // 'list_id' is added later
			'title' => $mailchimp_campaign_title,
			'subject' => $email_subject,
			'from_email' => 'webtide@ua.edu',
			'from_name' => 'UA WebTide',
			'template_id' => $mailchimp_job_postings_template_id,
			'tracking' => array(
				'opens' => true,
				'html_clicks' => true,
				'text_clicks' => true
			),
			'analytics' => array(
				'google' => 'UA_Web_Jobs_Subscription_' . $right_now->format( 'Y\_m\_d' )
			),
			'generate_text' => true,
			'auto_tweet' => false,
			'fb_comments' => false,
		),
		'content' => array(
			'sections' => array(
				'body' => $email_body,
			)
		)
	);

	// We'll schedule the campaign to send an hour from now
	$schedule_time = $hour_from_now->format( 'Y-m-d H:i:s' );

	// If the notification hasn't been sent to the job subscription list
	if ( ! ( isset( $webtide_job_subs_notif ) && ! empty( $webtide_job_subs_notif ) ) ) {

		// Modify campaign args to create a new campaign for the job subscription list
		$campaign_args[ 'options' ][ 'list_id' ] = $mailchimp_job_postings_list_id;

		// Create a new campaign for the job postings list
		$mailchimp_create_campaign_for_jobs_list = $mailchimp->call( 'campaigns/create', $campaign_args );

		// If the campaign was created and we have an ID
		if ( $mailchimp_create_campaign_for_jobs_list
			&& ( $mailchimp_campaign_jobs_list_id = isset( $mailchimp_create_campaign_for_jobs_list[ 'id' ] ) ? $mailchimp_create_campaign_for_jobs_list[ 'id' ] : NULL ) ) {

			// Schedule the campaign to send an hour from now
			if ( $mailchimp_schedule_jobs_list_campaign = $mailchimp->call( 'campaigns/schedule', array(
				'cid'			=> $mailchimp_campaign_jobs_list_id,
				'schedule_time' => $schedule_time,
			) ) ) {

				// If it was scheduled...
				if ( isset( $mailchimp_schedule_jobs_list_campaign[ 'complete' ] ) ) {

					// Mark that this job has been "notified" with the date/time
					update_post_meta( $post_id, '_mailchimp_webtide_job_subscription_notification', $schedule_time );

				}

			}

		}

	}

}
	
// Get job content for MailChimp email
function get_ua_webtide_job_body_for_mailchimp( $post_id, $args = array() ) {
	
	// Set up defaults
	$defaults = array(
		'header_prefix' => NULL,
		'header_suffix' => NULL,
	);
	extract( wp_parse_args( $args, $defaults ), EXTR_OVERWRITE );

	// Get the job permalink
	$job_permalink = get_permalink( $post_id );
	
	// Get the job title
	$job_title = get_the_title( $post_id );
	
	// Create the email body
	$email_body = NULL;
	
	// Add the job title
	$email_body = '<h2 style="margin:1.1em 0 0.075em 0;">' . $header_prefix . '<a href="' . $job_permalink . '" target="_blank">' . $job_title . '</a> ' . $header_suffix . '</h2>';
	
	// Add the job details
	$department = get_post_meta( $post_id, 'department', true );
	$closing_date = strtotime( get_post_meta( $post_id, 'closing_date', true ) );
	
	if ( $department || $closing_date ) {
		
		$email_body .= '<p style="margin:0 0 10px 0; color:#444;">';
		
			if ( $department ) {
				$email_body .= '<strong>Department:</strong> ' . $department;
			}
				
			if ( $closing_date ) {
				
				// Add separator
				if ( $department ) {
					$email_body .= ' / ';
				}
					
				$email_body .= '<strong>Closing Date:</strong> ' . date( 'n/j/Y', $closing_date );
				
			}
				
		$email_body .= '</p>';
		
	}
	
	// Build Facebook share URL
	$facebook_share_url = get_ua_webtide_facebook_share_url( $post_id ); 
	
	// Build Twitter share URL
	$twitter_share_url = get_ua_webtide_job_tweet_intent_url( $post_id );
	
	// Add the social share
	if ( $facebook_share_url || $twitter_share_url ) {
		
		$email_body .= '<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnShareBlock" style="margin: 0;padding: 0;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
		    <tbody class="mcnShareBlockOuter" style="padding:0;">
		            <tr>
		                <td valign="top" style="padding: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" class="mcnShareBlockInner">
		                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnShareContentContainer" style="border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
		    <tbody><tr>
		        <td align="left" style="padding-top: 0;padding-left: 0;padding-bottom: 0;padding-right: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
		            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnShareContent" style="border: 0;background: none;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
		                <tbody><tr>
		                    <td align="left" valign="top" class="mcnShareContentItemContainer" style="padding: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
								<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
									<tbody><tr>
										<td align="left" valign="top" style="mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">';
										
											if ( $facebook_share_url ) {
					                        
					                            $email_body .= '<table align="left" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
					                                <tbody><tr>
					                                    <td valign="top" style="padding-right: 9px;padding-bottom: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" class="mcnShareContentItemContainer">
					                                        <table border="0" cellpadding="0" cellspacing="0" width="" class="mcnShareContentItem" style="border-collapse: separate;border: 1px solid #CCCCCC;border-radius: 5px;background-color: #FAFAFA;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
					                                            <tbody><tr>
					                                                <td align="left" valign="middle" style="padding-top: 5px;padding-right: 9px;padding-bottom: 5px;padding-left: 9px;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
					                                                    <table align="left" border="0" cellpadding="0" cellspacing="0" width="" style="border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
					                                                        <tbody><tr>
					                                                            <td align="center" valign="middle" width="24" class="mcnShareIconContent" style="mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
					                                                                <a href="' . $facebook_share_url . '" target="_blank" style="word-wrap: break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #990000;font-weight: normal;text-decoration: underline;"><img src="http://cdn-images.mailchimp.com/icons/social-block-v2/color-facebook-48.png" style="display: block;border: 0;outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;height: auto !important;" height="24" width="24" class=""></a>
					                                                            </td>
					                                                            <td align="left" valign="middle" class="mcnShareTextContent" style="padding-left: 5px;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
					                                                                <a href="' . $facebook_share_url . '" target="" style="color: #505050;font-family: Arial;font-size: 12px;font-weight: normal;line-height: 100%;text-align: center;text-decoration: none;word-wrap: break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">Share</a>
					                                                            </td>
					                                                        </tr>
					                                                    </tbody></table>
					                                                </td>
					                                            </tr>
					                                        </tbody></table>
					                                    </td>
					                                </tr>
					                            </tbody></table>';
					                            
					                        }
					                        
					                    $email_body .= '<!--[if gte mso 6]>
										</td>
								    	<td align="left" valign="top">
										<![endif]-->';
										
											if ( $twitter_share_url ) {
					                        
					                            $email_body .= '<table align="left" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
					                                <tbody><tr>
					                                    <td valign="top" style="padding-right: 0;padding-bottom: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" class="mcnShareContentItemContainer">
					                                        <table border="0" cellpadding="0" cellspacing="0" width="" class="mcnShareContentItem" style="border-collapse: separate;border: 1px solid #CCCCCC;border-radius: 5px;background-color: #FAFAFA;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
					                                            <tbody><tr>
					                                                <td align="left" valign="middle" style="padding-top: 5px;padding-right: 9px;padding-bottom: 5px;padding-left: 9px;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
					                                                    <table align="left" border="0" cellpadding="0" cellspacing="0" width="" style="border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
					                                                        <tbody><tr>
					                                                            <td align="center" valign="middle" width="24" class="mcnShareIconContent" style="mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
					                                                                <a href="' . $twitter_share_url . '" target="_blank" style="word-wrap: break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #990000;font-weight: normal;text-decoration: underline;"><img src="http://cdn-images.mailchimp.com/icons/social-block-v2/color-twitter-48.png" style="display: block;border: 0;outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;height: auto !important;" height="24" width="24" class=""></a>
					                                                            </td>
					                                                            <td align="left" valign="middle" class="mcnShareTextContent" style="padding-left: 5px;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
					                                                                <a href="' . $twitter_share_url . '" target="" style="color: #505050;font-family: Arial;font-size: 12px;font-weight: normal;line-height: 100%;text-align: center;text-decoration: none;word-wrap: break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">Tweet</a>
					                                                            </td>
					                                                        </tr>
					                                                    </tbody></table>
					                                                </td>
					                                            </tr>
					                                        </tbody></table>
					                                    </td>
					                                </tr>
					                            </tbody></table>';
						                            
						                    }
						                        
						                $email_body .= '<!--[if gte mso 6]>
										</td>
								    	<td align="left" valign="top">
										<![endif]-->
					                        
										</td>
									</tr>
								</tbody></table>
		                    </td>
		                </tr>
		            </tbody></table>
		        </td>
		    </tr>
		</tbody></table>
		
		                </td>
		            </tr>
		    </tbody>
		</table>';
		
	}
	
	// Add description
	if ( ( $job_post_content = get_post_field( 'post_content', $post_id ) )
		&& ( $job_content = wpautop( $job_post_content, false ) ) ) {
		
		$email_body .= $job_content;
		
	}
	
	return $email_body;
		
}

//! Get job posting tweet intent URL
function get_ua_webtide_job_tweet_intent_url( $post_id ) {
	
	if ( strcasecmp( 'yes', get_post_meta( $post_id, 'customize_the_tweet', true ) ) == 0
		&& ( $custom_tweet = get_post_meta( $post_id, 'custom_tweet', true ) ) ) {
			
		return 'https://twitter.com/intent/tweet?text=' . urlencode( $custom_tweet ) . '&url=' . urlencode( 'https://webtide.ua.edu/jobs/' ); //get_permalink( $post_id )
		
	}
	
	return false;
	
}

//! Get job posting facebook share URL
function get_ua_webtide_facebook_share_url( $post_id ) {
	
	return add_query_arg( array(
		 'app_id' 		=> '1407829472855501',
		 'display' 		=> 'page',
		 'href'			=> urlencode( 'https://webtide.ua.edu/jobs/' ), //get_permalink( $post_id )
		 'redirect_uri' => urlencode( 'https://webtide.ua.edu/jobs/' ),
		 ), 'https://www.facebook.com/dialog/share' );
	
}

//! Get open jobs
function get_ua_webtide_open_jobs( $exclude = NULL ) {
	
	// Will hold open jobs
	$open_jobs = array();
	
	if ( $open_jobs_posts = get_posts( array(
		'post_type' 		=> 'jobs',
		'posts_per_page' 	=> -1,
		'exclude' 			=> $exclude,
		'suppress_filters' 	=> false,
		) ) ) {
			
		foreach( $open_jobs_posts as $this_job ) {
		
			// Check the closing date
			if ( $closing_date = get_post_meta( $this_job->ID, 'closing_date', true ) ) {
				
				// Only get jobs that haven't closed
				if ( strtotime( $closing_date ) >= strtotime( 'now' ) ) {
				
					$open_jobs[] = $this_job;
					
				}
					
			}
			
		}
		
	}
	
	return $open_jobs;
	
}

//! Get jobs count
function get_ua_webtide_jobs_count() {
	
	// See if the jobs count is cached
	$webtide_jobs_count = wp_cache_get( 'jobs_count', 'webtide' );
	
	// If it's not cached...
	if ( false === $webtide_jobs_count ) {
		
		// Get the count
		$webtide_jobs_count = ( $jobs_counts = wp_count_posts( 'jobs' ) ) && isset( $jobs_counts->publish ) && $jobs_counts->publish > 0 ? $jobs_counts->publish : 0;
		
		// Set the cache
		wp_cache_set( 'jobs_count', $webtide_jobs_count, 'webtide' );
		
	}
	
	return $webtide_jobs_count;
	
}
	
//! Filter the main menu to add jobs notifications
add_filter( 'walker_nav_menu_start_el', 'ua_webtide_filter_jobs_nav_menu', 100, 4 );
function ua_webtide_filter_jobs_nav_menu( $item_output, $item, $depth, $args ) {
	
	// Only for the main menu
	if ( ! ( isset( $args ) && isset( $args->theme_location ) && 'main-menu' == $args->theme_location ) ) {
		return $item_output;
	}
		
	// Only if it has the "add-jobs-count" class
	if ( ! ( isset( $item ) && isset( $item->classes ) && in_array( 'add-jobs-count', $item->classes ) ) ) {
		return $item_output;
	}
		
	// Get/add the jobs count
	if ( $published_jobs_count = function_exists( 'get_ua_webtide_jobs_count' ) ? get_ua_webtide_jobs_count() : 0 ) {
		
		// Add the count before the link
		$item_output = '<span class="has-jobs-notification"><span class="job-notification">' . $published_jobs_count . '</span>' . $item_output . '</span>';
		
	}

	return $item_output;
	
}

//! Filter the jobs permalink
add_filter( 'post_type_link', 'ua_webtide_filter_jobs_permalink', 10, 4 );
function ua_webtide_filter_jobs_permalink( $post_link, $post, $leavename, $sample ) {
	
	// Make sure its a job post type
	if ( ! ( 'jobs' == $post->post_type ) ) {
		return $post_link;
	}
		
	// Get job posting URL
	if ( $job_posting_permalink = get_post_meta( $post->ID, 'job_posting_permalink', true ) ) {
		return $job_posting_permalink;
	}
		
	return $post_link;
	
}

//! Filter the jobs count queries to get valid jobs count
add_filter( 'wp_count_posts', 'ua_webtide_filter_jobs_wp_count_posts', 100, 3 );
function ua_webtide_filter_jobs_wp_count_posts( $counts, $post_type, $perm ) {
	global $wpdb;
	
	// Only for the front end
	if ( is_admin() ) {
		return $counts;
	}
	
	// Only for jobs
	if ( 'jobs' != $post_type ) {
		return $counts;
	}
		
	// Build query - pulled from wp_count_posts()
	$query = "SELECT wp_posts.post_status, COUNT(*) AS num_posts FROM {$wpdb->posts} wp_posts";
	
		// Filter the closing date
		$query .= " LEFT JOIN {$wpdb->postmeta} closing_date_meta ON closing_date_meta.post_id = wp_posts.ID AND closing_date_meta.meta_key = 'closing_date'";
		
		$query .= " WHERE wp_posts.post_type = '{$post_type}'";
		
		// Make sure the closing date hasnt passed
		$query .= " AND IF ( closing_date_meta.meta_value != '', ( STR_TO_DATE( closing_date_meta.meta_value, '%m/%d/%Y' ) >= CURDATE() OR STR_TO_DATE( closing_date_meta.meta_value, '%Y%m%d' ) >= CURDATE() ), TRUE )";
	
		if ( 'readable' == $perm && is_user_logged_in() ) {
			
			$post_type_object = get_post_type_object( $post_type );
			
			if ( ! current_user_can( $post_type_object->cap->read_private_posts ) ) {
				$query .= $wpdb->prepare( " AND ( wp_posts.post_status != 'private' OR ( wp_posts.post_author = %d AND wp_posts.post_status = 'private' ))", get_current_user_id() );
			}
			
		}
		
	$query .= ' GROUP BY wp_posts.post_status';

	// Get results
	$results = (array) $wpdb->get_results( $query, ARRAY_A );
	$counts = array_fill_keys( get_post_stati(), 0 );

	foreach ( $results as $row ) {
		$counts[ $row[ 'post_status' ] ] = $row['num_posts'];
	}

	$counts = (object) $counts;
	
	return $counts;
	
}

//! Filter the jobs queries to only get valid jobs
add_filter( 'posts_clauses', 'ua_webtide_filter_jobs_posts_clauses', 0, 2 );
function ua_webtide_filter_jobs_posts_clauses( $pieces, $query ) {
	global $wpdb;
	
	// Only for the front end
	if ( is_admin() ) {
		return $pieces;
	}
	
	// Only for jobs queries
	if ( 'jobs' != $query->get( 'post_type' ) ) {
		return $pieces;
	}
		
	// Add a left join to get closing date info
	$pieces[ 'join' ] .= " LEFT JOIN {$wpdb->postmeta} closing_date_meta ON closing_date_meta.post_id = wp_posts.ID AND closing_date_meta.meta_key = 'closing_date'";
	
	// Add a where to test the closing date
	$pieces[ 'where' ] .= " AND IF ( closing_date_meta.meta_value != '', ( STR_TO_DATE( closing_date_meta.meta_value, '%m/%d/%Y' ) >= CURDATE() OR STR_TO_DATE( closing_date_meta.meta_value, '%Y%m%d' ) >= CURDATE() ), TRUE )";

	// Order by closing date then title
	$pieces[ 'orderby' ] = "STR_TO_DATE( closing_date_meta.meta_value, '%Y%m%d' ) ASC, wp_posts.post_title ASC";

	return $pieces;
	
}