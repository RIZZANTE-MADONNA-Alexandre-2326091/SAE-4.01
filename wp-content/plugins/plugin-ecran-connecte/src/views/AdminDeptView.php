<?php

namespace Views;

class AdminDeptView extends UserView {

	public function displayFormAdminDept() {
		return '
        <h2>Compte Administrateur Département</h2>
        <p class="lead">Pour créer des administrateurs de département, remplissez ce formulaire avec les valeurs demandées.</p>
        ' . $this->displayBaseForm('AdminDept');
	}

	public function displayAllAdminDept($users) {
		$title = 'Admin Département';
		$name = 'AdminDept';
		$header = ['Login'];

		$row = array();
		$count = 0;
		foreach ($users as $user) {
			++$count;
			$row[] = [$count, $this->buildCheckbox($name, $user->getId()), $user->getLogin()];
		}

		return $this->displayAll($name, $title, $header, $row, $name);
	}

}