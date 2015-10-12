<?php
	
// Add Twitter card
add_action( 'wp_head', 'ua_webtide_add_twitter_card' );
function ua_webtide_add_twitter_card() {
	global $post;
	
	// Get our description - use Facebook OG
	$description = isset( $post ) && isset( $post->ID ) ? get_post_meta( $post->ID, '_yoast_wpseo_opengraph-description', true ) : NULL;
	
	// If no OG description, get excerpt
	if ( ! $description && isset( $post->post_excerpt ) ) {
		$description = wp_trim_excerpt($post->post_excerpt);
	}
		
	// Get the image
	$image = isset( $post ) && isset( $post->ID ) ? get_post_meta( $post->ID, '_yoast_wpseo_opengraph-image', true ) : 'https://webtide.ua.edu/wp-content/uploads/wwWebTide-logo-fb-og-w-c.png';
		
	?><meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:site" content="@bamawebtide">
	<meta name="twitter:title" content="<?php echo get_bloginfo( 'name' ); ?>" />
	<meta name="twitter:description" content="<?php echo $description; ?>">
	<meta name="twitter:image:src" content="<?php echo $image; ?>"><?php

}