<?php
function artist_codex_post_init()
{
    $labels = array(
        'name' => _x('Artist', 'Post type general name', 'artist'),
        'singular_name' => _x('Artist', 'Post type singular name', 'artist'),
        'menu_name' => _x('Artist', 'Admin Menu text', 'artist'),
        'name_admin_bar' => _x('Artist', 'Add New on Toolbar', 'artist'),
        'add_new' => __('Add New', 'artist'),
        'add_new_item' => __('Add New Artist', 'artist'),
        'new_item' => __('New Artist', 'artist'),
        'edit_item' => __('Edit Artist', 'artist'),
        'view_item' => __('View Artist', 'artist'),
        'all_items' => __('Artist', 'artist'),
        'search_items' => __('Artist', 'artist'),
        'parent_item_colon' => __('Parent Artist:', 'artist'),
        'not_found' => __('No posts found.', 'artist'),
        'not_found_in_trash' => __('No posts found in Trash.', 'artist'),
        'featured_image' => _x('Post Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'artist'),
        'set_featured_image' => _x('Set image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'artist'),
        'remove_featured_image' => _x('Remove image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'artist'),
        'use_featured_image' => _x('Use as image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'artist'),
        'archives' => _x('Artist archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'artist'),
        'insert_into_item' => _x('Insert into', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'artist'),
        'uploaded_to_this_item' => _x('Uploaded to this post', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'artist'),
        'filter_items_list' => _x('Filter posts list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'artist'),
        'items_list_navigation' => _x('Posts list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'artist'),
        'items_list' => _x('Artist list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'artist'),
    );


    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'artist'),
        'capability_type' => 'page',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'menu_icon' => 'dashicons-microphone',
        'supports' => array('title', 'author', 'thumbnail')
    );
    register_post_type('artist', $args);
}

add_action('init', 'artist_codex_post_init');

add_action( 'init', 'delete_post_type' );
function delete_post_type() {
    unregister_post_type( 'project', 'post');
}