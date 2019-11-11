<?php
add_shortcode( 'popular_posts', 'popular_posts_shortcode_function' );

function popular_posts_shortcode_function( $atts ){
	global $post;

	$views_option 	= get_option( 'posts_option' );
	$exlude_array 	= get_option( 'exlude_array' );
	$include_array 	= get_option( 'include_array' );
	$cat_option 	= get_option( 'posts_categories' );

	if ( !empty( $views_option ) ) {

		$args = array(
		    'post_type' 		=> 'post',
		    'posts_per_page' 	=> -1
	    );

		if( $views_option == 'all' ) {
			
			if( !empty( $exlude_array ) ) {
				$args['post__not_in'] = $exlude_array;
			}
			
		} elseif( $views_option == 'custom' ) {

			if( !empty( $include_array ) ) {
				$args['post__in'] = $include_array;
			} else {
				return;
			}

		} else {
			return;
		}

		if ( !empty( $cat_option ) ) {
			$args['cat'] = $cat_option;
		}

	    $query_posts = new WP_Query( $args );

	    $posts_populatiry_array = array();

	    if ( $query_posts->have_posts() )
	        while ( $query_posts->have_posts() ) {
	            $query_posts->the_post();

	    		$post_views = get_post_meta( $post->ID, 'post_views_count', true);

	    		$posts_populatiry_array[$post->ID] = (int) $post_views;

	    } else {
	    	return;
	    }
		wp_reset_query();

		return get_top_3_populatiry_posts( $posts_populatiry_array );

	}
}

function get_top_3_populatiry_posts( $posts_populatiry_array ) {

	$top_3_populatiry_posts_array = array();
	$out = '';

	for ( $i = 0; $i <= 2; $i++ ) {

		$max_item_in_array = max( $posts_populatiry_array );
		$result = array_filter( $posts_populatiry_array, function( $v ) use ( $max_item_in_array ) { 
			return $v == $max_item_in_array; 
		} );

		$top_3_populatiry_posts_array[] = key( $result );

		unset( $posts_populatiry_array[key( $result )] );
	}

	if ( $top_3_populatiry_posts_array ) {
		foreach ( $top_3_populatiry_posts_array as $post_id ) {

			if ( $post_id ) {

				// todo: Edit html and styles for posts

				$out .= '<div id="post-' . $post_id . '">
					<a href="' . get_the_permalink( $post_id ) . '">
						<div class="post-title">' . get_the_title( $post_id ) . '</div>';

				if ( $thumbnail_url ) {
					$out .= '<div class="post-thumbnail"><img src="' . wp_get_attachment_url( get_post_thumbnail_id( $post_id ), 'thumbnail' ) . '"></div>';
				}

				$out .= '</a>';

				$out .= get_post_views( $post_id );

				//$out .= '<div class="entry-content">' . apply_filters('the_content', get_post_field('post_content', $post_id)) . '</div>';

				$out .= '</div>';
			}
		}
	}

    return html_entity_decode( $out );
}