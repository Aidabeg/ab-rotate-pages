<?php

add_action('init', 'ab_rotate_pages_init');
function ab_rotate_pages_init() {
	if (is_admin()) {
		$args = array(
			'numberposts' => -1,
			'meta_key'    => '_ab_rotate_pages_val',
			'post_type'   => 'any'
		);
		$get_posts = get_posts( $args );
		foreach ($get_posts as $get_post) {
			$post_meta_value = '';
			$post_meta_show = '';
			$post_id = $get_post->ID;
			$post_meta_value = get_post_meta($post_id, '_ab_rotate_pages_val', true);
			$post_meta_show = get_post_meta($post_id, '_ab_rotate_pages_show', true);
			if ($post_meta_value && $post_meta_show != $post_id) {
				$rotate_post_1 = $get_post;
				$rotate_post_2 = get_post($post_meta_show);
				update_post_meta($post_id, '_ab_rotate_pages_show', $rotate_post_1->ID);
				wp_update_post( array(
		            'ID' => $rotate_post_1->ID,
		            'post_name' => 'ab-rotate-pages-'.$rotate_post_2->ID
		        ));
		        wp_update_post( array(
		            'ID' => $rotate_post_2->ID,
		            'post_name' => 'ab-rotate-pages-'.$rotate_post_1->ID
		        ));
				wp_update_post( array(
		            'ID' => $rotate_post_1->ID,
		            'post_name' => $rotate_post_2->post_name
		        ));
		        wp_update_post( array(
		            'ID' => $rotate_post_2->ID,
		            'post_name' => $rotate_post_1->post_name
		        ));
			}
		}
	}
}


