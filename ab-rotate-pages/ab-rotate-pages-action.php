<?php

add_action('the_post', 'ab_rotate_pages_function');
function ab_rotate_pages_function() {
	if (is_singular()) {
		global $post;
		$orig_post = '';
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
					if ($ab_rotate_page_id == $post->ID) {
						$orig_post = $get_post;
					}
				}
			}
		}
		if ($orig_post) {
			$post_data = $orig_post;
		} else {
			$post_data = $post;
		}
		$post_id = $post_data->ID;
		$post_meta_value = get_post_meta($post_id, '_ab_rotate_pages_val', true);
		if ($post_meta_value) {
			$post_meta_show = get_post_meta($post_id, '_ab_rotate_pages_show', true);
			$ab_rotate_pages_ids = array_filter(explode(',', $post_meta_value));
			if ($post_data->ID == $post_meta_show) {
				$rotate_post_1 = $post_data;
				$rotate_post_2 = get_post($ab_rotate_pages_ids[0]);
				update_post_meta($post_id, '_ab_rotate_pages_show', $rotate_post_2->ID);
			} else if($post_meta_show == $ab_rotate_pages_ids[count($ab_rotate_pages_ids)-1]) {
				$rotate_post_2 = $post_data;
				$rotate_post_1 = get_post($post_meta_show);
				update_post_meta($post_id, '_ab_rotate_pages_show', $rotate_post_2->ID);
			}
			if ($post_data->ID == $post_meta_show || $post_meta_show == $ab_rotate_pages_ids[count($ab_rotate_pages_ids)-1]) {
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
	        if ($post_data->ID != $post_meta_show && count($ab_rotate_pages_ids) > 1 && $post_meta_show != $ab_rotate_pages_ids[count($ab_rotate_pages_ids)-1]) {
				$rotate_post_1 = '';
				$rotate_post_2 = '';
		        for ($i=0; $i < count($ab_rotate_pages_ids); $i++) { 
		        	if ($ab_rotate_pages_ids[$i] == $post_meta_show) {
		        		$n_i = $i+1;
		        		if ($n_i < count($ab_rotate_pages_ids)) {
		        			$rotate_post_1 = $post_data;
							$rotate_post_2 = get_post($ab_rotate_pages_ids[$n_i]);
							$rotate_post_3 = get_post($post_meta_show);
		        		}
		        	}
		        }
				if ($rotate_post_1 && $rotate_post_2 && $rotate_post_3) {
					update_post_meta($post_id, '_ab_rotate_pages_show', $rotate_post_2->ID);
					wp_update_post( array(
			            'ID' => $rotate_post_1->ID,
			            'post_name' => 'ab-rotate-pages-'.$rotate_post_2->ID
			        ));
			        wp_update_post( array(
			            'ID' => $rotate_post_2->ID,
			            'post_name' => 'ab-rotate-pages-'.$rotate_post_3->ID
			        ));
			        wp_update_post( array(
			            'ID' => $rotate_post_3->ID,
			            'post_name' => 'ab-rotate-pages-'.$rotate_post_1->ID
			        ));
					wp_update_post( array(
			            'ID' => $rotate_post_1->ID,
			            'post_name' => $rotate_post_2->post_name
			        ));
			        wp_update_post( array(
			            'ID' => $rotate_post_2->ID,
			            'post_name' => $rotate_post_3->post_name
			        ));
			        wp_update_post( array(
			            'ID' => $rotate_post_3->ID,
			            'post_name' => $rotate_post_1->post_name
			        ));
			    }
			}
		}
	}
}



