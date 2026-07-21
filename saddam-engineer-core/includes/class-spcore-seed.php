<?php
/**
 * Idempotent starter-content importer for Saddam Hossen's engineering site.
 *
 * @package SaddamEngineerCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class SPCore_Seed {

	/** Register the importer screen and action. */
	public static function register() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ), 20 );
		add_action( 'admin_post_spcore_seed', array( __CLASS__, 'run' ) );
	}

	/** Add the importer below the site settings page. */
	public static function menu() {
		add_submenu_page(
			'spcore-settings',
			__( 'Import Engineering Site', 'spcore' ),
			__( 'Starter Content', 'spcore' ),
			'manage_options',
			'spcore-seed',
			array( __CLASS__, 'page' )
		);
	}

	/** Render the importer. */
	public static function page() {
		$processed = isset( $_GET['seeded'] ) ? absint( wp_unslash( $_GET['seeded'] ) ) : -1;
		echo '<div class="wrap"><h1>' . esc_html__( 'Engineering Site Starter Content', 'spcore' ) . '</h1>';
		if ( $processed >= 0 ) {
			echo '<div class="notice notice-success"><p>' . esc_html( sprintf( __( 'Complete. %d records were created or updated.', 'spcore' ), $processed ) ) . '</p></div>';
		}
		echo '<p>' . esc_html__( 'Creates the homepage structure, Engineering and Experience pages, factual project and case-study content, practical technical articles, navigation and reading settings. Running it again updates seeded records instead of duplicating them.', 'spcore' ) . '</p>';
		echo '<p><strong>' . esc_html__( 'Important:', 'spcore' ) . '</strong> ' . esc_html__( 'Replace profile links and add only claims, testimonials and metrics you can verify.', 'spcore' ) . '</p>';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="spcore_seed">';
		wp_nonce_field( 'spcore_seed_action', 'spcore_seed_nonce' );
		submit_button( __( 'Import / Repair Site Content', 'spcore' ) );
		echo '</form></div>';
	}

	/** Execute the import. */
	public static function run() {
		if ( ! current_user_can( 'manage_options' ) || ! isset( $_POST['spcore_seed_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['spcore_seed_nonce'] ) ), 'spcore_seed_action' ) ) {
			wp_die( esc_html__( 'Permission denied.', 'spcore' ) );
		}

		self::retire_legacy_claims();
		self::rename_legacy_content();
		$count  = self::seed_projects();
		$count += self::seed_case_studies();
		$count += self::seed_services();
		$count += self::seed_pages();
		$count += self::seed_posts();
		$count += self::seed_tools();
		self::seed_menu();
		self::setup_reading();
		delete_transient( SPCORE_CACHE_KEY );
		flush_rewrite_rules();

		wp_safe_redirect( admin_url( 'admin.php?page=spcore-seed&seeded=' . $count ) );
		exit;
	}

	/** Find one post by title. */
	private static function existing_id( $post_type, $title ) {
		$posts = get_posts(
			array(
				'post_type'      => $post_type,
				'post_status'    => 'any',
				'title'          => $title,
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);
		return $posts ? absint( $posts[0] ) : 0;
	}

	/** Create or update a post and its prefixed metadata. */
	private static function upsert( $post_type, $title, $content, $meta = array(), $extra = array() ) {
		$id   = self::existing_id( $post_type, $title );
		$args = array_merge(
			array(
				'post_type'    => $post_type,
				'post_title'   => $title,
				'post_content' => $content,
				'post_status'  => 'publish',
			),
			$extra
		);
		if ( $id ) {
			$args['ID'] = $id;
			$id         = wp_update_post( $args, true );
		} else {
			$id = wp_insert_post( $args, true );
		}
		if ( is_wp_error( $id ) || ! $id ) {
			return 0;
		}
		foreach ( $meta as $key => $value ) {
			update_post_meta( $id, '_spcore_' . $key, $value );
		}
		update_post_meta( $id, '_spcore_seeded_release', 1 );
		return 1;
	}

	/** Create or update one page by path. */
	private static function page_upsert( $title, $slug, $content, $template = '', $excerpt = '' ) {
		$page = get_page_by_path( $slug, OBJECT, 'page' );
		$args = array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_content' => $content,
			'post_excerpt' => $excerpt,
		);
		if ( $page ) {
			$args['ID'] = $page->ID;
			$id         = wp_update_post( $args, true );
		} else {
			$id = wp_insert_post( $args, true );
		}
		if ( is_wp_error( $id ) || ! $id ) {
			return 0;
		}
		if ( $template ) {
			update_post_meta( $id, '_wp_page_template', $template );
		}
		update_post_meta( $id, '_spcore_seeded_release', 1 );
		return 1;
	}

	/** Retire v3 starter records containing demonstration-only claims. */
	private static function retire_legacy_claims() {
		$legacy = array(
			'sp_case_study' => array(
				'Custom Event Import System',
				'WooCommerce Performance Optimization',
				'High-Traffic Site Optimization',
				'WebP Optimizer WordPress Plugin',
				'Headless WordPress Architecture Planning',
				'WordPress Malware Cleanup Workflow',
			),
			'sp_testimonial' => array(
				'EventPrime Client ,  Custom Import',
				'SEO Client ,  eCommerce',
				'Migration Client ,  Migration',
				'Web Scraping Client ,  Automation',
			),
			'sp_service' => array(
				'AI Integration for WordPress',
				'Headless WordPress & Next.js Frontends',
				'AI-Ready WordPress Audits with 99BinaryLab / SiteProof AI',
			),
			'post' => array(
				'Why My Portfolio Uses a Theme and a Companion Plugin',
				'How I use AI without letting AI replace engineering judgment',
				'Headless WordPress vs traditional WordPress: when it makes sense',
				'How to choose a WordPress developer for a serious business website',
				'The tools I use to build faster WordPress websites',
				'My founder operating system: discipline, deep work, prayer and execution',
				'Best laptops for WordPress developers: what actually matters',
			),
		);
		foreach ( $legacy as $post_type => $titles ) {
			foreach ( $titles as $title ) {
				$id = self::existing_id( $post_type, $title );
				if ( $id ) {
					wp_update_post( array( 'ID' => $id, 'post_status' => 'draft' ) );
				}
			}
		}

		foreach ( array( 'booking-api-systems', 'headless-wordpress', 'performance', 'technical-seo' ) as $slug ) {
			$page = get_page_by_path( $slug, OBJECT, 'page' );
			if ( $page ) {
				wp_update_post( array( 'ID' => $page->ID, 'post_status' => 'draft' ) );
			}
		}
	}


	/** Rename legacy seeded records before upserting revised public titles. */
	private static function rename_legacy_content() {
		$renames = array(
			array( 'sp_project', 'SaddamHossen.dev ,  WordPress Engineering Platform', 'SaddamHossen.dev ,  WordPress Engineering Platform', '' ),
			array( 'sp_case_study', 'Turning a Developer Site into Verifiable Engineering Evidence', 'Building a Maintainable WordPress Engineering Platform', '' ),
			array( 'post', 'Why This WordPress Site Uses a Theme and a Companion Plugin', 'When a WordPress Site Needs a Theme and a Companion Plugin', 'when-wordpress-needs-theme-and-companion-plugin' ),
		);

		foreach ( $renames as $rename ) {
			$old_id = self::existing_id( $rename[0], $rename[1] );
			$new_id = self::existing_id( $rename[0], $rename[2] );
			if ( $old_id && ! $new_id ) {
				$args = array( 'ID' => $old_id, 'post_title' => $rename[2] );
				if ( $rename[3] ) {
					$args['post_name'] = $rename[3];
				}
				wp_update_post( $args );
			}
		}
	}

	/** Seed honest, representative project records. */
	private static function seed_projects() {
		$count    = 0;
		$projects = array(
			array(
				'title'   => 'Vissual Events ,  Multilingual Event Platform',
				'content' => '<p>A production WordPress platform for international fairs and events. The work includes data-driven event pages, multilingual URL behavior, plugin integration, templates, structured content and ongoing debugging across a complex live stack.</p>',
				'meta'    => array(
					'live_url'        => 'https://vissualevents.com/',
					'category_label'  => 'WordPress Platform Engineering',
					'industry_label'  => 'Events',
					'project_type'    => 'Multilingual data-driven WordPress platform',
					'client_location' => 'International / Spain',
					'summary'         => 'Data-driven event pages, multilingual architecture, custom plugin integration and production troubleshooting.',
					'problem'         => 'The platform combines generated event data, multilingual routing, reusable page sections and several WordPress plugins. Changes must preserve live URLs, SEO behavior and content consistency.',
					'role'            => 'WordPress development, debugging, template implementation, plugin analysis, data mapping and technical SEO investigation.',
					'what_built'      => 'Reusable event templates, field mappings, organizer-data integration, multilingual content behavior and investigation tooling for invalid URL generation.',
					'tech_stack'      => 'WordPress, PHP, Elementor, MPG, TranslatePress, JavaScript, CSS, MySQL',
					'business_value'  => 'A repeatable content system for publishing and maintaining event information across languages while reducing one-off page work.',
					'result'          => 'Established a maintainable implementation path and documented production issues for safe remediation.',
					'featured'        => '1',
				),
			),
			array(
				'title'   => 'SiteProof AI ,  WordPress Audit Plugin',
				'content' => '<p>An independently developed WordPress product exploring security, compliance, performance, AI search readiness and WooCommerce auditing. The plugin includes issue reporting, guarded fixes, backup/undo concepts and a structured admin experience.</p>',
				'meta'    => array(
					'category_label' => 'Plugin Product Engineering',
					'industry_label' => 'WordPress Software',
					'project_type'   => 'Custom WordPress audit plugin',
					'summary'        => 'A modular WordPress audit plugin with security, compliance, performance and AI search readiness workflows.',
					'problem'        => 'Site owners often receive disconnected warnings without a clear implementation or rollback path.',
					'role'           => 'Product definition, plugin architecture, admin UX, API integration, feature QA and market research.',
					'what_built'     => 'Module-based audits, issue scoring, guarded fixes, fix history, settings, API-key handling and free/pro positioning.',
					'tech_stack'     => 'WordPress, PHP, JavaScript, REST APIs, WPScan API, Claude API',
					'business_value' => 'A focused codebase for testing a practical audit-and-remediation workflow inside WordPress.',
					'result'         => 'Built a working product foundation and documented the remaining validation needed before public distribution.',
					'featured'       => '1',
					'nda'            => '1',
				),
			),
			array(
				'title'   => 'SaddamHossen.dev ,  WordPress Engineering Platform',
				'content' => '<p>This site is built as a maintainable WordPress engineering system: a custom theme owns presentation while a companion plugin owns portable data models, APIs, Gutenberg features, privacy, operations and quality tooling.</p>',
				'meta'    => array(
					'live_url'       => home_url( '/' ),
					'category_label' => 'WordPress Engineering Platform',
					'industry_label' => 'Developer Tools / Publishing',
					'project_type'   => 'Custom theme and companion plugin',
					'summary'        => 'A maintainable WordPress implementation with traceable feature-to-code evidence and clear operational boundaries.',
					'problem'        => 'A professional site needs structured publishing, reusable project data, secure lead handling and a clean separation between presentation and portable behavior.',
					'role'           => 'Architecture, development, content strategy, security review, QA and documentation.',
					'what_built'     => 'CPTs and taxonomies, registered metadata, REST routes, a dynamic block, WP-CLI, custom lead storage, privacy tools, Site Health, caching, tests and CI documentation.',
					'tech_stack'     => 'WordPress, PHP, JavaScript, Gutenberg, REST API, WP-CLI, PHPUnit, PHPCS, GitHub Actions',
					'business_value' => 'The system is easier to maintain, redesign and extend because content, APIs and operational behavior remain independent of the visual theme.',
					'result'         => 'Created a reusable engineering platform with documented architecture, observable workflows and a public source trail.',
					'featured'       => '1',
				),
			),
			array(
				'title'   => 'IndexAI ,  AI Visibility Service Website',
				'content' => '<p>A WordPress business website for an Australian AI search visibility service, including positioning, content structure, analytics/search-console setup and conversion paths.</p>',
				'meta'    => array(
					'live_url'       => 'https://indexai.com.au/',
					'category_label' => 'WordPress Business Website',
					'industry_label' => 'AI / Search Visibility',
					'project_type'   => 'Service website and technical launch setup',
					'summary'        => 'A structured service website with technical discovery and lead-generation foundations.',
					'role'           => 'Website implementation, content integration, analytics, search-console setup and ongoing optimization.',
					'tech_stack'     => 'WordPress, PHP, Google Analytics, Search Console, Bing Webmaster Tools',
					'result'         => 'Delivered a functional launch foundation ready for content, measurement and lead-generation iteration.',
				),
			),
		);

		foreach ( $projects as $project ) {
			$count += self::upsert( 'sp_project', $project['title'], $project['content'], $project['meta'] );
			$id     = self::existing_id( 'sp_project', $project['title'] );
			if ( $id ) {
				wp_set_object_terms( $id, $project['meta']['category_label'], 'sp_category', false );
				wp_set_object_terms( $id, $project['meta']['industry_label'], 'sp_industry', false );
			}
		}
		return $count;
	}

	/** Seed case studies without invented numbers or quotations. */
	private static function seed_case_studies() {
		$count = 0;
		$cases = array(
			array(
				'title' => 'Designing a Multilingual, Data-Driven Event Page System',
				'meta'  => array(
					'client_project'    => 'Vissual Events',
					'challenge'         => 'Generated event records, Elementor templates and four-language routing had to work together without creating inconsistent page structures or unsafe URL behavior.',
					'solution'          => 'Treat the event page as a structured system rather than a collection of manually edited pages. Map stable data fields to reusable sections and isolate plugin-specific responsibilities.',
					'technical_approach'=> 'Audited MPG fields and template output, connected organizer data from a custom plugin, preserved progressive fallbacks, and investigated hreflang, sitemap, canonical, JSON-LD and internal-link sources for duplicated language prefixes.',
					'stack'             => 'WordPress, PHP, Elementor, MPG, TranslatePress, JavaScript, technical SEO',
					'result'            => 'Produced a reusable page architecture and a safe investigation/fix path for a complex multilingual production environment.',
					'proof_metric'      => 'Reusable architecture · four-language environment',
					'cta_label'         => 'Review the project',
					'cta_url'           => 'https://vissualevents.com/',
				),
			),
			array(
				'title' => 'Building a Guarded WordPress Audit and Fix Workflow',
				'meta'  => array(
					'client_project'    => 'SiteProof AI',
					'challenge'         => 'Security and performance tools can overwhelm site owners with warnings or apply risky changes without enough context, verification or rollback support.',
					'solution'          => 'Organize checks into focused modules, separate findings from remediations and make fix history, permissions, backups and undo behavior part of the product design.',
					'technical_approach'=> 'Implemented modular audits, scoring, API integrations, guarded fix actions, state tracking and admin UX. Tested real hosting constraints such as blocked headers, file permissions and external API credit failures.',
					'stack'             => 'WordPress plugin APIs, PHP, JavaScript, REST APIs, WPScan, Claude API',
					'result'            => 'Created a functioning product foundation while keeping unresolved market and distribution assumptions explicit.',
					'proof_metric'      => 'Modular audit · fix history · rollback thinking',
					'cta_label'         => 'Discuss plugin engineering',
					'cta_url'           => home_url( '/contact/' ),
				),
			),
			array(
				'title' => 'Building a Maintainable WordPress Engineering Platform',
				'meta'  => array(
					'client_project'    => 'SaddamHossen.dev',
					'challenge'         => 'The site needed to present real work, publish technical guidance and handle enquiries without coupling structured data and operational behavior to one visual theme.',
					'solution'          => 'Separate presentation from portable WordPress behavior, then document the boundaries so future changes remain predictable.',
					'technical_approach'=> 'Separated theme and plugin responsibilities; added versioned REST routes, a dynamic block, WP-CLI, privacy tools, Site Health, cache lifecycle, CI/tests and feature-to-file documentation. Replaced unverifiable marketing metrics with traceable evidence.',
					'stack'             => 'WordPress, PHP, JavaScript, Gutenberg, REST, WP-CLI, PHPUnit, PHPCS, GitHub Actions',
					'result'            => 'Created a codebase that supports ongoing publishing, source review and future visual iteration without migrating core content.',
					'proof_metric'      => 'Theme/plugin separation · documented quality gates',
					'cta_label'         => 'Explore the architecture',
					'cta_url'           => home_url( '/engineering/' ),
				),
			),

			array(
				'title' => 'Designing a Privacy-Conscious WordPress Lead Workflow',
				'meta'  => array(
					'client_project'     => 'SaddamHossen.dev contact system',
					'challenge'          => 'A public contact form needs to preserve legitimate enquiries while limiting spam, protecting secrets and keeping personal data manageable inside WordPress.',
					'solution'           => 'Use layered validation, bounded storage, explicit retention settings and native WordPress privacy hooks instead of treating form delivery as a single email action.',
					'technical_approach' => 'Added nonce checks, honeypot and timing controls, field limits, inquiry allowlists, rate limiting, optional CAPTCHA, safe webhook delivery, lead status tracking, personal-data export and erasure, and scheduled retention cleanup.',
					'stack'              => 'WordPress, PHP, AJAX, custom database table, Privacy API, Cron, HTTP API',
					'result'             => 'Created a contact workflow that remains useful when email delivery fails and gives administrators a documented path for data export, erasure and retention.',
					'proof_metric'       => 'Layered validation · privacy hooks · retention lifecycle',
					'cta_label'          => 'Review contact architecture',
					'cta_url'            => home_url( '/engineering/' ),
				),
			),
		);
		foreach ( $cases as $case ) {
			$count += self::upsert( 'sp_case_study', $case['title'], '', $case['meta'] );
			$id     = self::existing_id( 'sp_case_study', $case['title'] );
			if ( $id ) {
				wp_set_object_terms( $id, 'Engineering Case Study', 'sp_category', false );
			}
		}
		return $count;
	}

	/** Seed a concise capability archive without making it the primary identity. */
	private static function seed_services() {
		$count    = 0;
		$services = array(
			array( 'Custom WordPress Plugin Engineering', 'Custom plugin architecture, integrations, admin workflows and maintainable domain logic.', 'Product teams and businesses that have outgrown generic plugins.', "Requirements and data model\nPlugin architecture\nSecure admin or frontend workflow\nTests and documentation", 'PHP, OOP, WordPress APIs, REST and WP-CLI' ),
			array( 'WooCommerce Engineering', 'Checkout, catalog, order and integration work implemented through extension points instead of fragile template hacks.', 'Stores that need custom business logic or external system integration.', "Extension-point audit\nCustom business rules\nAPI or webhook integration\nRegression QA", 'Maintainable commerce behavior with documented edge cases' ),
			array( 'Gutenberg & Native Editing', 'Dynamic blocks and editing workflows that keep content structured, reusable and accessible.', 'Teams moving from page-builder dependence toward native WordPress.', "Block schema\nEditor controls\nServer rendering where appropriate\nResponsive and accessibility QA", 'A better editor experience without duplicating data' ),
			array( 'Performance & Production Debugging', 'Root-cause investigation across PHP, database queries, assets, caching, integrations and hosting behavior.', 'Production sites with slow pages, conflicts or hard-to-reproduce failures.', "Reproduction and profiling\nRoot-cause report\nTargeted implementation\nVerification and rollback notes", 'Evidence-led fixes instead of plugin guessing' ),
			array( 'WordPress Technical Support Engineering', 'Structured diagnosis and clear communication for product customers, agencies and internal teams.', 'WordPress product companies and remote support teams.', "Issue reproduction\nEnvironment and conflict analysis\nActionable written response\nEscalation notes", 'Customer empathy combined with engineering judgment' ),
		);
		foreach ( $services as $service ) {
			$count += self::upsert(
				'sp_service',
				$service[0],
				'',
				array(
					'short_desc'   => $service[1],
					'who_for'      => $service[2],
					'deliverables' => $service[3],
					'outcome'      => $service[4],
					'cta_label'    => 'Discuss the role or project',
					'cta_url'      => home_url( '/contact/' ),
				)
			);
		}
		return $count;
	}

	/** Seed public pages. */
	private static function seed_pages() {
		$count  = self::page_upsert( 'Home', 'home', '<!-- Rendered by front-page.php. -->' );
		$count += self::page_upsert( 'Engineering', 'engineering', '<p>The Engineering page explains the system architecture, API contract, quality gates and operational decisions used across this WordPress site.</p>', 'template-engineering.php', 'Explore the code-backed WordPress engineering features implemented across this site.' );
		$count += self::page_upsert( 'Experience', 'experience', '<p>The Experience page presents the path from frontend development and WordPress support to independent product and platform delivery.</p>', 'template-experience.php', 'WordPress engineering, technical support and frontend experience.' );
		$count += self::page_upsert( 'Blog', 'blog', '' );
		$count += self::page_upsert( 'Recommended Tools', 'recommended-tools', '<p>These recommendations are organized around real WordPress engineering jobs: development, debugging, source control, email delivery, backup, edge delivery and performance. Approved affiliate URLs can be added separately from normal product links in the WordPress admin.</p>', 'template-tools.php', 'A transparent, affiliate-ready WordPress tools catalogue with practical selection criteria and clear disclosures.' );
		$count += self::page_upsert(
			'About',
			'about',
			'<h2>Senior WordPress engineering with a support mindset</h2><p>I am Saddam Hossen, a WordPress engineer based in Bangladesh and working remotely across time zones. My background includes frontend development, a WordPress technical-support role and independent delivery for international clients.</p><p>I focus on custom plugins, WooCommerce, Gutenberg, integrations, performance and production debugging. I am comfortable moving between source code, browser behavior, WordPress data, server constraints and clear stakeholder communication.</p><h2>How this site is engineered</h2><p>This website is not built with a page builder. The custom theme owns presentation while a companion plugin owns content models, registered metadata, REST routes, a dynamic Gutenberg block, WP-CLI commands, lead storage, privacy tooling, Site Health checks, cache lifecycle and starter content.</p><h2>Current goal</h2><p>I am looking for a stable full-time remote role where I can contribute as a WordPress Engineer, Plugin or WooCommerce Developer, or Technical Support Engineer inside a strong product or engineering team.</p><p><a href="/engineering/">Explore the engineering system </a> · <a href="/contact/">Start a conversation </a></p>'
		);
		$count += self::page_upsert( 'Contact', 'contact', '<p>Share the product context, team responsibilities, technical stack and the problems that need attention. For a project, include the current URL, constraints and desired outcome.</p>', 'template-contact.php' );
		$count += self::page_upsert( 'Privacy Policy', 'privacy-policy', '<p>This site stores information submitted through the contact form so the message can be reviewed and answered. IP-address storage is disabled by default. Administrators can configure a retention period, and the companion plugin integrates with the WordPress personal-data exporter and eraser.</p><p>The guided site assistant processes its predefined navigation responses in the browser. When optional AI mode is enabled, a visitor question is sent through this WordPress server to the configured AI provider or owner-controlled webhook. The public page never receives the private API key.</p><p>Messages may also be sent by email or to a configured webhook. Do not submit passwords, access tokens, private client data or other secrets through the public form or assistant.</p>' );
		$count += self::page_upsert( 'Terms', 'terms', '<p>Site information is provided to explain professional experience and engineering work. Project details may be summarized to protect client confidentiality. No performance, security, ranking or business outcome is guaranteed.</p><p>Some Recommended Tools links may be affiliate links. Those relationships are disclosed, and affiliate links are qualified appropriately. A recommendation is not a warranty, and visitors should verify current pricing, terms and suitability with the vendor.</p><p>WordPress is a trademark of the WordPress Foundation. Other product and company names belong to their respective owners.</p>' );
		return $count;
	}

	/** Seed long-form, practical and non-duplicative technical writing. */
	private static function seed_posts() {
		$count = 0;
		$categories = array();
		foreach ( array( 'WordPress Engineering', 'APIs & Integrations', 'Production Operations', 'SEO & AI Search', 'Tools & Workflow' ) as $category_name ) {
			$term = term_exists( $category_name, 'category' );
			if ( ! $term ) {
				$term = wp_insert_term( $category_name, 'category' );
			}
			if ( ! is_wp_error( $term ) ) {
				$categories[ $category_name ] = absint( is_array( $term ) ? $term['term_id'] : $term );
			}
		}

		$posts = array(
			array(
				'title'    => 'How I Structure a Maintainable WordPress Plugin',
				'slug'     => 'how-i-structure-a-maintainable-wordpress-plugin',
				'category' => 'WordPress Engineering',
				'excerpt'  => 'A practical architecture guide for separating responsibilities, protecting boundaries and making a custom WordPress plugin easier to test, operate and extend.',
				'content'  => <<<'HTML'
<p>Plugin maintainability is not created by adding more folders. It comes from making responsibilities visible, keeping WordPress boundaries explicit and designing failure paths before a feature reaches production. This is the approach I use when a plugin must survive changing requirements, multiple developers and real support incidents.</p>
<div class="sp-article-summary"><strong>What you will learn</strong><ul><li>How to decide what the plugin owns.</li><li>How to separate bootstrapping, domain logic and WordPress integration.</li><li>Where capabilities, nonces, sanitization and escaping belong.</li><li>How to make the plugin observable through tests, CLI and Site Health.</li></ul></div>
<h2>Start with ownership, not classes</h2>
<p>Before opening the editor, write down the data and behavior the plugin owns. A booking plugin might own availability rules and reservation state, but it should not silently own the site’s typography or global navigation. Clear ownership prevents the plugin from turning into a second theme.</p>
<p>I normally answer five questions first:</p>
<ol><li>What business capability does this plugin provide?</li><li>Which records must remain available if the theme changes?</li><li>Which actions require administrator, editor or customer permissions?</li><li>What external systems can fail?</li><li>How will an operator diagnose and recover from failure?</li></ol>
<h2>Keep the bootstrap intentionally boring</h2>
<p>The main plugin file should define stable constants, load classes and start modules at predictable hooks. It should not contain hundreds of lines of feature logic. A boring bootstrap is easier to review during an incident because there are fewer hidden side effects.</p>
<pre><code>// Main plugin file.
define( 'ACME_VERSION', '1.0.0' );
require_once __DIR__ . '/includes/class-acme-rest.php';
require_once __DIR__ . '/includes/class-acme-orders.php';

add_action( 'plugins_loaded', static function () {
    Acme_Orders::instance();
    Acme_REST::instance();
} );</code></pre>
<p>In a larger codebase I use Composer autoloading, but the architectural goal stays the same: loading code and running business behavior are different responsibilities.</p>
<h2>Organize modules around capabilities</h2>
<p>Folders such as <code>admin</code> and <code>frontend</code> can be useful, but they are not enough to describe a system. I prefer modules that answer a maintainer’s question directly: registration, metadata, REST, payments, privacy, caching, CLI or scheduled maintenance.</p>
<table><thead><tr><th>Module</th><th>Owns</th><th>Should not own</th></tr></thead><tbody><tr><td>Orders</td><td>Order state and transitions</td><td>Global page layout</td></tr><tr><td>REST</td><td>Route contracts and authorization</td><td>Unrelated database migrations</td></tr><tr><td>Privacy</td><td>Export, erasure and retention</td><td>Marketing consent assumptions</td></tr><tr><td>Cache</td><td>Keys and invalidation</td><td>Permanent source-of-truth data</td></tr></tbody></table>
<h2>Use the right WordPress storage model</h2>
<p>Custom post types work well for editorial records that benefit from revisions, the admin UI and WordPress permissions. Registered post meta provides a defined schema and REST compatibility. A custom table becomes reasonable when query patterns, indexing, retention or write volume do not fit the posts table.</p>
<p>The decision should come from access patterns, not from a rule that every plugin must use CPTs or that every serious plugin needs custom tables.</p>
<h2>Protect every input and action boundary</h2>
<p>Security is a chain. A nonce helps verify intent, but it does not grant permission. A capability check answers whether the current user may perform the action. Sanitization normalizes incoming data. Validation rejects data that does not meet the contract. Escaping happens when a value is rendered into HTML, an attribute, a URL or JavaScript.</p>
<pre><code>if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( esc_html__( 'Permission denied.', 'acme' ) );
}
check_admin_referer( 'acme_save_settings' );
$value = isset( $_POST['acme_label'] )
    ? sanitize_text_field( wp_unslash( $_POST['acme_label'] ) )
    : '';</code></pre>
<h2>Design external integrations for failure</h2>
<p>Remote APIs time out, change response shape and return rate-limit errors. Use the WordPress HTTP API, set a realistic timeout, validate the status code and body, and make the user-facing failure recoverable. Do not log secrets. Do not retry indefinitely during a page request.</p>
<p>For important writes, I prefer an explicit status such as pending, delivered or failed, plus a safe retry path. That gives support teams evidence instead of forcing them to guess.</p>
<h2>Make cache invalidation part of the feature</h2>
<p>Adding a transient is easy. Knowing when it becomes stale is the actual design work. Define the key, lifetime and every event that invalidates it. Save, delete, taxonomy and settings hooks often matter more than the cache call itself.</p>
<h2>Build an operational surface</h2>
<p>A production plugin should tell an operator enough to diagnose it. Depending on the product, that may include a WP-CLI status command, Site Health information, structured logs, a migration version, scheduled-task visibility and a manual cache clear action.</p>
<h2>Quality checklist before release</h2>
<ul><li>Run PHP syntax, coding-standard and compatibility checks.</li><li>Test capabilities with more than one user role.</li><li>Test nonces, invalid input and empty states.</li><li>Exercise activation, upgrade, deactivation and uninstall behavior.</li><li>Verify cache invalidation and scheduled events.</li><li>Test with debugging enabled and review the browser console.</li><li>Document installation, architecture, trade-offs and known limitations.</li></ul>
<h2>The practical outcome</h2>
<p>A maintainable plugin is not one that predicts every future requirement. It is one where the next engineer can locate responsibility, understand the data contract, reproduce a failure and make a change without accidentally rewriting the entire system.</p>
HTML,
			),
			array(
				'title'    => 'WordPress REST API Routes: Permission, Validation and Caching',
				'slug'     => 'wordpress-rest-api-permission-validation-caching',
				'category' => 'APIs & Integrations',
				'excerpt'  => 'A route-by-route checklist for building WordPress REST APIs with explicit permissions, constrained arguments, predictable responses and safe cache invalidation.',
				'content'  => <<<'HTML'
<p>A REST route is a public contract, even when it is used only by your own block or admin screen. The callback is only one part of that contract. Permissions, input rules, response shape, errors, pagination and caching determine whether the route remains trustworthy in production.</p>
<div class="sp-article-summary"><strong>Core principle</strong><p>Make the route boring for its consumer: explicit arguments, stable output, useful errors and no hidden authorization assumptions.</p></div>
<h2>Choose a versioned namespace</h2>
<p>Use a namespace such as <code>acme/v1</code>. The version is not decoration; it gives you room to introduce a changed contract later without breaking existing clients.</p>
<pre><code>register_rest_route(
    'acme/v1',
    '/projects',
    array(
        'methods'             =&gt; WP_REST_Server::READABLE,
        'callback'            =&gt; array( $this, 'projects' ),
        'permission_callback' =&gt; '__return_true',
        'args'                =&gt; $this-&gt;project_args(),
    )
);</code></pre>
<h2>Authentication and authorization are different</h2>
<p>Authentication identifies the requester. Authorization decides whether that requester may perform this action. A logged-in user is not automatically allowed to read private records or change settings.</p>
<p>Public read-only site data can intentionally use <code>__return_true</code>. Administrative routes should check the narrowest relevant capability, not simply whether a user exists.</p>
<h2>Describe arguments in the route</h2>
<p>Argument definitions create one reviewable place for defaults, sanitization and validation. They also help generated API documentation and reduce callback branching.</p>
<pre><code>'page' =&gt; array(
    'default'           =&gt; 1,
    'sanitize_callback' =&gt; 'absint',
    'validate_callback' =&gt; static function ( $value ) {
        return $value &gt;= 1;
    },
),
'per_page' =&gt; array(
    'default'           =&gt; 10,
    'sanitize_callback' =&gt; 'absint',
    'validate_callback' =&gt; static function ( $value ) {
        return $value &gt;= 1 &amp;&amp; $value &lt;= 50;
    },
),</code></pre>
<h2>Return resources, not database internals</h2>
<p>A consumer should not need to understand your post-meta naming convention. Build a deliberate response object with the fields the client needs. This also prevents accidental leakage of private metadata.</p>
<table><thead><tr><th>Good contract</th><th>Fragile contract</th></tr></thead><tbody><tr><td><code>title</code>, <code>summary</code>, <code>url</code></td><td>Entire raw <code>WP_Post</code> plus all meta</td></tr><tr><td>ISO date</td><td>Server-specific formatted date</td></tr><tr><td>Documented nullable field</td><td>Field disappears unpredictably</td></tr></tbody></table>
<h2>Use meaningful HTTP errors</h2>
<p>Return <code>WP_Error</code> with a stable machine-readable code, a safe message and an appropriate status. A validation error, permission error and unavailable dependency should not all look like a generic 500 response.</p>
<h2>Paginate and constrain expensive queries</h2>
<p>Every collection route needs a hard upper bound. Add total and total-page headers when useful. Avoid unbounded meta queries, and request only the fields or IDs the callback needs.</p>
<h2>Cache the query result, not the permission decision</h2>
<p>Read-heavy public endpoints can benefit from transients or object caching. Include relevant inputs in the cache key. More importantly, list the events that invalidate the cache: content save, delete, term changes or settings updates.</p>
<pre><code>$key = 'acme_projects_' . md5( wp_json_encode( $args ) );
$data = get_transient( $key );
if ( false === $data ) {
    $data = $this-&gt;query_projects( $args );
    set_transient( $key, $data, 10 * MINUTE_IN_SECONDS );
}</code></pre>
<h2>Add operational headers carefully</h2>
<p>Pagination headers, cache-state headers in development and a request identifier can make support easier. Never expose secrets, raw SQL or sensitive environment details.</p>
<h2>Test the contract from the outside</h2>
<ul><li>Anonymous, subscriber and administrator permissions.</li><li>Missing, malformed, boundary and excessive arguments.</li><li>Empty collections and deleted resources.</li><li>Cache hit, miss and invalidation.</li><li>Consumer behavior when an optional field is absent.</li><li>Response time under realistic data volume.</li></ul>
<h2>Final review question</h2>
<p>Could another developer build a client against this route using only the contract you expose? If the answer depends on reading the database or guessing permissions, the route is not finished.</p>
HTML,
			),
			array(
				'title'    => 'When a WordPress Site Needs a Theme and a Companion Plugin',
				'slug'     => 'when-wordpress-needs-theme-and-companion-plugin',
				'category' => 'WordPress Engineering',
				'excerpt'  => 'A practical look at the portability, coupling and operational trade-offs behind separating site presentation from WordPress data and behavior.',
				'content'  => <<<'HTML'
<p>A professional WordPress site can look simple, but it often contains several kinds of responsibility: presentation, structured projects, contact workflows, APIs, privacy, analytics and operational tooling. I split this implementation into a custom theme and a companion plugin so those responsibilities can be inspected separately.</p>
<h2>The decision in one sentence</h2>
<p>The theme owns how the site looks; the plugin owns data and behavior that should survive a visual redesign.</p>
<h2>What belongs to the theme</h2>
<p>The theme controls templates, visual hierarchy, navigation, responsive behavior, typography and progressive interactions. If I replace the theme, the site should look different, but its projects, case studies and stored leads should not disappear.</p>
<ul><li>Homepage and archive templates</li><li>Article reading layout and table of contents</li><li>Responsive navigation and footer</li><li>Design tokens, cards and component styling</li><li>Small enhancements such as reading progress and back to top</li></ul>
<h2>What belongs to the companion plugin</h2>
<p>The plugin registers content models and taxonomies, defines metadata, exposes REST routes, renders a dynamic Gutenberg block, stores contact leads, integrates privacy tools and provides operational commands.</p>
<p>This separation lets a maintainer deactivate the theme conceptually and ask: which capabilities still make sense? Projects and their metadata do. A hero layout does not.</p>
<h2>Why not put everything in the theme?</h2>
<p>Putting CPTs and business behavior in a theme is fast at the beginning. The coupling becomes visible when the theme changes or the same data must be consumed by an API, block or command-line task. A design change should not require a content migration.</p>
<h2>Why not build one large plugin for everything?</h2>
<p>The opposite extreme also creates friction. A plugin that prints every visual section can make the theme a thin shell and limit normal WordPress template behavior. Presentation changes then become plugin releases even though the business behavior is unchanged.</p>
<h2>The cost of two packages</h2>
<p>There are real trade-offs:</p>
<ul><li>Installation and version compatibility must be documented.</li><li>The theme needs standalone-safe fallbacks.</li><li>The plugin needs output that is not tightly coupled to one theme.</li><li>Integration QA covers more combinations.</li></ul>
<p>For this site, that cost is worthwhile because the content, API and operational workflows are expected to survive future redesigns. For a simple brochure site, a smaller solution might be more appropriate.</p>
<h2>How the packages communicate</h2>
<p>The theme reads settings through a small wrapper and renders plugin shortcodes only when they exist. The plugin does not assume a theme template path. Shared class names are prefixed, and the public API is intentionally narrow.</p>
<h2>What I would change at larger scale</h2>
<p>For a commercial product I would add a formal compatibility matrix, semantic release automation, migration tests, more complete integration tests and possibly shared Composer packages for contracts. The current project keeps those ideas visible without pretending to be a multi-team platform.</p>
<h2>The decision rule I use</h2>
<p>Two packages are not automatically better. The useful question is whether data and behavior should survive a theme change. Architecture is a decision process based on ownership, coupling, portability and operational cost, not a folder diagram.</p>
HTML,
			),
			array(
				'title'    => 'A Production Debugging Checklist for WordPress',
				'slug'     => 'production-debugging-checklist-wordpress',
				'category' => 'Production Operations',
				'excerpt'  => 'A repeatable incident workflow for preserving evidence, reducing the search space, validating a fix and communicating clearly during WordPress production problems.',
				'content'  => <<<'HTML'
<p>Fast debugging is not a sequence of random fixes. It is a disciplined reduction of uncertainty. The first objective is to protect the site and preserve evidence; the second is to isolate the failing layer; only then should code or configuration change.</p>
<div class="sp-article-summary"><strong>Incident rule</strong><p>Change one variable at a time, record what you observed and keep a rollback path.</p></div>
<h2>1. Define the symptom and impact</h2>
<p>“The site is broken” is not actionable. Write the exact URL, user role, device, time, expected behavior and observed behavior. Clarify whether the issue affects one visitor, one workflow or the whole site.</p>
<h2>2. Stabilize before investigating</h2>
<p>If data loss, checkout failure or active compromise is possible, reduce further damage first. That might mean maintenance mode, disabling a failing integration, pausing a queue or taking a verified backup. Avoid destructive cleanup before evidence is captured.</p>
<h2>3. Build a timeline</h2>
<p>Check recent deploys, plugin/theme updates, content imports, DNS/CDN changes, server changes and third-party incidents. Correlation is not proof, but the timeline creates useful hypotheses.</p>
<h2>4. Collect evidence</h2>
<ul><li>WordPress debug log and PHP/server logs</li><li>Browser console and network requests</li><li>HTTP status, headers and redirect chain</li><li>Database/query behavior</li><li>Cron and queue state</li><li>Cache/CDN state</li><li>External API response and timeout behavior</li></ul>
<p>Remove or redact credentials and personal data before sharing logs.</p>
<h2>5. Reproduce safely</h2>
<p>A staging copy with similar data and configuration is ideal. When reproduction requires production, use the lowest-risk read-only observation possible and define the stop condition in advance.</p>
<h2>6. Isolate the layer</h2>
<table><thead><tr><th>Layer</th><th>Useful question</th></tr></thead><tbody><tr><td>Browser</td><td>Does it reproduce in a clean session and another browser?</td></tr><tr><td>Theme</td><td>Is the output wrong before or after JavaScript runs?</td></tr><tr><td>Plugin</td><td>Which hook or integration owns the behavior?</td></tr><tr><td>Data</td><td>Is one record malformed, missing or duplicated?</td></tr><tr><td>Cache/CDN</td><td>Is the origin correct while an edge serves stale output?</td></tr><tr><td>Server</td><td>Are PHP, memory, filesystem or upstream limits involved?</td></tr></tbody></table>
<h2>7. Form a falsifiable hypothesis</h2>
<p>A useful hypothesis predicts what you should observe. “The cache is broken” is vague. “The page cache key does not vary by language, so the first translated response is reused” can be tested.</p>
<h2>8. Apply the smallest reversible change</h2>
<p>Prefer a targeted patch or configuration change over several simultaneous updates. Record the previous value, deploy path and rollback step. A successful fix that cannot be explained is still operational risk.</p>
<h2>9. Verify beyond the happy path</h2>
<p>Retest the original reproduction and adjacent cases: user roles, languages, mobile, cached/uncached sessions, invalid input and scheduled behavior. Review logs again after the fix.</p>
<h2>10. Close the incident properly</h2>
<p>Document impact, timeline, root cause, resolution and prevention. Prevention might be a test, monitoring alert, validation rule, runbook or clearer deployment gate.</p>
<h2>Communication template</h2>
<blockquote><p><strong>Impact:</strong> who and what is affected.<br><strong>Current status:</strong> stable, investigating or fixed.<br><strong>Evidence:</strong> what has been confirmed.<br><strong>Next action:</strong> the next safe test and expected update time.<br><strong>Risk:</strong> what must not be changed yet.</p></blockquote>
<h2>Why this matters for support engineers</h2>
<p>Strong support is engineering work. It connects user reports to browser behavior, WordPress internals, infrastructure and clear decisions. The goal is not only to close a ticket, it is to make the next incident less likely and easier to diagnose.</p>
HTML,
			),
			array(
				'title'    => 'WordPress SEO and AEO for AI Search: A Practical Engineering Guide',
				'slug'     => 'seo-aeo-wordpress-ai-search',
				'category' => 'SEO & AI Search',
				'excerpt'  => 'A no-hype technical guide to crawlability, useful content, structured data, authorship and machine-readable discovery for search engines and AI search experiences.',
				'content'  => <<<'HTML'
<p>"AEO" is often sold as a separate switch that makes a website appear in AI answers. In practice, the strongest foundation is still excellent technical SEO plus content that is clear, original, attributable and easy to extract without losing its meaning. This guide explains what I implement in WordPress and what I refuse to promise.</p>
<div class="sp-article-summary"><strong>Important expectation</strong><p>No plugin or schema block can guarantee rankings, citations or inclusion in an AI-generated answer. The goal is to remove technical ambiguity and publish information worth retrieving.</p></div>
<h2>Start with crawl and index control</h2>
<p>Search engines and AI-powered search systems cannot use pages they cannot reliably discover or interpret. Confirm that important pages return a successful status, are internally linked, have a self-referencing canonical URL and are not blocked by robots directives.</p>
<ul><li>Use clean, stable permalinks.</li><li>Keep one preferred URL for each page.</li><li>Submit the WordPress XML sitemap in search-console tools.</li><li>Noindex thin search, date and author archives when they add no unique value.</li><li>Fix broken internal links and redirect obsolete URLs deliberately.</li></ul>
<h2>Write a distinct search summary for every important page</h2>
<p>The title, visible H1, excerpt and meta description should describe the same page without being identical boilerplate. A good article excerpt explains the reader outcome; it should not simply repeat the first paragraph.</p>
<h2>Use semantic page structure</h2>
<p>One descriptive H1, useful H2 sections, lists, tables and code examples help humans scan the page and help machines identify its parts. Headings should represent questions or decisions, not visual styling.</p>
<h2>Show authorship and evidence</h2>
<p>Technical advice is more useful when readers can see who wrote it, when it was updated and which implementation supports the claim. Link to an author/profile page, relevant project or repository where appropriate. Do not create fake reviews, credentials or performance numbers for perceived authority.</p>
<h2>Use structured data that matches the page</h2>
<p>Structured data is a machine-readable description, not a place to add invisible marketing claims. A professional engineering site can reasonably use:</p>
<table><thead><tr><th>Page</th><th>Useful schema</th></tr></thead><tbody><tr><td>Homepage/profile</td><td>WebSite, Person, ProfilePage</td></tr><tr><td>Technical article</td><td>BlogPosting and BreadcrumbList</td></tr><tr><td>Project archive</td><td>CollectionPage</td></tr><tr><td>Normal page</td><td>WebPage</td></tr><tr><td>Visible FAQ</td><td>FAQPage only when the same questions and answers appear on the page</td></tr></tbody></table>
<p>Avoid publishing SoftwareApplication, review or rating markup unless the visible page contains accurate supporting information and meets the relevant eligibility rules.</p>
<h2>Build internal topic paths</h2>
<p>One isolated article is less useful than a connected body of work. Link plugin architecture articles to REST, debugging and quality-gate articles. Link engineering pages to the project or case study that proves the implementation. Use descriptive anchor text rather than repeating “click here.”</p>
<h2>Make the article genuinely useful</h2>
<p>AI-assisted drafting can accelerate research and editing, but scaled generic pages create repetition and no distinctive value. Add real decisions, examples, trade-offs, checklists and limitations. Remove paragraphs that could appear unchanged on any website.</p>
<h2>Consider AI discovery files carefully</h2>
<p>An <code>llms.txt</code> file can provide an experimental plain-text overview for systems that choose to read it. It does not replace robots.txt, sitemaps, normal HTML, canonical tags or high-quality content. Treat the human-readable page as the source of truth.</p>
<h2>Technical implementation checklist</h2>
<ul><li>Unique title, H1, excerpt and meta description</li><li>Canonical URL and correct robots directive</li><li>XML sitemap discovery</li><li>Open Graph and social image fallback</li><li>Accurate contextual JSON-LD</li><li>Breadcrumbs and internal related-reading links</li><li>Visible author and updated date</li><li>Fast, responsive, accessible reading experience</li><li>No duplicated or fabricated content</li><li>Analytics and search-console measurement</li></ul>
<h2>How to measure progress</h2>
<p>Track indexed pages, impressions, queries, clicks, crawl errors and the performance of individual articles. For AI search, record referral traffic and manually verify citations when they appear, but do not treat anecdotal screenshots as a complete measurement system.</p>
<h2>The engineering standard</h2>
<p>A technically optimized page still needs a reason to exist. My standard is simple: a reader should leave with a clearer decision, a safer implementation or a reusable workflow. SEO and AI search readiness should improve access to that value, not substitute for it.</p>
HTML,
			),
			array(
				'title'    => 'How to Build a Client-Editable WordPress Site Without a Page Builder',
				'slug'     => 'client-editable-wordpress-without-page-builder',
				'category' => 'WordPress Engineering',
				'excerpt'  => 'A practical architecture for giving non-technical clients safe control over text, images and repeatable content without making every page dependent on a visual builder.',
				'content'  => <<<'HTML'
<p>A custom WordPress build should not force a non-technical client to contact a developer every time a heading, image or call to action changes. It should also avoid giving every editor unrestricted control over layout, spacing and component behavior. The practical goal is a controlled editing system: simple changes stay simple, repeatable records stay structured and complex functionality stays protected.</p>
<div class="sp-article-summary"><strong>Architecture in one sentence</strong><p>Use core blocks for normal content, patterns for reusable sections, custom post types and registered fields for repeatable records, and custom blocks only for functionality that genuinely needs a data contract.</p></div>
<h2>Start by classifying the content</h2>
<p>Before choosing a page builder, ACF field group or custom block, classify what the client needs to edit. The editing tool should follow the content shape.</p>
<table><thead><tr><th>Content</th><th>Best editing model</th><th>Reason</th></tr></thead><tbody><tr><td>Page copy, headings, lists and normal images</td><td>Core blocks</td><td>Native, accessible and familiar</td></tr><tr><td>Reusable hero, CTA or feature layout</td><td>Block pattern</td><td>Approved structure with editable content</td></tr><tr><td>Projects, services, team members or events</td><td>Custom post type plus fields</td><td>Repeatable records need a stable schema</td></tr><tr><td>Interactive calculator, API viewer or filtered directory</td><td>Custom block</td><td>Complex behavior needs a controlled contract</td></tr><tr><td>Sitewide email, profile links and availability</td><td>Settings page</td><td>One source of truth across templates</td></tr></tbody></table>
<h2>Use core blocks for normal editorial work</h2>
<p>Paragraphs, headings, images, galleries, columns, buttons and lists already have mature WordPress blocks. Rebuilding them as custom fields creates unnecessary maintenance and often gives editors a worse experience.</p>
<p>Global styles should control typography, spacing, colors and button design. The editor changes the content; the theme protects the visual system.</p>
<h2>Use patterns for approved section layouts</h2>
<p>A pattern is a reusable arrangement of blocks. It gives an editor a starting layout without locking the whole page into a proprietary builder. A theme can register patterns from its <code>/patterns</code> directory, and the editor can replace the copy and media after insertion.</p>
<pre><code>&lt;?php
/**
 * Title: Service feature grid
 * Slug: client-theme/service-feature-grid
 * Categories: client-sections
 */
?&gt;
&lt;!-- wp:columns --&gt;
... editable core blocks ...
&lt;!-- /wp:columns --&gt;</code></pre>
<p>Patterns are a strong fit for hero sections, trust rows, service grids, split content, testimonials, FAQs and calls to action. They reduce design drift without removing normal editing.</p>
<h2>Use structured records for repeatable business content</h2>
<p>If a client manages fifty events, products or team members, each record should use the same fields. A custom post type provides revisions, permissions, admin screens and WordPress-native querying. Registered metadata defines fields such as venue, date, role, URL or summary.</p>
<pre><code>register_post_meta(
    'client_event',
    '_client_venue',
    array(
        'type'              =&gt; 'string',
        'single'            =&gt; true,
        'show_in_rest'      =&gt; true,
        'sanitize_callback' =&gt; 'sanitize_text_field',
        'auth_callback'     =&gt; static function () {
            return current_user_can( 'edit_posts' );
        },
    )
);</code></pre>
<p>The template reads the structured data. The client edits a form with clear business labels. This is safer than asking the client to duplicate and rearrange complex page layouts for every record.</p>
<h2>Use custom blocks only when the interface needs one</h2>
<p>A custom block is justified when the editor needs a purpose-built interface or the frontend needs a stable server-side data contract. Examples include an event selector, pricing comparison, API-powered directory or engineering evidence component.</p>
<p>Prefer <code>block.json</code>, explicit attributes and server rendering when the output depends on live WordPress data. Avoid creating a custom block for every heading and card.</p>
<h2>Protect structure with locking and permissions</h2>
<p>Editing freedom should match the client’s responsibility. WordPress supports block and template locking, reusable patterns and role capabilities. Locking can prevent accidental removal of required structure while still allowing text and image replacement.</p>
<ul><li>Authors may edit article content but not site settings.</li><li>Editors may manage projects and pages.</li><li>Administrators control integrations, credentials and global configuration.</li><li>Templates protect required wrappers and system output.</li></ul>
<h2>Make images easy and safe to manage</h2>
<p>Use the Media Library, meaningful image sizes and clear guidance for aspect ratio and alternative text. Do not make clients paste raw image URLs into content unless the image is intentionally remote. Generate responsive image markup through WordPress functions instead of hard-coded HTML.</p>
<h2>Give the admin interface business language</h2>
<p>“Hero eyebrow” may make sense to a designer but not to every client. Labels should explain purpose: “Short page label”, “Primary button text”, “Event venue” or “Public contact email”. Add help text that explains where the value appears and what format is expected.</p>
<h2>Plan for portability and handoff</h2>
<p>Business records and core workflows should remain available if the visual theme changes. That is why content models and integrations belong in a plugin while templates and styling belong in the theme. A clean handoff should include:</p>
<ul><li>A one-page editor guide with screenshots</li><li>Defined user roles and responsibilities</li><li>Backup and recovery instructions</li><li>Update and testing workflow</li><li>List of custom post types, fields and patterns</li><li>Known limitations and escalation path</li></ul>
<h2>When Elementor is still a reasonable choice</h2>
<p>Elementor can be appropriate for a marketing team that genuinely needs broad visual composition control and accepts the dependency. The decision should consider editor skill, performance budget, portability, maintenance responsibility and the expected life of the site. "No page builder" is not automatically senior engineering; choosing the simplest maintainable system for the client is.</p>
<h2>A practical acceptance test</h2>
<p>Before handoff, ask a non-technical person to update a heading, replace an image, add a project, change a CTA and recover a revision without developer help. If the workflow is confusing, the architecture is not finished.</p>
<h2>The standard I use</h2>
<p>A client-editable WordPress site should protect the system without trapping the editor. Normal content should feel native. Repeated content should feel structured. Complex behavior should feel purposeful. The architecture is successful when the client can handle routine publishing and the developer can change the system without untangling page-builder markup.</p>
HTML,
			),
			array(
				'title'    => 'How I Evaluate WordPress Tools Before Recommending Them',
				'slug'     => 'evaluate-wordpress-tools-affiliate-recommendations',
				'category' => 'Tools & Workflow',
				'excerpt'  => 'A transparent review framework for security, portability, support, performance and affiliate disclosure before a WordPress tool earns a recommendation.',
				'content'  => <<<'HTML'
<p>A useful tools page can help readers and create affiliate income, but only when recommendation quality comes first. I do not want a commission to turn a useful resource into a list of "best" products with no testing criteria. This is the framework I use before adding or promoting a WordPress product.</p>
<h2>Define the job the tool must do</h2>
<p>Start with the problem: reliable backups, email delivery, caching, local development or managed hosting. A product is not good in isolation; it is suitable or unsuitable for a specific environment, team and risk level.</p>
<h2>Review data ownership and portability</h2>
<ul><li>Can data be exported in a usable format?</li><li>What happens when the subscription ends?</li><li>Does uninstalling remove or damage business data?</li><li>Is the workflow tied to a proprietary builder or cloud?</li></ul>
<h2>Inspect security and permissions</h2>
<p>For plugins, review update history, disclosed vulnerabilities, capabilities, external requests and how credentials are stored. For SaaS integrations, understand which site or customer data leaves WordPress and where it is processed.</p>
<h2>Test performance in context</h2>
<p>A plugin can be lightweight on an empty demo and expensive on a large WooCommerce site. Measure database queries, background tasks, frontend assets and admin behavior using realistic data.</p>
<h2>Evaluate support and recovery</h2>
<p>Documentation and responsive support matter most when an update fails. Check whether the vendor publishes changelogs, rollback guidance, compatibility details and a clear route for urgent issues.</p>
<h2>Compare total cost, not only the first price</h2>
<p>Include renewal pricing, site limits, required add-ons, migration time, team training and the cost of lock-in. The cheapest license can be expensive if it creates operational dependence.</p>
<h2>Disclose affiliate relationships clearly</h2>
<p>When a link can earn a commission, the disclosure should appear near the recommendation, not hidden on a distant legal page. Affiliate links should use an appropriate sponsored relationship, and the recommendation should include a normal product URL when practical.</p>
<h2>Use a repeatable scorecard</h2>
<table><thead><tr><th>Area</th><th>Questions</th></tr></thead><tbody><tr><td>Fit</td><td>Does it solve the defined problem without unnecessary scope?</td></tr><tr><td>Security</td><td>Are permissions, updates and data handling acceptable?</td></tr><tr><td>Performance</td><td>What does it add to frontend, admin, database and cron?</td></tr><tr><td>Portability</td><td>Can the site or data move away?</td></tr><tr><td>Operations</td><td>Are support, logs, rollback and documentation strong?</td></tr><tr><td>Cost</td><td>What is the multi-year and switching cost?</td></tr></tbody></table>
<h2>How recommendations appear on this site</h2>
<p>The Recommended Tools page stores a non-affiliate URL and an optional approved affiliate URL separately. When an affiliate URL exists, the site adds a visible disclosure and the appropriate link relationship. No fake partner link is inserted by the starter importer.</p>
<h2>The trust test</h2>
<p>Would I still recommend the tool if the affiliate program ended tomorrow? If the answer is no, it should not be presented as an engineering recommendation.</p>
HTML,
			),
		);

		$article_dates = array(
			'2026-07-18 09:15:00',
			'2026-07-14 11:40:00',
			'2026-07-10 08:25:00',
			'2026-07-06 14:10:00',
			'2026-07-02 10:30:00',
			'2026-06-28 16:20:00',
			'2026-06-23 09:50:00',
		);

		foreach ( $posts as $post_index => $post ) {
			$category_id = isset( $categories[ $post['category'] ] ) ? $categories[ $post['category'] ] : 0;
			$post_date   = isset( $article_dates[ $post_index ] ) ? $article_dates[ $post_index ] : gmdate( 'Y-m-d H:i:s', time() - ( $post_index + 1 ) * DAY_IN_SECONDS );
			$count += self::upsert(
				'post',
				$post['title'],
				$post['content'],
				array(),
				array(
					'post_name'     => $post['slug'],
					'post_excerpt'  => $post['excerpt'],
					'post_category' => $category_id ? array( $category_id ) : array(),
					'comment_status'=> 'closed',
					'post_date'     => $post_date,
					'post_date_gmt' => get_gmt_from_date( $post_date ),
				)
			);
		}
		return $count;
	}

	/** Seed an honest, affiliate-ready tools catalogue without fake partner URLs. */
	private static function seed_tools() {
		$count = 0;
		$tools = array(
			array( 'WP-CLI', 'Developer Workflow', 'Command-line administration and automation for WordPress.', 'Useful for repeatable maintenance, database operations, scripted checks and production support.', 'Developers and support engineers', 'https://wp-cli.org/' ),
			array( 'Query Monitor', 'Debugging', 'A developer toolbar for inspecting database queries, hooks, HTTP requests and PHP errors.', 'It shortens the path from a visible symptom to the WordPress layer responsible for it.', 'Development and staging diagnosis', 'https://wordpress.org/plugins/query-monitor/' ),
			array( 'Local', 'Local Development', 'A desktop environment for creating and managing local WordPress installations.', 'A convenient option for isolated reproduction, plugin testing and client-site copies before production changes.', 'Local WordPress development', 'https://localwp.com/' ),
			array( 'GitHub', 'Source Control', 'Git hosting, pull requests, issues and automated workflows.', 'A public repository keeps architecture, commit discipline, tests and review history visible to collaborators and future maintainers.', 'Version control and CI', 'https://github.com/' ),
			array( 'Cloudflare', 'DNS & Edge', 'DNS, CDN and security services that can sit in front of a WordPress origin.', 'Useful when configured carefully for DNS reliability, caching and edge protection without hiding origin problems.', 'Production DNS and edge delivery', 'https://www.cloudflare.com/' ),
			array( 'FluentSMTP', 'Email Delivery', 'A WordPress SMTP and transactional email connection plugin.', 'It provides a practical route for connecting WordPress mail to a real delivery provider and reviewing delivery logs.', 'Contact-form and transactional email', 'https://wordpress.org/plugins/fluent-smtp/' ),
			array( 'UpdraftPlus', 'Backup & Recovery', 'A WordPress backup and restore plugin with remote-storage options.', 'It can provide an accessible backup workflow, but restore testing and off-site storage still need an operational plan.', 'Site backups and restore workflows', 'https://updraftplus.com/' ),
			array( 'WP Rocket', 'Performance', 'A commercial WordPress caching and performance plugin.', 'It can reduce implementation time for common caching and asset optimizations when its behavior is tested against the site stack.', 'Sites needing a managed performance layer', 'https://wp-rocket.me/' ),
		);

		foreach ( $tools as $index => $tool ) {
			$count += self::upsert(
				'sp_tool',
				$tool[0],
				'<p>' . esc_html( $tool[2] ) . '</p>',
				array(
					'category_label' => $tool[1],
					'description'    => $tool[2],
					'why'            => $tool[3],
					'best_for'       => $tool[4],
					'affiliate_url'  => '',
					'plain_url'      => $tool[5],
					'disclose'       => 1,
					'cta_label'      => 'Visit official website',
					'featured'       => $index < 4 ? 1 : 0,
				),
				array( 'menu_order' => $index + 1 )
			);
			$id = self::existing_id( 'sp_tool', $tool[0] );
			if ( $id ) {
				wp_set_object_terms( $id, $tool[1], 'sp_tool_category', false );
			}
		}
		return $count;
	}


	/** Build a concise primary menu. */
	private static function seed_menu() {
		$menu_name = 'Primary';
		$menu      = wp_get_nav_menu_object( $menu_name );
		$menu_id   = $menu ? (int) $menu->term_id : wp_create_nav_menu( $menu_name );
		if ( is_wp_error( $menu_id ) || ! $menu_id ) {
			return;
		}
		foreach ( wp_get_nav_menu_items( $menu_id ) ?: array() as $item ) {
			wp_delete_post( $item->ID, true );
		}
		$add = static function ( $label, $url, $parent = 0 ) use ( $menu_id ) {
			$item = wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'     => $label,
					'menu-item-url'       => $url,
					'menu-item-parent-id' => $parent,
					'menu-item-status'    => 'publish',
					'menu-item-type'      => 'custom',
				)
			);
			return is_wp_error( $item ) ? 0 : (int) $item;
		};

		$add( 'Home', home_url( '/' ) );
		$add( 'Engineering', home_url( '/engineering/' ) );
		$work = $add( 'Work', get_post_type_archive_link( 'sp_case_study' ) ?: home_url( '/case-studies/' ) );
		$add( 'Case Studies', get_post_type_archive_link( 'sp_case_study' ) ?: home_url( '/case-studies/' ), $work );
		$add( 'Projects', get_post_type_archive_link( 'sp_project' ) ?: home_url( '/projects/' ), $work );
		$add( 'GitHub', spcore_get( 'github_url', 'https://github.com/iamsaddamhossen' ), $work );
		$add( 'Experience', home_url( '/experience/' ) );
		$resources = $add( 'Resources', home_url( '/blog/' ) );
		$add( 'Writing', home_url( '/blog/' ), $resources );
		$add( 'Recommended Tools', home_url( '/recommended-tools/' ), $resources );
		$add( 'Contact', home_url( '/contact/' ) );

		$locations            = get_theme_mod( 'nav_menu_locations' );
		$locations            = is_array( $locations ) ? $locations : array();
		$locations['primary'] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}

	/** Configure the static front page and posts index. */
	private static function setup_reading() {
		$home = get_page_by_path( 'home' );
		$blog = get_page_by_path( 'blog' );
		if ( $home && $blog ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $home->ID );
			update_option( 'page_for_posts', $blog->ID );
		}
	}
}

SPCore_Seed::register();
