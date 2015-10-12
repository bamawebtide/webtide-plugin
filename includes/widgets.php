<?php

//! Register our widgets
add_action( 'widgets_init', function() {
	
	// HighEdWeb Alabama Save The Date
	register_widget( 'HighEdWeb_Alabama_Save_The_Date' );
	
	// Add buttons
	register_widget( 'WebTide_Button_Widget' );
	
	// Show's the next meeting
	register_widget( 'WebTide_Next_Meeting_Widget' );
	
	// Show's the upcoming events
	register_widget( 'WebTide_Upcoming_Events_Widget' );
	
});

class HighEdWeb_Alabama_Save_The_Date extends WP_Widget {

	// Sets up the widgets name etc
	public function __construct() {
		
		parent::__construct(
			'highedweb-alabama-save-the-date-widget',
			'HighEdWeb Alabama - Save The Date',
			array(
				'description' 	=> 'Adds a HighEdWeb Alabama "Save the Date" badge.',
				'classname'		=> 'highedweb-alabama-save-the-date-badge',
				)
		);
		
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		echo $args[ 'before_widget' ];
		
			?><a href="http://al15.highedweb.org/">
                <img class="logo" src="<?php echo plugins_url( 'images/heweblog-with-alabama.png', dirname( __FILE__ ) ); ?>" alt="" />
				<h3 class="header">HighEdWeb Alabama</h3>
				<p class="date">June 29-30, 2015</p>
				<p class="city">Tuscaloosa, Alabama</p>
				<p class="description">A two-day conference in Tuscaloosa, Alabama for all higher education web professionals.</p>
			</a><?php
		
		echo $args[ 'after_widget' ];
		
	}

}
	
class WebTide_Button_Widget extends WP_Widget {

	// Sets up the widgets name etc
	public function __construct() {
		
		parent::__construct(
			'webtide-button-widget',
			'WebTide Button',
			array(
				'description' 	=> 'Adds a button.',
				'classname'		=> 'webtide-button',
				)
		);
		
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		
		// See if we need to hide the button
		if ( ! apply_filters( 'show_ua_webtide_widget_button', true, $instance, $args ) ) {
			return;
		}
		
		// If for members only
		if ( isset( $instance[ 'for_members_only' ] ) && $instance[ 'for_members_only' ] && ! IS_WEBTIDE_MEMBER ) {
			return;
		}

		// If for non-members only
		if ( isset( $instance[ 'for_non_members_only' ] ) && $instance[ 'for_non_members_only' ] && IS_WEBTIDE_MEMBER ) {
			return;
		}

		// If we have linkage...
		if ( $button_linkage = ua_webtide_widgets_get_linkage_a( $instance ) ) {

			// If we're supposed to hide the button on the page it's linked to
			if ( isset( $instance[ 'hide_on_link' ] ) && $instance[ 'hide_on_link' ] ) {

				// Get the linkage
				if ( $linkage = ua_webtide_widgets_get_linkage( $instance ) ) {

					// If the URLs match, get out of here
					if ( is_viewing_ua_webtide_link( $linkage ) )
						return;

				}

			}

			echo $args[ 'before_widget' ];

				echo $button_linkage . $instance[ 'text' ] . '</a>';

			echo $args[ 'after_widget' ];

		}

	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {

		// Update settings
		$instance[ 'text' ] = strip_tags( stripslashes( $new_instance[ 'text' ] ) );
		$instance[ 'classes' ] = strip_tags( stripslashes( $new_instance[ 'classes' ] ) );
		$instance[ 'hide_on_link' ] = strip_tags( stripslashes( $new_instance[ 'hide_on_link' ] ) );
		$instance[ 'for_members_only' ] = strip_tags( stripslashes( $new_instance[ 'for_members_only' ] ) );
		$instance[ 'for_non_members_only' ] = strip_tags( stripslashes( $new_instance[ 'for_non_members_only' ] ) );

		$instance[ 'link_media_id' ] = $new_instance[ 'link_media_id' ];
		$instance[ 'link_page_id' ] = $new_instance[ 'link_page_id' ];
		$instance[ 'link_url' ] = strip_tags( stripslashes( $new_instance[ 'link_url' ] ) );
		$instance[ 'link_target' ] = $new_instance[ 'link_target' ];

		return $instance;

	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {

		// Form defaults
		$defaults = array(
			'text'			=> NULL,
			'classes'		=> 'button expand',
			'hide_on_link'	=> true,
			'for_members_only' => false,
			'for_non_members_only' => false,
			);
		$instance = wp_parse_args( $instance, $defaults );

		?><p>
			<label for="<?php echo $this->get_field_id( 'text' ); ?>">Text:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>" class="widefat" value="<?php echo isset( $instance[ 'text' ] ) && ! empty( $instance[ 'text' ] ) ? $instance[ 'text' ] : NULL; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'classes' ); ?>">Set button's CSS class(es):</label>
			<input type="text" id="<?php echo $this->get_field_id( 'classes' ); ?>" name="<?php echo $this->get_field_name( 'classes' ); ?>" class="widefat" value="<?php echo isset( $instance[ 'classes' ] ) && ! empty( $instance[ 'classes' ] ) ? $instance[ 'classes' ] : NULL; ?>" />
		</p><?php

		ua_webtide_widgets_linkage( $this, $instance );

		?><p>
			<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id( 'hide_on_link' ); ?>" name="<?php echo $this->get_field_name( 'hide_on_link' ); ?>" value="1"<?php checked( isset( $instance[ 'hide_on_link' ] ) && $instance[ 'hide_on_link' ] ); ?> /> <label for="<?php echo $this->get_field_id( 'hide_on_link' ); ?>">Don't show button when viewing link</label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id( 'for_members_only' ); ?>" name="<?php echo $this->get_field_name( 'for_members_only' ); ?>" value="1"<?php checked( isset( $instance[ 'for_members_only' ] ) && $instance[ 'for_members_only' ] ); ?> /> <label for="<?php echo $this->get_field_id( 'for_members_only' ); ?>">Show for members only</label><br />
			<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id( 'for_non_members_only' ); ?>" name="<?php echo $this->get_field_name( 'for_non_members_only' ); ?>" value="1"<?php checked( isset( $instance[ 'for_non_members_only' ] ) && $instance[ 'for_non_members_only' ] ); ?> /> <label for="<?php echo $this->get_field_id( 'for_non_members_only' ); ?>">Show for non-members only</label>
		</p><?php
		
	}

}

class WebTide_Next_Meeting_Widget extends WP_Widget {

	// Sets up the widgets name etc
	public function __construct() {
		
		parent::__construct(
			'webtide-next-meeting-widget',
			'WebTide\'s Next Meeting',
			array(
				'description' 	=> 'Display\'s the information for WebTide\'s next monthly meeting.',
				'classname'		=> 'webtide-next-meeting',
				)
		);
		
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		
		// Only for WebTide members
		if ( ! IS_WEBTIDE_MEMBER ) {
			return;
		}
		
		// If we have a next meeting...
		if ( ! ( $next_meeting = ua_webtide_get_next_monthly_meeting() ) ) {
			return;
		}
			
		// Make sure we have a start date
		if ( ! ( ( $event_start_date_str = isset( $next_meeting->EventStartDate ) ? $next_meeting->EventStartDate : NULL )
			&& strtotime( $event_start_date_str ) !== false ) ) {
			return;
		}
				
		// Set the start datetime
		$event_start_date = new DateTime( $event_start_date_str, new DateTimeZone( 'America/Chicago' ) );
		
		// Get the current date/time in Central time
		$right_now = new DateTime();
		$right_now->setTimeZone( new DateTimeZone( 'America/Chicago' ) );
		
		// Make sure its in the future
		if ( $event_start_date < $right_now ) {
			return;
		}
			
		// Are we viewing the next meeting?
		$viewing_next_meeting = is_single( $next_meeting->ID );
		
		// If we're not a member and viewing it, don't print this
		if ( ! IS_WEBTIDE_MEMBER && $viewing_next_meeting ) {
			return;
		}
		
		echo $args[ 'before_widget' ];
		
			echo $args[ 'before_title' ] . 'Our Next Meeting' . $args[ 'after_title' ];

			// If we're looking at it
			if ( IS_WEBTIDE_MEMBER && $viewing_next_meeting ) {

				?><p><strong>You're looking at it!</strong><?php

					// What's the difference
					$time_until_event = $right_now->diff( $event_start_date );

					// It's today
					if ( ! $time_until_event->invert && $time_until_event->d <= 0 ) {

						?> Looks like the meeting will start soon. We hope you'll join us!<?php

					}

					// It's within the week
					else if ( ! $time_until_event->invert && $time_until_event->d <= 7 ) {

						?> It's coming up soon. We hope you'll join us!<?php

					}

				?></p><?php

			} else {

				echo ua_webtide_get_next_monthly_meeting_html( array(
					'event_title_element' 	=> 'h4',
					'abbreviate_dt'			=> true,
					'view_details'			=> false,
					));

			}

		echo $args[ 'after_widget' ];

	}

}

class WebTide_Upcoming_Events_Widget extends WP_Widget {

	// Sets up the widgets name etc
	public function __construct() {

		parent::__construct(
			'webtide-upcoming-events-widget',
			'WebTide\'s Upcoming Events',
			array(
				'description' 	=> 'Display\'s a list of upcoming WebTide events.',
				'classname'		=> 'webtide-upcoming-events',
				)
		);

	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		echo $args[ 'before_widget' ];

			echo $args[ 'before_title' ] . 'Upcoming Events' . $args[ 'after_title' ];
			
			// Get the upcoming events
			$upcoming_events = tribe_get_events( array(
				'eventDisplay'   => 'list',
				'posts_per_page' => 2,
				));
				
			if ( ! $upcoming_events ) {
				
				?><p><em>There are no upcoming events.</em></p><?php
					
			} else {
				
				?><ul class="webtide-upcoming-events no-list-style"><?php
				
					foreach( $upcoming_events as $event ) {
						
						// Get event permalink
						$event_permalink = get_permalink( $event->ID );
						
						// Get the speaker info
						$speaker_name = get_post_meta( $event->ID, 'speaker_name', true );
						$speaker_from = get_post_meta( $event->ID, 'speaker_from', true );
						
						?><li class="event">
							<h4 class="meeting-title"><a href="<?php echo $event_permalink; ?>"><?php echo get_the_title( $event->ID ); ?></a></h4>
							<p><?php
								
								// Print speaker name and from
								if ( $speaker_name ) {
									
									?><span class="meeting-speaker"><?php
										echo $speaker_name . ( $speaker_from ? ", <em>{$speaker_from}</em>" : NULL );
									?></span><?php
								
								}
								
								// True = abbreviate stuff
								if ( $event_dt_string = ua_webtide_get_date_time_string( $event, true ) ) {
									
									?><span class="meeting-date-time"><?php echo $event_dt_string; ?></span><?php
									
								}
								
								// Get the venue
								if ( $event_venue = tribe_get_venue() ) {
									
									?><span class="meeting-location"><?php echo $event_venue; ?></span><?php
									
								}
								
								?><a class="meeting-view-details" href="<?php echo $event_permalink; ?>">View details</a>
							</p>
						</li><?php
							
					}
					
				?></ul><?php
				
			}
		
		echo $args[ 'after_widget' ];
		
	}

}

// Get the linkage <a>
function ua_webtide_widgets_get_linkage_a( $instance ) {

	// Linkage defaults
    $instance = wp_parse_args( $instance, array(
        'link_target'	=> NULL,
        'classes'		=> NULL,
    ));
	
	// Get the linkage
	$linkage = ua_webtide_widgets_get_linkage( $instance );
		
	// Set the target
	$linkage_target = isset( $instance[ 'link_target' ] ) && ! empty( $instance[ 'link_target' ] ) ? ' target="' . $instance[ 'link_target' ] . '"' : NULL;
	
	// Set up any classes
	$linkage_classes = isset( $instance[ 'classes' ] ) && ! empty( $instance[ 'classes' ] ) ? ' class="' . $instance[ 'classes' ] . '"' : NULL;
		
	// Return linkage
	if ( $linkage ) {
		return '<a' . $linkage_classes . ' href="' . $linkage . '"' . $linkage_target . '>';
	} else {
		return NULL;
	}

}

// Get the linkage
function ua_webtide_widgets_get_linkage( $instance ) {
	
	// Linkage defaults
    $instance = wp_parse_args( $instance, array(
        'link_media_id'	=> 0,
        'link_page_id'	=> 0,
        'link_url'		=> NULL,
    ));
	
	// If media ID is set
	$linkage = isset( $instance[ 'link_media_id' ] ) && $instance[ 'link_media_id' ] > 0 && ( $link_media_url = wp_get_attachment_url( $instance[ 'link_media_id' ] ) ) ? $link_media_url : NULL;
	
	// If page ID is set and is page with page ID
	if ( isset( $instance[ 'link_page_id' ] ) && is_numeric( $instance[ 'link_page_id' ] ) && $instance[ 'link_page_id' ] > 0 && ( $link_page_url = get_permalink( $instance[ 'link_page_id' ] ) ) ) {
		$linkage = $link_page_url;
	}
		
	// If page ID is set and is post type archive page
	else if ( isset( $instance[ 'link_page_id' ] ) && ! empty( $instance[ 'link_page_id' ] ) && post_type_exists( $instance[ 'link_page_id' ] ) && ( $link_page_url = get_post_type_archive_link( $instance[ 'link_page_id' ] ) ) ) {
		$linkage = $link_page_url;
	}
	
	// If URL is set
	if ( isset( $instance[ 'link_url' ] ) && ! empty( $instance[ 'link_url' ] ) ) {
		$linkage = $instance[ 'link_url' ];
	}
		
	return $linkage;
	
}

// Print form to select linkage
function ua_webtide_widgets_linkage( $widget, $instance ) {

	// Linkage defaults
    $instance = wp_parse_args( $instance, array(
        'link_media_id'	=> 0,
        'link_page_id'	=> 0,
        'link_url'		=> NULL,
        'link_target'	=> NULL
    ));
		
	// Get all media
	$media_data = get_posts( array(
		'posts_per_page'   => -1,
		'post_type'        => 'attachment',
		'post_status'      => 'inherit'
		));
	
	// Create array of page info
	$all_pages = array();
	
	// Get basic pages
	if ( $pages = get_pages( array(
		'sort_order' => 'ASC',
		'sort_column' => 'post_title',
		'hierarchical' => 1
		)) ) {
		
		foreach( $pages as $page ) {
			$all_pages[] = array( 'ID' => $page->ID, 'title' => $page->post_title );
		}
		
	}
		
	// Add custom post type archive pages
	foreach( get_post_types( array( 'public' => true, '_builtin' => false ), 'objects' ) as $cpt ) {
	
		// If 'has_archive', add to list of pages
		if ( isset( $cpt->has_archive ) && $cpt->has_archive )
			$all_pages[] = array( 'ID' => $cpt->name, 'title' => $cpt->labels->name );
	
	}
	
	?><p>
		<label for="<?php echo $widget->get_field_id( 'link_media_id' ); ?>">Link to Media</label>
		<select class="widefat" id="<?php echo $widget->get_field_id( 'link_media_id' ); ?>" name="<?php echo $widget->get_field_name( 'link_media_id' ); ?>">
			<option value=""></option>
			<?php foreach( $media_data as $med ) { ?>
				<option value="<?php echo $med->ID; ?>"<?php selected( $med->ID, $instance[ 'link_media_id' ] ); ?>><?php echo $med->post_title; ?></option>
			<?php } ?>			
		</select><br /><strong>OR</strong><br /><label for="<?php echo $widget->get_field_id( 'link_page_id' ); ?>">Link to Page</label>
		<select class="widefat" id="<?php echo $widget->get_field_id( 'link_page_id' ); ?>" name="<?php echo $widget->get_field_name( 'link_page_id' ); ?>">
			<option value=""></option>
			<?php foreach( $all_pages as $page ) {
				$page = (object) $page;
				?><option value="<?php echo $page->ID; ?>"<?php selected( $page->ID, $instance[ 'link_page_id' ] ); ?>><?php echo $page->title; ?></option>
			<?php } ?>			
		</select><br /><strong>OR</strong><br /><label for="<?php echo $widget->get_field_id( 'link_url' ); ?>">Link to URL</label> <input class="widefat" id="<?php echo $widget->get_field_id( 'link_url' ); ?>" name="<?php echo $widget->get_field_name( 'link_url' ); ?>" type="text" value="<?php if ( isset( $instance[ 'link_url' ] ) ) { echo esc_attr( $instance[ 'link_url' ] ); } ?>" />
	</p>
	<p>
		<input class="checkbox" type="checkbox" id="<?php echo $widget->get_field_id( 'link_target' ); ?>" name="<?php echo $widget->get_field_name( 'link_target' ); ?>" value="_target"<?php checked( $instance[ 'link_target' ], '_target' ); ?> /> <label for="<?php echo $widget->get_field_id( 'link_target' ); ?>">Open link in new window</label>
	</p><?php

}