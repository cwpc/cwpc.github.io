<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package ortext
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11"> 
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php include_once( 'googleanalyticstracking.php' ) ?>
<div id="page" class="hfeed site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'ortext' ); ?></a>

	<header id="masthead" class="site-header" role="banner">
		<div class="site-branding">
			<div class="title-box">
				<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
				<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
			</div>
		</div>
		<div id="scroller-anchor"></div>
		<nav id="site-navigation" class="main-navigation clear" role="navigation">
			<span class="menu-toggle"><a href="#"><?php _e( 'menu', 'ortext' ); ?></a></span>
			<?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>			
			<div id="rng">
				<?php ortext_secondary_menu(); ?>
				<div class="search-toggle">
					<span class="fa fa-search"></span>
					<a href="#search-container" class="screen-reader-text"><?php _e( 'search', 'ortext' ); ?></a>
				</div>
			</div>
		</nav><!-- #site-navigation -->				
		<div id="header-search-container" class="search-box-wrapper clear hide">
			<div class="search-box clear">
				<?php get_search_form(); ?>
			</div>
		</div>						
	</header><!-- #masthead -->

	<div id="content" class="site-content">
