wp.blocks.registerBlockType('wordpress-theme-elegance/work-block', {
    title: 'Work Block',
    icon: 'portfolio',
    category: 'widgets',

    edit: () => {
        const blockProps = wp.blockEditor.useBlockProps();
        const innerBlockProps = wp.blockEditor.useInnerBlocksProps(
            {
                className: 'work-list owl-carousel'
            },
            {
                allowedBlocks: ['wordpress-theme-elegance/work-item-block'],
                template: [['wordpress-theme-elegance/work-item-block']],
                templateLock: false
            }
        );

        return  wp.element.createElement('div', { ...blockProps, className: 'work-section' },
            wp.element.createElement('div', innerBlockProps)
        );
    },

    save: () => {
        return wp.element.createElement('div', { className: 'work-section' },
            wp.element.createElement('div', { className: 'work-list owl-carousel' },
                wp.element.createElement(wp.blockEditor.InnerBlocks.Content)
            )
        );
    }
});
