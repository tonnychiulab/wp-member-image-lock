<?php
/**
 * PHPUnit bootstrap for WP Member Image Lock tests.
 *
 * Loads Composer autoloader, defines WordPress constants and function stubs
 * so the plugin classes can be loaded and tested without a full WordPress install.
 */

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

// ── Constants ─────────────────────────────────────────────────────────────────

if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

if ( ! defined( 'WMIL_VERSION' ) ) {
    define( 'WMIL_VERSION', '1.0.0' );
}

if ( ! defined( 'WMIL_PLUGIN_DIR' ) ) {
    define( 'WMIL_PLUGIN_DIR', dirname( __DIR__ ) . '/' );
}

if ( ! defined( 'WMIL_PLUGIN_URL' ) ) {
    define( 'WMIL_PLUGIN_URL', 'https://example.com/wp-content/plugins/wp-member-image-lock/' );
}

if ( ! defined( 'WMIL_OPT_MESSAGE' ) ) {
    define( 'WMIL_OPT_MESSAGE', 'wmil_placeholder_message' );
}

if ( ! defined( 'WMIL_OPT_SHOW_LOGIN' ) ) {
    define( 'WMIL_OPT_SHOW_LOGIN', 'wmil_show_login_link' );
}

if ( ! defined( 'WMIL_OPT_LOGIN_TEXT' ) ) {
    define( 'WMIL_OPT_LOGIN_TEXT', 'wmil_login_link_text' );
}

if ( ! defined( 'WMIL_OPT_POST_TYPES' ) ) {
    define( 'WMIL_OPT_POST_TYPES', 'wmil_post_types' );
}

// ── WordPress function stubs ───────────────────────────────────────────────────

if ( ! function_exists( 'is_user_logged_in' ) ) {
    function is_user_logged_in(): bool {
        return ! empty( $GLOBALS['wmil_test_logged_in'] );
    }
}

if ( ! function_exists( 'is_singular' ) ) {
    function is_singular(): bool {
        if ( isset( $GLOBALS['wmil_test_is_singular'] ) ) {
            return (bool) $GLOBALS['wmil_test_is_singular'];
        }
        return true;
    }
}

if ( ! function_exists( 'get_the_ID' ) ) {
    function get_the_ID(): int {
        return isset( $GLOBALS['wmil_test_post_id'] ) ? (int) $GLOBALS['wmil_test_post_id'] : 1;
    }
}

if ( ! function_exists( 'get_post_type' ) ) {
    function get_post_type( $id = null ): string {
        return isset( $GLOBALS['wmil_test_post_type'] ) ? (string) $GLOBALS['wmil_test_post_type'] : 'post';
    }
}

if ( ! function_exists( 'get_option' ) ) {
    function get_option( string $key, $default = '' ) {
        if ( isset( $GLOBALS['wmil_options'][ $key ] ) ) {
            return $GLOBALS['wmil_options'][ $key ];
        }
        return $default;
    }
}

if ( ! function_exists( 'add_option' ) ) {
    function add_option( string $key, $value ): void {
        $GLOBALS['wmil_options'][ $key ] = $value;
    }
}

if ( ! function_exists( 'wp_login_url' ) ) {
    function wp_login_url( string $redirect = '' ): string {
        return 'https://example.com/wp-login.php?redirect_to=' . urlencode( $redirect );
    }
}

if ( ! function_exists( 'get_permalink' ) ) {
    function get_permalink(): string {
        return 'https://example.com/test-post/';
    }
}

if ( ! function_exists( 'wp_enqueue_style' ) ) {
    function wp_enqueue_style(): void {
        // no-op
    }
}

if ( ! function_exists( 'add_filter' ) ) {
    function add_filter(): void {
        // no-op
    }
}

if ( ! function_exists( 'add_action' ) ) {
    function add_action(): void {
        // no-op
    }
}

if ( ! function_exists( 'current_user_can' ) ) {
    function current_user_can( string $cap ): bool {
        if ( isset( $GLOBALS['wmil_test_current_user_can'] ) ) {
            return (bool) $GLOBALS['wmil_test_current_user_can'];
        }
        return true;
    }
}

if ( ! function_exists( 'get_admin_page_title' ) ) {
    function get_admin_page_title(): string {
        return 'Member Image Lock';
    }
}

if ( ! function_exists( 'settings_fields' ) ) {
    function settings_fields(): void {
        // no-op
    }
}

if ( ! function_exists( 'do_settings_sections' ) ) {
    function do_settings_sections(): void {
        // no-op
    }
}

if ( ! function_exists( 'submit_button' ) ) {
    function submit_button(): void {
        // no-op
    }
}

if ( ! function_exists( 'register_setting' ) ) {
    function register_setting(): void {
        // no-op
    }
}

if ( ! function_exists( 'add_settings_section' ) ) {
    function add_settings_section(): void {
        // no-op
    }
}

if ( ! function_exists( 'add_settings_field' ) ) {
    function add_settings_field(): void {
        // no-op
    }
}

if ( ! function_exists( 'add_options_page' ) ) {
    function add_options_page(): void {
        // no-op
    }
}

if ( ! function_exists( 'get_post_types' ) ) {
    function get_post_types( $args = array(), string $output = 'names' ) {
        $types = array(
            'post' => (object) array(
                'name'   => 'post',
                'labels' => (object) array( 'singular_name' => 'Post' ),
            ),
            'page' => (object) array(
                'name'   => 'page',
                'labels' => (object) array( 'singular_name' => 'Page' ),
            ),
        );

        if ( 'objects' === $output ) {
            return $types;
        }

        // Return name => name map for 'names' output.
        return array_combine( array_keys( $types ), array_keys( $types ) );
    }
}

if ( ! function_exists( 'wp_die' ) ) {
    function wp_die( $msg = '' ): void {
        throw new \RuntimeException( 'wp_die: ' . $msg );
    }
}

if ( ! function_exists( 'checked' ) ) {
    function checked( $val, $current = true, bool $echo = true ): string {
        // no-op
        return '';
    }
}

if ( ! function_exists( 'esc_html' ) ) {
    function esc_html( string $str ): string {
        return htmlspecialchars( $str, ENT_QUOTES );
    }
}

if ( ! function_exists( 'esc_attr' ) ) {
    function esc_attr( string $str ): string {
        return htmlspecialchars( $str, ENT_QUOTES );
    }
}

if ( ! function_exists( 'esc_url' ) ) {
    function esc_url( string $url ): string {
        return $url;
    }
}

if ( ! function_exists( 'esc_textarea' ) ) {
    function esc_textarea( string $str ): string {
        return htmlspecialchars( $str, ENT_QUOTES );
    }
}

if ( ! function_exists( 'esc_html_e' ) ) {
    function esc_html_e( string $str ): void {
        echo htmlspecialchars( $str, ENT_QUOTES );
    }
}

if ( ! function_exists( 'esc_attr_e' ) ) {
    function esc_attr_e( string $str ): void {
        echo htmlspecialchars( $str, ENT_QUOTES );
    }
}

if ( ! function_exists( '__' ) ) {
    function __( string $str, string $domain = 'default' ): string {
        return $str;
    }
}

if ( ! function_exists( 'sanitize_key' ) ) {
    function sanitize_key( string $str ): string {
        return strtolower( preg_replace( '/[^a-z0-9_-]/i', '', strtolower( $str ) ) );
    }
}

if ( ! function_exists( 'sanitize_text_field' ) ) {
    function sanitize_text_field( string $str ): string {
        return trim( strip_tags( $str ) );
    }
}

if ( ! function_exists( 'sanitize_textarea_field' ) ) {
    function sanitize_textarea_field( string $str ): string {
        return trim( strip_tags( $str ) );
    }
}

if ( ! function_exists( 'is_admin' ) ) {
    function is_admin(): bool {
        return false;
    }
}

if ( ! function_exists( 'plugin_dir_path' ) ) {
    function plugin_dir_path( string $file ): string {
        return trailingslashit( dirname( $file ) );
    }
}

if ( ! function_exists( 'plugin_dir_url' ) ) {
    function plugin_dir_url( string $file ): string {
        return 'https://example.com/wp-content/plugins/' . basename( dirname( $file ) ) . '/';
    }
}

if ( ! function_exists( 'trailingslashit' ) ) {
    function trailingslashit( string $str ): string {
        return rtrim( $str, '/\\' ) . '/';
    }
}

if ( ! function_exists( 'register_activation_hook' ) ) {
    function register_activation_hook(): void {
        // no-op
    }
}

if ( ! function_exists( 'esc_html__' ) ) {
    function esc_html__( string $str, string $domain = 'default' ): string {
        return htmlspecialchars( $str, ENT_QUOTES );
    }
}

// ── Load plugin source classes ────────────────────────────────────────────────

require_once dirname( __DIR__ ) . '/includes/class-wmil-content-filter.php';
require_once dirname( __DIR__ ) . '/includes/class-wmil-settings.php';
