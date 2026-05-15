# Yak Theme

A fast, modern, developer-focused child theme built on the Genesis Framework — perfect for custom client builds with ACF Pro, advanced block styling, and powerful layout tools.

---

**Contributors:** tomatillodesign  
**Tags:** genesis, custom-theme, block-editor, accessibility-ready, developer-friendly  
**Requires at least:** 6.0  
**Tested up to:** 6.5  
**Requires PHP:** 7.4  
**Version:** 1.0.7  
**License:** [GPL v2 or later](https://www.gnu.org/licenses/gpl-2.0.html)

---

## Repository notes (Tomatillo internal)

**1.0.6** is a deliberate **reversion**: the checked-in theme matches the **1.0.4** codebase. **1.0.5** is **withdrawn**—do not deploy it for new projects.

The **1.0.5 commit history is preserved** for archival review:

- Branch: `archive/main-through-1.0.5`
- Tag: `v1.0.5-archived`

New features ship on **`main`** starting from **1.0.6**; **1.0.7** adds the WP-CLI Theme Settings agent commands documented below.

---

## Changelog

### Version 1.0.7

- **WP-CLI Theme Settings agent:** `wp yak settings schema`, `get`, and `patch` expose self-describing JSON for all ACF **options-page** Theme Settings fields (colors, typography, layouts, performance, login, branding, etc.).
- **Docs for agents:** **`AGENTS.md`** (quick playbook) and **readme.md** — full command reference, PATCH value shapes, logo/color/repeater examples, authorized `--user`, troubleshooting.
- **`patch`** requires `--user=<id>` for a user allowed by Yak Theme Settings authorization (`yak_user_is_yak_authorized`).
- **Safety:** Updates to **`yak_allowed_users`** and **`yak_dev_mode`** via `patch` are blocked unless `define( 'YAK_AGENT_ALLOW_PERMISSION_FIELDS', true );` is set in `wp-config.php` (see Agent CLI section).

### Version 1.0.6

- **↩️ Reversion baseline:** Theme files match the **1.0.4** release (no **1.0.5** code).
- **📚 Archival refs:** Full **1.0.5** development remains reachable via branch **`archive/main-through-1.0.5`** and tag **`v1.0.5-archived`** on GitHub (`tomatillodesign/yak`).

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
- 🤖 **WP-CLI Theme Settings API** — self-documenting JSON schema plus read/write for ACF Theme Settings (logo, colors, typography, layouts, login, performance); see **Agent CLI** below and **`AGENTS.md`**

---

## Installation

1. Install and activate the [Genesis Framework](https://my.studiopress.com/themes/genesis/).
2. Install and activate [Advanced Custom Fields Pro](https://www.advancedcustomfields.com/pro/).
3. Install and activate the Yak theme.
4. (Optional) Install recommended Yak companion plugins (listed in Appearance → Theme Settings → Plugins tab).
5. **Coding assistants / automation:** Read **`AGENTS.md`** and **`readme.md` → Agent CLI** before changing Theme Settings via WP-CLI (`wp yak settings …`).

---

## Agent CLI — Theme Settings (1.0.7)

Use these commands on a **trusted machine** where WP-CLI can bootstrap this WordPress install (e.g. Local WP, SSH). There is **no HTTP REST** surface for this feature in 1.0.7.

**Companion doc for assistants:** See **`AGENTS.md`** in this theme directory for a short playbook (same rules, optimized for tooling).

### For coding assistants — recommended workflow

1. Resolve the WordPress root path (contains `wp-config.php`). On Local WP this is typically `…/app/public`.
2. Run **`wp yak settings schema --path=… --pretty`** and treat the output as the **only** authoritative list of Theme Settings **field names**, **types**, **`choices`**, and **repeater `sub_fields`**.
3. Optionally run **`wp yak settings get --path=… --pretty`** to see live values (logo IDs, hex colors, repeaters, toggles).
4. Build a **flat JSON object** whose keys are **top-level ACF field names** from the schema. Values must match the shapes below.
5. Apply with **`wp yak settings patch … --user=<authorized_user_id> --path=…`** using a user ID that passes **`yak_user_is_yak_authorized()`** (see **Authorized `--user` IDs**).

Never guess field names: names differ slightly from panel titles (e.g. logo field is **`yak_logo_image`**, not “Logo”).

### Commands reference

**Prerequisites:** Yak active, Genesis + ACF Pro loaded as usual; WP-CLI available (`wp --info`).

Export schema (self-documentation for agents):

```bash
wp yak settings schema --path=/path/to/wordpress --pretty
```

Dump current option values:

```bash
wp yak settings get --path=/path/to/wordpress --pretty
```

Merge a **partial** update (only keys present in the JSON are written):

```bash
wp yak settings patch ./settings-patch.json --user=2 --path=/path/to/wordpress
```

Pipe JSON instead of a file:

```bash
wp yak settings patch --user=2 --path=/path/to/wordpress < ./settings-patch.json
```

### PATCH value shapes (Theme Settings)

| Schema `type` | Send in JSON as | Notes |
|---------------|-----------------|-------|
| `image`, `file` | integer | WordPress **attachment post ID**. Use Media Library or `wp media import` first. |
| `color_picker` | string | Hex color `#rrggbb` (validated). |
| `true_false` | boolean or `0`/`1` | Stored as ACF expects after sanitization. |
| `text`, `textarea` | string | Plain text / textarea rules apply. |
| `number`, `range` | number | Respect schema `min` / `max` when present. |
| `button_group`, `radio`, `select` | string | Must be an existing **key** in schema `choices` (not the human label unless key and label match). |
| `user` | array of integers | User IDs that exist in WordPress. Blocked for **`yak_allowed_users`** unless permission constant is set (below). |
| `repeater` | array of objects | Each object keys **must match repeater `sub_field` names only**. Sending a patch **replaces the entire repeater** value for that field. |

Fields with **`writable: false`** (including UI **`message`** fields and, by default, **`yak_allowed_users`** / **`yak_dev_mode`**) cannot be updated via `patch`.

### Examples (confirm keys in `schema` on each site)

**Logo + favicon** (attachment IDs 123 and 456):

```json
{
  "yak_logo_image": 123,
  "yak_favicon": 456,
  "yak_logo_type": "image",
  "yak_logo_max_width": 220,
  "yak_show_site_description": false,
  "yak_sticky_header_desktop": true
}
```

**Primary brand colors** (hex):

```json
{
  "yak_base_color": "#1a4731",
  "yak_accent_color": "#c4553d"
}
```

**Additional palette repeater** (`yak_additional_colors` rows use sub-fields `name` and `hex` — verify in schema):

```json
{
  "yak_additional_colors": [
    { "name": "Brand muted", "hex": "#e8f0ec" },
    { "name": "Deep forest", "hex": "#0f2918" }
  ]
}
```

### Authorized `--user` IDs

`patch` requires **`--user=<id>`** where that WordPress user is allowed to use Yak Theme Settings in the admin:

- **`YAK_PRIMARY_USER_ID`** in `functions.php`
- IDs listed in **`yak_get_manual_allowed_user_ids()`**
- Users listed in the **`yak_allowed_users`** ACF field (once set — usually via wp-admin because PATCH blocks this field by default)

To discover IDs from CLI: `wp user list --path=… --fields=ID,user_login,roles`.

### Safety: permission fields

Updates to **`yak_allowed_users`** and **`yak_dev_mode`** via `patch` are **blocked** unless you define **`YAK_AGENT_ALLOW_PERMISSION_FIELDS`** as **`true`** in `wp-config.php`. Prefer leaving this off so automation cannot lock humans out of Theme Settings.

Optional PHP filters:

```php
// Extend which ACF option-page slugs participate (advanced).
add_filter( 'yak/agent/option_pages', fn( $slugs ) => array_merge( $slugs, [] ) );

// Allow permission-field writes without wp-config constant (still discouraged).
add_filter( 'yak/agent/allow_permission_fields', '__return_true' );
```

### Troubleshooting

- **`Unknown … field`** — Key not in schema output; typo or wrong site/theme version.
- **`not writable`** — Field is informational (`message`) or permission-blocked.
- **`Expected numeric`** / **`Invalid hex`** — Value shape does not match field type.
- **`Repeater … Unknown sub-fields`** — Row objects include keys not listed under `sub_fields` for that repeater.
- **`not authorized`** — Pick a different `--user` ID that passes Yak authorization.

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

**Programmatic updates (agents, scripts):** Use WP-CLI **`wp yak settings`** as documented in **Agent CLI — Theme Settings** above and **`AGENTS.md`**. Run **`schema`** before changing values; patch uses the same field names as this screen (e.g. `yak_logo_image`, `yak_base_color`).

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
- **`AGENTS.md`** + **`readme.md` → Agent CLI** — guidance for coding assistants updating Theme Settings via `wp yak settings`

---

## Support

Yak is not a commercial product and does not include end-user support.  
Developers are encouraged to fork, extend, and customize as needed.

---

## License

This theme, like WordPress itself, is licensed under the [GPL v2 or later](https://www.gnu.org/licenses/gpl-2.0.html).
