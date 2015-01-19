<?php
/**
 * The template part for displaying results in search pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package ortext
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( sprintf( '<h1 class="excerpt-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>
	</header><!-- .entry-header -->

	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->

	<footer class="entry-footer">
		<div class="tags-footer">
			<?php 
			$taglist = get_the_tag_list('Topics: '); 
			edit_post_link( __( 'Edit', 'ortext' ), $taglist . '<span class="edit-link">', '</span>' ); 
			?>	
		</div>			
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->