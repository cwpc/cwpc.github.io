<?php
/*
 * Plugin Name: Ortext BibTeX Importer
 * Plugin URI: http://acrosswalls.org/ortext-bibtex-importer/
 * Description: Import BibTeX references into a custom post type
 * Author: Communicating with Prisoners Collective
 * Author URI: http://acrosswalls.org/authors/
 * Version: 1.0
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

/* This plugin contains some legacy code from Martin Fenner's BibTex Importer
That in turn cited the following code sources:
This code is based on the OMPL Importer Plugin: http://wordpress.org/extend/plugins/opml-importer/
This code uses the bibtexParse library from the Bibliophile project: http://bibliophile.sourceforge.net/

This plugin's legacy code needs to be updated to use better built-in WordPress query functions.
*/


// Prohibit direct script loading
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

// Load Importer API
require_once ABSPATH . 'wp-admin/includes/import.php';

if ( !class_exists( 'WP_Importer' ) ) {
	$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
	if ( file_exists( $class_wp_importer ) )
		require_once $class_wp_importer;
}

if ( class_exists( 'WP_Importer' ) ) {
// Load bibTexParse
require_once 'bibtexparse/parseentries.php';
require_once 'bibtexparse/parsecreators.php';

class bibtex_type_import extends WP_Importer {

	function bibtex_type_import() {}   // older PHP constructor call (function with same name as class)
	
	function author_year( $entry ) {
		// Fetch surname from first author and publication year
		$creator = new PARSECREATORS();
		if ( isset( $entry['author'] ) ) {
			$creatorArray = $creator->parse($entry['author']);
			// creatorArray is $firstname, $initials, $surname, $prefix
			$surname = $creatorArray[0][2];
		}
		else {
			$surname = '';
		}
		
		$author_year = ($surname != "" ? $surname . ' ': '') . ( isset( $entry['year'] ) && $entry['year'] != "" ? $entry['year'] . '. ' : '');
		return $author_year;
	}
	
	function doi_or_url( $entry ) {
		// Fetch doi, use url if no doi
		if ( isset( $entry['doi'] ) && $entry['doi'] != "")
			return 'http://dx.doi.org/' . $entry['doi'];
		else
			return ( isset( $entry['url'] ) ? $entry['url'] : '');
	}
	
	function is_duplicate( $key ) {
		// Check whether reference key (ortext key) already exists
// echo 'check dups for key: ' . $key . '<br/>';
		$arg = array( 'post_type' => 'refs', 'meta_key' => 'otx-key', 'meta_value' => $key);
		$refs = get_posts( $arg );
// foreach( $refs as $ref) echo $ref->post_title . '<br/>';
		return ( count($refs) >0  ? TRUE : FALSE );
	}
	
	function trimmed_title( $entry ) {
		// Trim the following: . { }
		if ( isset( $entry['title'] ) ) :
			$tt = $this->sanitize_txt( trim( $entry['title'], "\x20\x7B\x7D" ) );
		else:
			$tt = '';
		endif;
		return $tt;
	}

	function dispatch() {
		global $wpdb, $user_ID;
		$step = isset( $_POST['step'] ) ? (int) $_POST['step'] : 0;
		switch ($step) {
			case 0: {
				include_once( ABSPATH . 'wp-admin/admin-header.php' );
				if ( !current_user_can('publish_posts') )
					wp_die(__('Cheatin&#8217; uh?', 'otx-bibtex-importer'));
				?>

				<div class="wrap">		  
				  <h2><?php _e('Import BibTeX references into a custom post type', 'otx-bibtex-importer') ?></h2>
				  <form enctype="multipart/form-data" action="admin.php?import=bibtex2" method="post" name="bibtex2">
				  <?php wp_nonce_field('import-bibtex2') ?>
				  <div style="width: 90%; margin: auto; height: 8em;">
				    <input type="hidden" name="step" value="1" />
				    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo wp_max_upload_size(); ?>" />
				    <div style="width: 48%;" class="alignleft">
				    <h3><label for="bibtex_url"><?php _e('Specify a BibTeX URL:', 'otx-bibtex-importer'); ?></label></h3>
				    <input type="text" name="bibtex_url" id="bibtex_url" size="50" class="code" style="width: 90%;" value="http://" />
				  </div>

				  <div style="width: 48%;" class="alignleft">
				    <h3><label for="userfile"><?php _e('Or choose from your local disk:', 'otx-bibtex-importer'); ?></label></h3>
				    <input id="userfile" name="userfile" type="file" size="30" />
				  </div>

				</div>

				<p style="clear: both; margin-top: 1em;"><label for="cat_id"><?php _e('The importer will automatically file the citations as separate posts with custom post type:', 'otx-bibtex-importer') ?></label> 
				<select name="cpt_id" id="cpt_id">
				<?php
				$post_types = get_post_types( array( 'public' => true, '_builtin' => false ) );
				foreach ($post_types as $post_type) {
				  ?>
				  <option value="<?php echo $post_type; ?>"<?php if ( 'refs' == $post_type ) echo ' selected '?>><?php echo esc_html( $post_type ); ?></option>
				  <?php
				} // end foreach			
				?>
				</select></p>

				<p class="submit"><input type="submit" name="submit" value="<?php esc_attr_e('Import BibTeX File', 'otx-bibtex-importer') ?>" /></p>
				</form>

				</div>
				<?php
				break;
			} // end case 0

			case 1: {
				check_admin_referer('import-bibtex2');
				include_once( ABSPATH . 'wp-admin/admin-header.php' );
				if ( !current_user_can('publish_posts') )
					wp_die(__('Cheatin&#8217; uh?', 'otx-bibtex-importer'));
				?>
				<div class="wrap">
				<h2><?php _e('Importing...', 'otx-bibtex-importer') ?></h2>
				<?php
				$post_type = $_POST['cpt_id'];
				if ( ! post_type_exists( $post_type ) )
					return;

				$bibtex = "";
				$bibtex_url = $_POST['bibtex_url'];
				if ( isset($bibtex_url) && $bibtex_url != '' && $bibtex_url != 'http://' ) {
					$bibtex = wp_remote_fopen($bibtex_url);
				} else { 
					// try to get the upload file.
					$overrides = array('test_form' => false, 'test_type' => false);
					$file = wp_handle_upload($_FILES['userfile'], $overrides);
					if ( isset($file['error']) )
						wp_die($file['error']);
					$bibtex_url = $file['file'];
					$bibtex = file_get_contents($bibtex_url);
				}
				if ( $bibtex != '' ) {
					// Load bibtexParse parser, parse bibtex into array 
					$parse = NEW PARSEENTRIES();
					$parse->loadBibtexString($bibtex);
					$parse->extractEntries();
					list($preamble, $strings, $entries, $undefinedStrings) = $parse->returnArrays();
				
					// Load bibtexParse parser, parse bibtex into bibtex entries
					$bibtex_parse = NEW PARSEENTRIES();
					$bibtex_parse->fieldExtract = FALSE;
					$bibtex_parse->loadBibtexString($bibtex);
					$bibtex_parse->extractEntries();
					list( $bibtex_preamble, $bibtex_strings, $bibtex_entries, $bibtex_undefinedStrings ) = $bibtex_parse->returnArrays();		
					$imports = 0;
					foreach ($entries as $num => $entry) {
						$key = $entry['otx-key'];
						if ( !isset( $key ) || $key == '' ) {
							echo 'missing or zero-length key' . '<br/>';
						}
						else if ($this->is_duplicate( $key )) {
							echo sprintf(__('<strong style="color: red;">Not imported because reference %s already exists.</strong> ', 'otx-bibtex-importer'),$key);
							printf('<p>');
						} else {
							// create ref post
							$bibtex_entry = ltrim($bibtex_entries[$num]);
							if ( !isset( $entry['year'] )) $entry['year'] = 'nd';
							$link = $this->doi_or_url( $entry );
							$short_title = $this->shorten_title( $this->trimmed_title( $entry ) );
							$ref = array( 
								'post_type' 	=> $post_type,
								'post_status' 	=> 'publish',
								'post_title' 	=> $key . ' ' . $short_title,
								'post_content' 	=> $this->formatBibtex( $entry, $link ), 
								'post_excerpt' 	=> $this->formatExcerpt( $entry ),
								);

							$refID = wp_insert_post( $ref );
							if ( ! $refID ) {
								error_log( 'failed to insert ref ' . $key );
								continue;
							}
							add_post_meta( $refID, 'otx-key', $this->sanitize_txt( $key ), true );
							add_post_meta( $refID, '_otx-bibtex', $this->sanitize_txt( $bibtex_entry ), true );
							if ( '' != $link ) { add_post_meta( $refID, '_otx-ref-url', esc_url( $link ), true ); }
							
							$imports++;
						}
						echo sprintf(__('<strong>%s</strong> %s', 'otx-bibtex-importer').'</p>', $this->author_year( $entry ), $this->trimmed_title( $entry )) . "<br />";
					}
					?>
					<p><?php printf(__('<p>Inserted %1$d out of %2$d references into post type <strong>%3$s</strong>.', 'otx-bibtex-importer'), $imports, count($entries), $post_type ) ?></p>
					<?php
				} // end if got Bibtex
				else {
					echo "<p>" . __("No BibTeX found. Press back on your browser and try again", 'otx-bibtex-importer') . "</p>\n";
				} 

				do_action( 'wp_delete_file', $bibtex_url);
				@unlink($bibtex_url);
				?>
				</div>
				<?php
				break;
			} // end case 1
		} // end switch
	}

	function shorten_title( $title ) {
		$char_max = 50;	
		if ( strlen($title) <= $char_max ) return $title;
		$word_list = str_word_count( $title, 2 );
		$word_pos = array_keys( $word_list );
		for( $i=0; isset( $word_pos[$i] ) && $word_pos[$i] < $char_max; $i++ ) ;
		if ( isset( $word_pos[$i] ) ) {
			$short_title = substr( $title, 0, $word_pos[$i] );
			}
		else {
			$short_title = substr( $title, 0, $char_max );
		}
			
		return $short_title;
	}

	function formatBibtex( $entry, $link ) {
		$new_entry = '';
		foreach( $entry as $key => $value ) {
			if ( 'bibtexCitation' == $key || 'bibtexEntryType' == $key || 'url' == $key ) { continue; }
			$new_entry .= $key . ': ' . $this->sanitize_txt( $value ) . " <br />";
		}
		$link = esc_url($link);
		if ('' != $link ) { $new_entry .=  '<br />Full text: <a href="' . $link . '">' . $link . '</a><br />'; }
		return $new_entry;
	}

	function formatExcerpt( $entry ) {
		$author = isset( $entry['author'] ) ? $entry['author'] : '';
		$year = isset( $entry['year'] ) ? ' (' . $entry['year'] . '). ' : ' ';	
		$title = isset( $entry['title'] ) ? $entry['title'] : '';		
		$excerpt = $author . $year . $title;
		return $excerpt;
	}
	
	
	
// for some odd reason sanitize_text_field() and esc_html() chop text at &#195 in this plugin (doesn't happen from console)
// below is alternate PHP 5.2 text sanitization	
	function sanitize_txt( $text ) {
//		if ( !mb_check_encoding( $text, 'UTF-8' ) ) $text = mb_convert_encoding($text, 'UTF-8'); 
//		$san_text = filter_var($text, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_HIGH | FILTER_FLAG_STRIP_LOW ) ;
		$san_text = esc_html( $text );
		return $san_text;
	}
	
	
}  // end class bibtex_type_import

$bibtex_importer = new bibtex_type_import();
register_importer('bibtex2', __('BibTeX', 'otx-bibtex-importer'), __('Import BibTeX into custom post type.', 'otx-bibtex-importer'), array(&$bibtex_importer, 'dispatch'));

}   // end if WP_IMPORTER class exists

?>