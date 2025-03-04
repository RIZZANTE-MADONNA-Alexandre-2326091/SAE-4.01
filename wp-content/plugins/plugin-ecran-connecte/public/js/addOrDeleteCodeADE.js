let countRow = 0;

/**
 * Create a new select to add a new group for the alert
 */
function addButton(deptId, information) {
    console.log(countRow);
    countRow = countRow + 1;
    var presenceSupp = false;

    let formId = document.querySelector('form').id;
    console.log(formId);

    let buttonName = document.querySelector('button[type="submit"]').name;
    console.log(buttonName);

    $.ajax({
        url: '/wp-admin/admin-ajax.php',
        type: 'POST',
        data: {
            action: 'get_all_codes',
            deptId: deptId,
            information: information
        }
    }).done(function (data) {
        let div = $('<div >', {
            class: 'row alertEntry',
            id: countRow
        }).appendTo('#' + formId);
        let select = $('<select>', {
            name: 'select[]',
            class: 'form-control firstSelect'
        }).append(data).appendTo(div);
        let button = $('<input>', {
            type: 'button',
            id: countRow,
            onclick: 'deleteRow(this.id)',
            class: 'selectbtn',
            value: 'Retirer'
        }).appendTo(div);

        // Delete buttons from the form.
        let delePlus = document.getElementById("plus");
        delePlus.remove();
        let deleValider = document.getElementById("valider");
        deleValider.remove();
        if(document.getElementById("supprimer")){
            let deleSupprimer = document.getElementById("supprimer");
            deleSupprimer.remove();
            presenceSupp = true;
        }

        // Adding the buttons so that they are at the end of the form.
        let add = $('<input>', {
            type: 'button',
            id: 'plus',
            onclick: 'addButton()',
            class: 'addbtn btn button_ecran',
            value: 'Ajouter'
        }).appendTo('#' + formId);
        let valider = $('<button>', {
            type: 'submit',
            class: 'btn button_ecran',
            id: 'valider',
            name: buttonName,
            text: "Valider"
        }).appendTo('#' + formId);
        if(presenceSupp){
            let supprimer = $('<button>', {
                type: 'submit',
                class: 'btn delete_button_ecran',
                id: 'supprimer',
                name: 'delete',
                onclick: 'return confirm(\' Voulez-vous supprimer cette alerte ?\');',
                text: 'Supprimer'
            }).appendTo('#' + formId);
        }
    });
}

/**
 * Delete the select
 *
 * @param id
 */
function deleteRow(id) {
    let dele = document.getElementById(id);
    dele.remove();
    let dele2 = document.getElementById(id);
    if(dele2 != null) dele2.remove();
}