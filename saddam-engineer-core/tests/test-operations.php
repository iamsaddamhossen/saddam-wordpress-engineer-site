<?php
/**
 * Operational behavior tests.
 */

class SPCore_Operations_Test extends WP_UnitTestCase {

	public function test_daily_maintenance_hook_is_registered() {
		$this->assertNotFalse( has_action( 'spcore_daily_maintenance' ) );
	}

	public function test_default_configuration_is_privacy_first() {
		$defaults = spcore_default_settings();
		$this->assertSame( 0, $defaults['store_ip'] );
		$this->assertSame( '365', $defaults['lead_retention_days'] );
		$this->assertSame( 0, $defaults['modal_enable'] );
		$this->assertSame( 1, $defaults['chat_enable'] );
		$this->assertSame( 'guided', $defaults['chat_mode'] );
		$this->assertSame( 'openai', $defaults['chat_api_provider'] );
		$this->assertSame( 1, $defaults['comments_disable'] );
	}
}
