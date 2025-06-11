# Yak Theme

A fast, modern, developer-focused child theme built on the Genesis Framework — perfect for custom client builds with ACF Pro, advanced block styling, and powerful layout tools.

---

**Contributors:** tomatillodesign  
**Tags:** genesis, custom-theme, block-editor, accessibility-ready, developer-friendly  
**Requires at least:** 6.0  
**Tested up to:** 6.5  
**Requires PHP:** 7.4  
**Version:** 1.0.1  
**License:** [GPL v2 or later](https://www.gnu.org/licenses/gpl-2.0.html)

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

---

## Support

Yak is not a commercial product and does not include end-user support.  
Developers are encouraged to fork, extend, and customize as needed.

---

## License

This theme, like WordPress itself, is licensed under the [GPL v2 or later](https://www.gnu.org/licenses/gpl-2.0.html).
