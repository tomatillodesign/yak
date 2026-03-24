# Yak admin UI — scope and direction

This documents **theme-owned** admin surfaces only. **ACF Theme Settings** field groups are intentionally unchanged structurally; ACF’s toggle UI (`ui => 1` on `true_false`) already matches current binary-setting practice.

## Mode

**Mode A — System UI (admin):** restrained, functional, low noise. No attempt to rebuild core wp-admin globally.

## Design direction (conservative pass)

| Input | Choice |
| ----- | ------ |
| **Purpose** | Clarify Yak-built tools (Performance & Tools, import/export, dashboard widget) without fighting ACF. |
| **Audience** | Site owners and authorized theme editors in wp-admin. |
| **Tone** | Calm, editorial — clear headings, short ledes, no emoji in primary headings. |
| **Differentiation** | Scoped purple admin chrome (existing) + token-driven cards for Yak tools only. |

## Canonical patterns (this theme)

| Surface | Pattern |
| ------- | ------- |
| Performance & Tools | Card sections (`.yak-import-export-section`), token spacing/radius, `role="region"` + `aria-labelledby`. |
| Inline notices (AJAX) | `.yak-notice` variants driven by CSS variables in `:root`. |
| Dashboard welcome | Flex layout `.yak-dash-welcome` with structured contact list. |
| List table ID | `.yak-admin-id-pill` monospace chip. |

## Token authority

New admin values live in [`css/clb-custom-yak-admin-styles.css`](../css/clb-custom-yak-admin-styles.css) under `:root` (`--yak-admin-*` alongside existing `--yak-*` / `--wp--preset--*`).

## Design debt (explicit)

| Item | Why not done |
| ---- | ------------- |
| Full `.yak-settings-layout` shell on every options page | Would fight ACF’s markup and your existing “good place” screens. |
| Replacing ACF `select` with custom segmented controls | Higher risk; ACF `radio` + horizontal layout already used where it fits. |
| Core `.nav-tab-wrapper` removal | Out of scope; would affect non-Yak screens. |

## Related

- [`tooling.md`](tooling.md) — filters and lifecycle.
- Admin stylesheet enqueued from [`functions.php`](../functions.php) (`clb_custom_admin_styles`) with `filemtime` for cache-friendly updates.
