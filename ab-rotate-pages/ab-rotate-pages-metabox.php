<?php

add_action('add_meta_boxes', 'ab_rotate_pages_add_custom_box');
function ab_rotate_pages_add_custom_box(){
	//$screens = array( 'post', 'page' );
	$screens = array();
	add_meta_box( 'ab_rotate_pages_metabox', 'AB Rotate Pages', 'ab_rotate_pages_meta_box_callback', $screens );
}

function ab_rotate_pages_meta_box_callback( $post, $meta ){
	$screens = $meta['args'];

	wp_nonce_field( plugin_basename(__FILE__), 'ab_rotate_pages_noncename' );

	$ab_rotate_pages_value = get_post_meta($post->ID, '_ab_rotate_pages_val', true);
	$ab_rotate_pages_description = get_post_meta($post->ID, '_ab_rotate_pages_desc', true);

	if ($ab_errors = get_transient( 'ab_error' )) {
		if ( array_filter($ab_errors) ) { ?>
		    <div class="error">
		    	<?php 
		    	foreach($ab_errors as $ab_error) { 
		    		if ($ab_error) { ?>
		        	<p><?php echo $ab_error; ?></p>
		        <?php } 
		    	} ?>
		    </div><?php

		    delete_transient('ab_error');
		}
	}

	$used_as_variation = false;
	$ab_rotate_pages_ids = '';
	$args = array(
		'numberposts' => -1,
		'meta_key'    => '_ab_rotate_pages_val',
		'post_type'   => 'any'
	);
	if ($get_posts = get_posts( $args )) {
		foreach ($get_posts as $get_post) {
			$get_post_meta = get_post_meta($get_post->ID, '_ab_rotate_pages_val', true);
			$ab_rotate_pages_ids = array_filter(explode(',', $get_post_meta));
			foreach ($ab_rotate_pages_ids as $ab_rotate_page_id) {
				if ($ab_rotate_page_id && $ab_rotate_page_id == $post->ID) {
					$used_as_variation = true;
				}
			}
		}
	}
	if ($used_as_variation) {
		echo 'This page is already used as a variation.';
	} else {
		echo '<div class="ab_rotate_pages_field ab_rotate_pages_field_id">
			<label for="ab_rotate_pages_id">Enter page id or ids (with commas):</label> 
			<input type="text" id="ab_rotate_pages_id" name="ab_rotate_pages_value" value="'.$ab_rotate_pages_value.'" />
		</div>';
		echo '<div class="ab_rotate_pages_field ab_rotate_pages_field_desc">
			<label for="ab_rotate_pages_desc">Description:</label> 
			<input type="text" id="ab_rotate_pages_desc" name="ab_rotate_pages_desc" value="'.$ab_rotate_pages_description.'" />
		</div>';
	}
	wp_reset_postdata();
}

add_action( 'save_post', 'ab_rotate_pages_save_postdata' );
function ab_rotate_pages_save_postdata( $post_id ) {
	$ab_error = new WP_Error('ab_error', '');
	
	if ( ! isset( $_POST['ab_rotate_pages_value'] ) )
		return;

	if ( ! wp_verify_nonce( $_POST['ab_rotate_pages_noncename'], plugin_basename(__FILE__) ) )
		return;

	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
		return;

	if( ! current_user_can( 'edit_post', $post_id ) )
		return;

	echo $ab_rotate_pages_value;
	$ab_rotate_pages_value = sanitize_text_field( $_POST['ab_rotate_pages_value'] );
	$ab_rotate_pages_desc = $_POST['ab_rotate_pages_desc'];

	$boolean = true;
	$post_meta_value = get_post_meta( $post_id, '_ab_rotate_pages_val', true );
	if ($post_meta_value == $ab_rotate_pages_value) {
		$boolean = false;
	}
	$ab_rotate_pages_ids = array_filter(explode(',', $ab_rotate_pages_value), 'is_numeric');
	if ($ab_rotate_pages_value && !$ab_rotate_pages_ids) {
		$boolean = false;
	}
	foreach ($ab_rotate_pages_ids as $ab_rotate_pages_id) {
		$args = array(
			'numberposts' => -1,
			'meta_key'    => '_ab_rotate_pages_val',
			'post_type'   => 'any'
		);
		if ($get_posts = get_posts( $args )) {
			foreach ($get_posts as $get_post) {
				if ($get_post->ID != $post_id) {
					if (get_post_meta($get_post->ID, '_ab_rotate_pages_val', true) == $ab_rotate_pages_id) {
						$boolean = false;
						$ab_error->add('ab_error', 'Another page has ID '.$ab_rotate_pages_id);
					}
				}
			}
		}
	
		if ($get_post = get_post( $ab_rotate_pages_id )) {
			if (get_post_meta($ab_rotate_pages_id, '_ab_rotate_pages_val', true)) {
				$boolean = false;
				$ab_error->add('ab_error', 'A page with ID '.$ab_rotate_pages_id.' already has variation itself');
			}
		} else {
			$boolean = false;
			$ab_error->add('ab_error', 'The page with ID '.$ab_rotate_pages_id.' doesn\'t exist');
		}
		if ($ab_rotate_pages_id == $post_id) {
			$boolean = false;
			$ab_error->add('ab_error', 'The current page ID '.$post_id.' can\'t be set as variation');
		}
	}
	
	if ($ab_rotate_pages_value == '' && $boolean) {
		$post_meta_description = get_post_meta( $post_id, '_ab_rotate_pages_desc', true );
		$post_meta_date = get_post_meta( $post_id, '_ab_rotate_pages_date', true );
		$end_date = date("Y-m-d H:i:s");
		if ($post_meta_value) {
			if ($get_options = get_option('_ab_rotate_pages')) {
				$get_options[] = array(
					$post_id,
					$post_meta_value,
					$post_meta_description,
					$post_meta_date,
					$end_date
				);
				update_option('_ab_rotate_pages', $get_options);
			} else {
				add_option('_ab_rotate_pages', array(array(
					$post_id,
					$post_meta_value,
					$post_meta_description,
					$post_meta_date,
					$end_date
				)));
			}
		}
		delete_post_meta( $post_id, '_ab_rotate_pages_val' );
		delete_post_meta( $post_id, '_ab_rotate_pages_show' );
		delete_post_meta( $post_id, '_ab_rotate_pages_desc' );
		delete_post_meta( $post_id, '_ab_rotate_pages_date' );
	} else if ($ab_rotate_pages_value != '' && $boolean) {
		update_post_meta( $post_id, '_ab_rotate_pages_val', $ab_rotate_pages_value );
		update_post_meta( $post_id, '_ab_rotate_pages_show', $post_id );
		update_post_meta( $post_id, '_ab_rotate_pages_desc', $ab_rotate_pages_desc);
		$date = date("Y-m-d H:i:s");
		update_post_meta( $post_id, '_ab_rotate_pages_date', $date);
	}
	if ($ab_error) {
	    set_transient("ab_error", $ab_error->get_error_messages('ab_error'), 45);
	}
}