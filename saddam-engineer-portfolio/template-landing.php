<?php
/**
 * Template Name: Capability Landing
 *
 * Flexible engineering landing page for an engineering capability.
 *
 * @package SaddamEngineerPortfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	$subhead = has_excerpt() ? get_the_excerpt() : '';
	?>
	<section class="sp-landing-hero" data-anim="hero">
		<div class="sp-container">
			<p class="sp-eyebrow"><?php esc_html_e( 'Engineering Capability', 'sptheme' ); ?></p>
			<h1 class="sp-hero-title"><?php the_title(); ?></h1>
			<?php if ( $subhead ) : ?><p class="sp-hero-sub"><?php echo esc_html( $subhead ); ?></p><?php endif; ?>
			<div class="sp-hero-cta sp-center-flex">
				<a class="sp-btn sp-btn-primary" href="<?php echo esc_url( home_url( '/engineering/' ) ); ?>"><?php esc_html_e( 'Explore Related Implementation', 'sptheme' ); ?></a>
				<a class="sp-btn" href="<?php echo esc_url( get_post_type_archive_link( 'sp_case_study' ) ? get_post_type_archive_link( 'sp_case_study' ) : home_url( '/case-studies/' ) ); ?>"><?php esc_html_e( 'Read Case Studies', 'sptheme' ); ?></a>
			</div>
		</div>
	</section>

	<section class="sp-section"><div class="sp-container sp-prose"><div class="sp-content"><?php the_content(); ?></div></div></section>

	<section class="sp-section sp-section-dark sp-final">
		<div class="sp-container sp-center">
			<h2><?php esc_html_e( 'Evaluating this capability for your team?', 'sptheme' ); ?></h2>
			<p><?php esc_html_e( 'Share the role, expected responsibilities, stack and product context for a focused conversation.', 'sptheme' ); ?></p>
			<div class="sp-hero-cta sp-center-flex">
				<a class="sp-btn sp-btn-primary" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" data-sp-event="career_cta_click" data-sp-label="capability_landing"><?php esc_html_e( 'Discuss a Role', 'sptheme' ); ?></a>
				<a class="sp-btn sp-btn-ghost" href="mailto:<?php echo esc_attr( antispambot( sp_setting( 'email', 'saddam.wp007@gmail.com' ) ) ); ?>"><?php esc_html_e( 'Email', 'sptheme' ); ?></a>
			</div>
		</div>
	</section>
	<?php
endwhile;

get_footer();
