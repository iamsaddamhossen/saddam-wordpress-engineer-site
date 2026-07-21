<?php
/**
 * Single project template.
 *
 * @package SaddamEngineerPortfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$live     = function_exists( 'spcore_meta' ) ? (string) spcore_meta( 'live_url' ) : '';
	$nda      = function_exists( 'spcore_meta' ) ? (bool) spcore_meta( 'nda' ) : false;
	$stack    = function_exists( 'spcore_meta' ) ? (string) spcore_meta( 'tech_stack' ) : '';
	$category = function_exists( 'spcore_meta' ) ? (string) spcore_meta( 'category_label' ) : '';
	$github   = (string) sp_setting( 'github_url', 'https://github.com/iamsaddamhossen' );
	$result   = function_exists( 'spcore_meta' ) ? (string) spcore_meta( 'result' ) : '';
	?>
	<section class="sp-case-hero sp-dark-hero">
		<div class="sp-container sp-case-hero-grid">
			<div>
				<p class="sp-eyebrow"><?php echo esc_html( $category ? $category : __( 'Production work', 'sptheme' ) ); ?></p>
				<h1><?php the_title(); ?></h1>
				<p><?php esc_html_e( 'A concise review of the system context, my responsibility, implementation approach and observable engineering value.', 'sptheme' ); ?></p>
				<div class="sp-hero-cta">
					<?php if ( $live && ! $nda ) : ?>
						<a class="sp-btn sp-btn-primary" href="<?php echo esc_url( $live ); ?>" target="_blank" rel="noopener noreferrer" data-sp-event="project_link_click" data-sp-label="<?php echo esc_attr( get_the_title() ); ?>"><?php esc_html_e( 'Open Public Implementation ', 'sptheme' ); ?></a>
					<?php elseif ( $nda ) : ?>
						<span class="sp-tag"><?php esc_html_e( 'Confidential implementation', 'sptheme' ); ?></span>
					<?php endif; ?>
					<a class="sp-btn sp-btn-ghost-light" href="<?php echo esc_url( $github ); ?>" target="_blank" rel="noopener noreferrer">GitHub </a>
				</div>
			</div>
			<aside class="sp-case-score-card">
				<span><?php esc_html_e( 'Observable result', 'sptheme' ); ?></span>
				<strong><?php echo esc_html( $result ? $result : __( 'Documented production contribution', 'sptheme' ) ); ?></strong>
				<p><?php esc_html_e( 'Project details are limited to information that can be explained honestly without exposing confidential client data.', 'sptheme' ); ?></p>
			</aside>
		</div>
	</section>

	<section class="sp-section sp-section-tight sp-section-light">
		<div class="sp-container sp-case-layout">
			<article class="sp-case-main">
				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="sp-featured"><?php the_post_thumbnail( 'large', array( 'loading' => 'eager' ) ); ?></figure>
				<?php endif; ?>

				<?php
				$rows = array(
					__( 'Project type', 'sptheme' )      => function_exists( 'spcore_meta' ) ? spcore_meta( 'project_type' ) : '',
					__( 'Client / location', 'sptheme' ) => function_exists( 'spcore_meta' ) ? spcore_meta( 'client_location' ) : '',
					__( 'Industry', 'sptheme' )          => function_exists( 'spcore_meta' ) ? spcore_meta( 'industry_label' ) : '',
					__( 'Technology', 'sptheme' )        => $stack,
				);
				?>
				<div class="sp-meta-grid">
					<?php foreach ( $rows as $label => $value ) : ?>
						<?php if ( $value ) : ?><div><strong><?php echo esc_html( $label ); ?></strong><span><?php echo esc_html( $value ); ?></span></div><?php endif; ?>
					<?php endforeach; ?>
				</div>

				<?php
				$blocks = array(
					__( 'System context', 'sptheme' )        => function_exists( 'spcore_meta' ) ? spcore_meta( 'summary' ) : '',
					__( 'Constraint or problem', 'sptheme' ) => function_exists( 'spcore_meta' ) ? spcore_meta( 'problem' ) : '',
					__( 'My responsibility', 'sptheme' )     => function_exists( 'spcore_meta' ) ? spcore_meta( 'role' ) : '',
					__( 'Implementation', 'sptheme' )        => function_exists( 'spcore_meta' ) ? spcore_meta( 'what_built' ) : '',
					__( 'Engineering value', 'sptheme' )     => function_exists( 'spcore_meta' ) ? spcore_meta( 'business_value' ) : '',
				);
				foreach ( $blocks as $heading => $body ) :
					if ( ! $body ) {
						continue;
					}
					?>
					<section class="sp-case-block">
						<p class="sp-eyebrow"><?php echo esc_html( $heading ); ?></p>
						<p><?php echo esc_html( $body ); ?></p>
					</section>
				<?php endforeach; ?>

				<div class="sp-content sp-prose"><?php the_content(); ?></div>
			</article>

			<aside class="sp-case-sidebar">
				<div class="sp-sticky-panel">
					<p class="sp-eyebrow"><?php esc_html_e( 'Technical path', 'sptheme' ); ?></p>
					<h3><?php esc_html_e( 'Related technical context', 'sptheme' ); ?></h3>
					<ul class="sp-check-list">
						<li><?php esc_html_e( 'Review the engineering architecture', 'sptheme' ); ?></li>
						<li><?php esc_html_e( 'Inspect the public repository', 'sptheme' ); ?></li>
						<li><?php esc_html_e( 'Review constraints and trade-offs', 'sptheme' ); ?></li>
						<li><?php esc_html_e( 'Continue with the product context and priorities', 'sptheme' ); ?></li>
					</ul>
					<a class="sp-btn sp-btn-primary" href="<?php echo esc_url( home_url( '/engineering/' ) ); ?>"><?php esc_html_e( 'Explore Engineering System', 'sptheme' ); ?></a>
					<a class="sp-text-link" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Discuss a role', 'sptheme' ); ?> </a>
				</div>
			</aside>
		</div>
	</section>
	<?php
endwhile;

get_footer();
