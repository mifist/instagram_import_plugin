<?php

function big_post_init() {
	// create a new taxonomy
	$labels = array(
		'name' => _x('Big Post', 'Post type general name', 'big'),
		'singular_name' => _x('Big Post', 'Post type singular name', 'big'),
		'menu_name' => _x('Big ', 'Admin Menu text', 'big'),
		'name_admin_bar' => _x('Big Post', 'Add New on Toolbar', 'big'),
		'add_new' => __('Add New', 'big'),
		'add_new_item' => __('Add New Post', 'big'),
		'new_item' => __('New Post', 'big'),
		'edit_item' => __('Edit Post', 'big'),
		'view_item' => __('View Post', 'big'),
		'all_items' => __('Big Posts', 'big'),
		'search_items' => __('Search Posts', 'big'),
		'parent_item_colon' => __('Parent Posts:', 'big'),
		'not_found' => __('No posts found.', 'big'),
		'not_found_in_trash' => __('No posts found in Trash.', 'big'),
		'featured_image' => _x('Post Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'big'),
		'set_featured_image' => _x('Set image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'big'),
		'remove_featured_image' => _x('Remove image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'big'),
		'use_featured_image' => _x('Use as image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'big'),
		'archives' => _x('Post archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'big'),
		'insert_into_item' => _x('Insert into', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'big'),
		'uploaded_to_this_item' => _x('Uploaded to this post', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'big'),
		'filter_items_list' => _x('Filter posts list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'big'),
		'items_list_navigation' => _x('Posts list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'big'),
		'items_list' => _x('Posts list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'big'),
	);
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_menu' => 'edit.php?post_type=insta',
		'query_var' => true,
		'rewrite' => array('slug' => 'big'),
		'capability_type' => 'page',
		'has_archive' => true,
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('title', 'author', 'thumbnail',),
	);
	register_post_type('big', $args);
}
add_action( 'init', 'big_post_init' );