<?php

namespace Views;

/**
 * Class View
 *
 * Main class View,
 * got basics functions for all views
 *
 * @package Views
 */
class View
{

	/**
	 * Generates and displays an HTML table with a search functionality and sorting options.
	 *
	 * @param string $name The name of the table, used for toggling checkboxes.
	 * @param string $title The title displayed above the table.
	 * @param array $dataHeader An array of column headers for the table.
	 * @param array $dataList A two-dimensional array containing the data to be displayed in the table.
	 * @param string $idTable An optional table ID for DOM manipulation (default is an empty string).
	 *
	 * @return string The HTML structure of the table.
	 */
    public function displayAll(string $name, string $title, array $dataHeader, array $dataList, string $idTable = ''): string {
        $name = '\'' . $name . '\'';
        $table = '
		<h2>' . $title . '</h2>
		<input type="text" id="key' . $idTable . '" name="key" onkeyup="search(\'' . $idTable . '\')" placeholder="Recherche...">
		<form method="post">
			<div class="table-responsive">
				<table class="table table-striped table-hover" id="table' . $idTable . '">
					<thead>
						<tr class="text-center">
							<th width="5%" class="text-center" onclick="sortTable(0, \'' . $idTable . '\')">#</th>
		                    <th scope="col" width="5%" class="text-center"><input type="checkbox" onClick="toggle(this, ' . $name . ')" /></th>';
        $count = 1;
        foreach ($dataHeader as $data) {
            ++$count;
            $table .= '<th scope="col" class="text-center" onclick="sortTable(' . $count . ', \'' . $idTable . '\')">' . $data . '</th>';
        }
        $table .= '
			</tr>
		</thead>
		<tbody>';
        foreach ($dataList as $data) {
            $table .= '<tr>';
            foreach ($data as $column) {
                $table .= '<td class="text-center">' . $column . '</td>';
            }
            $table .= '</tr>';
        }
        $table .= '
					</tbody>
				</table>
			</div>
	        <button type="submit" class="btn delete_button_ecran" value="Supprimer" name="delete" onclick="return confirm(\' Voulez-vous supprimer le(s) élément(s) sélectionné(s) ?\');">Supprimer</button>
	    </form>';
        return $table;
    }

	/**
	 * Generate a pagination component based on the current page, total pages, and other parameters.
	 *
	 * @param int $pageNumber The total number of pages available.
	 * @param int $currentPage The current active page in the pagination.
	 * @param string $url The base URL for the pagination links.
	 * @param mixed|null $numberElement An optional parameter to append as a query parameter in the pagination links.
	 *
	 * @return string The generated HTML string for the pagination component.
	 */
	public function pageNumber(int $pageNumber, int $currentPage, string $url, mixed $numberElement = null): string {
        $pagination = '
        <nav aria-label="Page navigation example">
            <ul class="pagination">';

        if ($currentPage > 1) {
            $pagination .= '
            <li class="page-item">
              <a class="page-link" href="' . $url . '/' . ($currentPage - 1) . '/?number=' . $numberElement . '" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
              </a>
            </li>
            <li class="page-item"><a class="page-link" href="' . $url . '/1/?number=' . $numberElement . '">1</a></li>';
        }
        if ($currentPage > 3) {
            $pagination .= '<li class="page-item page-link disabled">...</li>';
        }
        for ($i = $currentPage - 3; $i < $currentPage; ++$i) {
            if ($i > 1) {
                $pagination .= '<li class="page-item"><a class="page-link" href="' . $url . $i . '/?number=' . $numberElement . '">' . $i . '</a></li>';
            }
        }
        $pagination .= '
        <li class="page-item active_ecran" aria-current="page">
          <a class="page-link" href="' . $url . $currentPage . '/?number=' . $numberElement . '">' . $currentPage . '<span class="sr-only">(current)</span></a>
        </li>';
        for ($i = $currentPage + 1; $i < $currentPage + 3; ++$i) {
            if ($i < $pageNumber) {
                $pagination .= '<li class="page-item"><a class="page-link" href="' . $url . '/' . $i . '/?number=' . $numberElement . '">' . $i . '</a></li>';
            }
        }
        if ($currentPage < $pageNumber) {
            if ($pageNumber - $currentPage > 3) {
                $pagination .= '<li class="page-item page-link disabled">...</li>';
            }
            $pagination .= '
            <li class="page-item"><a class="page-link" href="' . $url . '/' . $pageNumber . '/?number=' . $numberElement . '">' . $pageNumber . '</a></li>
            <li class="page-item">
              <a class="page-link" href="' . $url . '/' . ($currentPage + 1) . '/?number=' . $numberElement . '" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
              </a>
            </li>';
        }
        $pagination .= '
          </ul>
        </nav>';
        return $pagination;
    }

    /**
     * Create a link for modify an element
     *
     * @param $link
     *
     * @return string
     */
    public function buildLinkForModify($link) {
        return '<a href="' . $link . '">Modifier</a>';
    }

    /**
     * Create a checkbox
     *
     * @param $name
     * @param $id
     *
     * @return string
     */
    public function buildCheckbox($name, $id) {
        return '<input type="checkbox" name="checkboxStatus' . $name . '[]" value="' . $id . '"/>';
    }

    /**
     * Create the begin of a multi select
     *
     * @return string
     */
    public function displayStartMultiSelect() {
        return '<nav>
          <div class="nav nav-tabs" id="nav-tab" role="tablist">';
    }

    /**
     * Create one tab for the multi select
     *
     * @param $id           string id de l'onglet
     * @param $title        string titre de l'onglet
     * @param $active       bool affiche l'onglet (si c'est à true) lors du chargement de la page
     * @return string
     */
    public function displayTitleSelect($id, $title, $active = false) {
        $string = '<a class="nav-item nav-link';
        if ($active) $string .= ' active';
        $string .= '" id="nav-' . $id . '-tab" data-toggle="tab" href="#nav-' . $id . '" role="tab" aria-controls="nav-' . $id . '" aria-selected="false">' . $title . '</a>';
        return $string;
    }

    /**
     * Close the creation of new tab
     *
     * @return string
     */
    public function displayEndOfTitle() {
        return '
            </div>
        </nav>
        <br/>
        <div class="tab-content" id="nav-tabContent">';
    }

    /**
     * Create the content for one tab
     *
     * @param $id           string
     * @param $content      string
     * @param $active       bool
     *
     * @return string
     */
    public function displayContentSelect($id, $content, $active = false) {
        $string = '<div class="tab-pane fade show';
        if ($active) $string .= ' active';
        $string .= '" id="nav-' . $id . '" role="tabpanel" aria-labelledby="nav-' . $id . '-tab">' . $content . '</div>';
        return $string;
    }

    /**
     * Refresh the page
     */
    public function refreshPage() {
        echo '<meta http-equiv="refresh" content="0">';
    }

    /**
     * @param $title
     * @param $content
     * @param null $redirect
     */
    public function buildModal($title, $content, $redirect = null) {
        $modal = '
		<!-- MODAL -->
		<div class="modal" id="myModal" tabindex="-1" role="dialog">
		  <div class="modal-dialog modal-dialog-centered" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title" id="exampleModalLabel">' . $title . '</h5>
		      </div>
		      <div class="modal-body">
		        ' . $content . '
		      </div>
		      <div class="modal-footer">';
        if (empty($redirect)) {
            $modal .= '<button type="button" class="btn button_ecran" onclick="$(\'#myModal\').hide();">Fermer</button>';
        } else {
            $modal .= '<button type="button" class="btn button_ecran" onclick="document.location.href =\' ' . $redirect . ' \'">Fermer</button>';
        }
        $modal .= '</div>
		    </div>
		  </div>
		</div>
		
		<script>
			$(\'#myModal\').show();
		</script>';

        echo $modal;
    }

    /**
     * Returns the closing div tag as a string.
     *
     * @return string The closing div tag.
     */
    public function displayEndDiv(): string {
        return '</div>';
    }


    public function displayAllDept(array $departments, int $currentDept = null): string
    {
        $form = '';
        foreach ($departments as $dept) {
            $selected = ($dept->getId() == $currentDept) ? 'selected' : '';
            $form .= '<option' . $selected . ' value="' . $dept->getId() . '">' . $dept->getName() . '</option>';
        }
        return $form;
    }

    /**
     * Close a div.
     *
     * @return string
     */
    public function endDiv(): string {
        return '</div>';
    }

	/**
	 * Display a message indicating that the passwords are incorrect.
	 *
	 * @return void
     */
    public function displayBadPassword(): void
    {
        $this->buildModal('Mauvais mot de passe', '<p class=\'alert alert-danger\'>Les deux mots de passe ne sont pas correctes </p>');
    }

	/**
	 * Display an error message for duplicate entries during registration.
	 *
	 * @param array $doubles An array of entries that caused a problem during registration, typically due to duplicate login or email.
	 *
	 * @return void
	 */
    public function displayErrorDouble(array $doubles): void {
        $content = "";
        foreach ($doubles as $double) {
            $content .= '<p class="alert alert-danger">' . $double . ' a rencontré un problème lors de l\'enregistrement, veuillez vérifiez son login et son email !</p>';
        }
        $this->buildModal('Erreur durant l\'inscription', $content);
    }

	/**
	 * Display a message indicating successful registration.
	 *
	 * @return void
	 */
    public function displayInsertValidate(): void {
        $this->buildModal('Inscription validée', '<p class=\'alert alert-success\'>Votre inscription a été validée.</p>');
    }

	/**
	 * Display a message indicating that the file has a wrong extension.
	 *
	 * @return void
	 */
    public function displayWrongExtension(): void {
        $this->buildModal('Mauvais fichier !', '<p class="alert alert-danger"> Mauvaise extension de fichier !</p>');
    }

	/**
	 * Displays a modal indicating that the user has uploaded the wrong file
	 * or changed the column names in the Excel file.
	 *
	 * @return void
	 */
    public function displayWrongFile(): void {
        $this->buildModal('Mauvais fichier !', '<p class="alert alert-danger"> Vous utilisez un mauvais fichier excel / ou vous avez changé le nom des colonnes</p>');
    }

	/**
	 * Displays a modal indicating that the modification has been successfully applied.
	 *
	 * @param string|null $redirect An optional URL to redirect to after applying the modification.
	 *
	 * @return void
	 */
    public function displayModificationValidate($redirect = null) {
        $this->buildModal('Modification réussie', '<p class="alert alert-success"> La modification a été appliquée</p>', $redirect);
    }

	/**
	 * Displays a modal indicating an error during the registration process,
	 * such as the login or email address already being in use.
	 *
	 * @return void
	 */
    public function displayErrorInsertion(): void {
        $this->buildModal('Erreur lors de l\'inscription', '<p class="alert alert-danger"> Le login ou l\'adresse mail est déjà utilisé(e) </p>');
    }

	/**
	 * Displays a modal indicating that the form has not been correctly completed
	 * and prompts the user to review the entered data and try again.
	 *
	 * @return void
	 */
	public function errorMessageInvalidForm(): void {
        $this->buildModal('Le formulaire n\'a pas été correctement remplie', '<p class="alert alert-danger">Le formulaire a été mal remplie, veuillez revoir les données rentrées et réessayez.</p>');
    }

    public function errorMessageCantAdd() {
        $this->buildModal('L\'ajout a échoué', '<p class="alert alert-danger">Une erreur s\'est produite lors de l\'envoie du formulaire, veuillez réessayer après avoir vérifié vos informations.</p>');
    }
}
