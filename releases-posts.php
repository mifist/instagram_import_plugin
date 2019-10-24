<?php
function releases_codex_post_init()
{
    $labels = array(
        'name' => _x('Release', 'Post type general name', 'releases'),
        'singular_name' => _x('Release', 'Post type singular name', 'releases'),
        'menu_name' => _x('Release', 'Admin Menu text', 'releases'),
        'name_admin_bar' => _x('Release', 'Add New on Toolbar', 'releases'),
        'add_new' => __('Add New', 'releases'),
        'add_new_item' => __('Add New Release', 'releases'),
        'new_item' => __('New Release', 'releases'),
        'edit_item' => __('Edit Release', 'releases'),
        'view_item' => __('View Release', 'releases'),
        'all_items' => __('Release', 'releases'),
        'search_items' => __('Release', 'releases'),
        'parent_item_colon' => __('Parent Release:', 'releases'),
        'not_found' => __('No posts found.', 'releases'),
        'not_found_in_trash' => __('No posts found in Trash.', 'releases'),
        'featured_image' => _x('Post Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'releases'),
        'set_featured_image' => _x('Set image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'releases'),
        'remove_featured_image' => _x('Remove image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'releases'),
        'use_featured_image' => _x('Use as image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'releases'),
        'archives' => _x('Release archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'releases'),
        'insert_into_item' => _x('Insert into', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'releases'),
        'uploaded_to_this_item' => _x('Uploaded to this post', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'releases'),
        'filter_items_list' => _x('Filter posts list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'releases'),
        'items_list_navigation' => _x('Posts list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'releases'),
        'items_list' => _x('Release list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'releases'),
    );


    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'releases'),
        'capability_type' => 'page',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'menu_icon'           => 'dashicons-album',
        'supports' => array('title', 'author', 'thumbnail')
    );
    register_post_type('releases', $args);
}

add_action('init', 'releases_codex_post_init');

