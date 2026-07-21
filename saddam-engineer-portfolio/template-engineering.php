<?php
/**
 * Template Name: Engineering System
 * Template Post Type: page
 *
 * @package SaddamEngineerPortfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
$github = (string) sp_setting( 'github_url', 'https://github.com/iamsaddamhossen' );
?>

<section class="s1-page-hero s1-page-hero-engineering">
	<div class="sp-container s1-page-hero-grid">
		<div>
			<p class="s1-eyebrow"><?php esc_html_e( 'Engineering system', 'sptheme' ); ?></p>
			<h1><?php esc_html_e( 'A WordPress architecture designed to be editable, testable and maintainable.', 'sptheme' ); ?></h1>
			<p><?php esc_html_e( 'The theme owns presentation. The companion plugin owns portable content models, workflows and integrations. Native editor tools keep normal content changes accessible to non-technical users.', 'sptheme' ); ?></p>
			<div class="s1-actions"><a class="s1-btn s1-btn-primary" href="<?php echo esc_url( $github ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Inspect Repository', 'sptheme' ); ?></a><a class="s1-btn s1-btn-secondary" href="#architecture"><?php esc_html_e( 'Explore Architecture', 'sptheme' ); ?></a></div>
		</div>
		<aside class="s1-architecture-summary">
			<div><span>01</span><strong><?php esc_html_e( 'Presentation', 'sptheme' ); ?></strong><small><?php esc_html_e( 'Templates, responsive UI and global design system', 'sptheme' ); ?></small></div>
			<div><span>02</span><strong><?php esc_html_e( 'Domain behavior', 'sptheme' ); ?></strong><small><?php esc_html_e( 'Content types, metadata, REST, leads and operations', 'sptheme' ); ?></small></div>
			<div><span>03</span><strong><?php esc_html_e( 'Editorial experience', 'sptheme' ); ?></strong><small><?php esc_html_e( 'Blocks, patterns, clear fields and safe content ownership', 'sptheme' ); ?></small></div>
		</aside>
	</div>
</section>

<section class="s1-section s1-section-light" id="architecture">
	<div class="sp-container">
		<header class="s1-section-head"><div><p class="s1-eyebrow"><?php esc_html_e( 'Ownership boundaries', 'sptheme' ); ?></p><h2><?php esc_html_e( 'Each layer has a clear job.', 'sptheme' ); ?></h2></div><p><?php esc_html_e( 'The goal is not separation for its own sake. It is to keep data portable, visual changes safe and editing understandable.', 'sptheme' ); ?></p></header>
		<div class="s1-architecture-grid">
			<article><span><?php esc_html_e( 'Theme', 'sptheme' ); ?></span><h3><?php esc_html_e( 'Presentation and interaction', 'sptheme' ); ?></h3><p><?php esc_html_e( 'Semantic templates, responsive layout, accessibility, navigation and visual states. Changing the theme does not delete business content.', 'sptheme' ); ?></p><code>saddam-engineer-portfolio/</code></article>
			<article><span><?php esc_html_e( 'Companion plugin', 'sptheme' ); ?></span><h3><?php esc_html_e( 'Portable domain behavior', 'sptheme' ); ?></h3><p><?php esc_html_e( 'Custom post types, registered metadata, REST routes, privacy tools, lead workflow, caching, WP-CLI and Site Health.', 'sptheme' ); ?></p><code>saddam-engineer-core/</code></article>
			<article><span><?php esc_html_e( 'Editor experience', 'sptheme' ); ?></span><h3><?php esc_html_e( 'Safe everyday content editing', 'sptheme' ); ?></h3><p><?php esc_html_e( 'Core blocks, reusable patterns and structured fields let editors change copy and media without opening PHP templates.', 'sptheme' ); ?></p><code>patterns/ + registered meta</code></article>
		</div>
	</div>
</section>

<section class="s1-section" id="domains">
	<div class="sp-container">
		<header class="s1-section-head"><div><p class="s1-eyebrow"><?php esc_html_e( 'Engineering domains', 'sptheme' ); ?></p><h2><?php esc_html_e( 'The implementation is organized around responsibilities, not buzzwords.', 'sptheme' ); ?></h2></div><p><?php esc_html_e( 'Each domain below has a practical purpose, a WordPress-native implementation path and an explicit operational concern.', 'sptheme' ); ?></p></header>
		<div class="s1-domain-list">
			<article><div class="s1-domain-number">01</div><div><p class="s1-domain-label"><?php esc_html_e( 'Product architecture', 'sptheme' ); ?></p><h3><?php esc_html_e( 'Plugins, content models and data ownership', 'sptheme' ); ?></h3><p><?php esc_html_e( 'Custom post types are used for editorial records. Registered metadata defines stable fields and REST schemas. A custom table is reserved for lead queries and retention behavior that do not fit normal content storage.', 'sptheme' ); ?></p><ul><li>Custom post types and taxonomies</li><li>Registered meta with sanitization and capabilities</li><li>Custom table with indexed fields and lifecycle handling</li></ul></div><code>class-spcore-cpt.php<br>class-spcore-meta.php<br>class-spcore-leads.php</code></article>
			<article><div class="s1-domain-number">02</div><div><p class="s1-domain-label"><?php esc_html_e( 'Contracts and integrations', 'sptheme' ); ?></p><h3><?php esc_html_e( 'REST APIs, external services and failure-aware workflows', 'sptheme' ); ?></h3><p><?php esc_html_e( 'Public routes use explicit argument schemas, bounded pagination and deliberate response fields. Sensitive health information remains capability-protected. External requests use WordPress HTTP APIs and recoverable failure paths.', 'sptheme' ); ?></p><ul><li>Versioned REST namespace</li><li>Permissions, validation and predictable errors</li><li>Cache keys and explicit invalidation</li></ul></div><code>class-spcore-rest.php<br>class-spcore-contact.php</code></article>
			<article><div class="s1-domain-number">03</div><div><p class="s1-domain-label"><?php esc_html_e( 'Editor and frontend', 'sptheme' ); ?></p><h3><?php esc_html_e( 'Gutenberg, reusable patterns and accessible interfaces', 'sptheme' ); ?></h3><p><?php esc_html_e( 'The dynamic engineering block uses block metadata and server rendering. Reusable native patterns cover common client sections. Frontend behavior progressively enhances accessible HTML instead of replacing it.', 'sptheme' ); ?></p><ul><li>block.json and server-side rendering</li><li>Pattern library for common layouts</li><li>Keyboard, focus and reduced-motion support</li></ul></div><code>blocks/engineering-proof/<br>patterns/<br>assets/js/main.js</code></article>
			<article><div class="s1-domain-number">04</div><div><p class="s1-domain-label"><?php esc_html_e( 'Production operations', 'sptheme' ); ?></p><h3><?php esc_html_e( 'Security, privacy, diagnostics and quality gates', 'sptheme' ); ?></h3><p><?php esc_html_e( 'Capabilities, nonces, validation, output escaping and rate limits protect the request boundary. Privacy export/erase support, WP-CLI, Site Health and automated checks create an operational surface beyond the browser.', 'sptheme' ); ?></p><ul><li>Privacy exporter, eraser and retention</li><li>WP-CLI and Site Health diagnostics</li><li>PHPCS, compatibility checks, PHPUnit and CI</li></ul></div><code>class-spcore-privacy.php<br>class-spcore-health.php<br>.github/workflows/</code></article>
		</div>
	</div>
</section>

<section class="s1-section s1-section-editor" id="editor-model">
	<div class="sp-container s1-editor-grid">
		<div class="s1-editor-copy">
			<p class="s1-eyebrow"><?php esc_html_e( 'Client editing model', 'sptheme' ); ?></p>
			<h2><?php esc_html_e( 'How a non-technical client edits this kind of site without Elementor.', 'sptheme' ); ?></h2>
			<p><?php esc_html_e( 'The editor should see business language and approved layout choices, not implementation details. The architecture uses the simplest editing tool appropriate for each content type.', 'sptheme' ); ?></p>
			<a class="s1-btn s1-btn-dark" href="<?php echo esc_url( home_url( '/client-editable-wordpress-without-page-builder/' ) ); ?>"><?php esc_html_e( 'Open the complete guide', 'sptheme' ); ?></a>
		</div>
		<div class="s1-editing-table" role="table" aria-label="<?php esc_attr_e( 'Editing model', 'sptheme' ); ?>">
			<div role="row"><strong role="cell"><?php esc_html_e( 'Normal page copy and images', 'sptheme' ); ?></strong><span role="cell"><?php esc_html_e( 'Core blocks', 'sptheme' ); ?></span></div>
			<div role="row"><strong role="cell"><?php esc_html_e( 'Reusable hero, CTA and feature sections', 'sptheme' ); ?></strong><span role="cell"><?php esc_html_e( 'Block patterns', 'sptheme' ); ?></span></div>
			<div role="row"><strong role="cell"><?php esc_html_e( 'Projects, services and team records', 'sptheme' ); ?></strong><span role="cell"><?php esc_html_e( 'Custom post types and clear fields', 'sptheme' ); ?></span></div>
			<div role="row"><strong role="cell"><?php esc_html_e( 'Complex interactive functionality', 'sptheme' ); ?></strong><span role="cell"><?php esc_html_e( 'Purpose-built custom block', 'sptheme' ); ?></span></div>
			<div role="row"><strong role="cell"><?php esc_html_e( 'Sitewide contact and profile details', 'sptheme' ); ?></strong><span role="cell"><?php esc_html_e( 'Settings screen', 'sptheme' ); ?></span></div>
		</div>
	</div>
</section>

<section class="s1-section s1-section-quality-light" id="quality">
	<div class="sp-container">
		<header class="s1-section-head"><div><p class="s1-eyebrow"><?php esc_html_e( 'Production readiness', 'sptheme' ); ?></p><h2><?php esc_html_e( 'Quality, search clarity and AI features share the same foundation: trustworthy implementation.', 'sptheme' ); ?></h2></div><p><?php esc_html_e( 'No schema or AI feature can replace useful content, crawlable pages, secure code and evidence that matches visible claims.', 'sptheme' ); ?></p></header>
		<div class="s1-quality-grid">
			<article><span><?php esc_html_e( 'Code quality', 'sptheme' ); ?></span><h3><?php esc_html_e( 'Review before release', 'sptheme' ); ?></h3><p><?php esc_html_e( 'Syntax checks, WordPress Coding Standards, compatibility rules, integration tests and GitHub Actions.', 'sptheme' ); ?></p></article>
			<article><span><?php esc_html_e( 'SEO and AEO', 'sptheme' ); ?></span><h3><?php esc_html_e( 'Useful content with technical clarity', 'sptheme' ); ?></h3><p><?php esc_html_e( 'Canonical URLs, contextual metadata, accurate JSON-LD, internal links, visible authorship and human-first technical articles.', 'sptheme' ); ?></p></article>
			<article><span><?php esc_html_e( 'AI assistant', 'sptheme' ); ?></span><h3><?php esc_html_e( 'Progressive enhancement, not dependency', 'sptheme' ); ?></h3><p><?php esc_html_e( 'Guided mode works without an API. Optional AI mode keeps keys server-side, limits input and grounds answers in site content.', 'sptheme' ); ?></p></article>
		</div>
		<div class="s1-repo-cta"><div><h3><?php esc_html_e( 'The full repository documents architecture, tests and release workflows.', 'sptheme' ); ?></h3><p><?php esc_html_e( 'Start with the README, then review the theme, companion plugin, tests and quality workflow.', 'sptheme' ); ?></p></div><a class="s1-btn s1-btn-primary" href="<?php echo esc_url( $github ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Inspect Source Code', 'sptheme' ); ?></a></div>
	</div>
</section>

<?php get_footer(); ?>
