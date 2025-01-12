<?php

namespace Views;

use Models\Department;

/**
 * Class DepartmentView
 *
 * @package Views
 */
class DepartmentView extends View
{
	/**
	 * Display a form for adding a department
	 *
	 * @return string
	 */
    public function displayFormDepartment(): string {
        return '
            <form method="post">
                <div class="form-group">
                    <label for="deptName">Nom du département</label>
                    <input class="form-control" type="text" name="deptName" placeholder="280 caractères maximum" required="">
                </div>
                <button type="submit" class="btn button_ecran" id="valid" name="submit">Créer</button>
            </form>';
    }

	/**
	 * Display a form for modifying a department
	 *
	 * @return string
	 */
    public function modifyForm(): string {
        $page = get_page_by_title_V2('Gestion des départements');
        $linkManageDept = get_permalink($page->ID);

        return '
         <a href="' . esc_url(get_permalink($page)) . '">< Retour</a>
         <form method="post">
         	<label for="deptName">Nom du département</label>
            <input class="form-control" type="text" name="deptName" placeholder="280 caractères maximum" required="">
         <button type="submit" class="btn button_ecran" name="modifDept">Modifier</button>
          <a href="'. $linkManageDept .'">Annuler</a>
        </form>';
    }

	/**
	 * Generates and returns the HTML form for deleting a department.
	 *
	 * @return string The HTML string representing the delete department form.
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
	 * Generates and returns the HTML content for displaying all departments with explanations and actions available to the user.
	 *
	 * @return string The HTML string representing the context display for all departments.
	 */
	public function contextDisplayAll(): string{
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
		<a href="' . esc_url(get_permalink(get_page_by_title_V2('Créer un département'))) . '">Créer un département</a>
		<hr class="half-rule">';
	}

	/**
	 * Generates and returns the HTML content displayed when a department is not found.
	 *
	 * @return string The HTML string indicating that the department does not exist, with navigation options.
	 */
	public function noDepartment(): string{
		return '<a href="' . esc_url(get_permalink(get_page_by_title_V2('Gestion des départements'))) . '">< Retour</a>
		<div>
			<h3>Département non trouvé</h3>
			<p>Ce département n\'existe pas</p>
			<a href="' . esc_url(get_permalink(get_page_by_title_V2('Créer un département'))) . '">Créer une département</a>
		</div>';
	}

	/**
	 * Generates and returns an HTML table displaying all departments with options to modify them.
	 *
	 * @param array $departments An array of department objects, each containing details such as ID and name.
	 *
	 * @return string The HTML string representing the table of departments.
	 */
   public function displayAllDept(array $departments): string {
	   $page = get_page_by_title_V2('Modifier un département');
	   $linkManageDept = get_permalink($page->ID);

	    $title = 'Départements';
	    $name = 'Dept';
	    $header = ['Nom', 'Modifier'];
	    $row = [];
	    $count = 1;
	    foreach ($departments as $dept) {
		    $row[] = [ $count,
			    $this->buildCheckbox($name, $dept->getId()),
			    htmlspecialchars($dept->getName()),
			    $this->buildLinkForModify($linkManageDept . '?id=' . $dept->getId())
		    ];
		    ++$count;
		}

	    return $this->displayAll($name, $title, $header, $row);
    }

	/**
	 * Displays a success modal indicating that the department creation was successful.
	 *
	 * @return void
	 */
    public function displayCreationSuccess(): void {
        $this->buildModal('Création réussie', '<div class="alert alert-success">Le département a été créé avec succès !</div>');
    }

	/**
	 * Displays a modal with an error message indicating the failure of department creation.
	 *
	 * @return void
	 */
    public function displayCreationError(): void {
        $this->buildModal('Échec de la création', '<div class="alert alert-danger">Une erreur s\'est produite lors de la création du département. Veuillez réessayer.</div>');
    }

	/**
	 * Displays a modal indicating the success of a deletion operation.
	 *
	 * @return void This method does not return any value.
	 */
    public function displayDeletionSuccess(): void {
        $this->buildModal('Suppression réussie', '<div class="alert alert-success">Le département a été supprimé avec succès.</div>');
    }

	/**
	 * Displays a modal indicating an error occurred during the department deletion process.
	 *
	 * @return void This method does not return any value.
	 */
    public function displayDeletionError(): void {
        $this->buildModal('Échec de la suppression', '<div class="alert alert-danger">Impossible de supprimer le département. Veuillez réessayer.</div>');
    }

	/**
	 * Displays a success modal indicating that the modification has been successfully completed.
	 *
	 * @return void
	 */
	public function displayModificationSucces(): void {
		$this->buildModal('Modification réussie', '<div class="alert alert-danger">Le département a été modifié avec succès.</div>');
	}

	/**
	 * Displays a modal indicating the failure of a department modification.
	 *
	 * @return void
	 */
	public function displayModificationError(): void {
		$this->buildModal('Échec de la modification', '<div class="alert alert-danger">Impossible de modifier le département. Veuillez réessayer.</div>');
	}

	/**
	 * Displays an error message indicating that the department name already exists.
	 *
	 * @return void
	 */
	public function displayErrorDoubleName(): void {
		echo '<p class="alert alert-danger"> Ce nom de département existe déjà</p>';
	}

}