// Yak Color Swatch Copy Utility  
// Copies the swatch HEX value to clipboard on click  
// Displays a temporary "Copied!" badge for user feedback  


document.addEventListener('DOMContentLoaded', () => {
	document.querySelectorAll('.yak-color-swatch').forEach((btn) => {
		btn.addEventListener('click', () => {
			const hex = btn.dataset.hex;
			if (!hex) return;

			navigator.clipboard.writeText(hex).then(() => {
				let badge = btn.querySelector('.yak-swatch-copied');
				if (!badge) {
					badge = document.createElement('div');
					badge.className = 'yak-swatch-copied';
					badge.textContent = 'Copied!';
					badge.style.position = 'absolute';
					badge.style.top = '8px';
					badge.style.left = '8px';
					badge.style.background = '#0073aa';
					badge.style.color = '#fff';
					badge.style.padding = '2px 6px';
					badge.style.borderRadius = '3px';
					badge.style.fontSize = '11px';
					badge.style.pointerEvents = 'none';
					btn.appendChild(badge);
				}
				badge.style.display = 'block';
				clearTimeout(badge.dataset.timeout);
				badge.dataset.timeout = setTimeout(() => {
					badge.style.display = 'none';
				}, 1200);
			});
		});
	});
});

