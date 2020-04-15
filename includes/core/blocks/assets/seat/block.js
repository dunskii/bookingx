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

    blocks.registerBlockType( 'booking-x/bkx-seat-block', {
        title: 'Staff Listings',
        icon: 'calendar-alt',
        category: 'booking-x',
        attributes: {
            'showDesc': {
                type: 'boolean',
                default: true
            },
            'showImage': {
                type: 'boolean',
                default: true
            },
            // 'showExtra': {
            //     type: 'boolean',
            //     default: true
            // },
            'seatPosts' : {
                type : 'object'
            },
            'seatPostId': {
                type: 'string'
            },
            'columns':{
                type : 'integer',
                default: 3
            },
            'rows':{
                type : 'integer',
                default: 1
            }
        },
        edit: ( props ) => {

            if(!props.attributes.seatPosts){
                wp.apiFetch({
                    url: '/wp-json/wp/v2/bkx_seat'
                }).then( resource => {
                    props.setAttributes({
                        seatPosts :resource
                    })
                });
            }

            var options = [];

            if( props.attributes.seatPosts ) {
                options.push( { value: 0, label: 'All Seats' } );
                props.attributes.seatPosts.forEach((post) => { // simple foreach loop
                    options.push({value:post.id, label:post.title.rendered});
                });
            } else {
                options.push( { value: 0, label: 'Loading...' } )
            }

            if(props.attributes.seatPosts && props.attributes.seatPosts.length === 0){
                options.push( { value: 0, label: 'No seats found, Please add new seats.' } )
            }

            console.log(props.attributes)
            return[
                /**
                 * Server side render
                 */

                el("div", {
                        className: "booking-x-container"
                    },
                    el( InspectorControls, {},
                        el( PanelBody, { title: 'Layout Settings', initialOpen: true },
                            // Text field
                            el( PanelRow, {},
                                el( RangeControl,
                                    {
                                        label: 'Columns',
                                        onChange: ( value ) => {
                                            props.setAttributes( { columns: value } );
                                        },
                                        min : 1,
                                        max : 4,
                                        value: props.attributes.columns

                                    },
                                )
                            ),

                            el( PanelRow, {},
                                el( RangeControl,
                                    {
                                        label: 'Rows',
                                        onChange: ( value ) => {
                                            props.setAttributes( { rows: value } );
                                        },
                                        min : 1,
                                        max : 6,
                                        value: props.attributes.rows

                                    },
                                )
                            )
                        ),
                        el( PanelBody, { title: 'Content Settings', initialOpen: true },
                            // Text field
                            el( PanelRow, {},
                                el( SelectControl,
                                    {
                                        label: 'Select Seat',
                                        value: props.attributes.seatPostId,
                                        options : options,
                                        onChange: ( value ) => {
                                            props.setAttributes( { seatPostId: value } );
                                        },
                                    },
                                )
                            ),

                            el( PanelRow, {},
                                el( ToggleControl,
                                    {
                                        label: 'Show Description',
                                        onChange: ( value ) => {
                                            props.setAttributes( { showDesc: value } );
                                        },
                                        checked: props.attributes.showDesc,
                                    }
                                )
                            ),

                            el( PanelRow, {},
                                el( ToggleControl,
                                    {
                                        label: 'Show Image',
                                        onChange: ( value ) => {
                                            props.setAttributes( { showImage: value } );
                                        },
                                        checked: props.attributes.showImage,
                                    }
                                ),
                            ),

                           /* el( PanelRow, {},
                                el( ToggleControl,
                                    {
                                        label: 'Show Extra Info',
                                        onChange: ( value ) => {
                                            props.setAttributes( { showExtra: value } );
                                        },
                                        checked: props.attributes.showExtra,
                                    }
                                )
                            ),*/
                        ),
                    ),
                    el( ServerSideRender, {
                        block: 'booking-x/bkx-seat-block',
                        attributes: {
                            showDesc : props.attributes.showDesc,
                            showImage : props.attributes.showImage,
                            //showExtra : props.attributes.showExtra,
                            seatPostId : props.attributes.seatPostId,
                            columns : props.attributes.columns,
                            rows : props.attributes.rows,
                        }
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