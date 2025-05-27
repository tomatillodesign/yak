/**
 * ============================================================================
 * 🧠 Yak Theme – Frontend Utility Scripts
 * ============================================================================
 *
 * This file includes a collection of JavaScript enhancements and layout helpers
 * that improve frontend block behavior, layout rendering, and responsive interactivity.
 * These utilities are designed to work alongside Yak’s custom CSS and block enhancements.
 *
 * Included Features:
 *
 * 1. ✅ Alignwide/Alignfull Wrappers:
 *    - Dynamically wraps `.alignwide` and `.alignfull` blocks in structural divs
 *    - Enables consistent horizontal padding and alignment control across devices
 *
 * 2. ✅ Dim Opacity Fix for WP Cover Block:
 *    - Applies precise opacity to `.has-background-dim-*` cover blocks
 *    - Converts class-based values to inline opacity on background spans
 *
 * 3. ✅ Pull Alignment Runtime Logic:
 *    - Evaluates whether `.yak-pull-left` / `.yak-pull-right` blocks have enough space
 *    - Applies negative margins only if layout can support it (with safety checks)
 *    - Adds `yak-pull-active` class when pull is visually viable
 *
 * 4. ✅ Mega Menu Enhancements:
 *    - Detects nested submenus and adds `.yak-has-mega` to parent items
 *    - Adds `.yak-mega-column` class to second-level menu items for grid-based layout
 *
 * 5. ✅ Scroll-Based Header Behavior:
 *    - Tracks scroll position and adds `yak-scrolled` class to `<body>`
 *    - Uses `data-scroll` attribute and debounced updates for smooth transitions
 *
 * 6. ✅ Dynamic Title Width Variable:
 *    - Measures `.title-area` on load and resize
 *    - Sets CSS variable `--yak-title-area-width` on `<html>` for layout sync
 *
 * Notes:
 * - Runs on `DOMContentLoaded`, with debounce and resize listeners where needed
 * - Designed for performance and non-intrusive frontend behavior
 * - Works alongside Yak’s CSS layers and block-enhancements.js for full effect
 *
 * Location: /js/clb-custom-yak-scripts.js
 */



////////////////////////////////////////////////////////////
// *** Add custom wrappers around alignwide, alignfull
////////////////////////////////////////////////////////////
document.addEventListener('DOMContentLoaded', function () {
  const blocks = [
       { selector: '.alignwide', wrapperClass: 'yak-alignwide-wrapper', innerClass: 'yak-alignwide-inner' },
       { selector: '.alignfull', wrapperClass: 'yak-alignfull-wrapper', innerClass: 'yak-alignfull-inner' }
  ];

  blocks.forEach(({ selector, wrapperClass, innerClass }) => {
       document.querySelectorAll('.entry-content ' + selector).forEach(function (el) {
            // Skip if already wrapped
            if (el.closest(`.${wrapperClass}`)) return;

            // Create wrapper and inner
            const wrapper = document.createElement('div');
            wrapper.className = wrapperClass;

            const inner = document.createElement('div');
            inner.className = innerClass;

            // Move the align block into inner, then inner into wrapper
            el.parentNode.insertBefore(wrapper, el);
            inner.appendChild(el);
            wrapper.appendChild(inner);
       });
  });
});
////////////////////////////////////////////////////////////
// END Add custom wrappers around alignwide, alignfull
////////////////////////////////////////////////////////////



document.addEventListener('DOMContentLoaded', () => {
     document.querySelectorAll('.wp-block-cover[class*="has-background-dim-"]').forEach(cover => {
         const match = cover.className.match(/has-background-dim-(\d+)/);
         if (match) {
             const opacity = parseInt(match[1], 10) / 100;
             const bgSpan = cover.querySelector('span.has-background');
             if (bgSpan) {
                 bgSpan.style.opacity = opacity.toString();
             }
         }
     });
 });
 


(function () {

	const REQUIRED_MARGIN = 20;    // Minimum visible padding
	const MIN_VISIBLE_WIDTH = 320;
	const DEFAULT_PULL = 150;

	function evaluateYakPulls() {
		const pulledItems = document.querySelectorAll('[data-yak-pull]');

		pulledItems.forEach(el => {
			const isLeft = el.classList.contains('yak-pull-left');
			const isRight = el.classList.contains('yak-pull-right');

			// If no pull class is present, remove the attribute and skip
			if (!isLeft && !isRight) {
				el.removeAttribute('data-yak-pull');
				return;
			}

			const raw = el.getAttribute('data-yak-pull');
			const pullAmount = parseInt(raw || DEFAULT_PULL, 10);

			el.classList.remove('yak-pull-active');
			el.classList.add('yak-pull-measuring');

			// Reset inline margins for clean measurement
			el.style.marginInlineStart = '';
			el.style.marginInlineEnd = '';

			const rect = el.getBoundingClientRect();
			const width = el.offsetWidth;

			let safe = false;

			if (isLeft) {
				const leftAfterPull = rect.left - pullAmount;
				safe = leftAfterPull >= REQUIRED_MARGIN && width >= MIN_VISIBLE_WIDTH;
			}

			if (isRight) {
				const rightAfterPull = rect.right + pullAmount;
				safe = rightAfterPull <= window.innerWidth - REQUIRED_MARGIN && width >= MIN_VISIBLE_WIDTH;
			}

			el.classList.remove('yak-pull-measuring');

			if (safe) {
				el.classList.add('yak-pull-active');

				if (isLeft) {
					el.style.marginInlineStart = `-${pullAmount}px`;
					el.style.marginInlineEnd = '2em';
				}

				if (isRight) {
					el.style.marginInlineEnd = `-${pullAmount}px`;
					el.style.marginInlineStart = '2em';
				}
			}
		});
	}

	window.addEventListener('resize', evaluateYakPulls);
	window.addEventListener('DOMContentLoaded', evaluateYakPulls);
})();





document.addEventListener('DOMContentLoaded', () => {
	document.querySelectorAll('.nav-primary .menu-item-has-children').forEach(item => {
		const secondLevel = item.querySelector(':scope > .sub-menu');
		if (secondLevel && secondLevel.querySelector('.menu-item-has-children')) {
			item.classList.add('yak-has-mega');
		}
	});
});



document.addEventListener('DOMContentLoaded', () => {

	// Step 1: Get top-level .yak-has-mega <li> items
	const megaParents = document.querySelectorAll('.yak-main-nav > li.yak-has-mega');

	megaParents.forEach((parent, index) => {

		// Step 2: Get its direct child submenu
		const submenu = parent.querySelector(':scope > ul.sub-menu');
		if (!submenu) {
			return;
		}

		// Step 3: Get direct <li> items inside that submenu
		const columnItems = submenu.querySelectorAll(':scope > li');

		columnItems.forEach((item, liIndex) => {
			item.classList.add('yak-mega-column');
		});
	});
});


// add yak-scrolled body class
(function () {
	const threshold = 200;
	const buffer = 20;

	let lastScroll = -1;
	let lastClassState = null;
	let rafId;

	function updateScrollState() {
		const scrollY = Math.round(window.scrollY || window.pageYOffset);
		document.body.setAttribute('data-scroll', scrollY);

		const isOverThreshold = scrollY > threshold + buffer;
		const isUnderThreshold = scrollY < threshold - buffer;
		const shouldAddClass = isOverThreshold || (!isUnderThreshold && lastClassState);

		if (shouldAddClass !== lastClassState) {
			document.body.classList.toggle('yak-scrolled', shouldAddClass);
			lastClassState = shouldAddClass;
		}

		lastScroll = scrollY;
	}

	window.addEventListener('scroll', function () {
		if (rafId) cancelAnimationFrame(rafId);
		rafId = requestAnimationFrame(updateScrollState);
	});

	// Run on load
	updateScrollState();
})();



// find title area width
function setTitleAreaWidthVariable() {
	const titleArea = document.querySelector('.title-area');
	if (!titleArea) return;

	const width = Math.round(titleArea.getBoundingClientRect().width);
	document.documentElement.style.setProperty('--yak-title-area-width', `${width}`);
}

// Debounce helper
function debounce(fn, delay) {
	let timeout;
	return () => {
		clearTimeout(timeout);
		timeout = setTimeout(fn, delay);
	};
}

// Run on DOM ready
window.addEventListener('DOMContentLoaded', setTitleAreaWidthVariable);

// Run on resize (debounced)
window.addEventListener('resize', debounce(setTitleAreaWidthVariable, 100));

// Move all modal wrappers to website footer for best compatibility & function
document.addEventListener('DOMContentLoaded', function () {
	const modalWrappers = document.querySelectorAll('.clb-move-modals');

	modalWrappers.forEach(function(wrapper) {
		document.body.appendChild(wrapper);
	});
});

// after page is fully loaded and rendered, add correct "target=" to all links inside entry content
window.addEventListener('load', () => {
	// Get current site origin (e.g., https://example.com)
	const siteOrigin = window.location.origin;

	document.querySelectorAll('.entry-content a[href]').forEach(link => {
		const href = link.getAttribute('href');
		if (!href) return;

		const isPDF = href.toLowerCase().endsWith('.pdf');

		// Create absolute URL for comparison
		const url = new URL(href, siteOrigin);
		const isSameOrigin = url.origin === siteOrigin;

		if (isPDF || !isSameOrigin) {
			link.setAttribute('target', '_blank');
			link.setAttribute('rel', 'noopener noreferrer');
		} else {
			link.setAttribute('target', '_self');
		}
	});
});




// Create CSS variable for the page content width: `--yak-content-width`
(function() {
	function updateYakContentWidth() {
		const el = document.querySelector('.site-inner .content');
		if (!el) return;

		const width = el.getBoundingClientRect().width;
		document.documentElement.style.setProperty('--yak-content-width', `${Math.round(width)}px`);
	}

	window.addEventListener('load', () => {
		updateYakContentWidth();

		const debouncedUpdate = debounce(updateYakContentWidth, 150);
		window.addEventListener('resize', debouncedUpdate);
	});
})();







// DEV MODE WORK
(function () {
	if (!document.body.classList.contains('yak-dev-mode-95ac0a')) return;

	document.querySelectorAll('.yak-genesis-hook').forEach(el => {
		const hook = el.dataset.hook;
		if (!hook) return;

		const label = document.createElement('div');
		label.textContent = hook;
		label.style.position = 'absolute';
		label.style.top = '0';
		label.style.left = '0';
		label.style.background = '#f90';
		label.style.color = '#000';
		label.style.fontSize = '10px';
		label.style.padding = '2px 4px';
		label.style.zIndex = '99999';
		label.style.pointerEvents = 'none';
		label.style.fontFamily = 'monospace';

		el.style.position = 'relative';
		el.style.minHeight = '1em';
		el.style.outline = '2px dashed orange';

		el.appendChild(label);
	});
})();



(function () {
	if (!document.body.classList.contains('yak-dev-mode-95ac0a')) return;

	// Create tooltip
	const tooltip = document.createElement('div');
	tooltip.id = 'yak-font-tooltip';
	tooltip.style.position = 'fixed';
	tooltip.style.zIndex = '99999';
	tooltip.style.pointerEvents = 'none';
	tooltip.style.background = 'rgba(0,0,0,0.85)';
	tooltip.style.color = '#fff';
	tooltip.style.padding = '6px 8px';
	tooltip.style.borderRadius = '4px';
	tooltip.style.fontSize = '12px';
	tooltip.style.fontFamily = 'monospace';
	tooltip.style.lineHeight = '1.4';
	tooltip.style.maxWidth = '400px';
	tooltip.style.whiteSpace = 'pre-wrap';
	tooltip.style.display = 'none';
	document.body.appendChild(tooltip);

	let currentTarget = null;

	// Convert rgb to hex
	function rgbToHex(rgb) {
		const result = rgb.match(/\d+/g);
		if (!result) return rgb;
		return (
			'#' +
			result
				.slice(0, 3)
				.map(x => parseInt(x).toString(16).padStart(2, '0'))
				.join('')
		);
	}

	// Try to find which variable maps to the given value
	function resolveCSSVar(el, property, value) {
		let match = null;

		// Traverse up and collect all CSS variables
		while (el && el !== document.documentElement) {
			const styles = getComputedStyle(el);
			for (let i = 0; i < styles.length; i++) {
				const name = styles[i];
				if (!name.startsWith('--yak-')) continue;

				const resolved = styles.getPropertyValue(name).trim();
				if (resolved === value) {
					match = `var(${name})`;
					return match;
				}
			}
			el = el.parentElement;
		}

		return null;
	}

	document.addEventListener('mouseover', function (e) {
		const target = e.target;
		if (target === tooltip || tooltip.contains(target)) return;
		if (!target.innerText?.trim()) return;

		currentTarget = target;
		const styles = getComputedStyle(target);

		const fontSize = styles.fontSize;
		const fontColor = styles.color;

		const fontSizeVar = resolveCSSVar(target, 'font-size', fontSize);
		const colorVar = resolveCSSVar(target, 'color', fontColor);

		const content = [
			`font-size: ${fontSize}${fontSizeVar ? `  ← ${fontSizeVar}` : ''}`,
			`font-family: ${styles.fontFamily}`,
			`font-weight: ${styles.fontWeight}`,
			`line-height: ${styles.lineHeight}`,
			`letter-spacing: ${styles.letterSpacing}`,
			`text-transform: ${styles.textTransform}`,
			`color: ${fontColor}${colorVar ? `  ← ${colorVar}` : ''}`,
			`        ${rgbToHex(fontColor)}`
		].join('\n');

		tooltip.textContent = content;
		tooltip.style.display = 'block';
	});

	document.addEventListener('mousemove', function (e) {
		if (!currentTarget) return;
		tooltip.style.top = `${e.clientY + 12}px`;
		tooltip.style.left = `${e.clientX + 12}px`;
	});

	document.addEventListener('mouseout', function (e) {
		if (e.target === currentTarget) {
			currentTarget = null;
			tooltip.style.display = 'none';
		}
	});
})();
