<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return <<<'DOC'
# Yak Theme — Architecture decisions

Dated entries. When behavior changes, add a new entry or revise with a new date.

---

## 2025-03 — Theme Settings capability model

**Decision:** Use dedicated capability `yak_manage_theme_settings` instead of granting `manage_options` to Yak-authorized users.

**Why:** Granting `manage_options` via `user_has_cap` affected the entire admin for those users (privilege escalation for Editors added to the allow list).

**Tradeoff:** Authorized users need the virtual cap for Theme Settings only; other plugins that check `manage_options` are unaffected.

---

## 2025-03 — ACF required at runtime

**Decision:** Hard bail if `get_field` is missing (admin error page, frontend maintenance).

**Why:** Theme options and layout logic assume ACF; running without it produces broken UX.

**Rejected:** Soft degradation with defaults everywhere (high maintenance, easy to ship broken sites).

---

## 2025-03 — `theme.json` without duplicate editor palette

**Decision:** `theme.json` carries layout, spacing, and typography; editor color palette remains ACF-driven.

**Why:** Avoid two sources of truth for swatches and conflicts in the block editor UI.

---

## 2025-03 — Font Awesome always enqueued

**Decision:** Load Font Awesome kit on every front and admin request when the theme is active.

**Why:** Companion Tomatillo plugins expect the kit present; conditional loading broke integration.

**Tradeoff:** Extra third-party script; mitigated with stable URL (`null` version) for HTTP caching.

---

## 2025-03 — Developer mode requires `YAK_DEBUG`

**Decision:** ACF “Developer Mode” toggle does nothing for output unless `YAK_DEBUG` is true in `wp-config.php`.

**Why:** Prevent accidental Genesis hook markers and body classes on production if the toggle is left on.

---

## 2025-03 — PHP 8.2 and WordPress 6.8 floors

**Decision:** Declare `Requires PHP: 8.2` and `Requires at least: 6.8` in `style.css`.

**Why:** Align with modern hosting and WordPress APIs (e.g. speculation rules, block editor) without legacy polyfills.

DOC;
