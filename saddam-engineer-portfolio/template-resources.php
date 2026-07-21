<?php
/**
 * Template Name: Resources
 *
 * Engineering resources and technical navigation hub.
 *
 * @package SaddamEngineerPortfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
$github = (string) sp_setting( 'github_url', 'https://github.com/iamsaddamhossen' );
?>
<section class="sp-resources-hero sp-dark-hero">
	<div class="sp-container sp-resources-hero-grid">
		<div>
			<p class="sp-eyebrow"><?php esc_html_e( 'Engineering Resources', 'sptheme' ); ?></p>
			<h1><?php esc_html_e( 'Architecture notes, source-backed examples and practical WordPress engineering writing.', 'sptheme' ); ?></h1>
			<p><?php esc_html_e( 'Use this hub to review how I reason about WordPress architecture, security, REST APIs, Gutenberg, performance, testing, debugging and remote engineering work.', 'sptheme' ); ?></p>
			<div class="sp-hero-cta">
				<a class="sp-btn sp-btn-primary" href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'Read Technical Notes', 'sptheme' ); ?></a>
				<a class="sp-btn sp-btn-ghost-light" href="<?php echo esc_url( $github ); ?>" target="_blank" rel="noopener noreferrer">GitHub </a>
			</div>
		</div>
		<aside class="sp-resource-map-card">
			<strong><?php esc_html_e( 'Use this hub to', 'sptheme' ); ?></strong>
			<ul>
				<li><?php esc_html_e( 'Connect technical concepts to working implementation', 'sptheme' ); ?></li>
				<li><?php esc_html_e( 'Review architecture and quality decisions', 'sptheme' ); ?></li>
				<li><?php esc_html_e( 'Build practical WordPress engineering knowledge', 'sptheme' ); ?></li>
			</ul>
		</aside>
	</div>
</section>

<section class="sp-section sp-section-tight">
	<div class="sp-container">
		<div class="sp-resource-grid">
			<a class="sp-resource-card" href="<?php echo esc_url( home_url( '/engineering/' ) ); ?>">
				<span>01</span><h2><?php esc_html_e( 'Engineering System', 'sptheme' ); ?></h2><p><?php esc_html_e( 'Feature map, REST endpoint, Gutenberg block, WP-CLI commands, privacy hooks, Site Health checks and quality gates.', 'sptheme' ); ?></p><b><?php esc_html_e( 'Explore the system ', 'sptheme' ); ?></b>
			</a>
			<a class="sp-resource-card" href="<?php echo esc_url( home_url( '/blog/' ) ); ?>">
				<span>02</span><h2><?php esc_html_e( 'Technical Writing', 'sptheme' ); ?></h2><p><?php esc_html_e( 'Practical notes on plugin architecture, Gutenberg, APIs, security, performance, debugging and maintainable delivery.', 'sptheme' ); ?></p><b><?php esc_html_e( 'Read articles ', 'sptheme' ); ?></b>
			</a>
			<a class="sp-resource-card" href="<?php echo esc_url( get_post_type_archive_link( 'sp_case_study' ) ? get_post_type_archive_link( 'sp_case_study' ) : home_url( '/case-studies/' ) ); ?>">
				<span>03</span><h2><?php esc_html_e( 'Case Studies', 'sptheme' ); ?></h2><p><?php esc_html_e( 'Production contexts explained through constraints, responsibility, implementation decisions, safeguards and observable results.', 'sptheme' ); ?></p><b><?php esc_html_e( 'Review case studies ', 'sptheme' ); ?></b>
			</a>
		</div>
	</div>
</section>

<section class="sp-section sp-section-alt sp-section-tight">
	<div class="sp-container sp-resource-feature-grid">
		<div><p class="sp-eyebrow"><?php esc_html_e( 'Suggested path', 'sptheme' ); ?></p><h2><?php esc_html_e( 'A focused route through the engineering work.', 'sptheme' ); ?></h2></div>
		<div class="sp-resource-links">
			<a href="<?php echo esc_url( home_url( '/engineering/' ) ); ?>"><?php esc_html_e( 'Explore architecture and implementation details', 'sptheme' ); ?> <span></span></a>
			<a href="<?php echo esc_url( home_url( '/engineering/#api' ) ); ?>"><?php esc_html_e( 'Inspect the public REST endpoint', 'sptheme' ); ?> <span></span></a>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'sp_case_study' ) ? get_post_type_archive_link( 'sp_case_study' ) : home_url( '/case-studies/' ) ); ?>"><?php esc_html_e( 'Read production case studies', 'sptheme' ); ?> <span></span></a>
			<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Share the product context', 'sptheme' ); ?> <span></span></a>
		</div>
	</div>
</section>
<?php
get_footer();
