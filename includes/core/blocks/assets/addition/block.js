// block.js

(function (blocks, element, serverSideRender, components, editor) {

    var el = element.createElement,
        registerBlockType = blocks.registerBlockType,
        ServerSideRender = components.ServerSideRender,
        SelectControl = components.SelectControl,
        TextControl = components.RadioControl,
        ToggleControl = components.ToggleControl,
        TextareaControl = components.TextareaControl,
        InspectorControls = editor.InspectorControls,
        RangeControl = components.RangeControl,
        Panel = components.Panel,
        PanelBody = components.PanelBody,
        PanelRow = components.PanelRow;

    const {updateCategory} = blocks;

    const main_icon = el('svg', {
            className: 'bkx-svg',
            width: 24,
            height: 24,
            xmlns: 'http://www.w3.org/2000/svg',
            id: 'Layer_1',
            viewBox: '0 0 124 124'
        },
        el('path', {
            class: 'bkx-svg',
            fill: '#333',
            d: "M88.81,32.38a10.79,10.79,0,0,0-3.66-.17c-2.84.34-5,2-7.22,3.81l-.5.4c-3.64,2.87-6.3,6.7-8.87,10.4-.49.7-1,1.41-1.48,2.1-.25.36-.51.71-.82,1.12l-1.12,1.52-.89-3.48c-.44-1.71-.85-3.3-1.25-4.9A25.78,25.78,0,0,0,57.87,32.5a2,2,0,0,0-2.1-.85c-3,.49-6,1-8.9,1.4l-5.41.84c-.48.07-1.29.2-1.42.39a2.9,2.9,0,0,0,.16,1.31l1,0c1,0,2.1,0,3.16.09a9.76,9.76,0,0,1,9.3,6.71,28.09,28.09,0,0,1,1,3.25l0,.15C55.85,50.44,57,55.23,58,60a4.55,4.55,0,0,1-.45,3.16c-4.32,6.52-8.31,12.4-12.21,18a16.18,16.18,0,0,1-2.83,2.84c-.26.23-.53.45-.78.68a2.6,2.6,0,0,1-3.68,0l-.57-.47a21.58,21.58,0,0,0-2.16-1.68,5.08,5.08,0,0,0-2.68-.86,4.43,4.43,0,0,0-3.72,1.86A4.89,4.89,0,0,0,30,90.16c2.29,1.75,4.83,1.9,8,.48a19.72,19.72,0,0,0,7.73-6.47c2.84-3.85,5.61-7.86,8.28-11.73,1.06-1.53,2.11-3.06,3.18-4.58.22-.32.42-.65.68-1.08l.5-.81L59,65l.44,1.74c.06.27.11.44.14.61q.68,3,1.3,6c1,4.48,2,9.12,3.14,13.62.69,2.64,2,4.41,3.84,5.12s4.06.31,6.39-1.19A38.2,38.2,0,0,0,86.47,77.69l-1.39-.78c0,.08-.09.16-.14.24a7,7,0,0,1-.89,1.26c-.52.56-1,1.14-1.56,1.72a44,44,0,0,1-4.73,4.73,3.58,3.58,0,0,1-3.21,1,4,4,0,0,1-2.45-2.8,20.11,20.11,0,0,1-.72-2.43c-.47-2.1-.91-4.21-1.35-6.31-1-4.61-2-9.38-3.23-14-1.17-4.27-.5-7.5,2.19-10.47,1.05-1.16,2.09-2.36,3.09-3.52,1.45-1.68,3-3.42,4.51-5.06a5.43,5.43,0,0,1,6.09-1.18,23.66,23.66,0,0,0,4.48,1.27c2.56.38,4.81-1,5.24-3.25A4.7,4.7,0,0,0,88.81,32.38Z"
        }),
    );

    /*const icon = el('svg', { className: 'bkx-svg', width : 24, height : 24, xmlns: 'http://www.w3.org/2000/svg', id : 'Layer_1', viewBox: '0 0 124 124'  },
        el('defs'),
        el('style', {cssText : '.bkx-svg{fill:#333;}'}),
        el('g', { id:'group' },
            el('g', { id : 'group-1'}, el('path', { class : 'bkx-svg',d: "M82.06,97.5H37.62a7.41,7.41,0,0,1-7.4-7.41V35.91a7.41,7.41,0,0,1,7.4-7.41H82.06a7.41,7.41,0,0,1,7.41,7.41V90.09A7.41,7.41,0,0,1,82.06,97.5ZM37.62,33.44a2.46,2.46,0,0,0-2.46,2.47V90.09a2.46,2.46,0,0,0,2.46,2.47H82.06a2.46,2.46,0,0,0,2.47-2.47V35.91a2.46,2.46,0,0,0-2.47-2.47Z" } ) ),
            el('g', { id : 'group-2'}, el('path', { class : 'bkx-svg',d: "M76.49,45.18H53.94a1.24,1.24,0,0,1,0-2.47H76.49a1.24,1.24,0,1,1,0,2.47Z" } ) ),
            el('g', { id : 'group-2'}, el('path', { class : 'bkx-svg',d: "M76.49,57.21H53.94a1.24,1.24,0,0,1,0-2.47H76.49a1.24,1.24,0,1,1,0,2.47Z" } ) ),
            el('g', { id : 'group-2'}, el('path', { class : 'bkx-svg',d: "M76.49,69.23H53.94a1.23,1.23,0,1,1,0-2.46H76.49a1.23,1.23,0,1,1,0,2.46Z" } ) ),
            el('g', { id : 'group-2'}, el('path', { class : 'bkx-svg',d: "M76.49,81.26H53.94a1.24,1.24,0,0,1,0-2.47H76.49a1.24,1.24,0,1,1,0,2.47Z" } ) ),
            el('path', { class : 'bkx-svg',d: "M46,48.37h-3a2.93,2.93,0,0,1-2.92-2.93v-3a2.92,2.92,0,0,1,2.92-2.92h3A2.92,2.92,0,0,1,49,42.45v3A2.93,2.93,0,0,1,46,48.37Zm-3-6.37a.45.45,0,0,0-.45.45v3a.46.46,0,0,0,.45.46h3a.46.46,0,0,0,.45-.46v-3A.45.45,0,0,0,46,42Z" }),
            el('path', { class : 'bkx-svg',d: "M46,60.39h-3a2.92,2.92,0,0,1-2.92-2.92v-3a2.93,2.93,0,0,1,2.92-2.93h3A2.93,2.93,0,0,1,49,54.48v3A2.92,2.92,0,0,1,46,60.39Zm-3-6.37a.46.46,0,0,0-.45.46v3a.45.45,0,0,0,.45.45h3a.45.45,0,0,0,.45-.45v-3A.46.46,0,0,0,46,54Z" }),
            el('path', { class : 'bkx-svg',d: "M46,72.42h-3a2.93,2.93,0,0,1-2.92-2.93v-3a2.92,2.92,0,0,1,2.92-2.92h3A2.92,2.92,0,0,1,49,66.5v3A2.93,2.93,0,0,1,46,72.42Zm-3-6.37a.45.45,0,0,0-.45.45v3a.46.46,0,0,0,.45.46h3a.46.46,0,0,0,.45-.46v-3a.45.45,0,0,0-.45-.45Z" }),
            el('path', { class : 'bkx-svg',d: "M46,84.13h-3a2.93,2.93,0,0,1-2.92-2.93v-3a2.92,2.92,0,0,1,2.92-2.92h3A2.92,2.92,0,0,1,49,78.21v3A2.93,2.93,0,0,1,46,84.13Zm-3-6.37a.45.45,0,0,0-.45.45v3a.46.46,0,0,0,.45.46h3a.46.46,0,0,0,.45-.46v-3a.45.45,0,0,0-.45-.45Z" }),
        ),
     );*/
    const icon = el('svg', {
            className: 'bkx-svg',
            width: 24,
            height: 24,
            xmlns: 'http://www.w3.org/2000/svg',
            id: 'Layer_1',
            viewBox: '0 0 124 124'
        },
        el('path', {
            class: 'bkx-svg',
            d: "M40.61,72.92a9.69,9.69,0,1,1,9.69-9.69A9.69,9.69,0,0,1,40.61,72.92Zm0-15.42a5.73,5.73,0,1,0,5.73,5.73A5.74,5.74,0,0,0,40.61,57.5Z"
        }),
        el('path', {
            class: 'bkx-svg',
            d: "M62.56,72.92a9.69,9.69,0,1,1,9.69-9.69A9.69,9.69,0,0,1,62.56,72.92Zm0-15.42a5.73,5.73,0,1,0,5.73,5.73A5.74,5.74,0,0,0,62.56,57.5Z"
        }),
        el('path', {
            class: 'bkx-svg',
            d: "M84.51,72.92a9.69,9.69,0,1,1,9.69-9.69A9.69,9.69,0,0,1,84.51,72.92Zm0-15.42a5.73,5.73,0,1,0,5.73,5.73A5.74,5.74,0,0,0,84.51,57.5Z"
        }),
    );

    updateCategory('booking-x', {icon: main_icon});

    blocks.registerBlockType('booking-x/bkx-addition-block', {
        title: bkx_block_obj.e_alias+' Listings',
        icon: icon,
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
            'additionPosts': {
                type: 'object'
            },
            'additionPostId': {
                type: 'string'
            },
            'columns': {
                type: 'integer',
                default: 3
            },
            'rows': {
                type: 'integer',
                default: 1
            },
            'additionDescription': {
                type: 'string',
            },
            'additionImageStatus': {
                type: 'string',
            }
        },
        edit: (props) => {

            if (!props.attributes.additionPosts) {
                wp.apiFetch({
                    url: bkx_block_obj.site_url+'/wp-json/wp/v2/bkx_addition'
                }).then(resource => {
                    props.setAttributes({
                        additionPosts: resource
                    })
                });
            }

            var options = [];

            if (props.attributes.additionPosts) {
                options.push({value: 0, label: 'All Extra Services'});
                props.attributes.additionPosts.forEach((post) => { // simple foreach loop
                    options.push({value: post.id, label: post.title.rendered});
                });
            } else {
                options.push({value: 0, label: 'Loading...'})
            }

            if (props.attributes.additionPosts && props.attributes.additionPosts.length === 0) {
                options.push({value: 0, label: 'No extras found, Please add new extra service.'})
            }
            return [
                /**
                 * Server side render
                 */

                el("div", {
                        className: "booking-x-container"
                    },
                    el(InspectorControls, {},
                        el(PanelBody, {title: 'Layout Settings', initialOpen: true},
                            // Text field
                            el(PanelRow, {},
                                el(RangeControl,
                                    {
                                        label: 'Columns',
                                        onChange: (value) => {
                                            props.setAttributes({columns: value});
                                        },
                                        min: 1,
                                        max: 4,
                                        value: props.attributes.columns

                                    },
                                )
                            ),

                            el(PanelRow, {},
                                el(RangeControl,
                                    {
                                        label: 'Rows',
                                        onChange: (value) => {
                                            props.setAttributes({rows: value});
                                        },
                                        min: 1,
                                        max: 6,
                                        value: props.attributes.rows

                                    },
                                )
                            )
                        ),
                        el(PanelBody, {title: 'Content Settings', initialOpen: true},
                            // Text field
                            el(PanelRow, {},
                                el(SelectControl,
                                    {
                                        label: 'Select '+bkx_block_obj.e_alias,
                                        value: props.attributes.additionPostId,
                                        options: options,
                                        onChange: (value) => {
                                            props.setAttributes({additionPostId: value});
                                            if (value > 0) {
                                                props.setAttributes({showDesc: true});
                                                props.setAttributes({showImage: true});
                                                props.setAttributes({additionDescription: 'yes'});
                                                props.setAttributes({additionImageStatus: 'yes'});
                                            }
                                        },
                                    },
                                )
                            ),

                            el(PanelRow, {},
                                el(ToggleControl,
                                    {
                                        label: 'Show Description',
                                        onChange: (value) => {
                                            if (value == true) {
                                                props.setAttributes({additionDescription: 'yes'});
                                            } else {
                                                props.setAttributes({additionDescription: 'no'});
                                            }
                                            props.setAttributes({showDesc: value});
                                        },
                                        checked: props.attributes.showDesc,
                                    }
                                )
                            ),

                            el(PanelRow, {},
                                el(ToggleControl,
                                    {
                                        label: 'Show Image',
                                        onChange: (value) => {
                                            if (value == true) {
                                                props.setAttributes({additionImageStatus: 'yes'});
                                            } else {
                                                props.setAttributes({additionImageStatus: 'no'});
                                            }
                                            props.setAttributes({showImage: value});
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
                    el(ServerSideRender, {
                        block: 'booking-x/bkx-addition-block',
                        attributes: {
                            additionDescription: (!props.attributes.additionDescription ? 'yes' : props.attributes.additionDescription),
                            additionImageStatus: (!props.attributes.additionImageStatus ? 'yes' : props.attributes.additionImageStatus),
                            additionPostId: props.attributes.additionPostId,
                            columns: props.attributes.columns,
                            rows: props.attributes.rows,
                        }
                    }),
                )
            ]
        },
        save: function (props) {
            return null;
        },
    });
})(
    window.wp.blocks,
    window.wp.element,
    window.wp.serverSideRender,
    window.wp.components,
    window.wp.editor
);