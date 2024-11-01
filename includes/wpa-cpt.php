<?php
/*
///////////////////////////////////////////////
This function adds the slider 
custom post type
\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
*/
add_action('init', 'wpa_cpt');
function wpa_cpt() {
	$labels = array(
		'name' => _x('WPA Slides', 'post type general name', 'wp-accordion', 'wp-accordion'),
		'singular_name' => _x('WPA Slide', 'post type singular name', 'wp-accordion'),
		'add_new' => _x('Add New', 'slide', 'wp-accordion'),
		'add_new_item' => __('Add New WPA Slide', 'wp-accordion'),
		'edit_item' => __('Edit WPA Slide', 'wp-accordion'),
		'new_item' => __('New WPA Slide', 'wp-accordion'),
		'view_item' => __('View WPA Slide', 'wp-accordion'),
		'search_items' => __('Search WPA Slides', 'wp-accordion'),
		'not_found' =>  __('No WPA slides found', 'wp-accordion'),
		'not_found_in_trash' => __('No WPA slides found in Trash', 'wp-accordion'), 
		'parent_item_colon' => '',
		'menu_name' => 'WPA Slides'
		);
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true, 
		'show_in_menu' => 'upload.php',
		'query_var' => true,
		'rewrite' => array('slides'),
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_icon' => WP_ACCORDION_URL .'/images/slides.png',
		'menu_position' => null,
		'supports' => array('title','editor','thumbnail'),
		'register_meta_box_cb' => 'add_wpa_slide_metabox'
		); 
	register_post_type('wpa_slides',$args);
}

add_filter('gettext', 'wpa_slide_title', 10, 4);
function wpa_slide_title( $translation, $text, $domain) {
	global $post;

	if ( ! isset( $post->post_type ) ) {
		return $translation;
	}

	$translations = &get_translations_for_domain($domain);
	$translation_array = array();
 
	switch ($post->post_type) {
		case 'wpa_slides': // enter your post type name here
			$translation_array = array(
				'Enter title here' => 'Enter title here (does not appear)'
			);
			break;
	}
 
	if (array_key_exists($text, $translation_array)) {
		return $translations->translate($translation_array[$text]);
	}
	return $translation;
}

function add_wpa_slide_metabox() {
	add_meta_box('wpa_caption_box' , 'Slide Details' , 'wpa_caption_box' , 'wpa_slides' , 'normal' , 'default' );
}

function wpa_caption_box() {
	global $post;
	
	// Noncename needed to verify where the data originated
    echo '<input type="hidden" name="wpa_noncename" id="wpa_noncename" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	
	// Get the location data if its already been entered
    $wpa_caption = get_post_meta($post->ID, '_wpa_caption', true);
	$wpa_caption = ($wpa_caption) ? $wpa_caption : 'Enter Caption Here';
	
	$wpa_image_link = get_post_meta($post->ID, '_wpa_img_link', true);
	$wpa_image_link = ($wpa_image_link) ? $wpa_image_link : 'Enter Image Link Here, e.g., http://wpsmith.net';
 
    // Echo out the fields
    echo "<h4>";
	_e( 'Caption' , WPA_DOMAIN );
	echo "</h4>";
	echo '<p><input type="textarea" style="width: 98%;" name="_wpa_caption" value="' . $wpa_caption  . '" class="widefat" onfocus="if (this.value == \'Enter Caption Here\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \'Enter Caption Here\';}" /></p>'; 
	echo "<h4>";
	_e( 'Image Link' , WPA_DOMAIN );
	echo "</h4>";
	echo '<p><input type="textarea" style="width: 98%;" name="_wpa_img_link" value="' . $wpa_image_link  . '" class="widefat" onfocus="if (this.value == \'Enter Image Link Here, e.g., http://wpsmith.net\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \'Enter Image Link Here, e.g., http://wpsmith.net\';}" /></p>'; 
	echo '<p>Enter only the URL</p>';
}

// Save the Metabox Data
add_action('save_post', 'wpa_save_caption_meta', 1, 2); // save the custom fields
function wpa_save_caption_meta($post_id, $post) {
 
    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if ( !wp_verify_nonce( $_POST['wpa_noncename'], plugin_basename(__FILE__) )) {
		return $post->ID;
    }
 
    // Is the user allowed to edit the post or page?
    if ( !current_user_can( 'edit_post', $post->ID ))
        return $post->ID;
 
    // OK, we're authenticated: we need to find and save the data
    // We'll put it into an array to make it easier to loop though.
 
    $caption_meta['_wpa_caption'] = $_POST['_wpa_caption'];
	$caption_meta['_wpa_img_link'] = $_POST['_wpa_img_link'];
 
    // Add values of $caption_meta as custom fields
 
    foreach ($caption_meta as $key => $value) { // Cycle through the $events_meta array!
        if( $post->post_type == 'revision' ) 
			return; // Don't store custom data twice
        $value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
        if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
            update_post_meta($post->ID, $key, $value);
        } else { // If the custom field doesn't have a value
            add_post_meta($post->ID, $key, $value);
        }
        if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
    }
 
}

?>