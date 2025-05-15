/**
 * ============================================================================
 * 🎛 yakstrap.js — Lightweight Modal + Collapse Behavior (No Bootstrap Required)
 * ============================================================================
 *
 * This script provides Bootstrap-like interactivity for modals and collapsible
 * sections using native JavaScript. It powers Yak’s custom UI components without
 * requiring Bootstrap’s full JS bundle or jQuery dependency.
 *
 * Features:
 *
 * 1. ✅ Modal Behavior
 *    - Activates modals via `[data-bs-toggle="modal"]`
 *    - Supports ARIA attributes, focus management, and ESC key to close
 *    - Dismisses modals via `[data-bs-dismiss="modal"]` or clicking outside
 *    - Adds `yak-modal-open` body class to manage scroll lock or overlay styling
 *
 * 2. ✅ Collapse Toggle
 *    - Adds smooth animated expand/collapse via `[data-bs-toggle="collapse"]`
 *    - Handles height transitions manually for fluid open/close
 *    - Applies `.show` and `.yak-collapse--animating` classes for control
 *
 * 3. ✅ Search Enhancements
 *    - Enhances `.yak-search-trigger` links to auto-wire modals for search UX
 *
 * Notes:
 * - This script mimics Bootstrap’s data-attribute behavior with a much smaller footprint
 * - Integrates directly with Yak’s custom markup and CSS animation layers
 * - Ensure that `.yak-modal`, `.yak-collapse`, and related styles are defined in CSS
 *
 * Location: /js/yakstrap.js
 */



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
			const modal = document.querySelector(trigger.dataset.bsTarget);
			if (!modal) return;

			// Step 1: Show the modal immediately
			modal.style.display = 'flex';

			// Step 2: Wait a frame, then remove aria-hidden + inert + fade in
			requestAnimationFrame(() => {
				modal.removeAttribute('aria-hidden');
				modal.removeAttribute('inert');
				document.body.classList.add('yak-modal-open');
				modal.classList.add('show');

				// Step 3: Delay focus to next frame after visibility update
				setTimeout(() => {
					const focusable = modal.querySelector('input, button, [tabindex]:not([tabindex="-1"])');
					if (focusable) focusable.focus();
				}, 16); // ~1 frame @ 60fps
			});
		});
	});

	// ===== MODAL CLOSE (button) =====
	document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(closeBtn => {
		closeBtn.addEventListener('click', () => {
			const modal = closeBtn.closest('.yak-modal');
			if (!modal) return;

			modal.classList.remove('show');
			document.body.classList.remove('yak-modal-open');

			// Wait for transition to finish (optional)
			setTimeout(() => {
				modal.setAttribute('aria-hidden', 'true');
				modal.setAttribute('inert', '');
				modal.style.display = '';
			}, 300);
		});
	});

	// ===== MODAL CLOSE (click outside) =====
	document.querySelectorAll('.yak-modal').forEach(modal => {
		modal.addEventListener('click', e => {
			if (e.target === modal) {
				modal.classList.remove('show');
				document.body.classList.remove('yak-modal-open');
				setTimeout(() => {
					modal.setAttribute('aria-hidden', 'true');
					modal.setAttribute('inert', '');
					modal.style.display = '';
				}, 300);
			}
		});
	});

	// ===== MODAL ESC CLOSE =====
	document.addEventListener('keydown', e => {
		if (e.key === 'Escape') {
			document.querySelectorAll('.yak-modal.show').forEach(modal => {
				modal.classList.remove('show');
				document.body.classList.remove('yak-modal-open');
				setTimeout(() => {
					modal.setAttribute('aria-hidden', 'true');
					modal.setAttribute('inert', '');
					modal.style.display = '';
				}, 300);
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
				// Closing
				const height = target.scrollHeight;
				target.style.height = height + 'px';

				requestAnimationFrame(() => {
					target.style.height = '0';
				});

				target.addEventListener('transitionend', function handler() {
					target.classList.remove('show');
					target.classList.remove('yak-collapse--animating');
					target.style.height = '';
					target.removeEventListener('transitionend', handler);
				});
			} else {
				// Opening
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
});
