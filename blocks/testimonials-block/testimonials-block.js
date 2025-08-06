wp.blocks.registerBlockType('wordpress-theme-elegance/testimonials-block', {
    title: 'Testimonials Block',
    icon: 'format-quote',
    category: 'widgets',

    edit: () => {
        const blockProps = wp.blockEditor.useBlockProps();
        const innerBlockProps = wp.blockEditor.useInnerBlocksProps(
            {
                className: 'horizontal-scroll-container testimonials-container'
            },
            {
                allowedBlocks: ['wordpress-theme-elegance/testimonial-item-block'],
                template: [['wordpress-theme-elegance/testimonial-item-block']],
                templateLock: false
            }
        );

        return  wp.element.createElement('div', { ...blockProps, className: 'col-md-8 offset-md-2' },
                    wp.element.createElement('div', { className: 'testimonials-section' },
                        wp.element.createElement('div', innerBlockProps)
                    )
            );
    },

    save: () => {
        return wp.element.createElement('div', { className: 'col-md-8 offset-md-2' },
            wp.element.createElement('div', { className: 'testimonials-section' },
                wp.element.createElement('div', { id: 'testimonials', className: 'horizontal-scroll-container' },
                    wp.element.createElement(wp.blockEditor.InnerBlocks.Content)                                                        
                )
            )
        );
    }
});
