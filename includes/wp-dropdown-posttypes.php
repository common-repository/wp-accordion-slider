<?php
/**
 * This function creates a dropdown select box,
 * for all custom post types with the option to include
 * pages and posts (but not the other builtins).
 *
 * Version: 1.0
 * Author: Travis Smith
 * URI: http://wpsmith.net
 **/
 
function wp_dropdown_posttypes($args) {
	$defaults = array(
		'pt_args'		=> array (
				'public'   => true,
				'_builtin' => false
			),
		'output'	=> 'objects', // names or objects, note names is the default
		'operator'	=> 'and', // 'and' or 'or'
		'selected' => 0,
		'echo' => 1,
		'show_option_none' => '',
		'show_option_no_change' => '',
		'option_none_value' => '',
		'name' => 'page_id',
		'id' => '',
		'include_builtins' => array('page','post','attachment'), //also mediapage, revision (not public), nav_menu_item (not public)
		'excludes' => '', //array of cpts to exclude
		'add_option' => ''
	); 
	
	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$post_types = get_post_types( $pt_args , $output , $operator ); 
	$output = '';
	
	$name = esc_attr($name);
	
	if ( ! empty($post_types) ) {
		$output = "<select name=\"$name\" id=\"$name\" >\n";
		if ( $show_option_no_change )
			$output .= "<option value=\"-1\">$show_option_no_change</option>";
		if ( $show_option_none )
			$output .= "<option value=\"" . esc_attr($option_none_value) . "\">$show_option_none</option>\n";
		if ( $include_builtins ) {
			foreach ($include_builtins  as $builtin ) {
				$obj = get_post_type_object($builtin);
				if($selected == $obj->name) {	$sel = ' selected = "selected"'; } else { $sel = ''; }
				$output .= '<option class="post-type" value="'.$obj->name.'"'.$sel.'>'.$obj->labels->singular_name.'</option>';
			}
		}
		foreach ($post_types  as $post_type ) {
			if ($excludes) {
				$skip = false;
				foreach ($excludes  as $exclude ) {
					if ($exclude == $post_type->name) {
						$skip = true;
						break;
					}
				}
				if ($skip) break;
			}
			
			if($selected == $post_type->name) {	$sel = ' selected = "selected"'; } else { $sel = ''; }
			$output .= '<option class="post-type" value="'.$post_type->name.'"'.$sel.'>'. $post_type->label. '</option>';
		}
		if ( $add_option ) {
			if($selected == $add_option['value']) {	$sel = ' selected = "selected"'; } else { $sel = ''; }
			$output .= "<option value='".$add_option['value']."'".$sel.">".$add_option['name']."</option>\n";
		}
		$output .= "</select>\n";
	}
	
	$output = apply_filters('wp_dropdown_posttypes', $output);

	//if ( $echo )
		echo $output;

	return $output;
}
?>