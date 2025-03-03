<?php

namespace Views;


use Models\CodeAde;
use Models\User;

/**
 * Class TechnicianView
 *
 * Contain all view for technician (Forms, tables)
 *
 * @package Views
 */
class TechnicianView extends UserView
{

	/**
	 * Displays the form for creating technician accounts.
	 *
	 * @return string The rendered output of the technician account creation form with a title, description, and base form elements.
	 */
    public function displayFormTechnician(array $years,array $groups, array $halfGroups): string {
        $form = '
        <h2>Compte technicien</h2>
        <p class="lead">Pour créer des techniciens, remplissez ce formulaire avec les valeurs demandées.</p>
        ' . $this->displayBaseForm('Tech');'
        <p class="lead">Vous pouvez mettre autant d\'emploi du temps que vous souhaitez, cliquez sur "Ajouter des emplois du temps</p>
            <label>Premier emploi du temps</label>' .
            $this->buildSelectCode($years, $groups, $halfGroups) . '
            </div>
            <input type="button" class="btn button_ecran" id="addSchedule" onclick="addButtonTv()" value="Ajouter des emplois du temps">
            <button type="submit" class="btn button_ecran" id="validTv" name="createTv">Créer</button>
        </form>';

        return $form;
    }


        /**
	 * Displays a list of all technicians with their corresponding login information.
	 *
	 * @param array $users An array of user objects, where each object represents a technician and contains relevant data such as ID and login.
	 *
	 * @return string The rendered output of the technicians' data in a formatted display.
	 */
    public function displayAllTechnicians(array $users): string {
        $title = 'Techniciens';
        $name = 'Tech';
        $header = ['Login', 'Nombre d\'emplois du temps ', 'Modifier'];

        $row = array();
        $count = 0;
        foreach ($users as $user) {
            ++$count;
            $row[] = [$count, $this->buildCheckbox($name, $user->getId()), $user->getLogin()];
        }

        return $this->displayAll($name, $title, $header, $row, $name);
    }

    /**
     * Builds an HTML select element with options for years, groups, and half-groups.
     *
     * This method dynamically generates a dropdown menu containing categorized groups of options,
     * including options for years, groups, and half-groups. An initial option tied to an existing
     * CodeAde object can be included if provided.
     *
     * @param array $years An array of objects representing years. Each object must have methods getCode() and getTitle() to retrieve the year code and title respectively.
     * @param array $groups An array of objects representing groups. Each object must have methods getCode() and getTitle() to retrieve the group code and title respectively.
     * @param array $halfGroups An array of objects representing half-groups. Each object must have methods getCode() and getTitle() to retrieve the half-group code and title respectively.
     * @param CodeAde|null $code An optional object representing a preselected code. The object must provide getCode() and getTitle() methods. If null, no predefined option is selected.
     * @param int $count A unique identifier used to differentiate the generated select element, typically in its ID attribute. Default is 0.
     *
     * @return string The generated HTML string for the select element, including all options and optgroup elements.
     */
    public function buildSelectCode(array $years, array $groups, array $halfGroups, CodeAde $code = null, int $count = 0): string {
        $select = '<select class="form-control firstSelect" id="selectId' . $count . '" name="selectTechnician[]" required="">';

        if (!is_null($code)) {
            $select .= '<option value="' . $code->getCode() . '">' . $code->getTitle() . '</option>';
        }

        $select .= '<option value="0">Aucun</option>
                <optgroup label="Année">';

        foreach ($years as $year) {
            $select .= '<option value="' . $year->getCode() . '">' . $year->getTitle() . '</option>';
        }
        $select .= '</optgroup><optgroup label="Groupe">';

        foreach ($groups as $group) {
            $select .= '<option value="' . $group->getCode() . '">' . $group->getTitle() . '</option>';
        }
        $select .= '</optgroup><optgroup label="Demi groupe">';

        foreach ($halfGroups as $halfGroup) {
            $select .= '<option value="' . $halfGroup->getCode() . '">' . $halfGroup->getTitle() . '</option>';
        }
        $select .= '</optgroup>
        </select>';

        return $select;
    }

	/**
	 * Display the starting container for a slideshow
	 *
	 * @return string
	 */
	public function displayStartSlide():string {
		return '<div id="slideshow-container" class="slideshow-container">';
	}

	/**
	 * Display the mid-slide HTML content
	 *
	 * @return string
	 */
	public function displayMidSlide(): string {
		return '<div class="mySlides">';
	}
}