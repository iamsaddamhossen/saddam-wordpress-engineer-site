<?php
/**
 * Lightweight technical SEO and AI-discovery foundations.
 *
 * @package SaddamEngineerCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class SPCore_SEO {

	/** @var self|null */
	private static $instance = null;

	/** Return singleton. */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/** Register hooks. */
	private function __construct() {
		add_action( 'wp_head', array( $this, 'meta' ), 2 );
		add_filter( 'pre_get_document_title', array( $this, 'document_title' ) );
		add_action( 'init', array( $this, 'llms_rewrite' ) );
		add_action( 'template_redirect', array( $this, 'llms_output' ) );
		add_filter( 'robots_txt', array( $this, 'robots_txt' ), 10, 2 );
		add_filter( 'wp_robots', array( $this, 'robots_meta' ) );
		add_filter( 'the_content', array( $this, 'affiliate_rel' ), 20 );
	}

	/** Detect common dedicated SEO plugins to prevent duplicate metadata. */
	private function seo_plugin_active() {
		return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'AIOSEO_VERSION' );
	}

	/** Output metadata only when another SEO plugin does not own it. */
	public function meta() {
		if ( $this->seo_plugin_active() || is_404() || is_search() ) {
			return;
		}

		$title = $this->title();
		$desc  = $this->description();
		$url   = $this->current_url();
		$image = $this->image();
		$type  = is_singular( array( 'post', 'sp_project', 'sp_case_study' ) ) ? 'article' : 'website';

		echo "\n";
		if ( '' !== $desc ) {
			printf( '<meta name="description" content="%s" />' . "\n", esc_attr( $desc ) );
		}
		if ( '' !== $url ) {
			printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( $url ) );
		}

		printf( '<meta property="og:locale" content="%s" />' . "\n", esc_attr( str_replace( '-', '_', get_bloginfo( 'language' ) ) ) );
		printf( '<meta property="og:type" content="%s" />' . "\n", esc_attr( $type ) );
		printf( '<meta property="og:title" content="%s" />' . "\n", esc_attr( $title ) );
		if ( '' !== $desc ) {
			printf( '<meta property="og:description" content="%s" />' . "\n", esc_attr( $desc ) );
		}
		printf( '<meta property="og:url" content="%s" />' . "\n", esc_url( $url ) );
		printf( '<meta property="og:site_name" content="%s" />' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
		if ( $image ) {
			printf( '<meta property="og:image" content="%s" />' . "\n", esc_url( $image ) );
			printf( '<meta property="og:image:alt" content="%s" />' . "\n", esc_attr( $title ) );
		}
		if ( 'article' === $type ) {
			printf( '<meta property="article:published_time" content="%s" />' . "\n", esc_attr( get_the_date( DATE_W3C ) ) );
			printf( '<meta property="article:modified_time" content="%s" />' . "\n", esc_attr( get_the_modified_date( DATE_W3C ) ) );
			printf( '<meta property="article:author" content="%s" />' . "\n", esc_url( home_url( '/about/' ) ) );
		}

		printf( '<meta name="twitter:card" content="%s" />' . "\n", $image ? 'summary_large_image' : 'summary' );
		printf( '<meta name="twitter:title" content="%s" />' . "\n", esc_attr( $title ) );
		if ( '' !== $desc ) {
			printf( '<meta name="twitter:description" content="%s" />' . "\n", esc_attr( $desc ) );
		}
		if ( $image ) {
			printf( '<meta name="twitter:image" content="%s" />' . "\n", esc_url( $image ) );
		}
	}

	/** Use the contextual title when a dedicated SEO plugin is not active.
	 *
	 * @param string $title Existing document title.
	 * @return string
	 */
	public function document_title( $title ) {
		if ( $this->seo_plugin_active() ) {
			return $title;
		}
		return $this->title();
	}

	/** Contextual title. */
	private function title() {
		if ( is_front_page() ) {
			return (string) spcore_get( 'meta_title' );
		}
		if ( is_singular() ) {
			return get_the_title() . ' ,  ' . get_bloginfo( 'name' );
		}
		if ( is_home() ) {
			return __( 'WordPress Engineering Articles', 'spcore' ) . ' ,  ' . get_bloginfo( 'name' );
		}
		if ( is_post_type_archive() ) {
			return post_type_archive_title( '', false ) . ' ,  ' . get_bloginfo( 'name' );
		}
		if ( is_category() || is_tag() || is_tax() ) {
			return single_term_title( '', false ) . ' ,  ' . get_bloginfo( 'name' );
		}
		return (string) spcore_get( 'meta_title' );
	}

	/** Contextual description with a human-written excerpt preference. */
	private function description() {
		if ( is_singular() ) {
			$excerpt = trim( (string) get_the_excerpt() );
			if ( '' === $excerpt ) {
				$excerpt = wp_strip_all_tags( strip_shortcodes( (string) get_post_field( 'post_content', get_queried_object_id() ) ) );
			}
			return wp_trim_words( $excerpt, 32, '…' );
		}
		if ( is_home() ) {
			return __( 'Practical WordPress engineering articles covering custom plugins, REST APIs, Gutenberg, production debugging, performance, technical SEO and AI-search readiness.', 'spcore' );
		}
		if ( is_post_type_archive( 'sp_project' ) ) {
			return __( 'Production WordPress projects explained through technical responsibility, constraints, implementation choices and observable outcomes.', 'spcore' );
		}
		if ( is_post_type_archive( 'sp_case_study' ) ) {
			return __( 'WordPress engineering case studies covering multilingual platforms, plugin architecture, debugging and maintainable delivery.', 'spcore' );
		}
		if ( is_category() || is_tag() || is_tax() ) {
			$description = trim( wp_strip_all_tags( term_description() ) );
			if ( $description ) {
				return wp_trim_words( $description, 32, '…' );
			}
		}
		return (string) spcore_get( 'meta_description' );
	}

	/** Canonical URL without tracking parameters. */
	private function current_url() {
		if ( is_front_page() ) {
			return home_url( '/' );
		}
		if ( is_singular() ) {
			return (string) get_permalink();
		}
		if ( is_home() ) {
			$page_for_posts = absint( get_option( 'page_for_posts' ) );
			return $page_for_posts ? (string) get_permalink( $page_for_posts ) : home_url( '/blog/' );
		}
		if ( is_post_type_archive() ) {
			return (string) get_post_type_archive_link( get_query_var( 'post_type' ) );
		}
		if ( is_category() || is_tag() || is_tax() ) {
			$link = get_term_link( get_queried_object() );
			return is_wp_error( $link ) ? '' : (string) $link;
		}
		return home_url( '/' );
	}

	/** Resolve share image from post, setting or site icon. */
	private function image() {
		if ( is_singular() && has_post_thumbnail() ) {
			return (string) get_the_post_thumbnail_url( get_queried_object_id(), 'large' );
		}
		$image = (string) spcore_get( 'social_image' );
		if ( $image ) {
			return $image;
		}
		$icon_id = absint( get_option( 'site_icon' ) );
		return $icon_id ? (string) wp_get_attachment_image_url( $icon_id, 'full' ) : '';
	}

	/** Add noindex to thin or duplicate utility archives. */
	public function robots_meta( $robots ) {
		if ( is_search() || is_404() || is_author() || is_date() || is_attachment() ) {
			$robots['noindex'] = true;
			$robots['follow']  = true;
			unset( $robots['index'] );
		} else {
			$robots['max-image-preview'] = 'large';
			$robots['max-snippet']       = -1;
			$robots['max-video-preview'] = -1;
		}
		return $robots;
	}

	/** Register the experimental llms.txt route. */
	public function llms_rewrite() {
		add_rewrite_rule( '^llms\.txt$', 'index.php?spcore_llms=1', 'top' );
		add_rewrite_tag( '%spcore_llms%', '1' );
	}

	/** Plain-text site overview for compatible AI systems. */
	public function llms_output() {
		if ( '1' !== get_query_var( 'spcore_llms' ) ) {
			return;
		}
		nocache_headers();
		header( 'Content-Type: text/plain; charset=utf-8' );
		$name = (string) spcore_get( 'name' );
		$site = home_url( '/' );
		echo '# ' . sanitize_text_field( $name ) . "\n\n";
		echo "> Experimental machine-readable site summary. Standard crawling, sitemaps, canonical URLs and human-readable pages remain the source of truth.\n\n";
		echo "## Professional summary\n" . wp_strip_all_tags( (string) spcore_get( 'bio' ) ) . "\n\n";
		echo "## Engineering focus\n";
		echo "- Custom WordPress plugin architecture and OOP PHP\n- WooCommerce and Gutenberg engineering\n- REST APIs, integrations and WP-CLI\n- Performance, security and production debugging\n- Technical support and incident ownership\n- Technical SEO and AI-search readiness\n\n";
		echo "## Primary pages\n";
		echo '- Home: ' . esc_url_raw( $site ) . "\n";
		echo '- Engineering evidence: ' . esc_url_raw( home_url( '/engineering/' ) ) . "\n";
		echo '- Projects: ' . esc_url_raw( get_post_type_archive_link( 'sp_project' ) ) . "\n";
		echo '- Case studies: ' . esc_url_raw( get_post_type_archive_link( 'sp_case_study' ) ) . "\n";
		echo '- Technical writing: ' . esc_url_raw( home_url( '/blog/' ) ) . "\n";
		echo '- Recommended tools: ' . esc_url_raw( home_url( '/recommended-tools/' ) ) . "\n";
		echo '- Contact: ' . esc_url_raw( home_url( '/contact/' ) ) . "\n\n";
		echo "## Public profiles\n";
		foreach ( array( 'github_url' => 'GitHub', 'linkedin_url' => 'LinkedIn', 'upwork_url' => 'Upwork' ) as $key => $label ) {
			$url = (string) spcore_get( $key );
			if ( $url ) {
				echo '- ' . $label . ': ' . esc_url_raw( $url ) . "\n";
			}
		}
		echo "\n## Contact\n" . esc_html( $name ) . ' ,  ' . sanitize_email( (string) spcore_get( 'email' ) ) . "\n";
		exit;
	}

	/** Append standard sitemap discovery to robots.txt. */
	public function robots_txt( $output, $public ) {
		if ( $public ) {
			$output .= "\nSitemap: " . home_url( '/wp-sitemap.xml' ) . "\n";
		}
		return $output;
	}

	/** Merge sponsored relationship tokens into marked affiliate links. */
	public function affiliate_rel( $content ) {
		if ( ! spcore_get( 'affiliate_nofollow', 1 ) || false === strpos( $content, 'data-affiliate' ) ) {
			return $content;
		}
		return (string) preg_replace_callback(
			'/<a\s([^>]*data-affiliate=["\']1["\'][^>]*)>/i',
			static function ( $matches ) {
				$attrs = $matches[1];
				$tokens = array( 'sponsored', 'nofollow', 'noopener' );
				if ( preg_match( '/\srel=["\']([^"\']*)["\']/i', $attrs, $rel_match ) ) {
					$current = preg_split( '/\s+/', strtolower( trim( $rel_match[1] ) ) ) ?: array();
					$rel     = implode( ' ', array_unique( array_filter( array_merge( $current, $tokens ) ) ) );
					$attrs   = preg_replace( '/\srel=["\'][^"\']*["\']/i', ' rel="' . esc_attr( $rel ) . '"', $attrs, 1 );
				} else {
					$attrs .= ' rel="sponsored nofollow noopener"';
				}
				return '<a ' . $attrs . '>';
			},
			(string) $content
		);
	}
}
