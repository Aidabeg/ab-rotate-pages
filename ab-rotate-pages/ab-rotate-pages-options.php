<?php 

add_action( 'admin_menu', 'ab_rotate_pages_menu' );
function ab_rotate_pages_menu() {
	add_options_page('AB Rotate Pages', 'AB Rotate Pages', 'manage_options', 'ab-rotate-pages', 'ab_rotate_pages_options');
}

function ab_rotate_pages_options(){ 
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	if (isset($_POST['ab_rotate_pages_stop'])) {
		$the_post_id = $_POST['ab_rotate_pages_stop'];
		$post_meta_value = get_post_meta( $the_post_id, '_ab_rotate_pages_val',true );
		$post_meta_description = get_post_meta( $the_post_id, '_ab_rotate_pages_desc',true );
		$post_meta_date = get_post_meta( $the_post_id, '_ab_rotate_pages_date', true );
		$end_date = date("Y-m-d H:i:s");
		if ($get_options = get_option('_ab_rotate_pages')) {
			$get_options[] = array(
				$the_post_id,
				$post_meta_value,
				$post_meta_description,
				$post_meta_date,
				$end_date
			);
			update_option('_ab_rotate_pages', $get_options);
		} else {
			add_option('_ab_rotate_pages', array(array(
				$the_post_id,
				$post_meta_value,
				$post_meta_description,
				$post_meta_date,
				$end_date
			)));
		}
		delete_post_meta( $the_post_id, '_ab_rotate_pages_val' );
		delete_post_meta( $the_post_id, '_ab_rotate_pages_show' );
		delete_post_meta( $the_post_id, '_ab_rotate_pages_desc' );
		delete_post_meta( $the_post_id, '_ab_rotate_pages_date' );
	}
	?>
	<div id="ab-rotate-pages-wrap">
		<div class="ab-rotate-pages-title">Ab Rotate Pages</div>
		<div class="ab-rotate-pages-links">
			<ul>
				<li>
					<b>Options</b>
				</li>
				<li>
					<a href="<?=menu_page_url('ab-rotate-pages-history', false)?>">History</a>
				</li>
			</ul>
		</div>

		<div class="ab-rotate-pages-table">
			<table>
				<tr>
					<th>Original Page ID</th>
					<th>Variation Page ID</th>
					<th>Description</th>
					<th>Date Start</th>
					<th>Date End</th>
					<th></th>
				</tr>
				<?php $no_results = false;
				$args = array(
					'numberposts' => -1,
					'meta_key'    => '_ab_rotate_pages_val',
					'post_type'   => 'any'
				);
				if ($get_posts = get_posts( $args )) { 
					foreach ($get_posts as $get_post) {
						$post_id = $get_post->ID;
						$post_link = get_permalink($post_id);
						$description = get_post_meta($post_id, '_ab_rotate_pages_desc', true);
						$rotate_posts_ids = get_post_meta($post_id, '_ab_rotate_pages_val', true);
						$rotate_posts_ids = array_filter(explode(',', $rotate_posts_ids));
						$rotate_post_links = [];
						foreach ($rotate_posts_ids as $rotate_post_id) {
							$rotate_post_link = get_permalink($rotate_post_id);
							$rotate_post_links[$rotate_post_id] = $rotate_post_link;
						}
						$post_meta_date = get_post_meta($post_id, '_ab_rotate_pages_date', true);
						if ($post_meta_date) {
							$post_meta_date = date('y-m-d H:i', strtotime($post_meta_date));
						} ?>
						<tr>
							<td class="orig-post-id"><a href="<?=$post_link?>" target="_blank"><?=$post_id?></a></td>
							<td class="rotate-post-id">
								<?php foreach($rotate_post_links as $rotate_post_id => $rotate_post_link) { ?>
								<a href="<?=$rotate_post_link?>" target="_blank"><?=$rotate_post_id?></a> 
								<?php } ?>
							</td>
							<td class="description"><?=$description?></td>
							<td><?=$post_meta_date?></td>
							<td class="activity">active</td>
							<td>
								<form method="POST">
									<button type="submit" id="ab-rotate-pages-stop" name="ab_rotate_pages_stop" value="<?=$post_id?>">Stop</button>
								</form>
							</td>
						</tr>
					<?php }
				} else { ?>
					<tr>
						<td colspan="6">No pages set as variations.</td>
					</tr>
				<?php } ?>
			</table>
		</div>
	</div> <?php 
}

