 let count = 0;

/**
 * Create a new select to add a new group for the television
 */
function addButtonTv() {
    console.log(count);
    count = count + 1;

     $.ajax({
         url: '/wp-admin/admin-ajax.php',
         type: 'POST',
         data: {
             action: 'get_all_codes'
         }
     }).done(function (data) {
        let div = $('<div >', {
            class: 'row',
            id: count
        }).appendTo('#registerTvForm');
        let select = $('<select >', {
            id: count,
            name: 'selectTv[]',
            class: 'form-control firstSelect'
        }).append(data).appendTo(div);
        let button = $('<input >', {
            id: count,
            class: 'btn button_ecran',
            type: 'button',
            onclick: 'deleteRow(this.id)',
            value: 'Supprimer'
        }).appendTo(div)

        // Delete buttons from the form.
        document.getElementById("addSchedule").remove();

        document.getElementById("validTv").remove();

        var presenceReturn = false;
        let idReturn;
        if(document.getElementById("linkReturn")){
            idReturn = document.getElementById("linkReturn");
            let deleReturn = idReturn;
            deleReturn.remove();
            presenceReturn = true;
        }

        // Adding the buttons so that they are at the end of the form.
        let add = $('<input>', {
            type: 'button',
            id: 'addSchedule',
            onclick: 'addButtonTv()',
            class: 'btn button_ecran',
            value: 'Ajouter des emplois du temps'
        }).appendTo('#registerTvForm');
        let create = $('<button>', {
            type: 'submit',
            class: 'btn button_ecran',
            id: 'validTv',
            name: 'createTv',
            text: "Créer"
        }).appendTo('#registerTvForm');
        if(presenceReturn) {
            let linkReturn = $('<a>', {
                href: idReturn.getAttribute('href'),
                id: 'linkReturn',
                text: 'Annuler'
            }).appendTo('#registerTvForm')
        }
    });
}

/**
 * Delete the select
 *
 * @param id
 */
function deleteRow(id) {
    document.getElementById(id).remove();

}