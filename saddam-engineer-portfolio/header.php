<?php
/**
 * Site header.
 *
 * @package SaddamEngineerPortfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$announcement_url = (string) sp_setting( 'announce_cta_url', '/engineering/' );
if ( 0 === strpos( $announcement_url, '/' ) ) {
	$announcement_url = home_url( $announcement_url );
}
$cv_url     = (string) sp_setting( 'cv_url', '' );
$header_url = $cv_url ? $cv_url : home_url( '/contact/' );
$header_cta = $cv_url ? __( 'View CV', 'sptheme' ) : __( 'Discuss a Role', 'sptheme' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="sp-skip" href="#main"><?php esc_html_e( 'Skip to content', 'sptheme' ); ?></a>

<?php if ( sp_setting( 'announce_enable', 1 ) ) : ?>
	<div class="sp-announcement" role="region" aria-label="<?php esc_attr_e( 'Career availability', 'sptheme' ); ?>">
		<div class="sp-container sp-announcement-inner">
			<span><?php echo esc_html( sp_setting( 'announce_text', 'Open to full-time remote WordPress engineering roles and focused production collaborations.' ) ); ?></span>
			<a href="<?php echo esc_url( $announcement_url ); ?>" data-sp-event="career_cta_click" data-sp-label="announcement">
				<?php echo esc_html( sp_setting( 'announce_cta_label', 'Explore Engineering' ) ); ?> 
			</a>
		</div>
	</div>
<?php endif; ?>

<header class="sp-header" id="site-header">
	<div class="sp-container sp-header-inner">
		<div class="sp-brand">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="sp-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<?php echo esc_html( sp_setting( 'name', get_bloginfo( 'name' ) ) ); ?><span class="sp-logo-dot">.</span>
				</a>
			<?php endif; ?>
		</div>

		<nav class="sp-nav" id="sp-primary-nav" aria-label="<?php esc_attr_e( 'Primary navigation', 'sptheme' ); ?>">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'sp-nav-list',
						'depth'          => 2,
						'fallback_cb'    => false,
					)
				);
			} else {
				sptheme_fallback_menu();
			}
			?>
		</nav>

		<div class="sp-header-cta">
			<a class="sp-btn sp-btn-small" href="<?php echo esc_url( $header_url ); ?>" <?php echo $cv_url ? 'target="_blank" rel="noopener noreferrer"' : ''; ?> data-sp-event="career_cta_click" data-sp-label="header">
				<?php echo esc_html( $header_cta ); ?>
			</a>
			<button class="sp-nav-toggle" type="button" aria-expanded="false" aria-controls="sp-primary-nav" aria-label="<?php esc_attr_e( 'Open navigation menu', 'sptheme' ); ?>">
				<span></span><span></span><span></span>
			</button>
		</div>
	</div>
</header>

<main id="main" class="sp-main">
