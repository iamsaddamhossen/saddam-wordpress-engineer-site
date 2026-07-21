<?php
/**
 * Site footer.
 *
 * @package SaddamEngineerPortfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$email        = (string) sp_setting( 'email', 'saddam.wp007@gmail.com' );
$name         = (string) sp_setting( 'name', 'Saddam Hossen' );
$github       = (string) sp_setting( 'github_url', 'https://github.com/iamsaddamhossen' );
$linkedin     = (string) sp_setting( 'linkedin_url', '' );
$upwork       = (string) sp_setting( 'upwork_url', '' );
$cv_url       = (string) sp_setting( 'cv_url', '' );
$projects_url = get_post_type_archive_link( 'sp_project' );
$cases_url    = get_post_type_archive_link( 'sp_case_study' );
?>
</main>

<footer class="sp-footer sp-footer-pro">
	<div class="sp-container">
		<section class="sp-footer-cta" aria-labelledby="sp-footer-cta-title">
			<div>
				<p class="sp-eyebrow"><?php esc_html_e( 'Open to remote WordPress roles', 'sptheme' ); ?></p>
				<h2 id="sp-footer-cta-title"><?php esc_html_e( 'Need dependable WordPress engineering support?', 'sptheme' ); ?></h2>
				<p><?php esc_html_e( 'Share the role, product context and technical priorities. I will respond with the most relevant experience and availability.', 'sptheme' ); ?></p>
			</div>
			<div class="sp-footer-cta-actions">
				<a class="sp-btn sp-btn-primary" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" data-sp-event="career_cta_click" data-sp-label="footer"><?php esc_html_e( 'Discuss a Role', 'sptheme' ); ?></a>
				<a class="sp-btn sp-btn-ghost-light" href="<?php echo esc_url( home_url( '/engineering/' ) ); ?>"><?php esc_html_e( 'Explore Engineering', 'sptheme' ); ?></a>
			</div>
		</section>

		<div class="sp-footer-grid sp-footer-grid-career">
			<div class="sp-footer-brand">
				<a class="sp-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( $name ); ?><span class="sp-logo-dot">.</span></a>
				<p class="sp-footer-tag"><?php esc_html_e( 'Senior WordPress Engineer focused on custom plugins, WooCommerce, Gutenberg, REST APIs, performance, security and production debugging.', 'sptheme' ); ?></p>
				<p><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></p>
			</div>

			<nav aria-label="<?php esc_attr_e( 'Engineering links', 'sptheme' ); ?>">
				<h3><?php esc_html_e( 'Engineering', 'sptheme' ); ?></h3>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/engineering/' ) ); ?>"><?php esc_html_e( 'Implementation Details', 'sptheme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/engineering/#api' ) ); ?>"><?php esc_html_e( 'REST API', 'sptheme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/engineering/#quality' ) ); ?>"><?php esc_html_e( 'Quality Gates', 'sptheme' ); ?></a></li>
					<li><a href="<?php echo esc_url( $github ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'GitHub Repository', 'sptheme' ); ?></a></li>
				</ul>
			</nav>

			<nav aria-label="<?php esc_attr_e( 'Work links', 'sptheme' ); ?>">
				<h3><?php esc_html_e( 'Work', 'sptheme' ); ?></h3>
				<ul>
					<li><a href="<?php echo esc_url( $cases_url ? $cases_url : home_url( '/case-studies/' ) ); ?>"><?php esc_html_e( 'Case Studies', 'sptheme' ); ?></a></li>
					<li><a href="<?php echo esc_url( $projects_url ? $projects_url : home_url( '/projects/' ) ); ?>"><?php esc_html_e( 'Projects', 'sptheme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/experience/' ) ); ?>"><?php esc_html_e( 'Experience', 'sptheme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'Technical Writing', 'sptheme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/recommended-tools/' ) ); ?>"><?php esc_html_e( 'Recommended Tools', 'sptheme' ); ?></a></li>
				</ul>
			</nav>

			<nav aria-label="<?php esc_attr_e( 'Profile links', 'sptheme' ); ?>">
				<h3><?php esc_html_e( 'Profile', 'sptheme' ); ?></h3>
				<ul>
					<?php if ( $cv_url ) : ?><li><a href="<?php echo esc_url( $cv_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Curriculum Vitae', 'sptheme' ); ?></a></li><?php endif; ?>
					<?php if ( $linkedin ) : ?><li><a href="<?php echo esc_url( $linkedin ); ?>" target="_blank" rel="noopener noreferrer">LinkedIn</a></li><?php endif; ?>
					<?php if ( $upwork ) : ?><li><a href="<?php echo esc_url( $upwork ); ?>" target="_blank" rel="noopener noreferrer">Upwork</a></li><?php endif; ?>
					<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'sptheme' ); ?></a></li>
				</ul>
			</nav>

			<nav aria-label="<?php esc_attr_e( 'Legal links', 'sptheme' ); ?>">
				<h3><?php esc_html_e( 'Site', 'sptheme' ); ?></h3>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About', 'sptheme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy', 'sptheme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>"><?php esc_html_e( 'Terms', 'sptheme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/llms.txt' ) ); ?>">llms.txt</a></li>
				</ul>
			</nav>
		</div>

		<div class="sp-footer-bottom">
			<span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( $name ); ?>.</span>
			<span><?php esc_html_e( 'Custom WordPress theme and companion plugin. No page builder.', 'sptheme' ); ?></span>
		</div>
	</div>
</footer>

<?php if ( sp_setting( 'mobile_bar_enable', 1 ) ) : ?>
	<nav class="sp-mobile-cta" id="sp-mobile-cta" aria-label="<?php esc_attr_e( 'Mobile quick actions', 'sptheme' ); ?>">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><span class="sp-nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 11.5 12 5l8 6.5V20h-5v-5H9v5H4Z"/></svg></span><?php esc_html_e( 'Home', 'sptheme' ); ?></a>
		<a href="<?php echo esc_url( home_url( '/engineering/' ) ); ?>"><span class="sp-nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m8.5 7-5 5 5 5M15.5 7l5 5-5 5M14 4l-4 16"/></svg></span><?php esc_html_e( 'Code', 'sptheme' ); ?></a>
		<a href="<?php echo esc_url( $cases_url ? $cases_url : home_url( '/case-studies/' ) ); ?>"><span class="sp-nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 3 21 12 12 21 3 12Z"/><path d="M12 7 17 12 12 17 7 12Z"/></svg></span><?php esc_html_e( 'Work', 'sptheme' ); ?></a>
		<a href="<?php echo esc_url( $github ); ?>" target="_blank" rel="noopener noreferrer"><span class="sp-nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 3a9 9 0 0 0-2.85 17.54c.45.08.62-.2.62-.44v-1.73c-2.52.55-3.05-1.07-3.05-1.07-.41-1.05-1-1.33-1-1.33-.82-.56.06-.55.06-.55.91.06 1.39.94 1.39.94.81 1.39 2.12.99 2.64.76.08-.59.32-.99.58-1.22-2.01-.23-4.12-1.01-4.12-4.49 0-.99.35-1.8.93-2.44-.09-.23-.4-1.16.09-2.4 0 0 .76-.24 2.48.93A8.6 8.6 0 0 1 12 8.2c.76 0 1.52.1 2.23.3 1.72-1.17 2.48-.93 2.48-.93.49 1.24.18 2.17.09 2.4.58.64.93 1.45.93 2.44 0 3.49-2.12 4.25-4.14 4.48.33.28.62.83.62 1.68v2.53c0 .24.16.52.62.43A9 9 0 0 0 12 3Z"/></svg></span>GitHub</a>
		<a class="sp-mobile-cta-main" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><span class="sp-nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg></span><?php esc_html_e( 'Contact', 'sptheme' ); ?></a>
	</nav>
<?php endif; ?>

<button class="sp-back-to-top" id="sp-back-to-top" type="button" aria-label="<?php esc_attr_e( 'Back to top', 'sptheme' ); ?>" title="<?php esc_attr_e( 'Back to top', 'sptheme' ); ?>">
	<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 19V5M6.5 10.5 12 5l5.5 5.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
</button>

<?php if ( sp_setting( 'modal_enable', 0 ) ) : ?>
	<div class="sp-lead-modal" id="sp-lead-modal" aria-hidden="true" data-delay="<?php echo esc_attr( sp_setting( 'modal_delay', '12000' ) ); ?>" data-scroll="<?php echo esc_attr( sp_setting( 'modal_scroll', '55' ) ); ?>" data-days="<?php echo esc_attr( sp_setting( 'modal_frequency_days', '14' ) ); ?>">
		<div class="sp-lead-modal-backdrop" data-modal-close></div>
		<div class="sp-lead-modal-card" role="dialog" aria-modal="true" aria-labelledby="sp-modal-title">
			<button class="sp-modal-close" type="button" aria-label="<?php esc_attr_e( 'Close dialog', 'sptheme' ); ?>" data-modal-close>×</button>
			<p class="sp-eyebrow"><?php esc_html_e( 'Hiring conversation', 'sptheme' ); ?></p>
			<h2 id="sp-modal-title"><?php echo esc_html( sp_setting( 'modal_headline', 'Hiring a WordPress engineer?' ) ); ?></h2>
			<p><?php echo esc_html( sp_setting( 'modal_body', 'Share the product context, technical priorities and team needs.' ) ); ?></p>
			<div class="sp-modal-actions">
				<a class="sp-btn sp-btn-primary" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php echo esc_html( sp_setting( 'modal_cta_label', 'Discuss a Role' ) ); ?></a>
				<button class="sp-btn sp-btn-secondary" type="button" data-modal-close><?php esc_html_e( 'Continue browsing', 'sptheme' ); ?></button>
			</div>
		</div>
	</div>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
