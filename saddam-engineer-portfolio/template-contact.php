<?php
/**
 * Template Name: Contact
 *
 * @package SaddamEngineerPortfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
$email    = (string) sp_setting( 'email', 'saddam.wp007@gmail.com' );
$linkedin = (string) sp_setting( 'linkedin_url', '' );
$github   = (string) sp_setting( 'github_url', 'https://github.com/iamsaddamhossen' );
$cv_url   = (string) sp_setting( 'cv_url', '' );
?>

<section class="s1-page-hero s1-page-hero-contact">
	<div class="sp-container s1-page-hero-grid">
		<div><p class="s1-eyebrow"><?php esc_html_e( 'Contact', 'sptheme' ); ?></p><h1><?php esc_html_e( 'Share the role, product context and the problem that needs ownership.', 'sptheme' ); ?></h1><p><?php esc_html_e( 'For hiring conversations, include the team, responsibilities, stack, work mode and interview process. For technical work, include the current behavior, constraints and desired outcome.', 'sptheme' ); ?></p></div>
		<aside class="s1-page-side-note"><span><?php esc_html_e( 'Best starting point', 'sptheme' ); ?></span><strong><?php esc_html_e( 'Enough context for a focused technical reply.', 'sptheme' ); ?></strong><p><?php esc_html_e( 'Please do not send passwords, API keys, production credentials or private customer data.', 'sptheme' ); ?></p></aside>
	</div>
</section>

<section class="s1-section s1-section-light">
	<div class="sp-container s1-contact-grid">
		<div class="s1-contact-form-card">
			<p class="s1-eyebrow"><?php esc_html_e( 'Send the context', 'sptheme' ); ?></p>
			<h2><?php esc_html_e( 'Start a focused conversation.', 'sptheme' ); ?></h2>
			<p><?php esc_html_e( 'Your message is stored in WordPress only for follow-up and can be exported or erased through the site privacy tools.', 'sptheme' ); ?></p>
			<?php sp_block( '[spcore_contact]' ); ?>
		</div>
		<aside class="s1-contact-side">
			<div class="s1-contact-fit">
				<p class="s1-eyebrow"><?php esc_html_e( 'Best-fit conversations', 'sptheme' ); ?></p>
				<h3><?php esc_html_e( 'Senior WordPress engineering and production problem-solving.', 'sptheme' ); ?></h3>
				<ul><li><?php esc_html_e( 'Full-time or long-term remote WordPress roles', 'sptheme' ); ?></li><li><?php esc_html_e( 'Custom plugins, WooCommerce and integrations', 'sptheme' ); ?></li><li><?php esc_html_e( 'Gutenberg and editor experience', 'sptheme' ); ?></li><li><?php esc_html_e( 'Performance, security and production debugging', 'sptheme' ); ?></li><li><?php esc_html_e( 'Technical support engineering and incident ownership', 'sptheme' ); ?></li></ul>
			</div>
			<div class="s1-contact-links">
				<strong><?php esc_html_e( 'Direct profiles', 'sptheme' ); ?></strong>
				<a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a>
				<a href="<?php echo esc_url( $github ); ?>" target="_blank" rel="noopener noreferrer">GitHub </a>
				<?php if ( $linkedin ) : ?><a href="<?php echo esc_url( $linkedin ); ?>" target="_blank" rel="noopener noreferrer">LinkedIn </a><?php endif; ?>
				<?php if ( $cv_url ) : ?><a href="<?php echo esc_url( $cv_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Curriculum Vitae ', 'sptheme' ); ?></a><?php endif; ?>
			</div>
		</aside>
	</div>
</section>

<?php get_footer(); ?>
