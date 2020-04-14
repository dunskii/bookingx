// block.js

( function( blocks, element, serverSideRender, components, editor ) {

    /*var  el = element.createElement,
        registerBlockType = blocks.registerBlockType,
        ServerSideRender = serverSideRender;
    var InspectorControls = editor;
    var TextControl = components.TextControl;
    var ToggleControl = components.ToggleControl;*/

    var el = element.createElement,
        registerBlockType = blocks.registerBlockType,
        ServerSideRender = components.ServerSideRender,
        TextControl = components.TextControl,
        ToggleControl = components.ToggleControl,
        TextareaControl = components.TextareaControl,
        InspectorControls = editor.InspectorControls;


    blocks.registerBlockType( 'booking-x/bkx-seat-block', {
        title: 'Staff Listings',
        icon: 'calendar-alt',
        category: 'booking-x',
        attributes: {
            'showDesc': {
                type: 'boolean',
                default: true
            },
            'ShowImage': {
                type: 'boolean',
                default: true
            },
        },
        edit: ( props ) => {

            return[
                /**
                 * Server side render
                 */
                el("div", {
                        className: "booking-x-container",
                        style: {textAlign: "center"}
                    },
                    el( ServerSideRender, {
                        block: 'booking-x/bkx-seat-block',
                        attributes: props.attributes
                    } )
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