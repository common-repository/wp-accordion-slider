<?php
//If uninstall not called from WordPress exit
if( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

unregister_setting( 'wp_accordion_images', 'wp_accordion_images' );
unregister_setting( 'wp_accordion_settings', 'wp_accordion_settings');

delete_option('wp_accordion_images');
delete_option('wp_accordion_settings');

?>