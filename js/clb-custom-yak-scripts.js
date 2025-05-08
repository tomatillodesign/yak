


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



