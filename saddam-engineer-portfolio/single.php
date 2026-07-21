<?php
/**
 * Single technical article template.
 *
 * @package SaddamEngineerPortfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$raw_content   = apply_filters( 'the_content', get_the_content() );
	$prepared      = sptheme_prepare_article_content( $raw_content );
	$article_html  = $prepared['content'];
	$toc           = $prepared['toc'];
	$words         = str_word_count( wp_strip_all_tags( $raw_content ) );
	$reading       = max( 1, (int) ceil( $words / 220 ) );
	$categories    = get_the_category();
	$github        = (string) sp_setting( 'github_url', 'https://github.com/iamsaddamhossen' );
	$related       = sptheme_related_articles( get_the_ID(), 3 );
	$is_affiliate  = (bool) preg_match( '/data-affiliate=["\']1["\']/', $raw_content ) || (bool) get_post_meta( get_the_ID(), '_spcore_has_affiliate', true );
	$published_ts  = (int) get_the_time( 'U' );
	$modified_ts   = (int) get_the_modified_time( 'U' );
	$show_modified = $modified_ts > ( $published_ts + DAY_IN_SECONDS );
	$author_photo  = (string) sp_setting( 'photo_url', '' );
	if ( '' === $author_photo ) {
		$author_photo = get_theme_file_uri( '/assets/images/saddam-hossen-author.webp' );
	}
	?>
	<div class="sp-reading-progress" aria-hidden="true"><span id="sp-reading-progress-bar"></span></div>
	<article <?php post_class( 'sp-article' ); ?>>
		<header class="sp-article-hero">
			<div class="sp-container sp-prose-wide">
				<nav class="sp-breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'sptheme' ); ?>">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'sptheme' ); ?></a><span aria-hidden="true">/</span>
					<a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'Writing', 'sptheme' ); ?></a><span aria-hidden="true">/</span>
					<span aria-current="page"><?php echo esc_html( wp_trim_words( get_the_title(), 7 ) ); ?></span>
				</nav>
				<p class="sp-eyebrow"><?php echo esc_html( $categories ? $categories[0]->name : __( 'Engineering Note', 'sptheme' ) ); ?></p>
				<h1><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?><p class="sp-article-excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>
				<div class="sp-article-meta">
					<span><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></span>
					<?php if ( $show_modified ) : ?><span><?php echo esc_html( sprintf( __( 'Updated %s', 'sptheme' ), get_the_modified_date( 'F j, Y' ) ) ); ?></span><?php endif; ?>
					<span><?php echo esc_html( $reading ); ?> <?php esc_html_e( 'min read', 'sptheme' ); ?></span>
					<span><?php esc_html_e( 'By Saddam Hossen', 'sptheme' ); ?></span>
				</div>
			</div>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="sp-container sp-prose-wide"><figure class="sp-featured"><?php the_post_thumbnail( 'large', array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); ?></figure></div>
		<?php endif; ?>

		<section class="sp-section sp-section-tight sp-article-section">
			<div class="sp-container sp-article-layout">
				<aside class="sp-article-aside" aria-label="<?php esc_attr_e( 'Article navigation', 'sptheme' ); ?>">
					<?php if ( $toc ) : ?>
						<nav class="sp-toc" aria-labelledby="sp-toc-title">
							<p class="sp-toc-label" id="sp-toc-title"><?php esc_html_e( 'In this article', 'sptheme' ); ?></p>
							<ol>
								<?php foreach ( $toc as $item ) : ?>
									<li class="sp-toc-level-<?php echo esc_attr( (string) $item['level'] ); ?>"><a href="#<?php echo esc_attr( $item['id'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a></li>
								<?php endforeach; ?>
							</ol>
						</nav>
					<?php endif; ?>
					<div class="sp-author-card sp-author-card-premium">
						<img class="sp-author-photo" src="<?php echo esc_url( $author_photo ); ?>" alt="<?php esc_attr_e( 'Saddam Hossen, Senior WordPress Engineer', 'sptheme' ); ?>" width="160" height="160" loading="lazy" decoding="async" />
						<div class="sp-author-copy">
							<p class="sp-author-kicker"><?php esc_html_e( 'Written by', 'sptheme' ); ?></p>
							<strong><?php esc_html_e( 'Saddam Hossen', 'sptheme' ); ?></strong>
							<span><?php esc_html_e( 'Senior WordPress Engineer', 'sptheme' ); ?></span>
						</div>
						<p><?php esc_html_e( 'I write from hands-on WordPress delivery, technical support and production debugging experience. Each note documents a practical decision, trade-off or workflow I can explain and apply.', 'sptheme' ); ?></p>
						<a class="sp-author-link" href="<?php echo esc_url( home_url( '/engineering/' ) ); ?>"><?php esc_html_e( 'View engineering profile', 'sptheme' ); ?></a>
					</div>
				</aside>

				<div class="sp-content sp-prose" id="article-content">
					<?php if ( $is_affiliate ) : ?>
						<div class="sp-affiliate-note"><strong><?php esc_html_e( 'Affiliate disclosure:', 'sptheme' ); ?></strong> <?php echo esc_html( sp_setting( 'affiliate_disclosure', 'Some resource links may be affiliate links. A commission does not change the price you pay.' ) ); ?></div>
					<?php endif; ?>
					<?php echo $article_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Filtered WordPress post content. ?>

					<div class="sp-article-cta">
						<div><p class="sp-eyebrow"><?php esc_html_e( 'Related implementation', 'sptheme' ); ?></p><h2><?php esc_html_e( 'See how the ideas map to working code.', 'sptheme' ); ?></h2><p><?php esc_html_e( 'The public repository connects the article’s architecture, security, REST, blocks, CLI, privacy, testing and CI concepts to working code.', 'sptheme' ); ?></p></div>
						<div class="sp-article-cta-actions"><a class="sp-btn sp-btn-primary" href="<?php echo esc_url( home_url( '/engineering/' ) ); ?>"><?php esc_html_e( 'Explore Engineering System', 'sptheme' ); ?></a><a class="sp-btn sp-btn-secondary" href="<?php echo esc_url( $github ); ?>" target="_blank" rel="noopener noreferrer">GitHub </a></div>
					</div>

					<?php if ( $related ) : ?>
						<section class="sp-related-reading" aria-labelledby="sp-related-title">
							<p class="sp-eyebrow"><?php esc_html_e( 'Continue learning', 'sptheme' ); ?></p>
							<h2 id="sp-related-title"><?php esc_html_e( 'Related WordPress engineering notes', 'sptheme' ); ?></h2>
							<div class="sp-related-grid">
								<?php foreach ( $related as $related_post ) : ?>
									<article><span><?php echo esc_html( get_the_date( 'M Y', $related_post ) ); ?></span><h3><a href="<?php echo esc_url( get_permalink( $related_post ) ); ?>"><?php echo esc_html( get_the_title( $related_post ) ); ?></a></h3><p><?php echo esc_html( wp_trim_words( get_the_excerpt( $related_post ), 22 ) ); ?></p><a class="sp-link" href="<?php echo esc_url( get_permalink( $related_post ) ); ?>"><?php esc_html_e( 'Read article', 'sptheme' ); ?> </a></article>
								<?php endforeach; ?>
							</div>
						</section>
					<?php endif; ?>

					<?php if ( ! sp_setting( 'comments_disable', 1 ) && ( comments_open() || get_comments_number() ) ) : ?>
						<?php comments_template(); ?>
					<?php endif; ?>
				</div>
			</div>
		</section>
	</article>
	<?php
endwhile;

get_footer();
