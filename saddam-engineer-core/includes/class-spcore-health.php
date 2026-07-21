<?php
/**
 * Site Health tests and debug information.
 *
 * @package SaddamEngineerCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class SPCore_Health {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_filter( 'site_status_tests', array( $this, 'tests' ) );
		add_filter( 'debug_information', array( $this, 'debug_information' ) );
	}

	/**
	 * Add direct checks.
	 *
	 * @param array<string, mixed> $tests Site Health tests.
	 * @return array<string, mixed>
	 */
	public function tests( $tests ) {
		$tests['direct']['spcore_https'] = array(
			'label' => __( 'Site uses HTTPS', 'spcore' ),
			'test'  => array( $this, 'test_https' ),
		);
		$tests['direct']['spcore_permalinks'] = array(
			'label' => __( 'Site permalinks are configured', 'spcore' ),
			'test'  => array( $this, 'test_permalinks' ),
		);
		$tests['direct']['spcore_theme'] = array(
			'label' => __( 'Companion engineering theme is active', 'spcore' ),
			'test'  => array( $this, 'test_theme' ),
		);
		$tests['direct']['spcore_cron'] = array(
			'label' => __( 'Site maintenance is scheduled', 'spcore' ),
			'test'  => array( $this, 'test_cron' ),
		);
		return $tests;
	}

	/**
	 * HTTPS check.
	 *
	 * @return array<string, mixed>
	 */
	public function test_https() {
		$good = is_ssl() || 'https' === wp_parse_url( home_url(), PHP_URL_SCHEME );
		return $this->result(
			$good,
			__( 'The public site uses HTTPS.', 'spcore' ),
			__( 'The public site is not configured to use HTTPS.', 'spcore' ),
			__( 'HTTPS protects visitor data in transit.', 'spcore' ),
			'spcore_https'
		);
	}

	/**
	 * Permalink check.
	 *
	 * @return array<string, mixed>
	 */
	public function test_permalinks() {
		$good = '' !== (string) get_option( 'permalink_structure' );
		return $this->result(
			$good,
			__( 'Pretty permalinks are enabled.', 'spcore' ),
			__( 'Pretty permalinks are disabled.', 'spcore' ),
			__( 'Readable permalinks are required for content archives and REST discovery.', 'spcore' ),
			'spcore_permalinks'
		);
	}

	/**
	 * Theme compatibility check.
	 *
	 * @return array<string, mixed>
	 */
	public function test_theme() {
		$theme = wp_get_theme();
		$good  = 'saddam-engineer-portfolio' === $theme->get_stylesheet();
		return $this->result(
			$good,
			__( 'Saddam Engineer theme is active.', 'spcore' ),
			__( 'The companion engineering theme is not active.', 'spcore' ),
			__( 'The plugin works independently, while the companion theme provides the complete public experience.', 'spcore' ),
			'spcore_theme'
		);
	}

	/**
	 * Scheduled event check.
	 *
	 * @return array<string, mixed>
	 */
	public function test_cron() {
		$good = (bool) wp_next_scheduled( 'spcore_daily_maintenance' );
		return $this->result(
			$good,
			__( 'Daily cache and retention maintenance is scheduled.', 'spcore' ),
			__( 'Daily site maintenance is not scheduled.', 'spcore' ),
			__( 'Deactivate and reactivate the plugin to recreate the scheduled event.', 'spcore' ),
			'spcore_cron'
		);
	}

	/**
	 * Add a debug-information section.
	 *
	 * @param array<string, mixed> $info Debug info.
	 * @return array<string, mixed>
	 */
	public function debug_information( $info ) {
		$stats = SPCore_Feature_Registry::stats();
		$info['spcore'] = array(
			'label'       => __( 'Saddam Engineer Core', 'spcore' ),
			'description' => __( 'Operational details for the WordPress engineering platform.', 'spcore' ),
			'fields'      => array(
				'version' => array(
					'label' => __( 'Plugin version', 'spcore' ),
					'value' => SPCORE_VERSION,
				),
				'features' => array(
					'label' => __( 'Implemented engineering features', 'spcore' ),
					'value' => (string) $stats['feature_count'],
				),
				'rest' => array(
					'label' => __( 'REST namespace', 'spcore' ),
					'value' => 'spcore/v1',
				),
				'cron' => array(
					'label' => __( 'Daily maintenance', 'spcore' ),
					'value' => wp_next_scheduled( 'spcore_daily_maintenance' ) ? __( 'Scheduled', 'spcore' ) : __( 'Missing', 'spcore' ),
				),
			),
		);
		return $info;
	}

	/**
	 * Build a standard Site Health result.
	 *
	 * @param bool   $good         Whether the test passes.
	 * @param string $good_label   Passed label.
	 * @param string $bad_label    Failed label.
	 * @param string $description  Description.
	 * @param string $test         Test ID.
	 * @return array<string, mixed>
	 */
	private function result( $good, $good_label, $bad_label, $description, $test ) {
		return array(
			'label'       => $good ? $good_label : $bad_label,
			'status'      => $good ? 'good' : 'recommended',
			'badge'       => array(
				'label' => __( 'WordPress Engineering Site', 'spcore' ),
				'color' => 'blue',
			),
			'description' => '<p>' . esc_html( $description ) . '</p>',
			'actions'     => '',
			'test'        => $test,
		);
	}
}
