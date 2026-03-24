<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return <<<'DOC'
# Yak Theme — Specification (canonical)

This file is the single source of truth for **what the theme is**, **what it requires**, and **how it behaves**. Public readmes (`readme.md`, `README.txt`) are summaries and must not contradict this spec.

---

## Identity

- **Type:** WordPress **child theme** of Genesis (`Template: genesis` in `style.css`).
- **Text domain:** `yak`
- **Version:** See `style.css` header `Version:` (authoritative).

---

## Platform requirements

| Requirement | Minimum | Declared in |
|-------------|---------|-------------|
| WordPress | **6.8** | `style.css` `Requires at least`, readmes |
| PHP | **8.2** | `style.css` `Requires PHP`, readmes |
| Genesis Framework | Active parent theme | Installation |
| Advanced Custom Fields (ACF) | `get_field` available (Pro expected for options UI) | Runtime: theme bails with `wp_die` if missing |

---

## Core behavior

1. **Loads Genesis** via `require_once get_template_directory() . '/lib/init.php'` from parent.
2. **ACF gate:** If `get_field` does not exist, admin shows error; frontend shows maintenance message. Recovery: user with `switch_themes` may use `?yak_force_switch=1` in admin to switch to Twenty Twenty-Five.
3. **Layout:** Unregisters default Genesis sidebars and three-column layouts; removes `genesis_do_sidebar`; forces full-width content layout filter.
4. **Block editor:** `editor-styles`, `align-wide`, `theme.json` (layout, spacing, typography). Editor colors and gradients primarily from ACF (`inc/yak-colors.php`), not duplicated in `theme.json` palette.
5. **Frontend assets:** Conditional theme JS (feeds/embeds skipped); Font Awesome kit on all front + admin; Dashicons on front only on WooCommerce views when theme WC CSS applies; `yak-blocks.css`, `yakstrap.js`, etc. per `functions.php`.
6. **Search:** Custom Genesis search results markup; optional SearchWP highlighting when plugin present.

---

## Theme Settings (ACF)

**Menu:** Appearance → Theme Settings (`theme-settings`).

**Capability:** All ACF options pages and Yak import/export AJAX use **`yak_manage_theme_settings`** (`YAK_CAP_THEME_SETTINGS` in `functions.php`). Granted only to **Yak-authorized** users via `user_has_cap` when that specific capability is checked — never a global `manage_options` grant.

**Authorization sources (any match):** manual ID list `yak_get_manual_allowed_user_ids()`, constant `YAK_PRIMARY_USER_ID`, ACF option `yak_allowed_users`.

**Subpages (capability on each):** Colors, Typography, Layouts, Performance & Tools, Login Screen.

**Notable subsystems:**

- **Performance & Tools** (`yak-options-performance`): Speculative loading filters (`inc/yak-performance.php`), import/export UI (`inc/yak-import-export.php`).
- **Import/export:** AJAX actions `yak_export_settings`, `yak_import_settings`; nonce + `YAK_CAP_THEME_SETTINGS`; JSON size limit; denylist and transient skips (filters documented in `tooling.md`).

---

## Developer mode

- ACF toggle **Developer Mode** alone does **not** output hook markers.
- **`YAK_DEBUG`** must be `true` in `wp-config.php` **and** ACF toggle on for `yak_is_dev_mode_active()` to be true.
- Output gated by `current_user_can( YAK_CAP_THEME_SETTINGS )` for Genesis hook markers and dev body class.

---

## Lifecycle

- **`inc/yak-theme-lifecycle.php`:** On activation of this child, sets option `yak_theme_db_version` from `style.css` Version. Actions: `yak_theme_after_switch`, `yak_theme_on_leave` for extensions.

---

## Integrations (optional)

- **WooCommerce:** `lib/woocommerce/*`; Genesis Connect notice + dismiss AJAX (nonce + `manage_woocommerce`).
- **Tracklight:** `inc/class-tracklight-integration.php`; filter `yak/tracklight/option_names`.
- **SearchWP:** Custom excerpt/title in search template when APIs exist.

---

## Public integration contracts (filters / actions)

Authoritative lists for extenders:

**Actions**
- `yak_theme_after_switch` — `( $version, $old_name, $old_theme )` after version option updated.
- `yak_theme_on_leave` — `( $new_name, $new_theme, $old_theme )` when switching away from Yak.

**Filters**
- `yak_should_load_frontend_theme_scripts` — bool; skip theme front JS when false (after feed/embed guard).
- `yak_import_json_max_bytes` — int.
- `yak_import_denied_option_names` — array of `options_*` keys to block on import.
- `yak/tracklight/option_names` — array of option names for Tracklight.

Additional hooks exist throughout `functions.php` and `inc/*`; search `add_filter` / `add_action` for a full inventory when extending.

---

## Admin UI (theme-owned)

- Styles: `css/clb-custom-yak-admin-styles.css` (enqueued with `filemtime`).
- Scoped surfaces: Performance & Tools import/export, dashboard widget, list-table ID column. See `admin-ui.md` for design scope.

---

## Documentation map

| File | Role |
|------|------|
| `docs/spec.php` (this) | Canonical behavior and requirements |
| `docs/decisions.php` | Why key choices were made |
| `docs/dev-notes.php` | File layout, **agent hook map** (where WP/Genesis/ACF registrations live), implementation notes |
| `docs/tooling.md` | Release/tooling conventions (markdown adjunct) |
| `docs/admin-ui.md` | Admin UI scope (markdown adjunct) |

DOC;
