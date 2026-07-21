<?php
/**
 * Template Name: Full Width (with shortcodes)
 *
 * @package SaddamEngineerPortfolio
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
while ( have_posts() ) : the_post(); ?>
	<header class="sp-page-head sp-dark-hero">
		<div class="sp-container">
			<p class="sp-eyebrow"><?php esc_html_e( 'Saddam Hossen', 'sptheme' ); ?></p>
			<h1><?php the_title(); ?></h1>
		</div>
	</header>
	<section class="sp-section sp-section-tight"><div class="sp-container"><div class="sp-content"><?php the_content(); ?></div></div></section>
<?php endwhile;
get_footer();
