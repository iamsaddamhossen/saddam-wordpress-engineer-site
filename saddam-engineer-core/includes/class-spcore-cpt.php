<?php
/**
 * Registers all custom post types and taxonomies.
 *
 * @package SaddamPortfolioCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPCore_CPT {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'register_all' ) );
	}

	/**
	 * Register every CPT and taxonomy.
	 */
	public function register_all() {
		$this->register_projects();
		$this->register_case_studies();
		$this->register_services();
		$this->register_testimonials();
		$this->register_tools();
		$this->register_leads_cpt();
		$this->register_taxonomies();
	}

	private function labels( $singular, $plural ) {
		return array(
			'name'               => $plural,
			'singular_name'      => $singular,
			'menu_name'          => $plural,
			'add_new'            => __( 'Add New', 'spcore' ),
			'add_new_item'       => sprintf( __( 'Add New %s', 'spcore' ), $singular ),
			'edit_item'          => sprintf( __( 'Edit %s', 'spcore' ), $singular ),
			'new_item'           => sprintf( __( 'New %s', 'spcore' ), $singular ),
			'view_item'          => sprintf( __( 'View %s', 'spcore' ), $singular ),
			'search_items'       => sprintf( __( 'Search %s', 'spcore' ), $plural ),
			'not_found'          => __( 'None found', 'spcore' ),
			'not_found_in_trash' => __( 'None found in Trash', 'spcore' ),
			'all_items'          => sprintf( __( 'All %s', 'spcore' ), $plural ),
		);
	}

	private function register_projects() {
		register_post_type(
			'sp_project',
			array(
				'labels'       => $this->labels( __( 'Project', 'spcore' ), __( 'Projects', 'spcore' ) ),
				'public'       => true,
				'has_archive'  => true,
				'menu_icon'    => 'dashicons-portfolio',
				'menu_position'=> 20,
				'rewrite'      => array( 'slug' => 'projects' ),
				'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
				'show_in_rest' => true,
			)
		);
	}

	private function register_case_studies() {
		register_post_type(
			'sp_case_study',
			array(
				'labels'       => $this->labels( __( 'Case Study', 'spcore' ), __( 'Case Studies', 'spcore' ) ),
				'public'       => true,
				'has_archive'  => true,
				'menu_icon'    => 'dashicons-analytics',
				'menu_position'=> 21,
				'rewrite'      => array( 'slug' => 'case-studies' ),
				'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
				'show_in_rest' => true,
			)
		);
	}

	private function register_services() {
		register_post_type(
			'sp_service',
			array(
				'labels'       => $this->labels( __( 'Service', 'spcore' ), __( 'Services', 'spcore' ) ),
				'public'       => true,
				'has_archive'  => true,
				'menu_icon'    => 'dashicons-hammer',
				'menu_position'=> 22,
				'rewrite'      => array( 'slug' => 'services' ),
				'supports'     => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
				'show_in_rest' => true,
			)
		);
	}

	private function register_testimonials() {
		register_post_type(
			'sp_testimonial',
			array(
				'labels'       => $this->labels( __( 'Testimonial', 'spcore' ), __( 'Testimonials', 'spcore' ) ),
				'public'       => true,
				'has_archive'  => false,
				'menu_icon'    => 'dashicons-format-quote',
				'menu_position'=> 23,
				'rewrite'      => array( 'slug' => 'testimonials' ),
				'supports'     => array( 'title', 'editor', 'page-attributes' ),
				'show_in_rest' => true,
			)
		);
	}

	private function register_tools() {
		register_post_type(
			'sp_tool',
			array(
				'labels'       => $this->labels( __( 'Recommended Tool', 'spcore' ), __( 'Recommended Tools', 'spcore' ) ),
				'public'       => true,
				'has_archive'  => false,
				'menu_icon'    => 'dashicons-admin-links',
				'menu_position'=> 24,
				'rewrite'      => array( 'slug' => 'tools' ),
				'supports'     => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
				'show_in_rest' => true,
			)
		);
	}

	/**
	 * Contact Leads CPT ,  stored privately, admin only.
	 */
	private function register_leads_cpt() {
		register_post_type(
			'sp_lead',
			array(
				'labels'       => $this->labels( __( 'Lead', 'spcore' ), __( 'Contact Leads', 'spcore' ) ),
				'public'       => false,
				'show_ui'      => true,
				'show_in_menu' => true,
				'has_archive'  => false,
				'menu_icon'    => 'dashicons-email-alt',
				'menu_position'=> 25,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'supports'     => array( 'title' ),
				'capabilities' => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap' => true,
			)
		);
	}

	private function register_taxonomies() {
		$shared = array(
			'hierarchical'      => true,
			'public'            => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
		);

		register_taxonomy(
			'sp_category',
			array( 'sp_project', 'sp_case_study' ),
			array_merge(
				$shared,
				array(
					'labels'  => $this->labels( __( 'Category', 'spcore' ), __( 'Categories', 'spcore' ) ),
					'rewrite' => array( 'slug' => 'project-category' ),
				)
			)
		);

		register_taxonomy(
			'sp_industry',
			array( 'sp_project' ),
			array_merge(
				$shared,
				array(
					'labels'  => $this->labels( __( 'Industry', 'spcore' ), __( 'Industries', 'spcore' ) ),
					'rewrite' => array( 'slug' => 'industry' ),
				)
			)
		);

		register_taxonomy(
			'sp_tool_category',
			array( 'sp_tool' ),
			array_merge(
				$shared,
				array(
					'labels'  => $this->labels( __( 'Tool Category', 'spcore' ), __( 'Tool Categories', 'spcore' ) ),
					'rewrite' => array( 'slug' => 'tool-category' ),
				)
			)
		);
	}
}
