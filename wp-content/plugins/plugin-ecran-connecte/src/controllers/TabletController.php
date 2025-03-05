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

    public function modify(): string {
        $userId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$userId) {
            return 'Veuillez choisir un utilisateur';
        }

        $user = $this->model->get($userId);
        if (!$user) {
            return 'Utilisateur non trouvé';
        }

        $page = get_page_by_title_V2('Gestion des utilisateurs');
        $linkManageUser = get_permalink($page->ID);

        $codeAde = new CodeAde();
        $deptModel = new Department();

        $action = filter_input(INPUT_POST, 'modifyTablet');

        if (isset($action)) {
            $selectedRoomCode = filter_input(INPUT_POST, 'selectTablet');
            $deptId = filter_input(INPUT_POST, 'deptTablet', FILTER_VALIDATE_INT);

            if ($selectedRoomCode && $deptId !== false) {
                $room = $codeAde->getByCode($selectedRoomCode);

                if ($room && !is_null($room->getId())) {
                    $user->setCodes([$room]);
                    $user->setDeptId($deptId);

                    if ($user->update()) {
                        $this->view->displayModificationValidate($linkManageUser);
                    } else {
                        $this->view->displayErrorModification();
                    }
                } else {
                    $this->view->displayErrorInvalidRoom();
                }
            } else {
                $this->view->displayErrorInvalidData();
            }
        }

        // Récupérer les salles disponibles
        $allRooms = $codeAde->getAllFromType('room') ?? [];
        $occupiedRooms = $this->model->getOccupiedRooms();
        $availableRooms = array_filter($allRooms, function($room) use ($occupiedRooms) {
            return !in_array($room->getId(), $occupiedRooms);
        });

        // Récupérer les départements
        $departments = $deptModel->getAll();

        return $this->view->modifyForm($user, $availableRooms, $departments, in_array('administrator', wp_get_current_user()->roles), $user->getDeptId());
    }


    public function displayAllTablets(): string
    {
        $users = $this->model->getUsersByRole('tablette');

        $deptModel = new Department();
        $userDeptList = array();
        foreach ($users as $user)
        {
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