<?php
/**
 * WP-CLI commands for operational visibility.
 *
 * @package SaddamEngineerCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class SPCore_CLI {

	/**
	 * Register commands only when WP-CLI is active.
	 *
	 * @return void
	 */
	public static function register() {
		if ( defined( 'WP_CLI' ) && WP_CLI && class_exists( 'WP_CLI' ) ) {
			WP_CLI::add_command( 'spcore', __CLASS__ );
		}
	}

	/**
	 * Display WordPress platform status.
	 *
	 * ## EXAMPLES
	 *
	 *     wp spcore status
	 *
	 * @return void
	 */
	public function status() {
		$stats = SPCore_Feature_Registry::stats();
		$rows  = array(
			array( 'Property' => 'Plugin version', 'Value' => $stats['plugin_version'] ),
			array( 'Property' => 'WordPress', 'Value' => $stats['wordpress_version'] ),
			array( 'Property' => 'PHP', 'Value' => $stats['php_version'] ),
			array( 'Property' => 'Engineering features', 'Value' => (string) $stats['feature_count'] ),
			array( 'Property' => 'REST namespace', 'Value' => $stats['rest_namespace'] ),
			array( 'Property' => 'Daily maintenance', 'Value' => wp_next_scheduled( 'spcore_daily_maintenance' ) ? 'scheduled' : 'missing' ),
		);

		WP_CLI\Utils\format_items( 'table', $rows, array( 'Property', 'Value' ) );
	}

	/**
	 * Manage cached engineering data.
	 *
	 * ## OPTIONS
	 *
	 * <action>
	 * : clear or warm
	 *
	 * ## EXAMPLES
	 *
	 *     wp spcore cache clear
	 *     wp spcore cache warm
	 *
	 * @param array<int, string> $args Positional arguments.
	 * @return void
	 */
	public function cache( $args ) {
		$action = isset( $args[0] ) ? sanitize_key( $args[0] ) : '';
		$cache  = SPCore_Cache::instance();

		if ( 'clear' === $action ) {
			$cache->invalidate();
			WP_CLI::success( 'Site caches cleared.' );
			return;
		}

		if ( 'warm' === $action ) {
			$cache->invalidate();
			SPCore_Feature_Registry::stats();
			WP_CLI::success( 'Engineering stats cache warmed.' );
			return;
		}

		WP_CLI::error( 'Use `wp spcore cache clear` or `wp spcore cache warm`.' );
	}
}
