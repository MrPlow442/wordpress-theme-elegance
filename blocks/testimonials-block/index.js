wp.blocks.registerBlockType('elegance-theme/testimonials-block', {
    title: 'Testimonials Block',
    icon: 'format-quote',
    category: 'widgets',

    edit: () => {
        const blockProps = wp.blockEditor.useBlockProps();
        const innerBlockProps = wp.blockEditor.useInnerBlocksProps(
            {
                className: 'testimonials-slider owl-carousel'
            },
            {
                allowedBlocks: ['elegance-theme/testimonial-item-block'],
                template: [['elegance-theme/testimonial-item-block']],
                templateLock: false
            }
        );

        return  wp.element.createElement('div', { ...blockProps, className: 'col-md-8 offset-md-2' },
                    wp.element.createElement('div', { className: 'testimonials-section' },
                        wp.element.createElement('div', innerBlockProps
                            /*,wp.element.createElement(wp.blockEditor.InnerBlocks, { allowedBlocks: ['elegance-theme/testimonial-item-block'], template: [['elegance-theme/testimonial-item-block']], templateLock: false })*/
                        )
                    )
            );
    },

    save: () => {
        return wp.element.createElement('div', { className: 'col-md-8 offset-md-2' },
            wp.element.createElement('div', { className: 'testimonials-section' },
                wp.element.createElement('div', { className: 'testimonials-slider owl-carousel' },
                    wp.element.createElement(wp.blockEditor.InnerBlocks.Content)
                )
            )
        );
    }
});
