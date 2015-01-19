<?php
/**
 * @package ortext
 */

get_header(); ?>
	<section id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<header class="page-header"><div class="section-label">
				<?php
				$icat = get_category_by_slug('introduction');
				if ( $icat ) wp_list_categories( array( 'title_li' => '', 'include' => ( $icat->term_id ) ) );
				?>					
				</div>
			</header>
			
			<?php the_post(); get_template_part( 'content', 'page' ); ?>
			
		</main><!-- #main -->
	</section><!-- #primary -->
<?php get_footer(); ?>
