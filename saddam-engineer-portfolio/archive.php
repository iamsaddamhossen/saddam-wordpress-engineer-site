<?php
/**
 * Generic archive (category, tag, date, author).
 * @package SaddamPortfolioV3
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header(); ?>
<section class="sp-landing-hero">
	<div class="sp-container">
		<p class="sp-eyebrow"><?php esc_html_e( 'Archive', 'sptheme' ); ?></p>
		<h1 class="sp-hero-title"><?php the_archive_title(); ?></h1>
		<?php if ( get_the_archive_description() ) : ?><div class="sp-hero-sub"><?php the_archive_description(); ?></div><?php endif; ?>
	</div>
</section>
<section class="sp-section sp-section-tight">
	<div class="sp-container">
		<?php if ( have_posts() ) : ?>
			<div class="sp-post-grid">
				<?php while ( have_posts() ) : the_post(); ?>
					<article <?php post_class( 'sp-card sp-post-card' ); ?>>
						<div class="sp-card-body">
							<span class="sp-post-date"><?php echo esc_html( get_the_date() ); ?></span>
							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
							<div class="sp-card-links"><a class="sp-link sp-link-arrow" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read', 'sptheme' ); ?> </a></div>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
			<div class="sp-pagination"><?php the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => '←', 'next_text' => '' ) ); ?></div>
		<?php else : ?>
			<p class="sp-empty"><?php esc_html_e( 'Nothing here yet.', 'sptheme' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php get_footer();
