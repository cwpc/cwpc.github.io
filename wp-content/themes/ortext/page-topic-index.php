<?php
/**
 * Integrated topic index
 * Topics are defined with the WordPress built-in taxonomy post_tag (which works for all post types)
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
						<?php the_title( '<h1 id="custom-page-top">', '</h1>' ); ?>
					</header><!-- .entry-header -->	
					<div class="entry-content">
						<?php get_template_part( 'content', 'pagenh' ); ?>
					
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
