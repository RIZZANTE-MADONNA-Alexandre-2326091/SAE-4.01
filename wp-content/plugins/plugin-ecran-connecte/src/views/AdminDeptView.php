<?php

namespace Views;

class AdminDeptView extends UserView {

	/**
	 * Displays the form for creating or managing department administrator accounts.
	 *
	 * @return string The HTML content of the form.
	 */
	public function displayFormAdminDept(array $departments, bool $isAdmin = false, int $currentDept = null): string {
		return '
        <h2>Compte Administrateur Département</h2>
        <p class="lead">Pour créer des administrateurs de département, remplissez ce formulaire avec les valeurs demandées.</p>
        ' . $this->displayBaseForm('AdminDept', $departments, $isAdmin, $currentDept);
	}

	/**
	 * Displays the overview of all department administrators with relevant details.
	 *
	 * @param array $users List of user objects representing department administrators.
     * @param array $userDeptList
     *
	 * @return string The HTML content displaying the administrators' information.
	 */
	public function displayAllAdminDept(array $users, array $userDeptList): string {
		$title = 'Admin Département';
		$name = 'AdminDept';
		$header = ['Login', 'Département'];

		$row = array();
		$count = 0;
		foreach ($users as $user) {
            $row[] = [$count+1, $this->buildCheckbox($name, $user->getId()), $user->getLogin(), $userDeptList[$count]];

            ++$count;
		}

		return $this->displayAll($name, $title, $header, $row, $name);
	}

}