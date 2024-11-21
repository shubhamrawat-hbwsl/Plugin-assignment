const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.blockEditor;
const { PanelBody, TextControl } = wp.components;
const { createElement } = wp.element;

registerBlockType('wp-book/custom-wp-block', {
    title: 'Custom WP Block',
    description: 'Block to display a custom Call to Action',
    icon: 'format-image',
    category: 'layout',

    // Attributes to hold block data
    attributes: {
        content: {
            type: 'string',
            default: 'This is a custom block!',
        },
    },

    // Edit function: What users see in the block editor
    edit: function(props) {
        const { attributes, setAttributes } = props;
        const { content } = attributes;

        return createElement(
            wp.element.Fragment, 
            null, 
            createElement(InspectorControls, null, 
                createElement(PanelBody, { title: 'Settings' }, 
                    createElement(TextControl, {
                        label: 'Block Content',
                        value: content,
                        onChange: function(newContent) {
                            setAttributes({ content: newContent });
                        }
                    })
                )
            ),
            createElement('div', { className: 'custom-block' },
                createElement('p', null, content)
            )
        );
    },

    // Save function: What is saved in the database
    save: function(props) {
        const { attributes } = props;
        return createElement('div', { className: 'custom-block' },
            createElement('p', null, attributes.content)
        );
    },
});
