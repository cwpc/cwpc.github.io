<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package ortext
 */

function otx_post_type_labels ( $pta, $sep = ', ' ) {
	$ptext = '';
	$pta = (array) $pta;
	foreach ( $pta as $pt ) {
		if ( $ptext !='' ) $ptext .= $sep;
		$pto = get_post_type_object( $pt );
		if ( $pto ) $ptext .= $pto->labels->name;		
	}
	return $ptext;
}


if ( ! function_exists( 'otx_integrated_paging_nav' ) ) :
function otx_integrated_paging_nav( $fp ) {
	?>
	<nav class="navigation paging-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Posts navigation', 'ortext' ); ?></h1>
		<div class="nav-links">
			<?php if ( get_next_posts_link() ) : ?>
				<div class="nav-previous-i"><?php next_posts_link( __( 'Forward<span class="meta-nav">&rarr;</span>', 'ortext' ) ); ?></div>
			<?php elseif ( $apost = get_next_post() ): 
				$acat = get_the_category( $apost->ID );
				$acat = $acat[0]->term_id; ?>
				<div class="nav-previous-i"><a href="<?php echo esc_url( get_category_link( $acat ) ); ?>" title="Category Name"><?php _e( 'Next section<span class="meta-nav">&rarr;</span>', 'ortext' ) ?></a></div>			
			<?php endif; ?>
			
			<?php if ( get_previous_posts_link() ) : ?>
				<div class="nav-next-i"><?php previous_posts_link( __( '<span class="meta-nav">&larr;</span>Backward', 'ortext' ) ); ?></div>
			<?php elseif ( $apost = otx_get_previous_post( $fp ) ): 
				$acat = get_the_category( $apost->ID );
				$acat = $acat[0]->term_id; ?>
				<div class="nav-next-i"><a href="<?php echo esc_url( get_category_link( $acat ) ); ?>" title="Category Name"><?php _e( '<span class="meta-nav">&larr;</span>Previous section', 'ortext' ) ?></a></div>	
			<?php endif; ?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'ortext_paging_nav' ) ) :
function ortext_paging_nav() {
	// Don't print empty markup if there's only one page.
	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {return;}
	?>
	<nav class="navigation paging-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Posts navigation', 'ortext' ); ?></h1>
		<div class="nav-links">
			<?php if ( get_next_posts_link() ) : ?>
				<div class="nav-previous"><?php next_posts_link( __( 'More<span class="meta-nav">&rarr;</span>', 'ortext' ) ); ?></div>	
			<?php endif; ?>
			
			<?php if ( get_previous_posts_link() ) : ?>
				<div class="nav-next"><?php previous_posts_link( __( '<span class="meta-nav">&larr;</span>Prior', 'ortext' ) ); ?></div>
			<?php endif; ?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

/* adapted from http://wordpress.stackexchange.com/questions/55259/get-previous-next-posts-by-post-id */
function otx_get_previous_post( $post_id ) {
    // Get a global post reference since get_adjacent_post() references it
    global $post;

    // Store the existing post object for later so we don't lose it
    $oldGlobal = $post;

    // Get the post object for the specified post and place it in the global variable
    $post = get_post( $post_id );

    // Get the post object for the previous post
    $previous_post = get_previous_post();

    // Reset our global object
    $post = $oldGlobal;

    return $previous_post;
}


if ( ! function_exists( 'ortext_post_nav' ) ) :
/**
 * Display navigation to next/previous post when applicable.
 */
function ortext_post_nav() {
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}
	?>
	<nav class="navigation post-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'ortext' ); ?></h1>
		<?php
		if ( get_post_type() == 'post' ) : 
			echo '<div class="nav-links">';
			previous_post_link( '<div class="nav-previous">%link</div>', _x( '<span class="meta-nav">&larr;</span>&nbsp;%title', 'Previous post link', 'ortext' ) ); 
			next_post_link(     '<div class="nav-next">%link</div>',     _x( '%title&nbsp;<span class="meta-nav">&rarr;</span>', 'Next post link',     'ortext' ) );
		else:
			$pt = get_post_type_object( get_post_type() )->labels->name;
			echo '<div class="nav-links-nd"><div class="nav-nd-title">' . __( 'In Series of ', 'ortext' ) . $pt . '</div>';
			previous_post_link( '<div class="nav-previous">%link</div>', _x( '%title', 'Previous post link', 'ortext' ) ); 
			next_post_link( '<div class="nav-next">%link</div>', _x( '%title', 'Next post link', 'ortext' ) );	
		endif; ?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'ortext_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function ortext_posted_on() {
	$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string .= '<time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	$posted_on = sprintf(
		_x( 'Posted on %s', 'post date', 'ortext' ),
		'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
	);
/*
	$byline = sprintf(
		_x( 'by %s', 'post author', 'ortext' ),
		'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
	);
	echo '<span class="posted-on">' . $posted_on . '</span><span class="byline"> ' . $byline . '</span>';
*/
	echo '<span class="posted-on">' . $posted_on . '</span>';
}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function ortext_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'ortext_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,

			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'ortext_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so ortext_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so ortext_categorized_blog should return false.
		return false;
	}
}

function otx_refs_search_form( $form ) {
	$form = str_replace( '</form>', '<input type="hidden" value="refs" name="post_type" id="post_type" /></form>', $form );
	$form = str_replace( 'type="search"', 'type="search" id="refs-search-field"', $form );
	return $form;
}

// otx_the_category works like the_category() outside of the loop
function otx_the_category( $separator ) {
	$qc = get_query_var( 'cat' );
	if ( isset( $qc ) ) :
		echo get_category_parents( $qc, TRUE, $separator );
	endif;
}

/**
 * Flush out the transients used in ortext_categorized_blog.
 */
function ortext_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'ortext_categories' );
}
add_action( 'edit_category', 'ortext_category_transient_flusher' );
add_action( 'save_post',     'ortext_category_transient_flusher' );


function ortext_secondary_menu() {
    if ( has_nav_menu( 'secondary' ) ) {
		wp_nav_menu(
			array(
				'theme_location'  => 'secondary',
				'container'       => 'div',
				'container_id'    => 'menu-secondary',
				'container_class' => 'menu-secondary',
				'menu_id'         => 'menu-secondary-items',
				'menu_class'      => 'menu-items',
				'depth'           => 1,				
				'fallback_cb'     => '',
			)
	);
    }
}


