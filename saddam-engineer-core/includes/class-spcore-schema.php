<?php
/**
 * Contextual JSON-LD that mirrors visible site content.
 *
 * @package SaddamEngineerCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class SPCore_Schema {

	/** @var self|null */
	private static $instance = null;

	/** Return singleton. */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/** Register output. */
	private function __construct() {
		add_action( 'wp_head', array( $this, 'output' ), 20 );
	}

	/** Avoid duplicate graphs from a dedicated SEO plugin. */
	private function schema_plugin_active() {
		return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'AIOSEO_VERSION' );
	}

	/** Build the graph. */
	private function graph() {
		$site  = trailingslashit( home_url( '/' ) );
		$graph = array( $this->website( $site ), $this->person( $site ) );

		if ( is_front_page() || is_page( array( 'about', 'engineering', 'experience' ) ) ) {
			$graph[] = $this->profile_page( $site );
		} elseif ( is_singular( 'post' ) ) {
			$graph[] = $this->article( $site );
			$graph[] = $this->breadcrumbs();
		} elseif ( is_singular() ) {
			$graph[] = $this->webpage( $site );
			$graph[] = $this->breadcrumbs();
		} elseif ( is_home() || is_post_type_archive() || is_category() || is_tag() || is_tax() ) {
			$graph[] = $this->collection_page( $site );
		}


		return array(
			'@context' => 'https://schema.org',
			'@graph'   => array_values( array_filter( $graph ) ),
		);
	}

	/** Website entity. */
	private function website( $site ) {
		return array(
			'@type'       => 'WebSite',
			'@id'         => $site . '#website',
			'url'         => $site,
			'name'        => get_bloginfo( 'name' ),
			'description' => (string) spcore_get( 'meta_description' ),
			'inLanguage'  => get_bloginfo( 'language' ),
			'publisher'   => array( '@id' => $site . '#person' ),
		);
	}

	/** Person entity grounded in settings. */
	private function person( $site ) {
		$person = array(
			'@type'       => 'Person',
			'@id'         => $site . '#person',
			'name'        => (string) spcore_get( 'name' ),
			'url'         => $site,
			'jobTitle'    => 'Senior WordPress Engineer',
			'description' => (string) spcore_get( 'bio' ),
			'email'       => 'mailto:' . sanitize_email( (string) spcore_get( 'email' ) ),
			'knowsAbout'  => array( 'WordPress', 'PHP', 'WooCommerce', 'Gutenberg', 'REST APIs', 'WP-CLI', 'Web performance', 'Production debugging' ),
		);
		$same_as = array_values( array_filter( array_map( 'esc_url_raw', array( spcore_get( 'github_url' ), spcore_get( 'linkedin_url' ), spcore_get( 'upwork_url' ) ) ) ) );
		if ( $same_as ) {
			$person['sameAs'] = $same_as;
		}
		$image = (string) spcore_get( 'photo_url' );
		if ( ! $image && function_exists( 'get_theme_file_path' ) && function_exists( 'get_theme_file_uri' ) ) {
			$photo_path = get_theme_file_path( '/assets/images/saddam-hossen-author.webp' );
			if ( is_string( $photo_path ) && file_exists( $photo_path ) ) {
				$image = get_theme_file_uri( '/assets/images/saddam-hossen-author.webp' );
			}
		}
		if ( $image ) {
			$person['image'] = esc_url_raw( $image );
		}
		return $person;
	}

	/** Profile page entity. */
	private function profile_page( $site ) {
		$url = is_front_page() ? $site : (string) get_permalink();
		return array(
			'@type'      => 'ProfilePage',
			'@id'        => $url . '#profilepage',
			'url'        => $url,
			'name'       => is_front_page() ? (string) spcore_get( 'meta_title' ) : get_the_title(),
			'description'=> $this->description(),
			'inLanguage' => get_bloginfo( 'language' ),
			'mainEntity' => array( '@id' => $site . '#person' ),
			'isPartOf'   => array( '@id' => $site . '#website' ),
		);
	}

	/** BlogPosting entity. */
	private function article( $site ) {
		$article = array(
			'@type'            => 'BlogPosting',
			'@id'              => get_permalink() . '#article',
			'headline'         => get_the_title(),
			'description'      => $this->description(),
			'datePublished'    => get_the_date( DATE_W3C ),
			'dateModified'     => get_the_modified_date( DATE_W3C ),
			'mainEntityOfPage' => array( '@type' => 'WebPage', '@id' => get_permalink() ),
			'author'           => array( '@id' => $site . '#person' ),
			'publisher'        => array( '@id' => $site . '#person' ),
			'inLanguage'       => get_bloginfo( 'language' ),
			'isPartOf'         => array( '@id' => $site . '#website' ),
		);
		$image = $this->image();
		if ( $image ) {
			$article['image'] = array( $image );
		}
		return $article;
	}

	/** Generic contextual WebPage. */
	private function webpage( $site ) {
		return array(
			'@type'       => 'WebPage',
			'@id'         => get_permalink() . '#webpage',
			'url'         => get_permalink(),
			'name'        => get_the_title(),
			'description' => $this->description(),
			'inLanguage'  => get_bloginfo( 'language' ),
			'isPartOf'    => array( '@id' => $site . '#website' ),
			'about'       => array( '@id' => $site . '#person' ),
		);
	}

	/** Archive collection entity. */
	private function collection_page( $site ) {
		if ( is_home() ) {
			$page_id = absint( get_option( 'page_for_posts' ) );
			$url     = $page_id ? get_permalink( $page_id ) : home_url( '/blog/' );
			$name    = __( 'WordPress Engineering Articles', 'spcore' );
		} elseif ( is_post_type_archive() ) {
			$url  = get_post_type_archive_link( get_query_var( 'post_type' ) );
			$name = post_type_archive_title( '', false );
		} else {
			$link = get_term_link( get_queried_object() );
			$url  = is_wp_error( $link ) ? home_url( '/' ) : $link;
			$name = single_term_title( '', false );
		}
		return array(
			'@type'       => 'CollectionPage',
			'@id'         => $url . '#collection',
			'url'         => $url,
			'name'        => $name,
			'description' => $this->description(),
			'inLanguage'  => get_bloginfo( 'language' ),
			'isPartOf'    => array( '@id' => $site . '#website' ),
		);
	}

	/** Hierarchical BreadcrumbList. */
	private function breadcrumbs() {
		$items = array(
			array( '@type' => 'ListItem', 'position' => 1, 'name' => __( 'Home', 'spcore' ), 'item' => home_url( '/' ) ),
		);
		if ( is_singular( 'post' ) ) {
			$items[] = array( '@type' => 'ListItem', 'position' => 2, 'name' => __( 'Writing', 'spcore' ), 'item' => home_url( '/blog/' ) );
		} elseif ( is_singular( 'sp_project' ) ) {
			$items[] = array( '@type' => 'ListItem', 'position' => 2, 'name' => __( 'Projects', 'spcore' ), 'item' => get_post_type_archive_link( 'sp_project' ) );
		} elseif ( is_singular( 'sp_case_study' ) ) {
			$items[] = array( '@type' => 'ListItem', 'position' => 2, 'name' => __( 'Case Studies', 'spcore' ), 'item' => get_post_type_archive_link( 'sp_case_study' ) );
		}
		$items[] = array( '@type' => 'ListItem', 'position' => count( $items ) + 1, 'name' => get_the_title(), 'item' => get_permalink() );
		return array( '@type' => 'BreadcrumbList', 'itemListElement' => $items );
	}

	/** Description fallback. */
	private function description() {
		$excerpt = is_singular() ? trim( (string) get_the_excerpt() ) : '';
		return $excerpt ? wp_trim_words( wp_strip_all_tags( $excerpt ), 34, '…' ) : (string) spcore_get( 'meta_description' );
	}

	/** Image fallback. */
	private function image() {
		if ( is_singular() && has_post_thumbnail() ) {
			return (string) get_the_post_thumbnail_url( get_the_ID(), 'full' );
		}
		$image = (string) spcore_get( 'social_image' );
		if ( $image ) {
			return esc_url_raw( $image );
		}
		$icon_id = absint( get_option( 'site_icon' ) );
		return $icon_id ? (string) wp_get_attachment_image_url( $icon_id, 'full' ) : '';
	}

	/** Print graph. */
	public function output() {
		if ( $this->schema_plugin_active() || is_404() || is_search() ) {
			return;
		}
		echo "\n<script type=\"application/ld+json\">" . wp_json_encode( $this->graph(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
