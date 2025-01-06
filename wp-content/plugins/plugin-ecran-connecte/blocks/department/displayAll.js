/**
 * Build the block
 */
(function( blocks, element, data)
{
    var el = element.createElement;

    wp.blocks.registerBlockType('tvconnecteeamu/manage-department', {
        title: 'Affiche les départements',
        icon: 'smiley',
        category: 'common',

        edit: function() {
            return "Affiche tous les départements";
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