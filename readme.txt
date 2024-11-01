=== WP Accordion Slider ===
Contributors: wpsmith
Plugin URI: http://www.wpsmith.net/wp-accordion-slider
Donate link: http://www.wpsmith.net/donation
Tags: accordion, slider
Requires at least: 3.1
Tested up to: 3.2
Stable tag: trunk

This plugin creates a jQuery accordion slider from the images you upload displayed in your theme via php code or a shortcode.

== Description ==

The WP Accordion Slider plugin allows you to upload images from your computer, which will then be used to generate a jQuery Accordion of the images. Custom titles and content from various sources can be shown. Content, excerpt, and content limits can all be used for posts and pages.

Each image can also be given a URL which, when the image is active in the slideshow, will be used as an anchor wrapper around the image, turning the image into a link to the URL you specified. Images can also be deleted via the plugins Administration page.

**NOTICE**: Currently in Beta. Please [notify me](http://www.wpsmith.net/contact/ "email Travis Smith") any issues or bugs.

IMPORTANT: 
**You must have 'post-thumbnails' enabled to use this plugin. If it is not enabled, this plugin will enable it.**
**This plugin also enables the use of shortcodes in widgets**

**Features**:

1. Set Auto Play (and thus, slide delay, whether to pause on hover, and auto restart delay)
1. Create custom titles
1. Set caption height, delay, and easing
1. Create custom content for captions or capture content from the post's or page's content, content limit or excerpt.
1. Set accordion navigation key. If set to true, users can use right/left arrows to navigate through the accordion slider.
1. Custom set the div/ul ids as well as set the image/div sizes.
1. Ability to Deactivate/Reactivate images/slides without deleting them
1. Set Caption Background (white/black) transparency
1. Cross browser tested (IE7+, FF 3.6+, Chrome 10+)

== Installation ==

1. Upload the entire `wp-accordion-slider` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Configure the plugin, and upload/edit/delete images via the "WP Accordion" menu within the "Media" tab
1. Place `<?php wp_accordion(); ?>` in your theme where you want the slideshow to appear
1. Alternatively, you can use the shortcode [wp-accordion] in a post or page to display the slideshow.

== Frequently Asked Questions ==

= My images from my WPA slides don't appear? =

If you've changed any of your thumbnail dimensions (via Settings -> Media) after previously uploading images or have changed to a theme with different featured post image dimensions, try using [Regenerate Thumbnails](http://wordpress.org/extend/plugins/regenerate-thumbnails/ "Regnerate Thumbnails") to regenerate your thumbnails.

= My accordion is not appearing? =

1. Check with your host to ensure that $_GET is enabled.
1. Check your CSS, if you are using a custom CSS file. More than likely you have not set the height and width of your UL or DIV tag correctly.

= My images won't upload. What should I do? =

The plugin uses built in WordPress functions to handle image uploading. Therefore, you need to have [correct permissions](http://codex.wordpress.org/Changing_File_Permissions "Changing File Permissions") set for your uploads directory.

Also, a file that is not an image, or an image that does not meet the minimum height/width requirements, will not upload. Images larger than the dimensions set in the Settings of this plugin will be scaled down to fit, but images smaller than the dimensions set in the Settings will NOT be scaled up. The upload will fail and you will be asked to try again with another image.

Finally, you need to verify that your upload directory is properly set. Some hosts screw this up, so you'll need to check. Go to "Settings" -> "Miscellaneous" and find the input box labeled "Store uploads in this folder". Unless you are absolutely sure this needs to be something else, this value should be exactly this (without the quotes) "wp-content/uploads". If it says "/wp-content/uploads" then the plugin will not function correctly. No matter what, the value of this field should never start with a slash "/". It expects a path relative to the root of the WordPress installation.

= I'm getting an error message that I don't understand. What should I do? =

Please [use my support form](http://www.wpsmith.net/wp-accordion-slider/support/ "email Travis Smith"). This plugin is now relatively stable, so if you are experiencing problems that you would like me to diagnose and fix, please use my support form.

As much as I would like to, in most cases, I cannot provide free support.

= How can I style the slideshow further? =

In the settings of the plugin, you're able to set a custom DIV/UL ID for the slideshow. Use that DIV/UL ID to style the slideshow however you want using CSS. Also you can choose to use a custom stylesheet and begin from the sample stylesheet, which can be downloaded via the Help dropdown.

= In what order are the images/slides shown during the slideshow? =

Chronologically, from the time of upload. For instance, the first image/slides you upload will be the first image in the slideshow. The last image will be the last, etc. However, a new feature is incorporated that you can set the order of the images/slides.

= Can I reorder the images/slides? =

Yes.

= Why won't my images reorder? =

The images do reorder. You may need to clear your cache or your site's cache.

= Can I create anything other than images with this plugin? =

Yes, feel free to explore what the custom content can do. However, out of the box, the custom content will serve as a caption. You can use WPA Slides to create custom slides, e.g. slides with video and text.

= Do you have future plans for this plugin? =
Yes. Here are some things that I want to eventually include:

* Add the ability to override settings by using function/shortcode arguments: `<?php wp_accordion('img_width=300&img_height=200&div=slideshow'); ?>`
* Add more custom post type support that reflect pages/posts under hyperlink/page ID/post ID
* Add tooltip functionality
* Add different types of accordions
* Add ability to have multiple accordions


== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the directory of the stable readme.txt, so in this case, `/tags/4.3/screenshot-1.png` (or jpg, jpeg, gif)


== Changelog ==

= 1.9 =
* Fixed permissions

= 1.5 =
* Captions for Slide CPT
* Added link to Slide CPT

= 1.4 =
* Fixed $post->post_type bug error props @_mfields

= 1.3 =
* Initial Release

= 0.1-1.2 =
* Private Beta

== Special Thanks ==
I owe a huge debt of gratitude to all the folks at [StudioPress](http://wpsmith.net/go/studiopress "StudioPress"), their themes make life easier. This plugin is built on top of Nathan Rice's [WP-Cycle](http://wordpress.org/extend/plugins/wp-cycle/ "WP-Cycle") with some enhancements and customizations designed towards an accordion slider.

And thanks to the various individuals who helped me through the beta testing.