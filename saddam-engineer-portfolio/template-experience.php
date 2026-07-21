<?php
/**
 * Template Name: Engineering Experience
 * Template Post Type: page
 *
 * @package SaddamEngineerPortfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
$cv_url   = (string) sp_setting( 'cv_url', '' );
$linkedin = (string) sp_setting( 'linkedin_url', '' );
?>

<section class="s1-page-hero s1-page-hero-experience">
	<div class="sp-container s1-page-hero-grid">
		<div><p class="s1-eyebrow"><?php esc_html_e( 'Engineering experience', 'sptheme' ); ?></p><h1><?php esc_html_e( 'WordPress delivery backed by support engineering and frontend fundamentals.', 'sptheme' ); ?></h1><p><?php esc_html_e( 'My background spans independent client delivery, a remote WordPress technical-support role and frontend development. That mix shapes a practical style: investigate carefully, communicate clearly and leave maintainable systems behind.', 'sptheme' ); ?></p></div>
		<aside class="s1-page-side-note s1-profile-facts"><dl><div><dt><?php esc_html_e( 'Primary focus', 'sptheme' ); ?></dt><dd><?php esc_html_e( 'Senior WordPress Engineering', 'sptheme' ); ?></dd></div><div><dt><?php esc_html_e( 'Core stack', 'sptheme' ); ?></dt><dd>PHP · WordPress · JavaScript · React · MySQL</dd></div><div><dt><?php esc_html_e( 'Work mode', 'sptheme' ); ?></dt><dd><?php esc_html_e( 'Remote · Async · Flexible overlap', 'sptheme' ); ?></dd></div><div><dt><?php esc_html_e( 'Commercial proof', 'sptheme' ); ?></dt><dd><?php esc_html_e( 'Upwork Top Rated · International clients', 'sptheme' ); ?></dd></div></dl><?php if ( $cv_url ) : ?><a class="s1-btn s1-btn-primary" href="<?php echo esc_url( $cv_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Open CV', 'sptheme' ); ?></a><?php endif; ?></aside>
	</div>
</section>

<section class="s1-section s1-section-light">
	<div class="sp-container s1-resume-layout">
		<div class="s1-resume-main">
			<article class="s1-resume-entry"><div class="s1-resume-date">2023-Present</div><div><p class="s1-eyebrow"><?php esc_html_e( 'Independent / Upwork / direct', 'sptheme' ); ?></p><h2><?php esc_html_e( 'Independent WordPress Engineer', 'sptheme' ); ?></h2><p><?php esc_html_e( 'Delivering WordPress development, WooCommerce customization, technical troubleshooting, performance work and ongoing support for international clients.', 'sptheme' ); ?></p><h3><?php esc_html_e( 'Representative responsibilities', 'sptheme' ); ?></h3><ul><li><?php esc_html_e( 'Built and extended custom plugins and structured WordPress experiences.', 'sptheme' ); ?></li><li><?php esc_html_e( 'Integrated third-party services, webhooks and multilingual content workflows.', 'sptheme' ); ?></li><li><?php esc_html_e( 'Diagnosed theme, plugin, database, performance and production issues.', 'sptheme' ); ?></li><li><?php esc_html_e( 'Managed requirements, delivery, QA and client communication independently.', 'sptheme' ); ?></li></ul></div></article>
			<article class="s1-resume-entry"><div class="s1-resume-date"><?php esc_html_e( 'Previous remote role', 'sptheme' ); ?></div><div><p class="s1-eyebrow"><?php esc_html_e( 'WordPress product and support environment', 'sptheme' ); ?></p><h2><?php esc_html_e( 'WordPress Technical Support Engineer', 'sptheme' ); ?></h2><p><?php esc_html_e( 'Supported customers and production environments by reproducing issues, isolating conflicts, interpreting logs and communicating technically accurate next steps.', 'sptheme' ); ?></p><h3><?php esc_html_e( 'Transferable strengths', 'sptheme' ); ?></h3><ul><li><?php esc_html_e( 'Structured debugging across WordPress core, themes, plugins, hosting and browser behavior.', 'sptheme' ); ?></li><li><?php esc_html_e( 'Clear written communication with non-technical and technical stakeholders.', 'sptheme' ); ?></li><li><?php esc_html_e( 'Prioritization, escalation and documentation in a remote workflow.', 'sptheme' ); ?></li></ul></div></article>
			<article class="s1-resume-entry"><div class="s1-resume-date"><?php esc_html_e( 'Earlier career', 'sptheme' ); ?></div><div><p class="s1-eyebrow"><?php esc_html_e( 'Frontend engineering', 'sptheme' ); ?></p><h2><?php esc_html_e( 'Frontend Developer', 'sptheme' ); ?></h2><p><?php esc_html_e( 'Built responsive interfaces and developed strong foundations in HTML, CSS, JavaScript, accessibility, browser compatibility and design implementation.', 'sptheme' ); ?></p></div></article>
		</div>
		<aside class="s1-resume-side">
			<div><h3><?php esc_html_e( 'Technical toolkit', 'sptheme' ); ?></h3><p class="s1-tag-cloud"><span>WordPress APIs</span><span>PHP / OOP</span><span>WooCommerce</span><span>Gutenberg / React</span><span>REST APIs</span><span>JavaScript</span><span>MySQL</span><span>WP-CLI</span><span>Git / GitHub</span><span>PHPUnit</span><span>PHPCS</span><span>Performance</span><span>Security</span><span>Technical Support</span></p></div>
			<div><h3><?php esc_html_e( 'What I bring to a team', 'sptheme' ); ?></h3><ul class="s1-check-list"><li><?php esc_html_e( 'Ownership from investigation through release', 'sptheme' ); ?></li><li><?php esc_html_e( 'Production-aware debugging', 'sptheme' ); ?></li><li><?php esc_html_e( 'Async written communication', 'sptheme' ); ?></li><li><?php esc_html_e( 'Documented trade-offs and handoff', 'sptheme' ); ?></li></ul></div>
			<div><h3><?php esc_html_e( 'Profile links', 'sptheme' ); ?></h3><?php if ( $linkedin ) : ?><a href="<?php echo esc_url( $linkedin ); ?>" target="_blank" rel="noopener noreferrer">LinkedIn </a><?php endif; ?><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Discuss a role', 'sptheme' ); ?> </a></div>
		</aside>
	</div>
</section>

<?php get_footer(); ?>
