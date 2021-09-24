/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';
const { serverSideRender: ServerSideRender } = wp;
const {
	PanelBody, SelectControl, ToggleControl,  RangeControl
} = wp.components;

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

	const  addition_alias = bkx_addition_block_obj.e_alias;
	const  addition_option = "Select "+addition_alias;

	const onChangeRow = ( row ) => {
		setAttributes( { rows: row } );
	};

	const onChangeColumn = ( column ) => {
		setAttributes( { columns: column } );
	};

	const onadditionChange = ( addition_id ) => {
		setAttributes( { additionPostId: addition_id } );
	};
	const onOrderByChange = ( order_by ) => {
		setAttributes( { orderBy: order_by } );
	};
	const onOrderChange = ( order) => {
		setAttributes( { order: order } );
	};


	const onDescChange = ( desc ) => {
		if (desc == true) {
			setAttributes({additionDescription: 'yes'});
		} else {
			setAttributes({additionDescription: 'no'});
		}
		setAttributes({showDesc: desc});
	};
	const onImageChange = ( desc ) => {
		if (desc == true) {
			setAttributes({additionImageStatus: 'yes'});
		} else {
			setAttributes({additionImageStatus: 'no'});
		}
		setAttributes({showImage: desc});
	};

	const service_options = [];

	wp.apiFetch({
		url: bkx_addition_block_obj.end_point_addition
	}).then(service => {
		if(service.length === 0 ){
			service_options.push({value: 0, label: 'No '+ {addition_alias }+' found, Please add new additions.'})
		}else{
			service_options.push({value: 0, label: 'All '+addition_alias});
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
				'Booking X ' + addition_alias + ' Listing',
				'bkx-addition'
			) }

			<ServerSideRender block="booking-x/bkx-addition" attributes={ attributes } />
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
						label= { addition_option }
						value={ attributes.additionPostId }
						options= { service_options }
						onChange={ onadditionChange }
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
