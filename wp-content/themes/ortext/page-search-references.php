<?php
/**
 * Search box for searching only references.
 *
 * @package ortext
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">	
			<header class="page-header"><div class="section-label">
					<?php
					$icat = get_category_by_slug('introduction');
					if ( $icat ) wp_list_categories( array( 'title_li' => '', 'include' => ( $icat->term_id ) ) );
					?>					
					</div>
			</header>

			<?php while ( have_posts() ) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header class="entry-header">
						<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>			
					</header><!-- .entry-header -->
					
					<div class="entry-content">					
						<div class="search-box clear">
								<?php 
								add_filter( 'get_search_form', 'otx_refs_search_form' );
								get_search_form();
								remove_filter( 'get_search_form', 'otx_refs_search_form' );				
								?>
						</div>						
						<?php get_template_part( 'content', 'pagenh' ); ?>
					
			<?php endwhile; // end of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
