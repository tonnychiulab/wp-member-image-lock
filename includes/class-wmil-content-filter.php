<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * WMIL_Content_Filter
 *
 * Hooks into WordPress content filters to replace images with a
 * login-prompt placeholder for non-logged-in visitors.
 *
 * Handles:
 *  - Post content: <img> tags and <figure> blocks (Gutenberg)
 *  - Featured image (post thumbnail)
 */
class WMIL_Content_Filter {

    public function init(): void {
        add_filter( 'the_content',          array( $this, 'filter_content' ), 20 );
        add_filter( 'post_thumbnail_html',  array( $this, 'filter_thumbnail' ), 20, 5 );
        add_action( 'wp_enqueue_scripts',   array( $this, 'enqueue_assets' ) );
    }

    /**
     * Enqueue frontend CSS for the placeholder.
     */
    public function enqueue_assets(): void {
        if ( is_user_logged_in() || ! is_singular() ) {
            return;
        }

        wp_enqueue_style(
            'wmil-frontend',
            WMIL_PLUGIN_URL . 'assets/frontend.css',
            array(),
            WMIL_VERSION
        );
    }

    /**
     * Replace images in post content for non-logged-in users.
     *
     * @param string $content
     * @return string
     */
    public function filter_content( string $content ): string {
        if ( is_user_logged_in() ) {
            return $content;
        }

        if ( ! $this->applies_to_current_post() ) {
            return $content;
        }

        // Replace <figure> blocks that contain an <img> (Gutenberg image blocks).
        $content = preg_replace_callback(
            '/<figure\b[^>]*>.*?<img\b[^>]*>.*?<\/figure>/is',
            array( $this, 'replace_with_placeholder' ),
            $content
        );

        // Replace any remaining bare <img> tags not inside a <figure>.
        $content = preg_replace_callback(
            '/<img\b[^>]*>/i',
            array( $this, 'replace_with_placeholder' ),
            $content
        );

        return $content;
    }

    /**
     * Replace featured image for non-logged-in users.
     *
     * @param string $html
     * @param int    $post_id
     * @param int    $post_thumbnail_id
     * @param mixed  $size
     * @param mixed  $attr
     * @return string
     */
    public function filter_thumbnail( string $html, int $post_id, int $post_thumbnail_id, $size, $attr ): string {
        if ( is_user_logged_in() ) {
            return $html;
        }

        if ( ! $this->applies_to_post( $post_id ) ) {
            return $html;
        }

        return $this->render_placeholder();
    }

    /**
     * Callback for preg_replace_callback — returns placeholder HTML.
     *
     * @param array $matches
     * @return string
     */
    public function replace_with_placeholder( array $matches ): string {
        return $this->render_placeholder();
    }

    /**
     * Render the placeholder HTML from template.
     *
     * @return string
     */
    private function render_placeholder(): string {
        $message    = (string) get_option( WMIL_OPT_MESSAGE, __( 'Please log in to view this image.', 'wp-member-image-lock' ) );
        $show_login = (bool) get_option( WMIL_OPT_SHOW_LOGIN, true );
        $login_text = (string) get_option( WMIL_OPT_LOGIN_TEXT, __( 'Log in', 'wp-member-image-lock' ) );
        $login_url  = wp_login_url( get_permalink() );

        ob_start();
        require WMIL_PLUGIN_DIR . 'templates/placeholder.php';
        return ob_get_clean();
    }

    /**
     * Check if the filter applies to the current singular post.
     *
     * @return bool
     */
    private function applies_to_current_post(): bool {
        if ( ! is_singular() ) {
            return false;
        }

        $post_id = get_the_ID();
        if ( ! $post_id ) {
            return false;
        }

        return $this->applies_to_post( $post_id );
    }

    /**
     * Check if the filter applies to a specific post by ID.
     *
     * @param int $post_id
     * @return bool
     */
    private function applies_to_post( int $post_id ): bool {
        $allowed_types = get_option( WMIL_OPT_POST_TYPES, array( 'post', 'page' ) );

        if ( ! is_array( $allowed_types ) || empty( $allowed_types ) ) {
            return true; // Apply to all if none configured.
        }

        $post_type = get_post_type( $post_id );
        return in_array( $post_type, $allowed_types, true );
    }
}
