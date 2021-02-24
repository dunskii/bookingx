// block.js

(function (blocks, element, serverSideRender, components, editor) {

	var el                = element.createElement,
		registerBlockType = blocks.registerBlockType,
		ServerSideRender  = components.ServerSideRender,
		SelectControl     = components.SelectControl,
		TextControl       = components.RadioControl,
		ToggleControl     = components.ToggleControl,
		TextareaControl   = components.TextareaControl,
		InspectorControls = editor.InspectorControls,
		RangeControl      = components.RangeControl,
		Panel             = components.Panel,
		PanelBody         = components.PanelBody,
		PanelRow          = components.PanelRow;

	const icon = el(
		'svg',
		{
			className: 'bkx-svg',
			xmlns: 'http://www.w3.org/2000/svg',
			id: 'Layer_1',
			viewBox: '0 0 124 124'
		},
		el(
			'path',
			{
				class: 'cls-1',
				d: "M96.6,101.72H27.4a8.66,8.66,0,0,1-8.65-8.65V35.4a5.78,5.78,0,0,1,5.77-5.77h75a5.78,5.78,0,0,1,5.77,5.77V93.07A8.66,8.66,0,0,1,96.6,101.72ZM24.52,35.4V93.07A2.88,2.88,0,0,0,27.4,96H96.6a2.88,2.88,0,0,0,2.88-2.88V35.4Z"
			}
		),
		el(
			'path',
			{
				class: 'cls-1',
				d: "M41.82,41.17a2.89,2.89,0,0,1-2.89-2.89V21a2.89,2.89,0,0,1,5.77,0v17.3A2.88,2.88,0,0,1,41.82,41.17Z"
			}
		),
		el(
			'path',
			{
				class: 'cls-1',
				d: "M82.18,41.17a2.88,2.88,0,0,1-2.88-2.89V21a2.89,2.89,0,0,1,5.77,0v17.3A2.89,2.89,0,0,1,82.18,41.17Z"
			}
		),
		el( 'path', {class: 'cls-1', d: "M102.37,49.82H21.63a1.45,1.45,0,0,1,0-2.89h80.74a1.45,1.45,0,0,1,0,2.89Z"} ),
		el(
			'path',
			{
				class: 'cls-1',
				d: "M75.75,57.21A5.19,5.19,0,0,0,74,57.14,7,7,0,0,0,70.51,59l-.24.19a22.21,22.21,0,0,0-4.27,5c-.24.33-.47.67-.71,1l-.4.54-.54.73-.43-1.68c-.21-.82-.41-1.59-.6-2.36a12.46,12.46,0,0,0-2.47-5.14.94.94,0,0,0-1-.41c-1.43.24-2.88.46-4.29.68l-2.6.4c-.23,0-.62.1-.68.19a1.44,1.44,0,0,0,.07.63h.5c.5,0,1,0,1.52,0a4.7,4.7,0,0,1,4.48,3.24,13.58,13.58,0,0,1,.5,1.56v.07c.53,2.23,1.07,4.53,1.55,6.81A2.19,2.19,0,0,1,60.68,72c-2.08,3.14-4,6-5.88,8.65A7.67,7.67,0,0,1,53.44,82l-.38.33a1.26,1.26,0,0,1-1.77,0L51,82.12a9.92,9.92,0,0,0-1-.8,2.47,2.47,0,0,0-1.29-.42,2.13,2.13,0,0,0-1.79.9A2.36,2.36,0,0,0,47.43,85a3.49,3.49,0,0,0,3.86.23A9.48,9.48,0,0,0,55,82.15c1.37-1.86,2.7-3.79,4-5.65l1.53-2.2.33-.52.24-.39.3-.48.2.84c0,.13.06.21.08.29.21,1,.42,1.92.62,2.88.47,2.16.94,4.39,1.51,6.56a3.32,3.32,0,0,0,1.85,2.46,3.38,3.38,0,0,0,3.07-.57A18.45,18.45,0,0,0,74.62,79L74,78.65a.75.75,0,0,1-.07.12,3.67,3.67,0,0,1-.42.61c-.26.27-.51.54-.76.82a21.24,21.24,0,0,1-2.27,2.28,1.73,1.73,0,0,1-1.55.48,2,2,0,0,1-1.18-1.35,11.79,11.79,0,0,1-.35-1.17c-.22-1-.43-2-.65-3-.46-2.22-.94-4.51-1.55-6.73a4.88,4.88,0,0,1,1.06-5c.5-.56,1-1.13,1.48-1.69.7-.81,1.42-1.65,2.17-2.44a2.63,2.63,0,0,1,2.94-.57,11,11,0,0,0,2.15.61A2.11,2.11,0,0,0,77.47,60,2.27,2.27,0,0,0,75.75,57.21Z"
			}
		),
	);

	blocks.registerBlockType(
		'booking-x/bkx-booking-form-block',
		{
			title: 'Booking Form',
			icon: icon,
			category: 'booking-x',
			attributes: {},
			edit: (props) => {

				return [
				/**
				 * Server side render
				 */

				el(
					"div",
					{
						className: "booking-x-form-container"
						},
					el(
						ServerSideRender,
						{
							block: 'booking-x/bkx-booking-form-block',
							attributes: {}
						}
					),
				)
			]
			},
			save: function (props) {
				return null;
			},
		}
	);
})(
	window.wp.blocks,
	window.wp.element,
	window.wp.serverSideRender,
	window.wp.components,
	window.wp.editor
);
