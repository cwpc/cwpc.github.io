<?php
/**
 * @package ortext
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content(); ?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'ortext' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<div class="tags-footer">
			<?php 
			$taglist = get_the_tag_list('Topics: '); 
			edit_post_link( __( 'Edit', 'ortext' ), $taglist . '<span class="edit-link">', '</span>' ); 
			?>	
		</div>	
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
