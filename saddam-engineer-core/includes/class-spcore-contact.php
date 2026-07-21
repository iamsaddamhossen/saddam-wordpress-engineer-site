<?php
/**
 * Production-ready contact form: validation, honeypot, optional captcha,
 * lead storage, admin + visitor email, webhook, logging.
 *
 * Handles both classic POST (admin-post.php) and AJAX (admin-ajax.php),
 * so the form works even if JavaScript is disabled.
 *
 * @package SaddamPortfolioCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPCore_Contact {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		// AJAX (logged out + logged in).
		add_action( 'wp_ajax_spcore_contact', array( $this, 'handle_ajax' ) );
		add_action( 'wp_ajax_nopriv_spcore_contact', array( $this, 'handle_ajax' ) );
		// Classic no-JS fallback.
		add_action( 'admin_post_nopriv_spcore_contact', array( $this, 'handle_post' ) );
		add_action( 'admin_post_spcore_contact', array( $this, 'handle_post' ) );
	}

	/**
	 * Validate and process. Returns array( success, message, errors ).
	 */
	private function process( $data ) {
		$errors = array();

		// 1. Nonce.
		if ( empty( $data['spcore_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( (string) $data['spcore_nonce'] ), 'spcore_contact_action' ) ) {
			return array( 'success' => false, 'message' => __( 'Security check failed. Please refresh the page and try again.', 'spcore' ), 'errors' => array() );
		}

		// 2. Honeypot ,  if filled, silently succeed (bot).
		if ( ! empty( $data['spcore_website_hp'] ) ) {
			return array( 'success' => true, 'message' => __( 'Thanks ,  your message has been received.', 'spcore' ), 'errors' => array() );
		}

		// 3. Time trap ,  submitted too fast = bot.
		$rendered = isset( $data['spcore_ts'] ) ? absint( $data['spcore_ts'] ) : 0;
		if ( $rendered && ( time() - $rendered ) < 2 ) {
			return array( 'success' => true, 'message' => __( 'Thanks ,  your message has been received.', 'spcore' ), 'errors' => array() );
		}

		// 4. Required fields.
		$name    = isset( $data['name'] ) ? $this->limit( sanitize_text_field( $data['name'] ), 120 ) : '';
		$email   = isset( $data['email'] ) ? sanitize_email( $data['email'] ) : '';
		$message = isset( $data['message'] ) ? $this->limit( sanitize_textarea_field( $data['message'] ), 5000 ) : '';

		if ( '' === $name ) {
			$errors['name'] = __( 'Please enter your name.', 'spcore' );
		}
		if ( '' === $email || ! is_email( $email ) ) {
			$errors['email'] = __( 'Please enter a valid email address.', 'spcore' );
		}
		if ( ( function_exists( 'mb_strlen' ) ? mb_strlen( $message ) : strlen( $message ) ) < 10 ) {
			$errors['message'] = __( 'Please enter a short message.', 'spcore' );
		}

		// 5. Optional website validation.
		$website = '';
		if ( ! empty( $data['website'] ) ) {
			$website = esc_url_raw( $data['website'] );
			if ( '' === $website ) {
				$errors['website'] = __( 'Please enter a valid website URL, or leave it blank.', 'spcore' );
			}
		}

		// 6. Optional captcha (only enforced if keys exist).
		$captcha_ok = $this->verify_captcha( $data );
		if ( is_wp_error( $captcha_ok ) ) {
			$errors['captcha'] = $captcha_ok->get_error_message();
		}

		if ( ! empty( $errors ) ) {
			return array( 'success' => false, 'message' => __( 'Please fix the highlighted fields.', 'spcore' ), 'errors' => $errors );
		}

		// 7. Rate limiting after field and CAPTCHA validation.
		$rate_limit = $this->enforce_rate_limit( $email );
		if ( is_wp_error( $rate_limit ) ) {
			return array( 'success' => false, 'message' => $rate_limit->get_error_message(), 'errors' => array( 'rate_limit' => $rate_limit->get_error_message() ) );
		}

		// Build the clean lead record.
		$inquiry_options = array( 'Hiring / Full-time', 'Long-term contract', 'Technical assessment', 'Custom Plugin', 'WooCommerce', 'Booking / API System', 'Performance', 'Security / Malware Cleanup', 'Monthly Support', 'Other' );
		$engagement_options = array( 'Full-time role', 'Long-term contract', 'Part-time / trial', 'Defined project', 'Prefer not to say' );
		$inquiry = isset( $data['inquiry_type'] ) ? sanitize_text_field( $data['inquiry_type'] ) : '';
		$budget  = isset( $data['budget'] ) ? sanitize_text_field( $data['budget'] ) : '';

		$lead = array(
			'name'         => $name,
			'email'        => $email,
			'company'      => isset( $data['company'] ) ? $this->limit( sanitize_text_field( $data['company'] ), 191 ) : '',
			'website'      => $website,
			'inquiry_type' => in_array( $inquiry, $inquiry_options, true ) ? $inquiry : 'Other',
			'budget'       => in_array( $budget, $engagement_options, true ) ? $budget : 'Prefer not to say',
			'message'      => $message,
			'source_page'  => isset( $data['source_page'] ) ? esc_url_raw( $data['source_page'] ) : '',
			'utm_source'   => isset( $data['utm_source'] ) ? sanitize_text_field( $data['utm_source'] ) : '',
			'utm_medium'   => isset( $data['utm_medium'] ) ? sanitize_text_field( $data['utm_medium'] ) : '',
			'utm_campaign' => isset( $data['utm_campaign'] ) ? sanitize_text_field( $data['utm_campaign'] ) : '',
			'referrer'     => isset( $data['referrer'] ) ? esc_url_raw( $data['referrer'] ) : '',
			'ip'           => spcore_get( 'store_ip', 0 ) ? $this->ip() : '',
		);

		// Store lead (never blocks success).
		SPCore_Leads::store( $lead );

		// Email ,  capture failure to report gracefully.
		$mail_ok = $this->send_emails( $lead );

		// Webhook (best effort).
		$this->maybe_webhook( $lead );

		if ( ! $mail_ok ) {
			// Lead is saved; email failed. Tell the visitor honestly with a fallback.
			$fallback = spcore_get( 'recipient_email' );
			$this->log( 'wp_mail returned false for lead from ' . $email );
			return array(
				'success' => true,
				'message' => sprintf(
					/* translators: %s: fallback email */
					__( 'Your message was received and saved. If you do not receive a reply, please email me directly at %s.', 'spcore' ),
					$fallback
				),
				'errors'  => array(),
				'mail'    => false,
			);
		}

		return array(
			'success' => true,
			'message' => __( 'Thank you ,  your message has been sent. I will review it as soon as possible.', 'spcore' ),
			'errors'  => array(),
			'mail'    => true,
		);
	}

	/**
	 * Send admin notification + visitor confirmation via wp_mail.
	 *
	 * @return bool True if the admin notification sent (or was disabled).
	 */
	private function send_emails( $lead ) {
		$recipient = spcore_get( 'recipient_email' );
		$from_name = spcore_get( 'from_name' );
		$from_mail = spcore_get( 'from_email' );
		if ( ! is_email( $from_mail ) ) {
			$from_mail = 'wordpress@' . wp_parse_url( home_url(), PHP_URL_HOST );
		}

		$headers = array(
			'Content-Type: text/html; charset=UTF-8',
			sprintf( 'From: %s <%s>', $from_name, $from_mail ),
			sprintf( 'Reply-To: %s <%s>', $lead['name'], $lead['email'] ),
		);

		$admin_ok = true;

		if ( spcore_get( 'send_admin', 1 ) ) {
			$subject = sprintf( '[Saddam Hossen] %s ,  %s', $lead['name'], $lead['inquiry_type'] ? $lead['inquiry_type'] : 'Inquiry' );
			$body    = $this->admin_email_body( $lead );
			$admin_ok = wp_mail( $recipient, $subject, $body, $headers );
		}

		if ( spcore_get( 'send_visitor', 1 ) && is_email( $lead['email'] ) ) {
			$vsubject = sprintf( __( 'Thanks for reaching out, %s', 'spcore' ), $lead['name'] );
			$vbody    = $this->visitor_email_body( $lead );
			$vheaders = array(
				'Content-Type: text/html; charset=UTF-8',
				sprintf( 'From: %s <%s>', $from_name, $from_mail ),
				sprintf( 'Reply-To: %s <%s>', spcore_get( 'name' ), $recipient ),
			);
			wp_mail( $lead['email'], $vsubject, $vbody, $vheaders );
		}

		return (bool) $admin_ok;
	}

	private function email_shell( $title, $intro, $content ) {
		$name = spcore_get( 'name', 'Saddam Hossen' );
		return sprintf(
			'<!doctype html><html><body style="margin:0;background:#f6f7fb;padding:24px;font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Arial,sans-serif;color:#111827;">
			<table role="presentation" width="100%%" cellspacing="0" cellpadding="0" style="max-width:680px;margin:0 auto;background:#ffffff;border:1px solid #e5e7eb;border-radius:18px;overflow:hidden;box-shadow:0 10px 30px rgba(15,23,42,.06);">
			<tr><td style="background:#070a10;color:#ffffff;padding:24px 28px;"><div style="font-size:13px;color:#38bdf8;text-transform:uppercase;letter-spacing:.08em;font-weight:700;">%4$s</div><h1 style="font-size:24px;line-height:1.1;margin:8px 0 0;">%1$s</h1></td></tr>
			<tr><td style="padding:24px 28px;"><p style="font-size:15px;line-height:1.6;color:#4b5563;margin:0 0 18px;">%2$s</p>%3$s</td></tr>
			<tr><td style="background:#f8fafc;border-top:1px solid #e5e7eb;padding:16px 28px;font-size:12px;color:#6b7280;">Sent from %4$s · <a style="color:#2563eb" href="%5$s">%5$s</a></td></tr>
			</table></body></html>',
			esc_html( $title ),
			esc_html( $intro ),
			$content,
			esc_html( $name ),
			esc_url( home_url() )
		);
	}

	private function admin_email_body( $lead ) {
		$rows = '';
		$map  = array(
			'Name'         => $lead['name'],
			'Email'        => $lead['email'],
			'Company'      => $lead['company'],
			'Website'      => $lead['website'],
			'Inquiry Type' => $lead['inquiry_type'],
			'Budget'       => $lead['budget'],
			'Source Page'  => $lead['source_page'],
			'UTM Source'   => $lead['utm_source'],
			'UTM Medium'   => $lead['utm_medium'],
			'UTM Campaign' => $lead['utm_campaign'],
			'Referrer'     => $lead['referrer'],
			'IP'           => $lead['ip'],
		);
		foreach ( $map as $label => $val ) {
			if ( '' === $val ) {
				continue;
			}
			$rows .= sprintf(
				'<tr><td style="padding:10px 12px;border-bottom:1px solid #eef2f7;font-weight:700;color:#111827;width:160px;">%s</td><td style="padding:10px 12px;border-bottom:1px solid #eef2f7;color:#374151;">%s</td></tr>',
				esc_html( $label ),
				esc_html( $val )
			);
		}
		$content = sprintf(
			'<table role="presentation" width="100%%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;border:1px solid #eef2f7;border-radius:12px;overflow:hidden;margin-bottom:20px;">%s</table>
			<h2 style="font-size:16px;margin:0 0 8px;color:#111827;">Message</h2>
			<div style="white-space:pre-wrap;background:#f8fafc;border:1px solid #e5e7eb;border-radius:12px;padding:14px 16px;color:#374151;line-height:1.6;">%s</div>
			<p style="margin:18px 0 0;"><a href="mailto:%s" style="display:inline-block;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:999px;padding:10px 16px;font-weight:700;">Reply to lead</a></p>',
			$rows,
			esc_html( $lead['message'] ),
			esc_attr( $lead['email'] )
		);
		return $this->email_shell( 'New website inquiry', 'A new inquiry was submitted from your website. Review the details and reply while the context is fresh.', $content );
	}

	private function visitor_email_body( $lead ) {
		$name  = spcore_get( 'name', 'Saddam Hossen' );
		$reply = spcore_get( 'recipient_email', 'saddam.wp007@gmail.com' );
		$content = sprintf(
			'<p style="margin:0 0 14px;">Hi %s,</p>
			<p style="margin:0 0 14px;">Thanks for reaching out. I received your message and will review it personally. I will review the context and reply as soon as possible.</p>
			<h2 style="font-size:16px;margin:20px 0 8px;color:#111827;">Your message</h2>
			<div style="white-space:pre-wrap;background:#f8fafc;border:1px solid #e5e7eb;border-radius:12px;padding:14px 16px;color:#374151;line-height:1.6;">%s</div>
			<p style="margin:18px 0 0;">If it is urgent, reply to this email or write directly to <a style="color:#2563eb" href="mailto:%s">%s</a>.</p>
			<p style="margin:18px 0 0;">Best,<br><strong>%s</strong></p>',
			esc_html( $lead['name'] ),
			esc_html( $lead['message'] ),
			esc_attr( $reply ),
			esc_html( $reply ),
			esc_html( $name )
		);
		return $this->email_shell( 'Message received', 'This is an automatic confirmation from my website contact system.', $content );
	}

	/**
	 * Fire the automation webhook if configured.
	 */
	private function maybe_webhook( $lead ) {
		if ( ! spcore_get( 'send_to_webhook', 0 ) ) {
			return;
		}
		$url = spcore_get( 'webhook_url' );
		if ( ! $url ) {
			return;
		}
		$payload = wp_json_encode( array( 'type' => 'lead', 'data' => $lead, 'ts' => time() ) );
		$headers = array( 'Content-Type' => 'application/json' );
		$secret  = spcore_get( 'webhook_secret' );
		if ( $secret ) {
			$headers['X-SPCore-Signature'] = hash_hmac( 'sha256', $payload, $secret );
		}
		$res = wp_safe_remote_post(
			$url,
			array(
				'timeout'  => 8,
				'headers'  => $headers,
				'body'     => $payload,
				'blocking' => false,
			)
		);
		if ( is_wp_error( $res ) ) {
			$this->log( 'Webhook error: ' . $res->get_error_message() );
		}
	}

	/**
	 * Verify Turnstile or reCAPTCHA only if keys are present.
	 *
	 * @return true|WP_Error
	 */
	private function verify_captcha( $data ) {
		// Cloudflare Turnstile.
		$ts_secret = spcore_get( 'turnstile_secret' );
		if ( $ts_secret ) {
			$token = isset( $data['cf-turnstile-response'] ) ? sanitize_text_field( $data['cf-turnstile-response'] ) : '';
			if ( ! $token ) {
				return new WP_Error( 'captcha', __( 'Please complete the verification.', 'spcore' ) );
			}
			$resp = wp_remote_post(
				'https://challenges.cloudflare.com/turnstile/v0/siteverify',
				array( 'body' => array( 'secret' => $ts_secret, 'response' => $token, 'remoteip' => $this->ip() ), 'timeout' => 8 )
			);
			if ( is_wp_error( $resp ) ) {
				return new WP_Error( 'captcha_unavailable', __( 'Verification is temporarily unavailable. Please try again or use email.', 'spcore' ) );
			}
			$json = json_decode( wp_remote_retrieve_body( $resp ), true );
			if ( empty( $json['success'] ) ) {
				return new WP_Error( 'captcha', __( 'Verification failed. Please try again.', 'spcore' ) );
			}
			return true;
		}

		// Google reCAPTCHA v3.
		$rc_secret = spcore_get( 'recaptcha_secret' );
		if ( $rc_secret ) {
			$token = isset( $data['g-recaptcha-response'] ) ? sanitize_text_field( $data['g-recaptcha-response'] ) : '';
			if ( ! $token ) {
				return new WP_Error( 'captcha', __( 'Please complete the verification.', 'spcore' ) );
			}
			$resp = wp_remote_post(
				'https://www.google.com/recaptcha/api/siteverify',
				array( 'body' => array( 'secret' => $rc_secret, 'response' => $token, 'remoteip' => $this->ip() ), 'timeout' => 8 )
			);
			if ( is_wp_error( $resp ) ) {
				return new WP_Error( 'captcha_unavailable', __( 'Verification is temporarily unavailable. Please try again or use email.', 'spcore' ) );
			}
			$json = json_decode( wp_remote_retrieve_body( $resp ), true );
			if ( empty( $json['success'] ) || ( isset( $json['score'] ) && $json['score'] < 0.4 ) || ( isset( $json['action'] ) && 'contact' !== $json['action'] ) ) {
				return new WP_Error( 'captcha', __( 'Verification failed. Please try again.', 'spcore' ) );
			}
			return true;
		}

		return true;
	}

	/**
	 * Enforce a small per-IP and per-email submission budget.
	 *
	 * @param string $email Sanitized email address.
	 * @return true|WP_Error
	 */
	private function enforce_rate_limit( $email ) {
		$identity = strtolower( trim( $email ) ) . '|' . $this->ip();
		$key      = 'spcore_rate_' . hash_hmac( 'sha256', $identity, wp_salt( 'nonce' ) );
		$count    = (int) get_transient( $key );

		if ( $count >= 3 ) {
			return new WP_Error( 'rate_limit', __( 'Too many submissions. Please wait 15 minutes or email me directly.', 'spcore' ) );
		}

		set_transient( $key, $count + 1, 15 * MINUTE_IN_SECONDS );
		return true;
	}


	/**
	 * Limit a string without requiring the mbstring extension.
	 *
	 * @param string $value  Value to limit.
	 * @param int    $length Maximum length.
	 * @return string
	 */
	private function limit( $value, $length ) {
		return function_exists( 'mb_substr' ) ? mb_substr( $value, 0, $length ) : substr( $value, 0, $length );
	}

	private function ip() {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		return $ip;
	}

	private function log( $msg ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( '[SPCore Contact] ' . $msg ); // phpcs:ignore
		}
	}

	/**
	 * AJAX endpoint.
	 */
	public function handle_ajax() {
		$data   = wp_unslash( $_POST ); // Sanitized field-by-field in process().
		$result = $this->process( $data );
		if ( $result['success'] ) {
			wp_send_json_success( $result );
		}
		wp_send_json_error( $result );
	}

	/**
	 * Classic POST fallback (no JS). Redirects back with a status flag.
	 */
	public function handle_post() {
		$data   = wp_unslash( $_POST );
		$result = $this->process( $data );
		$requested_back = isset( $data['source_page'] ) ? esc_url_raw( $data['source_page'] ) : '';
		$back           = wp_validate_redirect( $requested_back, home_url( '/contact/' ) );
		$args   = $result['success'] ? array( 'sp_sent' => '1' ) : array( 'sp_sent' => '0' );
		wp_safe_redirect( add_query_arg( $args, $back ) . '#contact' );
		exit;
	}
}
