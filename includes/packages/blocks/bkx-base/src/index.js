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
registerBlockType( 'booking-x/bkx-base', {
	icon: <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" id="Layer_1" viewBox="0 0 124 124"
			   className="bkx-svg" role="img" aria-hidden="true" focusable="false">
		<path fill="#333"
			  d="M93.22,64.65a6.37,6.37,0,0,0-8.77.53l-6.24,6.7a6.36,6.36,0,0,0-6.08-4.48H61c-.85,0-1.13-.33-2.6-1.46a14.89,14.89,0,0,0-19.57,0l-4.09,3.6a6.34,6.34,0,0,0-5.84-.07l-4.69,2.34a2.12,2.12,0,0,0-.95,2.85L36,100.18a2.13,2.13,0,0,0,2.85.95l4.69-2.35a6.35,6.35,0,0,0,3.52-5.92H72.13A14.94,14.94,0,0,0,84,86.92L94.21,73.34A6.36,6.36,0,0,0,93.22,64.65ZM41.63,95l-2.79,1.39L28,74.71l2.8-1.4a2.13,2.13,0,0,1,2.85,1l8.93,17.88A2.12,2.12,0,0,1,41.63,95Zm49.18-24.2L80.62,84.37a10.66,10.66,0,0,1-8.49,4.25H45.57l-8-15.91,4-3.53a10.63,10.63,0,0,1,14,0C58,71.25,59.47,71.64,61,71.64H72.13a2.13,2.13,0,0,1,0,4.25H61.29a2.12,2.12,0,0,0,0,4.24H73.55a6.37,6.37,0,0,0,4.66-2l9.35-10a2.12,2.12,0,0,1,3.25,2.72Z"></path>
		<path fill="#333"
			  d="M61.3,48.77a1,1,0,0,1-.72-.32L55.39,42.8a1,1,0,1,1,1.43-1.31l4.48,4.88L72,34.67A1,1,0,0,1,73.46,36L62,48.45A1,1,0,0,1,61.3,48.77Z"></path>
		<path fill="#333"
			  d="M64.43,59.41A18.58,18.58,0,1,1,83,40.83,18.59,18.59,0,0,1,64.43,59.41Zm0-33.28a14.71,14.71,0,1,0,14.7,14.7A14.73,14.73,0,0,0,64.43,26.13Z"></path>
	</svg>,
	category: 'booking-x',
	attributes: {
		'basePostId': {
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
		'baseDescription': {
			type: 'string'
		},
		'baseImageStatus': {
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
