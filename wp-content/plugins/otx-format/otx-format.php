<?php
/*
 * Plugin Name: Ortext Formating
 * Plugin URI: http://acrosswalls.org/datasets/formatting-ortext/
 * Description: Ortext presentation formating
 * Author: Communicating with Prisoners Collective
 * Author URI: http://acrosswalls.org/authors/
 * Version: 1.0
 * License: CCO 1.0 Universal
 * License URI: http://creativecommons.org/publicdomain/zero/1.0/legalcode
*/

// Prohibit direct script loading
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

define( 'OTX_SHOW_FACES', TRUE ); // change to TRUE to FALSE to eliminate display of prisoners' faces

class Otx_Format {

	function __construct() {
		add_action('init', array( $this,'otx_register_custom_post_types') );	
		add_filter( 'pre_get_posts', array( $this, 'otx_search_types' ) );
		add_filter( 'pre_get_posts', array( $this, 'otx_tag_types' ) );
		add_filter( 'pre_get_posts', array( $this, 'otx_cats_exclusive' ) );
		add_filter( 'pre_get_posts', array( $this, 'otx_reverse_order' ) );
		add_filter( 'pre_get_posts', array( $this, 'otx_posts_per_page' ) );	
		add_filter( 'get_terms_args', array ( $this, 'otx_x_uncat' ), 10, 2 );
		add_filter( 'the_content', array ( $this, 'otx_ref_text' ) );
		add_filter( 'the_content', array ( $this, 'otx_faces' ) );
		add_filter( 'the_content', array ( $this, 'otx_main_outline' ) );
		add_filter( 'the_content', array ( $this, 'otx_make_outline' ) );
		add_filter( 'the_content', array ( $this, 'otx_topic_cloud' ) );
		add_filter( 'the_content', array ( $this, 'otx_dataset_list' ) );		
		add_filter( 'wp_nav_menu_items', array ( $this, 'otx_context_sensitive_links' ) );		
	}
	
	function otx_search_types( $query ) {
		// page-search-references hooks and unhooks to get_search_form function (below) otx_refs_search_form
		$pt = $query->get( 'post_type' );
	    if ( is_search() &&  ( $pt != 'refs' ) && !is_admin() && $query->is_main_query() && empty( $query->query_vars['suppress_filters'] ) ) :
			$query->set( 'post_type', array('post', 'notes', 'statistics', 'datasets', 'page' ) );
	    endif;
		return $query;
	}

	function otx_tag_types( $query ) {
		if( is_tag() && !is_admin() && $query->is_main_query() && empty( $query->query_vars['suppress_filters'] ) ) :
			$query->set( 'post_type', array( 'post', 'notes', 'statistics', 'datasets' ) );
		endif;
		return $query;
	}		


// exclude posts in child categories in category listing	
	function otx_cats_exclusive( $query )	{
		if ( is_admin() || !$query->is_main_query() || !empty( $query->query_vars['suppress_filters'] ) ) { return $query; }	
		if ( is_category() ) :
			$cat_no_kids = $query->get( 'cat' );
			$cat_no_kids2 = get_category_by_slug( $query->get( 'category_name' ) );
			$cat_no_kids = ($cat_no_kids) ? $cat_no_kids : $cat_no_kids2->term_id;
			$query->set( 'category__in', array ($cat_no_kids) );
		endif;
		return $query;
	}		

	function otx_reverse_order( $query ) {
		if ( !is_post_type_archive( 'news' ) && $query->is_main_query() && empty( $query->query_vars['suppress_filters'] ) ) :
			$query->set( 'order', 'asc' );
		endif;
		return $query;
	}

	function otx_posts_per_page( $query ) {
		if ( is_admin() || !$query->is_main_query() || !empty( $query->query_vars['suppress_filters'] ) ) { return $query; }
		$pt = $query->get( 'post_type' );
		if ( is_post_type_archive( 'news' ) ) :
			$query->set( 'posts_per_archive_page', 10 );
			return $query;		
		elseif ( is_category() && ( '' == $pt || 'post' == $pt ) ) :
			$query->set( 'posts_per_archive_page', 3 );
		else:
			if ( is_tag() ) : 
				$query->set( 'posts_per_archive_page', -1 ); 
			else: 
				$query->set( 'posts_per_archive_page', 10 ); 
			endif;
		endif;
		return $query;
	}


// exclude uncategorized category from front-end (usually cat id 1)
	function otx_x_uncat( $args, $taxonomies ) {
		if ( is_admin()) return $args;
		$uncatid = get_term_by( 'slug', 'uncategorized', 'category');	
		if ( $uncatid ) $args['exclude'] = array( $uncatid->term_id);
		return $args;
	}
	
	// add pointer text for in-line references
	function otx_ref_text( $content ) {
		if ( !is_single() ) return $content;
		if ( isset($_GET['otxrp']) && $ref_text =trim( $_GET['otxrp'] ) ) {
			$ref_text = sanitize_text_field( $ref_text );
			$content = '<div class="otx-ref-point">Reference point: ' . $ref_text . '</div>' . $content;
			return $content;
		}
		else return $content;
	}
	
	// add faces
	function otx_faces( $content ) {
		if ( !OTX_SHOW_FACES ) return $content;
		$pt = get_post_type();
		if ( !$pt || 'page' == $pt || 'news' == $pt ) return $content;
		$face = $this->random_face();
		if ( $face ) : return $face . $content;
		else: return $content;
		endif;
	}		
		
	// code below has benefited from Scott Reilly's Random File WordPress plugin		
	function random_face() {
		$dir = ABSPATH . 'wp-content/uploads/faces';
		if ( ! file_exists( $dir ) ) return;
		
		$handle = @opendir( $dir );
		if ( FALSE === $handle ) return;
		$i = -1;
		$files = array();
		while ( FALSE != ( $file = readdir( $handle ) ) ) {
			if ( is_file( $dir . '/' . $file ) && strrpos( $file, '.jpg', -4 ) ) {
				$files[] = $file;
				++$i;
				}
		}
		closedir( $handle );
		if ( empty( $files ) ) {
			return;
		}			

		mt_srand( (double) microtime() * 1000000 );
		$rand = mt_rand( 0, $i );
		$face_file = $files[ $rand ];
		$url = get_option( 'siteurl' ) . '/wp-content/uploads/faces/' . urlencode( $face_file );
		$face_html = '<img class="aligncenter otx-face" alt="face of a prisoner" src="' . esc_url( $url ) . '"></br>';
		return $face_html;
	}

	// add outline to bottom of otx-category->make pages
	function otx_main_outline( $content ) {
		$otxcat = get_post_meta( get_the_ID(), 'otx-category', TRUE);
		if ( 'main-outline' == $otxcat ) :
			global $post;
			$page_slug = get_post( $post )->post_name;
			$outline_type = explode( '-', $page_slug );
			if ( 'outline' == $outline_type[0] ) :
				array_shift( $outline_type );
			else :
				$outline_type = array( 'post' );
			endif;		
			$outline = $this->otx_list_categories( $outline_type, array( 'title_li' => '' ) ); 		
			$outline .= wp_list_pages( array(
											'include' 	=> array( get_page_by_path('topic-index')->ID, get_page_by_path('search-references')->ID ),
											'title_li'	=>'',
											'echo' 		=> 0,
											)
									);
			$outline = '<div class="outline-content"><ul>' . $outline . '</ul></div>';
			$content = $outline . $content;
		endif;
		return $content;
	}

	function otx_list_categories( $post_type_array, $args ) {
		$args['echo'] = 0;		
		$sp = array( 'post' );
		if ( $post_type_array == $sp ) : 
			$catlist = wp_list_categories( $args );
		else: 
			$catlist = wp_list_categories( $args );
			$catquery = '/?post_type[]=' . implode( '&post_type[]=', $post_type_array ) . '"';
			$catlist = str_replace( '/"', $catquery, $catlist );
		endif;
		return $catlist;
	}	

	
	// add outline to bottom of otx-category->make pages
	function otx_make_outline( $content ) {
		$otxcat = get_post_meta( get_the_ID(), 'otx-category', TRUE);
		if ( 'make-outline' == $otxcat ) :
			$pl_args = array( 'meta_key'	=> 'otx-category',
					  'meta_value'	=> 'make-outline',
					  'sort_column'	=> 'post_date',
					  'sort_order' 	=> 'ASC',
					  'title_li'	=> '<h3>' . __( 'Learn more about making an ortext:', 'ortext' ) . '</h3>',
					  'echo'		=> 0,
					);
			$outline = wp_list_pages( $pl_args );
			$outline = '<div class="make-outline">' . $outline . '</div>';
			$content = $content . $outline;
		endif;
		return $content;
	}

	function otx_topic_cloud( $content ) {
		$otxcat = get_post_meta( get_the_ID(), 'otx-category', TRUE);
		if ( 'topic-cloud' == $otxcat ) :
			$cloud = wp_tag_cloud( array( 
										'smallest'	=> 16, 
										'largest'	=> 16, 
										'unit'		=> 'px',
										'number'	=> 0,
										'echo' 		=> 0,
										'separator'=>'<span class="tag-separator">, </span>',
										) 
								  );
			$cloud = '<div class="tag-cloud">' . $cloud . '</div>';
			$content = $cloud . $content;
		endif;
		return $content;
	}

	function otx_dataset_list( $content ) {
		$otxcat = get_post_meta( get_the_ID(), 'otx-category', TRUE);
		if ( 'dataset-list' == $otxcat ) :
			$uncatid = get_category_by_slug( 'uncategorized' );
			$uncatid = $uncatid ? array( $uncatid->term_id ) : array();
			$ds_query_args = array(
									'post_type' 				=> 'datasets',	
									'posts_per_archive_page' 	=> -1,
									'orderby' 					=> 'date',
									'order' 					=> 'ASC',
									'category__not_in' 			=> $uncatid,
									);		
			$ds_query = new WP_Query( $ds_query_args );
			$list = '';
			if ( $ds_query->have_posts() ) : 
				while ( $ds_query->have_posts() ) : $ds_query->the_post(); 
					$pclass = 'class ="' . implode( ' ', get_post_class() ) . '"';
					$list .= '<article id="post-' . get_the_ID() . '" ' . $pclass . '>';
					$list .= '<header class="dl-header">';
					$list .= the_title( sprintf( '<h1 class="excerpt-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>', FALSE );
					$list .= '</header>';
					$list .= '<div class="entry-summary">' . get_the_excerpt() . '</div>';
					$list .= '<footer class="dl-footer"><div class="dl-meta">';
					$list .= __( 'Dataset id: ', 'otx-format') . $ds_query->post->post_name;
					$list .= '</div></footer></article>';
				endwhile; 
			endif;
			$content .= $list;
			wp_reset_postdata(); 
		endif;
		return $content;
	}


	
	function otx_context_sensitive_links( $items ) {
		$cc = $this->otx_context_category( array( 'notes' ) );
		if ( FALSE !== $cc ) :
			$cslink = get_category_link( $cc );
			$cslink .= '?post_type=notes';	
		else:
			$cslink = $this->otx_get_page( 'outline-notes' );		
		endif;
		$items = str_replace( 'http://context-notes' , $cslink , $items);	
		
		$cc = $this->otx_context_category( array( 'statistics', 'datasets' ) );
		if ( FALSE !== $cc ) :
			$cslink = get_category_link( $cc );		
			$cslink .= '?post_type[]=statistics&post_type[]=datasets';		
		else:
			$cslink = $this->otx_get_page( 'list-datasets' );
		endif;
		$items = str_replace( 'http://context-data' , $cslink , $items);		

		return ( $items );	
	}
	
	function otx_context_category( $posttype ) {
		$qpt = (array) get_query_var( 'post_type' );	
		$qc = get_query_var( 'cat' );
		if ( is_tag() || ( is_post_type_archive() && '' == $qc ) ) {return FALSE;}
		
		global $wp_query;
		$posts = $wp_query->posts;
		if ( !isset( $posts[0] ) ) :
			return ( $qc == '' || $qpt == $posttype ) ? FALSE : $qc;
		endif;
		$pid = $posts[0]->ID;
		$cid = get_the_category( $pid );
		if ( !$cid ) {return FALSE; };
		$cid = $cid[0]->cat_ID;	

		return ( $cid == $qc && $qpt == $posttype ) ? FALSE : $cid;
	}
	

	function otx_get_page( $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page ) : return get_page_link( $page->ID );
		else: return;
		endif;
	}
	
	function otx_register_custom_post_types() {
		register_post_type('refs', array(
		'label' => 'References',
		'description' => 'Bibliographic references imported into WordPress.',
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'capability_type' => 'post',
		'map_meta_cap' => true,
		'hierarchical' => false,
		'rewrite' => array('slug' => 'refs', 'with_front' => true),
		'query_var' => true,
		'has_archive' => true,
		'menu_position' => 8,
		'supports' => array('title','editor','excerpt','trackbacks','custom-fields','comments','revisions','thumbnail','author','page-attributes'),
		'taxonomies' => array('post_tag'),
		'labels' => array (
		  'name' => 'References',
		  'singular_name' => 'Reference',
		  'menu_name' => 'References',
		  'add_new' => 'Add Reference',
		  'add_new_item' => 'Add New Reference',
		  'edit' => 'Edit',
		  'edit_item' => 'Edit Reference',
		  'new_item' => 'New Reference',
		  'view' => 'View Reference',
		  'view_item' => 'View Reference',
		  'search_items' => 'Search References',
		  'not_found' => 'No References Found',
		  'not_found_in_trash' => 'No References Found in Trash',
		  'parent' => 'Parent Reference',
		)
		) ); 

		register_post_type('notes', array(
		'label' => 'Notes',
		'description' => 'Notes describing and citing references and sources.',
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'capability_type' => 'post',
		'map_meta_cap' => true,
		'hierarchical' => false,
		'rewrite' => array('slug' => 'notes', 'with_front' => true),
		'query_var' => true,
		'has_archive' => true,
		'menu_position' => 6,
		'supports' => array('title','editor','excerpt','trackbacks','custom-fields','comments','revisions','thumbnail','author','page-attributes'),
		'taxonomies' => array('category','post_tag'),
		'labels' => array (
		  'name' => 'Notes',
		  'singular_name' => 'Note',
		  'menu_name' => 'Notes',
		  'add_new' => 'Add Note',
		  'add_new_item' => 'Add New Note',
		  'edit' => 'Edit',
		  'edit_item' => 'Edit Note',
		  'new_item' => 'New Note',
		  'view' => 'View Note',
		  'view_item' => 'View Note',
		  'search_items' => 'Search Notes',
		  'not_found' => 'No Notes Found',
		  'not_found_in_trash' => 'No Notes Found in Trash',
		  'parent' => 'Parent Note',
		)
		) ); 

		register_post_type('news', array(
		'label' => 'News',
		'description' => 'Updates, announcements, and other news for this ortext.',
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'capability_type' => 'post',
		'map_meta_cap' => true,
		'hierarchical' => false,
		'rewrite' => array('slug' => 'news', 'with_front' => true),
		'query_var' => true,
		'has_archive' => true,
		'menu_position' => 22,
		'supports' => array('title','editor','excerpt','trackbacks','custom-fields','comments','revisions','thumbnail','author','page-attributes'),
		'labels' => array (
		  'name' => 'News',
		  'singular_name' => 'News',
		  'menu_name' => 'News',
		  'add_new' => 'Add News',
		  'add_new_item' => 'Add New News',
		  'edit' => 'Edit',
		  'edit_item' => 'Edit News',
		  'new_item' => 'New News',
		  'view' => 'View News',
		  'view_item' => 'View News',
		  'search_items' => 'Search News',
		  'not_found' => 'No News Found',
		  'not_found_in_trash' => 'No News Found in Trash',
		  'parent' => 'Parent News',
		)
		) ); 
		
		
		register_post_type('statistics', array(
		'label' => 'Statistics',
		'description' => 'High-level description and analysis of datasets and statistics.',
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'capability_type' => 'post',
		'map_meta_cap' => true,
		'hierarchical' => false,
		'rewrite' => array('slug' => 'statistics', 'with_front' => true),
		'query_var' => true,
		'has_archive' => true,
		'menu_position' => 7,
		'supports' => array('title','editor','excerpt','trackbacks','custom-fields','comments','revisions','thumbnail','author','page-attributes'),
		'taxonomies' => array('category','post_tag'),
		'labels' => array (
		  'name' => 'Statistics',
		  'singular_name' => 'Statistics',
		  'menu_name' => 'Statistics',
		  'add_new' => 'Add Statistics',
		  'add_new_item' => 'Add New Statistics',
		  'edit' => 'Edit',
		  'edit_item' => 'Edit Statistics',
		  'new_item' => 'New Statistics',
		  'view' => 'View Statistics',
		  'view_item' => 'View Statistics',
		  'search_items' => 'Search Statistics',
		  'not_found' => 'No Statistics Found',
		  'not_found_in_trash' => 'No Statistics Found in Trash',
		  'parent' => 'Parent Statistics',
		)
		) ); 
		
		register_post_type('datasets', array(
		'label' => 'Datasets',
		'description' => 'Structured description of single dataset, with links to external host of dataset versions.',
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'capability_type' => 'post',
		'map_meta_cap' => true,
		'hierarchical' => false,
		'rewrite' => array('slug' => 'datasets', 'with_front' => true),
		'query_var' => true,
		'has_archive' => true,
		'menu_position' => 8,
		'supports' => array('title','editor','excerpt','trackbacks','custom-fields','comments','revisions','thumbnail','author','page-attributes'),
		'taxonomies' => array('category','post_tag'),
		'labels' => array (
		  'name' => 'Datasets',
		  'singular_name' => 'Dataset',
		  'menu_name' => 'Datasets',
		  'add_new' => 'Add Dataset',
		  'add_new_item' => 'Add New Dataset',
		  'edit' => 'Edit',
		  'edit_item' => 'Edit Dataset',
		  'new_item' => 'New Dataset',
		  'view' => 'View Dataset',
		  'view_item' => 'View Dataset',
		  'search_items' => 'Search Datasets',
		  'not_found' => 'No Datasets Found',
		  'not_found_in_trash' => 'No Datasets Found in Trash',
		  'parent' => 'Parent Dataset',
		)
		) ); 		
		
	}
			
}
		
new Otx_Format();

?>
