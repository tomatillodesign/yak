---
description: Pre-release checks for Yak theme (PHP lint, version consistency, future phpcs)
---

# preflight (stub)

Run before tagging or shipping a Yak release.

## Intended steps (to implement)

1. `php -l` on all `*.php` under the theme root (recursive, exclude `node_modules` if ever added).
2. Confirm `Version:` in `style.css` matches `readme.md` and `README.txt`.
3. Optional: run PHPCS with the project ruleset when one exists.
4. Quick grep for forbidden patterns (e.g. `error_log(`, raw `$_POST` without nonce) — expand over time.

Until scripted, run manually from the theme directory:

```bash
find . -name '*.php' -not -path './vendor/*' -print0 | xargs -0 -n1 php -l
```
