<?php
/**
 * Single engineering capability template.
 *
 * @package SaddamEngineerPortfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$lines = static function ( $key ) {
		$value = function_exists( 'spcore_meta' ) ? spcore_meta( $key ) : '';
		return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $value ) ) ) );
	};

	$icon         = function_exists( 'spcore_meta' ) ? spcore_meta( 'icon' ) : '';
	$deliverables = $lines( 'deliverables' );
	$process      = $lines( 'process' );
	$problem      = function_exists( 'spcore_meta' ) ? spcore_meta( 'problem_statement' ) : '';
	$proof        = function_exists( 'spcore_meta' ) ? spcore_meta( 'proof_link' ) : '';
	$short_desc   = function_exists( 'spcore_meta' ) ? spcore_meta( 'short_desc' ) : '';
	$audience     = function_exists( 'spcore_meta' ) ? spcore_meta( 'who_for' ) : '';
	$outcome      = function_exists( 'spcore_meta' ) ? spcore_meta( 'outcome' ) : '';
	$email        = (string) sp_setting( 'email', 'saddam.wp007@gmail.com' );
	?>
	<section class="sp-svc-hero">
		<div class="sp-container sp-svc-hero-grid">
			<div>
				<?php if ( $icon ) : ?><div class="sp-service-icon sp-service-icon-lg" aria-hidden="true"><?php echo esc_html( $icon ); ?></div><?php endif; ?>
				<p class="sp-eyebrow"><?php esc_html_e( 'Engineering Capability', 'sptheme' ); ?></p>
				<h1><?php the_title(); ?></h1>
				<?php if ( $short_desc ) : ?><p class="sp-hero-sub"><?php echo esc_html( $short_desc ); ?></p><?php endif; ?>
				<div class="sp-hero-cta">
					<a class="sp-btn sp-btn-primary" href="<?php echo esc_url( home_url( '/engineering/' ) ); ?>"><?php esc_html_e( 'Explore Related Implementation', 'sptheme' ); ?></a>
					<a class="sp-btn" href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php esc_html_e( 'Email', 'sptheme' ); ?></a>
				</div>
			</div>
			<aside class="sp-svc-quick"><strong><?php esc_html_e( 'Evaluation approach', 'sptheme' ); ?></strong><p><?php esc_html_e( 'Review the related code path, case study, endpoint or quality gate, then ask about design choices and trade-offs.', 'sptheme' ); ?></p></aside>
		</div>
	</section>

	<section class="sp-section sp-section-tight">
		<div class="sp-container sp-svc-cols">
			<div class="sp-svc-main">
				<?php if ( $problem ) : ?><div class="sp-svc-problem"><p class="sp-eyebrow"><?php esc_html_e( 'Responsibility', 'sptheme' ); ?></p><h2><?php echo esc_html( $problem ); ?></h2></div><?php endif; ?>
				<?php if ( $audience ) : ?><h2><?php esc_html_e( 'Relevant team context', 'sptheme' ); ?></h2><p><?php echo esc_html( $audience ); ?></p><?php endif; ?>
				<?php if ( $deliverables ) : ?>
					<h2><?php esc_html_e( 'Related implementation', 'sptheme' ); ?></h2>
					<div class="sp-deliver-grid">
						<?php foreach ( $deliverables as $deliverable ) : ?><div><span aria-hidden="true">✓</span><?php echo esc_html( $deliverable ); ?></div><?php endforeach; ?>
					</div>
				<?php endif; ?>
				<?php if ( $process ) : ?>
					<h2><?php esc_html_e( 'Engineering workflow', 'sptheme' ); ?></h2>
					<ol class="sp-numlist"><?php foreach ( $process as $step ) : ?><li><?php echo esc_html( $step ); ?></li><?php endforeach; ?></ol>
				<?php endif; ?>
				<?php if ( $outcome ) : ?><h2><?php esc_html_e( 'Expected engineering outcome', 'sptheme' ); ?></h2><p><?php echo esc_html( $outcome ); ?></p><?php endif; ?>
				<div class="sp-content"><?php the_content(); ?></div>
				<?php if ( $proof ) : ?><p><a class="sp-link sp-link-arrow" href="<?php echo esc_url( $proof ); ?>"><?php esc_html_e( 'Open related work', 'sptheme' ); ?> </a></p><?php endif; ?>
				<?php
				$faq = get_post_meta( get_the_ID(), '_spcore_faq', true );
				if ( is_array( $faq ) && $faq ) :
					?>
					<h2><?php esc_html_e( 'Review questions', 'sptheme' ); ?></h2>
					<div class="sp-faq">
						<?php foreach ( $faq as $item ) : ?>
							<?php if ( empty( $item['q'] ) ) { continue; } ?>
							<details><summary><?php echo esc_html( $item['q'] ); ?></summary><div><p><?php echo esc_html( isset( $item['a'] ) ? $item['a'] : '' ); ?></p></div></details>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<aside class="sp-svc-panel">
				<h4><?php esc_html_e( 'Quality expectations', 'sptheme' ); ?></h4>
				<ul class="sp-svc-list">
					<li><?php esc_html_e( 'Clear scope and constraints', 'sptheme' ); ?></li>
					<li><?php esc_html_e( 'Security and capability checks', 'sptheme' ); ?></li>
					<li><?php esc_html_e( 'Maintainable WordPress-native design', 'sptheme' ); ?></li>
					<li><?php esc_html_e( 'Testing and failure-path review', 'sptheme' ); ?></li>
					<li><?php esc_html_e( 'Documentation and handover', 'sptheme' ); ?></li>
				</ul>
				<a class="sp-btn sp-btn-primary" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Discuss a Role', 'sptheme' ); ?></a>
				<p class="sp-svc-note"><?php esc_html_e( 'Direct email:', 'sptheme' ); ?> <a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></p>
				<p class="sp-svc-note"><?php esc_html_e( 'Confidentiality, access and commercial terms are agreed in writing before implementation.', 'sptheme' ); ?></p>
			</aside>
		</div>
	</section>

	<section class="sp-section sp-section-alt sp-section-tight">
		<div class="sp-container">
			<header class="sp-section-head"><p class="sp-eyebrow"><?php esc_html_e( 'Related Capabilities', 'sptheme' ); ?></p><h2><?php esc_html_e( 'Connected areas of WordPress engineering.', 'sptheme' ); ?></h2></header>
			<?php sp_block( '[spcore_services count="3"]' ); ?>
		</div>
	</section>
	<?php
endwhile;

get_footer();
