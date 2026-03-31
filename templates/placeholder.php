<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Template: Image placeholder shown to non-logged-in visitors.
 *
 * Available variables (set by WMIL_Content_Filter::render_placeholder()):
 *   $message    (string) — placeholder text
 *   $show_login (bool)   — whether to show login link
 *   $login_text (string) — login link label
 *   $login_url  (string) — URL to wp-login.php with redirect back
 */
?>
<div class="wmil-placeholder" role="img" aria-label="<?php echo esc_attr( $message ); ?>">
    <span class="wmil-placeholder-icon" aria-hidden="true">&#128247;</span>
    <p class="wmil-placeholder-message"><?php echo esc_html( $message ); ?></p>
    <?php if ( $show_login ) : ?>
        <a class="wmil-login-link" href="<?php echo esc_url( $login_url ); ?>">
            <?php echo esc_html( $login_text ); ?>
        </a>
    <?php endif; ?>
</div>
