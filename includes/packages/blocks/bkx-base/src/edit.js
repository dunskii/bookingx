/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';
import { PanelBody, SelectControl, ToggleControl,  RangeControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit({ attributes, setAttributes } ) {

	const  base_alias = bkx_base_block_obj.b_alias;
	const  base_option = "Select "+base_alias;

	const onChangeRow = ( row ) => {
		setAttributes( { rows: row } );
	};

	const onChangeColumn = ( column ) => {
		setAttributes( { columns: column } );
	};

	const onbaseChange = ( base_id ) => {
		setAttributes( { basePostId: base_id } );
	};
	const onOrderByChange = ( order_by ) => {
		setAttributes( { orderBy: order_by } );
	};
	const onOrderChange = ( order) => {
		setAttributes( { order: order } );
	};


	const onDescChange = ( desc ) => {
		if (desc == true) {
			setAttributes({baseDescription: 'yes'});
		} else {
			setAttributes({baseDescription: 'no'});
		}
		setAttributes({showDesc: desc});
	};
	const onImageChange = ( desc ) => {
		if (desc == true) {
			setAttributes({baseImageStatus: 'yes'});
		} else {
			setAttributes({baseImageStatus: 'no'});
		}
		setAttributes({showImage: desc});
	};

	const service_options = [];

	wp.apiFetch({
		url: bkx_base_block_obj.end_point_base
	}).then(service => {
		if(service.length === 0 ){
			service_options.push({value: 0, label: 'No '+ {base_alias }+' found, Please add new bases.'})
		}else{
			service_options.push({value: 0, label: 'All '+base_alias});
			service.forEach((post) => { // simple foreach loop
				service_options.push({value: post.id, label: post.title.rendered});
			});
		}
	});

	var order_by = [];
	order_by.push({value: 'ID', label: 'ID'}, {value: 'title', label: 'Name'});
	var order = [];
	order.push({value: 'ASC', label: 'ASC'}, {value: 'DESC', label: 'DESC'});



	return (

		<div { ...useBlockProps() }>
			{ __(
				'Booking X ' + base_alias + ' Listing',
				'bkx-base'
			) }
			<ServerSideRender block="booking-x/bkx-base" attributes={ attributes } />
			<InspectorControls key="setting">
				<PanelBody
					title={ __( 'Layout Settings', 'bookingx' ) }
					initialOpen={ true }>

					<RangeControl
						label={ __( 'Columns', 'bookingx' ) }
						value={ attributes.columns }
						onChange={ onChangeColumn }
						min={ 1 }
						max={ 4 }
					/>
					<RangeControl
						label={ __( 'Rows', 'bookingx' ) }
						value={ attributes.rows }
						onChange={ onChangeRow }
						min={ 1 }
						max={ 6 }
					/>
				</PanelBody>

				<PanelBody
					title={ __( 'Content Settings', 'bookingx' ) }
					initialOpen={ false }>
					<SelectControl
						label= { base_option }
						value={ attributes.basePostId }
						options= { service_options }
						onChange={ onbaseChange }
					/>
					<ToggleControl
						label = "Show Description"
						onChange = { onDescChange }
						checked = { attributes.showDesc }
					/>
					<ToggleControl
						label = "Show Image"
						onChange = { onImageChange }
						checked = { attributes.showImage }
					/>
					<SelectControl
						label= "Order By"
						value={ attributes.orderBy }
						options= { order_by }
						onChange={ onOrderByChange }
					/>
					<SelectControl
						label= "Order"
						value={ attributes.order }
						options= { order }
						onChange={ onOrderChange }
					/>
				</PanelBody>

			</InspectorControls>
		</div>
	);
}