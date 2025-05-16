document.addEventListener('DOMContentLoaded', () => {

	// ===== Enhance .yak-search-trigger menu item =====
	document.querySelectorAll('.yak-search-trigger a[href="#yak-search-modal"]').forEach(el => {
		el.setAttribute('data-bs-toggle', 'modal');
		el.setAttribute('data-bs-target', '#yak-search-modal');
	});


	// ===== MODAL OPEN =====
	document.querySelectorAll('[data-bs-toggle="modal"]').forEach(trigger => {
		trigger.addEventListener('click', e => {
			e.preventDefault();
			const targetSelector = trigger.getAttribute('data-bs-target');
			const modal = document.querySelector(targetSelector);
			if (!modal) return;

			modal.style.display = 'flex';

			requestAnimationFrame(() => {
				modal.removeAttribute('aria-hidden');
				modal.removeAttribute('inert');
				modal.classList.add('show');
				document.body.classList.add('yak-modal-open');

				// Optional: autofocus input inside modal
				setTimeout(() => {
					let focusTarget = modal.querySelector('[autofocus], input[type="search"], input, button, [tabindex]:not([tabindex="-1"])');
					if (modal.id === 'yak-search-modal') {
						focusTarget = modal.querySelector('input[type="search"]') || focusTarget;
					}
					if (focusTarget) focusTarget.focus();
				}, 16);
			});
		});
	});


	// ===== MODAL CLOSE (via button) =====
	document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(closeBtn => {
		closeBtn.addEventListener('click', () => {
			const modal = closeBtn.closest('.yak-modal');
			if (!modal) return;

			closeYakModal(modal);
		});
	});


	// ===== MODAL CLOSE (via backdrop click) =====
	document.querySelectorAll('.yak-modal').forEach(modal => {
		modal.addEventListener('click', e => {
			if (e.target === modal) {
				closeYakModal(modal);
			}
		});
	});


	// ===== MODAL CLOSE (via Escape key) =====
	document.addEventListener('keydown', e => {
		if (e.key === 'Escape') {
			document.querySelectorAll('.yak-modal.show').forEach(modal => {
				closeYakModal(modal);
			});
		}
	});


	// ===== COLLAPSE TOGGLE =====
	document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(trigger => {
		trigger.addEventListener('click', e => {
			e.preventDefault();
			const target = document.querySelector(trigger.dataset.bsTarget);
			if (!target) return;

			const isOpen = target.classList.contains('show');
			target.classList.add('yak-collapse--animating');

			if (isOpen) {
				const height = target.scrollHeight;
				target.style.height = height + 'px';

				requestAnimationFrame(() => {
					target.style.height = '0';
				});

				target.addEventListener('transitionend', function handler() {
					target.classList.remove('show', 'yak-collapse--animating');
					target.style.height = '';
					target.removeEventListener('transitionend', handler);
				});
			} else {
				target.classList.add('show');
				const height = target.scrollHeight;
				target.style.height = '0';

				requestAnimationFrame(() => {
					target.style.height = height + 'px';
				});

				target.addEventListener('transitionend', function handler() {
					target.classList.remove('yak-collapse--animating');
					target.style.height = '';
					target.removeEventListener('transitionend', handler);
				});
			}
		});
	});

	// ===== Modal Close Helper =====
	function closeYakModal(modal) {
		modal.classList.remove('show');
		document.body.classList.remove('yak-modal-open');
		document.body.classList.add('yak-modal-closing');

		// Fade out modal content (300ms), then delay backdrop hide (400ms)
		const CONTENT_FADE_DURATION = 600;
		const BACKDROP_FADE_DURATION = 200;

		// Optionally hide modal content early if needed
		setTimeout(() => {
			modal.setAttribute('aria-hidden', 'true');
			modal.setAttribute('inert', '');
		}, CONTENT_FADE_DURATION);

		// Then hide the whole modal including backdrop
		setTimeout(() => {
			modal.style.display = 'none';
			document.body.classList.remove('yak-modal-closing');
		}, BACKDROP_FADE_DURATION);
	}

	
});
