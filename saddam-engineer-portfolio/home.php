<?php
/**
 * Technical writing archive.
 *
 * @package SaddamEngineerPortfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
$categories = get_categories( array( 'hide_empty' => true, 'exclude' => array( absint( get_option( 'default_category' ) ) ) ) );
?>

<section class="s1-page-hero s1-page-hero-writing">
	<div class="sp-container s1-page-hero-grid">
		<div>
			<p class="s1-eyebrow"><?php esc_html_e( 'Technical writing', 'sptheme' ); ?></p>
			<h1><?php esc_html_e( 'Practical WordPress notes for building, debugging and maintaining real systems.', 'sptheme' ); ?></h1>
			<p><?php esc_html_e( 'Articles focus on decisions, examples, safeguards and trade-offs across plugin architecture, REST APIs, Gutenberg, production debugging, editor experience and search foundations.', 'sptheme' ); ?></p>
		</div>
		<aside class="s1-page-side-note">
			<span><?php esc_html_e( 'Editorial standard', 'sptheme' ); ?></span>
			<strong><?php esc_html_e( 'Useful enough to apply, honest about limitations.', 'sptheme' ); ?></strong>
			<p><?php esc_html_e( 'Every article should leave the reader with a clearer implementation decision, checklist or reusable workflow.', 'sptheme' ); ?></p>
		</aside>
	</div>
</section>

<section class="s1-section s1-section-light s1-writing-archive">
	<div class="sp-container">
		<?php if ( $categories ) : ?>
			<nav class="s1-filter-chips" aria-label="<?php esc_attr_e( 'Article categories', 'sptheme' ); ?>">
				<a class="is-active" href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'All', 'sptheme' ); ?></a>
				<?php foreach ( $categories as $category ) : ?>
					<a href="<?php echo esc_url( get_category_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>
			<?php $position = 0; ?>
			<div class="s1-article-grid">
				<?php while ( have_posts() ) : the_post(); ?>
					<?php
					$position++;
					$categories_for_post = get_the_category();
					$category_label      = $categories_for_post ? $categories_for_post[0]->name : __( 'WordPress Engineering', 'sptheme' );
					$read_time           = max( 1, (int) ceil( str_word_count( wp_strip_all_tags( get_the_content() ) ) / 220 ) );
					?>
					<article <?php post_class( 1 === $position ? 's1-article-card is-featured' : 's1-article-card' ); ?>>
						<div class="s1-article-topline"><span><?php echo esc_html( $category_label ); ?></span><small><?php echo esc_html( $read_time ); ?> <?php esc_html_e( 'min read', 'sptheme' ); ?></small></div>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 1 === $position ? 34 : 22 ) ); ?></p>
						<div class="s1-article-footer"><time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time><a href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read article', 'sptheme' ); ?> </a></div>
					</article>
				<?php endwhile; ?>
			</div>
			<div class="s1-pagination"><?php the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => '←', 'next_text' => '' ) ); ?></div>
		<?php else : ?>
			<div class="s1-empty-state"><h2><?php esc_html_e( 'Technical articles are being prepared.', 'sptheme' ); ?></h2><p><?php esc_html_e( 'Start with the Engineering page for the current architecture and implementation map.', 'sptheme' ); ?></p><a class="s1-btn s1-btn-primary" href="<?php echo esc_url( home_url( '/engineering/' ) ); ?>"><?php esc_html_e( 'Explore Engineering', 'sptheme' ); ?></a></div>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>
