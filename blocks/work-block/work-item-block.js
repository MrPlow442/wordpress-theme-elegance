wp.blocks.registerBlockType('wordpress-theme-elegance/work-item-block', {
    title: 'Work Item Block',
    icon: 'format-image',
    category: 'widgets',
    parent: ['wordpress-theme-elegance/work-block'],
    attributes: {
        imageUrl: {
            type: 'string',
            default: ''
        },
        imageAlt: {
            type: 'string',
            default: ''
        },
        title: {
            type: 'string',
            source: 'html',
            selector: 'h4',
            default: 'Work item title'
        },
        description: {
            type: 'string',
            source: 'html',
            selector: 'p',
            default: 'Some work item description text which describes what the item is all about.'
        },
        fileUrl: {
            type: 'string',
            default: ''
        },
        fileName: {
            type: 'string',
            default: ''
        }
    },
    supports: {
        inserter: false
    },

    edit: (props) => {
        const { attributes: { imageUrl, imageAlt, title, description, fileUrl, fileName }, setAttributes } = props;

        const blockProps = wp.blockEditor.useBlockProps({ className: 'work-item row' });

        const onSelectImage = (media) => {
            setAttributes({
                imageUrl: media.url,
                imageAlt: media.alt,
            });
        };

        const onSelectFile = (media) => {
            setAttributes({
                fileUrl: media.url,
                fileName: media.filename
            });
        };

        return wp.element.createElement('div', { ...blockProps },
            wp.element.createElement('div', { className: 'col-md-4' },
                imageUrl ? wp.element.createElement('img', {
                    src: imageUrl,
                    alt: imageAlt,
                    style: { maxWidth: '100%', maxHeight: '150px', objectFit: 'cover' }
                }) : null,
                wp.element.createElement(wp.blockEditor.MediaUpload, {
                    onSelect: onSelectImage,
                    allowedTypes: ['image'],
                    value: imageUrl,
                    render: ({ open }) => (
                        wp.element.createElement(wp.components.Button, {
                            onClick: open,
                            className: 'button button-large'
                        },
                            imageUrl ? 'Change Image' : 'Select Image'
                        )
                    )
                })
            ),
            wp.element.createElement('div', { className: 'col-md-8' },
                wp.element.createElement(wp.blockEditor.RichText, {
                    tagName: 'h4',
                    value: title,
                    onChange: (newTitle) => setAttributes({ title: newTitle }),
                    placeholder: 'Enter title...'
                }),
                wp.element.createElement(wp.blockEditor.RichText, {
                    tagName: 'p',
                    value: description,
                    onChange: (newDescription) => setAttributes({ description: newDescription }),
                    placeholder: 'Enter description...'
                }),
                wp.element.createElement('div', { className: 'file-upload' },
                    wp.element.createElement(wp.blockEditor.MediaUpload, {
                        onSelect: onSelectFile,
                        allowedTypes: ['application/pdf', 'application/zip', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'], // Add more types if needed
                        value: fileUrl,
                        render: ({ open }) => (
                            wp.element.createElement(wp.components.Button, {
                                onClick: open,
                                className: 'button button-large'
                            },
                                fileUrl ? `File: ${fileName}` : 'Upload File'
                            )
                        )
                    })
                )
            )
        );
    },

    save: (props) => {
        const { attributes: { imageUrl, imageAlt, title, description, fileUrl, fileName } } = props;

        return wp.element.createElement('div', { className: 'item animate', 'data-animate': 'fadeInUp' },
            wp.element.createElement('div', { className: 'work-item' },
                wp.element.createElement('div', { className: 'thumb' },
                    wp.element.createElement('img', { src: imageUrl, alt: imageAlt })
                ),
                wp.element.createElement('div', { className: 'thumb-inner animate', 'data-animate': 'fadeInUp' },
                    wp.element.createElement(wp.blockEditor.RichText.Content, {
                        tagName: 'h4',
                        value: title
                    }),
                    wp.element.createElement(wp.blockEditor.RichText.Content, {
                        tagName: 'p',
                        value: description
                    }),
                    fileUrl && wp.element.createElement('a', {
                        href: fileUrl,
                        className: 'btn btn-dark',
                        download: true
                    }, `Download`)
                )
            )
        );
    }
});
