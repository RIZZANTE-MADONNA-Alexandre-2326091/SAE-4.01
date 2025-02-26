<?php
namespace Controllers;

use Models\CodeAde;
use Models\Department;
use Models\User;
use Views\TabletView;

class TabletController extends UserController
{
    private $model;
    private $view;

    public function __construct() {
        parent::__construct();
        $this->model = new User();
        $this->view = new TabletView();
    }

    public function insert() {
        $action = filter_input(INPUT_POST, 'createTablet');

        $codeAde = new CodeAde();

        $currentUser = wp_get_current_user();
        $deptModel = new Department();
        $isAdmin = in_array('administrator', $currentUser->roles);
        $currentDept = $isAdmin ? null : $deptModel->getUserInDept($currentUser->ID)->getId();

        if (isset($action)) {
            $login = filter_input(INPUT_POST, 'loginTablet');
            $password = filter_input(INPUT_POST, 'pwdTablet');
            $passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmTablet');
            $deptId = $isAdmin ? filter_input(INPUT_POST, 'deptTablet') : $currentDept;
            $codes = filter_input(INPUT_POST, 'selectTablet', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

            if (is_string($login) && strlen($login) >= 4 && strlen($login) <= 25 &&
                is_string($password) && strlen($password) >= 8 && strlen($password) <= 25 &&
                $password === $passwordConfirm) {

                $codesAde = array();
                foreach ($codes as $code) {
                    if (is_numeric($code) && $code > 0) {
                        $codeAdeInstance = $codeAde->getByCode($code);
                        if (is_null($codeAdeInstance->getId())) {
                            return 'error'; // Code invalide;
                        } else {
                            $codesAde[] = $codeAdeInstance;
                        }
                    }
                }

                $this->model->setLogin($login);
                $this->model->setPassword($password);
                $this->model->setEmail($login . '@' . $login . '.fr');
                $this->model->setRole('tablet');
                $this->model->setDeptId($deptId);
                $this->model->setCodes($codesAde);

                if (!$this->checkDuplicateUser($this->model) && $this->model->insert()) {
                    $this->view->displayInsertValidate();
                } else {
                    $this->view->displayErrorInsertion();
                }
            } else {
                $this->view->displayErrorCreation();
            }
        }

        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        $allDepts = $deptModel->getAll();

        return $this->view->displayFormTablet($allDepts, $isAdmin, $currentDept, $years, $groups, $halfGroups);
    }

    public function displayAllTablets() {
        $users = $this->model->getUsersByRole('tablet');

        $deptModel = new Department();
        $userDeptList = array();
        foreach ($users as $user) {
            $userDeptList[] = $deptModel->getUserInDept($user->getId())->getName();
        }

        return $this->view->displayAllTablets($users, $userDeptList);
    }
}