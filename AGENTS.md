# Yak Theme — guidance for AI coding agents

Use this file when automating **Yak Theme Settings** (Appearance → Theme Settings). Those settings are stored as **ACF options-page fields**. This theme exposes them to WP-CLI so agents can read/write without clicking wp-admin.

## Required setup

- WordPress with **Genesis**, **ACF Pro**, and **Yak** active.
- **WP-CLI** installed and able to bootstrap the site (`wp core version --path=…`).
- For **writes**, a WordPress user ID that passes **`yak_user_is_yak_authorized()`** (see `YAK_PRIMARY_USER_ID`, `yak_get_manual_allowed_user_ids()`, and optional ACF field `yak_allowed_users` in `functions.php`). Pass that ID as **`--user=<id>`** on `patch`.

## Commands (always use schema first)

```bash
# Machine-readable field definitions (names, types, choices, repeaters).
wp yak settings schema --path=/path/to/wordpress --pretty

# Current stored values for every Theme Settings root field.
wp yak settings get --path=/path/to/wordpress --pretty

# Merge a partial JSON object (top-level keys = ACF field names).
wp yak settings patch ./patch.json --user=AUTHORIZED_USER_ID --path=/path/to/wordpress
```

On **Local WP**, `--path` is usually the site’s `public` folder (directory that contains `wp-config.php`).

## How to change common options

| Goal | Approach |
|------|----------|
| Logo / favicon | PATCH **`yak_logo_image`**, **`yak_favicon`** with **attachment IDs** (integers). Upload media first (`wp media import …`) if needed. |
| Logo layout / width | **`yak_logo_type`** (`button_group` keys from schema), **`yak_logo_max_width`** (number). |
| Brand colors | **`yak_base_color`**, **`yak_accent_color`** — hex strings `#rrggbb`. Repeaters (**`yak_additional_colors`**, editor palette, gradients) — JSON **array of row objects**; each row uses only **`sub_fields`** shown in schema (`name`, `hex`, etc.). PATCH **replaces** the whole repeater for that field. |
| Typography / layouts / login / performance | Same pattern: inspect schema → PATCH top-level keys only. |

## PATCH value rules

- **Unknown top-level keys** → command fails (do not invent names; copy from schema).
- **`writable: false`** in schema → cannot PATCH (includes `message` fields and, by default, **`yak_allowed_users`** / **`yak_dev_mode`**).
- To allow PATCH on permission fields: define **`YAK_AGENT_ALLOW_PERMISSION_FIELDS`** as **`true`** in `wp-config.php` (human decision only).

## Human-facing detail

See **readme.md** → **Agent CLI — Theme Settings** for extended examples, troubleshooting, and filters (`yak/agent/option_pages`, `yak/agent/allow_permission_fields`).
