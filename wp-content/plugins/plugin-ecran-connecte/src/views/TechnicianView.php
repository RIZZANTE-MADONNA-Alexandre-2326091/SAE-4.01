<?php

namespace Views;


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
    public function displayFormTechnician($departments, $isAdmin, $currentDept): string {
        return '
        <h2>Compte technicien</h2>
        <p class="lead">Pour créer des techniciens, remplissez ce formulaire avec les valeurs demandées.</p>
        ' . $this->displayBaseForm('Tech', $departments, $isAdmin, $currentDept);
    }

	/**
	 * Displays a list of all technicians with their corresponding login information.
	 *
	 * @param array $users An array of user objects, where each object represents a technician and contains relevant data such as ID and login.
	 *
	 * @return string The rendered output of the technicians' data in a formatted display.
	 */
    public function displayAllTechnicians(array $users, $userDeptList): string {
        $title = 'Techniciens';
        $name = 'Tech';
        $header = ['Login','Département'];

        $row = array();
        $count = 0;
        foreach ($users as $user) {
            ++$count;
            $row[] = [$count, $this->buildCheckbox($name, $user->getId()), $user->getLogin(), $userDeptList[$count - 1]];
        }

        return $this->displayAll($name, $title, $header, $row, $name);
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