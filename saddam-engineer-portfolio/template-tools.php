<?php
/**
 * Template Name: Recommended Tools
 *
 * @package SaddamEngineerPortfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<section class="sp-tools-hero sp-tools-hero-premium">
	<div class="sp-container sp-tools-hero-grid">
		<div>
			<p class="sp-eyebrow"><?php esc_html_e( 'Practical WordPress stack', 'sptheme' ); ?></p>
			<h1><?php esc_html_e( 'Tools chosen for a specific job, not a popularity list.', 'sptheme' ); ?></h1>
			<p><?php esc_html_e( 'A concise engineering stack for development, diagnosis, delivery, backup and performance. Each recommendation explains where it helps, where it does not, and what should be verified before production use.', 'sptheme' ); ?></p>
			<div class="sp-tools-hero-actions">
				<a class="sp-btn sp-btn-primary" href="#recommended-tools"><?php esc_html_e( 'Browse the stack', 'sptheme' ); ?></a>
				<a class="sp-btn sp-btn-secondary" href="<?php echo esc_url( home_url( '/evaluate-wordpress-tools-affiliate-recommendations/' ) ); ?>"><?php esc_html_e( 'Read the evaluation method', 'sptheme' ); ?></a>
			</div>
		</div>
		<aside class="sp-tools-note sp-tools-note-premium">
			<div class="sp-tools-note-icon" aria-hidden="true">
				<svg viewBox="0 0 24 24" focusable="false"><path d="M12 3 4.5 6v5.2c0 4.7 3 8.3 7.5 9.8 4.5-1.5 7.5-5.1 7.5-9.8V6L12 3Z"/><path d="m8.7 12 2.1 2.1 4.6-4.7"/></svg>
			</div>
			<strong><?php esc_html_e( 'Recommendation standard', 'sptheme' ); ?></strong>
			<ul>
				<li><?php esc_html_e( 'Solve a clear operational problem.', 'sptheme' ); ?></li>
				<li><?php esc_html_e( 'Check permissions, portability and recovery.', 'sptheme' ); ?></li>
				<li><?php esc_html_e( 'Disclose affiliate relationships clearly.', 'sptheme' ); ?></li>
			</ul>
		</aside>
	</div>
</section>

<section class="sp-section sp-tools-main" id="recommended-tools">
	<div class="sp-container">
		<div class="sp-tools-intro-grid">
			<div class="sp-prose sp-tools-intro-copy">
				<?php while ( have_posts() ) : the_post(); the_content(); endwhile; ?>
			</div>
			<div class="sp-tool-principles">
				<article><span>01</span><strong><?php esc_html_e( 'Fit before features', 'sptheme' ); ?></strong><p><?php esc_html_e( 'Start with the business or engineering problem. A longer feature list is not automatically a better fit.', 'sptheme' ); ?></p></article>
				<article><span>02</span><strong><?php esc_html_e( 'Operations before marketing', 'sptheme' ); ?></strong><p><?php esc_html_e( 'Review updates, support, logs, rollback and data ownership before choosing the tool.', 'sptheme' ); ?></p></article>
				<article><span>03</span><strong><?php esc_html_e( 'Trust before commission', 'sptheme' ); ?></strong><p><?php esc_html_e( 'A paid relationship never replaces a suitability review or a clear disclosure.', 'sptheme' ); ?></p></article>
			</div>
		</div>
		<?php sp_block( '[spcore_tools]' ); ?>
	</div>
</section>

<section class="sp-section sp-tools-framework">
	<div class="sp-container sp-tools-guide">
		<header class="sp-tools-framework-head">
			<div><p class="sp-eyebrow"><?php esc_html_e( 'Safe adoption order', 'sptheme' ); ?></p><h2><?php esc_html_e( 'Build the operating foundation before adding optimization layers.', 'sptheme' ); ?></h2></div>
			<p><?php esc_html_e( 'This order reduces risk because diagnosis, delivery and recovery remain visible before caching or edge behavior is introduced.', 'sptheme' ); ?></p>
		</header>
		<ol class="sp-steps-grid sp-tools-steps">
			<li><span>01</span><h3><?php esc_html_e( 'Develop and diagnose', 'sptheme' ); ?></h3><p><?php esc_html_e( 'Use local reproduction, source control and debugging tools to understand the system first.', 'sptheme' ); ?></p></li>
			<li><span>02</span><h3><?php esc_html_e( 'Deliver and observe', 'sptheme' ); ?></h3><p><?php esc_html_e( 'Make email, logs and monitoring dependable so failures do not stay hidden.', 'sptheme' ); ?></p></li>
			<li><span>03</span><h3><?php esc_html_e( 'Protect and recover', 'sptheme' ); ?></h3><p><?php esc_html_e( 'Back up important data and test the restore path before a risky change.', 'sptheme' ); ?></p></li>
			<li><span>04</span><h3><?php esc_html_e( 'Optimize with evidence', 'sptheme' ); ?></h3><p><?php esc_html_e( 'Add caching or edge services after measuring the actual bottleneck and origin behavior.', 'sptheme' ); ?></p></li>
		</ol>
	</div>
</section>
<?php get_footer();
