<?php

// transform otxdata shortcode in listing of dataset versions linked to hosting source

// Prohibit direct script loading
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

$otx_datalinks_canon = array( 
	'google webpage' => 	'Google Sheet published to web (fast, resilient)',
	'excel' 		 => 	'Microsoft Excel workbook (primary version)',
	'openoffice'	 =>		'Apache OpenOffice Calc spreadsheet',
	'csv' 			 => 	'Comma-separated-value text file',
	'google sheet' 	 => 	'Google Sheet (downloadable)',
	'excel (local)'	 =>		'Microsoft Excel workbook (secondary, use only if above fail)',
	);


class otx_show_datalinks {

	function __construct() {
		add_shortcode('otxdata', array(&$this,'list_datalinks'));
	}

	function list_datalinks($atts, $content = null) {
		$list = '<div class="datalinks">' . __( 'Get dataset in form/version:', 'otx-datalinks' ) . '<ul>';	
		$fetched = otx_get_datalinks( get_the_ID() );
		if ( !$fetched ) return $list . '</ul></div><div class="datalinks-warnings">' . __( 'No dataset specified', 'otx-datalinks' ) . '</div>';
		$links = $fetched->links;
		global $otx_datalinks_canon;		
		foreach ( $links as $version => $url ) {
 			$label = isset( $otx_datalinks_canon[ $version ] ) ? $otx_datalinks_canon[ $version ] : $version;
			$list .= '<li><a href="' . esc_url( $url ) . '">' . sanitize_text_field( __( $label, 'otx-datalinks' ) ) . '</a></li>';
		}
		$list .= '</ul></div>';
		$warnings = '';
		if ( $fetched->duplicate ) :
			$warnings .= sprintf( __( '(%1$d duplicate dataset version(s), such as %2$s, exists among datalinks)', 'otx-datalinks' ), $fetched->duplicate_count, $fetched->duplicate );
		endif;
		if ( $fetched->skipped ) :
			$warnings .= sprintf( __( '(%1$d datalink(s) without @ delimitor, such as %2$s, exists among datalinks)', 'otx-datalinks' ), $fetched->skipped_count, $fetched->skipped );
		endif;
		if ( $warnings ) { $list .= '<div class="datalinks-warnings">' . $warnings . '</div>'; }
		return $list;
	}	

}

new otx_show_datalinks();

class Otx_Datalinks_Fetch {
	public $links = array();
	public $metacount = 0;
	public $duplicate_count = 0;
	public $duplicate = '';
	public $skipped_count = 0;
	public $skipped = '';
}

// the datalinks importer also uses this function
function otx_get_datalinks( $id ) {
	$fetched = new Otx_Datalinks_Fetch();
	$linkstrings = get_post_custom_values( 'otx-datalinks', $id );
	if ( !$linkstrings ) :
		return;
	else:
		$fetched->metacount = count( $linkstrings );
		$links = array(); 
		foreach ($linkstrings as $linkstring) {
			$delpos = strpos( $linkstring, '@' );
			if ( FALSE === $delpos ) :
				$fetched->skipped_count++;
				$fetched->skipped = $linkstring;			
			else:
				$version = trim( substr( $linkstring, 0, $delpos ) );
				$url = trim( substr( $linkstring, $delpos+1 ) );
				if ( isset( $links[ $version ] ) ) :
					$fetched->duplicate_count++;
					$fetched->duplicate = $linkstring;
				else:
					$links[ $version ] = $url;
				endif;
			endif;
		}
		global $otx_datalinks_canon;
		$links_ordered = array_intersect( array_merge( $otx_datalinks_canon, $links), $links );
		$fetched->links = $links_ordered;
		return $fetched;	
	endif;
}

?>