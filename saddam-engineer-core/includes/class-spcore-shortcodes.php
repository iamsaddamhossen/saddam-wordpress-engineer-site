<?php
/**
 * Shortcodes for rendering engineering content and the contact form.
 *
 * @package SaddamPortfolioCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPCore_Shortcodes {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_shortcode( 'spcore_contact', array( $this, 'contact' ) );
		add_shortcode( 'spcore_projects', array( $this, 'projects' ) );
		add_shortcode( 'spcore_case_studies', array( $this, 'case_studies' ) );
		add_shortcode( 'spcore_featured_work', array( $this, 'featured_work' ) );
		add_shortcode( 'spcore_services', array( $this, 'services' ) );
		add_shortcode( 'spcore_testimonials', array( $this, 'testimonials' ) );
		add_shortcode( 'spcore_tools', array( $this, 'tools' ) );
		add_shortcode( 'spcore_trust_row', array( $this, 'trust_row' ) );
		add_shortcode( 'spcore_highlights', array( $this, 'highlights' ) );
	}

	/**
	 * Apple-style "Get the highlights" capability grid. Static positioning copy
	 * (rarely changes); editable here in one place and reusable on any page.
	 */
	public function highlights( $atts ) {
		$items = array(
			array( 'plugin', 'Plugin architecture', 'OOP PHP, hooks, registered metadata, custom admin workflows and maintainable boundaries.' ),
			array( 'blocks', 'Gutenberg engineering', 'Dynamic block registration with block.json, editor controls and server-side rendering.' ),
			array( 'api', 'REST APIs & integrations', 'Versioned routes, schemas, validation, permissions, caching and signed webhooks.' ),
			array( 'speed', 'Performance & Core Web Vitals', 'Diagnosis-first speed work at the code and infrastructure level.' ),
			array( 'security', 'Security & malware cleanup', 'Evidence-first, defensive cleanup and hardening ,  backups before deletion.' ),
			array( 'search', 'Technical SEO / AEO', 'Schema, clean markup, sitemaps and AI-search readiness.' ),
			array( 'terminal', 'WP-CLI & automation', 'Repeatable operational commands, scheduled maintenance and CI quality gates.' ),
			array( 'access', 'Accessible interfaces', 'Semantic HTML, keyboard behavior, reduced-motion support and readable states.' ),
		);
		ob_start();
		echo '<div class="sp-highlights" data-anim="stagger">';
		foreach ( $items as $it ) {
			echo '<article class="sp-highlight">';
			echo '<span class="sp-highlight-icon sp-highlight-icon-' . esc_attr( $it[0] ) . '" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5 6h14v12H5z"/><path d="M8 9h8M8 12h5M8 15h7"/></svg></span>';
			echo '<h3>' . esc_html( $it[1] ) . '</h3>';
			echo '<p>' . esc_html( $it[2] ) . '</p>';
			echo '</article>';
		}
		echo '</div>';
		return ob_get_clean();
	}

	/**
	 * The production contact form.
	 */
	public function contact( $atts ) {
		$sent      = isset( $_GET['sp_sent'] ) ? sanitize_text_field( wp_unslash( $_GET['sp_sent'] ) ) : '';
		$recipient = spcore_get( 'recipient_email' );
		$ts_site   = spcore_get( 'turnstile_site' );
		$rc_site   = spcore_get( 'recaptcha_site' );

		ob_start();
		?>
		<div class="sp-contact" id="contact">
			<?php if ( '1' === $sent ) : ?>
				<div class="sp-alert sp-alert-ok" role="status"><?php esc_html_e( 'Thank you ,  your message has been sent. I will review it as soon as possible.', 'spcore' ); ?></div>
			<?php elseif ( '0' === $sent ) : ?>
				<div class="sp-alert sp-alert-err" role="alert">
					<?php
					echo wp_kses_post(
						sprintf(
							/* translators: %s: linked email address. */
							__( 'Something went wrong. Please email me directly at %s.', 'spcore' ),
							'<a href="mailto:' . esc_attr( antispambot( $recipient ) ) . '">' . esc_html( antispambot( $recipient ) ) . '</a>'
						)
					);
					?>
				</div>
			<?php endif; ?>

			<form class="sp-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
				<input type="hidden" name="action" value="spcore_contact" />
				<input type="hidden" name="spcore_nonce" value="<?php echo esc_attr( wp_create_nonce( 'spcore_contact_action' ) ); ?>" />
				<input type="hidden" name="spcore_ts" value="<?php echo esc_attr( time() ); ?>" />
				<input type="hidden" name="source_page" value="<?php echo esc_url( home_url( add_query_arg( array(), '' ) ) ); ?>" />
				<input type="hidden" name="utm_source" value="" data-utm="utm_source" />
				<input type="hidden" name="utm_medium" value="" data-utm="utm_medium" />
				<input type="hidden" name="utm_campaign" value="" data-utm="utm_campaign" />
				<input type="hidden" name="referrer" value="" data-referrer="1" />

				<!-- Honeypot: hidden from humans -->
				<div class="sp-hp" aria-hidden="true" style="position:absolute;left:-9999px;top:-9999px">
					<label>Website<input type="text" name="spcore_website_hp" tabindex="-1" autocomplete="off" /></label>
				</div>

				<div class="sp-grid-2">
					<p class="sp-field">
						<label for="sp-name"><?php esc_html_e( 'Name', 'spcore' ); ?> *</label>
						<input type="text" id="sp-name" name="name" required />
					</p>
					<p class="sp-field">
						<label for="sp-email"><?php esc_html_e( 'Email', 'spcore' ); ?> *</label>
						<input type="email" id="sp-email" name="email" required />
					</p>
				</div>
				<div class="sp-grid-2">
					<p class="sp-field">
						<label for="sp-company"><?php esc_html_e( 'Company', 'spcore' ); ?></label>
						<input type="text" id="sp-company" name="company" />
					</p>
					<p class="sp-field">
						<label for="sp-website"><?php esc_html_e( 'Website', 'spcore' ); ?></label>
						<input type="url" id="sp-website" name="website" placeholder="https://" />
					</p>
				</div>
				<div class="sp-grid-2">
					<p class="sp-field">
						<label for="sp-type"><?php esc_html_e( 'Inquiry Type', 'spcore' ); ?></label>
						<select id="sp-type" name="inquiry_type">
							<option value="Hiring / Full-time"><?php esc_html_e( 'Full-time remote role', 'spcore' ); ?></option>
							<option value="Long-term contract"><?php esc_html_e( 'Long-term contract', 'spcore' ); ?></option>
							<option value="Technical assessment"><?php esc_html_e( 'Technical assessment', 'spcore' ); ?></option>
							<option value="Custom Plugin"><?php esc_html_e( 'Custom plugin project', 'spcore' ); ?></option>
							<option value="WooCommerce"><?php esc_html_e( 'WooCommerce', 'spcore' ); ?></option>
							<option value="Booking / API System"><?php esc_html_e( 'Booking / API System', 'spcore' ); ?></option>
							<option value="Performance"><?php esc_html_e( 'Performance', 'spcore' ); ?></option>
							<option value="Security / Malware Cleanup"><?php esc_html_e( 'Security / Malware Cleanup', 'spcore' ); ?></option>
							<option value="Monthly Support"><?php esc_html_e( 'Monthly Support', 'spcore' ); ?></option>
							<option value="Other"><?php esc_html_e( 'Other', 'spcore' ); ?></option>
						</select>
					</p>
					<p class="sp-field">
						<label for="sp-budget"><?php esc_html_e( 'Engagement', 'spcore' ); ?></label>
						<select id="sp-budget" name="budget">
							<option value="Full-time role"><?php esc_html_e( 'Full-time role', 'spcore' ); ?></option>
							<option value="Long-term contract"><?php esc_html_e( 'Long-term contract', 'spcore' ); ?></option>
							<option value="Part-time / trial"><?php esc_html_e( 'Part-time or paid trial', 'spcore' ); ?></option>
							<option value="Defined project"><?php esc_html_e( 'Defined project', 'spcore' ); ?></option>
							<option value="Prefer not to say"><?php esc_html_e( 'Prefer not to say', 'spcore' ); ?></option>
						</select>
					</p>
				</div>
				<p class="sp-field">
					<label for="sp-message"><?php esc_html_e( 'Role or project details', 'spcore' ); ?> *</label>
					<textarea id="sp-message" name="message" rows="5" required></textarea>
				</p>

				<?php if ( $ts_site ) : ?>
					<div class="cf-turnstile" data-sitekey="<?php echo esc_attr( $ts_site ); ?>"></div>
					<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
				<?php elseif ( $rc_site ) : ?>
					<input type="hidden" name="g-recaptcha-response" id="sp-recaptcha" />
					<script src="https://www.google.com/recaptcha/api.js?render=<?php echo esc_attr( $rc_site ); ?>"></script>
					<script>
					grecaptcha && grecaptcha.ready(function(){
						grecaptcha.execute('<?php echo esc_js( $rc_site ); ?>', {action:'contact'}).then(function(t){
							var f=document.getElementById('sp-recaptcha'); if(f) f.value=t;
						});
					});
					</script>
				<?php endif; ?>

				<div class="sp-form-actions">
					<button type="submit" class="sp-btn sp-btn-primary" data-sp-event="contact_submit"><?php esc_html_e( 'Send Role / Project Details', 'spcore' ); ?></button>
					<span class="sp-fallback"><?php esc_html_e( 'Prefer email?', 'spcore' ); ?> <a href="mailto:<?php echo esc_attr( $recipient ); ?>"><?php echo esc_html( $recipient ); ?></a></span>
				</div>
				<p class="sp-form-privacy"><?php esc_html_e( 'Your submission is stored only to respond to this inquiry and can be exported or erased through WordPress privacy tools.', 'spcore' ); ?> <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy policy', 'spcore' ); ?></a>.</p>
				<div class="sp-form-msg" role="status" aria-live="polite"></div>
			</form>
		</div>
		<?php
		return ob_get_clean();
	}

	public function projects( $atts ) {
		$atts = shortcode_atts( array( 'featured' => '0', 'count' => '12' ), $atts );
		$args = array(
			'post_type'      => 'sp_project',
			'posts_per_page' => intval( $atts['count'] ),
			'orderby'        => 'menu_order date',
			'order'          => 'ASC',
		);
		if ( '1' === $atts['featured'] ) {
			$args['meta_key']   = '_spcore_featured';
			$args['meta_value'] = '1';
		}
		$q = new WP_Query( $args );
		if ( ! $q->have_posts() ) {
			return '';
		}
		ob_start();
		echo '<div class="sp-project-grid">';
		while ( $q->have_posts() ) {
			$q->the_post();
			$live = spcore_meta( 'live_url' );
			$nda  = spcore_meta( 'nda' );
			$cat  = spcore_meta( 'category_label' );
			$stack = spcore_meta( 'tech_stack' );
			echo '<article class="sp-card">';
			if ( has_post_thumbnail() ) {
				echo '<a class="sp-card-thumb" href="' . esc_url( get_permalink() ) . '">' . get_the_post_thumbnail( get_the_ID(), 'large', array( 'loading' => 'lazy' ) ) . '</a>';
			}
			echo '<div class="sp-card-body">';
			if ( $cat ) {
				echo '<span class="sp-tag">' . esc_html( $cat ) . '</span>';
			}
			echo '<h3><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h3>';
			echo '<p>' . esc_html( spcore_meta( 'summary' ) ) . '</p>';
			$result = spcore_meta( 'result' );
			if ( $result ) {
				echo '<p class="sp-card-result">' . esc_html( $result ) . '</p>';
			}
			if ( $stack ) {
				echo '<p class="sp-stack">' . esc_html( $stack ) . '</p>';
			}
			echo '<div class="sp-card-links">';
			echo '<a class="sp-link" href="' . esc_url( get_permalink() ) . '">' . esc_html__( 'View details', 'spcore' ) . '</a>';
			if ( $live && ! $nda ) {
				echo ' <a class="sp-link sp-link-ext" href="' . esc_url( $live ) . '" target="_blank" rel="noopener" data-sp-event="project_link_click" data-sp-label="' . esc_attr( get_the_title() ) . '">' . esc_html__( 'Live site ', 'spcore' ) . '</a>';
			} elseif ( $nda ) {
				echo ' <span class="sp-link sp-link-muted">' . esc_html__( 'Private / NDA', 'spcore' ) . '</span>';
			}
			echo '</div></div></article>';
		}
		echo '</div>';
		wp_reset_postdata();
		return ob_get_clean();
	}

	public function case_studies( $atts ) {
		$atts  = shortcode_atts( array( 'limit' => 6 ), $atts, 'spcore_case_studies' );
		$limit = max( 1, min( 12, absint( $atts['limit'] ) ) );
		$q     = new WP_Query( array( 'post_type' => 'sp_case_study', 'posts_per_page' => $limit, 'orderby' => 'menu_order date', 'order' => 'ASC' ) );
		if ( ! $q->have_posts() ) {
			return '';
		}
		ob_start();
		echo '<div class="sp-case-grid">';
		while ( $q->have_posts() ) {
			$q->the_post();
			echo '<article class="sp-case-card">';
			$metric = spcore_meta( 'proof_metric' );
			if ( $metric ) {
				echo '<div class="sp-metric">' . esc_html( $metric ) . '</div>';
			}
			echo '<h3><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h3>';
			echo '<p>' . esc_html( spcore_meta( 'challenge' ) ) . '</p>';
			echo '<a class="sp-link" href="' . esc_url( get_permalink() ) . '">' . esc_html__( 'Read case study', 'spcore' ) . '</a>';
			echo '</article>';
		}
		echo '</div>';
		wp_reset_postdata();
		return ob_get_clean();
	}

	public function services( $atts ) {
		$atts = shortcode_atts( array( 'count' => '-1' ), $atts );
		$q    = new WP_Query( array( 'post_type' => 'sp_service', 'posts_per_page' => intval( $atts['count'] ), 'orderby' => 'menu_order title', 'order' => 'ASC' ) );
		if ( ! $q->have_posts() ) {
			return '';
		}
		ob_start();
		echo '<div class="sp-service-grid">';
		while ( $q->have_posts() ) {
			$q->the_post();
			$icon  = spcore_meta( 'icon' );
			$cta_l = spcore_meta( 'cta_label' );
			echo '<article class="sp-service-card">';
			if ( $icon ) {
				echo '<div class="sp-service-icon" aria-hidden="true">' . esc_html( $icon ) . '</div>';
			}
			echo '<h3><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h3>';
			echo '<p class="sp-svc-desc">' . esc_html( spcore_meta( 'short_desc' ) ) . '</p>';
			// Up to 3 deliverables as a short checklist (from the Deliverables field, one per line).
			$deliver = array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) spcore_meta( 'deliverables' ) ) ) ) );
			if ( $deliver ) {
				echo '<ul class="sp-svc-list">';
				foreach ( array_slice( $deliver, 0, 3 ) as $d ) {
					echo '<li>' . esc_html( $d ) . '</li>';
				}
				echo '</ul>';
			}
			echo '<a class="sp-link sp-link-arrow" href="' . esc_url( get_permalink() ) . '">' . esc_html( $cta_l ? $cta_l : __( 'Learn more', 'spcore' ) ) . ' </a>';
			echo '</article>';
		}
		echo '</div>';
		wp_reset_postdata();
		return ob_get_clean();
	}

	/**
	 * Featured work: one featured case study in a 60/40 split card
	 * (content left, screenshot or compact metric panel right), then four
	 * compact cards each showing metric + problem + solution + result.
	 * No oversized empty image frames: the visual side only renders a real
	 * screenshot or a small metric panel ,  never a blank placeholder.
	 * Admin-driven: order via menu_order; smalls fill from case studies
	 * then Featured projects.
	 */
	public function featured_work( $atts ) {
		$cases = get_posts( array( 'post_type' => 'sp_case_study', 'numberposts' => 5, 'orderby' => 'menu_order date', 'order' => 'ASC' ) );
		if ( empty( $cases ) ) {
			return '';
		}
		$featured = array_shift( $cases );
		// Fill the 4 small slots from remaining case studies, then Featured projects.
		$minis = $cases;
		if ( count( $minis ) < 4 ) {
			$projs = get_posts( array( 'post_type' => 'sp_project', 'numberposts' => 4 - count( $minis ), 'meta_key' => '_spcore_featured', 'meta_value' => '1', 'orderby' => 'menu_order date', 'order' => 'ASC' ) );
			$minis = array_merge( $minis, $projs );
		}
		$minis = array_slice( $minis, 0, 4 );

		$row = function ( $label, $text, $words ) {
			if ( ! $text ) {
				return '';
			}
			return '<p class="sp-work-row"><b>' . esc_html( $label ) . '</b> ' . esc_html( wp_trim_words( $text, $words ) ) . '</p>';
		};

		ob_start();

		// --- Featured 60/40 card ---
		$fid    = $featured->ID;
		$metric = get_post_meta( $fid, '_spcore_proof_metric', true );
		$thumb  = get_the_post_thumbnail( $fid, 'large', array( 'loading' => 'lazy', 'alt' => esc_attr( get_the_title( $featured ) ) ) );
		echo '<article class="sp-work-featured">';
		echo '<div class="sp-work-featured-body">';
		echo '<span class="sp-tag">' . esc_html__( 'Featured case study', 'spcore' ) . '</span>';
		echo '<h3><a href="' . esc_url( get_permalink( $featured ) ) . '">' . esc_html( get_the_title( $featured ) ) . '</a></h3>';
		echo '<div class="sp-work-rows">';
		echo $row( __( 'Problem', 'spcore' ), get_post_meta( $fid, '_spcore_challenge', true ), 22 );  // phpcs:ignore WordPress.Security.EscapeOutput
		echo $row( __( 'Solution', 'spcore' ), get_post_meta( $fid, '_spcore_solution', true ), 22 );  // phpcs:ignore WordPress.Security.EscapeOutput
		echo $row( __( 'Result', 'spcore' ), get_post_meta( $fid, '_spcore_result', true ), 22 );      // phpcs:ignore WordPress.Security.EscapeOutput
		echo '</div>';
		echo '<a class="sp-link sp-link-arrow" href="' . esc_url( get_permalink( $featured ) ) . '">' . esc_html__( 'Read the full case study', 'spcore' ) . ' </a>';
		echo '</div>';
		echo '<div class="sp-work-featured-visual">';
		echo '<span class="sp-work-chrome" aria-hidden="true"><i></i><i></i><i></i></span>';
		if ( $thumb ) {
			echo $thumb; // phpcs:ignore WordPress.Security.EscapeOutput -- WP-generated markup.
		} else {
			echo '<div class="sp-work-bigmetric"><b>' . esc_html( $metric ? $metric : get_the_title( $featured ) ) . '</b><span>' . esc_html__( 'measured result', 'spcore' ) . '</span></div>';
		}
		echo '</div>';
		echo '</article>';

		// --- Four compact cards ---
		if ( $minis ) {
			echo '<div class="sp-work-minis">';
			foreach ( $minis as $m ) {
				$mid     = $m->ID;
				$is_case = 'sp_case_study' === $m->post_type;
				$mmetric = $is_case ? get_post_meta( $mid, '_spcore_proof_metric', true ) : get_post_meta( $mid, '_spcore_result', true );
				echo '<article class="sp-work-mini">';
				if ( $mmetric ) {
					echo '<span class="sp-work-metric-pill">' . esc_html( $mmetric ) . '</span>';
				}
				echo '<h3><a href="' . esc_url( get_permalink( $m ) ) . '">' . esc_html( get_the_title( $m ) ) . '</a></h3>';
				if ( $is_case ) {
					echo $row( __( 'Problem', 'spcore' ), get_post_meta( $mid, '_spcore_challenge', true ), 12 );  // phpcs:ignore WordPress.Security.EscapeOutput
					echo $row( __( 'Solution', 'spcore' ), get_post_meta( $mid, '_spcore_solution', true ), 12 );  // phpcs:ignore WordPress.Security.EscapeOutput
					echo $row( __( 'Result', 'spcore' ), get_post_meta( $mid, '_spcore_result', true ), 12 );      // phpcs:ignore WordPress.Security.EscapeOutput
				} else {
					echo $row( __( 'Project', 'spcore' ), get_post_meta( $mid, '_spcore_summary', true ), 16 );    // phpcs:ignore WordPress.Security.EscapeOutput
				}
				echo '<a class="sp-link sp-link-arrow" href="' . esc_url( get_permalink( $m ) ) . '">' . ( $is_case ? esc_html__( 'Case study', 'spcore' ) : esc_html__( 'Project', 'spcore' ) ) . ' </a>';
				echo '</article>';
			}
			echo '</div>';
		}

		return ob_get_clean();
	}

	public function testimonials( $atts ) {
		$atts = shortcode_atts( array( 'count' => '-1' ), $atts );
		$q    = new WP_Query( array( 'post_type' => 'sp_testimonial', 'posts_per_page' => intval( $atts['count'] ), 'orderby' => 'menu_order date', 'order' => 'ASC' ) );
		if ( ! $q->have_posts() ) {
			return '';
		}
		ob_start();
		echo '<div class="sp-testi-grid">';
		while ( $q->have_posts() ) {
			$q->the_post();
			$name    = spcore_meta( 'client_name' );
			$initial = $name ? mb_strtoupper( mb_substr( $name, 0, 1 ) ) : '“';
			echo '<figure class="sp-testi">';
			echo '<blockquote>' . wp_kses_post( wpautop( get_the_content() ) ) . '</blockquote>';
			echo '<figcaption>';
			echo '<span class="sp-testi-ava" aria-hidden="true">' . esc_html( $initial ) . '</span>';
			echo '<span><strong>' . esc_html( $name ) . '</strong>';
			$plat = spcore_meta( 'platform' );
			$type = spcore_meta( 'project_type' );
			if ( $plat || $type ) {
				echo '<span>' . esc_html( trim( $type . ( $plat ? ' · ' . $plat : '' ), ' ·' ) ) . '</span>';
			}
			echo '</span></figcaption></figure>';
		}
		echo '</div>';
		wp_reset_postdata();
		return ob_get_clean();
	}

	public function tools( $atts ) {
		if ( ! spcore_get( 'enable_tools', 1 ) ) {
			return '';
		}
		$q = new WP_Query( array( 'post_type' => 'sp_tool', 'posts_per_page' => -1, 'orderby' => 'menu_order title', 'order' => 'ASC' ) );
		ob_start();
		echo '<p class="sp-disclosure">' . esc_html( spcore_get( 'affiliate_disclosure' ) ) . '</p>';
		if ( $q->have_posts() ) {
			echo '<div class="sp-tool-grid">';
			while ( $q->have_posts() ) {
				$q->the_post();
				$aff      = spcore_meta( 'affiliate_url' );
				$plain    = spcore_meta( 'plain_url' );
				$url      = $aff ? $aff : $plain;
				$is_aff   = (bool) $aff;
				$best_for = spcore_meta( 'best_for' );
				$cta      = spcore_meta( 'cta_label' );
				$icon = '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M8.5 3.5h7l1 3 3 1v7l-3 1-1 3h-7l-1-3-3-1v-7l3-1 1-3Z"/><circle cx="12" cy="11" r="2.7"/></svg>';
				echo '<article class="sp-tool-card">';
				echo '<div class="sp-tool-card-top"><span class="sp-tool-icon">' . $icon . '</span><span class="sp-tool-index">' . esc_html( str_pad( (string) $q->current_post + 1, 2, '0', STR_PAD_LEFT ) ) . '</span></div>';
				echo '<span class="sp-tag">' . esc_html( spcore_meta( 'category_label' ) ) . '</span>';
				echo '<h3>' . esc_html( get_the_title() ) . '</h3>';
				echo '<p>' . esc_html( spcore_meta( 'description' ) ) . '</p>';
				if ( $best_for ) {
					echo '<p class="sp-tool-best"><strong>' . esc_html__( 'Best for', 'spcore' ) . '</strong><span>' . esc_html( $best_for ) . '</span></p>';
				}
				$why = spcore_meta( 'why' );
				if ( $why ) {
					echo '<p class="sp-why"><strong>' . esc_html__( 'Why it is considered:', 'spcore' ) . '</strong> ' . esc_html( $why ) . '</p>';
				}
				if ( $url ) {
					$rel = $is_aff && spcore_get( 'affiliate_nofollow', 1 ) ? 'sponsored nofollow noopener' : 'noopener';
					printf(
						'<a class="sp-tool-cta" href="%s" target="_blank" rel="%s" %s data-sp-event="affiliate_link_click" data-sp-label="%s"><span>%s</span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 16 16 8M9 8h7v7"/></svg></a>',
						esc_url( $url ),
						esc_attr( $rel ),
						$is_aff ? 'data-affiliate="1"' : '',
						esc_attr( get_the_title() ),
						esc_html( $cta ? $cta : __( 'Visit official website', 'spcore' ) )
					);
				}
				echo '</article>';
			}
			echo '</div>';
			wp_reset_postdata();
		}
		return ob_get_clean();
	}

	public function trust_row( $atts ) {
		$items = array_filter(
			array(
				spcore_get( 'years_wording' ),
				spcore_get( 'upwork_status' ),
				spcore_get( 'job_success' ),
				spcore_get( 'project_count' ) ? spcore_get( 'project_count' ) . ' Projects' : '',
				spcore_get( 'plugin_count' ),
				'Founder of 99BinaryLab',
			)
		);
		$out = '<ul class="sp-trust-row">';
		foreach ( $items as $it ) {
			$out .= '<li>' . esc_html( $it ) . '</li>';
		}
		$out .= '</ul>';
		return $out;
	}
}
