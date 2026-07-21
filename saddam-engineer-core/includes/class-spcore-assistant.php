<?php
/**
 * Privacy-conscious guided and optional AI site assistant.
 *
 * @package SaddamEngineerCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class SPCore_Assistant {

	/** @var self|null */
	private static $instance = null;

	/** Return singleton. */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/** Register hooks. */
	private function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );
		add_action( 'wp_footer', array( $this, 'render' ), 35 );
		add_action( 'wp_ajax_spcore_assistant', array( $this, 'answer' ) );
		add_action( 'wp_ajax_nopriv_spcore_assistant', array( $this, 'answer' ) );
	}

	/** Whether the assistant should render. */
	private function enabled() {
		return ! is_admin() && (bool) spcore_get( 'chat_enable', 1 );
	}

	/** Load only the small assistant assets. */
	public function assets() {
		if ( ! $this->enabled() ) {
			return;
		}

		wp_enqueue_style( 'spcore-assistant', SPCORE_URL . 'assets/css/assistant.css', array(), SPCORE_VERSION );
		wp_enqueue_script( 'spcore-assistant', SPCORE_URL . 'assets/js/assistant.js', array(), SPCORE_VERSION, true );
		wp_script_add_data( 'spcore-assistant', 'strategy', 'defer' );
		wp_localize_script(
			'spcore-assistant',
			'SPCORE_ASSISTANT',
			array(
				'ajax'       => admin_url( 'admin-ajax.php' ),
				'nonce'      => wp_create_nonce( 'spcore_assistant_action' ),
				'mode'       => $this->mode(),
				'contactUrl' => home_url( '/contact/' ),
				'privacy'    => (string) spcore_get( 'chat_privacy_note', 'Guided messages are processed in your browser. If AI mode is enabled, the question is sent to the configured provider and is not stored by this website.' ),
				'i18n'       => array(
					'error'   => __( 'I could not answer that right now. Please use the contact page or try again shortly.', 'spcore' ),
					'working' => __( 'Reviewing Saddam’s WordPress work…', 'spcore' ),
				),
			)
		);
	}

	/** Resolve safe operating mode. */
	private function mode() {
		$mode = sanitize_key( (string) spcore_get( 'chat_mode', 'guided' ) );
		if ( 'ai' !== $mode ) {
			return 'guided';
		}

		$provider = sanitize_key( (string) spcore_get( 'chat_api_provider', '' ) );
		$key      = defined( 'SPCORE_OPENAI_API_KEY' ) ? trim( (string) SPCORE_OPENAI_API_KEY ) : trim( (string) spcore_get( 'chat_api_key', '' ) );
		$webhook  = esc_url_raw( (string) spcore_get( 'chat_webhook_url', '' ) );
		if ( ( 'openai' === $provider && '' !== $key ) || ( 'webhook' === $provider && '' !== $webhook ) ) {
			return 'ai';
		}
		return 'guided';
	}

	/** Render accessible assistant markup. */
	public function render() {
		if ( ! $this->enabled() ) {
			return;
		}

		$mode    = $this->mode();
		$welcome = (string) spcore_get( 'chat_welcome', 'Hi ,  I’m Saddam’s site assistant. I can help you explore his WordPress work, technical writing and availability.' );
		?>
		<div class="sp-assistant" id="sp-assistant" data-mode="<?php echo esc_attr( $mode ); ?>">
			<button class="sp-assistant-launcher" type="button" aria-expanded="false" aria-controls="sp-assistant-panel">
				<span class="sp-assistant-launcher-icon" aria-hidden="true">SH</span>
				<span class="sp-assistant-launcher-label"><?php esc_html_e( 'Ask Saddam', 'spcore' ); ?></span>
			</button>
			<section class="sp-assistant-panel" id="sp-assistant-panel" aria-label="<?php esc_attr_e( 'Saddam Hossen site assistant', 'spcore' ); ?>" aria-hidden="true">
				<header class="sp-assistant-header">
					<div><span class="sp-assistant-status" aria-hidden="true"></span><strong><?php esc_html_e( 'Saddam Hossen', 'spcore' ); ?></strong><small><?php echo 'ai' === $mode ? esc_html__( 'AI mode · site-grounded', 'spcore' ) : esc_html__( 'WordPress work guide', 'spcore' ); ?></small></div>
					<button type="button" class="sp-assistant-close" aria-label="<?php esc_attr_e( 'Close assistant', 'spcore' ); ?>">×</button>
				</header>
				<div class="sp-assistant-log" role="log" aria-live="polite" aria-relevant="additions">
					<div class="sp-assistant-message is-assistant"><p><?php echo esc_html( $welcome ); ?></p></div>
				</div>
				<div class="sp-assistant-suggestions" aria-label="<?php esc_attr_e( 'Suggested questions', 'spcore' ); ?>">
					<button type="button" data-prompt="engineering"><?php esc_html_e( 'Engineering evidence', 'spcore' ); ?></button>
					<button type="button" data-prompt="work"><?php esc_html_e( 'Production work', 'spcore' ); ?></button>
					<button type="button" data-prompt="availability"><?php esc_html_e( 'Role availability', 'spcore' ); ?></button>
					<button type="button" data-prompt="writing"><?php esc_html_e( 'Technical writing', 'spcore' ); ?></button>
				</div>
				<form class="sp-assistant-form">
					<label class="screen-reader-text" for="sp-assistant-input"><?php esc_html_e( 'Ask a question', 'spcore' ); ?></label>
					<input id="sp-assistant-input" name="message" type="text" maxlength="500" autocomplete="off" placeholder="<?php esc_attr_e( 'Ask about WordPress work…', 'spcore' ); ?>">
					<button type="submit" aria-label="<?php esc_attr_e( 'Send question', 'spcore' ); ?>">
						<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m4 4 16 8-16 8 3-8-3-8Zm3 8h8" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
					</button>
				</form>
				<p class="sp-assistant-privacy"><?php echo esc_html( spcore_get( 'chat_privacy_note', 'Guided messages are processed in your browser. If AI mode is enabled, the question is sent to the configured provider and is not stored by this website.' ) ); ?></p>
			</section>
		</div>
		<?php
	}

	/** AJAX answer for optional AI mode. */
	public function answer() {
		check_ajax_referer( 'spcore_assistant_action', 'nonce' );
		if ( ! spcore_get( 'chat_enable', 1 ) || 'ai' !== $this->mode() ) {
			wp_send_json_error( array( 'message' => __( 'AI mode is not configured.', 'spcore' ) ), 400 );
		}

		$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
		$message = trim( $message );
		if ( '' === $message || strlen( $message ) > 500 ) {
			wp_send_json_error( array( 'message' => __( 'Please enter a question under 500 characters.', 'spcore' ) ), 400 );
		}

		$rate_key = 'spcore_chat_' . $this->visitor_hash();
		$count    = absint( get_transient( $rate_key ) );
		if ( $count >= 12 ) {
			wp_send_json_error( array( 'message' => __( 'The assistant has reached its temporary request limit. Please try again later.', 'spcore' ) ), 429 );
		}
		set_transient( $rate_key, $count + 1, 10 * MINUTE_IN_SECONDS );

		$provider = sanitize_key( (string) spcore_get( 'chat_api_provider', '' ) );
		$result   = 'webhook' === $provider ? $this->webhook_answer( $message ) : $this->openai_answer( $message );
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ), 502 );
		}

		wp_send_json_success( array( 'answer' => $result ) );
	}

	/** Build a non-reversible, short-lived rate-limit key. */
	private function visitor_hash() {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
		$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
		return substr( hash_hmac( 'sha256', $ip . '|' . $ua, wp_salt( 'nonce' ) ), 0, 24 );
	}

	/** Site-grounded instruction text. */
	private function instructions() {
		$project_titles = wp_list_pluck(
			get_posts( array( 'post_type' => 'sp_project', 'post_status' => 'publish', 'posts_per_page' => 8, 'fields' => 'all', 'no_found_rows' => true ) ),
			'post_title'
		);
		$case_titles = wp_list_pluck(
			get_posts( array( 'post_type' => 'sp_case_study', 'post_status' => 'publish', 'posts_per_page' => 8, 'fields' => 'all', 'no_found_rows' => true ) ),
			'post_title'
		);

		return implode(
			"\n",
			array(
				'You help visitors navigate the public WordPress engineering site for ' . spcore_get( 'name', 'Saddam Hossen' ) . '.',
				'Answer briefly and professionally using only the supplied site facts. Never invent employers, metrics, testimonials, availability, salary, certifications or client outcomes.',
				'When evidence is incomplete, say so and direct the visitor to the relevant page or contact form.',
				'Professional summary: ' . spcore_get( 'bio' ),
				'Headline: ' . spcore_get( 'headline' ),
				'Projects: ' . implode( '; ', array_map( 'sanitize_text_field', $project_titles ) ),
				'Case studies: ' . implode( '; ', array_map( 'sanitize_text_field', $case_titles ) ),
				'Engineering evidence: ' . home_url( '/engineering/' ),
				'Work: ' . ( get_post_type_archive_link( 'sp_case_study' ) ?: home_url( '/case-studies/' ) ),
				'Writing: ' . home_url( '/blog/' ),
				'Contact: ' . home_url( '/contact/' ),
				'Keep the answer below 140 words. Do not output HTML.',
			)
		);
	}

	/** Call OpenAI Responses API from the server. */
	private function openai_answer( $message ) {
		$key   = defined( 'SPCORE_OPENAI_API_KEY' ) ? trim( (string) SPCORE_OPENAI_API_KEY ) : trim( (string) spcore_get( 'chat_api_key', '' ) );
		$model = defined( 'SPCORE_OPENAI_MODEL' ) ? sanitize_text_field( (string) SPCORE_OPENAI_MODEL ) : sanitize_text_field( (string) spcore_get( 'chat_api_model', 'gpt-5.6' ) );
		if ( '' === $key || '' === $model ) {
			return new WP_Error( 'spcore_chat_config', __( 'The AI assistant is not fully configured.', 'spcore' ) );
		}

		$response = wp_safe_remote_post(
			'https://api.openai.com/v1/responses',
			array(
				'timeout'     => 35,
				'redirection' => 0,
				'headers'     => array(
					'Authorization' => 'Bearer ' . $key,
					'Content-Type'  => 'application/json',
				),
				'body'        => wp_json_encode(
					array(
						'model'             => $model,
						'instructions'      => $this->instructions(),
						'input'             => $message,
						'max_output_tokens' => 450,
					)
				),
			)
		);
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		$status = wp_remote_retrieve_response_code( $response );
		$data   = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( $status < 200 || $status >= 300 || ! is_array( $data ) ) {
			return new WP_Error( 'spcore_chat_provider', __( 'The AI provider did not return a usable response.', 'spcore' ) );
		}

		$text = isset( $data['output_text'] ) ? (string) $data['output_text'] : '';
		if ( '' === trim( $text ) && ! empty( $data['output'] ) && is_array( $data['output'] ) ) {
			foreach ( $data['output'] as $item ) {
				if ( empty( $item['content'] ) || ! is_array( $item['content'] ) ) {
					continue;
				}
				foreach ( $item['content'] as $part ) {
					if ( isset( $part['text'] ) && is_string( $part['text'] ) ) {
						$text .= ( '' === $text ? '' : "\n" ) . $part['text'];
					}
				}
			}
		}
		$text = trim( wp_strip_all_tags( $text ) );
		return '' !== $text ? $text : new WP_Error( 'spcore_chat_empty', __( 'The AI provider returned an empty answer.', 'spcore' ) );
	}

	/** Call an optional owner-controlled webhook. */
	private function webhook_answer( $message ) {
		$url = esc_url_raw( (string) spcore_get( 'chat_webhook_url', '' ) );
		if ( '' === $url ) {
			return new WP_Error( 'spcore_chat_config', __( 'The assistant webhook is not configured.', 'spcore' ) );
		}
		$response = wp_safe_remote_post(
			$url,
			array(
				'timeout'     => 25,
				'redirection' => 0,
				'headers'     => array( 'Content-Type' => 'application/json' ),
				'body'        => wp_json_encode(
					array(
						'message'      => $message,
						'instructions' => $this->instructions(),
						'site_url'     => home_url( '/' ),
					)
				),
			)
		);
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		$text = is_array( $data ) && isset( $data['answer'] ) ? sanitize_textarea_field( $data['answer'] ) : '';
		return '' !== trim( $text ) ? trim( $text ) : new WP_Error( 'spcore_chat_empty', __( 'The assistant webhook returned an empty answer.', 'spcore' ) );
	}
}
