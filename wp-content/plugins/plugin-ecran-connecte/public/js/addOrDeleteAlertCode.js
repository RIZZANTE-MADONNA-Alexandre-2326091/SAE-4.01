let countRow = 0;

/**
 * Create a new select to add a new group for the alert
 */
function addButtonAlert() {
    console.log(countRow);
    countRow = countRow + 1;

    $.ajax({
        url: '/wp-content/plugins/plugin-ecran-connecte/public/js/utils/allCodes.php',
    }).done(function (data) {
        let div = $('<div >', {
            class: 'row',
            id: countRow
        }).appendTo('#alert');
        let select = $('<select >', {
            name: 'selectAlert[]',
            class: 'form-control firstSelect'
        }).append(data).appendTo(div);
        let button = $('<input >', {
            type: 'button',
            id: countRow,
            onclick: 'deleteRowAlert(this.id)',
            class: 'selectbtn',
            value: 'Supprimer'
        }).appendTo(div);

        // Delete buttons from the form.
        let delePlus = document.getElementById("plus");
        delePlus.remove();
        let deleValider = document.getElementById("valider");
        deleValider.remove();
        let deleSupprimer = document.getElementById("supprimer");
        deleSupprimer.remove();

        // Adding the buttons so that they are at the end of the form.

        let add = $('<input>', {
            type: 'button',
            id: 'plus',
            onclick: 'addButtonAlert()',
            value: '+'
        }).appendTo('#alert');
        let valider = $('<button>', {
            type: 'submit',
            class: 'btn button_ecran',
            id: 'valider',
            name: 'submit',
            text: "Valider"
        }).appendTo('#alert');
        let supprimer = $('<button>', {
            type: 'submit',
            class: 'btn delete_button_ecran',
            id: 'supprimer',
            name: 'delete',
            onclick: 'return confirm(\' Voulez-vous supprimer cette alerte ?\');',
            text: 'Supprimer'
        }).appendTo('#alert');
    });
}

/**
 * Delete the select
 *
 * @param id
 */
function deleteRowAlert(id) {
    let dele = document.getElementById(id);
    dele.remove();
    let dele2 = document.getElementById(id);
    if(dele2 != null) dele2.remove();
}