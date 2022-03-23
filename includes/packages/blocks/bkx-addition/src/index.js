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

( function() {
	const icon = <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 124 124">
		<defs>
			<style>.cls-1{'fill:#333;'}</style>
		</defs>
		<title>IconBlocks</title>
		<path className="cls-1"
			  d="M88.81,32.38a10.79,10.79,0,0,0-3.66-.17c-2.84.34-5,2-7.22,3.81l-.5.4c-3.64,2.87-6.3,6.7-8.87,10.4-.49.7-1,1.41-1.48,2.1-.25.36-.51.71-.82,1.12l-1.12,1.52-.89-3.48c-.44-1.71-.85-3.3-1.25-4.9A25.78,25.78,0,0,0,57.87,32.5a2,2,0,0,0-2.1-.85c-3,.49-6,1-8.9,1.4l-5.41.84c-.48.07-1.29.2-1.42.39a2.9,2.9,0,0,0,.16,1.31l1,0c1,0,2.1,0,3.16.09a9.76,9.76,0,0,1,9.3,6.71,28.09,28.09,0,0,1,1,3.25l0,.15C55.85,50.44,57,55.23,58,60a4.55,4.55,0,0,1-.45,3.16c-4.32,6.52-8.31,12.4-12.21,18a16.18,16.18,0,0,1-2.83,2.84c-.26.23-.53.45-.78.68a2.6,2.6,0,0,1-3.68,0l-.57-.47a21.58,21.58,0,0,0-2.16-1.68,5.08,5.08,0,0,0-2.68-.86,4.43,4.43,0,0,0-3.72,1.86A4.89,4.89,0,0,0,30,90.16c2.29,1.75,4.83,1.9,8,.48a19.72,19.72,0,0,0,7.73-6.47c2.84-3.85,5.61-7.86,8.28-11.73,1.06-1.53,2.11-3.06,3.18-4.58.22-.32.42-.65.68-1.08l.5-.81L59,65l.44,1.74c.06.27.11.44.14.61q.68,3,1.3,6c1,4.48,2,9.12,3.14,13.62.69,2.64,2,4.41,3.84,5.12s4.06.31,6.39-1.19A38.2,38.2,0,0,0,86.47,77.69l-1.39-.78c0,.08-.09.16-.14.24a7,7,0,0,1-.89,1.26c-.52.56-1,1.14-1.56,1.72a44,44,0,0,1-4.73,4.73,3.58,3.58,0,0,1-3.21,1,4,4,0,0,1-2.45-2.8,20.11,20.11,0,0,1-.72-2.43c-.47-2.1-.91-4.21-1.35-6.31-1-4.61-2-9.38-3.23-14-1.17-4.27-.5-7.5,2.19-10.47,1.05-1.16,2.09-2.36,3.09-3.52,1.45-1.68,3-3.42,4.51-5.06a5.43,5.43,0,0,1,6.09-1.18,23.66,23.66,0,0,0,4.48,1.27c2.56.38,4.81-1,5.24-3.25A4.7,4.7,0,0,0,88.81,32.38Z"/>
	</svg>;
	wp.blocks.updateCategory( 'booking-x', { icon: icon } );
} )();

registerBlockType( 'booking-x/bkx-addition', {
	title: 'Booking X Extra Listing', // Block name visible to user
	icon: <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" id="Layer_1" viewBox="0 0 124 124"
			   className="bkx-svg" role="img" aria-hidden="true" focusable="false">
		<path className="bkx-svg"
			  d="M40.61,72.92a9.69,9.69,0,1,1,9.69-9.69A9.69,9.69,0,0,1,40.61,72.92Zm0-15.42a5.73,5.73,0,1,0,5.73,5.73A5.74,5.74,0,0,0,40.61,57.5Z"></path>
		<path className="bkx-svg"
			  d="M62.56,72.92a9.69,9.69,0,1,1,9.69-9.69A9.69,9.69,0,0,1,62.56,72.92Zm0-15.42a5.73,5.73,0,1,0,5.73,5.73A5.74,5.74,0,0,0,62.56,57.5Z"></path>
		<path className="bkx-svg"
			  d="M84.51,72.92a9.69,9.69,0,1,1,9.69-9.69A9.69,9.69,0,0,1,84.51,72.92Zm0-15.42a5.73,5.73,0,1,0,5.73,5.73A5.74,5.74,0,0,0,84.51,57.5Z"></path>
	</svg>,
	category: 'booking-x',
	attributes: {
		'additionPostId': {
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
		'additionDescription': {
			type: 'string'
		},
		'additionImageStatus': {
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
