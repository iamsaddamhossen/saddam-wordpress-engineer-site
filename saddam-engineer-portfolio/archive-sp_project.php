<?php
/**
 * Project archive focused on engineering relevance.
 *
 * @package SaddamEngineerPortfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$featured = get_posts(
	array(
		'post_type'      => 'sp_project',
		'post_status'    => 'publish',
		'posts_per_page' => 6,
		'meta_key'       => '_spcore_featured',
		'meta_value'     => '1',
		'orderby'        => 'menu_order date',
		'order'          => 'ASC',
	)
);

$featured_ids = wp_list_pluck( $featured, 'ID' );
$delivery     = get_posts(
	array(
		'post_type'      => 'sp_project',
		'post_status'    => 'publish',
		'posts_per_page' => 12,
		'post__not_in'   => $featured_ids,
		'orderby'        => 'menu_order date',
		'order'          => 'ASC',
	)
);
?>

<section class="s1-page-hero s1-page-hero-projects">
	<div class="sp-container s1-page-hero-grid">
		<div><p class="s1-eyebrow"><?php esc_html_e( 'Selected work', 'sptheme' ); ?></p><h1><?php esc_html_e( 'WordPress systems organized by responsibility, not screenshot volume.', 'sptheme' ); ?></h1><p><?php esc_html_e( 'The first group highlights engineering depth. The second group shows broader delivery experience without presenting every website as the same kind of work.', 'sptheme' ); ?></p></div>
		<aside class="s1-page-side-note"><span><?php esc_html_e( 'What matters', 'sptheme' ); ?></span><strong><?php esc_html_e( 'Ownership, constraints, implementation and operational value.', 'sptheme' ); ?></strong><p><?php esc_html_e( 'Public work links are included only where they are still useful and appropriate.', 'sptheme' ); ?></p></aside>
	</div>
</section>

<section class="s1-section s1-section-light">
	<div class="sp-container">
		<header class="s1-section-head"><div><p class="s1-eyebrow"><?php esc_html_e( 'Engineering systems', 'sptheme' ); ?></p><h2><?php esc_html_e( 'Projects that best represent my current WordPress direction.', 'sptheme' ); ?></h2></div><p><?php esc_html_e( 'These projects involve custom architecture, structured data, plugin work, integrations, debugging or operational ownership.', 'sptheme' ); ?></p></header>
		<div class="s1-project-featured-grid">
			<?php foreach ( $featured as $project ) : ?>
				<?php
				$category = get_post_meta( $project->ID, '_spcore_category_label', true );
				$summary  = get_post_meta( $project->ID, '_spcore_summary', true );
				$result   = get_post_meta( $project->ID, '_spcore_result', true );
				$stack    = get_post_meta( $project->ID, '_spcore_tech_stack', true );
				?>
				<article>
					<?php if ( $category ) : ?><span><?php echo esc_html( $category ); ?></span><?php endif; ?>
					<h3><a href="<?php echo esc_url( get_permalink( $project ) ); ?>"><?php echo esc_html( get_the_title( $project ) ); ?></a></h3>
					<p><?php echo esc_html( wp_trim_words( (string) $summary, 30 ) ); ?></p>
					<?php if ( $result ) : ?><strong><?php echo esc_html( $result ); ?></strong><?php endif; ?>
					<?php if ( $stack ) : ?><small><?php echo esc_html( $stack ); ?></small><?php endif; ?>
					<a href="<?php echo esc_url( get_permalink( $project ) ); ?>"><?php esc_html_e( 'View project details', 'sptheme' ); ?> </a>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php if ( $delivery ) : ?>
<section class="s1-section">
	<div class="sp-container">
		<header class="s1-section-head"><div><p class="s1-eyebrow"><?php esc_html_e( 'Broader delivery experience', 'sptheme' ); ?></p><h2><?php esc_html_e( 'Selected client websites and commerce work.', 'sptheme' ); ?></h2></div><p><?php esc_html_e( 'These records show range across business websites, ecommerce, nonprofit and international client delivery. They are intentionally presented more compactly.', 'sptheme' ); ?></p></header>
		<div class="s1-project-compact-grid">
			<?php foreach ( $delivery as $project ) : ?>
				<?php
				$industry = get_post_meta( $project->ID, '_spcore_industry_label', true );
				$summary  = get_post_meta( $project->ID, '_spcore_summary', true );
				?>
				<article><span><?php echo esc_html( $industry ? $industry : __( 'WordPress delivery', 'sptheme' ) ); ?></span><h3><a href="<?php echo esc_url( get_permalink( $project ) ); ?>"><?php echo esc_html( get_the_title( $project ) ); ?></a></h3><p><?php echo esc_html( wp_trim_words( (string) $summary, 18 ) ); ?></p></article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
