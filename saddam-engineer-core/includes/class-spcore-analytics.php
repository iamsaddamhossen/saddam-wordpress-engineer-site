<?php
/**
 * GA4 base tag + a JS helper (spcoreTrack) for the theme to fire events.
 *
 * @package SaddamPortfolioCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SPCore_Analytics {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'wp_head', array( $this, 'ga4_tag' ), 5 );
		add_action( 'wp_head', array( $this, 'verification_tags' ), 1 );
		add_action( 'wp_footer', array( $this, 'tracker_helper' ), 20 );
	}

	public function ga4_tag() {
		$id = spcore_get( 'ga4_id' );
		if ( ! $id || ! spcore_get( 'ga4_events', 1 ) ) {
			return;
		}
		$id = preg_replace( '/[^A-Za-z0-9\-]/', '', $id );
		?>
<!-- Google Analytics 4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $id ); ?>"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', '<?php echo esc_js( $id ); ?>');
</script>
		<?php
	}

	public function verification_tags() {
		$gsc  = spcore_get( 'gsc_verification' );
		$bing = spcore_get( 'bing_verification' );
		if ( $gsc ) {
			printf( '<meta name="google-site-verification" content="%s" />' . "\n", esc_attr( $gsc ) );
		}
		if ( $bing ) {
			printf( '<meta name="msvalidate.01" content="%s" />' . "\n", esc_attr( $bing ) );
		}
	}

	/**
	 * spcoreTrack(name, params) ,  safe wrapper; no-ops if gtag missing.
	 * Recognised events: contact_submit, hire_cta_click, project_link_click,
	 * affiliate_link_click, cv_download, github_click, upwork_click.
	 */
	public function tracker_helper() {
		if ( ! spcore_get( 'ga4_events', 1 ) ) {
			return;
		}
		?>
<script>
window.spcoreTrack = function(name, params){
	try { if (typeof gtag === 'function') { gtag('event', name, params || {}); } } catch(e){}
};
document.addEventListener('click', function(e){
	var el = e.target.closest('[data-sp-event]');
	if(!el) return;
	window.spcoreTrack(el.getAttribute('data-sp-event'), { label: el.getAttribute('data-sp-label') || el.href || '' });
});
</script>
		<?php
	}
}
