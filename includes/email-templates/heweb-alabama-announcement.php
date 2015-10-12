<?php
	
global $post;
	
// This query parameter tells us we're retrieving the page to send in an email
$sending_the_email = isset( $_GET[ 'sending_the_email' ] ) && ( 1 == $_GET[ 'sending_the_email' ] || strcasecmp( 'true', $_GET[ 'sending_the_email' ] ) == 0 ) ? true : false;
	
// Remove filters
remove_all_filters( 'the_title' );
remove_all_filters( 'the_content' );

// Add auto <p> back
add_filter( 'the_content', 'wpautop' );

// Get the permalink
$permalink = get_permalink();

// Set the table width
$table_width = '700';
	
// Colors
$bg_color = '#c5dced'; //'#f2f2f2';
$main_bg_color = '#fff';

// Fonts
$body_font_family = 'Helvetica, Arial, sans-serif';
$body_font_size = '15px';
$body_line_height = '22px';
$body_color = '#000';
$body_a_color = '#900';

$h3_font_size = '18px';
$h3_line_height = '25px';
$h3_margin_top = '20px';
$h3_margin_bottom = '5px';
$h3_color = '#666';
$he_text_transform = 'uppercase';

$h4_font_size = '15px';
$h4_line_height = '22px';
$h4_margin_top = '17px';
$h4_margin_bottom = '5px';

// Elements
$paragraph_margin_bottom = '17px';
$hr_color = '#ddd';

// Banner
$banner_width = $table_width;
$banner_padding = '15px 10px 10px 13px';
$banner_color = '#444'; //'#83081e'

// Header
$header_width = $table_width;
$header_height = '233';

// Main Content
$main_content_padding = '20px 20px 25px 20px';

// What To Do Now
$what_to_do_now_bg_color = '#eee';
$what_to_do_now_color = '#666';
$what_to_do_now_font_size = '14px';
$what_to_do_now_line_height = '23px';
$what_to_do_now_header_font_size = '14px';
$what_to_do_now_header_line_height = '21px';
$what_to_do_now_header_margin = '0 0 2px 0';
$what_to_do_now_padding = '20px 20px 16px 20px';
$what_to_do_now_ul_margin = '0';
$what_to_do_now_bottom_border_color = '#d7d5d5';

// Footer
$footer_bg_color = '#393b3d'; //'#222';//#eee'
$footer_color = '#c2c2c3';
$footer_a_color = $footer_color;
$footer_font_size = '12px';
$footer_line_height = '18px';
$footer_td_padding = '15px 20px 15px 20px';

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		
		?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
				
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><?php
					
				if ( $sending_the_email ) {
					?><meta name="viewport" content="width=device-width, initial-scale=1" /><?php
				} else {
					?><meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"><?php
				}
				
				?><meta property="og:description" content="" />
				<meta property="og:image" content="" />
				<meta property="og:title" content="{template_title}" />
				<meta property="og:url" content="<?php echo $permalink; ?>" />
				
				<title>{template_title}</title>
				<style type="text/css"><?php
					
					// Prepare CSS selectors for html, body
					$html_body_css_string = "background:{$bg_color}; padding:0; margin:0; border:0; font-family:{$body_font_family}; font-size:{$body_font_size}; line-height:{$body_line_height}; color:{$body_color}; text-align:left;";
					
					// If in the act of sending the email, add padding all around
					if ( $sending_the_email ) {
						
						// Add padding all around
						$html_body_css_string .= ' padding:20px;';
					
					// If viewing in browser, add padding to top and bottom	
					} else {
						
						// Add padding to top and bottom
						$html_body_css_string .= ' padding:20px 0;';
						
					}
					
					// Prepare CSS selectors for what to do now
					$what_to_do_now_css_string = "background:none; margin:0; padding:{$what_to_do_now_padding}; border:0; font-size:{$what_to_do_now_font_size}; line-height:{$what_to_do_now_line_height}; color:{$what_to_do_now_color}; text-align:left;";
					
					$what_to_do_now_header_css_string = "font-size:{$what_to_do_now_header_font_size}; line-height:{$what_to_do_now_header_line_height}; color:{$what_to_do_now_color}; text-transform: uppercase; margin:{$what_to_do_now_header_margin}; border:0; padding:0; text-align:left;";
					
					$what_to_do_now_p_css_string = "font-size:{$what_to_do_now_font_size}; line-height:{$what_to_do_now_line_height}; color:{$what_to_do_now_color}; margin:{$what_to_do_now_ul_margin}; text-align:left;";
					
					?>html, body { <?php echo $html_body_css_string; ?> }
					a { color:<?php echo $body_a_color; ?>; text-decoration: underline; cursor:pointer; }
					h3 { font-family:<?php echo $body_font_family; ?>; font-size:<?php echo $h3_font_size; ?>; line-height:<?php echo $h3_line_height; ?>; margin:<?php echo $h3_margin_top; ?> 0 <?php echo $h3_margin_bottom; ?> 0; text-align:left; color:<?php echo $h3_color; ?>; text-transform: <?php echo $he_text_transform; ?>; }
					h4 { font-family:<?php echo $body_font_family; ?>; font-size:<?php echo $h4_font_size; ?>; line-height:<?php echo $h4_line_height; ?>; margin:<?php echo $h4_margin_top; ?> 0 <?php echo $h4_margin_bottom; ?> 0; text-align:left; }
					h3 + h4 { margin-top: 0; }
					p, ul, ol { font-size:<?php echo $body_font_size; ?>; line-height:<?php echo $body_line_height; ?>; margin:0 0 <?php echo $paragraph_margin_bottom; ?> 0; text-align:left; }
					hr { border: 1px solid <?php echo $hr_color; ?>; height: 0; margin: 20px 0; }
					img { margin:0; border:0; padding:0; }
					td { vertical-align: top; }
					td *:last-child { margin-bottom: 0; }
					#heweb-alabama-what-to-do-now { <?php echo $what_to_do_now_css_string; ?> }
					#heweb-alabama-what-to-do-now h4 { <?php echo $what_to_do_now_header_css_string; ?> }
					#heweb-alabama-what-to-do-now p { <?php echo $what_to_do_now_p_css_string; ?> }
					#heweb-alabama-main-content { padding:<?php echo $main_content_padding; ?>; }
					#heweb-alabama-main-content > *:first-child { margin-top:0; padding-top:0; }
					#heweb-alabama-main-content > *:last-child { margin-bottom:0; padding-bottom:0; }
					#heweb-alabama-footer-text { font-family:<?php echo $body_font_family; ?>; font-size:<?php echo $footer_font_size; ?>; line-height:<?php echo $footer_line_height; ?>; color:<?php echo $footer_color; ?>; }
					#heweb-alabama-footer-text a { color:<?php echo $footer_a_color; ?>; text-decoration: underline; cursor:pointer; }
				</style>
					
			</head>
			<body style="<?php echo $html_body_css_string; ?>">
				
				<div itemscopeitemtype="http://schema.org/EmailMessage">
					<div itemprop="publisher" itemscopeitemtype="http://schema.org/Organization">
						<meta itemprop="name" content="HighEdWeb Alabama">
						<link itemprop="url" content="http://al15.highedweb.org/">
					</div>
					<div itemprop="about" itemscopeitemtype="http://schema.org/Thing">
						<meta itemprop="name" content="{email_subject}">
					</div>
				</div>
				
				<center><?php
					
					// Only show the top message if we're sending the email
					if ( $sending_the_email ) {
				
						?><!-- TOP MESSAGE // -->
						<table width="<?php echo $table_width; ?>" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; border-spacing:0; width:<?php echo $table_width; ?>px; margin:0; padding:0; border:0;">
							<tbody>
								<tr>
									<td align="left" valign="bottom" width="<?php echo $banner_width; ?>" style="width:<?php echo $banner_width; ?>px; padding:0 10px 5px 10px; border:0;"><a href="<?php echo $permalink; ?>" target="_blank" title="View this announcement in your browser" style="font-family:<?php echo $body_font_family; ?>; font-size: 13px; line-height: normal; color: #005386; text-decoration: underline;">View this announcement in your browser</a></td>
								</tr>
							</tbody>
						</table><?php
						
					}
				
					?><!-- BEGIN MAIN AREA // -->
					<table border="0" cellpadding="0" cellspacing="0" width="<?php echo $table_width; ?>" style="width:<?php echo $table_width; ?>px; border-collapse:collapse; border-spacing:0; margin:0 auto; padding:0; border:0; background:<?php echo $main_bg_color; ?>;">
						<tbody>
							<tr>
								<td align="left" valign="top" style="padding:0; margin:0; border:0;">
									
									<!-- BANNER // -->
									<table width="<?php echo $table_width; ?>" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; border-spacing:0; width:<?php echo $table_width; ?>px; margin:0; padding:0; border:0;">
										<tbody>
											<tr>
												<td align="left" valign="bottom" width="<?php echo $banner_width; ?>" style="width:<?php echo $banner_width; ?>px; padding:<?php echo $banner_padding; ?>; border:0; color:<?php echo $banner_color; ?>"><h1 style="margin:0; padding:0; color:<?php echo $banner_color; ?>; font-family:<?php echo $body_font_family; ?>; font-weight:normal; font-size:26px; line-height:29px;">HighEdWeb Alabama - June 29-30, 2015</h1></td>
											</tr>
										</tbody>
									</table>
									<!-- END BANNER // -->
									
									<!-- HEADER // -->
									<table width="<?php echo $table_width; ?>" height="<?php echo $header_height; ?>" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; border-spacing:0; width:<?php echo $table_width; ?>px; height:<?php echo $header_height; ?>px; margin:0; padding:0; border:0;">
										<tbody>
											<tr>
												<td align="left" valign="top" width="<?php echo $header_width; ?>" height="<?php echo $header_height; ?>" style="background:none; width:<?php echo $header_width; ?>px; height:<?php echo $header_height; ?>px; padding:0; border:0;"><?php
													
													// Print the image	
													?><a href="http://al15.highedweb.org/" target="_blank" title="Save the date for HighEdWeb Alabama on June 29-30, 2015."><img width="<?php echo $header_width; ?>" height="<?php echo $header_height; ?>" src="http://al15.hewregionals.wpengine.com/wp-content/uploads/sites/10/2015/02/hewebAL-save-the-date-card.jpg" style="display:block; width:<?php echo $header_width; ?>px; height:<?php echo $header_height; ?>px; margin:0; border:0; padding:0;" alt="{email_subject}" /></a>
												</td>
											</tr>
										</tbody>
									</table>
									<!-- END HEADER // -->
									
									<!-- CONTENT // -->
									<table width="<?php echo $table_width; ?>" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; border-spacing:0; width:<?php echo $table_width; ?>px; margin:0; padding:0; border:0;">
										<tbody>
											<tr>
												<td id="heweb-alabama-main-content" align="left" valign="top" style="margin:0; padding:<?php echo $main_content_padding; ?>; border:0; background:none; text-align: left;"><?php
													
													?><h2 style="margin:0 0 15px 0; padding:0; font-family:<?php echo $body_font_family; ?>; font-weight:normal; font-size:22px; line-height:27px; color:<?php echo $body_color; ?>;">Announcing <a href="http://al15.highedweb.org/" target="_blank" style="color:<?php echo $body_a_color; ?>; text-decoration:underline;">HighEdWeb Alabama</a>, a two-day conference in Tuscaloosa, Alabama for all higher education web professionals</h2><?php
													
													the_content();
													
													$social_buttons_td_css_string = 'font-size:15px; line-height:18px; text-align:center; padding:15px 10px;';
													$social_buttons_a_css_string = 'font-size:15px; line-height:18px; color:#fff; text-decoration:underline;';
													
													?><table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; border-spacing:0; width:100%; margin:20px 0 0 0; padding:0; border:0;">
														<tbody>
															<tr>
																<td align="center" valign="middle" style="background:#7b161c; <?php echo $social_buttons_td_css_string; ?>"><img src="http://al15.hewregionals.wpengine.com/wp-content/uploads/sites/10/2015/02/calendar-icon.png" style="display:inline; width:auto; height:18px; margin:0 7px 0 0; vertical-align:middle" /><a href="http://lanyrd.com/2015/hewebal/save-to-calendar/" target="_blank" style="<?php echo $social_buttons_a_css_string; ?>">Save the date</a></td>
																<?php /*<td align="center" valign="middle" style="background:#7b161c; <?php echo $social_buttons_td_css_string; ?>"><img src="http://al15.hewregionals.wpengine.com/wp-content/uploads/sites/10/2015/02/email-icon.png" style="display:inline; width:auto; height:15px; margin:0 7px 0 0; vertical-align:middle" /><a href="http://al15.highedweb.org/" target="_blank" style="<?php echo $social_buttons_a_css_string; ?>">Subscribe to our <strong>mailing list</strong></a></td>*/ ?>
																<td align="center" valign="middle" style="background:#5ea2d1; border-left:5px solid #fff; <?php echo $social_buttons_td_css_string; ?>"><img src="http://al15.hewregionals.wpengine.com/wp-content/uploads/sites/10/2015/02/twitter-bird-white.png" style="display:inline; width:auto; height:18px; margin:0 7px 0 0; vertical-align:middle" /><a href="https://twitter.com/hewebAL" target="_blank" style="<?php echo $social_buttons_a_css_string; ?>">Follow us <strong>@hewebAL</strong></a></td>
																<td align="center" valign="middle" style="background:#3b5998; border-left:5px solid #fff;<?php echo $social_buttons_td_css_string; ?>"><img src="http://al15.hewregionals.wpengine.com/wp-content/uploads/sites/10/2015/02/facebook-white.png" style="display:inline; width:auto; height:18px; margin:0 7px 0 0; vertical-align:middle" /><a href="https://www.facebook.com/hewebAL" target="_blank" style="<?php echo $social_buttons_a_css_string; ?>">Join us on <strong>Facebook</strong></a></td>
															</tr>
														</tbody>
													</table>
													
												</td>
											</tr>
										</tbody>
									</table>
									<!-- END CONTENT // -->
									
									<!-- WHAT CAN YOU DO NOW // -->
									<table width="<?php echo $table_width; ?>" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; border-spacing:0; width:<?php echo $table_width; ?>px; margin:0; padding:0; border:0; border-bottom:1px solid <?php echo $what_to_do_now_bottom_border_color; ?>; background:<?php echo $what_to_do_now_bg_color; ?>;">
										<tbody>
											<tr>
												<td id="heweb-alabama-what-to-do-now" align="left" valign="top" style="<?php echo $what_to_do_now_css_string; ?>"><?php
													
													$what_to_do_now_items = array(
														'<a href="http://lanyrd.com/2015/hewebal/save-to-calendar/" target="_blank">Save the date</a> to your calendar.',
														//'Want to know when we\'re accepting speaker proposals, when registration is open, or when the schedule has been posted? <a href="http://al15.highedweb.org/" target="_blank">Subscribe to our mailing list</a>',
														'<strong>Help spread the word!</strong> Post about the conference on social media and tell your friends and colleagues.',
														'<a href="http://al15.highedweb.org/get-involved/" target="_blank">Get involved</a> and help plan HighEdWeb Alabama.',
														'Keep up with HighEdWeb Alabama on Twitter by following <a href="https://twitter.com/hewebAL" target="_blank">@hewebAL</a> or using the <a href="https://twitter.com/search?q=hewebAL" target="_blank">#hewebAL</a> hashtag.',
														'Join our community on <a href="https://www.facebook.com/hewebAL" target="_blank">Facebook</a>.',
														);
													
													?><h4 style="<?php echo $what_to_do_now_header_css_string; ?>">What Can You Do Now?</h4>
													<p style="<?php echo $what_to_do_now_p_css_string; ?>"><?php
													
														$item_index = 0;
														foreach( $what_to_do_now_items as $item ) {
															
															if ( $item_index > 0 ) echo '<br />';
															echo "- {$item}";
															$item_index++;
															
														}
													
													?></p>
													
												</td>
											</tr>
										</tbody>
									</table>
									<!-- END WHAT CAN YOU DO NOW // -->
									
									<?php /*<!-- FOOTER // -->
									<table width="<?php echo $table_width; ?>" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; border-spacing:0; width:<?php echo $table_width; ?>px; margin:0; padding:0; border:0; background:<?php echo $footer_bg_color; ?>;">
										<tbody>
											<tr>
												<td id="heweb-alabama-footer-text" align="left" valign="top" style="margin:0; padding:<?php echo $footer_td_padding; ?>; border:0; background:none; color:<?php echo $footer_color; ?>; font-family:<?php echo $body_font_family; ?>; font-size:<?php echo $footer_font_size; ?>; line-height:<?php echo $footer_line_height; ?>; text-align: left;"><em>You are receiving this email because you are considered a higher education web professional living in the Southeast region of the United States. We do not want to spam you so this will be our only communication.</em></td>
											</tr>
										</tbody>
									</table>
									<!-- END FOOTER // -->
									
									<!-- SECOND FOOTER // -->
									<table width="<?php echo $table_width; ?>" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; border-spacing:0; width:<?php echo $table_width; ?>px; margin:0; padding:0; border:0; border-top:2px solid #fff; background:#1d3e5a;">
										<tbody>
											<tr>
												<td id="heweb-alabama-footer-text" align="left" valign="top" style="margin:0; padding:<?php echo $footer_td_padding; ?>; border:0; background:none; color:#fff; font-family:<?php echo $body_font_family; ?>; font-size:14px; line-height:20px; text-align: left;"><em>If you'd like to continue to receive information about this conference, please <a href="http://al15.highedweb.org/" style="color:#fff;" target="_blank">subscribe to our mailing list</a>.</strong></em></td>
											</tr>
										</tbody>
									</table>
									<!-- END SECOND FOOTER // -->*/ ?>
									
								</td>
							</tr>
						</tbody>
					</table>
					<!-- END MAIN AREA // -->
				
				</center>
				
			</body>
		</html><?php
			
	endwhile;
endif;