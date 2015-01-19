<?php
/*
 * Ortext utility functions: USE WITH CAUTION!!!
 *
 * These utilities are run via a "Run utility" button available via the admin page Settings -> Ortexts
 * To made the "Run utilty" button visable, you must have defined OTX_UTL as TRUE at the top of otx-build.php
*/


// Prohibit direct script loading
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if ( OTX_UTL ) : 
class Otx_Util {
	function util_fire() {
// the subsequent line specifies the utility function to be fired; comment out when finished		
//		$this->permalinks_to_keylinks();
//		$this->keylinks_to_permalinks();
//		$this->search_replace();
		
		error_log('Done running utility ' );
		}

// for added protection, comment out the datebase write code in your utility when finished

// the custom post type for TablePress is tablepress_table
// TablePress stores the JSON-encoded table as the post content
// the flipper functions (keylinks_to_permalinks and permalinks_to_keylinks) handle JSON-encoded post content	

	function permalinks_to_keylinks() {
		$target_type = 'post';		// post_type to be flipped
		$database_write = FALSE;  	// set to TRUE to write the flipped entry to the database		
		error_log( "\n\nRunning permalinks_to_keylinks utility for type: " . $target_type );
		$all_posts = get_posts( array (
				'posts_per_page' => -1,
				'post_type' => $target_type,
				)
			);	
		$post_types = get_post_types();
		$post_types_json = array_fill_keys( array_keys( $post_types ), -1 );
		
		$count = 1;
		$db_util_writes = 0;
		foreach ( $all_posts as $single ) {
			$content = $single->post_content;
			if ( '' == $content ) { continue; }
			$new_content = $content;

//			check if post_type content is json encoded
			$post_type = $single->post_type;
			if ( -1 == $post_types_json[ $post_type ] ) :
				if ( json_decode( $content ) ) : 
					$post_types_json[ $post_type ] = 1;
				else:
					$post_types_json[ $post_type ] = 0;
				endif;
			endif;			

			$flips = 0;
			$tp = -3;		
			while ( FALSE !== $tp = strpos( $new_content, '<a class=', $tp + 3) ) {  // class used in keyed link
				$flips++;
				$hle = strpos( $new_content, '>', $tp );
				if ( !$hle ) :
					error_log( 'badly formed hyperlink: ' . $single->post_title . ' * ' . $single->post_type ); 
					continue;
				endif;
				
				$qss = strpos( $new_content, '/?', $tp);
				if ( !$qss || $hle < $qss ) { continue; }  // this link has no query string
				$qss += 2;
				$qse = strpos( $new_content, '"', $qss );
				if ( !$qse || $hle < $qse ) :
					error_log( 'badly formed hyperlink: ' . $single->post_title . ' * ' . $single->post_type ); 
					continue;
				endif;
				$qse = $qse - 1 - $post_types_json[ $post_type ];  // JSON has \ before "
				$qs = substr( $new_content, $qss,  $qse - $qss + 1 );

				parse_str( $qs, $qv );
				if ( isset( $qv[ 'otxkey' ] ) ) :
					$otk = $qv[ 'otxkey' ];
				else:
					continue; // this link not a keyed link
				endif;
				if ( isset( $qv[ 'otxrp' ] ) ) :
					$rp = $qv[ 'otxrp' ];
				else:
					$rp = '';
				endif;				
								
				$hlc = strpos( $new_content, '/a>', $hle );
				if ( !$hlc ) :
					error_log( 'badly formed hyperlink: ' . $single->post_title . ' * ' . $single->post_type ); 
					continue;
				endif;	
							
				$hlink = substr ( $new_content, $tp, $hlc + 3 - $tp );
				$scc = substr( $new_content, $hle + 1, $hlc - $hle - 2 - $post_types_json[ $post_type ] ); // JSON has \ before /
				$scc = trim( $scc );
				
				$rptext = $rp ? '@' . urldecode( $rp ) : '';
				$scctext = ( $scc == '^' ) ? ' ' : $scc;
				if ( $post_types_json[ $post_type ] ) :
					$osc = '[otx \"' . urldecode( $otk ) . $rptext . '\"]' . $scctext . '[\/otx]';
				else:
					$osc = '[otx "' . urldecode( $otk ) . $rptext . '"]' . $scctext . '[/otx]';
				endif;
				
//				error_log( $hlink . ' => ' . $osc );										
				$new_content = substr_replace( $new_content, $osc, $tp, strlen( $hlink ) );					
			}			

			$new_content = $this->triple_slashes( $new_content ) ;
						
//			error_log( 'entry ' + $count . ', flips to keylinks ' . $flips . ' : ' . $single->post_title . ' * ' . $single->post_type ); 	

			if ( $database_write ) :
				if ( 0 < $flips ) :
					wp_update_post( array(
							'ID' => $single->ID,
							'post_content' => $new_content,
							) );
					$db_util_writes++;
				endif;
			endif;
			$count++;
//			if ( 3 < $count ) break;	
		}			
	if ( $database_write ) { error_log( 'Database changed for ' . $db_util_writes . ' records.' ); } 
	}

	
	function keylinks_to_permalinks() {
		$target_type = 'datasets';		// post_type to be flipped
		$database_write = TRUE;  	// set to TRUE to write the flipped entry to the database	
		error_log( "\n\nRunning keylinks_to_permalinks utility for type: " . $target_type );
		$all_posts = get_posts( array (
				'posts_per_page' => -1,
				'post_type' => $target_type,
				)
			);	
		$post_types = get_post_types();
		$post_types_json = array_fill_keys( array_keys( $post_types ), -1 );
		
		$count = 1;
		$db_util_writes = 0;
		foreach ( $all_posts as $single ) {
			$content = $single->post_content;
			if ( '' == $content ) { continue; }
			$new_content = $content;
			
//			check if post_type content is json encoded
			$post_type = $single->post_type;
			if ( -1 == $post_types_json[ $post_type ] ) :
				if ( json_decode( $content ) ) : 
					$post_types_json[ $post_type ] = 1;
				else:
					$post_types_json[ $post_type ] = 0;
				endif;
			endif;
			
			$flips = 0;
			$tp = -5;		
			while ( FALSE !== $tp = strpos( $new_content, '[otx ', $tp + 5 ) ) {
				$ks = $tp + 6 + $post_types_json[ $post_type ];  // json has " as \"				
				if ( $post_types_json[ $post_type ] ) :
					$kl = strcspn( $new_content, '@\\', $ks );  // have to escape \ in php string
				else:		
					$kl = strcspn( $new_content, '@"', $ks );
				endif;
				if ( !$kl ) :
					error_log( 'bad key position/field: ' . $single->post_title . ' * ' . $single->post_type ); 
					continue;
				endif;
				$otk = substr( $new_content, $ks, $kl );
				
				$tp2 = strpos( $new_content, '"]', $ks );
				if ( !$tp2 ) :
					error_log( 'initial otx code open: ' . $single->post_title . ' * ' . $single->post_type ); 
					continue;
				endif;	
				$tp2a = $tp2 - $post_types_json[ $post_type ];  // account for escaped " in json				
				if ( $tp2a > $ks + $kl + 1 ) : 
					$rp = substr( $new_content, $ks + $kl + 1, $tp2a - $ks - $kl - 1 ) ; 
				else: 
					$rp = '';
				endif;
					
				$tp3 = strpos( $new_content, '/otx]', $tp2 );
				if ( !$tp3 ) :
					error_log( 'no closing code: ' . $single->post_title . ' * ' . $single->post_type ); 
					continue;
				endif;	
				$osc = substr ( $new_content, $tp, $tp3 + 5 - $tp );
				$scc = trim( substr( $new_content, $tp2 + 2, $tp3 - 1 - $post_types_json[ $post_type ] - $tp2 - 2 ) );
				$scc = $scc ? $scc : '^';	
				
				$qargs = array( 
					'post_type' => 'any', 
					'meta_key' => 'otx-key', 
					'meta_value' => sanitize_text_field( $otk ), 
					);
				$keyposts = new WP_Query( $qargs );	
				if  ( 0 == $keyposts->post_count ) :
					error_log( 'no post for key ' . $otk . ': ' . $single->post_title . ' * ' . $single->post_type ); 
					continue;
				elseif ( 1 < $keyposts->post_count ) :
					error_log( 'multiple posts for key ' . $otk . ': ' . $single->post_title . ' * ' . $single->post_type ); 
					continue;
				endif;
				$post = $keyposts->posts[0];
				$url = get_permalink( $post->ID );
				$title = get_the_title( $post->ID );
				$ref_var = $rp ? '&otxrp=' . urlencode( $rp ) : '';
				$url = esc_url( $url );
				if ( $post_types_json[ $post_type ] ) :
					$url = str_replace( '/', '\\/', $url );
					$title_json = substr( json_encode( $title ), 1, -1 );
					$hlink = '<a class=\"otx-link\" title=\"' . esc_attr( $title_json ) . '\" href=\"' . $url . '?otxkey=' . urlencode( $otk )  . $ref_var  . '\">' . $scc . '<\\/a>';
				else:
					$hlink = '<a class="otx-link" title="' . esc_attr( $title ) . '" href="' . $url . '?otxkey=' . urlencode( $otk )  . $ref_var  . '">' . $scc . '</a>';
				endif;
							
//				error_log( $osc . ' => ' . $hlink );
				
				$new_content = substr_replace( $new_content, $hlink, $tp, strlen( $osc ) );	
				$flips++;					
			}
			
			$new_content = $this->triple_slashes( $new_content ) ;
				
//			error_log( 'entry ' . $count . ', flips to permalinks ' . $flips . ' : ' . $single->post_title . ' * ' . $single->post_type ); 
				
			if ( $database_write ) :
				if ( 0 < $flips ) :
					wp_update_post( array(
							'ID' => $single->ID,
							'post_content' => $new_content,
							) );
					$db_util_writes++;
				endif;
			endif;
			$count++;
//			if ( 3 < $count ) break;	
		}			
	if ( $database_write ) { error_log( 'Database changed for ' . $db_util_writes . ' records.' ); }
	}
	
	
/* converts single slash to triple slash
essential for preparing JSON-format $content to be written with wp_update_post

JSON backslashed characters are quotation mark ("), backslash (\), forward slash (/), 
backspace (\b), formfeed (\f), newline (\n), carriage return (\r), horizontal tab (\t), 
and four-hexidecimal digits specify character (\u) 

wp_update_post runs stripslashes on content of post
stripslashes eliminates \ and \\, and converts \\\ to \
	
to preserve JSON encoding, \ must be replaced with \\\

also needed to preserve \ in ordinary post
*/
	function triple_slashes( $content ) {
		$nca = str_replace( '\\', '\\\\\\', $content );
		return $nca;	
	}

// find legacy meta-key ortext-date
	function find_meta_key() {
		error_log( "\n\nRunning find_meta utility." );	
		$all_posts = get_posts( array (
				'posts_per_page' => -1,
				'post_type' => 'any',
				)
			);	
		$count = 1;
		foreach ( $all_posts as $single ) {		
			$keys = get_post_custom_keys( $single->ID );
			$ks = implode( '+', $keys );
			if ( FALSE !== strpos( $ks, 'ort' ) ) :
				error_log( 'ortext-date: ' . $single->post_title . ' * ' . $single->post_type );
			endif;
			if ( in_array( 'videourl', $keys) ) :
				error_log( 'videourl: ' . $single->post_title . ' * ' . $single->post_type );
			endif;			
			$count++;
//			if ( $count > 2 ) break;	
		}			
	}

// delete all posts
	function delete_all_posts() {
		$target_type = 'refs';
		error_log( "\n\nRunning delete_all_posts utility." );	
		$all_posts = get_posts( array (
				'posts_per_page' => -1,
				'post_type' => $target_type,
				)
			);	
		$count = 1;
		foreach ( $all_posts as $single ) {		
			wp_delete_post( $single->ID, TRUE );
			$count++;
//			if ( $count > 3 ) break;	
		}			
	}	
	
// delete legacy meta-key ortext-date
	function delete_datalinks_meta() {
		error_log( "\n\nRunning delete_meta utility." );	
		$all_posts = get_posts( array (
				'posts_per_page' => -1,
				'post_type' => 'datasets',
				)
			);	
		$count = 1;
		foreach ( $all_posts as $single ) {		
			delete_post_meta( $single->ID, 'otx-datalinks' );
			$count++;
//			if ( $count > 3 ) break;	
		}			
	}

	
// change dataset shortcode links 		
	function change_otxkeys() {
		error_log( "\n\nRunning change_otxkeys utility." );		
		$all_posts = get_posts( array (
				'posts_per_page' => -1,
				'post_type' => 'any',
				)
			);	
		$count = 1;
		foreach ( $all_posts as $single ) {
			$content = $single->post_content;
			$new_content = $content;

			$search = '[otx "datasets-';
			$tp = strpos( $new_content, $search );			
			while ( FALSE !== $tp ) {
				$osc = substr ( $new_content, $tp, 60 );
				$pid = $tp + 15;
				$lp = strcspn( $new_content, '@"', $pid );
				$vid = (int) substr ( $new_content, $pid, $lp );
				$nsc = '(*** NO CHANGE ***)';
				if ( $vid ) :
					$post = get_post( $vid );
					if ( $post ) : 
						$slug = $post->post_name;
						$new_content = substr_replace( $new_content, $slug, $pid, $lp );	
						$nsc = substr ( $new_content, $tp, 60 );					
					else : 
						error_log( '*** POST NOT FOUND!!!! ***' . $osc );
					endif;
				endif;			
				
//				error_log( $osc . ' => ' . $nsc );
				$tp = strpos( $new_content, $search, $tp+16 );
			}			
			
/*
			if ( 3 > $count ) :
				error_log ( '*** OLD ' . $content );
				error_log ( '*** NEW ' . $new_content );
			endif;
*/

//  CHANGES MADE TO DATABASE HERE ... DOUBLE-CHECK CORRECTNESS BEFORE UNCOMMENTING

/*			
			if ( $content != $new_content ) :
				wp_update_post( array(
						'ID' => $single->ID,
						'post_content' => $new_content,
						) );
			endif;
*/

			$count++;
//			if ( $count > 2 ) break;	
		}			
	}

	
// search and replace 
	function search_replace() {
		$target_type = 'statistics';	
		error_log( "\n\nRunning search_replace utility for " . $target_type );
		$all_posts = get_posts( array (
				'posts_per_page' => -1,
				'post_type' => $target_type,
				)
			);	
		$count = 1;
		foreach ( $all_posts as $single ) {
			$content = $single->post_content;
			$new_content = $content;
			
//			$search = ". \xc2\xa0";
//			$search = "]  ";

//			$search = "\xc2\xa0";
//			$Replac = " ";
	

			$new_content = str_replace( $search, $Replac, $content , $rc );						
			if ( 0 == $rc ) continue; 
			error_log( $count . ', changed ' . $rc . ': ' . $single->post_title . ' * ' . $single->post_type );	
			
// replacement preview; run before uncommenting database write	
			$tp = strpos( $content, $search );			
			while ( FALSE !== $tp ) {
				$lp = $tp - 50;
				$lp = ( $lp < 0 ) ? 0 : $lp;
				error_log( substr( $content, $lp, 100 ) . ' => ' . substr( $new_content, $lp, 100 ) );
				$tp = strpos( $content, $search, $tp+1 );
			}

//  CHANGES MADE TO DATABASE HERE ... DOUBLE-CHECK CORRECTNESS BEFORE UNCOMMENTING
/*
			if ( $content != $new_content ) :
				wp_update_post( array(
						'ID' => $single->ID,
						'post_content' => $new_content,
						) );
			endif;
*/

			$count++;
//			if ( $count > 2 ) break;	// count = number of replaces, not number of records processed
		}			
	}


	
// had used dataset slug as datalinks id; converted to otx-key as better design 	
	function dataset_slug() {
		error_log( "\n\nRunning dataset_slug utility." );		
		$all_posts = get_posts( array (
				'posts_per_page' => -1,
				'post_type' => 'datasets',
				)
			);	
		$count = 1;
		foreach ( $all_posts as $single ) {		
			$slug = $single->post_name;
			$otxkey = get_post_custom_values( 'otx-key', $single->ID );
			error_log( $slug . '; ' . $single->ID . '; ' . $otxkey[0] );
			update_post_meta( $single->ID, 'otx-key', 'datasets-' . $slug );
			$count++;
//			if ( $count > 2 ) break;	
		}			
	}

	
// convert titles to title case
	function to_title_case() {
		error_log( "\n\nRunning to_title_case utility." );		
		$all_posts = get_posts( array (
				'posts_per_page' => -1,
				'post_type' => 'datasets'
				)
			);	
		$count = 1;
		foreach ( $all_posts as $single ) {
			$new_utitle = $this->uwk_title_case( $single->post_title );
	/* REVIEW ACTION HERE */
  			error_log( $count . ': ' . $single->post_title . '==>' . $new_utitle ); 
/* 	
			wp_update_post( array(
				'ID' => $single->ID,
				'post_title' => $new_utitle ) 
				);
*/
			$count++;

			/*		if ( $count > 2 ) break;	*/
		}
	}
	

/* title case adapted from 
 http://wordpress.stackexchange.com/questions/94856/how-to-change-the-case-of-all-post-titles-to-title-case
*/
	
	function uwk_title_case( $string ) {
		 /* Words that should be entirely lower-case */
		 $articles_conjunctions_prepositions = array(
			  'a','an','the',
			  'and','but','or','nor',
			  'if','then','else','when',
			  'at','by','from','for','in',
			  'off','on','out','over','to','into','with',
			  'vs','vs.','of','about','that', 'c.'
		 );
		 /* Words that should be entirely upper-case (need to be lower-case in this list!) */
		 $acronyms_and_such = array(
			 'asap', 'unhcr', 'wpse', 'wtf', 'u.s.', 'us', 'u.k.','uk','ny','u.n.', 'ala'
		 );
		 /* split title string into array of words */
		 $words = explode( ' ', mb_strtolower( $string ) );
		 /* iterate over words */
		 foreach ( $words as $position => $word ) {
			 /* re-capitalize acronyms */
			 if( in_array( $word, $acronyms_and_such ) ) {
				 $words[$position] = mb_strtoupper( $word );
			 /* capitalize first letter of all other words, if... */
			 } elseif (
				 /* ...first word of the title string... */
				 0 === $position ||
				 /* ...or not in above lower-case list*/
				 ! in_array( $word, $articles_conjunctions_prepositions ) 
			 ) {
				 $words[$position] = ucwords( $word );
			 }
		 }         
		 /* re-combine word array */
		 $string = implode( ' ', $words );
		 /* return title string in title case */
		 return $string;
	}
	
}

$otx_util = new Otx_Util();
endif;
	
?>