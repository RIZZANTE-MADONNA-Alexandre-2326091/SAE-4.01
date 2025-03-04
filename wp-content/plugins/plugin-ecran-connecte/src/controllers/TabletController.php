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

    public function __construct()
    {
        parent::__construct();
        $this->model = new User();
        $this->view = new TabletView();
    }

    public function insert()
    {
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
            $selectedRoomCode = filter_input(INPUT_POST, 'selectTablet');

            if (is_string($login) && strlen($login) >= 4 && strlen($login) <= 25 &&
                is_string($password) && strlen($password) >= 8 && strlen($password) <= 25 &&
                $password === $passwordConfirm) {

                $room = $codeAde->getByCode($selectedRoomCode);
                if (!$room || is_null($room->getId())) {
                    return 'error'; // Invalid room code
                }

                $this->model->setLogin($login);
                $this->model->setPassword($password);
                $this->model->setEmail($login . '@' . $login . '.fr');
                $this->model->setRole('tablette');
                $this->model->setDeptId($deptId);
                $this->model->setCodes([$room]);

                if (!$this->checkDuplicateUser($this->model) && $this->model->insert()) {
                    $this->view->displayInsertValidate();
                } else {
                    $this->view->displayErrorInsertion();
                }
            } else {
                $this->view->displayErrorCreation();
            }
        }

        $allRooms = $codeAde->getAllFromType('room') ?? [];
        $occupiedRooms = $this->model->getOccupiedRooms();
        $availableRooms = array_filter($allRooms, function($room) use ($occupiedRooms) {
            return !in_array($room->getId(), $occupiedRooms);
        });

        return $this->view->displayFormTablet($deptModel->getAll(), $isAdmin, $currentDept, $availableRooms);
    }


    public function displayAllTablets()
    {
        $users = $this->model->getUsersByRole('tablet');

        $deptModel = new Department();
        $userDeptList = array();
        foreach ($users as $user) {
            $userDeptList[] = $deptModel->getUserInDept($user->getId())->getName();
        }

        return $this->view->displayAllTablets($users, $userDeptList);


    }

    public function displayUserRoomSchedule(): string {
        $current_user = wp_get_current_user();
        $user = $this->model->get($current_user->ID);
        $rooms = $this->model->getRooms($user);

        return $this->view->displayRoomSchedule($rooms);
    }


}