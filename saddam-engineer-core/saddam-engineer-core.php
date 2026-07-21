<?php
/**
 * Plugin Name:       Saddam Engineer Core
 * Plugin URI:        https://saddamhossen.dev/
 * Description:       Companion plugin for the Saddam Engineer WordPress theme. Demonstrates production WordPress engineering with custom content models, REST API, a dynamic Gutenberg block, WP-CLI, privacy tools, Site Health, lead storage, schema, caching and automation hooks.
 * Version:           1.0.0
 * Author:            Saddam Hossen
 * Author URI:        https://saddamhossen.dev/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       spcore
 * Domain Path:       /languages
 * Requires PHP:      7.4
 * Requires at least: 6.7
 * Tested up to:      7.0.1
 *
 * @package SaddamEngineerCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SPCORE_VERSION', '1.0.0' );
define( 'SPCORE_SCHEMA_VERSION', '2026.07.20.2' );
define( 'SPCORE_FILE', __FILE__ );
define( 'SPCORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'SPCORE_URL', plugin_dir_url( __FILE__ ) );
define( 'SPCORE_LEAD_TABLE', 'spcore_leads' );
define( 'SPCORE_CACHE_KEY', 'spcore_engineering_stats_v1' );
define( 'SPCORE_CACHE_VERSION_OPTION', 'spcore_cache_version' );

/**
 * Load a plugin include file safely.
 *
 * @param string $relative Relative path inside the plugin folder.
 * @return void
 */
function spcore_require( $relative ) {
	$path = SPCORE_DIR . ltrim( $relative, '/' );
	if ( file_exists( $path ) ) {
		require_once $path;
	}
}

// Core modules.
spcore_require( 'includes/class-spcore-feature-registry.php' );
spcore_require( 'includes/class-spcore-cpt.php' );
spcore_require( 'includes/class-spcore-meta.php' );
spcore_require( 'includes/class-spcore-settings.php' );
spcore_require( 'includes/class-spcore-leads.php' );
spcore_require( 'includes/class-spcore-contact.php' );
spcore_require( 'includes/class-spcore-schema.php' );
spcore_require( 'includes/class-spcore-analytics.php' );
spcore_require( 'includes/class-spcore-shortcodes.php' );
spcore_require( 'includes/class-spcore-seo.php' );
spcore_require( 'includes/class-spcore-seed.php' );
spcore_require( 'includes/class-spcore-rest.php' );
spcore_require( 'includes/class-spcore-blocks.php' );
spcore_require( 'includes/class-spcore-cli.php' );
spcore_require( 'includes/class-spcore-privacy.php' );
spcore_require( 'includes/class-spcore-health.php' );
spcore_require( 'includes/class-spcore-cache.php' );
spcore_require( 'includes/class-spcore-assistant.php' );


/**
 * Apply non-destructive database and schedule upgrades after plugin updates.
 *
 * @return void
 */
function spcore_maybe_upgrade() {
	$installed_schema = (string) get_option( 'spcore_schema_version', '0' );
	if ( SPCORE_SCHEMA_VERSION === $installed_schema ) {
		return;
	}

	SPCore_Leads::create_table();
	if ( ! wp_next_scheduled( 'spcore_daily_maintenance' ) ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'spcore_daily_maintenance' );
	}

	$settings = get_option( 'spcore_settings', array() );
	$settings = is_array( $settings ) ? $settings : array();
	$defaults = spcore_default_settings();
	$settings = wp_parse_args( $settings, $defaults );

	// Initial public release: keep user values, but repair stale generated defaults.
	if ( empty( $settings['home_status'] ) ) {
		$settings['home_status'] = $defaults['home_status'];
	}
	if ( empty( $settings['home_hero_title'] ) || false !== stripos( (string) $settings['home_hero_title'], 'systems built to stay reliable' ) ) {
		$settings['home_hero_title'] = $defaults['home_hero_title'];
	}
	if ( empty( $settings['home_hero_intro'] ) ) {
		$settings['home_hero_intro'] = $defaults['home_hero_intro'];
	}
	if ( empty( $settings['home_primary_label'] ) ) {
		$settings['home_primary_label'] = $defaults['home_primary_label'];
	}
	if ( empty( $settings['home_secondary_label'] ) ) {
		$settings['home_secondary_label'] = $defaults['home_secondary_label'];
	}
	if ( empty( $settings['headline'] ) || false !== stripos( (string) $settings['headline'], 'Senior WordPress Engineer |' ) ) {
		$settings['headline'] = $defaults['headline'];
	}
	if ( empty( $settings['bio'] ) || false !== stripos( (string) $settings['bio'], 'selected engineering engagements' ) ) {
		$settings['bio'] = $defaults['bio'];
	}
	if ( empty( $settings['announce_text'] ) || false !== stripos( (string) $settings['announce_text'], 'selected technical collaborations' ) ) {
		$settings['announce_text'] = $defaults['announce_text'];
	}
	if ( empty( $settings['chat_welcome'] ) || false !== stripos( (string) $settings['chat_welcome'], 'portfolio' ) ) {
		$settings['chat_welcome'] = $defaults['chat_welcome'];
	}
	if ( empty( $settings['photo_url'] ) && ! empty( $defaults['photo_url'] ) ) {
		$settings['photo_url'] = $defaults['photo_url'];
	}
	$settings['mobile_bar_enable'] = 0;
	$settings['comments_disable']  = 1;

	update_option( 'spcore_settings', $settings );
	update_option( 'spcore_version', SPCORE_VERSION );
	update_option( 'spcore_schema_version', SPCORE_SCHEMA_VERSION );
}
add_action( 'plugins_loaded', 'spcore_maybe_upgrade', 5 );

/**
 * Boot the plugin after all plugins are available.
 *
 * @return void
 */
function spcore_boot() {
	load_plugin_textdomain( 'spcore', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	SPCore_CPT::instance();
	SPCore_Meta::instance();
	SPCore_Settings::instance();
	SPCore_Leads::instance();
	SPCore_Contact::instance();
	SPCore_Schema::instance();
	SPCore_Analytics::instance();
	SPCore_Shortcodes::instance();
	SPCore_SEO::instance();
	SPCore_REST::instance();
	SPCore_Blocks::instance();
	SPCore_Privacy::instance();
	SPCore_Health::instance();
	SPCore_Cache::instance();
	SPCore_Assistant::instance();
	SPCore_CLI::register();
}
add_action( 'plugins_loaded', 'spcore_boot' );

/**
 * Default settings used on activation and as option fallbacks.
 *
 * @return array<string, mixed>
 */
function spcore_default_settings() {
	$default_photo = '';
	if ( function_exists( 'get_theme_file_path' ) && function_exists( 'get_theme_file_uri' ) ) {
		$photo_path = get_theme_file_path( '/assets/images/saddam-hossen-author.webp' );
		if ( is_string( $photo_path ) && file_exists( $photo_path ) ) {
			$default_photo = get_theme_file_uri( '/assets/images/saddam-hossen-author.webp' );
		}
	}

	return array(
		// Profile.
		'name'                 => 'Saddam Hossen',
		'headline'             => 'Senior WordPress Engineer · Custom plugins, WooCommerce, Gutenberg, REST APIs and production debugging',
		'home_status'           => 'Available for remote WordPress engineering roles',
		'home_hero_title'       => 'I build reliable WordPress systems for production teams.',
		'home_hero_intro'       => 'Custom plugins, WooCommerce, Gutenberg, REST APIs, performance and production debugging.',
		'home_primary_label'    => 'Explore Engineering',
		'home_secondary_label'  => 'Read Case Studies',
		'bio'                  => 'Senior WordPress engineer with production experience across custom plugins, WooCommerce, multilingual systems, debugging, performance and technical support. Open to full-time remote roles and high-trust engineering work.',
		'location'             => 'Bangladesh',
		'timezone'             => 'Bangladesh time · Flexible overlap',
		'email'                => 'saddam.wp007@gmail.com',
		'phone'                => '+880 1920 066584',
		'whatsapp_number'      => '+8801920066584',
		'whatsapp_url'         => 'https://wa.me/8801920066584?text=Hi%20Saddam%2C%20I%20would%20like%20to%20discuss%20a%20WordPress%20engineering%20role.',
		'photo_url'            => $default_photo,
		'cv_url'               => '',
		// Trust and profiles.
		'years_wording'        => 'Building production WordPress solutions since 2017',
		'upwork_status'        => 'Top Rated on Upwork',
		'job_success'          => 'International client experience',
		'project_count'        => 'Production WordPress work',
		'plugin_count'         => 'Custom plugin engineering',
		'github_url'           => 'https://github.com/iamsaddamhossen',
		'upwork_url'           => '',
		'linkedin_url'         => '',
		'binarylab_url'        => 'https://99binarylab.com/',
		// SEO.
		'meta_title'           => 'Saddam Hossen | Senior WordPress Engineer',
		'meta_description'     => 'Senior WordPress engineer focused on custom plugins, WooCommerce, Gutenberg, REST APIs, performance, security and production debugging.',
		'social_image'         => '',
		'ga4_id'               => '',
		'gsc_verification'     => '',
		'bing_verification'    => '',
		// Email.
		'recipient_email'      => 'saddam.wp007@gmail.com',
		'from_name'            => 'Saddam Hossen',
		'from_email'           => '',
		'send_visitor'         => 1,
		'send_admin'           => 1,
		// Automation.
		'webhook_url'          => '',
		'webhook_secret'       => '',
		'send_to_webhook'      => 0,
		'ga4_events'           => 1,
		// Privacy and anti-spam.
		'store_ip'             => 0,
		'lead_retention_days'  => '365',
		'turnstile_site'       => '',
		'turnstile_secret'     => '',
		'recaptcha_site'       => '',
		'recaptcha_secret'     => '',
		// Resources.
		'affiliate_disclosure' => 'Some resource links may be affiliate links. Recommendations are based on practical WordPress engineering use, and a commission never changes the price you pay.',
		'enable_tools'         => 1,
		'affiliate_nofollow'   => 1,
		// Public contact and availability system.
		'announce_enable'      => 1,
		'announce_text'        => 'Open to full-time remote WordPress engineering roles and focused production collaborations.',
		'announce_cta_label'   => 'Explore Engineering',
		'announce_cta_url'     => '/engineering/',
		'modal_enable'         => 0,
		'modal_delay'          => '12000',
		'modal_scroll'         => '55',
		'modal_headline'       => 'Hiring a WordPress engineer?',
		'modal_body'           => 'Share the product context, responsibilities and technical priorities.',
		'modal_cta_label'      => 'Discuss a Role',
		'modal_cta_url'        => '/contact/',
		'modal_frequency_days' => '14',
		'mobile_bar_enable'    => 0,
		'chat_enable'          => 1,
		'chat_mode'            => 'guided',
		'chat_welcome'         => 'Hi, I am the site assistant for Saddam Hossen. I can help you explore his WordPress engineering work, technical writing and availability.',
		'chat_webhook_url'     => '',
		'chat_api_provider'    => 'openai',
		'chat_api_model'       => 'gpt-5.6',
		'chat_api_key'         => '',
		'chat_privacy_note'    => 'Guided messages are processed in your browser. If AI mode is enabled, the question is sent to the configured provider and is not stored by this website.',
		'comments_disable'     => 1,
	);
}

/**
 * Read one setting with a safe fallback.
 *
 * @param string $key     Setting key.
 * @param mixed  $default Fallback value.
 * @return mixed
 */
function spcore_get( $key, $default = '' ) {
	$settings = get_option( 'spcore_settings', array() );
	$defaults = spcore_default_settings();

	if ( isset( $settings[ $key ] ) && '' !== $settings[ $key ] ) {
		return $settings[ $key ];
	}

	if ( isset( $defaults[ $key ] ) ) {
		return $defaults[ $key ];
	}

	return $default;
}

/**
 * Activate: create storage, register content, schedule maintenance and flush.
 *
 * @return void
 */
function spcore_activate() {
	spcore_require( 'includes/class-spcore-cpt.php' );
	spcore_require( 'includes/class-spcore-leads.php' );
	spcore_require( 'includes/class-spcore-cache.php' );
	spcore_require( 'includes/class-spcore-assistant.php' );

	if ( class_exists( 'SPCore_CPT' ) ) {
		SPCore_CPT::instance()->register_all();
	}
	if ( class_exists( 'SPCore_Leads' ) ) {
		SPCore_Leads::create_table();
	}
	if ( false === get_option( 'spcore_settings' ) ) {
		add_option( 'spcore_settings', spcore_default_settings() );
	}
	if ( ! wp_next_scheduled( 'spcore_daily_maintenance' ) ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'spcore_daily_maintenance' );
	}

	update_option( 'spcore_version', SPCORE_VERSION );
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'spcore_activate' );

/**
 * Deactivate scheduled work and rewrite rules while preserving data.
 *
 * @return void
 */
function spcore_deactivate() {
	$timestamp = wp_next_scheduled( 'spcore_daily_maintenance' );
	if ( $timestamp ) {
		wp_unschedule_event( $timestamp, 'spcore_daily_maintenance' );
	}
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'spcore_deactivate' );
