<?php
/**
 * Recruiter-focused front page.
 *
 * @package SaddamEngineerPortfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$name          = (string) sp_setting( 'name', 'Saddam Hossen' );
$status        = (string) sp_setting( 'home_status', 'Available for remote WordPress engineering roles' );
$hero_title    = (string) sp_setting( 'home_hero_title', 'I build reliable WordPress systems for production teams.' );
$hero_intro    = (string) sp_setting( 'home_hero_intro', 'Custom plugins, WooCommerce, Gutenberg, REST APIs, performance and production debugging.' );
$primary_label = (string) sp_setting( 'home_primary_label', 'Explore Engineering' );
$second_label  = (string) sp_setting( 'home_secondary_label', 'Read Case Studies' );
$github        = (string) sp_setting( 'github_url', 'https://github.com/iamsaddamhossen' );
$case_url      = get_post_type_archive_link( 'sp_case_study' );
$project_url   = get_post_type_archive_link( 'sp_project' );

$cases = get_posts(
	array(
		'post_type'      => 'sp_case_study',
		'post_status'    => 'publish',
		'posts_per_page' => 4,
		'orderby'        => 'menu_order date',
		'order'          => 'ASC',
	)
);

$articles = get_posts(
	array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 3,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
?>

<section class="s1-hero" aria-labelledby="s1-hero-title">
	<div class="sp-container s1-hero-grid">
		<div class="s1-hero-copy">
			<p class="s1-status"><span aria-hidden="true"></span><?php echo esc_html( $status ); ?></p>
			<p class="s1-kicker"><?php echo esc_html( $name ); ?> · Senior WordPress Engineer</p>
			<h1 id="s1-hero-title"><?php echo esc_html( $hero_title ); ?></h1>
			<p class="s1-hero-intro"><?php echo esc_html( $hero_intro ); ?></p>
			<div class="s1-actions">
				<a class="s1-btn s1-btn-primary" href="<?php echo esc_url( home_url( '/engineering/' ) ); ?>"><?php echo esc_html( $primary_label ); ?></a>
				<a class="s1-btn s1-btn-secondary" href="<?php echo esc_url( $case_url ? $case_url : home_url( '/case-studies/' ) ); ?>"><?php echo esc_html( $second_label ); ?></a>
			</div>
			<dl class="s1-proof-row" aria-label="<?php esc_attr_e( 'Professional proof', 'sptheme' ); ?>">
				<div><dt><?php esc_html_e( 'Since 2017', 'sptheme' ); ?></dt><dd><?php esc_html_e( 'Production web development', 'sptheme' ); ?></dd></div>
				<div><dt><?php esc_html_e( 'Top Rated', 'sptheme' ); ?></dt><dd><?php esc_html_e( 'Upwork and international clients', 'sptheme' ); ?></dd></div>
				<div><dt><?php esc_html_e( 'Remote experience', 'sptheme' ); ?></dt><dd><?php esc_html_e( 'Delivery, support and async communication', 'sptheme' ); ?></dd></div>
			</dl>
		</div>

		<aside class="s1-ownership-card" aria-label="<?php esc_attr_e( 'Production ownership model', 'sptheme' ); ?>">
			<div class="s1-card-head">
				<div><span><?php esc_html_e( 'Production ownership', 'sptheme' ); ?></span><h2><?php esc_html_e( 'Understand the system before changing it.', 'sptheme' ); ?></h2></div>
				<b><?php esc_html_e( 'Available', 'sptheme' ); ?></b>
			</div>
			<ol class="s1-ownership-list">
				<li><span>01</span><div><strong><?php esc_html_e( 'Clarify the boundary', 'sptheme' ); ?></strong><p><?php esc_html_e( 'Requirements, data ownership, permissions and failure paths.', 'sptheme' ); ?></p></div></li>
				<li><span>02</span><div><strong><?php esc_html_e( 'Build the smallest safe change', 'sptheme' ); ?></strong><p><?php esc_html_e( 'Native APIs, maintainable structure and explicit trade-offs.', 'sptheme' ); ?></p></div></li>
				<li><span>03</span><div><strong><?php esc_html_e( 'Verify and hand off', 'sptheme' ); ?></strong><p><?php esc_html_e( 'Checks, QA, rollback thinking and concise documentation.', 'sptheme' ); ?></p></div></li>
			</ol>
			<div class="s1-stack"><span>PHP</span><span>WordPress</span><span>WooCommerce</span><span>Gutenberg</span><span>REST</span><span>WP-CLI</span></div>
			<div class="s1-card-links">
				<a href="<?php echo esc_url( home_url( '/wp-json/spcore/v1/engineering' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Live REST endpoint', 'sptheme' ); ?> </a>
				<a href="<?php echo esc_url( $github ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Source code', 'sptheme' ); ?> </a>
			</div>
		</aside>
	</div>
</section>

<section class="s1-section s1-section-light" id="capabilities">
	<div class="sp-container">
		<header class="s1-section-head">
			<div><p class="s1-eyebrow"><?php esc_html_e( 'What I own', 'sptheme' ); ?></p><h2><?php esc_html_e( 'WordPress engineering beyond page assembly.', 'sptheme' ); ?></h2></div>
			<p><?php esc_html_e( 'I work across the layers that make a WordPress product dependable: domain logic, editorial workflows, integrations, browser behavior and production operations.', 'sptheme' ); ?></p>
		</header>
		<div class="s1-pillar-grid">
			<article><span>01</span><h3><?php esc_html_e( 'Plugin and platform engineering', 'sptheme' ); ?></h3><p><?php esc_html_e( 'Custom plugins, data models, settings, background work, admin workflows and clear ownership boundaries.', 'sptheme' ); ?></p><small>PHP · OOP · MySQL · Hooks · WP-CLI</small></article>
			<article><span>02</span><h3><?php esc_html_e( 'WooCommerce and integrations', 'sptheme' ); ?></h3><p><?php esc_html_e( 'Checkout, catalog, operational workflows, remote APIs, webhooks and recoverable external-service failures.', 'sptheme' ); ?></p><small>Commerce · REST · Webhooks · Payments</small></article>
			<article><span>03</span><h3><?php esc_html_e( 'Gutenberg and editor experience', 'sptheme' ); ?></h3><p><?php esc_html_e( 'Native blocks, patterns, structured fields and safe editing workflows for developers and non-technical teams.', 'sptheme' ); ?></p><small>React · block.json · Patterns · Accessibility</small></article>
			<article><span>04</span><h3><?php esc_html_e( 'Performance, security and support', 'sptheme' ); ?></h3><p><?php esc_html_e( 'Root-cause debugging, query and cache analysis, deployment safety, incident communication and defensible controls.', 'sptheme' ); ?></p><small>Profiling · Site Health · Privacy · CI</small></article>
		</div>
	</div>
</section>

<section class="s1-section s1-section-dark" id="work">
	<div class="sp-container">
		<header class="s1-section-head s1-section-head-dark">
			<div><p class="s1-eyebrow"><?php esc_html_e( 'Selected systems', 'sptheme' ); ?></p><h2><?php esc_html_e( 'Real constraints, technical decisions and accountable outcomes.', 'sptheme' ); ?></h2></div>
			<p><?php esc_html_e( 'The case studies explain what the system needed, what I owned, why a technical direction was chosen and how risk was controlled.', 'sptheme' ); ?></p>
		</header>
		<div class="s1-case-grid">
			<?php foreach ( $cases as $case ) : ?>
				<?php
				$metric    = get_post_meta( $case->ID, '_spcore_proof_metric', true );
				$challenge = get_post_meta( $case->ID, '_spcore_challenge', true );
				?>
				<article class="s1-case-card">
					<?php if ( $metric ) : ?><p class="s1-case-metric"><?php echo esc_html( $metric ); ?></p><?php endif; ?>
					<h3><a href="<?php echo esc_url( get_permalink( $case ) ); ?>"><?php echo esc_html( get_the_title( $case ) ); ?></a></h3>
					<p><?php echo esc_html( wp_trim_words( (string) $challenge, 26 ) ); ?></p>
					<a href="<?php echo esc_url( get_permalink( $case ) ); ?>"><?php esc_html_e( 'Read case study', 'sptheme' ); ?> </a>
				</article>
			<?php endforeach; ?>
		</div>
		<div class="s1-section-actions">
			<a class="s1-btn s1-btn-primary" href="<?php echo esc_url( $case_url ? $case_url : home_url( '/case-studies/' ) ); ?>"><?php esc_html_e( 'All Case Studies', 'sptheme' ); ?></a>
			<a class="s1-link-light" href="<?php echo esc_url( $project_url ? $project_url : home_url( '/projects/' ) ); ?>"><?php esc_html_e( 'Browse selected projects', 'sptheme' ); ?> </a>
		</div>
	</div>
</section>

<section class="s1-section s1-section-editor" id="editor-experience">
	<div class="sp-container s1-editor-grid">
		<div class="s1-editor-copy">
			<p class="s1-eyebrow"><?php esc_html_e( 'Built for maintainers and editors', 'sptheme' ); ?></p>
			<h2><?php esc_html_e( 'Custom architecture should not make everyday editing difficult.', 'sptheme' ); ?></h2>
			<p><?php esc_html_e( 'A non-technical client should be able to update text, images and repeatable content without touching templates. I use native WordPress editing tools and reserve custom code for the parts that genuinely need it.', 'sptheme' ); ?></p>
			<a class="s1-btn s1-btn-dark" href="<?php echo esc_url( home_url( '/client-editable-wordpress-without-page-builder/' ) ); ?>"><?php esc_html_e( 'Read the editing architecture guide', 'sptheme' ); ?></a>
		</div>
		<div class="s1-editor-model">
			<article><b>01</b><div><h3><?php esc_html_e( 'Blocks for everyday content', 'sptheme' ); ?></h3><p><?php esc_html_e( 'Editors update copy, images, lists and simple layouts in the native editor.', 'sptheme' ); ?></p></div></article>
			<article><b>02</b><div><h3><?php esc_html_e( 'Patterns for reusable sections', 'sptheme' ); ?></h3><p><?php esc_html_e( 'Approved hero, CTA, feature and content layouts stay consistent without rebuilding them.', 'sptheme' ); ?></p></div></article>
			<article><b>03</b><div><h3><?php esc_html_e( 'Structured fields for repeatable records', 'sptheme' ); ?></h3><p><?php esc_html_e( 'Projects, services and case studies use clear fields instead of fragile free-form pages.', 'sptheme' ); ?></p></div></article>
			<article><b>04</b><div><h3><?php esc_html_e( 'Custom blocks only where needed', 'sptheme' ); ?></h3><p><?php esc_html_e( 'Complex interactive features keep a stable data contract and an editor-friendly interface.', 'sptheme' ); ?></p></div></article>
		</div>
	</div>
</section>

<section class="s1-section s1-section-light" id="experience">
	<div class="sp-container">
		<header class="s1-section-head">
			<div><p class="s1-eyebrow"><?php esc_html_e( 'Professional background', 'sptheme' ); ?></p><h2><?php esc_html_e( 'Delivery, support and frontend experience in one engineering practice.', 'sptheme' ); ?></h2></div>
			<p><?php esc_html_e( 'That combination helps me investigate beyond a single code layer and communicate clearly with product teams, clients and support stakeholders.', 'sptheme' ); ?></p>
		</header>
		<div class="s1-role-grid">
			<article><span><?php esc_html_e( 'Current', 'sptheme' ); ?></span><h3><?php esc_html_e( 'Independent WordPress Engineer', 'sptheme' ); ?></h3><p><?php esc_html_e( 'Custom development, WooCommerce, debugging, performance and ongoing support for international clients.', 'sptheme' ); ?></p></article>
			<article><span><?php esc_html_e( 'Previous remote role', 'sptheme' ); ?></span><h3><?php esc_html_e( 'WordPress Technical Support Engineer', 'sptheme' ); ?></h3><p><?php esc_html_e( 'Reproduced incidents, isolated conflicts, interpreted logs and translated findings into safe next steps.', 'sptheme' ); ?></p></article>
			<article><span><?php esc_html_e( 'Foundation', 'sptheme' ); ?></span><h3><?php esc_html_e( 'Frontend Developer', 'sptheme' ); ?></h3><p><?php esc_html_e( 'Built responsive interfaces and developed strong browser, JavaScript, CSS and accessibility fundamentals.', 'sptheme' ); ?></p></article>
		</div>
		<p class="s1-centered-action"><a class="s1-btn s1-btn-dark" href="<?php echo esc_url( home_url( '/experience/' ) ); ?>"><?php esc_html_e( 'View Full Experience', 'sptheme' ); ?></a></p>
	</div>
</section>

<?php if ( $articles ) : ?>
<section class="s1-section s1-section-writing">
	<div class="sp-container">
		<header class="s1-section-head">
			<div><p class="s1-eyebrow"><?php esc_html_e( 'Technical writing', 'sptheme' ); ?></p><h2><?php esc_html_e( 'Practical WordPress notes built around decisions and examples.', 'sptheme' ); ?></h2></div>
			<p><?php esc_html_e( 'The writing is designed to be useful during real implementation work: architecture, security, APIs, debugging, editor experience and search foundations.', 'sptheme' ); ?></p>
		</header>
		<div class="s1-writing-grid">
			<?php foreach ( $articles as $article ) : ?>
				<article>
					<p class="s1-writing-meta"><?php echo esc_html( get_the_date( '', $article ) ); ?> · <?php echo esc_html( max( 1, (int) ceil( str_word_count( wp_strip_all_tags( $article->post_content ) ) / 220 ) ) ); ?> min</p>
					<h3><a href="<?php echo esc_url( get_permalink( $article ) ); ?>"><?php echo esc_html( get_the_title( $article ) ); ?></a></h3>
					<p><?php echo esc_html( wp_trim_words( get_the_excerpt( $article ), 24 ) ); ?></p>
					<a href="<?php echo esc_url( get_permalink( $article ) ); ?>"><?php esc_html_e( 'Read article', 'sptheme' ); ?> </a>
				</article>
			<?php endforeach; ?>
		</div>
		<p class="s1-centered-action"><a class="s1-btn s1-btn-secondary-dark" href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'Browse All Technical Writing', 'sptheme' ); ?></a></p>
	</div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
