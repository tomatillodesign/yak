/**
 * ===============================================================
 * 🧱 Yak Block Enhancements – Custom Gutenberg Block Extensions
 * ===============================================================
 *
 * This file extends native and custom Gutenberg blocks with Yak-specific
 * editing capabilities and visual behavior. Enhancements are applied globally
 * to all relevant blocks using WordPress filter hooks and higher-order components.
 *
 * Included Enhancements:
 *
 * 1. ✅ Mobile/Desktop Visibility Toggles:
 *    - Adds two custom attributes (`yakMobileOnly`, `yakDesktopOnly`) to every block
 *    - Adds toggle controls to the block inspector sidebar
 *    - Applies frontend classes (`yak-mobile-only`, `yak-desktop-only`) during block rendering
 *    - Enables block-specific responsive visibility without relying on layout wrappers
 *
 * 2. ✅ Pull Left/Right Alignment Options:
 *    - Adds `yakPullAlign` and `yakPullAmount` to selected blocks (`image`, `quote`, `group`)
 *    - Injects toolbar buttons for "Pull Left" and "Pull Right" desktop alignment
 *    - Adds Inspector input to adjust pull distance (default: 150px)
 *    - Applies CSS class (`yak-pull-left` / `yak-pull-right`) and data attribute for distance
 *    - Supports custom layout effects via negative margins
 *
 * Notes:
 * - All changes are editor-safe and use WP's official hook system (`wp.hooks`)
 * - Block output remains semantic and minimal, relying on class-based targeting
 * - Styling for these features should live in yak-blocks.css and yak-overrides.css
 *
 * Location: /js/block-enhancements.js
 * Version: 1.0.4
 */




////////////////////////////////////////////////////////////
// *** Mobile/Desktop Only Settings Toggle
////////////////////////////////////////////////////////////
(function () {
	const { addFilter } = wp.hooks;
	const { createHigherOrderComponent } = wp.compose;
	const { InspectorControls } = wp.blockEditor || wp.editor;
	const { PanelBody, ToggleControl } = wp.components;
	const { Fragment, createElement } = wp.element;

	// 1. Add custom attributes to all blocks (core + ACF + custom)
	addFilter(
		'blocks.registerBlockType',
		'yak/custom-visibility-toggles',
		function (settings) {
			if (!settings.attributes) return settings;

			return {
				...settings,
				attributes: {
					...settings.attributes,
					yakMobileOnly: {
						type: 'boolean',
						default: false,
					},
					yakDesktopOnly: {
						type: 'boolean',
						default: false,
					},
				},
			};
		}
	);

	// 2. Add visibility toggles to the block sidebar
	const withVisibilityToggles = createHigherOrderComponent((BlockEdit) => {
		return (props) => {
			const { attributes, setAttributes } = props;

			if (
				typeof attributes.yakMobileOnly === 'undefined' &&
				typeof attributes.yakDesktopOnly === 'undefined'
			) {
				return createElement(BlockEdit, props);
			}

			return createElement(
				Fragment,
				{},
				createElement(BlockEdit, props),
				createElement(
					InspectorControls,
					{},
					createElement(
						PanelBody,
						{
							title: 'Visibility Settings',
							initialOpen: false,
						},
						createElement(ToggleControl, {
							label: 'Mobile Only',
							checked: !!attributes.yakMobileOnly,
							onChange: (value) =>
								setAttributes({
									yakMobileOnly: value,
									yakDesktopOnly: value ? false : attributes.yakDesktopOnly,
								}),
						}),
						createElement(ToggleControl, {
							label: 'Desktop Only',
							checked: !!attributes.yakDesktopOnly,
							onChange: (value) =>
								setAttributes({
									yakDesktopOnly: value,
									yakMobileOnly: value ? false : attributes.yakMobileOnly,
								}),
						})
					)
				)
			);
		};
	}, 'withVisibilityToggles');

	addFilter('editor.BlockEdit', 'yak/with-visibility-toggles', withVisibilityToggles);

	// 3. Add CSS classes to saved block output
	addFilter(
		'blocks.getSaveContent.extraProps',
		'yak/add-visibility-classes',
		function (extraProps, blockType, attributes) {
			if (
				typeof attributes.yakMobileOnly === 'undefined' &&
				typeof attributes.yakDesktopOnly === 'undefined'
			) {
				return extraProps;
			}

			const classes = extraProps.className ? [extraProps.className] : [];

			if (attributes.yakMobileOnly) {
				classes.push('yak-mobile-only');
			} else if (attributes.yakDesktopOnly) {
				classes.push('yak-desktop-only');
			}

			extraProps.className = classes.join(' ').trim();
			return extraProps;
		}
	);
})();

////////////////////////////////////////////////////////////
// END Mobile/Desktop Only Settings Toggle
////////////////////////////////////////////////////////////


////////////////////////////////////////////////////////////
// *** Pull Left/Right Custom Alignment + Settings
////////////////////////////////////////////////////////////
const { addFilter } = wp.hooks;
const { createHigherOrderComponent } = wp.compose;
const { Fragment, createElement } = wp.element;
const { BlockControls, InspectorControls } = wp.blockEditor || wp.editor;
const { ToolbarGroup, ToolbarButton, PanelBody, TextControl } = wp.components;

// Extend attributes
addFilter(
	'blocks.registerBlockType',
	'yak/extend-alignment-attributes',
	function (settings, name) {
		if (!['core/image', 'core/quote', 'core/group'].includes(name)) return settings;

		settings.attributes = {
			...settings.attributes,
			yakPullAlign: {
				type: 'string',
				default: '',
			},
			yakPullAmount: {
				type: 'string',
				default: '150',
			},
		};

		return settings;
	}
);

// Extend block editor UI
const withPullAlignmentEnhancements = createHigherOrderComponent((BlockEdit) => {
	return (props) => {
		const { name, attributes, setAttributes, isSelected } = props;
		if (!['core/image', 'core/quote', 'core/group'].includes(name)) return createElement(BlockEdit, props);

		const { align, yakPullAlign, yakPullAmount } = attributes;

		// Pull alignment clicks clear native align
		const handleYakPullClick = (side) => {
			const isActive = yakPullAlign === side;
			setAttributes({
				yakPullAlign: isActive ? '' : side,
				align: '', // always clear native align
			});
		};

		// Native alignment clears yakPullAlign
		const handleDefaultAlign = (newAlign) => {
			if (yakPullAlign) {
				setAttributes({ yakPullAlign: '' });
			}
			setAttributes({ align: newAlign });
		};

		return createElement(
			Fragment,
			{},
			createElement(BlockEdit, {
				...props,
				setAttributes: (attrs) => {
					if ('align' in attrs) handleDefaultAlign(attrs.align);
					else setAttributes(attrs);
				},
			}),

			// 👇 Inject toolbar into BlockControls without replacing native one
			isSelected &&
				createElement(
					BlockControls,
					{},
					createElement(
						ToolbarGroup,
						{ label: 'Pull Alignment' },
						createElement(ToolbarButton, {
							label: 'Pull Left (Desktop)',
							icon: 'arrow-left-alt2',
							isPressed: yakPullAlign === 'left',
							onClick: () => handleYakPullClick('left'),
						}),
						createElement(ToolbarButton, {
							label: 'Pull Right (Desktop)',
							icon: 'arrow-right-alt2',
							isPressed: yakPullAlign === 'right',
							onClick: () => handleYakPullClick('right'),
						})
					)
				),

			// 👇 Optional Inspector Controls when yakPullAlign is active
			yakPullAlign &&
				isSelected &&
				createElement(
					InspectorControls,
					{},
					createElement(
						PanelBody,
						{ title: 'Pull Alignment Options', initialOpen: true },
						createElement(TextControl, {
							label: 'Pull Distance (px)',
							help: 'Negative margin applied when space allows. Leave blank for default (150).',
							type: 'number',
							value: yakPullAmount || '',
							onChange: (val) => setAttributes({ yakPullAmount: val }),
						})
					)
				)
		);
	};
}, 'withPullAlignmentEnhancements');

addFilter('editor.BlockEdit', 'yak/pull-align-ui', withPullAlignmentEnhancements);

addFilter(
	'blocks.getSaveContent.extraProps',
	'yak/apply-pull-alignment-class-and-data',
	function (extraProps, blockType, attributes) {
		if (!['core/image', 'core/quote', 'core/group'].includes(blockType.name)) return extraProps;

		const { yakPullAlign = '', yakPullAmount = '150' } = attributes || {};
		const classes = extraProps.className ? [extraProps.className] : [];

		// Add alignment class
		if (yakPullAlign === 'left') {
			classes.push('yak-pull-left');
		} else if (yakPullAlign === 'right') {
			classes.push('yak-pull-right');
		}

		// Add class string
		extraProps.className = classes.join(' ').trim();

		// Add pull amount as data attribute
		const amount = parseInt(yakPullAmount || '150', 10);
		if (!isNaN(amount)) {
			extraProps['data-yak-pull'] = amount.toString();
		}

		return extraProps;
	}
);

////////////////////////////////////////////////////////////
// END Pull Left/Right Custom Alignment + Settings
////////////////////////////////////////////////////////////
