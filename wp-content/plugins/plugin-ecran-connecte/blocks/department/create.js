/**
 * Build the block
 */
(function(blocks, element, data)
{
    var el = element.createElement;

    wp.blocks.registerBlockType('tvconnecteeamu/add-department', {
        title: 'Créer un département',
        icon: 'smiley',
        category: 'common',

        edit: function() {
            return "Ajoute un département via un formulaire";
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