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

    blocks.registerBlockType( 'booking-x/bkx-base-block', {
        title: 'Service Listings',
        icon: 'calendar-alt',
        category: 'booking-x',
        attributes: {
            'showDesc': {
                type: 'boolean',
            },
            'showImage': {
                type: 'boolean',
            },
            // 'showExtra': {
            //     type: 'boolean',
            //     default: true
            // },
            'basePosts' : {
                type : 'object'
            },
            'basePostId': {
                type: 'string'
            },
            'columns':{
                type : 'integer',
                default: 3
            },
            'rows':{
                type : 'integer',
                default: 1
            },
            'baseDescription':{
                type : 'string',
            },
            'baseImageStatus':{
                type : 'string',
            }
        },
        edit: ( props ) => {

            if(!props.attributes.basePosts){
                wp.apiFetch({
                    url: '/wp-json/wp/v2/bkx_base'
                }).then( resource => {
                    props.setAttributes({
                        basePosts :resource
                    })
                });
            }

            var options = [];

            if( props.attributes.basePosts ) {
                options.push( { value: 0, label: 'All Services' } );
                props.attributes.basePosts.forEach((post) => { // simple foreach loop
                    options.push({value:post.id, label:post.title.rendered});
                });
            } else {
                options.push( { value: 0, label: 'Loading...' } )
            }

            if(props.attributes.basePosts && props.attributes.basePosts.length === 0){
                options.push( { value: 0, label: 'No bases found, Please add new service.' } )
            }
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
                                        label: 'Select base',
                                        value: props.attributes.basePostId,
                                        options : options,
                                        onChange: ( value ) => {
                                            props.setAttributes( { basePostId: value } );
                                            if(value > 0 ){
                                                props.setAttributes( { showDesc: true } );
                                                props.setAttributes( { showImage: true } );
                                                props.setAttributes( { baseImageStatus: 'yes' } );
                                                props.setAttributes( { baseDescription: 'yes' } );
                                            }
                                        },
                                    },
                                )
                            ),

                            el( PanelRow, {},
                                el( ToggleControl,
                                    {
                                        label: 'Show Description',
                                        onChange: ( value ) => {
                                            if(value == true){
                                                props.setAttributes( { baseDescription: 'yes' } );
                                            }else{
                                                props.setAttributes( { baseDescription: 'no' } );
                                            }
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
                                            if(value == true){
                                                props.setAttributes( { baseImageStatus: 'yes' } );
                                            }else{
                                                props.setAttributes( { baseImageStatus: 'no' } );
                                            }
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
                        block: 'booking-x/bkx-base-block',
                        attributes: {
                            baseDescription : (!props.attributes.baseDescription ? 'yes' : props.attributes.baseDescription),
                            baseImageStatus : (!props.attributes.baseImageStatus ? 'yes' : props.attributes.baseImageStatus),
                            basePostId : props.attributes.basePostId,
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