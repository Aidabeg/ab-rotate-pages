<?php
add_action( 'admin_menu', 'ab_rotate_pages_history_menu' );

function ab_rotate_pages_history_menu() {
	add_submenu_page( 'ab-rotate-pages','AB Rotate Pages history', 'AB Rotate Pages history', 'manage_options', 'ab-rotate-pages-history', 'ab_rotate_pages_history' );
}

function ab_rotate_pages_history() {
	global $wpdb;
	global $table_name;
	global $table_weeks;

	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	if (isset($_POST['ab_rotate_pages_history_remove'])) {
		$option_key = $_POST['ab_rotate_pages_history_remove'];
		$get_options = get_option('_ab_rotate_pages');
		$get_options = array_reverse($get_options);
		foreach ($get_options as $key => $value) {
			if ($key == $option_key) {
				unset($get_options[$key]);
			}
			$get_options = array_reverse($get_options);
			if ($get_options) {
				update_option('_ab_rotate_pages', $get_options);
			} else {
				delete_option('_ab_rotate_pages');
			}
		}
	}
	?>
	<div id="ab-rotate-pages-wrap">
		<div class="ab-rotate-pages-title">AB Rotate Pages history</div>
		<div class="ab-rotate-pages-links">
			<ul>
				<li>
					<a href="<?=menu_page_url('ab-rotate-pages', false)?>">Options</a>
				</li>
				<li>
					<b>History</b>
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
				if ($get_options = get_option('_ab_rotate_pages')) { 
					$get_options = array_reverse($get_options);
					foreach ($get_options as $key => $get_option) {
						$post_id = $get_option[0];
						$post_link = get_permalink($post_id);
						$rotate_posts_ids = array_filter(explode(',', $get_option[1]));
						$rotate_post_links = [];
						foreach ($rotate_posts_ids as $rotate_post_id) {
							$rotate_post_link = get_permalink($rotate_post_id);
							$rotate_post_links[$rotate_post_id] = $rotate_post_link;
						}
						$description = $get_option[2];
						$date_start = $get_option[3];
						$date_end = $get_option[4];
						if ($date_start) {
							$date_start = date('y-m-d H:i', strtotime($date_start));
						}
						if ($date_end) {
							$date_end = date('y-m-d H:i', strtotime($date_end));
						} ?>
						<tr>
							<td class="orig-post-id"><a href="<?=$post_link?>" target="_blank"><?=$post_id?></a></td>
							<td class="rotate-post-id">
								<?php foreach($rotate_post_links as $rotate_post_id => $rotate_post_link) { ?>
								<a href="<?=$rotate_post_link?>" target="_blank"><?=$rotate_post_id?></a> 
								<?php } ?>
							</td>
							<td><?=$description?></td>
							<td><?=$date_start?></td>
							<td><?=$date_end?></td>
							<td>
								<form method="POST">
									<button type="submit" id="ab-rotate-pages-history-remove" name="ab_rotate_pages_history_remove" value="<?=$key?>">Remove</button>
								</form>
							</td>
						</tr>
					<?php }
				} else { ?>
					<tr>
						<td colspan="6">No variations in history yet.</td>
					</tr>
				<?php } ?>
			</table>
		</div>
	</div>
	<?php
	
}


