<?php

namespace Views;

class AdminDeptView extends UserView {

	public function displayFormAdminDept($departments, $isAdmin = false, $currentDept = null) {
		return '
        <h2>Compte Administrateur Département</h2>
        <p class="lead">Pour créer des administrateurs de département, remplissez ce formulaire avec les valeurs demandées.</p>
        ' . $this->displayBaseForm('AdminDept', $departments, $isAdmin, $currentDept);
	}

	public function displayAllAdminDept($users, $userDeptList) {
		$title = 'Admin Département';
		$name = 'AdminDept';
		$header = ['Login', 'Département'];

		$row = array();
		$count = 0;
		foreach ($users as $user) {
			++$count;
			$row[] = [$count, $this->buildCheckbox($name, $user->getId()), $user->getLogin(), $userDeptList[$count - 1]];
		}

		return $this->displayAll($name, $title, $header, $row, $name);
	}

}