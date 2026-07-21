<?php
/**
 * Native meta boxes for engineering content types (no ACF required).
 *
 * @package SaddamPortfolioCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPCore_Meta {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'register_fields' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_boxes' ) );
		add_action( 'save_post', array( $this, 'save' ), 10, 2 );
	}

	/**
	 * Field schema per post type.
	 * Types: text, url, textarea, select, checkbox, number, repeater_faq.
	 */
	public function schema() {
		return array(
			'sp_project' => array(
				'live_url'        => array( 'label' => 'Live URL', 'type' => 'url' ),
				'category_label'  => array( 'label' => 'Category (display label)', 'type' => 'text' ),
				'industry_label'  => array( 'label' => 'Industry', 'type' => 'text' ),
				'project_type'    => array( 'label' => 'Project Type', 'type' => 'text' ),
				'client_location' => array( 'label' => 'Client / Location', 'type' => 'text' ),
				'summary'         => array( 'label' => 'Short Summary', 'type' => 'textarea' ),
				'problem'         => array( 'label' => 'Client Problem', 'type' => 'textarea' ),
				'role'            => array( 'label' => 'My Role', 'type' => 'textarea' ),
				'what_built'      => array( 'label' => 'What I Built', 'type' => 'textarea' ),
				'tech_stack'      => array( 'label' => 'Tech Stack (comma separated)', 'type' => 'text' ),
				'business_value'  => array( 'label' => 'Business Value', 'type' => 'textarea' ),
				'result'          => array( 'label' => 'Measurable Result', 'type' => 'text' ),
				'featured'        => array( 'label' => 'Featured Project', 'type' => 'checkbox' ),
				'nda'             => array( 'label' => 'NDA / Private (hide live link)', 'type' => 'checkbox' ),
			),
			'sp_case_study' => array(
				'client_project'   => array( 'label' => 'Client / Project', 'type' => 'text' ),
				'challenge'        => array( 'label' => 'Challenge', 'type' => 'textarea' ),
				'solution'         => array( 'label' => 'Solution', 'type' => 'textarea' ),
				'technical_approach' => array( 'label' => 'Technical Approach', 'type' => 'textarea' ),
				'stack'            => array( 'label' => 'Stack', 'type' => 'text' ),
				'result'           => array( 'label' => 'Result', 'type' => 'textarea' ),
				'proof_metric'     => array( 'label' => 'Proof Metric (e.g. 42  91)', 'type' => 'text' ),
				'testimonial'      => array( 'label' => 'Testimonial', 'type' => 'textarea' ),
				'cta_label'        => array( 'label' => 'CTA Label', 'type' => 'text' ),
				'cta_url'          => array( 'label' => 'CTA URL', 'type' => 'url' ),
				'related_project'  => array( 'label' => 'Related Project ID', 'type' => 'number' ),
			),
			'sp_service' => array(
				'short_desc'   => array( 'label' => 'Short Description', 'type' => 'textarea' ),
				'problem_statement' => array( 'label' => 'Problem Statement', 'type' => 'textarea' ),
				'who_for'      => array( 'label' => 'Who It Is For', 'type' => 'textarea' ),
				'deliverables' => array( 'label' => 'Deliverables (one per line)', 'type' => 'textarea' ),
				'process'      => array( 'label' => 'Process (one per line)', 'type' => 'textarea' ),
				'outcome'      => array( 'label' => 'Outcome', 'type' => 'textarea' ),
				'starting_note' => array( 'label' => 'Starting Note', 'type' => 'text' ),
				'cta_label'    => array( 'label' => 'CTA Label', 'type' => 'text' ),
				'cta_url'      => array( 'label' => 'CTA URL', 'type' => 'url' ),
				'proof_link'    => array( 'label' => 'Proof / Case Study URL', 'type' => 'url' ),
				'icon'         => array( 'label' => 'Dashicon or emoji', 'type' => 'text' ),
				'faq'          => array( 'label' => 'FAQs', 'type' => 'repeater_faq' ),
			),
			'sp_testimonial' => array(
				'client_name'   => array( 'label' => 'Client Name / Initials', 'type' => 'text' ),
				'platform'      => array( 'label' => 'Platform (Upwork, etc.)', 'type' => 'text' ),
				'project_type'  => array( 'label' => 'Project Type', 'type' => 'text' ),
				'rating'        => array( 'label' => 'Rating (1-5, optional)', 'type' => 'number' ),
				'verified_link' => array( 'label' => 'Verified Link (optional)', 'type' => 'url' ),
			),
			'sp_tool' => array(
				'category_label' => array( 'label' => 'Category', 'type' => 'text' ),
				'description'    => array( 'label' => 'Description', 'type' => 'textarea' ),
				'why'            => array( 'label' => 'Why I Recommend It', 'type' => 'textarea' ),
				'best_for'       => array( 'label' => 'Best For', 'type' => 'text' ),
				'affiliate_url'  => array( 'label' => 'Affiliate URL', 'type' => 'url' ),
				'plain_url'      => array( 'label' => 'Non-affiliate URL', 'type' => 'url' ),
				'disclose'       => array( 'label' => 'Show affiliate disclosure', 'type' => 'checkbox' ),
				'cta_label'      => array( 'label' => 'CTA Label', 'type' => 'text' ),
				'rating'         => array( 'label' => 'Rating (manual, optional)', 'type' => 'number' ),
				'featured'       => array( 'label' => 'Featured', 'type' => 'checkbox' ),
			),
		);
	}

	/**
	 * Register structured project metadata for REST and schema-aware tooling.
	 * Complex FAQ rows stay in the native meta box only.
	 *
	 * @return void
	 */
	public function register_fields() {
		foreach ( $this->schema() as $post_type => $fields ) {
			foreach ( $fields as $key => $conf ) {
				if ( 'repeater_faq' === $conf['type'] ) {
					continue;
				}

				$type = 'string';
				if ( 'checkbox' === $conf['type'] ) {
					$type = 'boolean';
				} elseif ( 'number' === $conf['type'] ) {
					$type = 'number';
				}

				register_post_meta(
					$post_type,
					'_spcore_' . $key,
					array(
						'type'              => $type,
						'single'            => true,
						'show_in_rest'      => true,
						'sanitize_callback' => array( $this, 'sanitize_registered_meta' ),
						'auth_callback'     => static function () {
							return current_user_can( 'edit_posts' );
						},
					)
				);
			}
		}
	}

	/**
	 * Sanitize registered metadata according to the runtime value type.
	 *
	 * @param mixed  $value    Submitted value.
	 * @param string $meta_key Meta key.
	 * @return mixed
	 */
	public function sanitize_registered_meta( $value, $meta_key ) {
		if ( is_bool( $value ) ) {
			return (bool) $value;
		}
		if ( is_int( $value ) || is_float( $value ) ) {
			return (float) $value;
		}
		if ( false !== strpos( $meta_key, '_url' ) || false !== strpos( $meta_key, '_link' ) ) {
			return esc_url_raw( (string) $value );
		}
		return sanitize_textarea_field( (string) $value );
	}

	public function add_boxes() {
		foreach ( $this->schema() as $post_type => $fields ) {
			add_meta_box(
				'spcore_' . $post_type,
				__( 'Engineering Details', 'spcore' ),
				array( $this, 'render_box' ),
				$post_type,
				'normal',
				'high'
			);
		}
	}

	public function render_box( $post ) {
		$schema = $this->schema();
		if ( ! isset( $schema[ $post->post_type ] ) ) {
			return;
		}
		wp_nonce_field( 'spcore_meta_save', 'spcore_meta_nonce' );
		echo '<style>.spcore-field{margin:0 0 16px}.spcore-field label{display:block;font-weight:600;margin-bottom:4px}.spcore-field input[type=text],.spcore-field input[type=url],.spcore-field input[type=number],.spcore-field textarea{width:100%}.spcore-field textarea{min-height:70px}.spcore-faq-row{border:1px solid #dcdcde;padding:10px;margin-bottom:8px;border-radius:6px}</style>';

		foreach ( $schema[ $post->post_type ] as $key => $conf ) {
			$value = get_post_meta( $post->ID, '_spcore_' . $key, true );
			echo '<div class="spcore-field">';
			echo '<label for="spcore_' . esc_attr( $key ) . '">' . esc_html( $conf['label'] ) . '</label>';

			switch ( $conf['type'] ) {
				case 'textarea':
					printf(
						'<textarea id="spcore_%1$s" name="spcore_%1$s">%2$s</textarea>',
						esc_attr( $key ),
						esc_textarea( $value )
					);
					break;
				case 'checkbox':
					printf(
						'<label style="font-weight:400"><input type="checkbox" id="spcore_%1$s" name="spcore_%1$s" value="1" %2$s /> Yes</label>',
						esc_attr( $key ),
						checked( $value, '1', false )
					);
					break;
				case 'number':
					printf(
						'<input type="number" id="spcore_%1$s" name="spcore_%1$s" value="%2$s" />',
						esc_attr( $key ),
						esc_attr( $value )
					);
					break;
				case 'url':
					printf(
						'<input type="url" id="spcore_%1$s" name="spcore_%1$s" value="%2$s" placeholder="https://" />',
						esc_attr( $key ),
						esc_attr( $value )
					);
					break;
				case 'repeater_faq':
					$this->render_faq( $post->ID );
					break;
				default:
					printf(
						'<input type="text" id="spcore_%1$s" name="spcore_%1$s" value="%2$s" />',
						esc_attr( $key ),
						esc_attr( $value )
					);
			}
			echo '</div>';
		}
	}

	/**
	 * Simple repeatable FAQ field (5 fixed rows ,  no JS needed, robust).
	 */
	private function render_faq( $post_id ) {
		$faq = get_post_meta( $post_id, '_spcore_faq', true );
		$faq = is_array( $faq ) ? $faq : array();
		echo '<p class="description">Add up to 6 FAQ items. Leave blank to skip.</p>';
		for ( $i = 0; $i < 6; $i++ ) {
			$q = isset( $faq[ $i ]['q'] ) ? $faq[ $i ]['q'] : '';
			$a = isset( $faq[ $i ]['a'] ) ? $faq[ $i ]['a'] : '';
			echo '<div class="spcore-faq-row">';
			printf( '<input type="text" name="spcore_faq[%d][q]" value="%s" placeholder="Question" style="width:100%%;margin-bottom:6px" />', $i, esc_attr( $q ) );
			printf( '<textarea name="spcore_faq[%d][a]" placeholder="Answer" style="width:100%%">%s</textarea>', $i, esc_textarea( $a ) );
			echo '</div>';
		}
	}

	public function save( $post_id, $post ) {
		if ( ! isset( $_POST['spcore_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['spcore_meta_nonce'] ) ), 'spcore_meta_save' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$schema = $this->schema();
		if ( ! isset( $schema[ $post->post_type ] ) ) {
			return;
		}

		foreach ( $schema[ $post->post_type ] as $key => $conf ) {
			if ( 'repeater_faq' === $conf['type'] ) {
				$rows = isset( $_POST['spcore_faq'] ) ? (array) wp_unslash( $_POST['spcore_faq'] ) : array();
				$clean = array();
				foreach ( $rows as $row ) {
					$q = isset( $row['q'] ) ? sanitize_text_field( $row['q'] ) : '';
					$a = isset( $row['a'] ) ? sanitize_textarea_field( $row['a'] ) : '';
					if ( '' !== $q ) {
						$clean[] = array( 'q' => $q, 'a' => $a );
					}
				}
				update_post_meta( $post_id, '_spcore_faq', $clean );
				continue;
			}

			$field = 'spcore_' . $key;
			if ( 'checkbox' === $conf['type'] ) {
				update_post_meta( $post_id, '_spcore_' . $key, isset( $_POST[ $field ] ) ? '1' : '' );
				continue;
			}
			if ( ! isset( $_POST[ $field ] ) ) {
				continue;
			}
			$raw = wp_unslash( $_POST[ $field ] );
			switch ( $conf['type'] ) {
				case 'url':
					$val = esc_url_raw( $raw );
					break;
				case 'textarea':
					$val = sanitize_textarea_field( $raw );
					break;
				case 'number':
					$val = '' === $raw ? '' : floatval( $raw );
					break;
				default:
					$val = sanitize_text_field( $raw );
			}
			update_post_meta( $post_id, '_spcore_' . $key, $val );
		}
	}
}

/**
 * Helper to read a CPT meta value in templates.
 *
 * @param string $key     Field key without the _spcore_ prefix.
 * @param int    $post_id Optional post ID.
 * @return mixed
 */
function spcore_meta( $key, $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	return get_post_meta( $post_id, '_spcore_' . $key, true );
}
