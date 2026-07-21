<?php
/**
 * Case studies archive.
 *
 * @package SaddamEngineerPortfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
$cases = get_posts(
	array(
		'post_type'      => 'sp_case_study',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order date',
		'order'          => 'ASC',
	)
);
?>

<section class="s1-page-hero s1-page-hero-case">
	<div class="sp-container s1-page-hero-grid">
		<div><p class="s1-eyebrow"><?php esc_html_e( 'Case studies', 'sptheme' ); ?></p><h1><?php esc_html_e( 'WordPress work explained through context, responsibility and technical decisions.', 'sptheme' ); ?></h1><p><?php esc_html_e( 'Each case study explains the system constraint, what I owned, how risk was managed and which result can be discussed honestly.', 'sptheme' ); ?></p></div>
		<aside class="s1-page-side-note"><span><?php esc_html_e( 'Reading framework', 'sptheme' ); ?></span><strong><?php esc_html_e( 'Context  decision  implementation  result', 'sptheme' ); ?></strong><p><?php esc_html_e( 'Confidential client details, credentials and unsupported metrics are intentionally excluded.', 'sptheme' ); ?></p></aside>
	</div>
</section>

<section class="s1-section s1-section-light">
	<div class="sp-container">
		<div class="s1-case-archive-grid">
			<?php foreach ( $cases as $case ) : ?>
				<?php
				$metric    = get_post_meta( $case->ID, '_spcore_proof_metric', true );
				$challenge = get_post_meta( $case->ID, '_spcore_challenge', true );
				$stack     = get_post_meta( $case->ID, '_spcore_stack', true );
				?>
				<article class="s1-case-archive-card">
					<?php if ( $metric ) : ?><p class="s1-case-metric"><?php echo esc_html( $metric ); ?></p><?php endif; ?>
					<h2><a href="<?php echo esc_url( get_permalink( $case ) ); ?>"><?php echo esc_html( get_the_title( $case ) ); ?></a></h2>
					<p><?php echo esc_html( wp_trim_words( (string) $challenge, 34 ) ); ?></p>
					<?php if ( $stack ) : ?><small><?php echo esc_html( $stack ); ?></small><?php endif; ?>
					<a href="<?php echo esc_url( get_permalink( $case ) ); ?>"><?php esc_html_e( 'Read case study', 'sptheme' ); ?> </a>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php get_footer(); ?>
