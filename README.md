# External Link Popup

**Version:** 2.1  
**Author:** Rob Kalajian  
**License:** [GPL-2.0-or-later](https://www.gnu.org/licenses/gpl-2.0.html)  
**Requires WordPress:** 5.0+  
**Requires PHP:** 7.2+  
**Tested up to:** 6.7

Displays a confirmation modal when visitors click external links. Fully customizable colors, logo, and content from the admin.

---

## Description

External Link Popup intercepts clicks on outbound links and shows a branded confirmation modal before the visitor leaves your site. It works automatically — no shortcodes, no per-link setup.

**Features:**

- Automatically detects external links on every page
- Excludes trusted domains (and subdomains) from triggering the popup
- Upload a custom logo or fall back to the site name
- Customize heading, message body (HTML supported), and footer text
- Color pickers for button and heading colors
- Lightweight — no jQuery on the frontend, no Bootstrap dependency
- Fully responsive with smooth open/close animations
- Accessible: keyboard navigation, Escape to close, ARIA attributes, focus management
- Clean uninstall removes all options from the database

---

## Installation

1. Upload the `external-link-popup` folder to `/wp-content/plugins/`.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Go to **Settings > External Link Manager** to configure.

---

## Configuration

### Excluded Domains
Enter one domain per line (e.g. `example.com`). Links pointing to these domains — including subdomains — will not trigger the popup. Your own site domain is excluded automatically.

### Logo
Upload an image via the media library or paste a URL. If left empty, your site name (from **Settings > General**) is displayed instead.

### Colors
Use the color pickers to set:

| Setting | Description |
|---|---|
| **Button Color** | Primary button background and secondary button border/text |
| **Button Hover** | Hover state for both buttons |
| **Heading Color** | The popup heading text color |

### Popup Content

| Field | Description |
|---|---|
| **Heading** | Bold title shown in the modal |
| **Message** | Body copy — HTML allowed (e.g. `<strong>` for bold) |
| **Footer Text** | Optional italicized text below the buttons |

---

## FAQ

**Does this require Bootstrap or jQuery?**  
No. The frontend modal is fully standalone — plain CSS and vanilla JavaScript. The admin settings page uses WordPress's built-in color picker and media uploader (jQuery is only loaded in the admin).

**Will it work with dynamically loaded content?**  
Yes. The plugin uses event delegation on the document, so links added after page load (e.g. via AJAX) are automatically handled.

**Does subdomain exclusion work?**  
Yes. Excluding `example.com` also covers `www.example.com`, `app.example.com`, and any other subdomain.

**How do I remove all plugin data on uninstall?**  
Deleting the plugin from the Plugins page automatically removes all saved options from the database via `uninstall.php`.

**What about modifier-key clicks?**  
Ctrl+click, Cmd+click, Shift+click, and middle-click all pass through normally. The popup only intercepts plain left-clicks.

---

## Changelog

### 2.1
- Added Settings link on the Plugins page
- Added `uninstall.php` for clean database removal on deletion
- Improved plugin headers (license, text domain, requirements)
- Version-stamped all enqueued assets for proper cache busting
- Subdomain matching: excluding `example.com` now also covers `*.example.com`
- Added explicit `current_user_can( 'manage_options' )` check in settings callback (defense in depth)
- Added `elp_esc_css_color()` helper to validate hex colors at output time before writing to inline `<style>` blocks, preventing CSS injection

### 2.0
- Complete rewrite — standalone modal, no Bootstrap dependency
- Single event delegation listener replaces per-link handlers
- Admin settings: logo upload, color pickers, editable heading/message/footer
- Separated CSS into its own file using CSS custom properties
- Smooth animations, backdrop blur, responsive layout
- Proper sanitization on all inputs, escaping on all outputs
- Accessible: ARIA roles, keyboard support, focus management

### 1.0
- Initial release
