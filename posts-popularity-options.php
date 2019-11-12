<?php

add_action( 'wp_head', 'popular_posts_process' );
function popular_posts_process() {
	if ( is_singular( 'post' ) ) {
		$post_id = get_the_ID();
		if ( !isset( $_COOKIE['post_view_' . $post_id] ) ) { 			// If COOKIE doesn't set
			set_cookie_by_post( 'post_view_' . $post_id, true, 30 ); 	// Set COOKIE to current post 
			set_post_views( $post_id ); 								// Set meta field to current post
		}

		$views_option = get_option( 'posts_option' );
	}
}

function set_cookie_by_post( $post_id, $attr, $days_to_expire ) {
	?>

	<script>
	  	var d = new Date();
	  	d.setTime(d.getTime() + (<?= $days_to_expire ?> * 24 * 60 * 60 * 1000));
	  	var expires = "expires="+ d.toUTCString();
	  	document.cookie = "<?= $post_id ?>" + "=" + "<?= $attr ?>" + ";" + expires + ";path=/";
	</script>

	<?php
}

function set_post_views( $post_id ) {
	$count_key = 'post_views_count';
	$count = get_post_meta( $post_id, $count_key, true );
	if ( $count == '' ) {
		$count = 1;
		delete_post_meta( $post_id, $count_key );
		add_post_meta( $post_id, $count_key, $count );
	} else {
		$count++;
		update_post_meta( $post_id, $count_key, $count );
	}
}

function get_post_views( $post_id ) {
    $count_key = 'post_views_count';
    $count = get_post_meta( $post_id, $count_key, true );
    if ( $count == '' ) {
        return '0' . __( 'View' );
    }
    return $count . __( 'Views' );
}