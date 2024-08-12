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
    supports: {
        inserter: false
    },

    edit: () => {
        const blockProps = wp.blockEditor.useBlockProps({ className: 'item animate', 'data-animate': 'fadeInUp' });

        return wp.element.createElement('div', { ...blockProps },
            wp.element.createElement('div', { className: 'testimonial-item' },
                wp.element.createElement('div', { className: 'testimonial-content' },
                    wp.element.createElement(wp.blockEditor.InnerBlocks, { template: TEMPLATE, templateLock: 'all' })
                )
            )
        );
    },

    save: () => {
        return wp.element.createElement('div', { className: 'item animate', 'data-animate': 'fadeInUp' },
            wp.element.createElement('div', { className: 'testimonial-item' },
                wp.element.createElement('div', { className: 'testimonial-content' },
                    wp.element.createElement(wp.blockEditor.InnerBlocks.Content)
                )
            )
        );
    }
});
