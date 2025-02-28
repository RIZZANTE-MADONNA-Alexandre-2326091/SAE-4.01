let countRow = 0;

/**
 * Create a new select to add a new group for the alert
 */
function addButtonAlert(deptId) {
    console.log(countRow);
    countRow = countRow + 1;
    var presenceSupp = false;

    console.log(deptId);

    $.ajax({
        url: '/wp-admin/admin-ajax.php',
        type: 'POST',
        data: {
            action: 'get_all_codes',
            deptId: deptId
        }
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
        if(document.getElementById("supprimer")){
            let deleSupprimer = document.getElementById("supprimer");
            deleSupprimer.remove();
            presenceSupp = true;
        }

        // Adding the buttons so that they are at the end of the form.

        let add = $('<input>', {
            type: 'button',
            id: 'plus',
            onclick: 'addButtonAlert()',
            class: 'btn button_ecran',
            value: '+'
        }).appendTo('#alert');
        let valider = $('<button>', {
            type: 'submit',
            class: 'btn button_ecran',
            id: 'valider',
            name: 'submit',
            text: "Valider"
        }).appendTo('#alert');
        if(presenceSupp){
            let supprimer = $('<button>', {
                type: 'submit',
                class: 'btn delete_button_ecran',
                id: 'supprimer',
                name: 'delete',
                onclick: 'return confirm(\' Voulez-vous supprimer cette alerte ?\');',
                text: 'Supprimer'
            }).appendTo('#alert');
        }
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