<?php
	
// Print WebTide member stats
add_shortcode( 'print_webtide_member_stats', 'get_webtide_member_stats' );
function get_webtide_member_stats( $args ) {

	// Store some data
	$total_members = 71;
	$no_of_sessions = 17;

	// Build content
	$content = '<p>We\'re working on some fun infographic-type charts but, while they\'re being finished, we still wanted to show you some stats about WebTide!</p>';

	$content .= '<ul class="ua-webtide-member-stats">
		<li>WebTide has ' . $total_members . ' members</li>
		<li>WebTide has had ' . $no_of_sessions  . ' monthly meetings on the following topics:
			<ol>
				<li><a href="https://webtide.ua.edu/event/a-different-perspective/">A Different Perspective: I Am A Higher Education Professional Who Is Also A Web Professional</a></li>
				<li><a href="https://webtide.ua.edu/event/accessibility/">Accessibility (2014)</a></li>
				<li><a href="https://webtide.ua.edu/event/accessibility-in-higher-ed-and-at-ua/">Accessibility in Higher Ed and at UA (2015)</a></li>
				<li><a href="https://webtide.ua.edu/event/alternatives-to-a-traditional-cms/">Alternatives to a Traditional CMS</a></li>
				<li><a href="https://webtide.ua.edu/event/caps/">Center for Advanced Public Safety</a></li>
				<li><a href="https://webtide.ua.edu/event/crisis-communication-at-ua/">Crisis Communication at UA</a></li>
				<li><a href="https://webtide.ua.edu/event/financial-affairs-information-technology/">Financial Affairs: Information Technology</a></li>
				<li><a href="https://webtide.ua.edu/event/human-at-work/">Human at Work: or How I Learned to Stop Worrying and Get Better at my Job</a></li>
				<li><a href="https://webtide.ua.edu/event/news-and-events-at-ua/">News and Events at UA</a></li>
				<li><a href="https://webtide.ua.edu/event/people-ua-edu/">People.ua.edu</a></li>
				<li><a href="https://webtide.ua.edu/event/security-awareness/">Security Awareness</a></li>
				<li><a href="https://webtide.ua.edu/event/social-media-roundtable/">Social Media (2014)</a></li>
				<li><a href="https://webtide.ua.edu/event/social-media-roundtable-2015/">Social Media (2015)</a></li>
				<li><a href="https://webtide.ua.edu/event/the-role-of-the-web-in-higher-education/">The Role of the Web in Higher Education</a></li>
				<li><a href="https://webtide.ua.edu/event/tools-and-resources-for-your-job/">Tools and Resources For Your Job</a></li>
				<li><a href="https://webtide.ua.edu/event/ua-web-comm/">UA Office of Web Communications</a></li>
				<li><a href="https://webtide.ua.edu/event/video-for-the-web/">Video for the Web</a></li>
			</ol>
		</li>
		<li>When you break WebTide members down by college:
			<ul>
				<li><a href="http://www.as.ua.edu/">Arts and Sciences</a>: 7</li>
				<li><a href="http://continuingstudies.ua.edu/">Continuing Studies</a>: 3</li>
				<li><a href="http://www.cba.ua.edu/">Commerce and Business Administration</a>: 2</li>
				<li><a href="http://www.cis.ua.edu/">Communication and Information Sciences</a>: 2</li>
				<li><a href="http://cchs.ua.edu/">Community Health Sciences</a>: 2</li>
				<li><a href="http://eng.ua.edu/">Engineering</a>: 2</li>
				<li><a href="http://education.ua.edu/">Education</a>: 1</li>
				<li><a href="http://graduate.ua.edu/">Graduate</a>: 1</li>
				<li><a href="http://honors.ua.edu/">Honors</a>: 1</li>
				<li><a href="http://www.law.ua.edu/">Law</a>: 1</li>
				<li><a href="http://www.ches.ua.edu/">Human Environmental Sciences</a>: 0</li>
				<li><a href="http://nursing.ua.edu/">Nursing</a>: 0</li>
				<li><a href="http://socialwork.ua.edu/">Social Work</a>: 0</li>
				<li>Not part of a college: 40</li>
			</ul>
		</li>';

		$departments = array(
			array(
				'label' => 'University Libraries',
				'count' => 8,
			),
			array(
				'label' => 'Office of Information Technology',
				'count' => 7,
				),
			array(
				'label' => 'Office of Educational Technology (Etech)',
				'count' => 5,
				),
			array(
				'label' => 'Office of Web Communications',
				'count' => 4,
				),
			array(
				'label' => 'Center For Instructional Technology',
				'count' => 3,
				),
			array(
				'label' => 'Office of Institutional Research and Assessment',
				'count' => 3,
				),
			array(
				'label' => 'Administrative Services - College of Continuing Studies',
				'count' => 2,
				),
			array(
				'label' => 'Dean\'s Office - College of Arts and Sciences',
				'count' => 2,
			),
			array(
				'label' => 'Financial Affairs Information Technology',
				'count' => 2,
				),
			array(
				'label' => 'Graduate School',
				'count' => 2,
				),
			array(
				'label' => 'Multimedia Services, Center For Instructional Technology',
				'count' => 2,
				),
			array(
				'label' => 'Undergraduate Admissions',
				'count' => 2,
				),
			array(
				'label' => 'Alabama Digital Humanities Center',
				'count' => 1,
				),
			array(
				'label' => 'Alabama Heritage',
				'count' => 1,
			),
			array(
				'label' => 'Alumni Affairs',
				'count' => 1,
				),
			array(
				'label' => 'Assoc VP For Human Resources',
				'count' => 1,
				),
			array(
				'label' => 'Bryant Conference Center',
				'count' => 1,
				),
			array(
				'label' => 'C&Ba Technology Group',
				'count' => 1,
				),
			array(
				'label' => 'CAPS',
				'count' => 1,
				),
			array(
				'label' => 'College of Communication and Information Sciences',
				'count' => 1,
				),
			array(
				'label' => 'College of Community Health Sciences',
				'count' => 1,
				),
			array(
				'label' => 'Distance Education Librarian',
				'count' => 1,
				),
			array(
				'label' => 'Financial Affairs Information Systems Support',
				'count' => 1,
				),
			array(
				'label' => 'Honors College',
				'count' => 1,
				),
			array(
				'label' => 'HR Learning and Development',
				'count' => 1,
				),
			array(
				'label' => 'Marketing - Culverhouse College of Commerce',
				'count' => 1,
				),
			array(
				'label' => 'Office of Student Media',
				'count' => 1,
				),
			array(
				'label' => 'Telecommunication and Film',
				'count' => 1,
				),
			array(
				'label' => 'University of Alabama Museums',
				'count' => 1,
				)
			);

		$content .= '<li>When you break WebTide members down by department:
			<ul>';

				foreach( $departments as $dept ) {

					// Convert to object
					$dept = (object) $dept;

					$content .= "<li>{$dept->label}: {$dept->count}</li>";

				}

			$content .= '</ul>
		</li>';

	// Close the list
	$content .= '</ul>';
	
	return $content;	
	
}
	
// Print a user's gravatar image. Defaults to current user.
add_shortcode( 'print_user_gravatar', 'get_ua_webtide_user_gravatar' );
function get_ua_webtide_user_gravatar( $args ) {
	
	// Set up defaults
	$defaults = array(
		'user_id' 		=> NULL,
		'user_email' 	=> NULL,
		'size'			=> NULL,
	);
	$args = wp_parse_args( $args, $defaults );
	
	// Only for those who are logged in
	if ( ! is_user_logged_in() ) {
		return NULL;
	}
		
	// If an email is passed...
	if ( isset( $args[ 'user_email' ] ) && ! empty( $args[ 'user_email' ] ) ) {
		return get_avatar($args[ 'user_email' ], $args[ 'size' ] );
	}
		
	// If a user ID is passed
	if ( isset( $args[ 'user_id' ] ) && $args[ 'user_id' ] > 0 ) {
		
		// Get user email
		if ( ( $user_info = get_user_by( 'id', $args[ 'user_id' ] ) ) && isset( $user_info->data ) && isset( $user_info->data->user_email ) ) {
			return get_avatar($user_info->data->user_email, $args[ 'size' ]);
		}
			
		return NULL;
		
	}
		
	// Get current user info
	if ( ( $current_user_info = wp_get_current_user() ) && isset( $current_user_info->data ) && isset( $current_user_info->data->user_email ) ) {
		return get_avatar($current_user_info->data->user_email, $args[ 'size' ]);
	}
			
	return NULL;

}

// Print a user's email. Defaults to current user.
add_shortcode( 'print_user_email', 'get_ua_webtide_user_email' );
function get_ua_webtide_user_email( $args ) {
	
	// Set up defaults
	$defaults = array(
		'user_id' => NULL,
		'include_link' => true
	);
	$args = wp_parse_args( $args, $defaults );
	
	// Only for those who are logged in
	if ( ! is_user_logged_in() ) {
		return NULL;
	}
		
	// Will hold the email
	$user_email = NULL;
		
	// If a user ID is passed
	if ( isset( $args[ 'user_id' ] ) && $args[ 'user_id' ] > 0 ) {
		
		// Get user email
		if ( ( $user_info = get_user_by( 'id', $args[ 'user_id' ] ) ) && isset( $user_info->data ) && isset( $user_info->data->user_email ) )
			$user_email = $user_info->data->user_email;
		
	}
		
	// Get current user info
	if ( ( $current_user_info = wp_get_current_user() ) && isset( $current_user_info->data ) && isset( $current_user_info->data->user_email ) )
		$user_email = $current_user_info->data->user_email;
	
	// If we have an email...
	if ( isset( $user_email ) && ! empty( $user_email ) ) {
		
		// Clean it up
		$user_email = antispambot( $user_email );
		
		if ( $args[ 'include_link' ] ) {
			return '<a href="mailto:"' . $user_email . '">' . $user_email . '</a>';
		} else {
			return $user_email;
		}
		
	}
			
	return NULL;
	
}