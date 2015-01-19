<?php
/**
 * The template for displaying all single posts.
 *
 * @package ortext
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php while ( have_posts() ) : the_post(); ?>
			<div class="section-label"><?php if ( !in_category( 'uncategorized') ) { the_category('  ','multiple'); } ?></div>
			
			<?php if ( has_post_thumbnail() ) {the_post_thumbnail();} ?>

			<?php get_template_part( 'content', 'single' ); ?>

			<?php ortext_post_nav(); ?>

			<?php
				// If comments are open or we have at least one comment, load up the comment template
				if ( comments_open() || '0' != get_comments_number() ) :
					comments_template();
				endif;
			?>

		<?php endwhile; // end of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>