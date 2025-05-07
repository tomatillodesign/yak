
(function($) {
acf.addAction('load', function () {
	const selectedSlugs = new Set();

	// Read all existing slugs from the repeater field
	acf.getField('field_yak_selected_editor_colors')?.$rows().each(function () {
		const $row = $(this);
		const slug = $row.find('[data-key="field_yak_selected_slug"] input').val();
		if (slug) selectedSlugs.add(slug);
	});

	// Add `.selected` to matching swatches
	$('.yak-color-swatch').each(function () {
		const $swatch = $(this);
		const slug = $swatch.data('slug');
		if (selectedSlugs.has(slug)) {
			$swatch.addClass('selected');
		}
	});
});
})(jQuery);


(function($) {
	acf.addAction('load', function() {

        console.log("Yak editor sawtch; js");

		// Map existing slugs in repeater
		let selectedSlugs = new Set();

		$('.acf-field[data-key="field_yak_selected_editor_colors"] .acf-row').each(function() {
			const slug = $(this).find('input[name*="[slug]"]').val();
			if (slug) selectedSlugs.add(slug);
		});

		// Loop through swatches and mark selected
		$('.yak-color-swatch').each(function() {
			const $swatch = $(this);
			const slug = $swatch.data('slug');
			if (selectedSlugs.has(slug)) {
				$swatch.addClass('selected');
			}
		});

        // handle click
        $('.yak-color-swatch').on('click', function () {
            const $swatch = $(this);
            const slug = $swatch.data('slug');
            const hex  = $swatch.data('hex');
        
            $swatch.toggleClass('selected');
        
            const repeaterField = acf.getField('field_yak_selected_editor_colors');
            if (!repeaterField) return;
        
            if ($swatch.hasClass('selected')) {
                // Check for existing slug first
                let duplicate = false;
        
                repeaterField.$rows().each(function () {
                    const $row = $(this);
                    const val = $row.find('[data-key="field_yak_selected_slug"] input').val();
                    if (val === slug) {
                        duplicate = true;
                        return false; // exit loop early
                    }
                });
        
                if (duplicate) return; // do not add again
        
                // Add row with values
                const rowEl = repeaterField.add();
                const $row = $(rowEl);
        
                setTimeout(() => {
                    $row.find('[data-key="field_yak_selected_slug"] input').val(slug).trigger('input');
                    $row.find('[data-key="field_yak_selected_hex"] input').val(hex).trigger('input');
                }, 100);
        
            } else {
                // Remove matching slug row
                repeaterField.$rows().each(function () {
                    const $row = $(this);
                    const val = $row.find('[data-key="field_yak_selected_slug"] input').val();
                    if (val === slug) {
                        repeaterField.remove($row);
                        return false;
                    }
                });
            }
        });
        
        

	});
})(jQuery);
