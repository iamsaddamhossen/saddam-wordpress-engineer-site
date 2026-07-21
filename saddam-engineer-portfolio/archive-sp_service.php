<?php
/**
 * Engineering capabilities archive.
 *
 * @package SaddamEngineerPortfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<section class="sp-svc-archive-hero sp-dark-hero">
	<div class="sp-container sp-svc-archive-grid">
		<div>
			<p class="sp-eyebrow"><?php esc_html_e( 'Engineering Capabilities', 'sptheme' ); ?></p>
			<h1><?php esc_html_e( 'WordPress capabilities connected to working implementations and clear ownership.', 'sptheme' ); ?></h1>
			<p><?php esc_html_e( 'These are not generic service claims. Each capability is supported by code, architecture notes, production case studies or an explicit learning and testing path.', 'sptheme' ); ?></p>
			<div class="sp-hero-cta">
				<a class="sp-btn sp-btn-primary" href="<?php echo esc_url( home_url( '/engineering/' ) ); ?>"><?php esc_html_e( 'Explore Engineering', 'sptheme' ); ?></a>
				<a class="sp-btn sp-btn-ghost-light" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Discuss a Role', 'sptheme' ); ?></a>
			</div>
		</div>
		<aside class="sp-svc-archive-panel">
			<strong><?php esc_html_e( 'Core areas', 'sptheme' ); ?></strong>
			<ul>
				<li><?php esc_html_e( 'Plugin and PHP architecture', 'sptheme' ); ?></li>
				<li><?php esc_html_e( 'WooCommerce and Gutenberg', 'sptheme' ); ?></li>
				<li><?php esc_html_e( 'REST APIs and integrations', 'sptheme' ); ?></li>
				<li><?php esc_html_e( 'Performance, security and debugging', 'sptheme' ); ?></li>
			</ul>
		</aside>
	</div>
</section>

<section class="sp-section sp-section-tight">
	<div class="sp-container">
		<header class="sp-section-head">
			<p class="sp-eyebrow"><?php esc_html_e( 'Capability Map', 'sptheme' ); ?></p>
			<h2><?php esc_html_e( 'How I approach the WordPress engineering responsibilities a team may need.', 'sptheme' ); ?></h2>
		</header>
		<?php echo do_shortcode( '[spcore_services]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Shortcode output is escaped internally. ?>
	</div>
</section>

<section class="sp-section sp-section-alt sp-section-tight">
	<div class="sp-container sp-process-band">
		<header><p class="sp-eyebrow"><?php esc_html_e( 'Working Method', 'sptheme' ); ?></p><h2><?php esc_html_e( 'Understand, isolate, implement, verify and document.', 'sptheme' ); ?></h2></header>
		<ol class="sp-inline-steps"><li><?php esc_html_e( 'Context', 'sptheme' ); ?></li><li><?php esc_html_e( 'Risk', 'sptheme' ); ?></li><li><?php esc_html_e( 'Design', 'sptheme' ); ?></li><li><?php esc_html_e( 'Build', 'sptheme' ); ?></li><li><?php esc_html_e( 'Test', 'sptheme' ); ?></li><li><?php esc_html_e( 'Document', 'sptheme' ); ?></li></ol>
	</div>
</section>

<section class="sp-final-cta sp-section-tight">
	<div class="sp-container sp-final-cta-inner">
		<div><p class="sp-eyebrow"><?php esc_html_e( 'Hiring', 'sptheme' ); ?></p><h2><?php esc_html_e( 'Share the role, team, product context and technical priorities.', 'sptheme' ); ?></h2></div>
		<a class="sp-btn sp-btn-primary" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Discuss a Role', 'sptheme' ); ?></a>
	</div>
</section>
<?php
get_footer();
