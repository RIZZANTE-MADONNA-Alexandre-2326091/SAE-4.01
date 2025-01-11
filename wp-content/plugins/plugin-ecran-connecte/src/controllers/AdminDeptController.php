<?php

namespace Controllers;

use Models\Department;
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

		$current_user = wp_get_current_user();

		$deptModel = new Department();
		$isAdmin = in_array('administrator', $current_user->roles);
		$currentDept = $isAdmin ? null : $deptModel->getDepartmentUsers($current_user->ID)->getId();
		$departments = $deptModel->getAll();

		if (isset($action)) {

			$login = filter_input(INPUT_POST, 'loginAdminDept');
			$password = filter_input(INPUT_POST, 'pwdAdminDept');
			$passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmAdminDept');
			$email = filter_input(INPUT_POST, 'emailAdminDept');
			$deptId = $isAdmin ? filter_input(INPUT_POST, 'dept') : $currentDept;

			if (is_string($login) && strlen($login) >= 4 && strlen($login) <= 25 &&
			    is_string($password) && strlen($password) >= 8 && strlen($password) <= 25 &&
			    $password === $passwordConfirm && is_email($email)) {

				$this->model->setLogin($login);
				$this->model->setPassword($password);
				$this->model->setEmail($email);
				$this->model->setRole('adminDept');
				$this->model->setDeptId($deptId);

				if (!$this->checkDuplicateUser($this->model) && !$this->checkDuplicateIdDept($this->model) && $this->model->insert()) {
					$this->view->displayInsertValidate();
				} else {
					$this->view->displayErrorInsertion();
				}
			} else {
				$this->view->displayErrorCreation();
			}
		}
		return $this->view->displayFormAdminDept($departments, $isAdmin, $currentDept);
	}

	/**
	 * Retrieves and displays all users with the role of 'adminDept'.
	 *
	 * @return mixed The result of displaying all admin department users, as handled by the view.
	 */
	public function displayAllAdminDept() {
		$users = $this->model->getUsersByRole('adminDept');

		$deptModel = new Department();
		$userDeptList = array();
		foreach ($users as $user) {
			$userDeptList[] = $deptModel->getUserInDept($user->getId())->getName();
		}

		return $this->view->displayAllAdminDept($users, $userDeptList);
	}


	public function checkDuplicateIdDept(User $newUser) {
		$dept = $this->model->getDeptAdmin($newUser->getDeptId());

		if (sizeof($dept) > 0) {
			return true;
		}

		return false;
	}

}