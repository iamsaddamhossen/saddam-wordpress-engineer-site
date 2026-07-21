<?php
/**
 * Theme functions.
 *
 * @package SaddamEngineerPortfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SPTHEME_VERSION', '1.0.0' );
define( 'SPTHEME_DIR', get_template_directory() );
define( 'SPTHEME_URI', get_template_directory_uri() );

/**
 * Configure theme features and editor support.
 *
 * @return void
 */
function sptheme_setup() {
	load_theme_textdomain( 'sptheme', SPTHEME_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'appearance-tools' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor-style.css' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 48,
			'width'       => 220,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support(
		'custom-background',
		array(
			'default-color' => 'f7f8fa',
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'sptheme' ),
			'footer'  => __( 'Footer Menu', 'sptheme' ),
		)
	);

	add_image_size( 'sp-card', 720, 460, true );
}
add_action( 'after_setup_theme', 'sptheme_setup' );

/** Register a dedicated pattern category for client-editable layouts. */
function sptheme_pattern_category() {
	if ( function_exists( 'register_block_pattern_category' ) ) {
		register_block_pattern_category(
			'saddam-engineer',
			array( 'label' => __( 'Saddam Engineer Sections', 'sptheme' ) )
		);
	}
}
add_action( 'init', 'sptheme_pattern_category' );

/**
 * Return a file version based on modification time for reliable cache busting.
 *
 * @param string $relative Relative path inside the theme.
 * @return string
 */
function sptheme_asset_version( $relative ) {
	$path = SPTHEME_DIR . '/' . ltrim( $relative, '/' );
	return file_exists( $path ) ? (string) filemtime( $path ) : SPTHEME_VERSION;
}

/**
 * Load local assets only. The interface remains functional without animation.
 *
 * @return void
 */
function sptheme_assets() {
	wp_enqueue_style(
		'sptheme-main',
		SPTHEME_URI . '/assets/css/main.css',
		array(),
		sptheme_asset_version( 'assets/css/main.css' )
	);

	wp_enqueue_style(
		'sptheme-release',
		SPTHEME_URI . '/assets/css/v1.css',
		array( 'sptheme-main' ),
		sptheme_asset_version( 'assets/css/v1.css' )
	);

	wp_enqueue_script(
		'sptheme-main',
		SPTHEME_URI . '/assets/js/main.js',
		array(),
		sptheme_asset_version( 'assets/js/main.js' ),
		true
	);
	wp_script_add_data( 'sptheme-main', 'strategy', 'defer' );
	wp_localize_script(
		'sptheme-main',
		'SPTHEME',
		array(
			'ajax'      => admin_url( 'admin-ajax.php' ),
			'nonce'     => wp_create_nonce( 'spcore_contact_action' ),
			'homeUrl'   => home_url( '/' ),
			'contactUrl'=> home_url( '/contact/' ),
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'sptheme_assets' );

/**
 * Register a footer widget area for optional content.
 *
 * @return void
 */
function sptheme_widgets() {
	register_sidebar(
		array(
			'name'          => __( 'Footer', 'sptheme' ),
			'id'            => 'footer-1',
			'before_widget' => '<div class="sp-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="sp-widget-title">',
			'after_title'   => '</h3>',
		)
	);
}
add_action( 'widgets_init', 'sptheme_widgets' );

/**
 * Read a companion-plugin setting while keeping the theme standalone-safe.
 *
 * @param string $key     Setting key.
 * @param mixed  $default Fallback value.
 * @return mixed
 */
function sp_setting( $key, $default = '' ) {
	if ( function_exists( 'spcore_get' ) ) {
		return spcore_get( $key, $default );
	}
	return $default;
}

/**
 * Render a companion shortcode or a visible admin hint.
 *
 * @param string $shortcode Full shortcode string.
 * @return void
 */
function sp_block( $shortcode ) {
	$tag = explode( ' ', trim( $shortcode, '[]' ) )[0];
	if ( shortcode_exists( $tag ) ) {
		echo do_shortcode( $shortcode ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Shortcode owns escaping.
	} elseif ( current_user_can( 'manage_options' ) ) {
		echo '<p class="sp-notice">' . esc_html__( 'Activate Saddam Engineer Core to render this section.', 'sptheme' ) . '</p>';
	}
}

/**
 * Alert administrators when the companion plugin is unavailable.
 *
 * @return void
 */
function sptheme_plugin_notice() {
	if ( function_exists( 'spcore_get' ) || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	echo '<div class="notice notice-warning"><p><strong>' . esc_html__( 'Saddam Engineer:', 'sptheme' ) . '</strong> ' . esc_html__( 'Install and activate Saddam Engineer Core to enable structured work content, engineering details, contact storage, REST API and schema.', 'sptheme' ) . '</p></div>';
}
add_action( 'admin_notices', 'sptheme_plugin_notice' );

/**
 * Accessible fallback navigation.
 *
 * @return void
 */
function sptheme_fallback_menu() {
	$projects = get_post_type_archive_link( 'sp_project' );
	$cases    = get_post_type_archive_link( 'sp_case_study' );
	?>
	<ul class="sp-nav-list">
		<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'sptheme' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/engineering/' ) ); ?>"><?php esc_html_e( 'Engineering', 'sptheme' ); ?></a></li>
		<li class="menu-item-has-children">
			<a href="<?php echo esc_url( $cases ? $cases : home_url( '/case-studies/' ) ); ?>"><?php esc_html_e( 'Work', 'sptheme' ); ?></a>
			<ul class="sub-menu">
				<li><a href="<?php echo esc_url( $cases ? $cases : home_url( '/case-studies/' ) ); ?>"><?php esc_html_e( 'Case Studies', 'sptheme' ); ?></a></li>
				<li><a href="<?php echo esc_url( $projects ? $projects : home_url( '/projects/' ) ); ?>"><?php esc_html_e( 'Projects', 'sptheme' ); ?></a></li>
				<li><a href="<?php echo esc_url( sp_setting( 'github_url', 'https://github.com/iamsaddamhossen' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'GitHub', 'sptheme' ); ?></a></li>
			</ul>
		</li>
		<li><a href="<?php echo esc_url( home_url( '/experience/' ) ); ?>"><?php esc_html_e( 'Experience', 'sptheme' ); ?></a></li>
		<li class="menu-item-has-children">
			<a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'Resources', 'sptheme' ); ?></a>
			<ul class="sub-menu">
				<li><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'Technical Writing', 'sptheme' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/recommended-tools/' ) ); ?>"><?php esc_html_e( 'Recommended Tools', 'sptheme' ); ?></a></li>
			</ul>
		</li>
		<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'sptheme' ); ?></a></li>
	</ul>
	<?php
}

/**
 * Add useful body classes for layout and plugin state.
 *
 * @param array<int, string> $classes Body classes.
 * @return array<int, string>
 */
function sptheme_body_classes( $classes ) {
	$classes[] = function_exists( 'spcore_get' ) ? 'spcore-active' : 'spcore-inactive';
	if ( is_page_template( 'template-engineering.php' ) ) {
		$classes[] = 'sp-engineering-page';
	}
	return $classes;
}
add_filter( 'body_class', 'sptheme_body_classes' );

/**
 * Keep excerpts concise on cards.
 */
add_filter(
	'excerpt_length',
	static function () {
		return 28;
	}
);
add_filter(
	'excerpt_more',
	static function () {
		return '…';
	}
);


/**
 * Add stable IDs to article headings and return a table of contents.
 *
 * @param string $content Filtered article HTML.
 * @return array{content:string,toc:array<int,array{level:int,id:string,label:string}>}
 */
function sptheme_prepare_article_content( $content ) {
	$toc  = array();
	$used = array();
	$body = preg_replace_callback(
		'/<h([23])([^>]*)>(.*?)<\/h\1>/is',
		static function ( $matches ) use ( &$toc, &$used ) {
			$level = absint( $matches[1] );
			$attrs = (string) $matches[2];
			$inner = (string) $matches[3];
			$label = trim( wp_strip_all_tags( $inner ) );
			if ( '' === $label ) {
				return $matches[0];
			}

			$id = '';
			if ( preg_match( '/\sid=["\']([^"\']+)["\']/i', $attrs, $id_match ) ) {
				$id = sanitize_title( $id_match[1] );
			}
			if ( '' === $id ) {
				$id = sanitize_title( $label );
				if ( '' === $id ) {
					$id = 'section-' . ( count( $toc ) + 1 );
				}
				$base = $id;
				$index = 2;
				while ( isset( $used[ $id ] ) ) {
					$id = $base . '-' . $index;
					++$index;
				}
				$attrs .= ' id="' . esc_attr( $id ) . '"';
			}

			$used[ $id ] = true;
			$toc_label = preg_replace( '/^\s*\d+(?:\.\d+)*[.)]?\s+/', '', $label );
			$toc_label = is_string( $toc_label ) && '' !== trim( $toc_label ) ? trim( $toc_label ) : $label;

			$toc[]       = array(
				'level' => $level,
				'id'    => $id,
				'label' => $toc_label,
			);

			return '<h' . $level . $attrs . '>' . $inner . '</h' . $level . '>';
		},
		(string) $content
	);

	return array(
		'content' => is_string( $body ) ? $body : (string) $content,
		'toc'     => $toc,
	);
}

/**
 * Return related technical articles using shared categories first.
 *
 * @param int $post_id Current post ID.
 * @param int $limit   Maximum results.
 * @return WP_Post[]
 */
function sptheme_related_articles( $post_id, $limit = 3 ) {
	$category_ids = wp_get_post_categories( $post_id );
	$args         = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => absint( $limit ),
		'post__not_in'        => array( absint( $post_id ) ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	);
	if ( $category_ids ) {
		$args['category__in'] = $category_ids;
	}
	$posts = get_posts( $args );
	if ( count( $posts ) < $limit ) {
		$fallback = get_posts(
			array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'posts_per_page'      => $limit - count( $posts ),
				'post__not_in'        => array_merge( array( absint( $post_id ) ), wp_list_pluck( $posts, 'ID' ) ),
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);
		$posts    = array_merge( $posts, $fallback );
	}
	return array_slice( $posts, 0, $limit );
}
