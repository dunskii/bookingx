jQuery( function( $ ) {
    // Common scroll to element code.
    $.bkx_scroll_to_notices = function( scrollElement ) {
        if ( scrollElement.length ) {
            $( 'html, body' ).animate( {
                scrollTop: ( scrollElement.offset().top - 100 )
            }, 1000 );
        }
    };

    var owl = $('.owl-carousel');
    owl.owlCarousel({
        loop: false,
        nav: true,
        responsive: {
            0: {
                items: 1
            },
            900: {
                items: 2
            },
            1000: {
                items: 3
            }
        }
    });
});



