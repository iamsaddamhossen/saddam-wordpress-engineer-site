<?php
/**
 * Gutenberg block registration.
 *
 * @package SaddamEngineerCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class SPCore_Blocks {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'register' ) );
	}

	/**
	 * Register all block.json definitions in the plugin.
	 *
	 * @return void
	 */
	public function register() {
		register_block_type( SPCORE_DIR . 'blocks/engineering-proof' );
	}
}
