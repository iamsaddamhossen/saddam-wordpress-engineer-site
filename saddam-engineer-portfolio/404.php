<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header(); ?>
<section class="sp-dark-hero sp-page-head sp-center">
	<div class="sp-container">
		<p class="sp-eyebrow"><?php esc_html_e( 'Page not found', 'sptheme' ); ?></p>
		<h1>404</h1>
		<p><?php esc_html_e( 'That page moved or never existed. Let’s get you back to Saddam’s WordPress work.', 'sptheme' ); ?></p>
		<p><a class="sp-btn sp-btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'sptheme' ); ?></a></p>
	</div>
</section>
<?php get_footer();
