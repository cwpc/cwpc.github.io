<?php
/**
 * The template for displaying tag-term archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package ortext
 */

get_header(); ?>

	<section id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
		<?php 
		set_query_var( 'posts_per_archive_page', -1 ); 	
		set_query_var( 'post_type', 'any' ); ?>
		<header class="page-header">
				<div class="section-label">
					<?php
					$icat = get_category_by_slug('introduction');
					if ( $icat ) { wp_list_categories( array( 'title_li' => '', 'include' => ( $icat->term_id ) ) ); }
					?>
				</div>		
		<?php	
		if ( have_posts() ) : ?>
			<div class="page-header-area">	
				<h1 class="archive-title">
					<?php printf( __( 'Topic Collection: %s', 'ortext' ), single_tag_title( '', false ) ); ?>
				</h1>
				<?php
				// Show an optional term description.
				$term_description = term_description();
				if ( ! empty( $term_description ) ) :
					printf( '<div class="taxonomy-description">%s</div>', $term_description );
				endif;
				?>
			</div>
			</header><!-- .page-header -->

			<?php /* Start the Loop */ ?>
			<?php 
			while ( have_posts() ) : the_post(); 
					get_template_part ( 'content', 'typed' );	
			endwhile; 
			ortext_paging_nav();
		else : ?>
			</header><!-- .page-header -->	
			<?php get_template_part( 'content', 'none' ); 
		endif; ?>

		</main><!-- #main -->
	</section><!-- #primary -->

<?php get_footer(); ?>
