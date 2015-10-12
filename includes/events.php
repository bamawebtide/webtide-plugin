<?php
	
// Change the sidebar ID
add_filter( 'ua_webtide_left_sidebar_id', 'ua_webtide_filter_events_left_sidebar_id' );
function ua_webtide_filter_events_left_sidebar_id( $sidebar_id ) {
	
	if ( is_post_type_archive( 'tribe_events' ) || is_singular( 'tribe_events' )
		|| is_page( 'submit-an-event' ) || is_tax( 'tribe_events_cat' ) ) {
			
		return 'calendar';
		
	}
		
	return $sidebar_id;
	
}

// Make sure all events archives are archives
add_filter( 'pre_get_posts', 'ua_webtide_set_events_post_type_archives', 100 );
function ua_webtide_set_events_post_type_archives( $query ) {
	
	// Only for main query
	if ( ! $query->is_main_query() ) {
		return;
	}
		
	// Is a display set?
	$event_display = $query->get( 'eventDisplay' );
		
	// If we're querying any events archives, make sure its marked as an archive
	if ( 'tribe_events' == $query->get( 'post_type' )
		&& $event_display && 'single-event' != $event_display
		&& ! $query->is_singular( 'tribe_events' ) && ! $query->is_tax( 'tribe_events_cat' ) ) {
		
		$query->is_post_type_archive = true;
		
	}
	
}

// Add the menu before the calendar sidebar
add_action( 'ua_webtide_before_sidebar', 'ua_webtide_add_calendar_menu' );
function ua_webtide_add_calendar_menu( $sidebar_id ) {
	
	// Only for the calendar sidebar
	if ( 'calendar' != $sidebar_id ) {
		return;
	}
		
	// Only if we have terms
	$events_categories = get_terms( 'tribe_events_cat', array(
    	'orderby'           => 'name', 
		'order'             => 'ASC',
		'hide_empty'        => true, 
		'fields'            => 'all', 
		) );
		
	if ( ! $events_categories ) {
		return;
	}
		
	// Are we viewing the main events page?
	$is_main_events_page = is_post_type_archive( 'tribe_events' );
	
	?><div class="widget widget_nav_menu view-events-menu">
		<h3 class="widget-title">View Events<?php
			
			// Link to view all events if not on main events page
			if ( ! $is_main_events_page ) {
				?><span class="widget-title-link"><a href="https://webtide.ua.edu/events/">View all</a></span><?php
			}
			
		?></h3>
		<div class="menu-calendar-container">
			<ul id="menu-calendar" class="menu"><?php
				
				foreach( $events_categories as $event_cat ) {
					
					// Get the term link
					if ( ! ( $event_cat_term_link = get_term_link( $event_cat, 'tribe_events_cat' ) ) )
						continue;
						
					// Is this the current page?
					$current_page = is_viewing_ua_webtide_link( $event_cat_term_link, ! $is_main_events_page );
					
					?><li<?php echo $current_page ? ' class="current-menu-item"' : NULL; ?>><a href="<?php echo $event_cat_term_link; ?>list/"><?php echo $event_cat->name; ?></a></li><?php
						
				}
				
			?></ul>
		</div>
	</div><?php
	
}

// Set the main header
add_filter( 'ua_webtide_main_header', 'ua_webtide_events_set_main_header_title' );
function ua_webtide_events_set_main_header_title( $title ) {
	
	if ( is_post_type_archive( 'tribe_events' ) || is_tax( 'tribe_events_cat' ) || is_singular( 'tribe_events' ) ) {
		return 'Calendar of Events';
	}
		
	return $title;
	
}

// Set the subheader
add_filter( 'ua_webtide_main_subheader', 'ua_webtide_events_set_main_subheader' );
function ua_webtide_events_set_main_subheader( $subheader ) {
	
	if ( is_post_type_archive( 'tribe_events' ) || is_tax( 'tribe_events_cat' ) || is_singular( 'tribe_events' ) ) {
		return 'We love professional development so on our calendar you\'ll find everything from our monthly member meetings to conferences, webinars and more.';
	}
		
	return $subheader;
	
}

// Don't print the loop
add_filter( 'ua_webtide_run_the_loop', 'ua_webtide_filter_events_run_the_loop' );
function ua_webtide_filter_events_run_the_loop( $run_the_loop ) {
	
	// Don't print the loop on the events pages
	if ( is_post_type_archive( 'tribe_events' ) || is_tax( 'tribe_events_cat' ) || is_singular( 'tribe_events' ) ) {
		return false;
	}
	
	return $run_the_loop;
	
}
	
//! Filter the events title
add_filter( 'tribe_events_title', 'ua_webtide_filter_tribe_get_events_title', 100 );
add_filter( 'tribe_get_events_title', 'ua_webtide_filter_tribe_get_events_title', 100 );
function ua_webtide_filter_tribe_get_events_title( $title, $depth = NULL ) {

	// Remove 'Events for' from beginning of title
	$title = preg_replace( '/Events\sfor\s/i', '', $title );
		
	return $title;
	
}

//! Filter the schedule details
add_filter( 'tribe_events_event_schedule_details', 'ua_webtide_filter_tribe_events_event_schedule_details' );
function ua_webtide_filter_tribe_events_event_schedule_details( $schedule ) {

	// Replace the "@" sign with a "/"
	$schedule = preg_replace( '/\s\@\s/i', ' / ', $schedule );
	
	return $schedule;
	
}

//! Add a monthly meeting notice
add_action( 'tribe_events_after_the_title', 'ua_webtide_after_title_notices' );
function ua_webtide_after_title_notices() {
	
	// Show notice for monthly meetings archive
	if ( is_tax( 'tribe_events_cat', 'monthly-meetings' ) ) {
		
		?><div class="tribe-events-notices"><ul><li><strong>Monthly meetings are for WebTide members only.</strong></li></ul></div><?php

	}
	
}

//! Filter the notices
//add_filter( 'tribe_events_the_notices', 'ua_webtide_filter_events_notices', 100, 2 );
function ua_webtide_filter_events_notices( $html, $notices ) {

	// If we're viewing a monthly meeting
	if ( is_singular( 'tribe_events' ) && has_term( 'monthly-meetings', 'tribe_events_cat' ) ) {
		
		// Add member notice
		$notices[] = '<strong>Monthly meetings are for WebTide members only.</strong>';
		
	}

	// Create new HTML
	$html = ! empty( $notices ) ? '<div class="tribe-events-notices"><ul><li>' . implode( '</li><li>', $notices ) . '</li></ul></div>' : NULL;

	return $html;
	
}

//! Add info before the event description
add_action( 'tribe_events_single_event_before_the_content', 'ua_webtide_events_add_before_event_description', 0 );
function ua_webtide_events_add_before_event_description() {
	global $post;
	
	// Only for single events pages
	if ( ! is_singular( 'tribe_events' ) ) {
		return;
	}
	
	// Get the event ID
	if ( ! ( $event_id = isset( $post ) && isset( $post->ID ) ? $post->ID : NULL ) ) {
		return;
	}
		
	// If we have speakers
	if ( have_rows( 'speakers' ) ):
	
	 	// Loop through the speakers
	    while ( have_rows( 'speakers' ) ) : the_row();
	
	        // Show the speaker
			if ( $speaker_name = get_sub_field( 'speaker_name' ) ) {
				
				?><h3 class="meeting-speaker"><span class="speaker-label">Speaker:</span> <?php echo $speaker_name;
					
					if ( $speaker_job_title = get_sub_field( 'speaker_job_title' ) ) {
						
						echo ", {$speaker_job_title}";
						
					}
					
					if ( $speaker_from = get_sub_field( 'speaker_from' ) ) {
						
						echo ", <em>{$speaker_from}</em>";
						
					}
					
				?></h3><?php
					
			}
	
	    endwhile;
	
	endif;
	
	// Show monthly meeting notice
	if ( has_term( 'monthly-meetings', 'tribe_events_cat' ) ) {
		
		?><div class="tribe-events-notices"><ul><li><strong>Monthly meetings are for WebTide members only.</strong></li></ul></div><?php
			
	}
		
	// Will lunch be served?
	if ( $will_lunch_be_served = strcasecmp( 'yes', get_post_meta( $event_id, 'will_lunch_be_served', true ) ) == 0 ) {
		
		// What will be served?
		$served_for_lunch = get_post_meta( $event_id, 'served_for_lunch', true );
		
		// Is someone sponsoring lunch?
		$lunch_is_sponsored = strcasecmp( 'yes', get_post_meta( $event_id, 'lunch_is_sponsored', true ) ) == 0;
			
		?><p class="lunch-will-be-served"><?php echo $lunch_is_sponsored ? '<strong>' . ( ! empty( $served_for_lunch ) ? $served_for_lunch : 'Lunch' ) . ' will be served!</strong>' : 'Lunch will be served!';
			
			if ( $lunch_is_sponsored
				&& ( $lunch_sponsor = get_post_meta( $event_id, 'lunch_sponsor', true ) ) ) {
					
				// Do we have a website?
				$lunch_sponsor_website = get_post_meta( $event_id, 'lunch_sponsor_website', true );
				
				?> Be sure to say thank you to <?php
					
					echo $lunch_sponsor_website ? '<a href="' . $lunch_sponsor_website . '">' . $lunch_sponsor . '</a>' : $lunch_sponsor;
					
				?> for sponsoring lunch.<?php
					
			}
			
		?></p><?php
		
	}
	
}

//! Add info after event description
//add_action( 'tribe_events_single_event_after_the_content', 'ua_webtide_events_add_after_event_description', 0 );
function ua_webtide_events_add_after_event_description() {
	global $post;
	
	// Only for single events pages
	if ( ! is_singular( 'tribe_events' ) ) {
		return;
	}
	
	// Get the event ID
	if ( ! ( $event_id = isset( $post ) && isset( $post->ID ) ? $post->ID : NULL ) ) {
		return;
	}
	
}

//! Filter the beginning of the day so that its set to current time so events that have passed in the day have passed
add_filter( 'tribe_event_beginning_of_day', 'ua_webtide_events_filter_tribe_event_beginning_of_day', 100 );
function ua_webtide_events_filter_tribe_event_beginning_of_day( $date ) {
	
	if ( is_front_page() ) {
		
		// Get the current date/time in Central time
		$right_now = new DateTime();
		$right_now->setTimeZone( new DateTimeZone( 'America/Chicago' ) );
	
		return $right_now->format( 'Y-m-d H:m:s' );
		
	}
	
	return $date;
	
}

//! Get the next meeting query
function ua_webtide_get_next_monthly_meeting() {
	
	// Get our next meeting
	if ( $next_meeting = tribe_get_events( array(
		'eventDisplay'   => 'list',
		'posts_per_page' => 1,
		'tax_query' => array(
			array(
				'taxonomy' => 'tribe_events_cat',
				'field'    => 'slug',
				'terms'    => 'monthly-meetings',
			),
		))) ) {
			
		return isset( $next_meeting[ 0 ] ) ? $next_meeting[ 0 ] : $next_meeting;

	}
	
	return false;
	
}

//! Get date/time string
function ua_webtide_get_date_time_string( $event, $abbreviate = false ) {
	
	// Build the string
	$dt_string = NULL;
	
	// What's the start date format?
	$start_date_format = $abbreviate ? 'D\., F j' : 'l, F j';
	
	// What's the start time format?
	$start_time_format = 'g:i a';
	
	// Get the start date
	if ( $start_date = tribe_get_start_date( $event, true, $start_date_format ) ) {
	
		// Add the date to the string
		$dt_string .= '<span class="meeting-date">' . $start_date . '</span>';
	
	}
	
	// Get the start time
	if ( $start_time = tribe_get_start_date( $event, true, $start_time_format ) ) {
		
		// Add the separator
		$dt_string .= '<span class="meeting-dt-sep"> / </span>';
		
		// Add the time to the string
		$dt_string .= '<span class="meeting-time">' . $start_time . '</span>';
		
	}
		
	return $dt_string;
	
}

//! Get the next meeting HTML
function ua_webtide_get_next_monthly_meeting_html( $args = array() ) {
	
	// Parse the args
	$defaults = array(
		'event_title_element'	=> 'h3',
		'abbreviate_dt'			=> false,
		'include_excerpt'		=> false,
		'view_details'			=> true,
		'include_button'		=> false,
	);
	$args = wp_parse_args( $args, $defaults );
	
	// Build the next meeting
	$next_meeting_html = NULL;
	
	// Get the next meeting
	if ( $next_meeting = ua_webtide_get_next_monthly_meeting() ) {
		
		// Get the permalink
		$next_meeting_permalink = get_permalink( $next_meeting->ID );
		
		// Print the event title
		$next_meeting_html .= '<' . $args[ 'event_title_element' ] . ' class="meeting-title"><a href="' . $next_meeting_permalink . '">' . get_the_title( $next_meeting->ID ) . '</a></' . $args[ 'event_title_element' ] . '>';
		
		// Print the details
		$next_meeting_html .= '<p class="meeting-details">';
		
			// Add members only message
			if ( ! IS_WEBTIDE_MEMBER ) {
			
				$next_meeting_html .= '<span class="members-only">Monthly meetings are for WebTide members only.</span>';
			
			}

			// Show details for members
			else {
				
				if ( $speakers = get_field( 'speakers', $next_meeting->ID ) ) {
					
					// Add each speaker
					foreach( $speakers as $speaker ) {
						
						// Make sure we have a name
						if ( isset( $speaker[ 'speaker_name' ] ) && ! empty( $speaker[ 'speaker_name' ] ) ) {
					
							$next_meeting_html .= '<span class="meeting-speaker">';
							
								// Print the speaker
								$next_meeting_html .= $speaker[ 'speaker_name' ];
								
								// If we have a "from"
								if ( isset( $speaker[ 'speaker_from' ] ) && ! empty( $speaker[ 'speaker_from' ] ) ) {
									
									$next_meeting_html .= ", <em>{$speaker[ 'speaker_from' ]}</em>";
									
								}
								
							$next_meeting_html .= '</span>';
							
						}
						
					}
					
				}
				
				// False = do not abbreviate stuff
				if ( $dt_string = ua_webtide_get_date_time_string( $next_meeting, false ) ) {
					
					$next_meeting_html .= '<span class="meeting-date-time">' . $dt_string . '</span>';
					
				}
				
				// Get the venue
				if ( $venue = tribe_get_venue() ) {
					
					$next_meeting_html .= '<span class="meeting-location">' . $venue . '</span>';
					
				}
				
				// Will lunch be served?
				if ( ( $will_lunch_be_served = get_post_meta( $next_meeting->ID, 'will_lunch_be_served', true ) )
					&& strcasecmp( 'yes', $will_lunch_be_served ) == 0 ) {
						
					$next_meeting_html .= '<span class="lunch-will-be-served">Lunch will be served!</span>';
					
				}
				
			}
		
		$next_meeting_html .= '</p>';

		// If we're including the excerpt
		if ( $args[ 'include_excerpt' ] ) {

			// Setup the text
			$raw_excerpt = $text = $next_meeting->post_excerpt;

			// If we have no excerpt...
			if ( '' == $text && ! empty( $next_meeting->post_content ) ) {

				// Use content and strip shortcodes
				$text = strip_shortcodes( $next_meeting->post_content );

			}

			// Take care of the text
			$text = apply_filters( 'the_content', $text );
			$text = str_replace(']]>', ']]&gt;', $text);

			// Get excerpt length
			$excerpt_length = 12; //apply_filters( 'excerpt_length', 10 );

			// Filter the string in the "more" link displayed after a trimmed excerpt.
			$excerpt_more = apply_filters( 'excerpt_more', '...' );

			// Trim the text
			$text = wp_trim_words( $text, $excerpt_length, $excerpt_more );

			// Filter the trimmed excerpt string.
			$text = apply_filters( 'wp_trim_excerpt', $text, $raw_excerpt );

			// Add to HTML
			if ( $text ) {
				$next_meeting_html .= wpautop( $text );
			}

		}

		// Show view details link?
		if ( $args[ 'view_details' ] ) {
			$next_meeting_html .= '<a class="meeting-view-details" href="' . $next_meeting_permalink . '">View details</a>';
		}

		// Show button?
		if ( $args[ 'include_button' ] ) {
			$next_meeting_html .= '<a class="button expand" href="' . $next_meeting_permalink. '">Learn more about the meeting</a>';
		}
		
	}
	
	return $next_meeting_html;
	
}