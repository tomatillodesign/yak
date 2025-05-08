(function () {
    const { addFilter } = wp.hooks;
    const { createHigherOrderComponent } = wp.compose;
    const { InspectorControls } = wp.blockEditor || wp.editor;
    const { PanelBody, ToggleControl } = wp.components;
    const { Fragment, createElement } = wp.element;

    // 1. Add custom attributes to all core blocks
    addFilter(
         'blocks.registerBlockType',
         'yak/custom-visibility-toggles',
         function (settings, name) {
              if (!name.startsWith('core/')) return settings;

              return Object.assign({}, settings, {
                   attributes: Object.assign({}, settings.attributes, {
                        yakMobileOnly: {
                             type: 'boolean',
                             default: false
                        },
                        yakDesktopOnly: {
                             type: 'boolean',
                             default: false
                        }
                   })
              });
         }
    );

    // 2. Add toggles to block inspector panel
    const withVisibilityToggles = createHigherOrderComponent(function (BlockEdit) {
         return function (props) {
              const { attributes, setAttributes, name } = props;

              if (!name.startsWith('core/')) return createElement(BlockEdit, props);

              return createElement(
                   Fragment,
                   {},
                   createElement(BlockEdit, props),
                   createElement(
                        InspectorControls,
                        {},
                        createElement(
                             PanelBody,
                             { title: 'Visibility Settings', initialOpen: false },
                             createElement(ToggleControl, {
                                  label: 'Mobile Only',
                                  checked: !!attributes.yakMobileOnly,
                                  onChange: function (value) {
                                       setAttributes({ yakMobileOnly: value, yakDesktopOnly: false });
                                  }
                             }),
                             createElement(ToggleControl, {
                                  label: 'Desktop Only',
                                  checked: !!attributes.yakDesktopOnly,
                                  onChange: function (value) {
                                       setAttributes({ yakDesktopOnly: value, yakMobileOnly: false });
                                  }
                             })
                        )
                   )
              );
         };
    }, 'withVisibilityToggles');

    addFilter('editor.BlockEdit', 'yak/with-visibility-toggles', withVisibilityToggles);

    // 3. Add class to saved block output
    addFilter(
         'blocks.getSaveContent.extraProps',
         'yak/add-visibility-classes',
         function (extraProps, blockType, attributes) {
              if (!blockType.name.startsWith('core/')) return extraProps;

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
