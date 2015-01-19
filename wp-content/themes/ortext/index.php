<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package ortext
 */

get_header(); ?>

	<section id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
		<?php 
		$pt = get_query_var( 'post_type' );
		$cat = get_query_var( 'cat' );
		if ( '' == $pt ) { $pt = 'post'; }		
		if ( have_posts() ) : ?>
			<header class="page-header">
				<div class="section-label">
					<?php
					if ( '' != $cat ) : 
						the_category('  ','multiple');
					else:
						$icat = get_category_by_slug('introduction');
						if ( $icat ) { wp_list_categories( array( 'title_li' => '', 'include' => ( $icat->term_id ) ) ); }			
					endif;
					?>
				</div>	
				<?php if ( 'post' != $pt ) : ?>
					<div class="page-header-area">
						<h1 class="archive-title">
							<?php echo otx_post_type_labels( $pt, __( ' and ', 'ortext') ); ?>						
						</h1>
					</div>  
				<?php endif; ?>
			</header><!-- .page-header -->

			<?php /* Start the Loop */ ?>
			<?php 
			$firstpost = ''; 
			$pcount = 0;
			while ( have_posts() ) : the_post(); 
				if ( $pcount == 0) $firstpost = get_the_ID();
				$pcount++; 
				if ( 'post' == $pt ) :
					get_template_part( 'content' );
				elseif ( count( (array)$pt ) == 1 ) : 
					get_template_part ( 'content', 'excerpts' );
				else :
//					get_template_part ( 'content', 'typed' );
					get_template_part ( 'content', 'excerpts' );
				endif;	
			endwhile; 
			if ( is_category() && ( 'post' == $pt ) ) :
				otx_integrated_paging_nav( $firstpost );
			else:
				ortext_paging_nav();
			endif;

		else : ?>
			<header class="page-header">
				<div class="section-label"><?php otx_the_category( '  ' ); ?></div>
			</header><!-- .page-header -->		
			<?php get_template_part( 'content', 'none' ); 
		endif; ?>

		</main><!-- #main -->
	</section><!-- #primary -->

<?php get_footer(); ?>
