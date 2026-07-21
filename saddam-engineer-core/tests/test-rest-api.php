<?php
/**
 * REST API tests.
 */

class SPCore_REST_API_Test extends WP_UnitTestCase {

	public function set_up() {
		parent::set_up();
		do_action( 'rest_api_init' );
	}

	public function test_engineering_endpoint_is_public() {
		$request  = new WP_REST_Request( 'GET', '/spcore/v1/engineering' );
		$response = rest_get_server()->dispatch( $request );
		$this->assertSame( 200, $response->get_status() );
		$data = $response->get_data();
		$this->assertArrayHasKey( 'features', $data );
		$this->assertArrayHasKey( 'stats', $data );
	}

	public function test_projects_endpoint_limits_per_page() {
		$request = new WP_REST_Request( 'GET', '/spcore/v1/projects' );
		$request->set_param( 'per_page', 200 );
		$response = rest_get_server()->dispatch( $request );
		$this->assertSame( 400, $response->get_status() );
	}

	public function test_health_endpoint_requires_administrator_capability() {
		wp_set_current_user( 0 );
		$request  = new WP_REST_Request( 'GET', '/spcore/v1/health' );
		$response = rest_get_server()->dispatch( $request );
		$this->assertSame( 401, $response->get_status() );
	}
}
