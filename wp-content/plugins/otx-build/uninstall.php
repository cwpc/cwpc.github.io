<?php
// if uninstall not called from WordPress, then exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();
	
delete_option( 'ortext_linking_options' );
delete_option( 'ortext_dating_options' );
?>