/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './style.scss';

/**
 * Internal dependencies
 */
import Edit from './edit';
import save from './save';

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType( 'booking-x/bkx-seat', {
	icon : <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" id="Layer_1" viewBox="0 0 124 124"
				className="bkx-svg" role="img" aria-hidden="true" focusable="false">
		<path fill="#333"
			  d="M48.45,44.7a7.34,7.34,0,0,0-1.28-1.07,7.27,7.27,0,1,0-10.28,0,9.07,9.07,0,0,0-4,7.47v4.51a1,1,0,0,0,1.94,0V51.11a7.14,7.14,0,0,1,3.68-6.23,7.3,7.3,0,0,0,6.93,0,6.53,6.53,0,0,1,1.59,1.17,7.05,7.05,0,0,1,2.09,5v4.51a1,1,0,0,0,1.94,0V51.11A8.93,8.93,0,0,0,48.45,44.7ZM36.68,38.48A5.35,5.35,0,1,1,42,43.83,5.35,5.35,0,0,1,36.68,38.48Z"></path>
		<path fill="#333"
			  d="M42,61.2A18.6,18.6,0,1,1,60.62,42.6,18.62,18.62,0,0,1,42,61.2Zm0-33.32A14.73,14.73,0,1,0,56.75,42.6,14.74,14.74,0,0,0,42,27.88Z"></path>
		<path fill="#333"
			  d="M42,101.26a18.6,18.6,0,1,1,18.59-18.6A18.62,18.62,0,0,1,42,101.26Zm0-33.33A14.73,14.73,0,1,0,56.75,82.66,14.75,14.75,0,0,0,42,67.93Z"></path>
		<path fill="#333"
			  d="M42,93.48a1,1,0,0,1-.77-.38c-.74-1-7.24-9.49-7.24-13.47a8,8,0,0,1,16,0c0,4-6.5,12.51-7.24,13.47A1,1,0,0,1,42,93.48Zm0-19.71A6,6,0,0,0,36,79.63C36,82.12,39.82,87.9,42,90.9c2.2-3,6.07-8.78,6.07-11.27A6,6,0,0,0,42,73.77Z"></path>
		<path fill="#333"
			  d="M42,82.92a3.08,3.08,0,1,1,3.07-3.08A3.08,3.08,0,0,1,42,82.92Zm0-4.22a1.15,1.15,0,1,0,1.14,1.14A1.14,1.14,0,0,0,42,78.7Z"></path>
		<path fill="#333"
			  d="M92.25,43.47H71.83a1,1,0,0,1-1-1V37.79a1,1,0,0,1,1-1H92.25a1,1,0,0,1,1,1V42.5A1,1,0,0,1,92.25,43.47ZM72.8,41.54H91.28V38.76H72.8Z"></path>
		<path fill="#333" d="M73.4,56a1,1,0,0,1-1-1V42.5a1,1,0,0,1,1.94,0V55.07A1,1,0,0,1,73.4,56Z"></path>
		<path fill="#333" d="M90.68,56a1,1,0,0,1-1-1V42.5a1,1,0,0,1,1.94,0V55.07A1,1,0,0,1,90.68,56Z"></path>
		<path fill="#333"
			  d="M82,60.38a18.57,18.57,0,1,1,18.57-18.57A18.59,18.59,0,0,1,82,60.38Zm0-33.27a14.7,14.7,0,1,0,14.7,14.7A14.71,14.71,0,0,0,82,27.11Z"></path>
		<path fill="#333"
			  d="M78.87,89.89a1,1,0,0,1-.71-.32L73,83.92a1,1,0,1,1,1.42-1.31l4.48,4.88L89.6,75.79A1,1,0,1,1,91,77.1L79.58,89.57A1,1,0,0,1,78.87,89.89Z"></path>
		<path fill="#333"
			  d="M82,100.53A18.58,18.58,0,1,1,100.57,82,18.59,18.59,0,0,1,82,100.53Zm0-33.28A14.71,14.71,0,1,0,96.7,82,14.72,14.72,0,0,0,82,67.25Z"></path>
	</svg>,
	category: 'booking-x',
	attributes: {
		'seatPostId': {
			type: 'string'
		},
		'orderBy': {
			type: 'string'
		},
		'order': {
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
		'showDesc': {
			type: 'boolean'
		},
		'showImage': {
			type: 'boolean'
		},
		'seatDescription': {
			type: 'string'
		},
		'seatImageStatus': {
			type: 'string'
		}
	},
	/**
	 * @see ./edit.js
	 */
	edit: Edit,

	/**
	 * @see ./save.js
	 */
	save,


} );
