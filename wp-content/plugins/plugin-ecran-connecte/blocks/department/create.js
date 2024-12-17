/**
 * Build the block
 */
(function(blocks, element, data)
{
    var el = element.createElement;

    blocks.registerBlockType('tvconnecteeamu/add-department', {
        title: 'Ajout département',
        icon: 'smiley',
        category: 'common',

        edit: function() {
            return "Ajoute un département via un formulaire";
        },
        save: function() {
            return "yo";
        },
    });
}(
    window.wp.blocks,
    window.wp.element,
    window.wp.data,
));