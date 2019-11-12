<?php 

add_action( 'admin_menu', function () {
    add_menu_page( 
    	'Popular Posts', 
    	'Popular Posts', 
    	'manage_options', 
    	'popular-posts', 
    	'popular_posts_admin_page_init',
    	'dashicons-images-alt2',
    	6
    );
} );

function popular_posts_admin_page_init() { 

	if ( isset( $_POST['delete'] ) ) {
		if ( get_option( 'exlude_array' ) ) delete_option( 'exlude_array' );
		if ( get_option( 'include_array' ) ) delete_option( 'include_array' );
		if ( get_option( 'posts_option' ) ) delete_option( 'posts_option' );
		if ( get_option( 'posts_categories' ) ) delete_option( 'posts_categories' );
	} else {

		if ( isset( $_POST['posts'] ) ) {
			$views_option = sanitize_text_field( $_POST['posts'] );
		} elseif ( !empty( get_option( 'posts_option' ) ) ) {
			$views_option = get_option( 'posts_option' );
		} else {
			$views_option = '';
		}

		if ( isset( $_POST['submit'] ) ) {
			check_admin_referer( 'popular-posts', '4jQqZ}@v8Sr+He(u' );
			update_option( 'posts_option', $views_option );

			if ( !isset( $_POST['posts_exlude_array'] ) AND get_option( 'exlude_array' ) ) {
				delete_option( 'exlude_array' );
			}

			if ( !isset( $_POST['posts_include_array'] ) AND get_option( 'include_array' ) ) {
				delete_option( 'include_array' );
			}

			if ( !isset( $_POST['cats_include_array'] ) AND get_option( 'posts_categories' ) ) {
				delete_option( 'posts_categories' );
			} elseif ( isset( $_POST['cats_include_array'] ) ) {

				$cat_option = (array) $_POST['cats_include_array'];
				$cat_option = array_map( 'esc_attr', $cat_option );

				update_option( 'posts_categories', $cat_option );
			}
		}

		if ( !empty( $views_option ) ) {

			$exlude_array = isset( $_POST['posts_exlude_array'] ) ? (array) $_POST['posts_exlude_array'] : get_option( 'exlude_array' );
			$exlude_array = array_map( 'esc_attr', $exlude_array );

			if ( $views_option == 'all' ) {
				update_option( 'exlude_array', $exlude_array );
			} 

			$include_array = isset( $_POST['posts_include_array'] ) ? (array) $_POST['posts_include_array'] : get_option( 'include_array' );
			$include_array = array_map( 'esc_attr', $include_array );

			if ( $views_option == 'custom' ) {
				update_option( 'include_array', $include_array );
			}
		}

		$cat_option = !empty( get_option( 'posts_categories' ) ) ? get_option( 'posts_categories' ) : '';
		
	}
	?> 

	<div class="wrap">
		<h2><?= get_admin_page_title() ?></h2>
		<form action="" method="POST">

			<h3><?= __( 'Posts' ) ?></h3>
			<input type="radio" id="all_posts" name="posts" value="all" <?= $views_option == 'all' ? 'checked' : ''; ?>><?= __('All Posts'); ?><br>
			<label for="posts_exlude"><?= __( 'Chose posts to exlude' ); ?></label><br>

			<?php 
			$query_attr = array(
				'post_type' => 'post'
			);
			$query_posts = new WP_Query( $query_attr ); 
			if ( $query_posts->have_posts() ) : ?>

				<div class="posts-wrapper">
					<select id="posts_exlude" multiple name="posts_exlude_array[]" size="<?= $query_posts->found_posts ?>">

						<?php while ( $query_posts->have_posts() ) :
							$query_posts->the_post(); 

							$post_id = get_the_ID();
							$post_slug = get_post_field( 'post_name', get_post() ); 

							if ( $exlude_array ) {
								$check = in_array( $post_id, $exlude_array ) == true ? 'selected' : '';
							} ?>

							<option value="<?= $post_id ?>" <?= $check; ?>><?php the_title(); ?></option>

							<?php $check = '';
						endwhile;
						wp_reset_postdata(); ?>

					</select>
				</div>

			<?php else :
				echo __( 'No posts found' );
			endif; ?>

			<button id="ecl_del"><?= __( 'Clear' ) ?></button><br><hr>

			<input type="radio" id="custom_posts" name="posts" value="custom" <?= $views_option == 'custom' ? 'checked' : ''; ?>><?= __('Custom'); ?><br>
			<label for="posts_include"><?= __( 'Chose posts to include' ); ?></label><br>

			<?php
			if ( $query_posts->have_posts() ) : ?>

				<div class="posts-wrapper">
					<select id="posts_include" multiple name="posts_include_array[]" size="<?= $query_posts->found_posts ?>">

						<?php while ( $query_posts->have_posts() ) :
							$query_posts->the_post(); 

							$post_id = get_the_ID();
							$post_slug = get_post_field( 'post_name', get_post() ); 

							if ( $include_array ) {
								$check = in_array( $post_id, $include_array ) == true ? 'selected' : '';
							} ?>

							<option value="<?= $post_id ?>" <?= $check; ?>><?php the_title(); ?></option>

							<?php $check = '';
						endwhile;
						wp_reset_postdata(); ?>

					</select>
				</div>

			<?php else :
				echo __( 'No posts found' );
			endif; ?>

			<button id="inc_del"><?= __( 'Clear' ) ?></button><br>
			
			<h3><?= __( 'Categories' ) ?></h3>
			<label for="categories"><?= __( 'Chose categories to include' ); ?></label><br>

			<?php
			$categories = get_categories( array(
				'taxonomy'     => 'category',
				'type'         => 'post'
			) );

			if ( $categories ) : ?>

				<div class="categories-wrapper">

					<select id="categories" multiple name="cats_include_array[]" size="<?= count( $categories ) ?>">

						<?php foreach ( $categories as $cat ) :

							if ( $cat_option ) {
								$check = in_array( $cat->term_id, $cat_option ) == true ? 'selected' : '';
							} ?>

							<option value="<?= $cat->term_id ?>" <?= $check; ?>><?= $cat->name; ?></option>

							<?php $check = ''; 
						endforeach; ?>

					</select>
				</div>

			<?php endif; ?>

			<button id="cat_del"><?= __( 'Clear' ) ?></button>

			<?php wp_nonce_field( 'popular-posts', '4jQqZ}@v8Sr+He(u' ); ?>
			<br>
			<br>

			<input type="submit" name="delete" value="Delete Options" class="button button-primary">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?= __( 'Save Changes ') ?>">

		</form>
	</div>

<?php }