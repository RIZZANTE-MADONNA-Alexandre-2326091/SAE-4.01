<?php

namespace Controllers;

use Models\User;
use Views\CommunicatorView;

/**
 * Class CommunicatorController
 *
 * All actions for communicator (Create, update, display)
 *
 * @package controllers
 */
class CommunicatorController extends UserController
{
    /**
     * @var User
     */
    private User $model;

    /**
     * @var CommunicatorView
     */
    private CommunicatorView $view;

    /**
     * Constructor of CommunicatorController.
     */
    function __construct()
    {
        parent::__construct();
        $this->model = new User();
        $this->view = new CommunicatorView();
    }

    /**
     * Displays the schedule by rendering the welcome communicator view.
     *
     * @return string The result of rendering the welcome admin view.
     */
    public function displayMySchedule(): string {
        return $this->view->displayWelcomeCommunicator();
    }

    /**
     * Handles the insertion of a communicator user record into the system.
     * Validates the input data, ensures it meets requirements, checks for duplicate users,
     * and calls the model to perform the actual insertion. Displays appropriate views based
     * on success or failure of actions.
     *
     * @return string The output of the communicator form view after the operation is handled.
     */
    function insert() : string
    {
	    $action = filter_input(INPUT_POST, 'createComm');
	    if (isset($action)) {

		    $login = filter_input(INPUT_POST, 'loginComm');
		    $password = filter_input(INPUT_POST, 'pwdComm');
		    $passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmComm');
		    $email = filter_input(INPUT_POST, 'emailComm');

		    if (is_string($login) && strlen($login) >= 4 && strlen($login) <= 25 &&
		        is_string($password) && strlen($password) >= 8 && strlen($password) <= 25 &&
		        $password === $passwordConfirm && is_email($email)) {

			    $this->model->setLogin($login);
			    $this->model->setPassword($password);
			    $this->model->setEmail($email);
			    $this->model->setRole('communicant');
				$this->model->setDeptId(0);

			    if (!$this->checkDuplicateUser($this->model) && $this->model->insert()) {
				    $this->view->displayInsertValidate();
			    } else {
				    $this->view->displayErrorInsertion();
			    }
		    } else {
			    $this->view->displayErrorCreation();
		    }
	    }
	    return $this->view->displayFormCommunicator();
    }

    function displayAllCommunicator(): string {
	    $users = $this->model->getUsersByRole('communicant');

	    return $this->view->displayAllCommunicator($users);
    }
}