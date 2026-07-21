<?php
/**
 * Registration tests.
 */

class SPCore_Registrations_Test extends WP_UnitTestCase {

	public function test_portfolio_content_types_are_registered() {
		foreach ( array( 'sp_project', 'sp_case_study', 'sp_service', 'sp_testimonial', 'sp_tool', 'sp_lead' ) as $post_type ) {
			$this->assertTrue( post_type_exists( $post_type ), $post_type . ' should be registered.' );
		}
	}

	public function test_portfolio_taxonomies_are_registered() {
		foreach ( array( 'sp_category', 'sp_industry', 'sp_tool_category' ) as $taxonomy ) {
			$this->assertTrue( taxonomy_exists( $taxonomy ), $taxonomy . ' should be registered.' );
		}
	}

	public function test_engineering_feature_registry_has_evidence_paths() {
		$features = SPCore_Feature_Registry::all();
		$this->assertGreaterThanOrEqual( 10, count( $features ) );
		foreach ( $features as $feature ) {
			$this->assertArrayHasKey( 'title', $feature );
			$this->assertArrayHasKey( 'evidence', $feature );
			$this->assertNotEmpty( $feature['evidence'] );
		}
	}
}
