<?php
/*
Plugin Name: MultiDrop
Plugin URI: https://vladi160.com
description: >-
a plugin to create awesomeness and spread joy
Version: 1.2
Author: Mr. Vladi Ivanov
Author URI: https://vladi160.com
License: GPL2
*/

require_once 'v1_site.php';


function v1_delete_site_tables_23434545( $blog_id, $drop )
{
	$v1 = new V1Site();

	$v1->delete_unused_tables_after_site_delition($blog_id);
	//echo '<pre>';var_dump($v1->delete_unused_tables_after_site_delition($blog_id));
	//echo '<pre>'; var_dump($blog_id); var_dump($drop);
	//wp_die( 'aborting delete_blog' );
}

//echo '<pre>';var_dump($v1->delete_unused_tables_after_site_delition(242));exit;
add_action( 'delete_blog', 'v1_delete_site_tables_23434545', 10, 2 );

?>