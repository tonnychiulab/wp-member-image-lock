<?php
/**
 * Tests for WMIL_Settings class.
 */

class WMIL_Settings_Test extends \PHPUnit\Framework\TestCase {

    /** @var WMIL_Settings */
    private WMIL_Settings $settings;

    protected function setUp(): void {
        parent::setUp();

        // Reset test globals to defaults before each test.
        $GLOBALS['wmil_options']               = array();
        $GLOBALS['wmil_test_current_user_can'] = true;

        $this->settings = new WMIL_Settings();
    }

    protected function tearDown(): void {
        unset(
            $GLOBALS['wmil_options'],
            $GLOBALS['wmil_test_current_user_can']
        );

        parent::tearDown();
    }

    // ── sanitize_checkbox tests ───────────────────────────────────────────────

    /**
     * Passing '1' (checked state) should return '1'.
     */
    public function test_sanitize_checkbox_returns_one_for_checked(): void {
        $result = $this->settings->sanitize_checkbox( '1' );
        $this->assertSame( '1', $result );
    }

    /**
     * Passing '0' (unchecked) should return empty string.
     */
    public function test_sanitize_checkbox_returns_empty_for_unchecked(): void {
        $result = $this->settings->sanitize_checkbox( '0' );
        $this->assertSame( '', $result );
    }

    /**
     * An arbitrary non-'1' string should return empty string.
     */
    public function test_sanitize_checkbox_returns_empty_for_arbitrary_value(): void {
        $result = $this->settings->sanitize_checkbox( 'yes' );
        $this->assertSame( '', $result );
    }

    // ── sanitize_post_types tests ─────────────────────────────────────────────

    /**
     * Invalid post types should be filtered out; valid ones kept.
     *
     * The stub get_post_types() returns 'post' and 'page' as public types.
     */
    public function test_sanitize_post_types_filters_invalid_types(): void {
        $result = $this->settings->sanitize_post_types( array( 'post', 'invalid_type', 'page' ) );

        $this->assertContains( 'post', $result );
        $this->assertContains( 'page', $result );
        $this->assertNotContains( 'invalid_type', $result );
    }

    /**
     * Non-array input should return an empty array.
     */
    public function test_sanitize_post_types_returns_empty_for_non_array(): void {
        $result = $this->settings->sanitize_post_types( 'post' );
        $this->assertSame( array(), $result );
    }

    /**
     * Input keys are sanitized (lowercased) before comparison.
     * 'POST' and 'Page' should match 'post' and 'page' after sanitize_key.
     */
    public function test_sanitize_post_types_sanitizes_keys(): void {
        $result = $this->settings->sanitize_post_types( array( 'POST', 'Page' ) );

        $this->assertContains( 'post', $result );
        $this->assertContains( 'page', $result );
    }

    // ── render_page capability check ──────────────────────────────────────────

    /**
     * Calling render_page() without the required capability should throw
     * a RuntimeException (via the wp_die stub).
     */
    public function test_render_page_dies_without_capability(): void {
        $GLOBALS['wmil_test_current_user_can'] = false;

        $this->expectException( \RuntimeException::class );
        $this->expectExceptionMessageMatches( '/wp_die/' );

        $this->settings->render_page();
    }
}
