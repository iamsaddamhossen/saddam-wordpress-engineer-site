(function (blocks, blockEditor, components, element, i18n, serverSideRender) {
	'use strict';

	var registerBlockType = blocks.registerBlockType;
	var InspectorControls = blockEditor.InspectorControls;
	var PanelBody = components.PanelBody;
	var RangeControl = components.RangeControl;
	var TextControl = components.TextControl;
	var ToggleControl = components.ToggleControl;
	var Fragment = element.Fragment;
	var __ = i18n.__;
	var ServerSideRender = serverSideRender;

	registerBlockType('spcore/engineering-proof', {
		edit: function (props) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;

			return element.createElement(
				Fragment,
				null,
				element.createElement(
					InspectorControls,
					null,
					element.createElement(
						PanelBody,
						{ title: __('Engineering proof settings', 'spcore'), initialOpen: true },
						element.createElement(TextControl, {
							label: __('Heading', 'spcore'),
							value: attributes.title,
							onChange: function (value) { setAttributes({ title: value }); }
						}),
						element.createElement(RangeControl, {
							label: __('Features to show', 'spcore'),
							value: attributes.limit,
							min: 3,
							max: 12,
							onChange: function (value) { setAttributes({ limit: value }); }
						}),
						element.createElement(ToggleControl, {
							label: __('Show source-file evidence', 'spcore'),
							checked: attributes.showEvidence,
							onChange: function (value) { setAttributes({ showEvidence: value }); }
						})
					)
				),
				element.createElement(ServerSideRender, {
					block: 'spcore/engineering-proof',
					attributes: attributes
				})
			);
		},
		save: function () {
			return null;
		}
	});
})(window.wp.blocks, window.wp.blockEditor, window.wp.components, window.wp.element, window.wp.i18n, window.wp.serverSideRender);
