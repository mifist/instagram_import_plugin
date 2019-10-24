<?php

## Adds thumbnails to the entry in the admin table
if(1){
	add_action('init', 'add_post_thumbs_in_post_list_table', 20 );
	function add_post_thumbs_in_post_list_table(){
		// check which records support thumbnails
		$supports = get_theme_support('post-thumbnails');
		$ptype_names = array('insta'); // specifies the types for which the column is needed separately
		// Define the record types automatically
		if( ! isset($ptype_names) ){
			if( $supports === true ){
				$ptype_names = get_post_types(array( 'public' => true ), 'names');
				$ptype_names = array_diff( $ptype_names, array('attachment') );
			}
			// for individual record types
			elseif( is_array($supports) ){
				$ptype_names = $supports[0];
			}
		}
		
		// add filters for all found record types
		foreach( $ptype_names as $ptype ){
			add_filter( "manage_{$ptype}_posts_columns", 'add_thumb_column' );
			add_action( "manage_{$ptype}_posts_custom_column", 'add_thumb_value', 10, 2 );
		}
	}
	
	// add a column
	function add_thumb_column( $columns ){
		// correct the width of the column through css
		add_action('admin_notices', function(){
			echo '
			<style>
				.column-thumbnail{ width:80px; text-align:center; }
			</style>';
		});
		
		$num = 1; // after which column under the account to insert new
		
		$new_columns = array( 'thumbnail' => __('Thumbnail') );
		
		return array_slice( $columns, 0, $num ) + $new_columns + array_slice( $columns, $num );
	}

	// fill the column
	function add_thumb_value( $colname, $post_id ){
		if( 'thumbnail' == $colname ){
			$width  = $height = 45;
			
			// thumbnail
			if( $thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true ) ){
				$thumb = wp_get_attachment_image( $thumbnail_id, array($width, $height), true );
			}
			// from the gallery ...
			elseif( $attachments = get_children( array(
				'post_parent'    => $post_id,
				'post_mime_type' => 'image',
				'post_type'      => 'attachment',
				'numberposts'    => 1,
				'order'          => 'DESC',
			) ) ){
				$attach = array_shift( $attachments );
				$thumb = wp_get_attachment_image( $attach->ID, array($width, $height), true );
			}
			
			echo empty($thumb) ? ' ' : $thumb;
		}
	}
}
