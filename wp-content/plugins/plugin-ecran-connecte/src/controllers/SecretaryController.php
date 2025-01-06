<?php

namespace Controllers;

use Models\Department;
use Models\User;
use Views\SecretaryView;

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
    private $model;

    /**
     * @var SecretaryView
     */
    private $view;

    /**
     * Constructor of SecretaryController.
     */
    public function __construct() {
        parent::__construct();
        $this->model = new User();
        $this->view = new SecretaryView();
    }


    /**
     * Display the magic button to dl schedule
     */
    public function displayMySchedule() {
        return $this->view->displayWelcomeAdmin();
    }

    /**
     * Insert a secretary in the database
     */
    public function insert() {
        $action = filter_input(INPUT_POST, 'createSecre');

	    $currentUser = wp_get_current_user();

	    $deptModel = new Department();
	    $isAdmin = in_array('administrator', $currentUser->roles);
	    $currentDept = $isAdmin ? -1 : $deptModel->getUserInDept($currentUser->ID)->getId();

        if (isset($action)) {

            $login = filter_input(INPUT_POST, 'loginSecre');
            $password = filter_input(INPUT_POST, 'pwdSecre');
            $passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmSecre');
            $email = filter_input(INPUT_POST, 'emailSecre');
	        $deptId = $isAdmin ? filter_input(INPUT_POST, 'deptIdSecre') : $currentUser;

            if (is_string($login) && strlen($login) >= 4 && strlen($login) <= 25 &&
                is_string($password) && strlen($password) >= 8 && strlen($password) <= 25 &&
                $password === $passwordConfirm && is_email($email)) {

                $this->model->setLogin($login);
                $this->model->setPassword($password);
                $this->model->setEmail($email);
                $this->model->setRole('secretaire');
				$this->model->setDeptId($deptId);

                if (!$this->checkDuplicateUser($this->model) && $this->model->insert()) {
                    $this->view->displayInsertValidate();
                } else {
                    $this->view->displayErrorInsertion();
                }
            } else {
                $this->view->displayErrorCreation();
            }
        }

		$departments = $deptModel->getAll();

        return $this->view->displayFormSecretary($departments, $isAdmin, $currentDept);
    }

    /**
     * Display all secretary
     *
     * @return string
     */
    public function displayAllSecretary() {
        $users = $this->model->getUsersByRole('secretaire');

		$deptModel = new Department();
		$userDeptList = array();
		foreach ($users as $user) {
			$userDeptList[] = $deptModel->getUserInDept($user->getId())->getName();
		}

        return $this->view->displayAllSecretary($users, $userDeptList);
    }

    /*** MANAGE USER ***/

    /**
     * Create an user
     *
     * @return string
     */
    public function createUsers() {
        $secretary = new SecretaryController();
        $technician = new TechnicianController();
        $television = new TelevisionController();
        return
            $this->view->displayStartMultiSelect() .
            $this->view->displayTitleSelect('secretary', 'Secrétaires', true) .
            $this->view->displayTitleSelect('technician', 'Technicien') .
            $this->view->displayTitleSelect('television', 'Télévisions') .
            $this->view->displayEndOfTitle() .
            $this->view->displayContentSelect('secretary', $secretary->insert(), true) .
            $this->view->displayContentSelect('technician', $technician->insert()) .
            $this->view->displayContentSelect('television', $television->insert()) .
            $this->view->displayEndDiv() .
            $this->view->contextCreateUser();
    }

    /**
     * Display users by roles
     */
    public function displayUsers() {
        $secretary = new SecretaryController();
        $technician = new TechnicianController();
        $television = new TelevisionController();
        return
            $this->view->displayStartMultiSelect() .
            $this->view->displayTitleSelect('secretary', 'Secrétaires', true) .
            $this->view->displayTitleSelect('technician', 'Technicien') .
            $this->view->displayTitleSelect('television', 'Télévisions') .
            $this->view->displayEndOfTitle() .
            $this->view->displayContentSelect('secretary', $secretary->displayAllSecretary(), true) .
            $this->view->displayContentSelect('technician', $technician->displayAllTechnician()) .
            $this->view->displayContentSelect('television', $television->displayAllTv()) .
            $this->view->displayEndDiv();
    }

    /**
     * Modify an user
     */
    public function modifyUser() {
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
     * Delete users
     */
    public function deleteUsers() {
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
     * Delete an user
     *
     * @param $id
     */
    private function deleteUser($id) {
        $user = $this->model->get($id);
        $user->delete();
    }
}
