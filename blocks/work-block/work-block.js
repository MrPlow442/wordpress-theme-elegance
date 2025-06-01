wp.blocks.registerBlockType('elegance-theme/work-block', {
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
                allowedBlocks: ['elegance-theme/work-item-block'],
                template: [['elegance-theme/work-item-block']],
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
