<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return <<<'DOC'
# Yak Theme — Developer notes (internal)

How the codebase is organized and where to change things. For **what** the theme promises externally, see `spec.php`.

---

## Entry point

- **`functions.php`** — Loads Genesis, constants (`YAK_PRIMARY_USER_ID`, `YAK_CAP_THEME_SETTINGS`, `YAK_DEBUG`), ACF gate, includes, enqueues, Genesis hooks, Theme Settings registration, access control, SearchWP, lifecycle require.

---

## Agent hook map (customizations at a glance)

Use this when you need **where behavior is wired**, not a line-by-line dump. **Theme-owned extender APIs** (filters/actions other code may rely on) stay documented in `spec.php` — this section maps **WordPress + Genesis + ACF** registrations.

**Full machine inventory:** search the theme for `add_action(` and `add_filter(` (include `page-templates/`, `home.php`, `search.php`).

### Layout and Genesis structure

| Area | Hooks / notes | Primary files |
|------|----------------|---------------|
| Sidebars removed | `widgets_init`, `after_setup_theme` (sidebar output) | `functions.php` |
| Full-width layout | `genesis_pre_get_option_site_layout` | `functions.php` |
| Header / nav | `genesis_header` → `genesis_do_nav`; `genesis_footer` → `genesis_do_subnav`; third nav `genesis_before_header` | `functions.php` |
| Footer widgets | `genesis_before_footer` | `functions.php` |
| Site footer markup | `genesis_footer` | `functions.php` |
| Logo / title | `genesis_site_title`; default header markup removed on `init` | `inc/yak-theme-settings.php` |
| Hello bar | `genesis_before_header` | `functions.php` |
| Featured banner | `genesis_after_header` | `functions.php` |
| Post title + subtitle | `genesis_post_title_output` | `functions.php` |

### Archives, loop, and blog

| Area | Hooks / notes | Primary files |
|------|----------------|---------------|
| Archive wrappers | `genesis_loop` / `genesis_after_loop` | `functions.php` |
| Archive entry cleanup | `genesis_before`; conditional `genesis_post_info` / `genesis_entry_content` | `functions.php` |
| Blog intro | `genesis_loop` (priority 5) | `home.php` |
| Edit post link hidden | `genesis_edit_post_link` | `functions.php` |
| GPB portfolio disabled | `init` | `functions.php` |

### Blocks and front markup

| Area | Hooks / notes | Primary files |
|------|----------------|---------------|
| Alignwide/full wrappers | `render_block` → `yak_wrap_align_blocks` | `functions.php` |
| Body classes (featured align, roles, layouts) | `body_class` (several callbacks across files) | `functions.php`, `inc/yak-layouts.php`, `inc/yak-theme-settings.php` |
| Gutenberg front + editor CSS | `wp_enqueue_scripts`, `enqueue_block_editor_assets` | `lib/gutenberg/init.php`, `lib/gutenberg/inline-styles.php` |
| Info Cards modal mount | `wp_footer` | `functions.php` |

### Search

| Area | Hooks / notes | Primary files |
|------|----------------|---------------|
| Placeholder text | `genesis_search_text` | `functions.php` |
| Global modal / mobile search | `wp_footer`, `wp_head` | `functions.php` |
| Search template | `genesis_before_content`, `genesis_before_loop`, `genesis_entry_content` | `search.php` |
| SearchWP excerpts | `genesis_entry_content` / `genesis_before_loop` patterns | `functions.php`, `search.php` |

### Assets (enqueue)

| Area | Hooks / notes | Primary files |
|------|----------------|---------------|
| Main front CSS/JS | `wp_enqueue_scripts` (priority 100) | `functions.php` |
| Editor-only assets | `enqueue_block_editor_assets` | `functions.php`, `lib/gutenberg/*`, `inc/yak-colors.php` |
| Font Awesome + crossorigin | `wp_enqueue_scripts`, `admin_enqueue_scripts`, `script_loader_tag` | `functions.php` |
| Yakstrap | `wp_enqueue_scripts` | `functions.php` |
| WooCommerce CSS + Dashicons when needed | `wp_enqueue_scripts` | `lib/woocommerce/*`, `functions.php` |
| Admin theme CSS | `admin_enqueue_scripts` | `functions.php` |

### Design tokens (CSS variables)

| Area | Hooks / notes | Primary files |
|------|----------------|---------------|
| Colors / swatches / gradients | `wp_head`, `admin_head`, `after_setup_theme`, block editor | `inc/yak-colors.php` |
| Typography | `wp_head`, `admin_head`, `after_setup_theme` | `inc/yak-typography.php` |
| Layout / featured image options | `wp_head`, `admin_head`, `body_class` | `inc/yak-layouts.php` |

### Admin (non-ACF UI)

| Area | Hooks / notes | Primary files |
|------|----------------|---------------|
| Post type ID column | `admin_init` → `manage_*_posts_columns` etc. | `functions.php` |
| Users ID column | `manage_users_*` filters | `functions.php` |
| Dashboard widget + remove defaults | `wp_dashboard_setup` | `functions.php` |
| Admin footer text | `admin_footer_text` | `functions.php` |
| Nav menus help | `admin_head-nav-menus.php`, `admin_footer-nav-menus.php` | `functions.php` |
| Theme Settings cap + menu hide + page guard | `user_has_cap`, `admin_init`, `admin_menu` | `functions.php` |
| Hide ACF permissions group for unauthorized | `acf/get_field_group` | `functions.php` |
| ACF options pages | `acf/init` (two registrations) | `functions.php` |
| Import/export scripts + AJAX | `admin_footer`, `wp_ajax_yak_*` | `inc/yak-import-export.php` |
| Performance (speculation, crossfade output) | `wp_speculation_rules_configuration`, `wp_load_speculation_rules`, `wp_head` | `inc/yak-performance.php` |
| Dev mode admin notice | `admin_notices` | `inc/yak-theme-settings.php`, `functions.php` |

### ACF / Theme Settings (runtime)

| Area | Hooks / notes | Primary files |
|------|----------------|---------------|
| Favicon output | `wp_head`, `admin_head`, `login_head` | `inc/yak-theme-settings.php` |
| Favicon crop on save | `acf/save_post` | `inc/yak-theme-settings.php` |
| Sticky header / site desc body classes | `body_class` | `inc/yak-theme-settings.php`, `inc/yak-layouts.php` |
| Color palette in ACF picker | `acf/input/admin_footer`, `acf/input/admin_enqueue_scripts` | `functions.php`, `inc/yak-colors.php` |
| Conditional ACF fields (layouts) | `acf/prepare_field` (keys) | `inc/yak-layouts.php` |

### Login, WooCommerce, lifecycle, integrations

| Area | Hooks / notes | Primary files |
|------|----------------|---------------|
| Login screen | `login_enqueue_scripts`, `login_message` | `inc/yak-custom-login.php` |
| WooCommerce styles, images, notice, dismiss AJAX | various `woocommerce_*`, `admin_*`, `switch_theme` | `lib/woocommerce/*` |
| Theme switch version option + extension actions | `after_switch_theme`, `switch_theme` | `inc/yak-theme-lifecycle.php` |
| Tracklight | per-option `add_option_*`, `update_option_*`, `deleted_option` | `inc/class-tracklight-integration.php` |

### Page templates

| Template | Notable hooks | File |
|----------|---------------|------|
| Landing | `body_class`, dequeue skip links | `page-templates/landing.php` |
| Wide content | `body_class` | `page-templates/wide-page-content.php` |

---

## Directory map

| Path | Role |
|------|------|
| `inc/yak-theme-settings.php` | ACF field groups (permissions, logo, plugins list), favicon, dev mode, sticky header |
| `inc/yak-colors.php` | Color system, editor palette, gradients, admin swatch UI |
| `inc/yak-typography.php` | Typography options output |
| `inc/yak-layouts.php` | Layout / featured image options |
| `inc/yak-performance.php` | Speculative loading + crossfade ACF; WP 6.8+ API filters |
| `inc/yak-import-export.php` | Performance page import/export AJAX + ACF message field HTML |
| `inc/yak-custom-login.php` | Login screen customization |
| `inc/yak-theme-lifecycle.php` | `after_switch_theme` / `switch_theme` stubs |
| `inc/class-tracklight-integration.php` | Tracklight hooks |
| `lib/helper-functions.php` | Genesis Sample color helpers (legacy names) |
| `lib/customize.php` | Customizer (mostly commented) |
| `lib/gutenberg/init.php` | Block body classes, content width hook |
| `lib/gutenberg/inline-styles.php` | Inline appearance CSS on registered handles |
| `lib/woocommerce/*` | WooCommerce styles, notice, dismiss AJAX |
| `css/` | `style.css` (layers), `yak-blocks.css`, admin styles, editor bridge |
| `js/` | `yakstrap.js`, mobile menu, block enhancements, custom scripts |
| `theme.json` | Editor/layout tokens (no ACF color palette duplication) |

---

## Constants (functions.php)

- `YAK_PRIMARY_USER_ID` — Primary Yak admin user ID for access.
- `YAK_CAP_THEME_SETTINGS` — String `yak_manage_theme_settings`.
- `YAK_DEBUG` — Optional `wp-config.php` define; default false if omitted.

---

## Security checklist for new features

- Theme Settings pages: capability `YAK_CAP_THEME_SETTINGS`; add slug to `$restricted_pages` in `yak_restrict_theme_settings_page_access` and menu removal if new subpage.
- AJAX: `wp_ajax_*` only (no unintended `nopriv`); `check_ajax_referer`; capability check matching sensitivity of data.
- Output: `esc_html`, `esc_url`, `esc_attr` per context; untrusted HTML only through `wp_kses_post` when intentional.

---

## Performance notes

- `render_block` filter `yak_wrap_align_blocks` runs per block; keep logic cheap.
- Conditional front scripts: `yak_should_load_frontend_theme_scripts` (feeds/embeds off); mobile menu if Primary menu exists.
- Speculation rules: theme does not force-enable core flag; only adjusts configuration when enabled.

---

## Styling

- Frontend: cascade layers in `style.css`; variables mirror `theme.json` / editor bridge where applicable.
- Admin: `clb-custom-yak-admin-styles.css`; scope new rules under specific body classes or `#yak_dashboard_widget` to avoid global admin bleed.

---

## Markdown adjuncts

Human- and editor-friendly notes also live as **markdown** (not PHP-wrapped):

- `tooling.md` — Version bumps, filters, lifecycle summary.
- `admin-ui.md` — Admin UI scope and tokens.

If `spec.php` and markdown disagree, **trust `spec.php`** and update markdown.

DOC;
