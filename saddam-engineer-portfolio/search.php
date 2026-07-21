<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header(); ?>
<section class="sp-section"><div class="sp-container">
	<header class="sp-section-head"><h1><?php printf( esc_html__( 'Search: %s', 'sptheme' ), '<span>' . esc_html( get_search_query() ) . '</span>' ); ?></h1></header>
	<?php if ( have_posts() ) : ?>
		<div class="sp-post-grid">
		<?php while ( have_posts() ) : the_post(); ?>
			<article class="sp-card"><div class="sp-card-body">
				<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
				<p><?php echo esc_html( get_the_excerpt() ); ?></p>
			</div></article>
		<?php endwhile; ?>
		</div>
	<?php else : ?>
		<p class="sp-empty"><?php esc_html_e( 'Nothing found. Try another term.', 'sptheme' ); ?></p>
	<?php endif; ?>
</div></section>
<?php get_footer();
