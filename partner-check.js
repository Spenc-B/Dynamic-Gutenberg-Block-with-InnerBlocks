document.addEventListener("DOMContentLoaded", function() {

    wp.blocks.registerBlockType('custom/partner-check', {
    title: 'Partner Check',
    icon: 'lock',
    category: 'layout',

    attributes: {
        role: { type: 'string', default: 'partner,administrator' },
        tier: { type: 'string', default: '' }
    },

    edit: function(props) {
        return wp.element.createElement(
            'div',
            { style: { border: '2px dashed #007cba', padding: '15px' } },
            wp.element.createElement('p', {}, 'Partner Protected Content'),
            wp.blockEditor.InnerBlocks
                ? wp.element.createElement(wp.blockEditor.InnerBlocks)
                : wp.element.createElement(wp.editor.InnerBlocks)
        );
    },

    save: function() {
            return wp.element.createElement(
                wp.blockEditor.InnerBlocks.Content
                    ? wp.blockEditor.InnerBlocks.Content
                    : wp.editor.InnerBlocks.Content
            );
        }
    });


});