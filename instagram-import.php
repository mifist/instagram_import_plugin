<?php
/*
 *	Plugin Name: Instagram Import
 *	Description: Imports Instagram photos as posts to your WordPress site
 *	Version: 2.0
 *
*/
require_once('inc/posts-thumbs-on-admin-col.php');
//require_once('artist-posts.php');
//require_once('releases-posts.php');
//require_once('insta-big-posts.php');
//require_once('insta-small-posts.php');
require_once('inc/InstagramImportClass.php');


require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';