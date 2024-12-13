<?php

namespace Views;

use Models\Department;

class DepartmentView extends View
{
    /**
     * Display a creation form for a department
     *
     * @return string
     */
    protected function displayFormDepartment(): string {
        return '
            <form method="post">
                <div class="form-group">
                    <label for="deptName">Nom du département</label>
                    <input class="form-control" type="text" name="deptName" placeholder="Nom du département" required="">
                </div>
                <button type="submit" class="btn button_ecran" id="valid" name="create">Créer</button>
            </form>';
    }

	/**
	 * Form for modify a department.
	 *
	 * @return string
	 */
    public function modifyForm(): string {
        $page = get_page_by_title('Gestion des départements');
        $linkManageDept = get_permalink($page->ID);

        return '
         <a href="' . esc_url(get_permalink($page)) . '">< Retour</a>
         <form method="post">
         	<label for="deptName">Nom du département</label>
            <input class="form-control" type="text" name="deptName" placeholder="Nom du département" required="">
         <button type="submit" class="btn button_ecran" name="modifValidate">Modifier</button>
          <a href="'. $linkManageDept .'">Annuler</a>
        </form>';
    }

    /**
     * Display a form for deleting a department
     *
     * @return string
     */
    public function displayDeleteDepartment(): string {
        return '
            <form method="post" id="deleteDepartment">
                <h2>Supprimer un département</h2>
                <label for="deptCode">Code du département</label>
                <input type="text" class="form-control text-center" name="deptCode" placeholder="Code du département" required="">
                <button type="submit" class="btn button_ecran" name="deleteDepartment">Supprimer</button>
            </form>';
    }

    /**
     * Display a list of departments
     *
     * @param $departments Department[]
     *
     * @return string
     */
    public function displayDepartmentsList($departments): string {
	    $page = get_page_by_title('Modifier un dépertement');
	    $linkManageUser = get_permalink($page->ID);

	    $title = 'Département';
	    $name = 'dept';
	    $header = ['Nom du département'];

	    $row = array();
	    $count = 0;
	    foreach ($departments as $dept) {
		    ++$count;
		    $row[] = [$count, $this->buildCheckbox($name, $dept->getId()), $dept->getLogin(), sizeof($dept->getCodes()), $dept->buildLinkForModify($linkManageUser . '?id=' . $dept->getId())];
	    }

	    return $this->displayAll($name, $title, $header, $row);
    }

    /**
     * Display a success message for department creation
     */
    public function displayCreationSuccess() {
        $this->buildModal('Création réussie', '<div class="alert alert-success">Le département a été créé avec succès !</div>');
    }

    /**
     * Display an error message for department creation failure
     */
    public function displayCreationError() {
        $this->buildModal('Échec de la création', '<div class="alert alert-danger">Une erreur s\'est produite lors de la création du département. Veuillez réessayer.</div>');
    }

    /**
     * Display a success message for department deletion
     */
    public function displayDeletionSuccess() {
        $this->buildModal('Suppression réussie', '<div class="alert alert-success">Le département a été supprimé avec succès.</div>');
    }

    /**
     * Display an error message for department deletion failure
     */
    public function displayDeletionError() {
        $this->buildModal('Échec de la suppression', '<div class="alert alert-danger">Impossible de supprimer le département. Veuillez réessayer.</div>');
    }

	/**
	 * Display an error message for department modification failure
	 **/
	public function displayModificationError() {
		$this->buildModal('Échec de la modification', '<div class="alert alert-danger">Impossible de modifier le département. Veuillez réessayer.</div>');
	}


}