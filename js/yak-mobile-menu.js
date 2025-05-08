


////////////////////////////////////////////////////////////
// *** Mobile Menu
////////////////////////////////////////////////////////////


(function () {
	/**
	 * Setup: Injects toggle + mobile menu markup
	 * Returns DOM references for behavior logic
	 */
	function yakMenuSetup() {

        console.log("Menu setup 233");

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

		const overlay = document.createElement('div');
		overlay.className = 'yak-mobile-overlay';

		const panel = document.createElement('nav');
		panel.className = 'yak-mobile-panel';
		panel.setAttribute('aria-label', 'Mobile Navigation');

		const clonedMenu = desktopMenu.cloneNode(true);
		clonedMenu.classList.add('yak-mobile-menu');

		// === Add submenu toggles ===
		clonedMenu.querySelectorAll('li.menu-item-has-children').forEach((li) => {
			const submenu = li.querySelector('ul');
			if (!submenu) return;

			const btn = document.createElement('button');
			btn.className = 'yak-submenu-toggle';
			btn.setAttribute('aria-expanded', 'false');
			btn.setAttribute('aria-label', 'Expand submenu');
			btn.innerHTML = '<span class="yak-submenu-caret" aria-hidden="true">+</span>';

			li.insertBefore(btn, submenu);
		});

		panel.appendChild(clonedMenu);
		overlay.appendChild(panel);
		mobileNav.appendChild(overlay);
		document.body.appendChild(mobileNav);

		return { toggle, mobileNav, panel };
	}

	/**
	 * Behavior: Adds interactivity to mobile nav
	 */
	function yakMenuBehavior({ toggle, mobileNav, panel }) {
		if (!toggle || !mobileNav || !panel) return;

		function openMenu() {
            // Clear any lingering state
	        mobileNav.classList.remove('is-closing');
			toggle.setAttribute('aria-expanded', 'true');
            requestAnimationFrame(() => {
                mobileNav.classList.add('is-open');
            });
		}

		function closeMenu() {
			toggle.setAttribute('aria-expanded', 'false');
            mobileNav.classList.remove('is-open');
            mobileNav.classList.add('is-closing');
            setTimeout(() => {
                mobileNav.classList.remove('is-closing');
                resetSubmenus();
            }, 500); // match the CSS transition time
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

	// === INIT ===
	document.addEventListener('DOMContentLoaded', () => {
		const refs = yakMenuSetup();
		if (refs) yakMenuBehavior(refs);
	});
})();



////////////////////////////////////////////////////////////
// END Mobile Menu
////////////////////////////////////////////////////////////