<?php
//this file is in main folder and it works for me(yourWordpressWebsite.com/yourFile.php)

$path = $_SERVER['DOCUMENT_ROOT'];
include_once $path . '/wp-config.php';
include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';

global $wpdb;

$posts = $wpdb->get_results("
		SELECT p.post_title, p.guid as url, concat('https://www.ourdomain.com','/wp-content/uploads/',pm2.meta_value) AS image
		FROM `$wpdb->posts` AS p
		INNER JOIN `$wpdb->postmeta` AS pm1 ON p.id = pm1.post_id
		INNER JOIN `$wpdb->postmeta` AS pm2 ON pm1.meta_value = pm2.post_id
		AND pm2.meta_key = '_wp_attached_file'
		AND pm1.meta_key = '_thumbnail_id'
		ORDER BY p.id DESC
	");

//print_r($posts);
echo json_encode($posts);
exit();

/*
	SELECT post_title, meta_value
	FROM $wpdb->posts 
	INNER JOIN $wpdb->postmeta on $wpdb->postmeta.post_id = $wpdb->posts.ID
	WHERE post_status = 'publish'
	AND meta_key = '_wp_attached_file'
	AND post_type='product'
*/




?>