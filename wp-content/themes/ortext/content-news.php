<?php
/**
 * @package ortext
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>

	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'ortext' ) ); ?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'ortext' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<div class="entry-meta">
			<?php ortext_posted_on(); ?>
		</div><!-- .entry-meta -->	
		<div class="tags-footer">
			<?php 
			edit_post_link( __( 'Edit', 'ortext' ), '<span class="edit-link">', '</span>' ); 
			?>
		</div>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->