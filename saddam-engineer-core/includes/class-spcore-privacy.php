<?php
/**
 * Privacy exporter, eraser and suggested policy text for stored leads.
 *
 * @package SaddamEngineerCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class SPCore_Privacy {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_exporter' ) );
		add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_eraser' ) );
		add_action( 'admin_init', array( $this, 'privacy_policy_content' ) );
	}

	/**
	 * Register the lead exporter.
	 *
	 * @param array<string, array<string, mixed>> $exporters Exporters.
	 * @return array<string, array<string, mixed>>
	 */
	public function register_exporter( $exporters ) {
		$exporters['spcore-leads'] = array(
			'exporter_friendly_name' => __( 'Website contact leads', 'spcore' ),
			'callback'               => array( $this, 'export' ),
		);
		return $exporters;
	}

	/**
	 * Register the lead eraser.
	 *
	 * @param array<string, array<string, mixed>> $erasers Erasers.
	 * @return array<string, array<string, mixed>>
	 */
	public function register_eraser( $erasers ) {
		$erasers['spcore-leads'] = array(
			'eraser_friendly_name' => __( 'Website contact leads', 'spcore' ),
			'callback'             => array( $this, 'erase' ),
		);
		return $erasers;
	}

	/**
	 * Export contact data for one email address.
	 *
	 * @param string $email_address Email address.
	 * @param int    $page          Page number.
	 * @return array<string, mixed>
	 */
	public function export( $email_address, $page = 1 ) {
		global $wpdb;

		$page   = max( 1, (int) $page );
		$number = 100;
		$offset = ( $page - 1 ) * $number;
		$table  = SPCore_Leads::table_name();
		$items  = array();
		$rows   = array();

		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) === $table ) {
			$rows = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM {$table} WHERE email = %s ORDER BY id ASC LIMIT %d OFFSET %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					$email_address,
					$number,
					$offset
				),
				ARRAY_A
			);
		}

		foreach ( $rows as $row ) {
			$data = array();
			foreach ( $this->exportable_fields() as $key => $label ) {
				if ( isset( $row[ $key ] ) && '' !== (string) $row[ $key ] ) {
					$data[] = array(
						'name'  => $label,
						'value' => (string) $row[ $key ],
					);
				}
			}

			$items[] = array(
				'group_id'    => 'spcore-leads',
				'group_label' => __( 'Website contact submissions', 'spcore' ),
				'item_id'     => 'lead-' . absint( $row['id'] ),
				'data'        => $data,
			);
		}

		return array(
			'data' => $items,
			'done' => count( $rows ) < $number,
		);
	}

	/**
	 * Anonymize lead rows and corresponding private posts.
	 *
	 * @param string $email_address Email address.
	 * @param int    $page          Page number.
	 * @return array<string, mixed>
	 */
	public function erase( $email_address, $page = 1 ) {
		global $wpdb;

		$table         = SPCore_Leads::table_name();
		$items_removed = false;
		$messages      = array();
		$anonymous     = wp_privacy_anonymize_data( 'email' );

		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) === $table ) {
			$updated = $wpdb->update(
				$table,
				array(
					'name'         => __( 'Anonymous', 'spcore' ),
					'email'        => $anonymous,
					'company'      => '',
					'website'      => '',
					'message'      => __( 'Personal data erased at the requester’s direction.', 'spcore' ),
					'utm_source'   => '',
					'utm_medium'   => '',
					'utm_campaign' => '',
					'referrer'     => '',
					'ip'           => '',
				),
				array( 'email' => $email_address ),
				array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ),
				array( '%s' )
			);
			$items_removed = false !== $updated && $updated > 0;
		}

		$lead_posts = get_posts(
			array(
				'post_type'      => 'sp_lead',
				'post_status'    => 'any',
				'posts_per_page' => 100,
				'meta_key'       => '_lead_email',
				'meta_value'     => $email_address,
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);

		foreach ( $lead_posts as $post_id ) {
			wp_update_post(
				array(
					'ID'         => $post_id,
					'post_title' => __( 'Anonymous ,  privacy request', 'spcore' ),
				)
			);
			update_post_meta( $post_id, '_lead_name', __( 'Anonymous', 'spcore' ) );
			update_post_meta( $post_id, '_lead_email', $anonymous );
			update_post_meta( $post_id, '_lead_company', '' );
			update_post_meta( $post_id, '_lead_website', '' );
			update_post_meta( $post_id, '_lead_message', __( 'Personal data erased at the requester’s direction.', 'spcore' ) );
			update_post_meta( $post_id, '_lead_referrer', '' );
			update_post_meta( $post_id, '_lead_ip', '' );
			$items_removed = true;
		}

		if ( ! $items_removed ) {
			$messages[] = __( 'No matching website contact data was found.', 'spcore' );
		}

		return array(
			'items_removed'  => $items_removed,
			'items_retained' => false,
			'messages'       => $messages,
			'done'           => true,
		);
	}

	/**
	 * Add suggested policy copy to the WordPress privacy guide.
	 *
	 * @return void
	 */
	public function privacy_policy_content() {
		if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
			return;
		}

		$content = '<p>' . esc_html__( 'When a visitor submits the website contact form, the site stores the submitted name, email address, optional company and website, inquiry details, source page and campaign parameters so the inquiry can be answered and tracked. IP storage is disabled by default. Data may be sent by email or to a configured webhook. WordPress privacy tools can export or erase stored lead data, and the configured retention period is applied by scheduled maintenance.', 'spcore' ) . '</p>';
		wp_add_privacy_policy_content( __( 'Saddam Engineer Core', 'spcore' ), wp_kses_post( wpautop( $content ) ) );
	}

	/**
	 * Human-readable field map.
	 *
	 * @return array<string, string>
	 */
	private function exportable_fields() {
		return array(
			'created_at'   => __( 'Submitted at', 'spcore' ),
			'name'         => __( 'Name', 'spcore' ),
			'email'        => __( 'Email', 'spcore' ),
			'company'      => __( 'Company', 'spcore' ),
			'website'      => __( 'Website', 'spcore' ),
			'inquiry_type' => __( 'Inquiry type', 'spcore' ),
			'budget'       => __( 'Budget or engagement', 'spcore' ),
			'message'      => __( 'Message', 'spcore' ),
			'source_page'  => __( 'Source page', 'spcore' ),
			'utm_source'   => __( 'UTM source', 'spcore' ),
			'utm_medium'   => __( 'UTM medium', 'spcore' ),
			'utm_campaign' => __( 'UTM campaign', 'spcore' ),
			'referrer'     => __( 'Referrer', 'spcore' ),
			'ip'           => __( 'IP address', 'spcore' ),
			'status'       => __( 'Lead status', 'spcore' ),
		);
	}
}
