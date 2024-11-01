<?php

add_action('init', 'register_my_script');
function register_my_script() {
	wp_register_script( 'wp_accordion_admin_js', WP_ACCORDION_URL.'/js/admin.js' );
	wp_register_script( 'jquery_easing', 'http://gsgd.co.uk/sandbox/jquery/easing/jquery.easing.1.3.js');
	wp_register_script( 'accordionza', WP_ACCORDION_URL.'/js/jquery.accordionza.pack.js');
}

// Admin when you plugin page is called
add_action( 'init' , 'wp_accordion_admin' );
function wp_accordion_admin() {
	if (isset($_GET['page']) && $_GET['page'] == 'wp-accordion') {
		add_action( 'admin_print_scripts' , 'wp_accordion_scripts' );
		add_action( 'admin_print_scripts' , 'wp_accordion_admin_script' );
		add_action( 'admin_print_styles' , 'wp_accordion_styles' );
		add_action( 'admin_head', 'wp_accordion_dyn_style' );
		add_action( 'admin_head', 'wp_accordion_dyn_script' );
	}
}

function wp_accordion_admin_script() {
	wp_enqueue_script( 'wp_accordion_admin_js' , '' , array('jquery') , false );
}

add_action( 'wp_print_scripts' , 'wp_accordion_scripts' );
function wp_accordion_scripts() {
	//wp_register_script( 'jquery_min', 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js');
	//wp_enqueue_script( 'jquery_min' , '' , '' , false );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery_easing' , '' , array('jquery'), false );
	wp_enqueue_script( 'accordionza' , '' , array('jquery','jquery_easing'), false );
}

add_action('wp_print_styles', 'wp_accordion_styles');
function wp_accordion_styles() {
	global $wp_accordion_settings;

	if (!$wp_accordion_settings['css'])
			wp_enqueue_style( 'wp_accordion_php' , WP_ACCORDION_URL.'css/style.php?ul='.$wp_accordion_settings['ul'] , __FILE__ );
	
	if (is_admin()) {
		wp_register_style( 'wp_accordion_admin_css', WP_ACCORDION_URL.'css/admin.css' );
		wp_enqueue_style( 'wp_accordion_admin_css' );
		wp_register_style('thetooltip', WP_ACCORDION_URL . 'css/thetooltip.css');
		wp_enqueue_style( 'thetooltip' );
	}
}

add_action( 'wp_head', 'wp_accordion_dyn_style' );
function wp_accordion_dyn_style() { 
	global $wp_accordion_settings;
?>
	<style type="text/css" media="screen">
		
		#<?php echo $wp_accordion_settings['ul']; ?> {
			height:<?php echo $wp_accordion_settings['ul_height']; ?>px;
			width:<?php echo $wp_accordion_settings['ul_width']; ?>px;
		}
		#<?php echo $wp_accordion_settings['ul']; ?> li {
			height:<?php echo $wp_accordion_settings['ul_height']; ?>px;
			max-height: <?php echo $wp_accordion_settings['ul_height']; ?>px;
		}
		#<?php echo $wp_accordion_settings['ul']; ?> .slide_handle, #<?php echo $wp_accordion_settings['ul']; ?> .slide_content  {
			max-height: <?php echo $wp_accordion_settings['ul_height']; ?>px;
		}
		#<?php echo $wp_accordion_settings['div']; ?> ul {
			padding: 0;
		}
		#<?php echo $wp_accordion_settings['div']; ?> {
			overflow: hidden;
		}
		#<?php echo $wp_accordion_settings['ul']; ?> .slide_caption {
			width:<?php echo $wp_accordion_settings['ul_width']; ?>px;
			background:url(<?php echo WP_ACCORDION_URL . '/images/bg/' . $wp_accordion_settings['slide_caption_bg']; ?>.png); /* You could use rgba instead, but that means less browser support */
		}
		#<?php echo $wp_accordion_settings['ul']; ?> .slide_caption_toggle div {
			width:<?php echo $wp_accordion_settings['img_width']; ?>px;
		}
	

	</style>
	
<?php 
}

add_action('wp_head', 'wp_accordion_dyn_script', 15);
function wp_accordion_dyn_script() {
	global $wp_accordion_settings; ?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	// Accordion
	$('#<?php echo $wp_accordion_settings['ul']; ?>').accordionza({
		autoPlay: <?php if ($wp_accordion_settings['auto_play']) { echo 'true'; } else { echo 'false'; } ?>,
		<?php if ($wp_accordion_settings['auto_play']): ?>
		autoRestartDelay: <?php if ($wp_accordion_settings['auto_restart']) { echo 'true'; } else { echo 'false'; } ?>,
		pauseOnHover: <?php if ($wp_accordion_settings['pause']) { echo 'true'; } else { echo 'false'; } ?>,
		<?php endif; ?>
		captionDelay: <?php echo ($wp_accordion_settings['caption_delay'] * 1000); ?>,
		captionEasing: '<?php echo $wp_accordion_settings['caption_easing']; ?>',
		captionHeight: <?php echo $wp_accordion_settings['caption_height']; ?>,
		captionHeightClosed: <?php echo $wp_accordion_settings['caption_height_closed']; ?>,
		navKey: <?php if ( $wp_accordion_settings['nav_key'] )  { echo 'true'; } else { echo 'false'; } ?>,
		slideDelay: <?php echo ($wp_accordion_settings['slide_delay'] * 1000); ?>
	});

});
</script>

<?php }
?>