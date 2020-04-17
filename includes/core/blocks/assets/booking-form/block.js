// block.js

( function( blocks, element, serverSideRender, components, editor ) {

    var el = element.createElement,
        registerBlockType = blocks.registerBlockType,
        ServerSideRender = components.ServerSideRender,
        SelectControl = components.SelectControl,
        TextControl = components.RadioControl,
        ToggleControl = components.ToggleControl,
        TextareaControl = components.TextareaControl,
        InspectorControls = editor.InspectorControls,
        RangeControl = components.RangeControl,
        Panel= components.Panel,
        PanelBody= components.PanelBody,
        PanelRow = components.PanelRow;

    blocks.registerBlockType( 'booking-x/bkx-booking-form-block', {
        title: 'Booking Form',
        icon: 'calendar-alt',
        category: 'booking-x',
        attributes: {

        },
        edit: ( props ) => {

            return[
                /**
                 * Server side render
                 */

                el("div", {
                        className: "booking-x-form-container"
                    },
                    el( ServerSideRender, {
                        block: 'booking-x/bkx-booking-form-block',
                        attributes: {}
                    } ),
                )
            ]
        },
        save: function( props ) {
            return null;
        },
    } );
} )(

    window.wp.blocks,
    window.wp.element,
    window.wp.serverSideRender,
    window.wp.components,
    window.wp.editor
);