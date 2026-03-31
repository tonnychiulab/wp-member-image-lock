<?php
/**
 * Tests for WMIL_Content_Filter class.
 */

class WMIL_Content_Filter_Test extends \PHPUnit\Framework\TestCase {

    /** @var WMIL_Content_Filter */
    private WMIL_Content_Filter $filter;

    protected function setUp(): void {
        parent::setUp();

        // Reset all test globals to defaults before each test.
        $GLOBALS['wmil_test_logged_in']         = false;
        $GLOBALS['wmil_test_is_singular']       = true;
        $GLOBALS['wmil_test_post_id']           = 1;
        $GLOBALS['wmil_test_post_type']         = 'post';
        $GLOBALS['wmil_options']                = array();
        $GLOBALS['wmil_test_current_user_can']  = true;

        $this->filter = new WMIL_Content_Filter();
    }

    protected function tearDown(): void {
        // Clean up globals after each test.
        unset(
            $GLOBALS['wmil_test_logged_in'],
            $GLOBALS['wmil_test_is_singular'],
            $GLOBALS['wmil_test_post_id'],
            $GLOBALS['wmil_test_post_type'],
            $GLOBALS['wmil_options'],
            $GLOBALS['wmil_test_current_user_can']
        );

        parent::tearDown();
    }

    // ── filter_content tests ──────────────────────────────────────────────────

    /**
     * Logged-in users should see the original content unchanged.
     */
    public function test_filter_content_returns_original_for_logged_in_user(): void {
        $GLOBALS['wmil_test_logged_in'] = true;

        $content = '<p>Some text</p><img src="x.jpg" alt="test"><p>More text</p>';
        $result  = $this->filter->filter_content( $content );

        $this->assertSame( $content, $result );
    }

    /**
     * Guest visitors should have <img> tags replaced by the placeholder.
     */
    public function test_filter_content_replaces_img_tag_for_guest(): void {
        $GLOBALS['wmil_test_logged_in'] = false;

        $content = '<p>Hello</p><img src="x.jpg"><p>World</p>';
        $result  = $this->filter->filter_content( $content );

        $this->assertStringContainsString( 'wmil-placeholder', $result );
        $this->assertStringNotContainsString( '<img', $result );
    }

    /**
     * Gutenberg <figure> blocks containing <img> should be fully replaced.
     */
    public function test_filter_content_replaces_figure_block_for_guest(): void {
        $GLOBALS['wmil_test_logged_in'] = false;

        $content = '<figure class="wp-block-image"><img src="x.jpg"></figure>';
        $result  = $this->filter->filter_content( $content );

        $this->assertStringContainsString( 'wmil-placeholder', $result );
        $this->assertStringNotContainsString( '<figure', $result );
        $this->assertStringNotContainsString( '<img', $result );
    }

    /**
     * Non-image text should be preserved even when images are replaced.
     */
    public function test_filter_content_preserves_text_for_guest(): void {
        $GLOBALS['wmil_test_logged_in'] = false;

        $content = '<p>Keep this text</p><img src="x.jpg"><p>And this</p>';
        $result  = $this->filter->filter_content( $content );

        $this->assertStringContainsString( 'Keep this text', $result );
        $this->assertStringContainsString( 'And this', $result );
    }

    /**
     * Non-singular contexts (archives, etc.) should return content unchanged.
     */
    public function test_filter_content_returns_original_when_not_singular(): void {
        $GLOBALS['wmil_test_logged_in']   = false;
        $GLOBALS['wmil_test_is_singular'] = false;

        $content = '<p>Hello</p><img src="x.jpg"><p>World</p>';
        $result  = $this->filter->filter_content( $content );

        $this->assertSame( $content, $result );
    }

    // ── filter_thumbnail tests ────────────────────────────────────────────────

    /**
     * Logged-in users should see the original thumbnail HTML.
     */
    public function test_filter_thumbnail_returns_original_for_logged_in_user(): void {
        $GLOBALS['wmil_test_logged_in'] = true;

        $html   = '<img src="thumbnail.jpg" class="attachment-post-thumbnail">';
        $result = $this->filter->filter_thumbnail( $html, 1, 10, 'post-thumbnail', array() );

        $this->assertSame( $html, $result );
    }

    /**
     * Guests should receive the placeholder instead of the thumbnail.
     */
    public function test_filter_thumbnail_replaces_thumbnail_for_guest(): void {
        $GLOBALS['wmil_test_logged_in'] = false;
        $GLOBALS['wmil_test_post_type'] = 'post';

        $html   = '<img src="thumbnail.jpg" class="attachment-post-thumbnail">';
        $result = $this->filter->filter_thumbnail( $html, 1, 10, 'post-thumbnail', array() );

        $this->assertStringContainsString( 'wmil-placeholder', $result );
    }

    // ── applies_to_post (via filter_content integration) ─────────────────────

    /**
     * Posts whose type is not in the allowed list should not have images replaced.
     */
    public function test_filter_content_skips_non_matching_post_type(): void {
        $GLOBALS['wmil_test_logged_in'] = false;
        $GLOBALS['wmil_test_post_type'] = 'product';
        $GLOBALS['wmil_options'][ WMIL_OPT_POST_TYPES ] = array( 'post', 'page' );

        $content = '<p>Hello</p><img src="x.jpg"><p>World</p>';
        $result  = $this->filter->filter_content( $content );

        $this->assertSame( $content, $result );
    }
}
