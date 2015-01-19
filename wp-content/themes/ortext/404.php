<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package ortext
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<section class="error-404 not-found">
				<header class="page-header">
					<div class="section-label">
						<?php
						$icat = get_category_by_slug('introduction');
						if ( $icat ) { wp_list_categories( array( 'title_li' => '', 'include' => ( $icat->term_id ) ) ); }
						?>
					</div>
					<h1 class="page-title"><?php _e( 'Nothing was found.', 'ortext' ); ?></h1>
				</header><!-- .page-header -->

				<div class="page-content">
					<p><?php _e( 'Try browsing the outline by clicking "Outline" on the far left of the black navigation bar above.', 'ortext' ); ?></p>

				</div><!-- .page-content -->
			</section><!-- .error-404 -->

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
