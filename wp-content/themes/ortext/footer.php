<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package ortext
 */
?>
	</div><!-- #content -->
	
		<footer id="colophon" class="site-footer" role="contentinfo">
			<?php if ( is_active_sidebar( 'footer-widget-1' ) ) : ?>
				<div id="footer-widget" class="primary-sidebar widget-area" role="complementary">
					<?php dynamic_sidebar( 'footer-widget-1' ); ?>
				</div>
			<?php endif; ?>
			<div class="site-info">
				<nav id="site-navigation" class="main-navigation clear" role="navigation">
					<span class="menu-toggle"><a href="#"><?php _e( 'menu', 'ortext' ); ?></a></span>
					<?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>			
					<div id="rng">
						<?php ortext_secondary_menu(); ?>
						<div class="search-toggle-bottom">
							<span class="fa fa-search"></span>
							<a href="#search-container" class="screen-reader-text"><?php _e( 'search', 'ortext' ); ?></a>
						</div>
					</div>			
					<div id="header-search-container" class="search-box-wrapper-bottom clear hide">
						<div class="search-box clear">
							<?php get_search_form(); ?>
						</div>
					</div>	
					<div id="footer-tagline">
						<a href="<?php echo esc_url( __( 'http://localhost/wordpress/', 'ortext' ) ); ?>"><?php printf( __( 'Communicating with Prisoners', 'ortext' )); ?></a>	
					</div>
				</nav><!-- #site-navigation -->				
			</div><!-- .site-info -->
		</footer><!-- #colophon -->

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>


