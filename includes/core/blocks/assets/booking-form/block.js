// block.js

( function( blocks, element ) {

    var el = element.createElement,
        source = blocks.source;

    blocks.registerBlockType( 'booking-x/booking-form', {
        title: 'Booking Form',
        icon: 'calendar-alt',
        category: 'booking-x',
        supports: {
            multiple: false,
        },
        attributes: {
            content: {
                type: 'string',
                source: 'html',
                multiline: 'p',
                selector: 'js-booking-form',
            }
        },
        edit: function( props ) {
            var booking_form = '[bookingform]';
            return (booking_form);
        },
        save: function( props ) {
            var booking_form = '[bookingform]';
            return (booking_form);

        },
    } );
} )(

    window.wp.blocks,
    window.wp.element
);
