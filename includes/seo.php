<?php

add_filter( 'wpseo_opengraph_desc', 'ua_webtide_wpseo_opengraph_desc', 100 );
function ua_webtide_wpseo_opengraph_desc( $ogdesc ) {

	// We have to custom add it for archives that we store as pages
	if ( is_post_type_archive( 'jobs' ) && ( $desc_meta = get_post_meta( 22, '_yoast_wpseo_opengraph-description', true ) ) ) {
		return $desc_meta;
	}

	return $ogdesc;

}