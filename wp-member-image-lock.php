<?php
/**
 * Plugin Name:       Member Image Lock
 * Plugin URI:        https://github.com/tonnychiulab/wp-member-image-lock
 * Description:       Hide images from non-logged-in visitors. All other content remains visible.
 * Version:           1.0.0
 * Author:            Your Name
 * Author URI:        https://github.com/tonnychiulab
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       member-image-lock
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Tested up to:      6.9
 * Requires PHP:      7.4
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'WMIL_VERSION',    '1.0.0' );
define( 'WMIL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WMIL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Option keys
define( 'WMIL_OPT_MESSAGE',    'wmil_placeholder_message' );
define( 'WMIL_OPT_SHOW_LOGIN', 'wmil_show_login_link' );
define( 'WMIL_OPT_LOGIN_TEXT', 'wmil_login_link_text' );
define( 'WMIL_OPT_POST_TYPES', 'wmil_post_types' );

require_once WMIL_PLUGIN_DIR . 'includes/class-wmil-content-filter.php';
require_once WMIL_PLUGIN_DIR . 'includes/class-wmil-settings.php';

register_activation_hook( __FILE__, 'wmil_activate' );

function wmil_activate(): void {
    if ( ! current_user_can( 'activate_plugins' ) ) {
        wp_die( esc_html__( 'Permission denied.', 'member-image-lock' ) );
    }
    // Set default options on first activation.
    add_option( WMIL_OPT_MESSAGE,    __( 'Please log in to view this image.', 'member-image-lock' ) );
    add_option( WMIL_OPT_SHOW_LOGIN, '1' );
    add_option( WMIL_OPT_LOGIN_TEXT, __( 'Log in', 'member-image-lock' ) );
    add_option( WMIL_OPT_POST_TYPES, array( 'post', 'page' ) );
}

add_action( 'plugins_loaded', 'wmil_boot' );

function wmil_boot(): void {
    ( new WMIL_Content_Filter() )->init();

    if ( is_admin() ) {
        ( new WMIL_Settings() )->init();
    }
}
