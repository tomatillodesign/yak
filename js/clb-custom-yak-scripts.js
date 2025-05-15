


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

