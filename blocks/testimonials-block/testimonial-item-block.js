const TEMPLATE = [
    ['core/heading', { level: 4, placeholder: 'Enter name...' }],
    ['core/paragraph', { placeholder: 'Enter testimonial text...' }],
    ['core/html', { placeholder: 'Enter position...' }],
];

wp.blocks.registerBlockType('wordpress-theme-elegance/testimonial-item-block', {
    title: 'Testimonial Item',
    icon: 'admin-comments',
    category: 'widgets',
    parent: ['wordpress-theme-elegance/testimonials-block'], // Only allow within the testimonials block
    attributes: {
        name: {
            type: 'string',
            source: 'html',
            selector: 'h4',
        },
        quote: {
            type: 'string',
            source: 'html',
            selector: 'p',
        },
        position: {
            type: 'string',
            source: 'html',
            selector: 'span',
        },
        imageUrl: { // Add an attribute for the image URL
            type: 'string',
            default: '', // Default to empty string if no image is uploaded
        },
    },
    supports: {
        inserter: false
    },

    edit: (props) => {
        const { attributes, setAttributes } = props;
        const { name, quote, position, imageUrl } = attributes;
        const blockProps = wp.blockEditor.useBlockProps({ className: 'item animate', 'data-animate': 'fadeInUp' });

        return wp.element.createElement('div', { ...blockProps },
            wp.element.createElement('div', { className: 'testimonial-item' },
                // Conditionally render the image if present
                imageUrl && wp.element.createElement('div', { className: 'client-row' },
                    wp.element.createElement('img', { src: imageUrl, className: 'rounded-circle', alt: 'Client image' })
                ),
                wp.element.createElement(wp.blockEditor.MediaUploadCheck, {},
                    wp.element.createElement(wp.blockEditor.MediaUpload, {
                        onSelect: (media) => setAttributes({ imageUrl: media.url }),
                        allowedTypes: ['image'],
                        render: ({ open }) => wp.element.createElement(wp.element.Fragment, {},
                            wp.element.createElement(wp.components.Button, { onClick: open },
                                imageUrl ? 'Change Image' : 'Upload Image'
                            ),
                            // Add a "Remove Image" button
                            imageUrl && wp.element.createElement(wp.components.Button, {
                                onClick: () => setAttributes({ imageUrl: '' }),
                                isDestructive: true,
                                style: { marginLeft: '10px' }
                            }, 'Remove Image')
                        )
                    })
                ),
                wp.element.createElement('div', { className: 'testimonial-content' },
                    wp.element.createElement(wp.blockEditor.RichText, { 
                        tagName: 'h4', 
                        value: name, 
                        placeholder: 'Name', 
                        onChange: (value) => setAttributes({ name: value })
                    }),
                    wp.element.createElement(wp.blockEditor.RichText, { 
                        tagName: 'p', 
                        value: quote, 
                        placeholder: 'Quote', 
                        onChange: (value) => setAttributes({ quote: value })
                    }),
                    wp.element.createElement(wp.blockEditor.RichText, { 
                        tagName: 'span', 
                        value: position, 
                        placeholder: 'Position', 
                        onChange: (value) => setAttributes({ position: value })
                    })
                )
            )
        );
    },

    save: (props) => {
        const { attributes } = props;
        const { name, quote, position, imageUrl } = attributes;

        return wp.element.createElement('div', { className: 'item animate', 'data-animate': 'fadeInUp' },
            wp.element.createElement('div', { className: 'testimonial-item' },
                // Conditionally render the image if present
                imageUrl && wp.element.createElement('div', { className: 'client-row' },
                    wp.element.createElement('img', { src: imageUrl, className: 'rounded-circle', alt: 'Client image' })
                ),
                wp.element.createElement('div', { className: 'testimonial-content' },
                    wp.element.createElement(wp.blockEditor.RichText.Content, { tagName: 'h4', value: name }),
                    wp.element.createElement(wp.blockEditor.RichText.Content, { tagName: 'p', value: quote }),
                    wp.element.createElement(wp.blockEditor.RichText.Content, { tagName: 'span', value: position })
                )
            )
        );
    }
});
