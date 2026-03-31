=== WP Member Image Lock ===
Contributors:      yourname
Tags:              members, images, login, content protection, access control
Requires at least: 6.0
Tested up to:      6.7
Requires PHP:      7.4
Stable tag:        1.0.0
License:           GPL-2.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

Hide images from non-logged-in visitors. All other content remains visible.

== Description ==

WP Member Image Lock lets you protect images on your WordPress site so that only logged-in members can view them. Non-logged-in visitors will see a placeholder with a login prompt in place of each image, while all other content (text, headings, links) remains fully visible.

**Features:**

* Replaces `<img>` tags and Gutenberg image blocks (`<figure>`) with a login prompt for guests
* Also hides featured images (post thumbnails) for non-logged-in visitors
* Configurable placeholder message
* Optional login link that redirects back to the original page after login
* Customizable login link text
* Choose which post types the protection applies to
* Lightweight — no JavaScript required

== Installation ==

1. Upload the `wp-member-image-lock` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the **Plugins** screen in WordPress
3. Go to **Settings → Member Image Lock** to configure the placeholder message and options

== Frequently Asked Questions ==

= Does this protect direct image URLs? =

No. This plugin hides images in page content and featured images rendered by WordPress. It does not block direct access to image file URLs. For full protection of image files, server-level rules (e.g. `.htaccess` or Nginx config) are required.

= Which post types does it support? =

By default it applies to Posts and Pages. You can add or remove post types from **Settings → Member Image Lock → Post Types**.

= Does it work with the Block Editor (Gutenberg)? =

Yes. The plugin handles both classic `<img>` tags and Gutenberg `<figure>` image blocks.

= Will it slow down my site? =

No. The plugin only runs its filter on singular post/page views for non-logged-in visitors, and only enqueues one small CSS file in that case.

== Screenshots ==

1. Placeholder shown to non-logged-in visitors in place of a hidden image
2. Settings page — configure message, login link, and post types

== Changelog ==

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial release.
