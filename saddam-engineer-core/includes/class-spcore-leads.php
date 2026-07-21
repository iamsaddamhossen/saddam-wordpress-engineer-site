<?php
/**
 * Lead storage: custom table + admin meta display on the sp_lead CPT.
 *
 * @package SaddamPortfolioCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPCore_Leads {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'lead_metabox' ) );
		add_filter( 'manage_sp_lead_posts_columns', array( $this, 'columns' ) );
		add_action( 'manage_sp_lead_posts_custom_column', array( $this, 'column_content' ), 10, 2 );
		add_action( 'save_post_sp_lead', array( $this, 'save_status' ) );
	}

	public static function table_name() {
		global $wpdb;
		return $wpdb->prefix . SPCORE_LEAD_TABLE;
	}

	/**
	 * Create the leads table (used as a queryable/exportable mirror).
	 */
	public static function create_table() {
		global $wpdb;
		$table   = self::table_name();
		$charset = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			post_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
			created_at DATETIME NOT NULL,
			name VARCHAR(191) NOT NULL DEFAULT '',
			email VARCHAR(191) NOT NULL DEFAULT '',
			company VARCHAR(191) NOT NULL DEFAULT '',
			website VARCHAR(255) NOT NULL DEFAULT '',
			inquiry_type VARCHAR(100) NOT NULL DEFAULT '',
			budget VARCHAR(100) NOT NULL DEFAULT '',
			message LONGTEXT NULL,
			source_page VARCHAR(255) NOT NULL DEFAULT '',
			utm_source VARCHAR(191) NOT NULL DEFAULT '',
			utm_medium VARCHAR(191) NOT NULL DEFAULT '',
			utm_campaign VARCHAR(191) NOT NULL DEFAULT '',
			referrer VARCHAR(255) NOT NULL DEFAULT '',
			ip VARCHAR(64) NOT NULL DEFAULT '',
			status VARCHAR(40) NOT NULL DEFAULT 'new',
			PRIMARY KEY  (id),
			KEY post_id (post_id),
			KEY email (email),
			KEY status (status),
			KEY created_at (created_at)
		) {$charset};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Store a lead in both the CPT (admin friendly) and the mirror table.
	 *
	 * @param array $data Sanitized lead data.
	 * @return int Post ID.
	 */
	public static function store( $data ) {
		global $wpdb;

		$title   = sprintf(
			'%s ,  %s',
			$data['name'] ? $data['name'] : __( 'Anonymous', 'spcore' ),
			$data['inquiry_type'] ? $data['inquiry_type'] : __( 'Inquiry', 'spcore' )
		);

		$post_id = wp_insert_post(
			array(
				'post_type'   => 'sp_lead',
				'post_title'  => wp_strip_all_tags( $title ),
				'post_status' => 'publish',
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			return 0;
		}

		foreach ( $data as $k => $v ) {
			update_post_meta( $post_id, '_lead_' . $k, $v );
		}
		update_post_meta( $post_id, '_lead_status', 'new' );

		// Mirror table (best-effort; never fatal).
		$table = self::table_name();
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) === $table ) {
			$wpdb->insert(
				$table,
				array(
					'post_id'      => $post_id,
					'created_at'   => current_time( 'mysql' ),
					'name'         => $data['name'],
					'email'        => $data['email'],
					'company'      => $data['company'],
					'website'      => $data['website'],
					'inquiry_type' => $data['inquiry_type'],
					'budget'       => $data['budget'],
					'message'      => $data['message'],
					'source_page'  => $data['source_page'],
					'utm_source'   => $data['utm_source'],
					'utm_medium'   => $data['utm_medium'],
					'utm_campaign' => $data['utm_campaign'],
					'referrer'     => $data['referrer'],
					'ip'           => $data['ip'],
					'status'       => 'new',
				)
			);
		}

		return $post_id;
	}

	public function lead_metabox() {
		add_meta_box( 'spcore_lead_detail', __( 'Lead Details', 'spcore' ), array( $this, 'render' ), 'sp_lead', 'normal', 'high' );
		add_meta_box( 'spcore_lead_status', __( 'Status', 'spcore' ), array( $this, 'render_status' ), 'sp_lead', 'side', 'high' );
	}

	public function render( $post ) {
		$fields = array(
			'name'         => 'Name',
			'email'        => 'Email',
			'company'      => 'Company',
			'website'      => 'Website',
			'inquiry_type' => 'Inquiry Type',
			'budget'       => 'Budget',
			'message'      => 'Message',
			'source_page'  => 'Source Page',
			'utm_source'   => 'UTM Source',
			'utm_medium'   => 'UTM Medium',
			'utm_campaign' => 'UTM Campaign',
			'referrer'     => 'Referrer',
			'ip'           => 'IP',
		);
		echo '<table class="widefat striped">';
		foreach ( $fields as $key => $label ) {
			$val = get_post_meta( $post->ID, '_lead_' . $key, true );
			printf(
				'<tr><th style="width:160px">%s</th><td>%s</td></tr>',
				esc_html( $label ),
				'email' === $key && $val ? '<a href="mailto:' . esc_attr( $val ) . '">' . esc_html( $val ) . '</a>' : esc_html( $val )
			);
		}
		echo '</table>';
	}

	public function render_status( $post ) {
		wp_nonce_field( 'spcore_lead_status', 'spcore_lead_status_nonce' );
		$status   = get_post_meta( $post->ID, '_lead_status', true );
		$statuses = array( 'new' => 'New', 'contacted' => 'Contacted', 'qualified' => 'Qualified', 'won' => 'Won', 'lost' => 'Lost', 'spam' => 'Spam' );
		echo '<select name="spcore_lead_status" style="width:100%">';
		foreach ( $statuses as $k => $v ) {
			printf( '<option value="%s" %s>%s</option>', esc_attr( $k ), selected( $status, $k, false ), esc_html( $v ) );
		}
		echo '</select>';
		$notes = get_post_meta( $post->ID, '_lead_notes', true );
		echo '<p><label style="font-weight:600">Notes</label>';
		printf( '<textarea name="spcore_lead_notes" style="width:100%%;min-height:90px">%s</textarea></p>', esc_textarea( $notes ) );
	}

	public function save_status( $post_id ) {
		if ( ! isset( $_POST['spcore_lead_status_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['spcore_lead_status_nonce'] ) ), 'spcore_lead_status' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['spcore_lead_status'] ) ) {
			$allowed = array( 'new', 'contacted', 'qualified', 'won', 'lost', 'spam' );
			$status  = sanitize_key( wp_unslash( $_POST['spcore_lead_status'] ) );
			if ( in_array( $status, $allowed, true ) ) {
				update_post_meta( $post_id, '_lead_status', $status );
				global $wpdb;
				$table = self::table_name();
				if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) === $table ) {
					$wpdb->update( $table, array( 'status' => $status ), array( 'post_id' => $post_id ), array( '%s' ), array( '%d' ) );
				}
			}
		}
		if ( isset( $_POST['spcore_lead_notes'] ) ) {
			update_post_meta( $post_id, '_lead_notes', sanitize_textarea_field( wp_unslash( $_POST['spcore_lead_notes'] ) ) );
		}
	}

	public function columns( $cols ) {
		$new = array(
			'cb'          => isset( $cols['cb'] ) ? $cols['cb'] : '',
			'title'       => __( 'Lead', 'spcore' ),
			'lead_email'  => __( 'Email', 'spcore' ),
			'lead_type'   => __( 'Inquiry', 'spcore' ),
			'lead_budget' => __( 'Budget', 'spcore' ),
			'lead_status' => __( 'Status', 'spcore' ),
			'date'        => __( 'Date', 'spcore' ),
		);
		return $new;
	}

	public function column_content( $col, $post_id ) {
		switch ( $col ) {
			case 'lead_email':
				echo esc_html( get_post_meta( $post_id, '_lead_email', true ) );
				break;
			case 'lead_type':
				echo esc_html( get_post_meta( $post_id, '_lead_inquiry_type', true ) );
				break;
			case 'lead_budget':
				echo esc_html( get_post_meta( $post_id, '_lead_budget', true ) );
				break;
			case 'lead_status':
				echo '<strong>' . esc_html( ucfirst( get_post_meta( $post_id, '_lead_status', true ) ) ) . '</strong>';
				break;
		}
	}
}
