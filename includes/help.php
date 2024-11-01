<?php

// documentation for sliders manager page
function wp_accordion_help($contextual_help, $screen_id, $screen) {	

		$contextual_help = '
			<h3>WP Accordion Help</h3>		
			<h4>Adding New Images/Slides</h4>
			<p>To add a new slide, either upload an image <strong><em>OR</em></strong> add a new custom slide.</p>
			<p>Your new slide/image will appear below any current slides/images already created.</p>
			<p>Custom slides will show the featured image of that slide if available otherwise, it will default to "Custom Slide" (e.g., instead of an image, you have a video).</p>
			
			<h4>Upload an Image</h4>
			<p>To upload an image, click Choose File. Then browse to the appropriate image and select. Then click Upload.</p>
			
			<h4>Add a Custom Slide</h4>
			<p>To add a custom slide, simply enter the post ID of the WPA Slide. To get this, go to WPA Slides. Locate the desired WPA Slide. Hover over it and it will reveal a URL similiar to <em>http://domain.com/wp-admin/post.php?post=690&action=edit</em>. If hovering doesn\'t reveal the URL, click on the desired WPA Slide (or click the edit link that appears underneath). This will then reveal the URL in the address bar. In this example 690 is the WPA Slide post ID.</p>
			
			<h4>Hyperlink/Page ID/Post ID</h4>
			<p>This is type of hyperlink for an image slide. For a custom slide, this will be completely arbitrary and will be replaced by a Configure link.</p>
			
			<h4>Slide Title</h4>
			<p>This will be the vertical text that appears beside the image/slide.</p>
			
			<h4>Slide/Caption Content</h4>
			<p>The options here are determined by the options selected under Hyperlink/Page ID/Post ID. They are:</p>
			<ul>
			<li>No Content: refers to not having any caption.</li>
			<li>Show Content: Shows all content in the caption.</li>
			<li>Content Limit: Reveals a box for you to enter the number of allowed characters. Shows limited content to a designated number of characters in the caption. Currently, everything is filtered except &lt;style&gt;, &lt;script&gt;, &lt;strong&gt;, and &lt;em&gt;. Remember tags also count towards the number of allowed characters. If you wish to add more tags, use <strong>add_filter( \'wpa_get_the_content_limit_allowedtags\' , \'YOURFUNCTION\' );</strong></li>
			<li>Custom Content: Reveals a textbox for you to copy/paste words/HTML. Shows this custom content in the caption.</li>
			<li>WPA Slide: You <strong><em>MUST</em></strong> enter a WPA Slide ID.</li>
			</ul>
			
			<h4>Ordering the Slides</h4>
			<p>Simply, enter a numbered order into the input boxes under Order and click Update.</p>
			
			<h4>Inserting a Slider into a Post or Page</h4>
			<p>Place the shortcode <em>[wp-accordion]</em> in a post or page to display a slider. Replace <em>#</em> with the unique ID number of the desired slider.</p>
			
			<h4>Inserting a WP Accordion into a theme Template file</h4>
			<p>Use <em>&lt;?php wp_accordion(); ?&gt;</em> to display the WP Accordion Slider anywhere in your template files.</p>
			
			<h4>Default CSS</h4>
			<p>The default/sample CSS is set to optimally show 4 images/slides, which can be found at <a href="'. WP_ACCORDION_URL.'/css/sample-accordion.css'.'">sample-accordion.css</a>.</p>
			<p>The plugin folder also contains sample images for you to test.</p>
			
			<h4>Custom CSS</h4>
			<p>Select this option to use your own CSS style for the Accordion. You will need to wp_enqueue_style() in your functions.php file to the \'wp_print_styles\' hook. If unselected, the Default CSS included in the plugin will be used.</p>
			<p>If you would like the preview to work as well, you will need to wp_enqueue_style() in your functions.php file to the \'admin_print_styles\' hook. If you want it to appear only on that page, use this function:</p><code><pre>	// Admin when you plugin page is called
	add_action( \'init\' , \'wp_accordion_admin\' );
	function wp_accordion_admin() {
		if (isset($_GET[\'page\']) && $_GET[\'page\'] == \'wp-accordion\') { 
			add_action( \'admin_print_styles\' , \'wp_accordion_custom_style\' ); 
		} 
	}</pre></code>
        ';
		return $contextual_help;
}
if (isset($_GET['page']) && $_GET['page'] == 'wp-accordion') {
	add_action('contextual_help', 'wp_accordion_help', 10, 3);
}

?>