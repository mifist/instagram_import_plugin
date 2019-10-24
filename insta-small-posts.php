<?php

function small_post_init() {
	// create a new taxonomy
	$labels = array(
		'name' => _x('Small Post', 'Post type general name', 'small'),
		'singular_name' => _x('Small Post', 'Post type singular name', 'small'),
		'menu_name' => _x('Small ', 'Admin Menu text', 'small'),
		'name_admin_bar' => _x('Small Post', 'Add New on Toolbar', 'small'),
		'add_new' => __('Add New', 'small'),
		'add_new_item' => __('Add New Post', 'small'),
		'new_item' => __('New Post', 'small'),
		'edit_item' => __('Edit Post', 'small'),
		'view_item' => __('View Post', 'small'),
		'all_items' => __('Small Posts', 'small'),
		'search_items' => __('Search Posts', 'small'),
		'parent_item_colon' => __('Parent Posts:', 'small'),
		'not_found' => __('No posts found.', 'small'),
		'not_found_in_trash' => __('No posts found in Trash.', 'small'),
		'featured_image' => _x('Post Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'small'),
		'set_featured_image' => _x('Set image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'small'),
		'remove_featured_image' => _x('Remove image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'small'),
		'use_featured_image' => _x('Use as image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'small'),
		'archives' => _x('Post archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'small'),
		'insert_into_item' => _x('Insert into', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'small'),
		'uploaded_to_this_item' => _x('Uploaded to this post', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'small'),
		'filter_items_list' => _x('Filter posts list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'small'),
		'items_list_navigation' => _x('Posts list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'small'),
		'items_list' => _x('Posts list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'small'),
	);
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_menu' => 'edit.php?post_type=insta',
		'query_var' => true,
		'rewrite' => array('slug' => 'small'),
		'capability_type' => 'page',
		'has_archive' => true,
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('title', 'author', 'thumbnail',),
	);
	register_post_type('small', $args);
}
add_action( 'init', 'small_post_init' );