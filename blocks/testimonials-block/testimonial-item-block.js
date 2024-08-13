const TEMPLATE = [
    ['core/heading', { level: 4, placeholder: 'Enter name...' }],
    ['core/paragraph', { placeholder: 'Enter testimonial text...' }],
    ['core/html', { placeholder: 'Enter position...' }],
];

wp.blocks.registerBlockType('elegance-theme/testimonial-item-block', {
    title: 'Testimonial Item',
    icon: 'admin-comments',
    category: 'widgets',
    parent: ['elegance-theme/testimonials-block'], // Only allow within the testimonials block
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
    },
    supports: {
        inserter: false
    },

    edit: (props) => {
        const { attributes, setAttributes } = props;
        const blockProps = wp.blockEditor.useBlockProps({ className: 'item animate', 'data-animate': 'fadeInUp' });

        return wp.element.createElement('div', { ...blockProps },
            wp.element.createElement('div', { className: 'testimonial-item' },
                wp.element.createElement('div', { className: 'testimonial-content' },
                    wp.element.createElement(wp.blockEditor.RichText, { 
                        tagName: 'h4', 
                        value: attributes.name, 
                        placeholder: 'Name', 
                        onChange: (value) => setAttributes({ name: value })}),
                    wp.element.createElement(wp.blockEditor.RichText, { 
                        tagName: 'p', 
                        value: attributes.quote, 
                        placeholder: 'Quote', 
                        onChange: (value) => setAttributes({ quote: value })}),
                    wp.element.createElement(wp.blockEditor.RichText, { 
                        tagName: 'span', 
                        value: attributes.position, 
                        placeholder: 'Position', 
                        onChange: (value) => setAttributes({ position: value })})
                )
            )
        );
    },

    save: (props) => {
        const { attributes, setAttributes } = props;
        return wp.element.createElement('div', { className: 'item animate', 'data-animate': 'fadeInUp' },
            wp.element.createElement('div', { className: 'testimonial-item' },
                wp.element.createElement('div', { className: 'testimonial-content' },
                    wp.element.createElement(wp.blockEditor.RichText.Content, { tagName: 'h4', value: attributes.name }),
                    wp.element.createElement(wp.blockEditor.RichText.Content, { tagName: 'p', value: attributes.quote }),
                    wp.element.createElement(wp.blockEditor.RichText.Content, { tagName: 'span', value: attributes.position })
                )
            )
        );
    }
});
