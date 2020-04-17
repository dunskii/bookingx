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

    blocks.registerBlockType( 'booking-x/bkx-addition-block', {
        title: 'Extra Listings',
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
            'additionPosts' : {
                type : 'object'
            },
            'additionPostId': {
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
            'additionDescription':{
                type : 'string',
            },
            'additionImageStatus':{
                type : 'string',
            }
        },
        edit: ( props ) => {

            if(!props.attributes.additionPosts){
                wp.apiFetch({
                    url: '/wp-json/wp/v2/bkx_addition'
                }).then( resource => {
                    props.setAttributes({
                        additionPosts :resource
                    })
                });
            }

            var options = [];

            if( props.attributes.additionPosts ) {
                options.push( { value: 0, label: 'All Extra Services' } );
                props.attributes.additionPosts.forEach((post) => { // simple foreach loop
                    options.push({value:post.id, label:post.title.rendered});
                });
            } else {
                options.push( { value: 0, label: 'Loading...' } )
            }

            if(props.attributes.additionPosts && props.attributes.additionPosts.length === 0){
                options.push( { value: 0, label: 'No extras found, Please add new extra service.' } )
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
                                        label: 'Select addition',
                                        value: props.attributes.additionPostId,
                                        options : options,
                                        onChange: ( value ) => {
                                            props.setAttributes( { additionPostId: value } );
                                            if(value > 0 ){
                                                props.setAttributes( { showDesc: true } );
                                                props.setAttributes( { showImage: true } );
                                                props.setAttributes( { additionDescription: 'yes' } );
                                                props.setAttributes( { additionImageStatus: 'yes' } );
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
                                                props.setAttributes( { additionDescription: 'yes' } );
                                            }else{
                                                props.setAttributes( { additionDescription: 'no' } );
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
                                                props.setAttributes( { additionImageStatus: 'yes' } );
                                            }else{
                                                props.setAttributes( { additionImageStatus: 'no' } );
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
                        block: 'booking-x/bkx-addition-block',
                        attributes: {
                            additionDescription : (!props.attributes.additionDescription ? 'yes' : props.attributes.additionDescription),
                            additionImageStatus : (!props.attributes.additionImageStatus ? 'yes' : props.attributes.additionImageStatus),
                            additionPostId : props.attributes.additionPostId,
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