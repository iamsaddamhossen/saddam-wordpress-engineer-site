<?php
/**
 * Cache invalidation and scheduled maintenance.
 *
 * @package SaddamEngineerCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class SPCore_Cache {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'save_post', array( $this, 'maybe_invalidate' ), 20, 2 );
		add_action( 'deleted_post', array( $this, 'invalidate' ) );
		add_action( 'created_term', array( $this, 'invalidate' ) );
		add_action( 'edited_term', array( $this, 'invalidate' ) );
		add_action( 'delete_term', array( $this, 'invalidate' ) );
		add_action( 'spcore_daily_maintenance', array( $this, 'daily_maintenance' ) );
	}

	/**
	 * Invalidate only when a managed content object changes.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 * @return void
	 */
	public function maybe_invalidate( $post_id, $post ) {
		if ( wp_is_post_revision( $post_id ) || ! $post instanceof WP_Post ) {
			return;
		}

		if ( 0 === strpos( $post->post_type, 'sp_' ) ) {
			$this->invalidate();
		}
	}

	/**
	 * Clear plugin transients.
	 *
	 * @return void
	 */
	public function invalidate() {
		delete_transient( SPCORE_CACHE_KEY );
		$version = max( 1, absint( get_option( SPCORE_CACHE_VERSION_OPTION, 1 ) ) );
		update_option( SPCORE_CACHE_VERSION_OPTION, $version + 1, false );
	}

	/**
	 * Rebuild stats and apply retention rules.
	 *
	 * @return void
	 */
	public function daily_maintenance() {
		$this->invalidate();
		SPCore_Feature_Registry::stats();
		$this->prune_old_leads();
	}

	/**
	 * Delete lead records older than the configured retention window.
	 *
	 * @return int Number of table rows removed.
	 */
	public function prune_old_leads() {
		global $wpdb;

		$days = absint( spcore_get( 'lead_retention_days', 365 ) );
		if ( $days < 1 ) {
			return 0;
		}

		$before = gmdate( 'Y-m-d H:i:s', time() - ( $days * DAY_IN_SECONDS ) );
		$table  = SPCore_Leads::table_name();
		$rows   = 0;

		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) === $table ) {
			$rows = (int) $wpdb->query(
				$wpdb->prepare( "DELETE FROM {$table} WHERE created_at < %s", $before ) // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			);
		}

		$batch = 0;
		do {
			$old_posts = get_posts(
				array(
					'post_type'      => 'sp_lead',
					'post_status'    => 'any',
					'posts_per_page' => 250,
					'date_query'     => array(
						array(
							'before'    => $before,
							'inclusive' => false,
						),
					),
					'fields'         => 'ids',
					'no_found_rows'  => true,
				)
			);

			foreach ( $old_posts as $post_id ) {
				wp_delete_post( $post_id, true );
			}
			++$batch;
		} while ( 250 === count( $old_posts ) && $batch < 20 );

		return $rows;
	}
}
