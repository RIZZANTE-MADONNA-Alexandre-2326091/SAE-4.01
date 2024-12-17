<?php

namespace Views;

use Models\Department;
use Utils\PublicationManagement;

/**
 * Class DepartmentView
 *
 * @package Views
 */
class DepartmentView extends View
{
    /**
     * Display a creation form for a department
     *
     * @return string
     */
    public function displayFormDepartment(): string {
        return '
            <form method="post">
                <div class="form-group">
                    <label for="deptName">Nom du département</label>
                    <input class="form-control" type="text" name="deptName" placeholder="Nom du département" required="">
                </div>
                <button type="submit" class="btn button_ecran" id="valid" name="createDept">Créer</button>
            </form>';
    }

	/**
	 * Form for modify a department.
	 *
	 * @return string
	 */
    public function modifyForm(): string {
		$linkManager = new PublicationManagement();

        $page = $linkManager->get_page_by_title('Gestion des départements');
        $linkManageDept = get_permalink($page->ID);

        return '
         <a href="' . esc_url(get_permalink($page)) . '">< Retour</a>
         <form method="post">
         	<label for="deptName">Nom du département</label>
            <input class="form-control" type="text" name="deptName" placeholder="Nom du département" required="">
         <button type="submit" class="btn button_ecran" name="modifDept">Modifier</button>
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
	 * Display all departments.
	 *
	 * @param array $departments
	 *
	 * @return string
	 */
	public function displayAllDept($departments): string {
		$linkManager = new PublicationManagement();

		$page = $linkManager->get_page_by_title('Modifier un département');
		$linkManageDept = get_permalink($page->ID);

		$title = 'Départements de l\'IUT';
		$name = 'Department';
		$header = ['Titre', 'Modifier'];

		$row = array();
		$count = 0;

		foreach ($departments as $department) {
			++$count;
			$row[] = [$count, $this->buildCheckbox($name, $this->buildLinkForModify($linkManageDept . '?id=' . $department->getId()))];
		}

		return $this->displayAll($name, $title, $row, $header);
	}

	/**
	 * @return string
	 */
	public function contextDisplayAll(): string{
		$linkManager = new PublicationManagement();

		return '
		<div class="row">
			<div class="col-6 mx-auto col-md-6 order-md-2">
				<img src="' . TV_PLUG_PATH . 'public/img/info.png" alt="Logo information" class="img-fluid mb-3 mb-md-0">
			</div>
			<div class="col-md-6 order-md-1 text-center text-md-left pr-md-5">
				<p class="lead">Vous pouvez retrouver ici tous les départements qui ont été créés sur ce site.</p>
				<p class="lead">Les départements sont triés de la plus vieille à la plus récente.</p>
				<p class="lead">Vous pouvez modifier un département en cliquant sur "Modifier" à la ligne correspondante au département.</p>
				<p class="lead">Vous souhaitez supprimer un / plusieurs département(s) ? Cochez les cases des départements puis cliquez sur "Supprimer" le bouton se situant en bas du tableau.</p>
			</div>
		</div>
		<a href="' . esc_url(get_permalink($linkManager->get_page_by_title('Créer un département'))) . '">Créer un département</a>
		<hr class="half-rule">';
	}

	/**
	 * @return string
	 */
	public function noDepartment(): string{
		$linkManager = new PublicationManagement();

		return '<a href="' . esc_url(get_permalink($linkManager->get_page_by_title('Gestion des départements'))) . '">< Retour</a>
		<div>
			<h3>Département non trouvé</h3>
			<p>Ce département n\'existe pas</p>
			<a href="' . esc_url(get_permalink($linkManager->get_page_by_title('Créer un département'))) . '">Créer une département</a>
		</div>';
	}

    /**
     * Display a list of departments
     *
     * @param $departments Department[]
     *
     * @return string
     */
    public function displayDepartmentsList($departments): string {
		$linkManager = new PublicationManagement();

	    $page = $linkManager->get_page_by_title('Modifier un département');
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
    public function displayCreationSuccess(): void {
        $this->buildModal('Création réussie', '<div class="alert alert-success">Le département a été créé avec succès !</div>');
    }

    /**
     * Display an error message for department creation failure
     */
    public function displayCreationError(): void {
        $this->buildModal('Échec de la création', '<div class="alert alert-danger">Une erreur s\'est produite lors de la création du département. Veuillez réessayer.</div>');
    }

    /**
     * Display a success message for department deletion
     */
    public function displayDeletionSuccess(): void {
        $this->buildModal('Suppression réussie', '<div class="alert alert-success">Le département a été supprimé avec succès.</div>');
    }

    /**
     * Display an error message for department deletion failure
     */
    public function displayDeletionError(): void {
        $this->buildModal('Échec de la suppression', '<div class="alert alert-danger">Impossible de supprimer le département. Veuillez réessayer.</div>');
    }

	/**
	 * Display an error message for department modification failure
	 **/
	public function displayModificationSucces(): void {
		$this->buildModal('Modification réussie', '<div class="alert alert-danger">Le département a été modifié avec succès.</div>');
	}

	/**
	 * Display an error message for department modification failure
	 **/
	public function displayModificationError(): void {
		$this->buildModal('Échec de la modification', '<div class="alert alert-danger">Impossible de modifier le département. Veuillez réessayer.</div>');
	}

	/**
	 * Error message if name exits
	 */
	public function displayErrorDoubleName(): void {
		echo '<p class="alert alert-danger"> Ce nom de département existe déjà</p>';
	}

	public function set_publication_management( PublicationManagement $publicationManagement ): DepartmentView {
		$this->publicationManagement = $publicationManagement;

		return $this;
	}

}