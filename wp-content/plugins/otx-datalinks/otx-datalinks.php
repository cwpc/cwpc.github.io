<?php
/*
 * Plugin Name: Ortext Datalinks 
 * Plugin URI: http://acrosswalls.org//ortext-datalinks/ 
 * Description: Implement otxdata shortcode and bulk datalinks importer
 * Author: Communicating with Prisoners Collective
 * Author URI: http://acrosswalls.org/authors/
 * Version: 1.0
 * License: CCO 1.0 Universal
 * License URI: http://creativecommons.org/publicdomain/zero/1.0/legalcode
*/

/*

The shortcode 'otxdata' retrieves and displays as an unordered list the labels and urls held in a post's 'otx-datalinks' meta-keys.  In the ortext application, the post describes a dataset.  The label describe a version of the dataset.  The url indicate where that version is located.  

A post can have multiple 'otx-datalinks' meta-keys.  Each one should have the form label@url, where '@' is the character used to separate the label from the url.  Whitespace can be added on either or both sides of the '@' to make the string more easily readable.

To enable bulk uploading, updating, and deleting datalinks, this plugin includes a datalinks importer.  The importer readers a comma-separated-value (csv) file where each line has in order three values: an otx-key, a label, and a url.  For each import file line, the importer looks for the dataset post with an otx-key meta-key that matches the given value.  If it finds such a dataset, it adds to it a otx-datalinks meta-key with value 'label @ url' from the file line.  If a otx-datalinks key with that label already exists, the url is updated with the url from the file line.  If the file line has DELETE for the url value, than the otx-datalinks meta-key instance with the corresponding label is deleted from the otx-key-identified post.

The datalink importer targets a post_type.  In the ortext application, the target post_type is the custom post type datasets.  The target post_type can be changed to a different post_type.  Setting the post_type to 'any' is possible, but requires more attention to avoid creating a slug that the importer inadvertently targets.

In the ortext application, otxkeys correspond to filenames (without suffixes) of Excel and OpenOffice workbooks.  A large set of files can thus be imported as datalinks with minor massaging of a directory listing holding all the files.  

*/

// Prohibit direct script loading
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );


// target post_type for datalinks meta; in the ortext application its 'datasets' 
define( 'OTX_DATALINKS_POST_TYPE', 'datasets' );

// process shortcode otxdata to display datalinks in post 
include( 'otx-show-datalinks.php' );


// Import datalinks into meta field 'otx-datalinks' in post-type dataset with corresponding slug

if ( !defined('WP_LOAD_IMPORTERS') )
	return;

// Load Importer API
require_once ABSPATH . 'wp-admin/includes/import.php';


if ( !class_exists( 'WP_Importer' ) ) {
	$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
	if ( file_exists( $class_wp_importer ) )
		require_once $class_wp_importer;
}

/**
 * Datalinks Importer
 *
 * @package WordPress
 * @subpackage Importer
 */
 
if ( class_exists( 'WP_Importer' ) ) {
class Otx_Datalinks_Importer extends WP_Importer {

	function __construct() {
		if ( ! is_admin() )	return;		
	}

	function dispatch() {
		echo '<div class="wrap">';
		echo '<h2>'.__('Import Datalinks', 'oxt-datalinks').'</h2>';
		$step = empty( $_GET['step'] ) ? 0 : (int) $_GET['step'];
		switch ( $step ) {
			case 0: 
				echo '<p>' . __( 'A datalinks file is a csv text file with fields: (otx-key), (version label), (host url).  Importing a datalinks file allows you to add or update hosting locations easily for many datasets.  Choose a datalinks file to upload, then click Upload file and import.', 'otx-datalinks' ) . '</p>';
				wp_import_upload_form( add_query_arg( array( 'step' => 1, 'import' => 'datalinks' ) ) );			
				break;
			case 1:
				check_admin_referer( 'import-upload' );
				$this->handle_upload();
				break;
		}
		echo '</div>';
	}		

	function handle_upload() {
		$file = wp_import_handle_upload();
		
		if ( isset( $file['error'] ) ) :
			echo '<p><strong>' . __( 'Sorry, there has been an error.', 'otx-datalinks' ) . '</strong><br />';
			echo esc_html( $file['error'] ) . '</p>';
			return false;
		elseif ( ! file_exists( $file['file'] ) ) :
			echo '<p><strong>' . __( 'Sorry, there has been an error.', 'otx-datalinks' ) . '</strong><br />';
			printf( __( 'The export file could not be found at <code>%s</code>. It is likely that this was caused by a permissions problem.', 'otx-datalinks' ), esc_html( $file['file'] ) );
			echo '</p>';
			return false;
		endif;
		
		$this->id = (int) $file['id'];
		$this->file = $file['file'];
		$this->process_datalinks();
		wp_import_cleanup( $this->id );
		echo '<h3>' . __( 'Finished.', 'otx-datalinks' ) . '</h3>';
	}

	function process_datalinks() {
		$handle = fopen( $this->file, 'r' );
		if ( false == $handle ) :
			$this->report_error( __( 'File failed to open: ' ) . $this->file );
			return;
		endif;

		echo '<ol>';
		$line = 0;
		while ( FALSE !== ( $data = fgetcsv( $handle, 0, ',' ) ) ) {
			$line++;
			if ( 3 != count( $data ) ) :
				$this->report_error( __( 'Incorrect number of fields in file line ', 'oxt-datalinks' ) . $line );
				continue;
			endif;

			$otxkey = trim( $data[0] );
			$version = trim( $data[1] );
			$datahost = trim( $data[2] );
			
			$qargs = array( 'post_type' => OTX_DATALINKS_POST_TYPE, 
							'meta_key' => 'otx-key', 
							'meta_value' => sanitize_text_field( $otxkey ), 
							);
			$query = new WP_Query( $qargs );			
			if ( 0 == $query->post_count ) :
				$this->report_error( __( 'Dataset key not found: ', 'oxt-datalinks' ) . $otxkey . __( ' line ', 'oxt-datalinks' ) . $line );
				continue;
			elseif ( 1 < $query->post_count ) :
				$this->report_error( __( 'Multiple datasets have the same otx-key: ', 'oxt-datalinks' ) . $otxkey . __( ' line ', 'oxt-datalinks' ) . $line  );
				continue;				
			else: 
				$dataset = $query->posts[0];
			endif;
			
			$fetched = otx_get_datalinks( $dataset->ID );
			if ( $fetched ) :
				$links = $fetched->links;
				if ( isset( $links[ $version ] ) ) :
					$prev_datalink = $version . ' @ ' . $links[ $version ];
				else:
					$prev_datalink = '';
				endif;
			else:
				$prev_datalink = '';
			endif;
			
			if ( 'DELETE' == $datahost ) :
				if ( '' != $prev_datalink ) { delete_post_meta( $dataset->ID, 'otx-datalinks', $prev_datalink ); }
				$new_datalink = 'DELETED';
			else:
				$new_datalink = $version . ' @ ' . $datahost;
				if ( $new_datalink == $prev_datalink ) :
					$new_datalink = 'IDENTICAL';
				else:
					$status = $prev_datalink ? update_post_meta( $dataset->ID, 'otx-datalinks', $new_datalink, $prev_datalink ) :
											   add_post_meta( $dataset->ID, 'otx-datalinks', $new_datalink ); 
					if ( ! $status ) :
						$this->report_error( __( 'Meta write failed: ', 'otx_datalinks' ) . $prev_datalink . ' &gt;&gt; ' . $new_datalink . __( ' line ', 'oxt-datalinks' ) . $line  );
						continue;
					endif;
				endif;
			endif;
			if ( 'IDENTICAL' != $new_datalink ) {echo '<li>' . esc_html( $otxkey ) . ': ' . esc_html( $prev_datalink ) . ' &gt;&gt; ' . esc_html( $new_datalink ) . '</li>';}
		}		
		echo '</ol>';
			
		fclose( $handle );			
	}	
	
	function report_error( $text ) {
		echo '<li>' . esc_html( $text ) . '</li>';
	}
	
		
}  // end class Otx_Datalink_Importer

function otx_datalinks_init() {
	load_plugin_textdomain( 'otx-datalinks', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	global $otx_datalinks_importer;
	$otx_datalinks_importer = new Otx_Datalinks_Importer();
	register_importer( 'datalinks', __( 'Datalinks', 'otx-datalinks' ), __('Import datalinks into meta of corresponding dataset posts.', 'otx-datalinks'), array( $otx_datalinks_importer, 'dispatch' ) );
}

add_action( 'admin_init', 'otx_datalinks_init' );

}   // end if WP_IMPORTER class exists

?>