<?php

namespace core;

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class InstagramImportClass  {
	/**
	 * @var string
	 */
	public $shortcode, $post_type;
	/**
	 * Api endpoint.
	 *
	 * @var string $endpoint
	 */
	protected $endpoint;
	/**
	 * Api endpoint.
	 *
	 * @var string $endpoint
	 */
	protected $oauth_endpoint;
	
	/**
	 * InstagramImportClass constructor.
	 * @param $settings
	 */
	public function __construct() {
		$this->endpoint = 'https://www.instagramapi.com/v3';
		$this->oauth_endpoint = 'https://www.instagram.com/oauth';
		$this->shortcode = 'instagram_apps_output';
		$this->shortcode_new = 'instagram_apps_output_new';
		$this->post_type = 'insta';
		
		// create post_type
		add_action( 'init', array( $this, 'create_insta_post_types' ) );
		// create settings page
		add_action( 'admin_menu', array( $this, 'add_instagram_apps_settings'));
		add_action( 'admin_menu', array( $this, 'hide_add_new_custom_type'));
		add_action( 'admin_init', array( $this, 'create_option' ) );
		add_shortcode( $this->shortcode, array( $this, 'instagram_apps_output' ) );
		add_shortcode( $this->shortcode_new, array( $this, 'instagram_apps_output_new' ) );
		
		// wp cron
		add_action( 'init', array( $this, 'add_new_knm_cron' ) );
		//add_filter( 'cron_schedules', array($this, 'cron_add_three_days') );
		add_action( 'knm_instagram_auto_fetch_insta', array($this, 'knm_parse_instagram') );
	
		if ( is_admin() ) {
			// custom scripts for wp-admin
			add_action( 'admin_enqueue_scripts', array( $this, 'load_style_script_back' ) );
			
			// meta boxes
			add_action( 'add_meta_boxes', array( $this, 'create_insta_meta_boxes' ) );
			add_action( 'save_post', array( $this, 'save_insta_meta_boxes' ) );
			// custom column
			add_filter('manage_insta_posts_columns', array( $this, 'insta_column' ), 4);
			add_action('admin_head', array( &$this,'insta_column_css' ));
			add_filter('manage_insta_posts_custom_column', array( $this, 'fill_insta_column' ), 5, 2);
		} else {
			add_action( 'wp_enqueue_scripts', array( $this, 'load_style_script_front' ) );
		}
		
		// ajax for back
		add_action( 'wp_ajax_instagram_update', array( $this, 'knm_parse_instagram' ) );
		add_action( 'wp_ajax_nopriv_instagram_update', array( $this, 'knm_parse_instagram' ) );
		
		// test
		add_action( 'wp_ajax_instagram_update_test', array( $this, 'knm_parse_instagram_test' ) );
		add_action( 'wp_ajax_nopriv_instagram_update_test', array( $this, 'knm_parse_instagram_test' ) );
		
		// ajax for shortcodes
		add_action( 'wp_ajax_knm_instagram_load_more', array( $this, 'load_more_insta_posts_shortcode' ) );
		add_action( 'wp_ajax_nopriv_knm_instagram_load_more', array( $this, 'load_more_insta_posts_shortcode' ) );
		
		
	}
	
	
	/**
	 * This is load Scripts and Style for Site
	 */
	public function load_style_script_front() {
		wp_enqueue_style(
			'instagram_apps-css',
			plugin_dir_url( __FILE__ ) . '../assets/style/instagram_apps-style.css',
			array()
			,uniqid(time()),
			'all'
		);
		wp_enqueue_script(
			'instagram_apps-scripts',
			plugin_dir_url( __FILE__ ) . '../assets/scripts/instagram_apps-scripts.js',
			array('jquery')
			,uniqid(time())
		);
		$jp_front = array(
			'nonce' => wp_create_nonce( 'nonce' ),
			'ajaxURL_front' => admin_url( 'admin-ajax.php' ),
		);
		wp_localize_script( 'instagram_apps-scripts', 'instagram_apps_ajax', $jp_front );
	}
	
	/**
	 * This is load Scripts and Style for wp-admin
	 */
	public function load_style_script_back(){
		wp_enqueue_style(
			'instagram_apps-back-css',
			plugin_dir_url( __FILE__ ) . '../assets/style/instagram_apps-style-back.css',
			array()
			,uniqid(time()),
			'all'
		);
		wp_enqueue_script(
			'instagram_apps-back-scripts',
			plugin_dir_url( __FILE__ ) . '../assets/scripts/instagram_apps-scripts-back.js',
			array('jquery')
			,uniqid(time())
		);
		$jp_back = array(
			'nonce' => wp_create_nonce( 'nonce' ),
			'ajaxURL' => admin_url( 'admin-ajax.php' ),
		);
		wp_localize_script( 'instagram_apps-back-scripts', 'instagram_apps_back_ajax', $jp_back );
	}
	
	
	/**
	 * replace my_type with the name of your post type
	 * */
	public function hide_add_new_custom_type() {
		global $submenu;
		// replace my_type with the name of your post type
		unset($submenu['edit.php?post_type=insta'][10]);
	}
	
	/**
	 * Create Post Type for Intagram posts
	 * */
	public function create_insta_post_types() {
		$labels = array(
			'name' => _x('Instagram Post', 'Post type general name', 'insta'),
			'singular_name' => _x('Instagram Post', 'Post type singular name', 'insta'),
			'menu_name' => _x('Instagram ', 'Admin Menu text', 'insta'),
			'name_admin_bar' => _x('Instagram Post', 'Add New on Toolbar', 'insta'),
			'add_new' => __('Add New', 'insta'),
			'add_new_item' => __('Add New Post', 'insta'),
			'new_item' => __('New Post', 'insta'),
			'edit_item' => __('Edit Post', 'insta'),
			'view_item' => __('View Post', 'insta'),
			'all_items' => __('Instagram Posts', 'insta'),
			'search_items' => __('Search Posts', 'insta'),
			'parent_item_colon' => __('Parent Posts:', 'insta'),
			'not_found' => __('No posts found.', 'insta'),
			'not_found_in_trash' => __('No posts found in Trash.', 'insta'),
			'featured_image' => _x('Post Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'insta'),
			'set_featured_image' => _x('Set image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'insta'),
			'remove_featured_image' => _x('Remove image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'insta'),
			'use_featured_image' => _x('Use as image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'insta'),
			'archives' => _x('Post archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'insta'),
			'insert_into_item' => _x('Insert into', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'insta'),
			'uploaded_to_this_item' => _x('Uploaded to this post', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'insta'),
			'filter_items_list' => _x('Filter posts list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'insta'),
			'items_list_navigation' => _x('Posts list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'insta'),
			'items_list' => _x('Posts list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'insta'),
		);
		$icon = plugin_dir_url( __FILE__ ).'../assets/img/instagram.png';
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'query_var' => true,
			'rewrite' => array('slug' => 'insta'),
			'capability_type' => 'page',
			'has_archive' => true,
			'hierarchical' => false,
			'menu_position' => null,
			'menu_icon'           => $icon,
			'supports' => array('title', 'author', 'thumbnail',),
		);
		register_post_type( $this->post_type, $args );
	}
	
	
	/**
	 * Settings page
	 * */
	public function add_instagram_apps_settings() {
		add_submenu_page(
			'edit.php?post_type=insta',
			'Instagram Settings',
			'Settings',
			'manage_options',
			'instagram_settings',
			array( $this, 'render_settings_page')
		);
	}
	public function create_option() {
		//register our settings
		register_setting('insta-settings-group', 'token');
		register_setting('insta-settings-group', 'user_id');
		register_setting('insta-settings-group', 'hashtag');
	}
	public function render_settings_page() {
		echo '<div id="instagram_page">';
		echo '<br /><h1 class="instagram_apps-title">' . get_admin_page_title() . '</h1>';
		?>
		<form id="instagram_options_form" method="post" action="options.php">
			<?php settings_fields('insta-settings-group'); ?>
			<?php do_settings_sections('insta-settings-group'); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Instagram Token</th>
					<td><input type="text" name="token" value="<?php echo esc_attr(get_option('token')); ?>"/></td>
				</tr>
				
				
				<tr valign="top">
					<th scope="row">Hashtags</th>
					<td><input type="text" name="hashtag" value="<?php echo esc_attr(get_option('hashtag')); ?>"></td>
				</tr>
			</table>
			<?php submit_button('Save'); ?>
		</form>
		<?php
			echo '<div id="instagram_update_insta">';
			echo '<h2>'.__('You can update manually Instagram Posts from Instagram Site here:').'</h2>';
			echo '<div id="instagram_update_loader">';
			echo '<div id="instagram_update_time"><input class="update_time" type="text" readonly value="'.date( "d-m-Y H:i:s", $_COOKIE['last_update_instagram'] ).'"></div></div>';
			echo '<button id="btn_instagram_update" class="button button-primary">'.__('Update All Instagram Posts').'</button>';
			echo '<span>'.__('The default instagram posts updates once daily.').'</span>';
			echo '</div>';
			echo '</div>';
		echo '</div>';
	}
	
	/**
	 * Custom Meta Boxes
	 * */
	
	/**
	 * Create meta boxes for post_type -> insta
	 * */
	public function create_insta_meta_boxes() {
		$screens = array( $this->post_type );
		foreach ( $screens as $screen ) {
			add_meta_box(
				'insta_meta_box_unique'
				, __( 'Unique Post Meta Box' )
				, array( &$this, 'render_insta_meta_boxes_callback' )
				, $screen
				, 'normal'
				, 'high'
			); // Add the action block to both post and page screens
		}
		
	}
	
	/**
	 * Render meta boxes for post_type -> insta
	 * */
	public function render_insta_meta_boxes_callback($post) {
		// Add a nonce field, which we will check when saving.
		wp_nonce_field('insta_posts_nonce_box', $_POST['insta_nonce_box'] );
		
		// Get existing data from the database.
		$insta_id_key         = $this->get_insta_meta('_insta_id_key', $post->ID);
		$insta_main_url_key   = $this->get_insta_meta('_insta_main_url_key', $post->ID);
		$insta_hashtag_key    = $this->get_insta_meta('_insta_hashtag_key', $post->ID);
	
		
		// Display the form fields using the received data.
		echo '<div class="field-row"><label for="insta_id_field">'._e( 'Instagram ID' ).'</label> ';
		echo '<input type="text" id="insta_id_field" name="insta_id_field" value="' . $insta_id_key  . '" 
	  placeholder="Event ID"/></div>';

		echo '<div class="field-row"><label for="insta_main_url_field">'._e( 'Instagram Link' ).'</label> ';
		echo '<input type="text" id="insta_main_url_field" name="insta_main_url_field" value="' . $insta_main_url_key  . '" 
	  placeholder="Link from Eventbrite"/></div>';
		
		echo '<div class="field-row"><label for="insta_hashtag_field">'._e( 'HashTag' ).'</label> ';
		echo '<input type="text" id="insta_hashtag_field" name="insta_hashtag_field" value="' . $insta_hashtag_key  . '" 
	  placeholder="Event`s Status"/></div>';
		
		
	}
	
	/**
	 * Save meta boxes for post_type -> insta
	 * */
	public function save_insta_meta_boxes($post_id) {
		if ( ! wp_verify_nonce( $_POST['insta_posts_nonce_box'], $_POST['insta_nonce_box'] ) ) return false; // Check if nonce is installed.
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return false; // If this autosave is not doing anything.
		if ( ! isset ( $_POST['post_type'] ) ) { return false; } // Some other POST request
		if ( ! in_array( $_POST['post_type'], array( $this->post_type ) ) ) { return false; } // Wrong post type.
		
		/* OK, everything is clean, you can save data. */
		$insta_id = $_POST['insta_id_field'] ;
		$insta_main_url = $_POST['insta_main_url_field'] ;
		$insta_hashtag = $_POST['insta_hashtag_field'] ;
		
		// Update the data.
		isset($insta_id) ?  update_post_meta( $post_id, '_insta_id_key', $insta_id ) : false;
		isset($insta_main_url) ?  update_post_meta( $post_id, '_insta_main_url_key', $insta_main_url ) : false;
		isset($insta_hashtag) ?  update_post_meta( $post_id, '_insta_hashtag_key', $insta_hashtag ) : false;
		
		
	}
	
	
	/**
	 * Custom Columns for post_type->insta
	 * */
	
	/**
	 * Create a new column to the post_type -> insta
	 * */
	public function insta_column( $columns ){
		unset($columns['author']);  // delete the column Author
		$num = 2; // after which column under the account to insert new
		$new_columns = array(
			'insta_hashtag' => __('HashTag')
		);
		return array_slice( $columns, 0, $num ) + $new_columns + array_slice( $columns, $num );
	}
	
	/**
	 * Adjust the width of the column via css
	 * */
	public function insta_column_css(){
		if( get_current_screen()->base == 'edit')
			echo '<style type="text/css"> 
				.column-insta_hashtag{ width: 10%; }
			</style>';
	}
	
	/**
	 * Fill the column for insta with dat
	 * wp-admin/includes/class-wp-posts-list-table.php
	 * */
	public function fill_insta_column( $colname ){
		global $post;
		$insta_hashtag = $this->get_insta_meta('_insta_hashtag_key', $post->ID);
		if( $colname === 'insta_hashtag' )
			echo $insta_hashtag ? $insta_hashtag : '-';
		
	}
	
	
	/**
	 * API Connect and GET data
	 * */
	
	/**
	 * This is send a HTTP POST request with curl
	 * @param $apiKey
	 * @param $api_url
	 * @param $json
	 */
	protected function instagram_apps_api_connect(){
		$token = get_option('token');
		// Get photos from Instagram
		$url = 'https://api.instagram.com/v1/users/self/media/recent/?access_token='.$token;
		
		$args = stream_context_create(array(
			'http' =>
				array(
					'timeout' => 2500,
				)
		));
		$insta_result = file_get_contents($url, false, $args);
		$json_feed = json_decode($insta_result);
		setcookie( "last_update_instagram", time(), time()+30*24*60*60 );
		return $json_feed;
	}
	
	protected function instagram_apps_api_connect_test(){
		$token = get_option('token');
		// Get photos from Instagram
		$url = 'https://api.instagram.com/v1/users/self/media/recent/?access_token='.$token;
		
		$args = stream_context_create(array(
			'http' =>
				array(
					'timeout' => 2500,
				)
		));
		$insta_result = file_get_contents($url, false, $args);
		$json_feed = json_decode($insta_result);
		setcookie( "last_update_instagram", time(), time()+30*24*60*60 );
		return $json_feed;
	}
	
	/**
	 * Helper Functions
	 * */
	
	/**
	 * Return true/false if slug empty
	 * @param $insta_post_name (slug)
	 * */
	protected function insta_slug_exists($insta_post_name) {
		global $wpdb;
		if ($wpdb->get_row("SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = '" . $insta_post_name . "'", 'ARRAY_A'))  :
			return true;
		else :
			return false;
		endif;
	}
	
	/**
	 * Return post_id/false if insta ID exist
	 * @param $insta_name (slug)
	 * */
	protected function insta_id_exists($insta_id) {
		global $wpdb;
		if ( $wpdb->get_row("SELECT meta_key FROM {$wpdb->prefix}postmeta WHERE meta_value = '" . $insta_id . "'", 'ARRAY_A') )  :
			return $wpdb->get_row("SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_value = '" . $insta_id . "'");
		else :
			return false;
		endif;
	}
	
	public function get_insta_meta($key, $id='') {
		global $post;
		$id = $id ? $id : $post->ID;
		$insta_meta = get_post_meta( $id, $key, true );
		return $insta_meta;
	}
	
	/**
	 * WP CRON
	 * */
	
	/**
	 * Register the 3 days interval for cron insta
	 * */
	public function cron_add_three_days( $schedules ) {
		$schedules['three_days'] = array(
			'interval' => 60 * 60 * 24 * 3,
			'display' => 'Every 3 days'
		);
		return $schedules;
	}
	
	/**
	 * Adds new cron task for add new or update insta
	 * */
	public function add_new_knm_cron() {
		if( ! wp_next_scheduled( 'knm_instagram_auto_fetch_insta' ) ) {
			wp_schedule_event( time(), 'daily', 'knm_instagram_auto_fetch_insta');
		}
	}
	
	/**
	 * Add function to specified cron hook
	 * */
	public function knm_parse_instagram() {
		$app_instas = $this->instagram_apps_api_connect();
		//var_dump($app_instas->data);
		$tags = preg_replace('/^#/', '', get_option('hashtag'));
		
		foreach ( $app_instas->data as $post ):
			$post_id = $post->id;
			$created_time = $post->created_time;
			$post_title = $post->caption->text ? $post->caption->text : $post->name->html;
			$slug = $post_id ? $post_id : sanitize_title($post_title);
			$post_url = $post->link;
		
			if (!$this->insta_slug_exists($post_id)) {
				foreach ($post->tags as $tag):
					$current_tags = implode(', ', $post->tags);
					if ($tag == $tags):
						$new_post = wp_insert_post( array(
							'post_content' => '',
							'post_date'     => date("Y-m-d H:i:s", $post->created_time),
							'post_date_gmt' => date("Y-m-d H:i:s", $post->created_time),
							'post_title'    => $post_title,
							'post_status'   => 'publish',
							'post_type'     => $this->post_type,
							'post_name'     => $slug
						), true );
						
						// Update the data.
						$post_id ? update_post_meta( $new_post, '_insta_id_key', $post_id ) : false;
						$post_url ? update_post_meta( $new_post, '_insta_main_url_key', $post_url ) : false;
						$current_tags ? update_post_meta( $new_post, '_insta_hashtag_key', $current_tags ) : false;
						
						$url = $post->images->standard_resolution->url;
						$post_id = $new_post;
						$desc = " ";
						
						$img_tag = media_sideload_image($url, $post_id, $desc, 'id');
						
						if (is_wp_error($img_tag)):
							echo $img_tag->get_error_message();
						else :
							set_post_thumbnail($post_id, $img_tag);
						endif;
						
					endif;
				endforeach;
				
			} else {
			
				foreach ($post->tags as $tag):
					$current_tags = implode(', ', $post->tags);
					if ($tag == $tags):
						$existent_post_id = $this->insta_id_exists($post_id)->post_id;
				
						$new_existent_post = wp_update_post( array(
							'post_content'      => '',
							'ID'                => $existent_post_id,
							'post_date'         => date("Y-m-d H:i:s", $post->created_time),
							'post_date_gmt'     => date("Y-m-d H:i:s", $post->created_time),
							'post_title'        => $post_title,
							'post_status'       => 'publish',
							'post_type'         => $this->post_type,
							'post_name'         => $slug,
						), true );
						
						// Update the data.
						$post_id ? update_post_meta( $new_existent_post, '_insta_id_key', $post_id ) : false;
						$post_url ? update_post_meta( $new_existent_post, '_insta_main_url_key', $post_url ) : false;
						$current_tags ? update_post_meta( $new_existent_post, '_insta_hashtag_key', $current_tags ) : false;
						
						$url = $post->images->standard_resolution->url;
						$post_id = $new_existent_post;
						$desc = " ";
						
						$img_tag = media_sideload_image($url, $post_id, $desc, 'id');
						
						if (is_wp_error($img_tag)):
							echo $img_tag->get_error_message();
						else :
							set_post_thumbnail($post_id, $img_tag);
						endif;
					
					endif;
				endforeach;
				
			}
		endforeach;
	
		exit();
	}
	
	public function knm_parse_instagram_test() {
		$app_instas = $this->instagram_apps_api_connect_test();
		var_dump($app_instas->data);
		
		$tags = preg_replace('/^#/', '', get_option('hashtag'));
		
		foreach ( $app_instas->data as $post ):
			$post_id = $post->id;
			$created_time = $post->created_time;
			$post_title = $post->caption->text ? $post->caption->text : $post->name->html;
			$slug = $post_id ? $post_id : sanitize_title($post_title);
			$post_url = $post->link;
			
			if (!$this->insta_slug_exists($post_id)) {
				foreach ($post->tags as $tag):
					$current_tags = implode(', ', $post->tags);
					if ($tag == $tags):
						$new_post = wp_insert_post( array(
							'post_content' => '',
							'post_date'     => date("Y-m-d H:i:s", $post->created_time),
							'post_date_gmt' => date("Y-m-d H:i:s", $post->created_time),
							'post_title'    => $post_title,
							'post_status'   => 'publish',
							'post_type'     => $this->post_type,
							'post_name'     => $slug
						), true );
						
						// Update the data.
						$post_id ? update_post_meta( $new_post, '_insta_id_key', $post_id ) : false;
						$post_url ? update_post_meta( $new_post, '_insta_main_url_key', $post_url ) : false;
						$current_tags ? update_post_meta( $new_post, '_insta_hashtag_key', $current_tags ) : false;
						
						$url = $post->images->standard_resolution->url;
						$post_id = $new_post;
						$desc = " ";
						
						$img_tag = media_sideload_image($url, $post_id, $desc, 'id');
						
						if (is_wp_error($img_tag)):
							echo $img_tag->get_error_message();
						else :
							set_post_thumbnail($post_id, $img_tag);
						endif;
					
					endif;
				endforeach;
				
			} else {
				
				foreach ($post->tags as $tag):
					$current_tags = implode(', ', $post->tags);
					if ($tag == $tags):
						$existent_post_id = $this->insta_id_exists($post_id)->post_id;
						
						$new_existent_post = wp_update_post( array(
							'post_content'      => '',
							'ID'                => $existent_post_id,
							'post_date'         => date("Y-m-d H:i:s", $post->created_time),
							'post_date_gmt'     => date("Y-m-d H:i:s", $post->created_time),
							'post_title'        => $post_title,
							'post_status'       => 'publish',
							'post_type'         => $this->post_type,
							'post_name'         => $slug,
						), true );
						
						// Update the data.
						$post_id ? update_post_meta( $new_existent_post, '_insta_id_key', $post_id ) : false;
						$post_url ? update_post_meta( $new_existent_post, '_insta_main_url_key', $post_url ) : false;
						$current_tags ? update_post_meta( $new_existent_post, '_insta_hashtag_key', $current_tags ) : false;
						
						$url = $post->images->standard_resolution->url;
						$post_id = $new_existent_post;
						$desc = " ";
						
						$img_tag = media_sideload_image($url, $post_id, $desc, 'id');
						
						if (is_wp_error($img_tag)):
							echo $img_tag->get_error_message();
						else :
							set_post_thumbnail($post_id, $img_tag);
						endif;
					
					endif;
				endforeach;
				
			}
		endforeach;
		
		exit();
	}
	
	/**
	 * AJAX/Shortcodes Functions
	 * */
	
	/**
	 * @return string
	 */
	public function load_more_insta_posts_shortcode() {
		$start           = isset( $_POST['count_post'] ) ? $_POST['count_post'] : 0;
		$tags            = isset( $_POST['tags'] ) ? $_POST['tags'] : '';
		$query_args      = array(
			'posts_per_page' => 12,
			'post_type'      => 'insta',
			'order'          => 'DESC',
			'orderby'        => 'date',
			'offset'         => $start + 4,
		
		);
		$instagram       = get_posts( $query_args );
		
		if ( $instagram && !empty( $tags ) ) {
		
			foreach ( $instagram as $post ) :
				$instalink = get_post_meta( $post->ID, '_insta_main_url_key', true );
				$hashtag       = get_post_meta( $post->ID, '_insta_hashtag_key', true );
				$post_hashtags = explode( ', ', $hashtag );
				$thumbnail_url = get_the_post_thumbnail_url( $post->ID );
				foreach ( $post_hashtags as $post_tag ) {
					if ( $post_tag == $tags ) { ?>
						<div id="post-<?php echo esc_attr( $post->ID ); ?>" class="grid grid-item--insta "
						     data-hash="<?php echo $hashtag; ?>">
							<div class="album-thumbnail">
								<?php if ( $instalink ) : ?> <a href="<?php echo esc_attr( $instalink ) ?>" target="_blank">
									<img src="<?php echo $thumbnail_url; ?>" alt="<?php echo $post->post_title; ?>">
									</a><?php
								else: ?> <a href="#" target="_blank">
									<img src="<?php echo $thumbnail_url; ?>" alt="<?php echo $post->post_title; ?>">
									</a><?php
								endif; ?>
							</div>
						</div>
					<?php }
				}
			endforeach;
		}
		exit();
	}
	
	private function get_query_insta_tmpl( $hashtag ) {
		$query_args = array(
			'numberposts'  => 12,
			'post_type'       => 'insta',
			'order'           => 'DESC',
			'orderby'         => 'date',
			'offset'          => 0,
		);
		$instagram = get_posts($query_args);
		if ( $instagram && !empty($hashtag) ) : ?>
			<div id="instagram-container" class="" >
				<div class="instagram-wrapper" data-hashtags="<?php echo $hashtag; ?>">
				<? foreach ($instagram as $post) :
					$instalink = get_post_meta( $post->ID, '_insta_main_url_key', true );
					$hashtags = get_post_meta( $post->ID, '_insta_hashtag_key', true );
					$post_hashtags = explode(', ', $hashtags);
					$thumbnail_url = get_the_post_thumbnail_url( $post->ID );
					foreach ($post_hashtags as $post_tag) :
						if ( $post_tag == $hashtag ) : ?>
							<div id="post-<?php echo esc_attr($post->ID); ?>" class="grid grid-item--insta "
							     data-hash="<?php echo $hashtag; ?>">
								<div class="album-thumbnail" >
									<?php if ($instalink) : ?> <a href="<?php echo esc_attr($instalink) ?>" target="_blank">
										<img src="<?php echo $thumbnail_url; ?>" alt="<?php echo $post->post_title; ?>">
										</a><?php
									else: ?> <a href="#" target="_blank">
										<img src="<?php echo $thumbnail_url; ?>" alt="<?php echo $post->post_title; ?>">
										</a><?php
									endif; ?>
								</div>
							</div>
						<?php endif; ?>
					<?php endforeach;
					endforeach; wp_reset_postdata(); ?>
				</div>
				<a href="#" id="loadMoreShortcode" class="btn"><?php echo __('Load More'); ?></a>
			</div>
		<? endif;
	}
	/**
	 * Output List of all Posts -> [instagram_apps_output]
	 */
	public function instagram_apps_output( $atts, $content = null, $tag ) {
		ob_start();
		$query_atts = shortcode_atts( array(
			'hashtag' => '',
		), $atts );
		$this->get_query_insta_tmpl( $query_atts['hashtag'] );
		$instagram_apps = ob_get_clean();
		return $instagram_apps;
	}
	
	/**
	 * Output List of all Posts -> [instagram_apps_output_new]
	 */
	public function instagram_apps_output_new( $atts, $content = null, $tag ) {
		ob_start();
		$query_atts = shortcode_atts( array(
			'hashtag' => '',
		), $atts );
		$this->get_query_insta_tmpl( $query_atts['hashtag'] );
		$instagram_apps = ob_get_clean();
		return $instagram_apps;
	}
	
	
}
new InstagramImportClass();