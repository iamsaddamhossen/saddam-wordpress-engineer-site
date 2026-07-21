<?php
/**
 * Site Settings admin page (tabbed, no external deps).
 *
 * @package SaddamPortfolioCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPCore_Settings {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_init', array( $this, 'register' ) );
	}

	public function menu() {
		add_menu_page(
			__( 'Site Settings', 'spcore' ),
			__( 'Saddam Engineer', 'spcore' ),
			'manage_options',
			'spcore-settings',
			array( $this, 'render_page' ),
			'dashicons-superhero',
			3
		);
	}

	public function register() {
		register_setting(
			'spcore_settings_group',
			'spcore_settings',
			array( 'sanitize_callback' => array( $this, 'sanitize' ) )
		);
	}

	/**
	 * Field definitions per tab.
	 */
	private function fields() {
		return array(
			'home' => array(
				'label'  => 'Homepage',
				'fields' => array(
					'home_status'          => array( 'Availability line', 'text' ),
					'home_hero_title'      => array( 'Hero title', 'textarea' ),
					'home_hero_intro'      => array( 'Hero introduction', 'textarea' ),
					'home_primary_label'   => array( 'Primary CTA label', 'text' ),
					'home_secondary_label' => array( 'Secondary CTA label', 'text' ),
				),
			),
			'profile' => array(
				'label'  => 'Profile',
				'fields' => array(
					'name'         => array( 'Name', 'text' ),
					'headline'     => array( 'Headline', 'text' ),
					'bio'          => array( 'Short Bio', 'textarea' ),
					'location'     => array( 'Location', 'text' ),
					'timezone'     => array( 'Timezone', 'text' ),
					'email'        => array( 'Public Email', 'text' ),
					'phone'        => array( 'Phone (optional)', 'text' ),
					'whatsapp_number' => array( 'WhatsApp Number', 'text' ),
					'whatsapp_url'    => array( 'WhatsApp CTA URL', 'url' ),
					'photo_url'    => array( 'Profile Photo URL', 'url' ),
					'cv_url'       => array( 'CV Download URL', 'url' ),
				),
			),
			'trust' => array(
				'label'  => 'Trust Metrics',
				'fields' => array(
					'years_wording' => array( 'Years Wording', 'text' ),
					'upwork_status' => array( 'Upwork Status', 'text' ),
					'job_success'   => array( 'Job Success', 'text' ),
					'project_count' => array( 'Project Count', 'text' ),
					'plugin_count'  => array( 'WordPress.org Plugin Note', 'text' ),
					'github_url'    => array( 'GitHub URL', 'url' ),
					'upwork_url'    => array( 'Upwork URL', 'url' ),
					'linkedin_url'  => array( 'LinkedIn URL', 'url' ),
					'binarylab_url' => array( '99BinaryLab URL', 'url' ),
				),
			),
			'seo' => array(
				'label'  => 'SEO',
				'fields' => array(
					'meta_title'        => array( 'Default Meta Title', 'text' ),
					'meta_description'  => array( 'Default Meta Description', 'textarea' ),
					'social_image'      => array( 'Social Share Image URL', 'url' ),
					'ga4_id'            => array( 'GA4 Measurement ID (G-XXXX)', 'text' ),
					'gsc_verification'  => array( 'Google Search Console verification', 'text' ),
					'bing_verification' => array( 'Bing verification', 'text' ),
				),
			),
			'email' => array(
				'label'  => 'Email',
				'fields' => array(
					'recipient_email' => array( 'Recipient Email', 'text' ),
					'from_name'       => array( 'From Name', 'text' ),
					'from_email'      => array( 'From Email (domain email recommended)', 'text' ),
					'send_admin'      => array( 'Send admin notification', 'checkbox' ),
					'send_visitor'    => array( 'Send visitor confirmation', 'checkbox' ),
				),
			),
			'privacy' => array(
				'label'  => 'Privacy',
				'fields' => array(
					'store_ip'            => array( 'Store visitor IP with leads', 'checkbox' ),
					'lead_retention_days' => array( 'Lead retention days (0 disables automatic deletion)', 'text' ),
				),
			),
			'antispam' => array(
				'label'  => 'Anti-Spam',
				'fields' => array(
					'turnstile_site'   => array( 'Cloudflare Turnstile Site Key', 'text' ),
					'turnstile_secret' => array( 'Cloudflare Turnstile Secret Key', 'text' ),
					'recaptcha_site'   => array( 'reCAPTCHA v3 Site Key', 'text' ),
					'recaptcha_secret' => array( 'reCAPTCHA v3 Secret Key', 'text' ),
				),
			),
			'automation' => array(
				'label'  => 'Automation',
				'fields' => array(
					'webhook_url'     => array( 'Webhook URL (CRM / Sheets / Make / Zapier)', 'url' ),
					'webhook_secret'  => array( 'Webhook Secret (sent as X-SPCore-Signature)', 'text' ),
					'send_to_webhook' => array( 'Send leads to webhook', 'checkbox' ),
					'ga4_events'      => array( 'Enable GA4 event tracking', 'checkbox' ),
				),
			),
			'affiliate' => array(
				'label'  => 'Affiliate',
				'fields' => array(
					'affiliate_disclosure' => array( 'Global Affiliate Disclosure', 'textarea' ),
					'enable_tools'         => array( 'Enable Tools / Resources page', 'checkbox' ),
					'affiliate_nofollow'   => array( 'Add rel="sponsored nofollow" to affiliate links', 'checkbox' ),
				),
			),
			'conversion' => array(
				'label'  => 'Conversion',
				'fields' => array(
					'announce_enable' => array( 'Enable announcement bar', 'checkbox' ),
					'announce_text' => array( 'Announcement text', 'textarea' ),
					'announce_cta_label' => array( 'Announcement CTA label', 'text' ),
					'announce_cta_url' => array( 'Announcement CTA URL', 'text' ),
					'modal_enable' => array( 'Enable lead modal', 'checkbox' ),
					'modal_delay' => array( 'Modal delay in ms', 'text' ),
					'modal_scroll' => array( 'Modal scroll percent', 'text' ),
					'modal_headline' => array( 'Modal headline', 'text' ),
					'modal_body' => array( 'Modal body', 'textarea' ),
					'modal_cta_label' => array( 'Modal CTA label', 'text' ),
					'modal_cta_url' => array( 'Modal CTA URL', 'text' ),
					'modal_frequency_days' => array( 'Frequency days', 'text' ),
					'mobile_bar_enable' => array( 'Enable mobile sticky bar', 'checkbox' ),
				),
			),
			'chat' => array(
				'label'  => 'Assistant',
				'fields' => array(
					'chat_enable' => array( 'Enable guided assistant', 'checkbox' ),
					'chat_mode' => array( 'Mode (guided / ai)', 'text' ),
					'chat_welcome' => array( 'Welcome message', 'textarea' ),
					'chat_webhook_url' => array( 'Optional assistant webhook URL', 'url' ),
					'chat_api_provider' => array( 'AI provider (openai / webhook)', 'text' ),
					'chat_api_model' => array( 'OpenAI model', 'text' ),
					'chat_api_key' => array( 'OpenAI API key (stored server-side)', 'password' ),
					'chat_privacy_note' => array( 'Assistant privacy note', 'textarea' ),
				),
			),
			'blog' => array(
				'label'  => 'Blog',
				'fields' => array(
					'comments_disable' => array( 'Disable comments globally', 'checkbox' ),
				),
			),
		);
	}

	/**
	 * Merge-on-save sanitize.
	 *
	 * v2 rebuilt the whole option from defaults on every save, so saving one
	 * tab wiped every checkbox (and any value) that lived on a *different*
	 * tab ,  a real data-loss bug. v3 starts from the existing option and only
	 * updates the fields that belong to the tab actually being submitted
	 * (identified by the hidden `spcore_active_tab` field). Checkboxes are
	 * then correctly resolved to 0/1 only within that tab, never touching the
	 * others. Result: you can save any tab in any order and nothing is lost.
	 */
	public function sanitize( $input ) {
		$tabs     = $this->fields();
		$defaults = spcore_default_settings();
		// Existing stored settings (fall back to defaults on first save).
		$existing = get_option( 'spcore_settings', array() );
		$clean    = wp_parse_args( is_array( $existing ) ? $existing : array(), $defaults );

		// Which tab is being saved? Fall back to sanitising every submitted key.
		$active = isset( $input['spcore_active_tab'] ) ? sanitize_key( $input['spcore_active_tab'] ) : '';
		$scope  = ( $active && isset( $tabs[ $active ] ) ) ? array( $active => $tabs[ $active ] ) : $tabs;

		foreach ( $scope as $tab ) {
			foreach ( $tab['fields'] as $key => $conf ) {
				$type = $conf[1];
				if ( 'checkbox' === $type ) {
					$clean[ $key ] = ! empty( $input[ $key ] ) ? 1 : 0;
					continue;
				}
				if ( ! isset( $input[ $key ] ) ) {
					continue; // Leave the stored value untouched.
				}
				$raw = wp_unslash( $input[ $key ] );
				if ( 'password' === $type && '' === trim( (string) $raw ) ) {
					continue; // A blank password field preserves the existing secret.
				}
				switch ( $type ) {
					case 'url':
						$clean[ $key ] = esc_url_raw( $raw );
						break;
					case 'textarea':
						$clean[ $key ] = sanitize_textarea_field( $raw );
						break;
					default:
						$clean[ $key ] = sanitize_text_field( $raw );
				}
			}
		}
		return $clean;
	}

	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$settings = get_option( 'spcore_settings', spcore_default_settings() );
		$tabs     = $this->fields();
		$active   = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'profile';
		if ( ! isset( $tabs[ $active ] ) ) {
			$active = 'profile';
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Site Settings', 'spcore' ); ?></h1>
			<h2 class="nav-tab-wrapper">
				<?php foreach ( $tabs as $slug => $tab ) : ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=spcore-settings&tab=' . $slug ) ); ?>"
						class="nav-tab <?php echo $active === $slug ? 'nav-tab-active' : ''; ?>">
						<?php echo esc_html( $tab['label'] ); ?>
					</a>
				<?php endforeach; ?>
			</h2>

			<form method="post" action="options.php">
				<?php settings_fields( 'spcore_settings_group' ); ?>
				<input type="hidden" name="spcore_settings[spcore_active_tab]" value="<?php echo esc_attr( $active ); ?>" />
				<table class="form-table" role="presentation">
					<?php
					foreach ( $tabs[ $active ]['fields'] as $key => $conf ) {
						$label = $conf[0];
						$type  = $conf[1];
						$value = isset( $settings[ $key ] ) ? $settings[ $key ] : '';
						echo '<tr><th scope="row"><label for="' . esc_attr( $key ) . '">' . esc_html( $label ) . '</label></th><td>';
						switch ( $type ) {
							case 'textarea':
								printf( '<textarea id="%1$s" name="spcore_settings[%1$s]" rows="4" class="large-text">%2$s</textarea>', esc_attr( $key ), esc_textarea( $value ) );
								break;
							case 'checkbox':
								printf( '<label><input type="checkbox" name="spcore_settings[%1$s]" value="1" %2$s /> Enabled</label>', esc_attr( $key ), checked( $value, 1, false ) );
								break;
							case 'password':
								printf( '<input type="password" id="%1$s" name="spcore_settings[%1$s]" value="" class="regular-text" autocomplete="new-password" placeholder="%2$s" />', esc_attr( $key ), esc_attr( $value ? __( 'Saved ,  leave blank to keep', 'spcore' ) : __( 'Enter key', 'spcore' ) ) );
								break;
							case 'url':
								printf( '<input type="url" id="%1$s" name="spcore_settings[%1$s]" value="%2$s" class="regular-text" placeholder="https://" />', esc_attr( $key ), esc_attr( $value ) );
								break;
							default:
								printf( '<input type="text" id="%1$s" name="spcore_settings[%1$s]" value="%2$s" class="regular-text" />', esc_attr( $key ), esc_attr( $value ) );
						}
						echo '</td></tr>';
					}
					?>
				</table>
				<p class="description">
					<?php esc_html_e( 'Each tab saves independently ,  your settings on the other tabs are preserved automatically.', 'spcore' ); ?>
				</p>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
