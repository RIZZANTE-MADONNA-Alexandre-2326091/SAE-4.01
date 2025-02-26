<?php

namespace Views;

use Models\CodeAde;
use Models\Department;

/**
 * Class CodeAdeView
 *
 * All view for code ade (Forms, table, messages)
 *
 * @package Views
 */
class CodeAdeView extends View
{

	/**
	 * Generate and return an HTML form for adding a code ADE.
	 *
	 * @return string The HTML form as a string.
	 */
    public function createForm(array $departments, bool $isAdmin = false, int $currentDept = null): string {
        $disabled = $isAdmin ? '' : 'disabled';

        return '
        <form method="post">
            <div class="form-group">
                <label for="title">Titre</label>
                <input class="form-control" type="text" id="title" name="title" placeholder="Titre" required="" minlength="5" maxlength="29">
            </div>
            <div class="form-group">
                <label for="code">Code ADE</label>
                <input class="form-control" type="text" id="code" name="code" placeholder="Code ADE" required="" maxlength="19" pattern="\d+">
            </div>
            <div class="form-group">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="type" id="year" value="year">
                    <label class="form-check-label" for="year">Année</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="type" id="group" value="group">
                    <label class="form-check-label" for="group">Groupe</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="type" id="halfGroup" value="halfGroup">
                    <label class="form-check-label" for="halfGroup">Demi-groupe</label>
                </div>
            </div>
            <div class="form-group">
                <label for="department">Département</label>
                <select name="dept" class="form-control" ' . $disabled . '>
                		'. $this->displayAllDept($departments, $currentDept) .'
                	</select>
            </div>
          <button type="submit" class="btn button_ecran" name="submit">Ajouter</button>
        </form>';
    }

	/**
	 * Displays a form to modify an ADE code.
	 *
	 * @param string $title The current title of the ADE code.
	 * @param string $type The current type associated with the ADE code.
	 * @param int $code The current code to be modified.
	 *
	 * @return string The HTML form as a string for modifying the ADE code.
	 */
    public function displayModifyCode(string $title, string $type, int $code): string {

        $page = get_page_by_title_V2('Gestion des codes ADE');
        $linkManageCode = get_permalink($page->ID);

        return '
        <a href="' . esc_url(get_permalink(get_page_by_title_V2('Gestion des codes ADE'))) . '">< Retour</a>
         <form method="post">
         	<div class="form-group">
            	<label for="title">Titre</label>
            	<input class="form-control" type="text" id="title" name="title" placeholder="Titre" value="' . $title . '">
            </div>
            <div class="form-group">
            	<label for="code">Code</label>
            	<input type="text" class="form-control" id="code" name="code" placeholder="Code" value="' . $code . '">
            </div>
            <div class="form-group">
            	<label for="type">Selectionner un type</label>
             	<select class="form-control" id="type" name="type">
                    ' . $this->createTypeOption($type) . '
                </select>
            </div>
            <button type="submit" class="btn button_ecran" name="submit">Modifier</button>
            <a href="' . $linkManageCode . '">Annuler</a>
         </form>';
    }

	/**
	 * Generate a list of HTML option elements for selecting a type.
	 *
	 * @param string $selectedType The value of the currently selected type.
	 *
	 * @return string The HTML string containing all option elements.
	 */
    private function createTypeOption(string $selectedType): string {
        $result = '';

        // Declare available code types
        $types = array(
            array(
                'value' => 'year',
                'title' => 'Année',
            ),
            array(
                'value' => 'group',
                'title' => 'Groupe',
            ),
            array(
                'value' => 'halfGroup',
                'title' => 'Demi-Groupe',
            ),
        );

        // Build option list
        foreach ($types as $type) {
            $result .= '<option value="' . $type['value'] . '"';

            if ($selectedType === $type['value'])
                $result .= ' selected';

            $result .= '>' . $type['title'] . '</option>' . PHP_EOL;
        }

        return $result;
    }

	/**
	 * Displays a list of all ADE codes including years, groups, and half-groups.
	 *
	 * @param array $years An array of ADE codes categorized as years.
	 * @param array $groups An array of ADE codes categorized as groups.
	 * @param array $halfGroups An array of ADE codes categorized as half-groups.
	 *
	 * @return string The rendered HTML output displaying all the ADE codes.
	 */
    public function displayAllCode(array $years,array $groups,array $halfGroups): string {
        $page = get_page_by_title_V2('Modifier un code ADE');
        $linkManageCodeAde = get_permalink($page->ID);

        $title = 'Codes Ade';
        $name = 'Code';
        $header = ['Titre', 'Code', 'Type', 'Département', 'Modifier'];

        $codesAde = [$years, $groups, $halfGroups];

        $row = array();
        $count = 0;

        $deptModel = new Department();

        foreach ($codesAde as $codeAde) {
            foreach ($codeAde as $code) {
                if ($code->getType() === 'year') {
                    $code->setType('Année');
                } else if ($code->getType() === 'group') {
                    $code->setType('Groupe');
                } else if ($code->getType() === 'halfGroup') {
                    $code->setType('Demi-groupe');
                }
                ++$count;
                $row[] = [$count, $this->buildCheckbox($name, $code->getId()), $code->getTitle(), $code->getCode(), $code->getType(), $deptModel->get($code->getDeptId())->getName() , $this->buildLinkForModify($linkManageCodeAde . '?id=' . $code->getId())];
            }
        }

        return $this->displayAll($name, $title, $header, $row, 'code');
    }

	/**
	 * Display a success message for the creation of a code ADE.
	 *
	 * @return void
	 */
    public function successCreation(): void {
        $this->buildModal('Ajout du code ADE', '<p>Le code ADE a bien été ajouté</p>');
    }

	/**
	 * Displays a success modal for ADE code modification.
	 *
	 * @return void
	 */
    public function successModification(): void {
        $page = get_page_by_title_V2('Gestion des codes ADE');
        $linkManageCode = get_permalink($page->ID);
        $this->buildModal('Modification du code ADE', '<p>Le code ADE a bien été modifié</p>', $linkManageCode);
    }

	/**
	 * Displays an error modal for ADE code creation failure.
	 *
	 * @return void
	 */
    public function errorCreation(): void {
        $this->buildModal('Erreur lors de l\'ajout du code ADE', '<p>Le code ADE a rencontré une erreur lors de son ajout</p>');
    }

	/**
	 * Displays an error modal for ADE code modification failure.
	 *
	 * @return void
	 */
    public function errorModification(): void {
        $this->buildModal('Erreur lors de la modification du code ADE', '<p>Le code ADE a rencontré une erreur lors de sa modification</p>');
    }

	/**
	 * Displays an error message indicating that a code or title already exists.
	 *
	 * @return void
	 */
    public function displayErrorDoubleCode(): void {
        echo '<p class="alert alert-danger"> Ce code ou ce titre existe déjà</p>';
    }

	/**
	 * Displays an error message indicating no content is available and provides a link to return.
	 *
	 * @return void
	 */
    public function errorNobody(): void {
        $page = get_page_by_title_V2('Gestion des codes ADE');
        $linkManageCode = get_permalink($page->ID);
        echo '<p>Il n\'y a rien par ici</p><a href="' . $linkManageCode . '">Retour</a>';
    }
}
