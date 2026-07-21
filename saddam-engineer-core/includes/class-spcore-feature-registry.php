<?php
/**
 * Verifiable engineering features exposed across the site, REST API and CLI.
 *
 * @package SaddamEngineerCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class SPCore_Feature_Registry {

	/**
	 * Return the implemented feature catalogue.
	 *
	 * @return array<int, array<string, string>>
	 */
	public static function all() {
		return array(
			array(
				'id'          => 'content-models',
				'category'    => 'WordPress Architecture',
				'title'       => 'Custom post types and taxonomies',
				'description' => 'Projects, case studies, services, testimonials, tools and private leads use native WordPress content APIs with REST support where appropriate.',
				'evidence'    => 'includes/class-spcore-cpt.php',
			),
			array(
				'id'          => 'registered-meta',
				'category'    => 'Data Modeling',
				'title'       => 'Registered post meta and native meta boxes',
				'description' => 'Structured project and case-study fields are sanitized, capability protected and exposed through the REST API using registered metadata.',
				'evidence'    => 'includes/class-spcore-meta.php',
			),
			array(
				'id'          => 'rest-api',
				'category'    => 'APIs',
				'title'       => 'Versioned custom REST API',
				'description' => 'Public read endpoints provide engineering metadata and structured projects with schemas, validation, pagination and transient caching.',
				'evidence'    => 'includes/class-spcore-rest.php',
			),
			array(
				'id'          => 'dynamic-block',
				'category'    => 'Gutenberg',
				'title'       => 'Dynamic Gutenberg engineering-proof block',
				'description' => 'A block.json-based server-rendered block demonstrates editor integration, InspectorControls, accessible markup and zero duplicated frontend data.',
				'evidence'    => 'blocks/engineering-proof/',
			),
			array(
				'id'          => 'custom-table',
				'category'    => 'Data Storage',
				'title'       => 'Custom database table with indexed fields',
				'description' => 'Contact leads are mirrored to a queryable custom table created with dbDelta while preserving an admin-friendly private post type.',
				'evidence'    => 'includes/class-spcore-leads.php',
			),
			array(
				'id'          => 'secure-contact',
				'category'    => 'Security',
				'title'       => 'Progressive contact workflow',
				'description' => 'AJAX and no-JavaScript submissions share validation, nonces, honeypot and timing checks, optional CAPTCHA, rate limiting, mail delivery and signed webhooks.',
				'evidence'    => 'includes/class-spcore-contact.php',
			),
			array(
				'id'          => 'privacy',
				'category'    => 'Privacy',
				'title'       => 'Personal-data exporter and eraser',
				'description' => 'Lead data integrates with WordPress privacy tools, supports anonymization and defaults to not storing visitor IP addresses.',
				'evidence'    => 'includes/class-spcore-privacy.php',
			),
			array(
				'id'          => 'wp-cli',
				'category'    => 'Developer Experience',
				'title'       => 'WP-CLI operational commands',
				'description' => 'CLI commands report platform status and manage caches for repeatable operations without using the browser.',
				'evidence'    => 'includes/class-spcore-cli.php',
			),
			array(
				'id'          => 'site-health',
				'category'    => 'Operations',
				'title'       => 'Site Health diagnostics',
				'description' => 'Custom tests surface HTTPS, permalink, companion-theme and scheduled-maintenance health inside WordPress Site Health.',
				'evidence'    => 'includes/class-spcore-health.php',
			),
			array(
				'id'          => 'cron-cache',
				'category'    => 'Performance',
				'title'       => 'Scheduled cache refresh and retention',
				'description' => 'Transients cache expensive site summaries, invalidation hooks keep results fresh and daily maintenance applies configurable lead retention.',
				'evidence'    => 'includes/class-spcore-cache.php',
			),
			array(
				'id'          => 'schema-seo',
				'category'    => 'Search',
				'title'       => 'Structured data and technical discovery',
				'description' => 'JSON-LD, canonical metadata, Open Graph, robots rules and llms.txt are generated from WordPress content without hard-coding page output.',
				'evidence'    => 'includes/class-spcore-schema.php, includes/class-spcore-seo.php',
			),
			array(
				'id'          => 'site-assistant',
				'category'    => 'AI & Progressive Enhancement',
				'title'       => 'Guided assistant with optional server-side AI',
				'description' => 'The accessible assistant works without an API in guided mode and can optionally call a site-grounded AI provider through rate-limited server-side requests.',
				'evidence'    => 'includes/class-spcore-assistant.php, assets/js/assistant.js',
			),
			array(
				'id'          => 'ci-quality',
				'category'    => 'Quality Engineering',
				'title'       => 'Automated quality gates',
				'description' => 'Composer scripts, WordPress Coding Standards, PHP compatibility checks, PHPUnit integration tests and GitHub Actions make the repository reviewable.',
				'evidence'    => 'composer.json, phpcs.xml.dist, tests/, .github/workflows/quality.yml',
			),
		);
	}

	/**
	 * Return a compact, cached stats payload.
	 *
	 * @return array<string, mixed>
	 */
	public static function stats() {
		$cached = get_transient( SPCORE_CACHE_KEY );
		if ( is_array( $cached ) ) {
			return $cached;
		}

		$post_types = array( 'sp_project', 'sp_case_study', 'sp_service', 'sp_testimonial', 'sp_tool' );
		$counts     = array();

		foreach ( $post_types as $post_type ) {
			$count                = wp_count_posts( $post_type );
			$counts[ $post_type ] = $count && isset( $count->publish ) ? (int) $count->publish : 0;
		}

		$features = self::all();
		$stats    = array(
			'plugin_version'     => SPCORE_VERSION,
			'wordpress_version'  => get_bloginfo( 'version' ),
			'php_version'        => PHP_VERSION,
			'feature_count'      => count( $features ),
			'content_type_count' => 6,
			'taxonomy_count'     => 3,
			'rest_namespace'     => 'spcore/v1',
			'post_counts'        => $counts,
			'generated_at'       => gmdate( 'c' ),
		);

		set_transient( SPCORE_CACHE_KEY, $stats, 15 * MINUTE_IN_SECONDS );
		return $stats;
	}
}
