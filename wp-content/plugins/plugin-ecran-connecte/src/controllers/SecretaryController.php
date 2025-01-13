<?php

namespace Controllers;

use Models\User;
use Views\SecretaryView;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Class SecretaryController
 *
 * All actions for secretary (Create, update, display)
 *
 * @package Controllers
 */
class SecretaryController extends UserController
{

    /**
     * @var User
     */
    private User $model;

    /**
     * @var SecretaryView
     */
    private SecretaryView $view;

    /**
     * Constructor of SecretaryController.
     */
    public function __construct() {
        parent::__construct();
        $this->model = new User();
        $this->view = new SecretaryView();
    }


	/**
	 * Displays the schedule by rendering the welcome admin view.
	 *
	 * @return string The result of rendering the welcome admin view.
	 */
    public function displayMySchedule(): string {
        return $this->view->displayWelcomeAdmin();
    }

	/**
	 * Handles the insertion of a secretary user record into the system.
	 * Validates the input data, ensures it meets requirements, checks for duplicate users,
	 * and calls the model to perform the actual insertion. Displays appropriate views based
	 * on success or failure of actions.
	 *
	 * @return string The output of the secretary form view after the operation is handled.
	 */
    public function insert(): string {
        $action = filter_input(INPUT_POST, 'createSecre');

        if (isset($action)) {

            $login = filter_input(INPUT_POST, 'loginSecre');
            $password = filter_input(INPUT_POST, 'pwdSecre');
            $passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmSecre');
            $email = filter_input(INPUT_POST, 'emailSecre');

            if (is_string($login) && strlen($login) >= 4 && strlen($login) <= 25 &&
                is_string($password) && strlen($password) >= 8 && strlen($password) <= 25 &&
                $password === $passwordConfirm && is_email($email)) {

                $this->model->setLogin($login);
                $this->model->setPassword($password);
                $this->model->setEmail($email);
                $this->model->setRole('secretaire');

                if (!$this->checkDuplicateUser($this->model) && $this->model->insert()) {
                    $this->view->displayInsertValidate();
                } else {
                    $this->view->displayErrorInsertion();
                }
            } else {
                $this->view->displayErrorCreation();
            }
        }
        return $this->view->displayFormSecretary();
    }

	/**
	 * Displays all secretaries by retrieving users with the role of 'secretaire' and rendering the appropriate view.
	 *
	 * @return string The result of the view's displayAllSecretary method.
	 */
    public function displayAllSecretary(): string {
        $users = $this->model->getUsersByRole('secretaire');
        return $this->view->displayAllSecretary($users);
    }

    /*** MANAGE USER ***/

	/**
	 * Creates users and generates a multi-select interface for different user roles.
	 *
	 * @return string A concatenated string containing the HTML output for user creation,
	 * including multi-select start, titles, content, and context-specific user creation interface.
	 */
    public function createUsers(): string {
	    $user_id = get_current_user_id();
	    $user_info = get_userdata($user_id);
		$adminDept = null;
		if(in_array('administrator', $user_info->roles)){
			$adminDept = new AdminDeptController();
		}
        $secretary = new SecretaryController();
        $technician = new TechnicianController();
        $television = new TelevisionController();

		$form = $this->view->displayStartMultiSelect() .
		           $this->view->displayTitleSelect('secretary', 'Secrétaires', true) .
		           $this->view->displayTitleSelect('technician', 'Technicien') .
		           $this->view->displayTitleSelect('television', 'Télévisions');

	    if (!is_null($adminDept)) {
			$form .= $this->view->displayTitleSelect('adminDept', 'Admin Département');;
	    }

		$form .= $this->view->displayEndOfTitle() .
		         $this->view->displayContentSelect('secretary', $secretary->insert(), true) .
		         $this->view->displayContentSelect('technician', $technician->insert()) .
		         $this->view->displayContentSelect('television', $television->insert());

	    if (!is_null($adminDept)) {
		    $form .= $this->view->displayContentSelect('adminDept', $adminDept->insert());
	    }

		$form .= $this->view->displayEndDiv() .
		        $this->view->contextCreateUser();

	    return $form;
    }

	/**
	 * Generates and displays a multi-select form based on different user roles and their associated data.
	 *
	 * This method retrieves the current user information, determines if the user has an administrator role,
	 * creates the necessary controllers for different roles, and dynamically builds a form for selecting
	 * and displaying user data for secretaries, technicians, televisions, and optionally administrators.
	 *
	 * @return string The generated HTML form for displaying users based on their roles.
	 */
    public function displayUsers(): string{
	    $user_id = get_current_user_id();
	    $user_info = get_userdata($user_id);
	    $adminDept = null;
	    if(in_array('administrator', $user_info->roles)){
		    $adminDept = new AdminDeptController();
	    }
	    $secretary = new SecretaryController();
	    $technician = new TechnicianController();
	    $television = new TelevisionController();

	    $form = $this->view->displayStartMultiSelect() .
	            $this->view->displayTitleSelect('secretary', 'Secrétaires', true) .
	            $this->view->displayTitleSelect('technician', 'Technicien') .
	            $this->view->displayTitleSelect('television', 'Télévisions');

	    if (!is_null($adminDept)) {
		    $form .= $this->view->displayTitleSelect('adminDept', 'Admin Département');;
	    }

	    $form .= $this->view->displayEndOfTitle() .
	             $this->view->displayContentSelect('secretary', $secretary->displayAllSecretary(), true) .
	             $this->view->displayContentSelect('technician', $technician->displayAllTechnician()) .
	             $this->view->displayContentSelect('television', $television->displayAllTv());

	    if (!is_null($adminDept)) {
		    $form .= $this->view->displayContentSelect('adminDept', $adminDept->displayAllAdminDept());
	    }

	    $form .= $this->view->displayEndDiv() .
	             $this->view->contextCreateUser();

	    return $form;
    }

	/**
	 * Modifies a user based on their ID and role.
	 *
	 * This method retrieves a user from the database and WordPress system
	 * using their ID. If the user exists and their role includes "television",
	 * it delegates the modification process to the TelevisionController.
	 * Otherwise, it displays a "no user" view.
	 *
	 * @return string Returns the result of the modification process if the user exists
	 *               and meets the criteria, otherwise returns the "no user" view.
	 */
    public function modifyUser(): string {
        $id = $_GET['id'];
        if (is_numeric($id) && $this->model->get($id)) {
            $user = $this->model->get($id);

            $wordpressUser = get_user_by('id', $id);

            if (in_array("television", $wordpressUser->roles)) {
                $controller = new TelevisionController();
                return $controller->modify($user);
            } else {
                return $this->view->displayNoUser();
            }
        } else {
            return $this->view->displayNoUser();
        }
    }

	/**
	 * Deletes users based on their roles and selected checkboxes.
	 *
	 * This method processes the delete action triggered via a POST request.
	 * It iterates through predefined roles, checks for corresponding selected checkboxes,
	 * and deletes the associated users by their IDs.
	 *
	 * @return void
	 */
    public function deleteUsers(): void {
        $actionDelete = filter_input(INPUT_POST, 'delete');
        $roles = ['Tech', 'Secre', 'Tele'];
        if (isset($actionDelete)) {
            foreach ($roles as $role) {
                if (isset($_REQUEST['checkboxStatus' . $role])) {
                    $checked_values = $_REQUEST['checkboxStatus' . $role];
                    foreach ($checked_values as $id) {
                        $this->deleteUser($id);
                    }
                }
            }
        }
    }

	/**
	 * Deletes a user by their unique identifier.
	 *
	 * @param int $id The unique identifier of the user to delete.
	 *
	 * @return void
	 */
    private function deleteUser($id): void {
        $user = $this->model->get($id);
        $user->delete();
    }
}
