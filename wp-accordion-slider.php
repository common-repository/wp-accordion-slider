<?php
/*
Plugin Name: WP Accordion Slider
Plugin URI: http://www.wpsmith.net
Description: This plugin creates an image accordion slider from the images you upload using jQuery. You can upload/delete images via the administration panel, and display the images in your theme by using the <code>wp_accordion();</code> template tag, or you can use the [wp-accordion] shortcode, which will generate all the necessary HTML for outputting the accordion slider.
Version: 1.9b
Author: Travis Smith
Author URI: http://www.wpsmith.net/
License: GPLv2

    Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : PLUGIN AUTHOR EMAIL)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/****************************************
includes
****************************************/
include_once('includes/wp-dropdown-posts.php');
include_once('includes/wp-dropdown-posttypes.php');
include_once('includes/scripts.php');
include_once('includes/wpa-cpt.php');
include_once('includes/wpa-admin-page.php');
include_once('includes/help.php');

register_activation_hook( __FILE__, 'wp_accordion_install' );
function wp_accordion_install() {
	global $wp_accordion_defaults;
	if ( version_compare( get_bloginfo( 'version' ), '3.1', '<' ) ) {
		deactivate_plugins( basename( __FILE__ ) );
	}
}

//Define GLOBALS/CONSTANTS
define( "WP_ACCORDION_URL" , WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)) );
define( "WPA_DOMAIN" , 'wp-accordion-slider' );

/*
///////////////////////////////////////////////
Define Variables' Defaults
\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
*/
$wp_accordion_defaults = apply_filters('wp_accordion_defaults', array(
	'auto_play' => 'true',
	'auto_restart' => 'true',
	'pause' => 'true',
	'caption_delay' => 1,
	'caption_height' => 40,
	'caption_height_closed' => 10,
	'caption_easing' => 'easeOutBounce',
	'nav_key' => 'true',
	'slide_delay' => 4,
	'ul' => 'accordion',
	'div' => 'wp-accordion-slider',
	'img_width' => 680,
	'img_height' => 240,
	'ul_width' => 840,
	'ul_height' => 240,
	'css' => '',
	'thumb_height' => 240,
	'thumb_width' => 680,
	'slide_caption_bg' => 'black-30pct',
	'test_css' => '#wpaa-accordion {display:none;}'
));

//	pull the settings from the db
$wp_accordion_settings = get_option('wp_accordion_settings');
$wp_accordion_images = get_option('wp_accordion_images');

//	fallback
$wp_accordion_settings = wp_parse_args($wp_accordion_settings, $wp_accordion_defaults);

/*
///////////////////////////////////////////////
This section checks for post-thumbnails support
and adds it if missing
\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
*/
if (!current_theme_supports('post-thumbnails')) {
	add_theme_support('post-thumbnails');
}

/*
///////////////////////////////////////////////
This section checks user's role, if admin checks cap.
If cap missing, adds cap.
\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
*/
/*
if ( current_user_can( 'administrator' ) ) {
	if ( ! current_user_can( 'upload_files' ) ) {
		// get the the role object
		$role_object = get_role( 'administrator' );
		
		// add $cap capability to this role object
		$role_object->add_cap( 'upload_files' );

	}
}

function get_current_user_role () {
    global $current_user;
    get_currentuserinfo();
    $user_roles = $current_user->roles;
    $user_role = array_shift($user_roles);
	if ( $user_role )
	    return $user_role;
	else
		return false;
};*/

add_image_size('accordion' , $wp_accordion_settings['img_width'] , $wp_accordion_settings['img_height'] , true);
add_image_size('wpa-slide' ,$wp_accordion_settings['thumb_width'] , $wp_accordion_settings['thumb_height'] , true);
add_image_size('wpa-thumb' , 100 , round((100 * $wp_accordion_settings['img_height']) / $wp_accordion_settings['img_width']) , true );


//	this function registers our settings in the db
add_action('admin_init', 'wp_accordion_register_settings');
function wp_accordion_register_settings() {
	register_setting('wp_accordion_images', 'wp_accordion_images', 'wp_accordion_images_validate');
	register_setting('wp_accordion_settings', 'wp_accordion_settings', 'wp_accordion_settings_validate');
}

//	this function adds the settings page to the Appearance tab
add_action('admin_menu', 'add_wp_accordion_menu');
function add_wp_accordion_menu() {
	add_submenu_page('upload.php', 'WP Accordion Settings', 'WP Accordion', 'upload_files', 'wp-accordion', 'wp_accordion_admin_page');
	add_submenu_page('upload.php', 'Add New WPA Slide', 'Add New Slide', 'upload_files', 'post-new.php?post_type=wpa_slides', '');
}

//	add "Settings" link to plugin page
add_filter('plugin_action_links_' . plugin_basename(__FILE__) , 'wp_accordion_plugin_action_links');
function wp_accordion_plugin_action_links($links) {
	$wp_accordion_settings_link = sprintf( '<a href="%s">%s</a>', admin_url( 'upload.php?page=wp-accordion#settings' ), __('Settings') );
	array_unshift($links, $wp_accordion_settings_link);
	return $links;
}

//this function reorders the Media submenu for logical appearance
add_action('admin_menu' , 'wpa_reorder_submenu');
function wpa_reorder_submenu() {
	global $submenu;
	$upload_menu = array();
	$upload_menu = $submenu['upload.php'];
	/*Default Array ( 
		[5] => Array ( [0] => Library [1] => upload_files [2] => upload.php ) 
		[10] => Array ( [0] => Add New [1] => upload_files [2] => media-new.php ) 
		[11] => Array ( [0] => WPA Slides [1] => edit_posts [2] => edit.php?post_type=wpa_slides [3] => Slides ) 
		[12] => Array ( [0] => WP Accordion [1] => upload_files [2] => wp-accordion [3] => WP Accordion Settings ) 
		[13] => Array ( [0] => Add New Slide [1] => upload_files [2] => post-new.php?post_type=wpa_slides [3] => Add New WPA Slide ) )*/
	$accordion_menu = array();
	$slides_menu = array();
	$add_slides_menu = array();
	foreach ($upload_menu as $key => $submenu_item) {
		if ($submenu_item[0] == 'WPA Slides') {
			$slides_menu = $submenu_item;
			$slides_menu_key = $key;
			unset($submenu['upload.php'][$key]);
		}
		elseif ($submenu_item[0] == 'WP Accordion') {
			$accordion_menu = $submenu_item;
			$accordion_menu_key = $key;
			unset($submenu['upload.php'][$key]);
		}
		elseif ($submenu_item[0] == 'Add New Slide') {
			$add_slides_menu = $submenu_item;
			$add_slides_menu_key = $key;
			unset($submenu['upload.php'][$key]);
		}
	}
	$submenu['upload.php'][$slides_menu_key] = $accordion_menu;
	$submenu['upload.php'][$accordion_menu_key] = $slides_menu;
	$submenu['upload.php'][$add_slides_menu_key] = $add_slides_menu;

}


/*
///////////////////////////////////////////////
this final section generates all the code that
is displayed on the front-end of the WP Theme
\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
*/
function wp_accordion($args = array('echo' => true), $content = null) {
	global $wp_accordion_settings, $wp_accordion_images;
	
	$args = wp_parse_args($args, $wp_accordion_settings);
	$echo = $args['echo'];
	
	$newline = "\n"; // line break
	
	$output = '';
	
	$output .= '<div id="'.$wp_accordion_settings['div'].'">';
	
	$output .= '<ul id="'.$wp_accordion_settings['ul'].'">'.$newline;
	
	$i=0;
	foreach((array)$wp_accordion_images as $image => $data) {
		
		//check to see if $image is a date('YmdHis'); by checking string length
		//poor patch of known bug $wp_accordion_images is saving an image_title in the array
		$length = strlen($image);
		if ($length != 14) 
			continue;
		
		if ( isset($data['disable']) )
			continue;
		$i++;
		$output .= '<li class="slide-'.$i.'">'.$newline;
		$title = isset($data['image_title']) ? $data['image_title'] : '';
		$output .= '<div class="slide_handle"><p class="css-vertical-text">'.$title.'</p><div></div></div>'.$newline;
		
		if ( (isset($data['image_content'])) && ($data['image_content'] == 'wpa-slides')) { //future development, add filters/hooks
			$output .= '<div class="slide_content" style="background:#FFF;">'.$newline;
			
			$post_id = $data['image_slide'];
			$img = '';
			$wpa_image_link = '';
			$wpa_image_link = get_post_meta($post_id, '_wpa_img_link', true);
			
			if ($wpa_image_link)
				$output .= "<a href=$wpa_image_link>";
			
			if (has_post_thumbnail($post_id)) {
				$attr = array(
							'class' => 'attachment-wpa-slide alignleft',
							'alt'=> $title, 
							'title'=> $title);
				//$img = wpa_get_image(array('post_id' => $post_id, 'size' => 'wpa-slide'));
				$img = get_the_post_thumbnail( $post_id, 'wpa-slide', $attr );
			}
			
			if ($img) {
				$output .= $img;
			}
			if ($wpa_image_link)
				$output .= '</a>';
			
			$output .= '<div class="wpa-slide-'.$post_id;			
			if ($img) {
				$output .= ' wpa-slide-content wpa-slide-img';
			} else {
				$output .= ' wpa-slides-content';
			}
			
			$output .= '">';
			$output .= wpa_get_the_content($post_id, 'wpa-slides');
			$output .= '</div><!-- end .wpa-slide-'.$post_id.' -->';
			
			
			$wpa_caption = get_post_meta($post_id, '_wpa_caption', true);
			$output .= '<div class="slide_caption">'.$newline;
			$output .= '<div class="slide_caption_toggle" title="Toggle caption"><div></div></div>'.$newline;
			$output .= wpautop($wpa_caption);
			$output .= '</div><!-- end .slide_caption -->';
			$output .= '</div><!-- end .slide_content -->';
			$output .= $newline;
		}
		else {
			$output .= '<div class="slide_content">';
			
			if($data['image_links_to']) {
				$checkhttp = substr($data['image_links_to'], 0, 4);
				if ($checkhttp != 'http') {
					$id = intval($data['image_links_to']);
					$data['image_links_to'] = get_permalink( $id );
				}
				$output .= '<a href="'.$data['image_links_to'].'">';
			}
			
			$output .= '<img src="'.$data['file_url'].'" width="'.$wp_accordion_settings['img_width'].'" height="'.$wp_accordion_settings['img_height'].'" class="'.$data['id'].'" alt="" />';
			
			if($data['image_links_to'])
				$output .= '</a>';
			
			$output .= $newline;
			
			if ($data['image_content']) {
				$output .= '<div class="slide_caption">'.$newline;
				$output .= '<div class="slide_caption_toggle" title="Toggle caption"><div></div></div>'.$newline;
				
				$id = '';
				if ( (isset($data['post_type'])) && (($data['post_type'] == 'page') || ($data['post_type'] == 'post')) ) {
					if ($data['post_type'] == 'page')
						$id = $data['page_id'];
					elseif ($data['post_type'] == 'post')
						$id = url_to_postid( $data['post_id']);
				}
				else
					$id = url_to_postid( $data['image_links_to'] );
							
				if ( isset($data['image_content']) ) {
					
					//only for pages and posts at this time
					if($data['image_content'] == 'excerpt') {
						$output .= wpa_get_the_excerpt($id);
					}
					//only for pages and posts at this time
					elseif($data['image_content'] == 'content-limit') {
						$output .= wpa_get_the_content_limit($id, $data['image_content_limit']);
					}
					//only for pages and posts at this time
					elseif($data['image_content'] == 'content') {
						$output .= wpautop(wpa_get_the_content($id));
					}
					elseif($data['image_content'] == 'custom-content') {
						$output .= wpautop($data['image_custom_content']);
					}
					
					$output .= $newline;
				}
				
				$output .= '</div><!-- end .slide_caption -->';
				$output .= '</div><!-- end .slide_content -->';
			}
		}
		$output .= '</li>'.$newline;
		
	}
	
	$output .= '</ul></div>'.$newline;
	
	echo apply_filters( 'wp_accordion_output' , $output );
}

// Use shortcodes in text widgets.
add_filter('widget_text', 'do_shortcode');

//	create the shortcode [wp-accordion]
add_shortcode('wp-accordion', 'wp_accordion_shortcode');
function wp_accordion_shortcode($atts) {
	
	// Temp solution, output buffer the echo function.
	ob_start();
	
	wp_accordion();
	
	
	$output = ob_get_clean();
	
	return $output;
	
}

function wpa_get_the_content($post_id)
{
	global $wpdb;
	$result = array();
	$result[] = array( 'post_content' => '' );
	$query = "SELECT post_content FROM $wpdb->posts WHERE ID = $post_id LIMIT 1";
	$result = $wpdb->get_results($query, ARRAY_A);
	return apply_filters('wpa_content' , $result[0]['post_content']);
}

function wpa_get_the_excerpt($post_id)
{
  global $wpdb;
  $query = "SELECT post_excerpt FROM $wpdb->posts WHERE ID = $post_id LIMIT 1";
  $result = $wpdb->get_results($query, ARRAY_A);
  return apply_filters('wpa_excerpt' , $result[0]['post_excerpt']);
}
function wpa_get_the_content_limit($id, $max_char, $more_link_text = '(more...)', $stripteaser = 0) {
	
	$content = wpa_get_the_content($id);

	// Strip tags and shortcodes
	$content = strip_tags(strip_shortcodes($content), apply_filters('wpa_get_the_content_limit_allowedtags', '<script>,<style>,<strong>,<em>'));

	// Inline styles/scripts
	$content = trim(preg_replace('#<(s(cript|tyle)).*?</\1>#si', '', $content));

	// Truncate $content to $max_char
	$content = genesis_truncate_phrase($content, $max_char);

	// More Link?
	if ( $more_link_text ) {
		$link = apply_filters( 'wpa_get_the_content_more_link', sprintf( '%s <a href="%s" class="more-link">%s</a>', g_ent('&hellip;'), get_permalink($id), $more_link_text ) );
		
		$output = sprintf('<p>%s %s</p>', $content, $link);
	}
	else {
		$output = sprintf('<p>%s</p>', $content);
	}

	return apply_filters('wpa_content_limit', $output, $content, $link, $max_char);
	
}


?>