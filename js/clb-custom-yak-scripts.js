


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
		const pulledItems = document.querySelectorAll('.yak-pull-left, .yak-pull-right');

		pulledItems.forEach(el => {
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

			if (el.classList.contains('yak-pull-left')) {
				const leftAfterPull = rect.left - pullAmount;
				safe = leftAfterPull >= REQUIRED_MARGIN && width >= MIN_VISIBLE_WIDTH;
			}

			if (el.classList.contains('yak-pull-right')) {
				const rightAfterPull = rect.right + pullAmount;
				safe = rightAfterPull <= window.innerWidth - REQUIRED_MARGIN && width >= MIN_VISIBLE_WIDTH;
			}

			el.classList.remove('yak-pull-measuring');

			if (safe) {
				el.classList.add('yak-pull-active');

				if (el.classList.contains('yak-pull-left')) {
					el.style.marginInlineStart = `-${pullAmount}px`;
					el.style.marginInlineEnd = '2em';
				}

				if (el.classList.contains('yak-pull-right')) {
					el.style.marginInlineEnd = `-${pullAmount}px`;
					el.style.marginInlineStart = '2em';
				}
			}
		});
	}

	window.addEventListener('resize', evaluateYakPulls);
	window.addEventListener('DOMContentLoaded', evaluateYakPulls);
})();





(function () {

	document.body.classList.add('yak-disable-header-transition');
	
	const body = document.body;
	let lastScrollY = window.scrollY;
	let placeholder = null;
	let header = null;

	function getAdminBarHeight() {
		const bar = document.getElementById('wpadminbar');
		return bar ? bar.offsetHeight : 0;
	}

	function getHeaderHeight() {
		return header ? header.offsetHeight : 0;
	}

	function updateHeaderAndPlaceholderHeights() {
		if (!header || !placeholder) return;
	
		const adminBarHeight = getAdminBarHeight();
		const headerHeight = getHeaderHeight();
		const totalHeight = headerHeight + adminBarHeight;
	
		// Save offset
		header.dataset.offset = totalHeight;
	
		// Set placeholder height to match
		placeholder.style.height = `${totalHeight}px`;
	
		// Set visible default position: below admin bar
		header.style.transform = `translateY(${adminBarHeight}px)`;
	}

	function createOrUpdatePlaceholder() {
		if (!placeholder) {
			placeholder = document.createElement('div');
			placeholder.className = 'yak-mobile-header-placeholder';
			header.parentNode.insertBefore(placeholder, header);
		}
		updateHeaderAndPlaceholderHeights();
	}

	function onResize() {
		if (!header) return;

		if (window.innerWidth < 960) {
			createOrUpdatePlaceholder();
		} else if (placeholder) {
			placeholder.remove();
			placeholder = null;
			header.style.transform = ''; // reset
		}
	}

	function onScroll() {
		if (window.innerWidth >= 960) return;
	
		const currentY = window.scrollY;
		const scrollingUp = currentY < lastScrollY;
		const offset = parseInt(header.dataset.offset || 0, 10);
		const adminOffset = getAdminBarHeight();
		const THRESHOLD = 150;
	
		// 1. At the very top of the page
		if (currentY <= 0) {
			body.classList.add('yak-at-top');
			body.classList.remove('yak-header-hidden', 'yak-show-mobile-header');
			header.style.transform = `translateY(${adminOffset}px)`;
		}
	
		// 2. User has scrolled less than threshold — do nothing
		else if (currentY > 0 && currentY <= THRESHOLD) {
			// Don't animate yet, just hold position
			body.classList.remove('yak-header-hidden', 'yak-show-mobile-header', 'yak-at-top');
			header.style.transform = `translateY(0)`; // Stuck at top (visible)
		}
	
		// 3. User scrolls up beyond threshold — show header at top
		else if (scrollingUp && currentY > THRESHOLD) {
			body.classList.add('yak-show-mobile-header');
			body.classList.remove('yak-header-hidden', 'yak-at-top');
			header.style.transform = `translateY(0)`;
		}
	
		// 4. User scrolls down beyond threshold — hide header
		else {
			body.classList.add('yak-header-hidden');
			body.classList.remove('yak-show-mobile-header', 'yak-at-top');
			header.style.transform = `translateY(-${offset}px)`;
		}
	
		lastScrollY = currentY;
	}
	

	function init() {
		header = document.querySelector('.site-header');
		if (!header) return;

		if (window.innerWidth < 960) {
			createOrUpdatePlaceholder();
			onScroll(); // run once immediately
		}

		// ✅ Remove transition-blocking class
		requestAnimationFrame(() => {
			body.classList.remove('yak-disable-header-transition');
		});

		window.addEventListener('resize', () => {
			onResize();
			setTimeout(updateHeaderAndPlaceholderHeights, 50); // after layout settles
		});

		window.addEventListener('scroll', onScroll, { passive: true });
	}

	// Run after render settles
	window.addEventListener('load', () => {
		setTimeout(init, 10); // wait just long enough for admin bar and header to fully render
	});
})();



document.addEventListener('DOMContentLoaded', () => {
	const megaMenus = document.querySelectorAll('.nav-primary .menu-item-has-children > .sub-menu');

	megaMenus.forEach(submenu => {
		// Wrap each 2nd-level menu item in a div.yak-mega-column
		submenu.querySelectorAll(':scope > .menu-item').forEach(item => {
			const wrapper = document.createElement('div');
			wrapper.className = 'yak-mega-column';
			item.parentNode.insertBefore(wrapper, item);
			wrapper.appendChild(item);
		});
	});
});

document.addEventListener('DOMContentLoaded', () => {
	document.querySelectorAll('.nav-primary .menu-item-has-children').forEach(item => {
		const secondLevel = item.querySelector(':scope > .sub-menu');
		if (secondLevel && secondLevel.querySelector('.menu-item-has-children')) {
			item.classList.add('yak-has-mega');
		}
	});
});
