<?php

namespace Controllers;

use Models\User;
use Views\AdminDeptView;

class AdminDeptController extends UserController {

	/**
	 * @var User
	 */
	private $model;

	/**
	 * @var AdminDeptView
	 */
	private $view;


	/**
	 * Constructor of SecretaryController.
	 */
	public function __construct(){
		parent::__construct();
		$this->model = new User();
		$this->view = new AdminDeptView();
	}

	/**
	 * Handles the insertion of a new admin department user.
	 *
	 * This method processes the form input for creating a new admin department
	 * user, validates the provided data (login, password, email), checks for
	 * duplicate users, and inserts the new user into the system. Based on the outcome,
	 * it renders appropriate views for success, error, or incomplete data submission.
	 *
	 * @return mixed The rendered view based on the action performed.
	 */
	public function insert(){
		$action = filter_input(INPUT_POST, 'createAdminDept');

		if (isset($action)) {

			$login = filter_input(INPUT_POST, 'loginAdminDept');
			$password = filter_input(INPUT_POST, 'pwdAdminDept');
			$passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmAdminDept');
			$email = filter_input(INPUT_POST, 'emailAdminDept');

			if (is_string($login) && strlen($login) >= 4 && strlen($login) <= 25 &&
			    is_string($password) && strlen($password) >= 8 && strlen($password) <= 25 &&
			    $password === $passwordConfirm && is_email($email)) {

				$this->model->setLogin($login);
				$this->model->setPassword($password);
				$this->model->setEmail($email);
				$this->model->setRole('adminDept');

				if (!$this->checkDuplicateUser($this->model) && $this->model->insert()) {
					$this->view->displayInsertValidate();
				} else {
					$this->view->displayErrorInsertion();
				}
			} else {
				$this->view->displayErrorCreation();
			}
		}
		return $this->view->displayFormAdminDept();
	}

	/**
	 * Retrieves and displays all users with the role of 'adminDept'.
	 *
	 * @return mixed The result of displaying all admin department users, as handled by the view.
	 */
	public function displayAllAdminDept() {
		$users = $this->model->getUsersByRole('adminDept');
		return $this->view->displayAllAdminDept($users);
	}


}