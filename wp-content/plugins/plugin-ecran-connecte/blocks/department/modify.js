/**
 * Build the block
 */
(function( blocks, element, data)
{
    var el = element.createElement;

    blocks.registerBlockType('tvconnecteeamu/modify-department', {
        title: 'Modifier le département',
        icon: 'smiley',
        category: 'common',

        edit: function() {
            return "Modifie le département sélectionné";
        },
        save: function() {
            return "test";
        },
    });
}(
    window.wp.blocks,
    window.wp.element,
    window.wp.data,
));