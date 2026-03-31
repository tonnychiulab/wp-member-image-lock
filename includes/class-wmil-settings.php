<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * WMIL_Settings — Admin settings page
 *
 * Adds "Member Image Lock" under the Settings menu.
 * Allows admins to configure:
 *  - Placeholder message
 *  - Show/hide login link
 *  - Login link text
 *  - Which post types the filter applies to
 */
class WMIL_Settings {

    const PAGE_SLUG = 'wp-member-image-lock';
    const OPTION_GROUP = 'wmil_options';

    public function init(): void {
        add_action( 'admin_menu',            array( $this, 'register_menu' ) );
        add_action( 'admin_init',            array( $this, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    /**
     * Register settings page under Settings menu.
     */
    public function register_menu(): void {
        add_options_page(
            __( 'Member Image Lock', 'wp-member-image-lock' ),
            __( 'Member Image Lock', 'wp-member-image-lock' ),
            'manage_options',
            self::PAGE_SLUG,
            array( $this, 'render_page' )
        );
    }

    /**
     * Register settings, sections, and fields via Settings API.
     */
    public function register_settings(): void {
        register_setting(
            self::OPTION_GROUP,
            WMIL_OPT_MESSAGE,
            array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_textarea_field',
                'default'           => __( 'Please log in to view this image.', 'wp-member-image-lock' ),
            )
        );

        register_setting(
            self::OPTION_GROUP,
            WMIL_OPT_SHOW_LOGIN,
            array(
                'type'              => 'string',
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
                'default'           => '1',
            )
        );

        register_setting(
            self::OPTION_GROUP,
            WMIL_OPT_LOGIN_TEXT,
            array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => __( 'Log in', 'wp-member-image-lock' ),
            )
        );

        register_setting(
            self::OPTION_GROUP,
            WMIL_OPT_POST_TYPES,
            array(
                'type'              => 'array',
                'sanitize_callback' => array( $this, 'sanitize_post_types' ),
                'default'           => array( 'post', 'page' ),
            )
        );

        // Section: Placeholder
        add_settings_section(
            'wmil_section_placeholder',
            __( 'Placeholder Settings', 'wp-member-image-lock' ),
            '__return_false',
            self::PAGE_SLUG
        );

        add_settings_field(
            WMIL_OPT_MESSAGE,
            __( 'Placeholder Message', 'wp-member-image-lock' ),
            array( $this, 'field_message' ),
            self::PAGE_SLUG,
            'wmil_section_placeholder'
        );

        add_settings_field(
            WMIL_OPT_SHOW_LOGIN,
            __( 'Show Login Link', 'wp-member-image-lock' ),
            array( $this, 'field_show_login' ),
            self::PAGE_SLUG,
            'wmil_section_placeholder'
        );

        add_settings_field(
            WMIL_OPT_LOGIN_TEXT,
            __( 'Login Link Text', 'wp-member-image-lock' ),
            array( $this, 'field_login_text' ),
            self::PAGE_SLUG,
            'wmil_section_placeholder'
        );

        // Section: Post Types
        add_settings_section(
            'wmil_section_post_types',
            __( 'Apply To', 'wp-member-image-lock' ),
            array( $this, 'section_post_types_description' ),
            self::PAGE_SLUG
        );

        add_settings_field(
            WMIL_OPT_POST_TYPES,
            __( 'Post Types', 'wp-member-image-lock' ),
            array( $this, 'field_post_types' ),
            self::PAGE_SLUG,
            'wmil_section_post_types'
        );
    }

    /**
     * Render the settings page.
     */
    public function render_page(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wp-member-image-lock' ) );
        }
        ?>
        <div class="wrap wmil-settings-wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

            <?php if ( isset( $_GET['settings-updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php esc_html_e( 'Settings saved.', 'wp-member-image-lock' ); ?></p>
                </div>
            <?php endif; ?>

            <form method="post" action="options.php">
                <?php
                settings_fields( self::OPTION_GROUP );
                do_settings_sections( self::PAGE_SLUG );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Enqueue admin CSS only on this plugin's settings page.
     *
     * @param string $hook
     */
    public function enqueue_assets( string $hook ): void {
        if ( 'settings_page_' . self::PAGE_SLUG !== $hook ) {
            return;
        }

        wp_enqueue_style(
            'wmil-admin',
            WMIL_PLUGIN_URL . 'assets/admin.css',
            array(),
            WMIL_VERSION
        );
    }

    // ── Field renderers ───────────────────────────────────────────────────────

    public function field_message(): void {
        $value = (string) get_option( WMIL_OPT_MESSAGE );
        ?>
        <textarea
            name="<?php echo esc_attr( WMIL_OPT_MESSAGE ); ?>"
            id="<?php echo esc_attr( WMIL_OPT_MESSAGE ); ?>"
            rows="3"
            cols="50"
            class="large-text"
        ><?php echo esc_textarea( $value ); ?></textarea>
        <p class="description"><?php esc_html_e( 'Text shown to visitors in place of each hidden image.', 'wp-member-image-lock' ); ?></p>
        <?php
    }

    public function field_show_login(): void {
        $checked = (bool) get_option( WMIL_OPT_SHOW_LOGIN, true );
        ?>
        <label>
            <input
                type="checkbox"
                name="<?php echo esc_attr( WMIL_OPT_SHOW_LOGIN ); ?>"
                value="1"
                <?php checked( $checked ); ?>
            />
            <?php esc_html_e( 'Show a login link below the placeholder message', 'wp-member-image-lock' ); ?>
        </label>
        <?php
    }

    public function field_login_text(): void {
        $value = (string) get_option( WMIL_OPT_LOGIN_TEXT );
        ?>
        <input
            type="text"
            name="<?php echo esc_attr( WMIL_OPT_LOGIN_TEXT ); ?>"
            id="<?php echo esc_attr( WMIL_OPT_LOGIN_TEXT ); ?>"
            value="<?php echo esc_attr( $value ); ?>"
            class="regular-text"
        />
        <p class="description"><?php esc_html_e( 'The clickable text of the login link.', 'wp-member-image-lock' ); ?></p>
        <?php
    }

    public function section_post_types_description(): void {
        echo '<p>' . esc_html__( 'Select which post types should have images hidden for non-logged-in visitors.', 'wp-member-image-lock' ) . '</p>';
    }

    public function field_post_types(): void {
        $public_types = get_post_types( array( 'public' => true ), 'objects' );
        $selected     = (array) get_option( WMIL_OPT_POST_TYPES, array( 'post', 'page' ) );

        foreach ( $public_types as $type ) {
            $checked = in_array( $type->name, $selected, true );
            ?>
            <label style="display:block; margin-bottom:4px;">
                <input
                    type="checkbox"
                    name="<?php echo esc_attr( WMIL_OPT_POST_TYPES ); ?>[]"
                    value="<?php echo esc_attr( $type->name ); ?>"
                    <?php checked( $checked ); ?>
                />
                <?php echo esc_html( $type->labels->singular_name ); ?>
                <code>(<?php echo esc_html( $type->name ); ?>)</code>
            </label>
            <?php
        }
    }

    // ── Sanitize callbacks ────────────────────────────────────────────────────

    public function sanitize_checkbox( $value ): string {
        return ( '1' === $value ) ? '1' : '';
    }

    public function sanitize_post_types( $input ): array {
        if ( ! is_array( $input ) ) {
            return array();
        }

        $public_types = get_post_types( array( 'public' => true ) );
        $sanitized    = array();

        foreach ( $input as $type ) {
            $type = sanitize_key( $type );
            if ( in_array( $type, $public_types, true ) ) {
                $sanitized[] = $type;
            }
        }

        return $sanitized;
    }
}
