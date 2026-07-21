<?php
/**
 * Single case study template.
 *
 * @package SaddamEngineerPortfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$evidence  = function_exists( 'spcore_meta' ) ? (string) spcore_meta( 'proof_metric' ) : '';
	$challenge = function_exists( 'spcore_meta' ) ? (string) spcore_meta( 'challenge' ) : '';
	$solution  = function_exists( 'spcore_meta' ) ? (string) spcore_meta( 'solution' ) : '';
	$approach  = function_exists( 'spcore_meta' ) ? (string) spcore_meta( 'technical_approach' ) : '';
	$stack     = function_exists( 'spcore_meta' ) ? (string) spcore_meta( 'stack' ) : '';
	$result    = function_exists( 'spcore_meta' ) ? (string) spcore_meta( 'result' ) : '';
	$github    = (string) sp_setting( 'github_url', 'https://github.com/iamsaddamhossen' );
	?>
	<section class="sp-case-hero sp-dark-hero">
		<div class="sp-container sp-case-hero-grid">
			<div>
				<p class="sp-eyebrow"><?php esc_html_e( 'Case Study', 'sptheme' ); ?></p>
				<h1><?php the_title(); ?></h1>
				<p><?php esc_html_e( 'A transparent breakdown of the context, constraints, technical decisions, implementation safeguards and observable result.', 'sptheme' ); ?></p>
				<div class="sp-hero-cta">
					<a class="sp-btn sp-btn-primary" href="<?php echo esc_url( home_url( '/engineering/' ) ); ?>"><?php esc_html_e( 'Explore Engineering', 'sptheme' ); ?></a>
					<a class="sp-btn sp-btn-ghost-light" href="<?php echo esc_url( $github ); ?>" target="_blank" rel="noopener noreferrer">GitHub </a>
				</div>
			</div>
			<aside class="sp-case-score-card">
				<span><?php esc_html_e( 'Outcome summary', 'sptheme' ); ?></span>
				<strong><?php echo esc_html( $evidence ? $evidence : __( 'Documented implementation outcome', 'sptheme' ) ); ?></strong>
				<p><?php echo esc_html( $result ? $result : __( 'The result is described only to the level that can be supported without exposing confidential information.', 'sptheme' ) ); ?></p>
			</aside>
		</div>
	</section>

	<section class="sp-section sp-section-tight">
		<div class="sp-container sp-case-layout">
			<article class="sp-case-main">
				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="sp-featured"><?php the_post_thumbnail( 'large', array( 'loading' => 'eager' ) ); ?></figure>
				<?php endif; ?>

				<?php if ( $challenge ) : ?>
					<section class="sp-case-block">
						<p class="sp-eyebrow"><?php esc_html_e( 'Context and constraint', 'sptheme' ); ?></p>
						<h2><?php esc_html_e( 'What had to be understood before coding', 'sptheme' ); ?></h2>
						<p><?php echo esc_html( $challenge ); ?></p>
					</section>
				<?php endif; ?>

				<?php if ( $solution ) : ?>
					<section class="sp-case-block">
						<p class="sp-eyebrow"><?php esc_html_e( 'Engineering decision', 'sptheme' ); ?></p>
						<h2><?php esc_html_e( 'What was built, changed or isolated', 'sptheme' ); ?></h2>
						<p><?php echo esc_html( $solution ); ?></p>
					</section>
				<?php endif; ?>

				<?php if ( $approach ) : ?>
					<section class="sp-case-block">
						<p class="sp-eyebrow"><?php esc_html_e( 'Implementation safeguards', 'sptheme' ); ?></p>
						<h2><?php esc_html_e( 'How maintainability, security and rollback were considered', 'sptheme' ); ?></h2>
						<p><?php echo esc_html( $approach ); ?></p>
					</section>
				<?php endif; ?>

				<?php if ( $result ) : ?>
					<section class="sp-case-block sp-case-result">
						<p class="sp-eyebrow"><?php esc_html_e( 'Observable result', 'sptheme' ); ?></p>
						<h2><?php echo esc_html( $evidence ? $evidence : __( 'Documented outcome', 'sptheme' ) ); ?></h2>
						<p><?php echo esc_html( $result ); ?></p>
					</section>
				<?php endif; ?>

				<div class="sp-content sp-prose"><?php the_content(); ?></div>
			</article>

			<aside class="sp-case-sidebar">
				<div class="sp-svc-panel sp-sticky-panel">
					<p class="sp-eyebrow"><?php esc_html_e( 'Review notes', 'sptheme' ); ?></p>
					<ul class="sp-check-list">
						<?php if ( $evidence ) : ?><li><strong><?php esc_html_e( 'Outcome:', 'sptheme' ); ?></strong> <?php echo esc_html( $evidence ); ?></li><?php endif; ?>
						<?php if ( $stack ) : ?><li><strong><?php esc_html_e( 'Stack:', 'sptheme' ); ?></strong> <?php echo esc_html( $stack ); ?></li><?php endif; ?>
						<li><?php esc_html_e( 'Scope and constraints documented', 'sptheme' ); ?></li>
						<li><?php esc_html_e( 'Security and failure paths considered', 'sptheme' ); ?></li>
						<li><?php esc_html_e( 'Claims limited to supportable project details', 'sptheme' ); ?></li>
					</ul>
					<a class="sp-btn sp-btn-primary" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Discuss a Role', 'sptheme' ); ?></a>
				</div>
			</aside>
		</div>
	</section>
	<?php
endwhile;

get_footer();
