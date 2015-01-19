<?php
/**
 * The template part for displaying a message that posts cannot be found.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package ortext
 */
?>

<section class="no-results not-found">
	<header class="page-header">
		<h1 class="page-title"><?php _e( 'Nothing Found', 'ortext' ); ?></h1>
	</header><!-- .page-header -->

	<div class="page-content">
		<?php 
		$pt = get_query_var( 'post_type' );
		if ( is_search() ) : ?>
			<p><?php _e( 'Sorry, but nothing matched your search terms.  Try different search keywords, or browse the links on the left of the black navigation bar above.', 'ortext' ); ?></p>
		<?php elseif ( $pt && $pt != 'post' ) :
			$ptn = otx_post_type_labels( $pt, ' or '); ?>
			<p><?php printf( __( "There aren't any %s in this section.  ", 'ortext' ), $ptn ); 
			$catid = get_query_var( 'cat' );
			$text = __( 'the section you were viewing', 'ortext' );
			if ( $catid ) :
				$text = '<a href="' . get_category_link( $catid ) . '" title="last viewed section">' . $text . '</a>';
			endif;
			echo __('You can go back to ', 'ortext' ) . $text . __( ' or use the navigation link to see an overview of that content type.', 'ortext' ); ?></p>
		<?php else : ?>
			<p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'ortext' ); ?></p>
			<?php get_search_form(); ?>
		<?php endif; ?>
	</div><!-- .page-content -->
</section><!-- .no-results -->
