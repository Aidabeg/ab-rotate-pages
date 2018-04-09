<?php 
/*
 * Plugin Name: Ab Rotate Pages
 * Description: This is a plugin to rotate between two pages.
 * Version: 1.0
 * Author: WebPepper
 * Author URI: https://www.webpepper.ru
 * License: GNU General Public License v3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: ab-rotate-pages

AB Rotate Pages is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.
 
AB Rotate Pages is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with AB Rotate Pages. If not, see http://www.gnu.org/licenses/gpl-3.0.html.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $ab_rotate_pages_db_version;
global $wpdb;
global $table_name;
$ab_rotate_pages_db_version = '1.4';

function ab_rotate_pages_install() {
	global $wpdb;
	global $ab_rotate_pages_db_version;

	if ( get_option( "ab_rotate_pages_db_version" ) != $ab_rotate_pages_db_version ) {
		
		$charset_collate = $wpdb->get_charset_collate();

		update_option( 'ab_rotate_pages_db_version', $ab_rotate_pages_db_version );

		$args = array(
			'numberposts' => -1,
			'meta_key'    => '_ab_rotate_pages_val',
			'post_type'   => 'any'
		);
		delete_post_meta_by_key( '_ab_rotate_pages_val' );
		delete_post_meta_by_key( '_ab_rotate_pages_show' );
		delete_post_meta_by_key( '_ab_rotate_pages_desc' );
		delete_post_meta_by_key( '_ab_rotate_pages_date' );
	}
}

register_activation_hook( __FILE__, 'ab_rotate_pages_install' );

add_action( 'admin_enqueue_scripts', 'ab_rotate_pages_admin_styles' );
function ab_rotate_pages_admin_styles() {
	global $ab_rotate_pages_db_version;
	wp_enqueue_style( 'dispo_style', plugins_url('/css/admin-style.css',__FILE__ ), array(), $ab_rotate_pages_db_version );
}  


require_once plugin_dir_path(__FILE__).'/ab-rotate-pages-options.php';
require_once plugin_dir_path(__FILE__).'/ab-rotate-pages-history.php';
require_once plugin_dir_path(__FILE__).'/ab-rotate-pages-metabox.php';
require_once plugin_dir_path(__FILE__).'/ab-rotate-pages-init.php';
require_once plugin_dir_path(__FILE__).'/ab-rotate-pages-action.php';



