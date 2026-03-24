# Yak theme — developer tooling (roadmap)

This file is the **single index** for automation we are adding around the [Yak theme repository](https://github.com/tomatillodesign/yak.git). Cursor command stubs live in [`.cursor/commands/`](../.cursor/commands/). Admin UI scope and tokens: [admin-ui.md](admin-ui.md).

**Canonical product docs** (PHP containers in this folder): [spec.php](spec.php) (behavior and APIs), [decisions.php](decisions.php), [dev-notes.php](dev-notes.php). [index.php](index.php) silences directory browsing.

## Planned commands

| Command | Purpose (planned) |
|--------|-------------------|
| **preflight** | Run before a release: PHP syntax check on theme PHP files, optional `phpcs` if configured, verify `Version` in `style.css` matches `readme.md` / `README.txt`, flag common issues. |
| **docs** | Regenerate or sync user-facing snippets from code (e.g. Theme Settings panel list, changelog pointers); keep GitHub readme aligned with `readme.md`. |
| **update-ui** | Bump version strings in a coordinated way (`style.css`, readmes, optional `package.json` if introduced) and produce a short release checklist. |

## Conventions (1.0.5+)

- **Theme version** is authoritative in `style.css` header; duplicate in `readme.md` and `README.txt` changelog.
- **WordPress:** minimum **6.8** (`Requires at least` in `style.css`, mirrored in readmes).
- **PHP:** minimum **8.2** (`Requires PHP` in `style.css`, mirrored in readmes). Preflight / CI should lint on 8.2+.
- **Theme Settings capability:** `yak_manage_theme_settings` (`YAK_CAP_THEME_SETTINGS` in `functions.php`). Any new ACF options subpages or privileged AJAX must use this capability, not `manage_options`, unless the feature is intentionally general admin.
- **Developer mode output:** `YAK_DEBUG` in `wp-config.php` must be `true` for the Theme Settings “Developer Mode” toggle to affect the site (`yak_is_dev_mode_active()` in `inc/yak-theme-settings.php`). Default is off.
- **Lifecycle:** `inc/yak-theme-lifecycle.php` — on activation of this child, stores option `yak_theme_db_version` (from `style.css` Version). Hooks: `yak_theme_after_switch`, `yak_theme_on_leave` for future migrations/cleanup.

### Performance-related filters

| Filter | Purpose |
|--------|---------|
| `yak_should_load_frontend_theme_scripts` | Return `false` to skip Yak front JS on a request (runs after feed/embed guard). |

**Font Awesome:** The kit is enqueued on every frontend and admin request (`yak-fontawesome`); version is `null` so the URL stays stable and HTTP caching (browser + Font Awesome CDN) applies.

### Import hardening filters

| Filter | Purpose |
|--------|---------|
| `yak_import_json_max_bytes` | Max upload string length (default `5 * MB_IN_BYTES`). |
| `yak_import_denied_option_names` | Array of `options_*` keys to refuse on import. |

## Next steps

Implement each stub under `.cursor/commands/` as real Cursor custom commands or npm/composer scripts, then link the exact invocation here.
