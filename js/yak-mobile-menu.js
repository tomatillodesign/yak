/**
 * ============================================================================
 * 📱 Yak Mobile Menu & Header Behavior
 * ============================================================================
 *
 * This script powers the Yak mobile navigation experience. It enhances accessibility,
 * ensures layout flexibility, and adds responsive behavior for small-screen devices.
 *
 * Features:
 *
 * 1. ✅ Custom Mobile Menu Setup
 *    - Dynamically injects a mobile nav panel based on the desktop `.yak-main-nav`
 *    - Adds a toggle button with animated icon and a closable overlay panel
 *    - Clones all submenus and adds accessible toggles with proper ARIA attributes
 *
 * 2. ✅ Mobile Menu Behavior
 *    - Handles opening, closing, ESC key handling, and overlay click dismissal
 *    - Traps keyboard focus within the open mobile menu
 *    - Tracks and restores focus when menu closes
 *
 * 3. ✅ Header Visibility on Scroll
 *    - Hides the header while scrolling down
 *    - Reveals the header when scrolling up or at the top of the page
 *    - Controlled via `<body>` classes: `yak-show-mobile-header`, `yak-at-top`
 *
 * 4. ✅ CSS Variable for Header Height
 *    - Calculates the mobile header height and sets two variables:
 *      `--yak-mobile-header-height`, `--yak-mobile-header-height-neg`
 *    - Used in CSS for precise layout offsetting
 *
 * 5. ✅ Inline Mobile Search Support
 *    - Adds expandable inline search to the mobile nav if a template exists
 *    - Preserves focus and toggles visibility of the search form and trigger
 *
 * Notes:
 * - This script runs on `DOMContentLoaded`
 * - Styles and transitions should be handled in yak-layout.css and yak-components.css
 * - Future extensions (e.g., accordion nav, mobile account panel) should build on this
 *
 * Location: /js/yak-mobile-menu.js
 */




////////////////////////////////////////////////////////////
// *** Mobile Menu
////////////////////////////////////////////////////////////

(function () {
	let lastFocusedElement = null;

	function yakMenuSetup() {
		const siteHeader = document.querySelector('.site-header .wrap');
		const desktopMenu = document.querySelector('.yak-main-nav');
		const existing = document.getElementById('yak-mobile-nav');
		if (!siteHeader || !desktopMenu || existing) return;

		// === Toggle Button ===
		const toggle = document.createElement('button');
		toggle.className = 'yak-menu-toggle';
		toggle.setAttribute('aria-expanded', 'false');
		toggle.setAttribute('aria-controls', 'yak-mobile-nav');
		toggle.setAttribute('aria-label', 'Toggle menu');

		const icon = document.createElement('span');
		icon.className = 'yak-menu-icon';
		icon.setAttribute('aria-hidden', 'true');
		icon.innerHTML = `
			<span class="yak-menu-bar"></span>
			<span class="yak-menu-bar"></span>
			<span class="yak-menu-bar"></span>`;
		toggle.appendChild(icon);
		siteHeader.insertBefore(toggle, siteHeader.querySelector('#genesis-nav-primary') || null);

		// === Mobile Nav Panel ===
		const mobileNav = document.createElement('div');
		mobileNav.id = 'yak-mobile-nav';
		mobileNav.className = 'yak-mobile-nav';
		mobileNav.setAttribute('role', 'dialog');
		mobileNav.setAttribute('aria-modal', 'true');

		const overlay = document.createElement('div');
		overlay.className = 'yak-mobile-overlay';

		const panel = document.createElement('nav');
		panel.className = 'yak-mobile-panel';
		panel.setAttribute('aria-label', 'Mobile Navigation');

		// === Mobile Menu Header ===
		const header = document.createElement('div');
		header.className = 'yak-mobile-header';

		const faviconLink = document.querySelector('link[rel="icon"]');
		const faviconUrl = faviconLink ? faviconLink.getAttribute('href') : null;

		const title = document.createElement('span');
		title.className = 'yak-mobile-title';
		if (faviconUrl) {
			const faviconImg = document.createElement('img');
			faviconImg.src = faviconUrl;
			faviconImg.alt = 'Site icon';
			faviconImg.className = 'yak-mobile-favicon';
			title.appendChild(faviconImg);
		}
		title.appendChild(document.createTextNode('Menu'));

		const closeBtn = document.createElement('button');
		closeBtn.className = 'yak-mobile-close';
		closeBtn.setAttribute('aria-label', 'Close Menu');
		closeBtn.innerHTML = '&times;';

		header.appendChild(title);
		header.appendChild(closeBtn);

		// === Clone Desktop Menu ===
		const clonedMenu = desktopMenu.cloneNode(true);
		clonedMenu.classList.add('yak-mobile-menu');

		// === Add submenu toggles with unique IDs and labels ===
		let submenuCount = 0;
		clonedMenu.querySelectorAll('li.menu-item-has-children').forEach((li) => {
			const submenu = li.querySelector('ul');
			if (!submenu) return;

			submenuCount++;
			const submenuId = `yak-submenu-${submenuCount}`;
			submenu.id = submenuId;

			const link = li.querySelector('a');
			const labelText = link?.textContent?.trim() || 'submenu';

		const btn = document.createElement('button');
		btn.className = 'yak-submenu-toggle';
		btn.setAttribute('aria-expanded', 'false');
		btn.setAttribute('aria-controls', submenuId);
		btn.setAttribute('aria-label', `Expand submenu for ${labelText}`);
		btn.innerHTML = '<svg class="yak-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" fill="none"/></svg>';

			li.insertBefore(btn, submenu);
		});

		panel.appendChild(header);
		panel.appendChild(clonedMenu);
		overlay.appendChild(panel);
		mobileNav.appendChild(overlay);
		document.body.appendChild(mobileNav);

		return { toggle, mobileNav, panel, closeBtn };
	}

	function yakMenuBehavior({ toggle, mobileNav, panel, closeBtn }) {
		if (!toggle || !mobileNav || !panel) return;

		const focusableSelectors = 'a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])';

		function trapFocus(e) {
			const focusableEls = panel.querySelectorAll(focusableSelectors);
			const first = focusableEls[0];
			const last = focusableEls[focusableEls.length - 1];

			if (e.key === 'Tab') {
				if (e.shiftKey && document.activeElement === first) {
					e.preventDefault();
					last.focus();
				} else if (!e.shiftKey && document.activeElement === last) {
					e.preventDefault();
					first.focus();
				}
			}
		}

		function openMenu() {
			lastFocusedElement = document.activeElement;
			mobileNav.classList.remove('is-closing');
			toggle.setAttribute('aria-expanded', 'true');
			requestAnimationFrame(() => {
				mobileNav.classList.add('is-open');
				setTimeout(() => {
					const firstFocusable = panel.querySelector(focusableSelectors);
					if (firstFocusable) firstFocusable.focus();
				}, 50);
			});
			document.addEventListener('keydown', trapFocus);
		}

		function closeMenu() {
			toggle.setAttribute('aria-expanded', 'false');
			mobileNav.classList.remove('is-open');
			mobileNav.classList.add('is-closing');
			setTimeout(() => {
				mobileNav.classList.remove('is-closing');
				resetSubmenus();
			}, 500);
			document.removeEventListener('keydown', trapFocus);
			if (lastFocusedElement) lastFocusedElement.focus();
		}

		function resetSubmenus() {
			mobileNav.querySelectorAll('.yak-submenu-toggle').forEach(btn => {
				btn.setAttribute('aria-expanded', 'false');
			});
			mobileNav.querySelectorAll('.sub-menu.is-open').forEach(ul => {
				ul.classList.remove('is-open');
			});
		}

		toggle.addEventListener('click', () => {
			const expanded = toggle.getAttribute('aria-expanded') === 'true';
			expanded ? closeMenu() : openMenu();
		});

		closeBtn.addEventListener('click', closeMenu);

		mobileNav.querySelector('.yak-mobile-overlay').addEventListener('click', (e) => {
			if (!panel.contains(e.target)) {
				closeMenu();
			}
		});

		document.addEventListener('keydown', (e) => {
			if (e.key === 'Escape' && mobileNav.classList.contains('is-open')) {
				closeMenu();
			}
		});

		mobileNav.querySelectorAll('.yak-submenu-toggle').forEach(btn => {
			btn.addEventListener('click', (e) => {
				e.preventDefault();
				const expanded = btn.getAttribute('aria-expanded') === 'true';
				btn.setAttribute('aria-expanded', String(!expanded));
				const submenu = btn.nextElementSibling;
				if (submenu) submenu.classList.toggle('is-open', !expanded);
			});
		});
	}

	document.addEventListener('DOMContentLoaded', () => {
		const refs = yakMenuSetup();
		if (refs) yakMenuBehavior(refs);
	});
})();

////////////////////////////////////////////////////////////
// END Mobile Menu init setup
////////////////////////////////////////////////////////////




(function () {
	const body = document.body;
	let lastScrollY = window.scrollY;
	const TRIGGER_DISTANCE = 400;

	function onScroll() {
		const currentY = window.scrollY;
		const scrollingUp = currentY < lastScrollY;
	
		if (window.innerWidth >= 960) {
			body.classList.remove('yak-show-mobile-header', 'yak-at-top');
			lastScrollY = currentY;
			return;
		}
	
		if (currentY <= 0) {
			// At top of page: let header fall into normal layout
			body.classList.remove('yak-show-mobile-header');
			body.classList.add('yak-at-top');
		} else if (scrollingUp) {
			// Scrolling up: show header as overlay
			body.classList.add('yak-show-mobile-header');
			body.classList.remove('yak-at-top');
		} else {
			// Scrolling down: hide header
			body.classList.remove('yak-show-mobile-header', 'yak-at-top');
		}
	
		lastScrollY = currentY;
	}
	

	window.addEventListener('scroll', onScroll, { passive: true });
})();




function setYakMobileHeaderHeightVar() {
	const header = document.querySelector('.site-header');
	if (!header) return;

	const height = Math.round(header.getBoundingClientRect().height);
	document.documentElement.style.setProperty('--yak-mobile-header-height', `${height}px`);
	document.documentElement.style.setProperty('--yak-mobile-header-height-neg', `-${height}px`);
}

function debounce(fn, delay = 100) {
	let timeout;
	return () => {
		clearTimeout(timeout);
		timeout = setTimeout(fn, delay);
	};
}

window.addEventListener('DOMContentLoaded', setYakMobileHeaderHeightVar);
window.addEventListener('resize', debounce(setYakMobileHeaderHeightVar, 150));



// inline search
document.addEventListener('DOMContentLoaded', () => {
	const trigger = document.querySelector('#yak-mobile-nav .yak-search-trigger > a');
	if (!trigger) return;

	trigger.addEventListener('click', (e) => {
		e.preventDefault();

		const li = trigger.closest('li');
		if (!li) return;

		// Check if form already exists
		let form = li.querySelector('.yak-inline-search-form');
		if (form) {
			form.classList.toggle('is-visible');

			// Show/hide the anchor link based on form visibility
			trigger.style.display = form.classList.contains('is-visible') ? 'none' : '';

			form.querySelector('input[type="search"]')?.focus();
			return;
		}

		// Grab WP template
		const template = document.getElementById('yak-inline-search-template');
		if (!template) return;

		const clonedForm = template.querySelector('form')?.cloneNode(true);
		if (!clonedForm) return;

		clonedForm.classList.add('yak-inline-search-form', 'is-visible');
		li.appendChild(clonedForm);
		trigger.style.display = 'none';
		clonedForm.querySelector('input[type="search"]')?.focus();
	});
});
