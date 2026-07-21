<?php
/**
 * Versioned REST API endpoints for public engineering data.
 *
 * @package SaddamEngineerCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class SPCore_REST {

	const NAMESPACE = 'spcore/v1';

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register API routes with explicit permissions and argument schemas.
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			self::NAMESPACE,
			'/engineering',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'engineering' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/projects',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'projects' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'page'     => array(
						'default'           => 1,
						'sanitize_callback' => 'absint',
						'validate_callback' => static function ( $value ) {
							return absint( $value ) >= 1;
						},
					),
					'per_page' => array(
						'default'           => 6,
						'sanitize_callback' => 'absint',
						'validate_callback' => static function ( $value ) {
							$value = absint( $value );
							return $value >= 1 && $value <= 20;
						},
					),
					'featured' => array(
						'default'           => false,
						'sanitize_callback' => 'rest_sanitize_boolean',
						'validate_callback' => static function ( $value ) {
							return is_bool( $value ) || in_array( strtolower( (string) $value ), array( '1', '0', 'true', 'false' ), true );
						},
					),
					'search'   => array(
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/health',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'health' ),
				'permission_callback' => static function () {
					return current_user_can( 'manage_options' );
				},
			)
		);
	}

	/**
	 * Public implementation catalogue.
	 *
	 * @return WP_REST_Response
	 */
	public function engineering() {
		$response = rest_ensure_response(
			array(
				'name'     => spcore_get( 'name', 'Saddam Hossen' ),
				'headline' => spcore_get( 'headline' ),
				'features' => SPCore_Feature_Registry::all(),
				'stats'    => SPCore_Feature_Registry::stats(),
				'links'    => array(
					'site'   => home_url( '/' ),
					'github'    => spcore_get( 'github_url' ),
				),
			)
		);
		$response->header( 'Cache-Control', 'public, max-age=300' );
		return $response;
	}

	/**
	 * Public project collection with pagination headers.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function projects( WP_REST_Request $request ) {
		$page     = max( 1, (int) $request->get_param( 'page' ) );
		$per_page = min( 20, max( 1, (int) $request->get_param( 'per_page' ) ) );
		$featured = (bool) $request->get_param( 'featured' );
		$search   = (string) $request->get_param( 'search' );
		$cache_version = max( 1, absint( get_option( SPCORE_CACHE_VERSION_OPTION, 1 ) ) );
		$cache_id      = 'spcore_rest_projects_' . md5( wp_json_encode( array( $cache_version, $page, $per_page, $featured, $search ) ) );
		$payload  = get_transient( $cache_id );

		if ( ! is_array( $payload ) ) {
			$args = array(
				'post_type'           => 'sp_project',
				'post_status'         => 'publish',
				'paged'               => $page,
				'posts_per_page'      => $per_page,
				'orderby'             => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
				'ignore_sticky_posts' => true,
				's'                   => $search,
			);

			if ( $featured ) {
				$args['meta_query'] = array(
					array(
						'key'   => '_spcore_featured',
						'value' => '1',
					),
				);
			}

			$query = new WP_Query( $args );
			$items = array();

			foreach ( $query->posts as $post ) {
				$items[] = $this->prepare_project( $post );
			}

			$payload = array(
				'items'       => $items,
				'total'       => (int) $query->found_posts,
				'total_pages' => (int) $query->max_num_pages,
			);

			set_transient( $cache_id, $payload, 5 * MINUTE_IN_SECONDS );
		}

		$response = rest_ensure_response( $payload['items'] );
		$response->header( 'X-WP-Total', (string) $payload['total'] );
		$response->header( 'X-WP-TotalPages', (string) $payload['total_pages'] );
		$response->header( 'Cache-Control', 'public, max-age=300' );
		return $response;
	}

	/**
	 * Admin-only operational health payload.
	 *
	 * @return WP_REST_Response
	 */
	public function health() {
		return rest_ensure_response(
			array(
				'status'                 => 'ok',
				'plugin_version'         => SPCORE_VERSION,
				'daily_event_scheduled'  => (bool) wp_next_scheduled( 'spcore_daily_maintenance' ),
				'lead_table_available'   => $this->lead_table_available(),
				'engineering_cache_warm' => (bool) get_transient( SPCORE_CACHE_KEY ),
			)
		);
	}

	/**
	 * Normalize one project for JSON output.
	 *
	 * @param WP_Post $post Project post.
	 * @return array<string, mixed>
	 */
	private function prepare_project( WP_Post $post ) {
		$thumbnail = get_the_post_thumbnail_url( $post, 'sp-card' );
		$nda       = (bool) get_post_meta( $post->ID, '_spcore_nda', true );
		$live_url  = get_post_meta( $post->ID, '_spcore_live_url', true );

		return array(
			'id'          => $post->ID,
			'slug'        => $post->post_name,
			'title'       => get_the_title( $post ),
			'url'         => get_permalink( $post ),
			'summary'     => get_post_meta( $post->ID, '_spcore_summary', true ),
			'role'        => get_post_meta( $post->ID, '_spcore_role', true ),
			'tech_stack'  => array_values(
				array_filter(
					array_map( 'trim', explode( ',', (string) get_post_meta( $post->ID, '_spcore_tech_stack', true ) ) )
				)
			),
			'result'      => get_post_meta( $post->ID, '_spcore_result', true ),
			'live_url'    => $nda ? null : esc_url_raw( $live_url ),
			'is_private'  => $nda,
			'is_featured' => (bool) get_post_meta( $post->ID, '_spcore_featured', true ),
			'thumbnail'   => $thumbnail ? esc_url_raw( $thumbnail ) : null,
		);
	}

	/**
	 * Check custom table availability without exposing table names publicly.
	 *
	 * @return bool
	 */
	private function lead_table_available() {
		global $wpdb;
		$table = SPCore_Leads::table_name();
		return $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) === $table;
	}
}
