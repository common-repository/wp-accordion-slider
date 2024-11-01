<?php

/*
///////////////////////////////////////////////
this function is the code that gets loaded when the
settings page gets loaded by the browser.  It calls 
functions that handle image uploads and image settings
changes, as well as producing the visible page output.
\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
*/
function wp_accordion_admin_page() {
	echo '<div class="wrap">';
	
		//	handle image upload, if necessary
		if( (isset($_REQUEST['action'])) && ($_REQUEST['action'] == 'wp_handle_upload'))
			wp_accordion_handle_upload();
			
		//	handle new slide, if necessary
		if( (isset($_REQUEST['action'])) && ($_REQUEST['action'] == 'wp_handle_newslide'))
			wp_accordion_handle_newslide($_POST['slide_id']);
		
		//	disable an image, if necessary
		if( isset($_REQUEST['disable']) )
			wp_accordion_disable_upload($_REQUEST['disable']);
			
		//	delete an image, if necessary
		if( isset($_REQUEST['delete']) )
			wp_accordion_delete_upload($_REQUEST['delete']);
			
		//	handle reorder, if necessary
		if( isset($_POST['reorder']) )
			wp_accordion_handle_reorder();
				
		//	beta content
		wp_accordion_beta_admin();
		
		//	the image management form
		wp_accordion_images_admin();
		
		//	the settings management form
		wp_accordion_settings_admin();
		
		//	preview
		wp_accordion_preview_admin();

	echo '</div>';
}


/*
///////////////////////////////////////////////
this section handles uploading images, adding
the image data to the database, deleting images,
and deleting image data from the database.
\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
*/
//	this function handles the file upload,
//	resize/crop, and adds the image data to the db
function wp_accordion_handle_upload() {
	global $wp_accordion_settings, $wp_accordion_images;
	
	//	upload the image
	$upload = wp_handle_upload($_FILES['wp_accordion'], 0);
	
	//	extract the $upload array
	extract($upload);
	
	//	the URL of the directory the file was loaded in
	$upload_dir_url = str_replace(basename($file), '', $url);
	
	//	get the image dimensions
	list($width, $height) = getimagesize($file);
	
	//	if the uploaded file is NOT an image
	if(strpos($type, 'image') === FALSE) {
		unlink($file); // delete the file
		echo '<div class="error" id="message"><p>Sorry, but the file you uploaded does not seem to be a valid image. Please try again.</p></div>';
		return;
	}
	
	//	if the image doesn't meet the minimum width/height requirements ...
	if($width < $wp_accordion_settings['img_width'] || $height < $wp_accordion_settings['img_height']) {
		unlink($file); // delete the image
		echo '<div class="error" id="message"><p>Sorry, but this image does not meet the minimum height/width requirements. Please upload another image</p></div>';
		return;
	}
	
	//	if the image is larger than the width/height requirements, then scale it down.
	if($width > $wp_accordion_settings['img_width'] || $height > $wp_accordion_settings['img_height']) {
		//	resize the image
		$resized = image_resize($file, $wp_accordion_settings['img_width'], $wp_accordion_settings['img_height'], true, 'resized');
		$resized_url = $upload_dir_url . basename($resized);
		//	delete the original
		unlink($file);
		$file = $resized;
		$url = $resized_url;
	}
	
	//	make the thumbnail
	$thumb_height = round((100 * $wp_accordion_settings['img_height']) / $wp_accordion_settings['img_width']);
	if(isset($upload['file'])) {
		$thumbnail = image_resize($file, 100, $thumb_height, true, 'thumb');
		$thumbnail_url = $upload_dir_url . basename($thumbnail);
	}
	
	//	use the timestamp as the array key and id
	$time = date('YmdHis'); //############## 14 digits
	
	//	add the image data to the array
	$wp_accordion_images[$time] = array(
		'id' => $time,
		'file' => $file,
		'file_url' => $url,
		'thumbnail' => $thumbnail,
		'thumbnail_url' => $thumbnail_url,
		'image_links_to' => '',
		'post_type' => '',
		'page_id' => '',
		'post_id' => '',
		'image_title' => '',
		'image_content' => '',
		'image_content_limit' => 0,
		'image_custom_content' => '',
		'image_slide' => '',
		'order' => '',
		'custom_slide' => false,
		'disable' => false
	);
	
	//	add the image information to the database
	$wp_accordion_images['update'] = 'Added';
	update_option('wp_accordion_images', $wp_accordion_images);
}

//	this function deletes the image,
//	and removes the image data from the db
function wp_accordion_delete_upload($id) {
	global $wp_accordion_images;
	
	//	if the ID passed to this function is invalid,
	//	halt the process, and don't try to delete.
	if(!isset($wp_accordion_images[$id])) return;
	
	//	delete the image and thumbnail
	if ($wp_accordion_images[$id]['file'])
		unlink($wp_accordion_images[$id]['file']);
	if ($wp_accordion_images[$id]['thumbnail'])
		unlink($wp_accordion_images[$id]['thumbnail']);
	
	//	indicate that the image was deleted
	$wp_accordion_images['update'] = 'Deleted';
	
	//	remove the image data from the db
	unset($wp_accordion_images[$id]);
	update_option('wp_accordion_images', $wp_accordion_images);
}

//	this function deactivates the image
function wp_accordion_disable_upload($id) {
	global $wp_accordion_images;
	
	//	if the ID passed to this function is invalid,
	//	halt the process, and don't try to delete.
	if(!isset($wp_accordion_images[$id])) return;
	
	//toggle activation
	if ($wp_accordion_images[$id]['disable'])
		$wp_accordion_images[$id]['disable'] = false;
	else
		$wp_accordion_images[$id]['disable'] = true;
	
	//	indicate that the image was disabled
	$wp_accordion_images['update'] = 'Disabled';
	
	//	update the image data in the db
	update_option('wp_accordion_images', $wp_accordion_images);
}

/*
///////////////////////////////////////////////
this section handles uploading images, adding
the image data to the database, deleting images,
and deleting image data from the database.
\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
*/
//	this function handles the file upload,
//	resize/crop, and adds the image data to the db
function wp_accordion_handle_newslide($page_id) {
	global $wp_accordion_settings, $wp_accordion_images;
	
	//	use the timestamp as the array key and id
	$time = date('YmdHis');
	
	//get slide thumbnail
	$page_id = ($page_id == 'Enter slide post ID') ? '' : $page_id;
	$post_thumbnail_id = get_post_thumbnail_id( $page_id );
	$post_thumb = wp_get_attachment_image_src( $post_thumbnail_id, 'wpa-thumb');
	
	//	add the image data to the array
	$wp_accordion_images[$time] = array(
		'id' => $time,
		'file' => '',
		'file_url' => '',
		'thumbnail' => '',
		'thumbnail_url' => $post_thumb[0],
		'image_links_to' => get_permalink($page_id),
		'image_slide' => $page_id,
		'image_content' => 'wpa-slides',
		'post_type' => 'wpa_slides',
		'page_id' => '',
		'post_id' => '',
		'image_title' => '',
		'image_content_limit' => 0,
		'image_custom_content' => '',
		'order' => '',
		'custom_slide' => true,
		'disable' => false
	);
	
	//	add the image information to the database
	$wp_accordion_images['update'] = 'Added';
	update_option('wp_accordion_images', $wp_accordion_images);
}

/*
///////////////////////////////////////////////
these two functions check to see if an update
to the data just occurred. if it did, then they
will display a notice, and reset the update option.
\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
*/
//	this function checks to see if we just updated the settings
//	if so, it displays the "updated" message.
function wp_accordion_settings_update_check() {
	global $wp_accordion_settings;
	if(isset($wp_accordion_settings['update'])) {
		echo '<div class="updated fade" id="message"><p>WP Accordion Settings <strong>'.$wp_accordion_settings['update'].'</strong></p></div>';
		unset($wp_accordion_settings['update']);
		update_option('wp_accordion_settings', $wp_accordion_settings);
	}
}
//	this function checks to see if we just added a new image
//	if so, it displays the "updated" message.
function wp_accordion_images_update_check() {
	global $wp_accordion_images;
	if(isset($wp_accordion_images['update'])) {
		if($wp_accordion_images['update'] == 'Added' || $wp_accordion_images['update'] == 'Deleted' || $wp_accordion_images['update'] == 'Updated' || $wp_accordion_images['update'] == 'Reordered') {
			echo '<div class="updated fade" id="message"><p>Image(s) '.$wp_accordion_images['update'].' Successfully</p></div>';
			unset($wp_accordion_images['update']);
			update_option('wp_accordion_images', $wp_accordion_images);
		}
	}
}

/*
///////////////////////////////////////////////
these two functions display the front-end code
on the admin page. it's mostly form markup.
\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
*/
//	display the beta administration code
function wp_accordion_beta_admin() {
 ?>
	<h2><?php _e('WP Accordion Beta Feedback', 'wp_accordion'); ?></h2>
	<p><strong>Thank you</strong> for beta testing WP Accordion Slider. Please email feedback to <a href="mailto:travis@wpsmith.net">travis@wpsmith.net</a>. A Sample custom CSS file can be found at <a href="<?php echo WP_ACCORDION_URL .'/css/sample-accordion.css'; ?>">sample-accordion.css</a>. I welcome any suggestions to my sample CSS file to create a better sample as design is <strong>not</strong> my strongest suit!
	
	One thing you may find is that WordPress may want to upgrade the plugin on your plugins page. Please do not upgrade the plugin.</p>
<?php
}

//	display the images administration code
function wp_accordion_images_admin() {

 ?>
 	<?php global $wp_accordion_images, $order; ?>
	<?php $reorder = wp_accordion_images_order_check(); ?>
	<?php wp_accordion_images_update_check(); ?>
	<?php if ($reorder) $wp_accordion_images = $order; ?>

	<h2><?php _e('WP Accordion Images (beta)', 'wp_accordion'); ?></h2>
	<p><strong>Need help?</strong> Click the <em>Help</em> tab in the upper right hand corner for assistance.</p>

	<table class="form-table">
		<tr valign="top"><th scope="row">Upload New Image</th>
			<td>
			<form enctype="multipart/form-data" method="post" action="?page=wp-accordion">
				<input type="hidden" name="post_id" id="post_id" value="0" />
				<input type="hidden" name="action" id="action" value="wp_handle_upload" />
				
				<label for="wp_accordion">Select a File: </label>
				<input type="file" name="wp_accordion" id="wp_accordion" />
				<input type="submit" class="button-primary" name="html-upload" value="Upload" />
			</form>
			</td>
		</tr>
		<tr valign="top"><th scope="row">Or, Add New Custom Slide</th>
			<td>
			<form enctype="multipart/form-data" method="post" action="?page=wp-accordion">
				<input type="hidden" name="post_id" id="post_id" value="0" />
				<input type="hidden" name="action" id="action" value="wp_handle_newslide" />
				
				<input type="text" name="slide_id" id="slide_id" value="Enter slide post ID" onfocus="if (this.value == 'Enter slide post ID') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Enter slide post ID';}" />				
				<input type="submit" class="button-primary" value="Add Slide" />
			</form>
			</td>
		</tr>
	</table><br />
	
	<?php if(!empty($wp_accordion_images)) : ?>
	
	<table class="widefat fixed" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" class="column-posts">Image</th>
				<th scope="col">Hyperlink/Page ID/Post ID</th>
				<th scope="col">Slide Title</th>
				<th scope="col" style="min-width: 130px;">Slide/Caption Content</th>
				<th scope="col" style="width: 100px;">Order</th>
				<th scope="col" style="min-width: 130px;">Actions</th>
			</tr>
		</thead>
		
		<tfoot>
			<tr>
				<th scope="col" class="column-posts">Image</th>
				<th scope="col">Hyperlink/Page ID/Post ID</th>
				<th scope="col">Slide Title</th>
				<th scope="col">Slide/Caption Content</th>
				<th scope="col">Order</th>
				<th scope="col">Actions</th>
			</tr>
		</tfoot>
		
		<tbody>
		
		<form method="post" action="options.php">
		<?php settings_fields('wp_accordion_images');
		$i=0;
		?>
		<?php foreach((array)$wp_accordion_images as $image => $data) : ?>
			<?php 
			
			//check to see if $image is a date('YmdHis'); by checking string length
			//patch of known bug $wp_accordion_images is saving an 'image_title' => 'hray'; in the array
			$length = strlen($image);
			if ($length != 14) { 
				unset($image);
				continue; 
			}?>
			<?php $i++; ?>
			<tr <?php if($data['disable']) echo 'class="disable"'; ?>>
				<input type="hidden" name="wp_accordion_images[<?php echo $image; ?>][id]" value="<?php echo $data['id']; ?>" />
				<input type="hidden" name="wp_accordion_images[<?php echo $image; ?>][file]" value="<?php echo $data['file']; ?>" />
				<input type="hidden" name="wp_accordion_images[<?php echo $image; ?>][file_url]" value="<?php echo $data['file_url']; ?>" />
				<input type="hidden" name="wp_accordion_images[<?php echo $image; ?>][thumbnail]" value="<?php echo $data['thumbnail']; ?>" />
				<input type="hidden" name="wp_accordion_images[<?php echo $image; ?>][thumbnail_url]" value="<?php echo $data['thumbnail_url']; ?>" />
				<th scope="row" class="column-posts"><?php
				if($data['thumbnail_url']):
					
					if (getimagesize($data['thumbnail_url'])) {
						?><img src="<?php echo $data['thumbnail_url']; ?>" />
				<?php
					} else {
						if ($data['post_type'] == 'wpa_slides' ) {
						$data['thumbnail_url']='';
						if ( ($data['image_slide']) && ($data['post_type'] == 'wpa_slides') && (has_post_thumbnail($data['image_slide'])) ) {
							$post_thumbnail_id = get_post_thumbnail_id( $data['image_slide'] );
							$post_thumb_data = wp_get_attachment_image_src( $post_thumbnail_id, 'wpa-thumb' );
							$data['thumbnail_url'] = $post_thumb_data[0];
						}
						?><img src="<?php echo $data['thumbnail_url']; ?>" />
				<?php
						}
					}
				
				endif; 
				if(!$data['thumbnail_url']): 
					?>Custom Slide
				<?php endif; ?>
				
				</th>
				
				<td>
				<?php if ( (isset($data['post_type'])) && ($data['post_type'] != 'wpa_slides') ) : ?>
					<div class="title" style="width: 50px; float: left;"><p><?php _e('Type: ', 'wp-accordion'); ?></p></div> <div class="image-link link-posttypes mainSelector"><?php wp_dropdown_posttypes(array('excludes' => array('wpa_slides'), 'show_option_none' => 'Default' , 'add_option' => array( 'value' => 'custom-link' , 'name' => 'Custom Link' ),'name' => "wp_accordion_images[".$image."][post_type] selector", 'selected' => isset($data['post_type']) ? $data['post_type'] : '' )); ?></div>
					
					<!-- HIDE until 'pages' is selected in previous dropdown: wp_accordion_images[".$image."][post_type] -->
					<!-- but show if 'pages' is the selected option setting -->
					<div class="image-link link-pages" <?php if ((!isset($data['post_type'])) || ($data['post_type'] != 'page')) echo 'style="display:none;"'; ?>><?php wp_dropdown_pages(array('name' => "wp_accordion_images[".$image."][page_id]", 'selected' => isset($data['page_id']) ? $data['page_id'] : '' )); ?></div>
					
					<!-- HIDE until 'posts' is selected in previous dropdown: wp_accordion_images[".$image."][post_type] -->
					<!-- but show if 'posts' is the selected option setting -->
					<?php //echo $data['post_id'];
						//$postid = url_to_postid( isset($data['post_id']) ? $data['post_id'] : ''  );
					?>
					<div class="image-link link-posts" <?php if ((!isset($data['post_type'])) || ($data['post_type'] != 'post')) echo 'style="display:none;"'; ?>><?php wp_dropdown_posts(array('name' => "wp_accordion_images[".$image."][post_id]", 'selected' => url_to_postid( isset($data['post_id']) ? $data['post_id'] : ''  ))); ?></div>
					
					<!-- HIDE if 'posts' or 'pages' is selected in previous dropdown: wp_accordion_images[".$image."][post_type] -->
					<!-- but show if !'posts' and !'pages' is the selected option setting -->
					<div class="image-link link-custom" <?php 
						if( (!isset($data['post_type'])) || ($data['post_type'] == 'page') || ($data['post_type'] == 'post') || ($data['post_type'] == '')) 
							echo 'style="display:none;"'; 
					?>><p><?php _e('URL: ', 'wp-accordion'); ?><input type="text" name="wp_accordion_images[<?php echo $image; ?>][image_links_to]" value="<?php if ($data['image_links_to']) { echo $data['image_links_to']; } ?>" size="25" /></p></div>
				<?php else: 
					$admin_url = get_admin_url();
					$slide_id = isset($data['image_slide']) ? $data['image_slide'] : '';
					$slide_id = ($slide_id == 'Enter slide post ID') ? '' : $slide_id;
					$data['image_slide'] = $slide_id;
					$edit_link = ($slide_id) ? $admin_url . 'post.php?post='.$slide_id.'&action=edit' : '';
					if ($edit_link)
						echo '<a href="'.$edit_link.'">Edit Slide</a>';
					else {
						$data['image_content'] = 'wpa-slides';
						
						echo 'Enter a Slide Post ID under Slide/Caption Content';
					}
				?>
				<input type="hidden" name="wp_accordion_images[<?php echo $image; ?>][image_content]" value="<?php echo $data['image_content']; ?>" />
				<input type="hidden" name="wp_accordion_images[<?php echo $image; ?>][image_links_to]" value="<?php echo $data['image_links_to']; ?>" />
				<input type="hidden" name="wp_accordion_images[<?php echo $image; ?>][post_type]" value="<?php echo $data['post_type']; ?>" />
				<input type="hidden" name="wp_accordion_images[<?php echo $image; ?>][page_id]" value="<?php echo $data['page_id']; ?>" />
				<input type="hidden" name="wp_accordion_images[<?php echo $image; ?>][post_id]" value="<?php echo $data['post_id']; ?>" />
				<input type="hidden" name="wp_accordion_images[<?php echo $image; ?>][image_content_limit]" value="<?php echo $data['image_content_limit']; ?>" />
				<input type="hidden" name="wp_accordion_images[<?php echo $image; ?>][image_custom_content]" value="<?php echo $data['image_custom_content']; ?>" />
				<input type="hidden" name="wp_accordion_images[<?php echo $image; ?>][custom_slide]" value="<?php echo $data['custom_slide']; ?>" />
				<?php endif; ?>
				</td>
				
				<td><input type="text" name="wp_accordion_images[<?php echo $image; ?>][image_title]" value="<?php echo isset($data['image_title']) ? $data['image_title'] : ''; ?>" size="25" /></td>
				<td>
				<?php if ( (isset($data['post_type'])) && ($data['post_type'] != 'wpa_slides') ) : ?>
				
				<div class="image-content mainSelector"><select id="wp_accordion_images[<?php echo $image; ?>][image_content]" class="image-content-select" name="wp_accordion_images[<?php echo $image; ?>][image_content]">
						<?php $post_type = isset($data['post_type']) ? $data['post_type'] : 0; ?>
						<option value="" <?php selected('' , isset($data['image_content']) ? $data['image_content'] : '' ); ?>><?php _e('No Content', 'wp-accordion'); ?></option>
						<?php if( (isset($data['post_type'])) && ($data['post_type'] != 'wpa_slides') ): ?>
						
							<option class="content-option" value="content" <?php if( (isset($data['post_type'])) && (($data['post_type'] == 'custom-link') || ($data['post_type'] == 'wpa_slides')) ) echo 'style="display:none;"'; ?> <?php selected('content' , isset($data['image_content']) ? $data['image_content'] : '' ); ?>><?php _e('Show Content', 'wp-accordion'); ?></option>
							<?php if (post_type_supports( $post_type , 'excerpt' )) : ?>
								<option class="content-option" value="excerpt" <?php if( (!post_type_supports( $post_type , 'excerpt' )) || ($data['post_type'] == 'wpa_slides') ) echo 'style="display:none;"'; ?> <?php selected('excerpt' , isset($data['image_content']) ? $data['image_content'] : '' ); ?>><?php _e('Show Excerpt', 'wp-accordion'); ?></option>
							<?php endif; ?>
							<option value="content-limit" <?php if( (isset($data['post_type'])) && (($data['post_type'] == 'custom-link') || ($data['post_type'] == 'wpa_slides')) ) echo 'style="display:none;"'; ?> <?php selected('content-limit' , isset($data['image_content']) ? $data['image_content'] : '' ); ?>><?php _e('Show Content Limit', 'wp-accordion'); ?></option>
							
						<?php endif; ?>
						<option value="custom-content" <?php selected('custom-content' , isset($data['image_content']) ? $data['image_content'] : '' ); ?>><?php _e('Custom Content', 'wp-accordion'); ?></option>
					</select></div>
					<div class="content-limit-chars" <?php if( $data['image_content'] != 'content-limit') echo 'style="display:none;"'; ?>>
					<?php _e('Limit content to', 'wp-accordion'); ?><input type="text" id="wp_accordion_images[<?php echo $image; ?>][image_content_limit]" name="wp_accordion_images[<?php echo $image; ?>][image_content_limit]" value="<?php echo esc_attr(intval( $data['image_content_limit'])); ?>" size="3" /> <?php _e('chars', 'wp-accordion'); ?>
					</div>
					<div class="content-custom" <?php if( (!isset($data['image_content'])) || ($data['image_content'] != 'custom-content')) echo 'style="display:none;"'; ?>>
					<?php _e('Custom Content', 'wp-accordion'); ?><br /><input type="textarea" rows="5" id="wp_accordion_images[<?php echo $image; ?>][image_custom_content]" name="wp_accordion_images[<?php echo $image; ?>][image_custom_content]" value="<?php echo isset($data['image_custom_content']) ? $data['image_custom_content'] : '' ?>" />
					</div>
				<?php else:
					$data['image_content'] = 'wpa-slides'; ?>
					<div class="wpa-slides" <?php if( (!isset($data['image_content'])) || ($data['image_content'] != 'wpa-slides')) echo 'style="display:none;"'; ?>>
					<?php _e('Slide ID:', 'wp-accordion'); ?><br /><input type="textarea" rows="5" id="wp_accordion_images[<?php echo $image; ?>][image_slide]" name="wp_accordion_images[<?php echo $image; ?>][image_slide]" value="<?php echo $data['image_slide']; ?>" />
					</div>
				<?php endif; ?>
				</td>
				<td><input type="text" name="wp_accordion_images[<?php echo $image; ?>][order]" value="<?php echo (isset($data['order']) && ($data['order']!='') ) ? $data['order'] : $i; ?>" size="1" />
				</td>
				<td><input type="submit" class="button-primary" value="Update" /> <a href="?page=wp-accordion&amp;delete=<?php echo $image; ?>" class="button">Delete</a> <a href="?page=wp-accordion&amp;disable=<?php echo $image; ?>" class="button"><?php if ($data['disable']) echo 'Activate'; else echo 'Deactivate'; ?></a></td>
				
			</tr>
		<?php endforeach; ?>
		<input type="hidden" name="wp_accordion_images[update]" value="Updated" />
		</form>
		
		</tbody>
	</table>
	<?php endif; ?>

<?php
}

//	display the settings administration code
function wp_accordion_settings_admin() { ?>
	<?php wp_accordion_settings_update_check(); ?>
	<h2><?php _e('WP Accordion Settings (beta)', 'wp-accordion'); ?></h2>
	<form method="post" action="options.php">
	<?php settings_fields('wp_accordion_settings'); ?>
	<?php global $wp_accordion_settings; $options = $wp_accordion_settings; ?>

	<table class="form-table">
		<tr><th colspan="2"><h3><?php _e('Accordion Slider JS Settings', 'wp-accordion'); ?></h3></th></tr>
		<tr class="auto-play"><th scope="row">Auto Play</th>
		<td class="mainSelector">Do you want the accordion to auto play?<br />
			<select id="wp_accordion_settings[auto_play]" class="auto-play-select" name="wp_accordion_settings[auto_play]">
				<option value="true" <?php selected('' , $options['auto_play'] ); ?>><?php _e('true', 'wp-accordion'); ?></option>
				<option value="" <?php selected('' , $options['auto_play'] ); ?>><?php _e('false', 'wp-accordion'); ?></option>
			</select>
		</td></tr>
		
		<tr class="ap-slide-delay ap" <?php if(!$options['auto_play']) echo 'style="display:none;"'; ?>><th scope="row">Slide Delay</th>
		<td>Length of time (in seconds) you would like the slide delay to be:<br />
			<input type="text" name="wp_accordion_settings[slide_delay]" value="<?php echo $options['slide_delay'] ?>" size="4" />
			<label for="wp_accordion_settings[slide_delay]">second(s)</label>
		</td></tr>
		
		<tr class="ap-pause ap" <?php if(!$options['auto_play']) echo 'style="display:none;"'; ?>><th scope="row">Pause on Hover</th>
		<td>Would you like to be able to pause the accordion when user hovers?<br />
			<select id="wp_accordion_settings[pause]" class="auto-play-select" name="wp_accordion_settings[pause]">
				<option value="true" <?php selected('' , $options['pause'] ); ?>><?php _e('true', 'wp-accordion'); ?></option>
				<option value="" <?php selected('' , $options['pause'] ); ?>><?php _e('false', 'wp-accordion'); ?></option>
			</select>
		</td></tr>
		
		<tr class="ap-auto-restart ap" <?php if(!$options['auto_play']) echo 'style="display:none;"'; ?>><th scope="row">Auto Restart</th>
		<td>Would you like the accordion to automatically restart? If true, the accordion will auto restart in <? echo $options['slide_delay']; ?> seconds based on your Slide Delay settings.<br />
			<select id="wp_accordion_settings[auto_restart]" class="auto-play-select" name="wp_accordion_settings[auto_restart]">
				<option value="true" <?php selected('true' , $options['auto_restart'] ); ?>><?php _e('true', 'wp-accordion'); ?></option>
				<option value="" <?php selected('' , $options['auto_restart'] ); ?>><?php _e('false', 'wp-accordion'); ?></option>
			</select>
		</td></tr>
		
		<tr><th scope="row">Caption Delay</th>
		<td>Length of time (in seconds) you would like the caption delay to be:<br />
			<input type="text" name="wp_accordion_settings[caption_delay]" value="<?php echo $options['caption_delay'] ?>" size="4" />
			<label for="wp_accordion_settings[caption_delay]">second(s)</label>
		</td></tr>

		<tr><th scope="row">Caption Height</th>
		<td>Please input the caption hieght:<br />
			<input type="text" name="wp_accordion_settings[caption_height]" value="<?php echo $options['caption_height'] ?>" size="4" />
			<label for="wp_accordion_settings[caption_height]">px</label>
			<br /><br />
			Please input the closed caption height:<br />
			<input type="text" name="wp_accordion_settings[caption_height_closed]" value="<?php echo $options['caption_height_closed'] ?>" size="4" />
			<label for="wp_accordion_settings[caption_height_closed]">px</label>
		</td></tr>
		
		<tr><th scope="row">Caption Easing</th>
		<td><div class="caption-easing">
		<select id="wp_accordion_settings[caption_easing]" class="image-content-select" name="wp_accordion_settings[caption_easing]">
		<?php 
			$easing_options = array('jswing', 'def', 'easeInQuad','easeOutQuad','easeInOutQuad','easeInCubic','easeOutCubic','easeInOutCubic','easeInQuart','easeOutQuart','easeInOutQuart','easeInQuint','easeOutQuint','easeInOutQuint','easeInSine','easeOutSine','easeInOutSine','easeInExpo','easeOutExpo','easeInOutExpo','easeInCirc','easeOutCirc','easeInOutCirc','easeInElastic','easeOutElastic','easeInOutElastic','easeInBack','easeOutBack','easeInOutBack','easeInBounce','easeOutBounce','easeInOutBounce');
			foreach ($easing_options as $easing_option) { ?>
				<option value="<?php echo $easing_option ?>" <?php selected($easing_option , $options['caption_easing'] ); ?>><?php echo $easing_option ?></option>
			<?php
			}
		?>
		</select>
		</td></tr>
		
		<tr class="nav-key" <?php if(!$options['auto_play']) echo 'style="display:none;"'; ?>><th scope="row">Nav Key</th>
		<td>Do you want users to be able to use their left/right arrows to navigate the accordion?<br />
			<select id="wp_accordion_settings[nav_key]" class="auto-play-select" name="wp_accordion_settings[nav_key]">
				<option value="true" <?php selected('true' , $options['nav_key'] ); ?>><?php _e('true', 'wp-accordion'); ?></option>
				<option value="" <?php selected('' , $options['nav_key'] ); ?>><?php _e('false', 'wp-accordion'); ?></option>
			</select>
		</td></tr>
		
		<tr><th colspan="2"><h3><?php _e('Accordion Slider CSS Settings', 'wp-accordion'); ?></h3></th></tr>

		<tr><th scope="row">Accordion DIV ID</th>
		<td>Please indicate what you would like the accordion DIV ID to be:<br />
			<input type="text" name="wp_accordion_settings[div]" value="<?php echo $options['div'] ?>" />
		</td></tr>
		
		<tr><th scope="row">Accordion UL ID</th>
		<td>Please indicate what you would like the accordion UL ID to be:<br />
			<input type="text" name="wp_accordion_settings[ul]" value="<?php echo $options['ul'] ?>" />
		</td></tr>
		
		<tr><th scope="row">Accordion Dimensions</th>
		<td>Please input the width of the accordion ul:<br />
			<input type="text" name="wp_accordion_settings[ul_width]" value="<?php echo $options['ul_width'] ?>" size="4" />
			<label for="wp_accordion_settings[ul_width]">px</label>
			<br /><br />
			Please input the height of the accordion ul:<br />
			<input type="text" name="wp_accordion_settings[ul_height]" value="<?php echo $options['ul_height'] ?>" size="4" />
			<label for="wp_accordion_settings[ul_height]">px</label>
		</td></tr>
		
		<tr><th scope="row">Accordion Image Dimensions</th>
		<td>Please input the width of the image accordion:<br />
			<input type="text" name="wp_accordion_settings[img_width]" value="<?php echo $options['img_width'] ?>" size="4" />
			<label for="wp_accordion_settings[img_width]">px</label>
			<br /><br />
			Please input the height of the image accordion:<br />
			<input type="text" name="wp_accordion_settings[img_height]" value="<?php echo $options['img_height'] ?>" size="4" />
			<label for="wp_accordion_settings[img_height]">px</label>
		</td></tr>
		
		<tr><th scope="row">Custom CSS</th>
		<td>Use custom CSS?<br />
			<input type="checkbox" name="wp_accordion_settings[css]" value="css" <?php if ($options['css']) echo ' checked'; ?> />
		</td></tr>
		<tr><th scope="row">Caption Slide Background</th>
		<td><div class="caption-background">
		<select id="wp_accordion_settings[slide_caption_bg]" class="image-content-select" name="wp_accordion_settings[slide_caption_bg]">
		<?php 
			$slide_caption_bg_options = array('black-10pct', 'black-20pct', 'black-30pct','black-40pct','black-50pct','black-60pct','black-70pct','black-80pct','black-90pct','white-10pct','white-20pct','white-30pct','white-40pct','white-50pct','white-60pct','white-70pct','white-80pct','white-90pct');
			foreach ($slide_caption_bg_options as $slide_caption_bg_option) { ?>
				<option value="<?php echo $slide_caption_bg_option ?>" <?php selected($slide_caption_bg_option , $options['slide_caption_bg'] ); ?>><?php echo $slide_caption_bg_option ?></option>
			<?php
			}
		?>
		</select>
		</td></tr>
		
		<tr><th colspan="2"><h3><?php _e('WPA Slides Settings', 'wp-accordion'); ?></h3></th></tr>
		<tr><th scope="row">Image Thumbnail Settings</th>
		<td>Please input the width of the image thumbnail:<br />
			<input type="text" name="wp_accordion_settings[thumb_width]" value="<?php echo $options['thumb_width'] ?>" size="4" />
			<label for="wp_accordion_settings[thumb_width]">px</label>
			<br /><br />
			Please input the height of the image thumbnail:<br />
			<input type="text" name="wp_accordion_settings[thumb_height]" value="<?php echo $options['thumb_height'] ?>" size="4" />
			<label for="wp_accordion_settings[thumb_height]">px</label>
		</td></tr>
		
		<input type="hidden" name="wp_accordion_settings[update]" value="UPDATED" />
	
	</table>
	<p class="submit">
	<input type="submit" class="button-primary" value="<?php _e('Save Settings') ?>" />
	</form>
	
	<!-- The Reset Option -->
	<form method="post" action="options.php">
	<?php settings_fields('wp_accordion_settings'); ?>
	<?php global $wp_accordion_defaults; // use the defaults ?>
	<?php foreach((array)$wp_accordion_defaults as $key => $value) : ?>
	<input type="hidden" name="wp_accordion_settings[<?php echo $key; ?>]" value="<?php echo $value; ?>" />
	<?php endforeach; ?>
	<input type="hidden" name="wp_accordion_settings[update]" value="RESET" />
	<input type="submit" class="button" value="<?php _e('Reset Settings') ?>" />
	</form>
	<!-- End Reset Option -->
	</p>

<?php
}

function wp_accordion_preview_admin() { ?>
	<h2><?php _e('WP Accordion Preview (beta)', 'wp-accordion'); ?></h2>
	<?php wp_accordion(); ?>
	

<?php
}

/*
///////////////////////////////////////////////
these two functions sanitize the data before it
gets stored in the database via options.php
\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
*/
//	this function sanitizes our settings data for storage
function wp_accordion_settings_validate($input) {
	$input['auto_play'] = wp_filter_nohtml_kses($input['auto_play']);
	if ($input['auto_play'] == 'false') $input['auto_play'] = '';
	$input['nav_key'] = wp_filter_nohtml_kses($input['nav_key']);
	if ($input['nav_key'] == 'false') $input['nav_key'] = '';
	$input['img_width'] = intval($input['img_width']);
	$input['img_height'] = intval($input['img_height']);
	$input['ul_width'] = intval($input['ul_width']);
	$input['ul_height'] = intval($input['ul_height']);
	$input['div'] = wp_filter_nohtml_kses($input['div']);
	$input['ul'] = wp_filter_nohtml_kses($input['ul']);
	$input['caption_delay'] = intval($input['caption_delay']);
	$input['caption_height'] = intval($input['caption_height']);
	$input['caption_height_closed'] = intval($input['caption_height_closed']);
	$input['slide_delay'] = intval($input['slide_delay']);
	
	return $input;
}

//	this function sanitizes our image data for storage
function wp_accordion_images_validate($input) {
		
	foreach((array)$input as $key => $value) {
		if($key != 'update') {
			if ($value['image_slide'] == 'Enter slide post ID')
				$input[$key]['image_slide'] = '';
			
			$input[$key]['file_url'] = esc_url($value['file_url']);
			$input[$key]['thumbnail_url'] = esc_url($value['thumbnail_url']);
			
			if ( (isset($value['image_links_to'])) && ($value['image_links_to'])) {
				$checkhttp = substr($value['image_links_to'], 0, 4);
				if ($checkhttp == 'http')
					$input[$key]['image_links_to'] = esc_url($value['image_links_to']);
			}
			
			//if( (isset($value['image_content'])) && ($value['image_content'] == 'content-limit'))
				//$input[$key]['content_limit'] = intval($value['content_limit']);
			
			if(isset($value['image_title']))
				$input[$key]['image_title'] = wp_filter_nohtml_kses($value['image_title']);
		}
	}
	return $input;
}

function wp_accordion_order() {
	global $wp_accordion_settings, $wp_accordion_images, $order;
	
	$order = array();	
	
	$i=0;
	$count = count((array)$wp_accordion_images);
	foreach((array)$wp_accordion_images as $image => $data) {
		$i++;
		
		if ($i > $count)
			break;
		
		foreach((array)$wp_accordion_images as $image2 => $data2) {
			if ($i == intval($data2['order'])) {
				$order[$image2] = $data2;
			}
		
		}
	}
	
	//	add the image information to the database
	$wp_accordion_images['update'] = 'Reordered';
	update_option('wp_accordion_images', $order);
	

}

function wp_accordion_images_order_check() {
	global $wp_accordion_settings, $wp_accordion_images;
	
	$reset = false;
	if(isset($wp_accordion_images['update'])) {
		
		if($wp_accordion_images['update'] == 'Updated') {
			$reset = true;
			wp_accordion_order();
		}
	}
	
	return $reset;

}

function checkRemoteFile($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    // don't download content
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    if(curl_exec($ch)!==FALSE)
    {
        return true;
    }
    else
    {
        return false;
    }
}

?>