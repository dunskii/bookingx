// block.js

( function( blocks, element, serverSideRender ) {

    var  el = element.createElement,
        registerBlockType = blocks.registerBlockType,
        ServerSideRender = serverSideRender;

    blocks.registerBlockType( 'booking-x/bkx-addition-block', {
        title: 'Extra Listings',
        icon: 'calendar-alt',
        category: 'booking-x',
        attributes: {
            content: {
                type: 'string',
                source: 'html',
                multiline: 'p',
                selector: 'js-addition-listing',
            }
        },
        edit: function( props ) {
            return(
                el( ServerSideRender, {
                    block: 'booking-x/bkx-addition-block'
                })
            )
        },
        save: function( props ) {
            return null;
        },
    } );
} )(

    window.wp.blocks,
    window.wp.element,
    window.wp.serverSideRender
);