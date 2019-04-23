/**
 * BLOCK: bl-basel-header
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

//  Import CSS.
import './style.scss';
import './editor.scss';

var classNames = require('classnames');
const { RichText, MediaUpload, PlainText } = wp.editor;
const el = wp.element.createElement;
const { __ } = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks
const iconEl = el('svg',{width:20,height:20},
		el('path',{ d: 'M18.2 0H1.8C.8 0 0 .8 0 1.8v16.3c0 1 .8 1.8 1.8 1.8h16.3c1 0 1.8-.8 1.8-1.8V1.8c.1-1-.7-1.8-1.7-1.8zM1.8 1.5h16.3c.1 0 .2.1.2.1s.1.1.1.2v1.7h-17V1.8c.1-.2.2-.3.4-.3zm16.4 17H1.8c-.2 0-.3-.1-.3-.3V4.5h17v13.6c0 .3-.1.4-.3.4zM16.3 5.8H3.7c-.2 0-.4 0-.5.2-.1.1-.2.3-.2.5v5c0 .2.1.4.2.5.1.1.3.2.5.2h12.5c.2 0 .4-.1.5-.2.1-.1.2-.3.2-.5v-5c0-.2-.1-.4-.2-.5 0-.2-.2-.2-.4-.2zM4.5 10.7V7.3h11v3.5h-11zM14 9c0 .4-.3.7-.7.7H6.7c-.4 0-.7-.3-.7-.7 0-.4.3-.7.7-.7h6.5c.5-.1.8.3.8.7zM2.7 2.5c0-.3.3-.5.5-.5s.5.2.5.5-.2.5-.5.5-.5-.2-.5-.5zm1.7 0c0-.3.2-.5.5-.5s.5.2.5.5-.2.5-.5.5-.5-.2-.5-.5zm1.7 0c0-.3.2-.5.5-.5s.5.2.5.5-.2.5-.5.5-.5-.2-.5-.5z'})
);

/**
 * Register: aa Gutenberg Block.
 *
 * Registers a new block provided a unique name and an object defining its
 * behavior. Once registered, the block is made editor as an option to any
 * editor interface where blocks are implemented.
 *
 * @link https://wordpress.org/gutenberg/handbook/block-api/
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */


   /*
    <div class="title-wrapper  basel-title-color-default basel-title-style-cross basel-title-size-default text-center">
        <span class="title-subtitle font-default">{super_header}</span>
        <div class="liner-continer"> 
            <span class="left-line"></span> 
            <h4 class="title">{header}
                <span class="title-separator" style="position: relative;"><span></span></span>
            </h4> 
            <span class="right-line"></span> 
        </div>
    </div>
  */

registerBlockType( 'cgb/block-bl-basel-header', {
	// Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
	title: __( 'Basel Theme Header Block' ), // Block title.
	icon: iconEl, // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
	category: 'bl-basel-gutenberg-blocks', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
	keywords: [
		__( 'Basel Theme Header Block' ),
		__( 'Basel' ),
		__( 'header' ),
	],
	attributes: {
		super_header: {
			type: 'string',
			selector: '.title-subtitle'
		},
		header: {
			type: 'string',
			selector: 'h4.title'
		},
	},

	/**
	 * The edit function describes the structure of your block in the context of the editor.
	 * This represents what the editor will render when the block is used.
	 *
	 * The "edit" property must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 */
	edit: function( { attributes, className, setAttributes } ) {
		// Creates a <p class='wp-block-cgb-block-bl-basel-header'></p>.

		return (
			<div className={classNames('title-wrapper', 'basel-title-color-default', 'basel-title-style-cross', 'basel-title-size-default', 'text-center')}
			>
				<span className={classNames('title-subtitle', 'font-default')} >
				<PlainText
					onChange={ content => setAttributes({ super_header: content }) }
					value={ attributes.super_header }
					placeholder="Enter Super Header"
					className="heading"
				/>
				</span>
				<div className={classNames('liner-continer')}> 
					<span className={classNames('left-line')}></span> 
					<h4 className={classNames('title')} 
					>
					<PlainText
						onChange={ content => setAttributes({ header: content }) }
						value={ attributes.header }
						placeholder="Enter Header"
						className="heading"
					/>
						<span className={classNames('title-separator')} style={ { position: 'relative' }}><span></span></span>
					</h4> 
					<span className={classNames('right-line')}></span> 
				</div>
			</div>
		);
	},

	/**
	 * The save function defines the way in which the different attributes should be combined
	 * into the final markup, which is then serialized by Gutenberg into post_content.
	 *
	 * The "save" property must be specified and must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 */
	save: function( { attributes } ) {
		return (
			<div className={classNames('title-wrapper', 'basel-title-color-default', 'basel-title-style-cross', 'basel-title-size-default', 'text-center')}
			>
				<span className={classNames('title-subtitle', 'font-default')} >{attributes.super_header}</span>
				<div className={classNames('liner-continer')}> 
					<span className={classNames('left-line')}></span> 
					<h4 className={classNames('title')} >{attributes.header}
						<span className={classNames('title-separator')} style={ { position: 'relative' }}><span></span></span>
					</h4> 
					<span className={classNames('right-line')}></span> 
				</div>
			</div>
		);
	},
} );
