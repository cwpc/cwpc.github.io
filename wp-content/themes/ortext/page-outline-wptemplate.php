<?php
/**
 * Template Name: Outline Page Template
 * Determines the post type to outline by looking for outline-{post_type} slug
 *
 * Please note that page here means the WordPress construct of pages.
 * Other 'pages' on your WordPress site will use a different template.
 *
 * @package ortext
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<?php
			global $post;
			$page_slug = get_post( $post )->post_name;
			$outline_type = explode( '-', $page_slug );
			if ( 'outline' == $outline_type[0] ) :
				array_shift( $outline_type );
			else :
				$outline_type = array( 'post' );
			endif;
			?>	
			<header class="page-header">
				<div class="section-label">
					<?php
					if ( $outline_type != array( 'post' ) ) : 
						$icat = get_category_by_slug('introduction');
						if ( $icat ) wp_list_categories( array( 'title_li' => '', 'include' => ( $icat->term_id ) ) );
					endif;
					?>
				</div>
			</header>

			<?php while ( have_posts() ) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header class="entry-header">
						<?php the_title( '<h1 id="outline-title">', '</h1>' ); ?>
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
