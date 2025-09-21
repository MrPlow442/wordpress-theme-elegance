const { registerPlugin } = wp.plugins;
const { PluginDocumentSettingPanel } = wp.editPost;
const { TextControl } = wp.components;
const { withSelect, withDispatch } = wp.data;
const { compose } = wp.compose;
const { __ } = wp.i18n;

const TestimonialMetaFields = compose(
    withSelect((select) => {
        const { getEditedPostAttribute, getCurrentPostType } = select('core/editor');
        const postMeta = getEditedPostAttribute('meta') || {};
        const postType = getCurrentPostType();
        return {
            postMeta: {
                client_url: postMeta.client_url || '',
                link_text: postMeta.link_text || '',
                ...postMeta
            },
            postType: postType
        };
    }),
    withDispatch((dispatch) => {
        const { editPost } = dispatch('core/editor');
        return {
            setPostMeta(newMeta) {                
                editPost({ meta: newMeta });
            }
        }
    })
)(({ postType, postMeta, setPostMeta }) => {    
    if ('testimonial' !== postType) {
        return null;
    }

    return (
        <PluginDocumentSettingPanel
            name="testimonial-client-link"
            title={__('Client Website', 'wordpress-theme-elegance')}
            className="testimonial-client-link-panel"
            icon="admin-links"
        >
            <TextControl
                label={__('Client Website URL', 'wordpress-theme-elegance')}
                value={postMeta.client_url}
                onChange={(value) => setPostMeta({ client_url: value })}
                placeholder="https://example.com"
                help={__('Optional: Enter the client\'s website URL', 'wordpress-theme-elegance')}
                type='url'
            />
            <TextControl
                label={__('Link Text', 'wordpress-theme-elegance')}
                value={postMeta.link_text}
                onChange={(value) => setPostMeta({ link_text: value })}
                placeholder={__('Visit Website', 'wordpress-theme-elegance')}
                help={__('Optional: Enter the link text', 'wordpress-theme-elegance')}
            />
        </PluginDocumentSettingPanel>
    );
});

registerPlugin('testimonial-client-link', {
    render: TestimonialMetaFields
});
