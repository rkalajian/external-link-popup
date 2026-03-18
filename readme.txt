=== External Link Popup ===
Contributors: Rob Kalajian
Tags: external links, popup, modal, confirmation, link warning
Requires at least: 5.0
Tested up to: 6.7
Requires PHP: 7.2
Stable tag: 2.1
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Displays a confirmation modal when visitors click external links. Fully customizable colors, logo, and content from the admin.

== Description ==

External Link Popup intercepts clicks on outbound links and shows a branded confirmation modal before the visitor leaves your site. It works automatically — no shortcodes, no per-link setup.

**Features:**

* Automatically detects external links on every page
* Exclude trusted domains so they bypass the popup
* Upload a custom logo or fall back to the site name
* Customize heading, message body (HTML supported), and footer text
* Color picker for button and heading colors
* Lightweight — no jQuery on the frontend, no Bootstrap dependency
* Fully responsive with smooth open/close animations
* Accessible: keyboard navigation, Escape to close, ARIA attributes, focus trap
* Clean uninstall removes all options from the database

== Installation ==

1. Upload the `external-link-popup` folder to `/wp-content/plugins/`.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Go to **Settings > External Link Manager** to configure.

== Configuration ==

= Excluded Domains =
Enter one domain per line (e.g. `example.com`). Links pointing to these domains will not trigger the popup. Your own site domain is excluded automatically.

= Logo =
Upload an image via the media library or paste a URL. If left empty, your site name (from Settings > General) is displayed instead.

= Colors =
Use the color pickers to set:

* **Button Color** — primary button background and secondary button border/text
* **Button Hover** — hover state for both buttons
* **Heading Color** — the popup heading text color

= Popup Content =
* **Heading** — the bold title shown in the modal
* **Message** — the body copy (HTML allowed, e.g. `<strong>` for bold)
* **Footer Text** — optional italicized text below the buttons

== Frequently Asked Questions ==

= Does this require Bootstrap or jQuery? =
No. The frontend modal is fully standalone — plain CSS and vanilla JavaScript. The admin settings page uses WordPress's built-in color picker and media uploader (which require jQuery in the admin only).

= Will it work with dynamically loaded content? =
Yes. The plugin uses event delegation on the document, so links added to the page after initial load (e.g. via AJAX or JavaScript) are automatically handled.

= How do I remove all data if I uninstall? =
Deleting the plugin from the Plugins page will automatically remove all saved options from the database.

= Can I allow modifier-key clicks (Ctrl+click, Cmd+click)? =
Yes — those are already allowed. The popup only intercepts normal left-clicks. Ctrl+click, Cmd+click, Shift+click, and middle-click all behave as usual.

== Screenshots ==

1. Frontend modal with custom logo, heading, and branded buttons.
2. Admin settings page — Excluded Domains, Logo, Colors, and Popup Content sections.

== Changelog ==

= 2.1 =
* Added plugin action links (Settings link on Plugins page)
* Added uninstall.php for clean database removal
* Improved plugin headers (license, text domain, requirements)
* Version-stamped all enqueued assets for cache busting

= 2.0 =
* Complete rewrite — standalone modal (no Bootstrap dependency)
* Event delegation replaces per-link listeners
* Added admin settings for logo upload, color pickers, and editable content
* Separated CSS into its own file with CSS custom properties
* Smooth animations, backdrop blur, responsive design
* Proper input sanitization and output escaping
* Accessible: ARIA roles, keyboard support, focus management

= 1.0 =
* Initial release

== Upgrade Notice ==

= 2.1 =
Adds a Settings link on the Plugins page, clean uninstall support, and improved plugin metadata.

= 2.0 =
Major rewrite. Removes Bootstrap dependency, adds full admin customization (logo, colors, content), and improves performance and accessibility.
