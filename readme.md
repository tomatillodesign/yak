# Yak Theme

A fast, modern, developer-focused child theme built on the Genesis Framework — perfect for custom client builds with ACF Pro, advanced block styling, and powerful layout tools.

---

**Contributors:** tomatillodesign  
**Tags:** genesis, custom-theme, block-editor, accessibility-ready, developer-friendly  
**Requires at least:** 6.8  
**Tested up to:** 6.8  
**Requires PHP:** 8.2  
**Version:** 1.0.5  
**License:** [GPL v2 or later](https://www.gnu.org/licenses/gpl-2.0.html)

---

## Changelog

### Version 1.0.5

- **Platform:** Requires **WordPress 6.8+** and **PHP 8.2+** (declared in `style.css` for core compatibility checks).
- **Security:** Theme Settings use dedicated capability `yak_manage_theme_settings` (via `YAK_CAP_THEME_SETTINGS`); removed broad `manage_options` grant for authorized users.
- **Security:** `yak_force_switch` recovery URL requires `switch_themes`; Performance & Tools page included in restricted admin pages; import/export AJAX uses the same capability.
- **Security / output:** Escaped featured-image subtitle, search result titles, and modal search field value where needed.
- **Privacy / performance:** Developer-mode Genesis hook markers only for users with Theme Settings capability; favicon query string uses filemtime (or theme version), not `time()`.
- **Performance:** Featured hero uses `get_post_thumbnail_id()` instead of `attachment_url_to_postid()`.
- **Fix:** Genesis appearance inline CSS attaches to registered handles `yak-genesis-appearance-inline` (front) and `yak-genesis-appearance-inline-editor` (block editor).
- **Maintenance:** Consolidated Genesis sidebar unregister / `genesis_do_sidebar` removal; removed forced `wp_enable_speculation_rules` so core/host controls enablement; trimmed import/export admin console logging.
- **Tooling (groundwork):** See [docs/tooling.md](docs/tooling.md) and `.cursor/commands/` for planned `preflight`, `docs`, and `update-ui` workflows.
- **Performance:** Conditional frontend scripts (skip feeds/embeds; mobile menu when Primary is assigned); removed unused jQuery from `clb-custom-yak-scripts.js`; `filemtime` on `yakstrap`.
- **Performance:** Dashicons on the frontend only on WooCommerce views that use theme WC CSS.
- **Font Awesome:** Kit loads on every frontend and admin request (expected by companion plugins); `null` enqueue version defers caching to the browser and Font Awesome CDN.
- **Best practices:** Root `theme.json` (layout, spacing, typography) plus `add_editor_style( 'css/yak-editor-bridge.css' )`; editor colors remain ACF-driven in `inc/yak-colors.php`.
- **Security:** Yak settings import — `yak_import_json_max_bytes`, denylist and transient skip (`yak_import_denied_option_names`).

### Version 1.0.4
- **🚀 MAJOR: Migrated alignwide/alignfull wrapping from JavaScript to PHP**
  - Replaced client-side DOM manipulation with server-side `render_block` filter
  - Improved performance and reliability (no JavaScript dependency)
  - Enhanced SEO compatibility (wrappers generated server-side)
  - Added triple-detection system for robust block alignment detection
- **🔧 Fixed horizontal scroll issues on mobile and desktop**
  - Added responsive max-width constraints using `min()` CSS function
  - Implemented smart margin system that prevents content overflow
  - Enhanced mobile responsiveness for featured image titles
- **📱 Improved mobile responsiveness**
  - Reduced padding and font sizes for featured image titles on mobile
  - Added responsive breakpoints for better mobile experience
- **🛡️ Enhanced code safety and maintainability**
  - Added comprehensive safety checks to prevent double-wrapping
  - Improved error handling and edge case coverage
  - Updated documentation and code comments

---

## Description

**Yak** is a lightweight, highly customized Genesis child theme created for modern WordPress development workflows. Built with accessibility, performance, and flexibility in mind, it serves as a rock-solid foundation for custom projects, particularly when paired with **Advanced Custom Fields Pro** and the **Genesis Framework**.

Yak includes carefully layered CSS architecture, robust block editor support, and a growing ecosystem of companion plugins (all GitHub-based). The theme is intended for developers and agencies who want full control over design systems, layout tooling, and editor behavior without bloated features or opinionated design defaults.

---

## Features

- 🔧 **Genesis Framework** child theme (Genesis must be installed)
- ⚙️ **ACF Pro Required** (for theme options, blocks, and layout control)
- 🌈 Custom editor color palette + typography options
- 💅 Modern CSS using cascade layers (`reset`, `yak-base`, `yak-layout`, `yak-components`, `yak-blocks`, `yak-utilities`, `yak-overrides`)
- 🧱 Fully customized Gutenberg block support with refined editor styles
- 🧭 Mobile-first layout system with container queries and layout helpers
- 🧩 Custom components: modals, mega menus, collapsible panels, featured image overlays
- ♿️ Accessibility-conscious design (skip links, screen reader text, etc.)
- 🧰 Developer-first utilities: font scaling, alignment, visibility, spacing
- 🛠 Optional companion plugins for cards, events, media protection, login UI, and more

---

## Installation

1. Install and activate the [Genesis Framework](https://my.studiopress.com/themes/genesis/).
2. Install and activate [Advanced Custom Fields Pro](https://www.advancedcustomfields.com/pro/).
3. Install and activate the Yak theme.
4. (Optional) Install recommended Yak companion plugins (listed in Appearance → Theme Settings → Plugins tab).

---

## Theme Settings

Yak uses ACF Pro to define a fully customizable **Theme Settings Panel**, accessible via:

```
Appearance → Theme Settings
```

### Available Panels:

- **Brand Colors** — Choose a base color and auto-generate accessible variants
- **Editor Color Palette** — Select which swatches should appear in the block editor
- **Typography** — Set base font size, ratio, line height system, and typefaces
- **Login Screen** — Customize background image or gradient, logo, and button style
- **Layout & Display Options** — Control featured image overlays, search UI, etc.
- **Plugin Recommendations** — Quick links and install status for Yak-compatible plugins

---

## CSS Architecture

Yak's CSS uses cascade layers and a clear file structure for maintainability and extensibility. See the `style.css` file for a detailed Table of Contents.

### Layer Structure:

- `RESET` — Normalize browser defaults  
- `BASE` — Root variables, typography, spacing, accessibility  
- `BLOCKS` — Gutenberg block styles  
- `LAYOUT` — Page structure, containers, breakpoints, featured overlays  
- `COMPONENTS` — UI elements (buttons, modals, navigation, etc.)  
- `UTILITIES` — Reusable utility classes  
- `OVERRIDES` — Optional last-layer tweaks  

CSS variables are declared globally and drive the entire design system, including spacing, typography scales, and color tokens like:

```css
--yak-color-primary
--yak-font-base
--yak-font-xl
--yak-padding-block
```

---

## Recommended Plugins

Yak is optimized to work with a growing suite of optional companion plugins:

- **Tomatillo Design ~ Info Cards**  
- **Tomatillo Design ~ Events Calendar**  
- **Tomatillo Design ~ AVIF Everywhere**  
- **Tomatillo Design ~ Site Manager Role**  
- **Tomatillo Design ~ Yakstretch Cover Block**  
- **Tomatillo Design ~ Simple Collapse**  

These plugins are listed and checked in the Yak Theme Settings under the Plugins tab, and can be installed directly via GitHub.

---

## Development Notes

Yak is built for serious WordPress developers who want full control:

- Modular PHP architecture for theme functions and editor logic
- ACF-based blocks and options pages with custom styling wrappers
- Accessible JavaScript enhancements via `yakstrap.js`
- Sensible defaults with minimal bloat
- GitHub-first plugin ecosystem

### In-repo documentation (canonical)

Durable technical docs live under **`docs/`** as **PHP container files** (heredoc markdown, `ABSPATH` guard; not meant for direct web access — `docs/index.php` blocks directory listing):

| File | Purpose |
|------|---------|
| [`docs/spec.php`](docs/spec.php) | **Canonical spec** — requirements, behavior, settings, hooks |
| [`docs/decisions.php`](docs/decisions.php) | Architecture decisions and tradeoffs (dated) |
| [`docs/dev-notes.php`](docs/dev-notes.php) | File map, conventions, implementation notes |

Markdown adjuncts ([`docs/tooling.md`](docs/tooling.md), [`docs/admin-ui.md`](docs/admin-ui.md)) are editor-friendly; if anything conflicts, **`spec.php` wins**.

---

## Support

Yak is not a commercial product and does not include end-user support.  
Developers are encouraged to fork, extend, and customize as needed.

---

## License

This theme, like WordPress itself, is licensed under the [GPL v2 or later](https://www.gnu.org/licenses/gpl-2.0.html).
