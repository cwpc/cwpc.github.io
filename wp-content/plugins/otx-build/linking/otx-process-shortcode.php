<?php
// Prohibit direct script loading
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

class otx_process_shortcode {

	function __construct() {
		add_shortcode('otx', array(&$this,'process_shortcode'));
		add_filter('the_excerpt', 'do_shortcode', 11);
	}

	function process_shortcode($atts, $content = null) {

		$key_extended = $atts[0];		
		$key_array = explode( '@', $key_extended, 2 );
		$key = $key_array[0];	
		$ref_point = '';
		if ( isset($key_array[1]) ) { $ref_point = $key_array[1]; }
		
		$qargs = array( 'post_type' => 'any', 
						'meta_key' => 'otx-key', 
						'meta_value' => sanitize_text_field( $key ), 
						);
		$keyposts = new WP_Query( $qargs );

		if ( 0 < $keyposts->post_count ) {
			$post = $keyposts->posts[0];
			$permalink = get_permalink( $post->ID );
			$title = get_the_title( $post->ID );			
			$send_ref = '?otxkey=' . urlencode( $key );
			$content = trim( $content );
			$content = $content ? $content : '^'; 
			if ( '' != $ref_point ) { $send_ref .= '&otxrp=' . urlencode( $ref_point ); }		
			return '<a class="otx-link" title="' . esc_attr( $title) . '" href="' . esc_url( $permalink . $send_ref ) . '">' . $content . '</a>';
		}
		else return $content . '#' . sanitize_text_field( $key_extended ) . '#';
	}

}

new otx_process_shortcode();

?>