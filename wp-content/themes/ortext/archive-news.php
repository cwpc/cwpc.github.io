<?php
/**
 * The template for displaying ortext news archive.
 *
 * @package ortext
 */

get_header(); ?>

	<section id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
		<?php 	
		if ( have_posts() ) : ?>
			<header class="page-header">
				<div class="page-header-area">
					<h1 class="archive-title">
						<?php _e( 'Updates, Announcements, and Other News', 'ortext' ); ?>						
					</h1>
				</div>  
			</header><!-- .page-header -->
			<?php 
			while ( have_posts() ) : the_post(); 
				get_template_part ( 'content', 'news' );	
			endwhile; 
			ortext_paging_nav();
		else : ?>	
			<?php get_template_part( 'content', 'none' ); 
		endif; ?>

		</main><!-- #main -->
	</section><!-- #primary -->

<?php get_footer(); ?>
