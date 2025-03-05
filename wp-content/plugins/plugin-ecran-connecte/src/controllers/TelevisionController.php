<?php

namespace Controllers;

use Exception;
use Models\CodeAde;
use Models\Department;
use Models\Information;
use Models\User;
use Views\TelevisionView;

/**
 * Class TelevisionController
 *
 * Manage televisions (Create, update, delete, display, display schedules)
 *
 * @package Controllers
 */
class TelevisionController extends UserController implements Schedule
{

    /**
     * @var User
     */
    private User $model;

    /**
     * @var TelevisionView
     */
    private TelevisionView $view;

    /**
     * Constructor of TelevisionController
     */
    public function __construct() {
        parent::__construct();
        $this->model = new User();
        $this->view = new TelevisionView();
    }

	/**
	 * Handles the insertion of a new television user and processes associated codes.
	 *
	 * Validates all input data including login, password and codes, and ensures the user does not already exist.
	 * If validation passes, the new user is created, and a success message is displayed.
	 * In case of any error, an appropriate error message is returned.
	 *
	 * @return string A response indicating the result of the action. Either 'error', renders a form, or displays a success/error message.
	 */
    public function insert(): string
    {
        $action = filter_input(INPUT_POST, 'createTv');

        $codeAde = new CodeAde();

        $currentUser = wp_get_current_user();
        $deptModel = new Department();
        $isAdmin = in_array('administrator', $currentUser->roles);
        $currentDept = $isAdmin ? null : $deptModel->getUserInDept($currentUser->ID)->getId();

        if (isset($action))
        {
            $login = filter_input(INPUT_POST, 'loginTv');
            $password = filter_input(INPUT_POST, 'pwdTv');
            $passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmTv');
            $deptId = $isAdmin ? filter_input(INPUT_POST, 'deptIdTv') : $currentDept;
            $codes = filter_input(INPUT_POST, 'selectTv', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            $typeDefilement = $_POST['defilement'];
            $tempsDefilement = filter_input(INPUT_POST, 'temps');

            if (is_string($login) && strlen($login) >= 4 && strlen($login) <= 25 &&
                is_string($password) && strlen($password) >= 8 && strlen($password) <= 25 &&
                $password === $passwordConfirm)
            {

                $codesAde = array();
                foreach ($codes as $code)
                {
                    if (is_numeric($code) && $code > 0)
                    {
                        if (is_null($codeAde->getByCode($code)->getId()))
                        {
                            return 'error'; // Code invalide;
                        }
                        else
                        {
                            $codesAde[] = $codeAde->getByCode($code);
                        }
                    }
                }

                // Configuration du modèle de télévision
                $this->model->setLogin($login);
                $this->model->setEmail($login . '@' . $login . '.fr');
                $this->model->setPassword($password);
                $this->model->setRole('television');
                $this->model->setCodes($codesAde);
                $this->model->setDeptId($deptId);

                if (empty($typeDefilement))
                {
                    $typeDefilement = 'suret';
                }

                if (empty($tempsDefilement))
                {
                    $tempsDefilement = 0;
                }
                $tempsDefilement = (int)$tempsDefilement;

                $this->model->setTypeDefilement($typeDefilement);

                if ($tempsDefilement <= 0)
                {
                    $this->view->displayTimeoutNegativeError();
                }
                else
                {
                    $this->model->setTimeout($tempsDefilement * 1000);

                    // Insertion du modèle dans la base de données
                    if (!$this->checkDuplicateUser($this->model) && $this->model->insert())
                    {
                        $this->view->displayInsertValidate();
                    }
                    else
                    {
                        $this->view->displayErrorInsertion();
                    }
                }
            }
            else
            {
                $this->view->displayErrorCreation();
            }
        }

        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        $allDepts = $deptModel->getAll();

        return $this->view->displayFormTelevision($years, $groups, $halfGroups, $allDepts, $isAdmin, $currentDept);
    }

    /**
     * Modify user data and handle the modification process
     *
     * @param User $user The user object that will be modified
     *
     * @return string The HTML content for the modification form or an error message
     * @throws Exception
     */
    public function modify(User $user): string {
        $page = get_page_by_title_V2('Gestion des utilisateurs');
        $returnUrl = get_permalink($page->ID);

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Récupération des données
                $codes = $_POST['selectTv'] ?? [];
                $scrollType = $_POST['defilement'] ?? 'suret';
                $timeout = (int)($_POST['temps'] ?? 10) * 1000;

                // Validation des données
                if ($timeout <= 0) {
                    throw new Exception("Durée invalide");
                }

                // Conversion des codes en objets CodeAde
                $codeAde = new CodeAde();
                $codesObjects = [];
                foreach ($codes as $code) {
                    if ($code !== '0') {
                        $codeObj = $codeAde->getByCode($code);
                        if ($codeObj) {
                            $codesObjects[] = $codeObj;
                        }
                    }
                }

                // Mise à jour de l'utilisateur
                $user->setCodes($codesObjects);
                $user->setTypeDefilement($scrollType);
                $user->setTimeout($timeout);

                if ($user->update()) {
                    wp_redirect(add_query_arg('success', '1', $returnUrl));
                    exit;
                }
            }

            // Récupération des données pour le formulaire
            $codeAde = new CodeAde();
            return $this->view->modifyForm(
                $user,
                $codeAde->getAllFromType('year'),
                $codeAde->getAllFromType('group'),
                $codeAde->getAllFromType('halfGroup')
            );

        } catch (Exception $e) {
            error_log("Erreur modification TV: " . $e->getMessage());
            return '<div class="alert alert-danger">Erreur: ' . $e->getMessage() . '</div>';
        }
    }

	/**
	 * Retrieves and displays all users with the role of 'television'.
	 *
	 * @return string The rendered view displaying all television users.
	 */
    public function displayAllTv(): string {
        $users = $this->model->getUsersByRole('television');

        $deptModel = new Department();
        $userDeptList = [];

        foreach ($users as $user) {
            $dept = $deptModel->getUserInDept($user->getId());
            $userDeptList[] = $dept ? $dept->getName() : 'Non assigné';
        }

        return $this->view->displayAllTv($users, $userDeptList);
    }


	/**
	 * Displays the current user's schedule based on their codes and theme settings.
	 * Generates and formats the schedule dynamically based on the number of codes
	 * and the selected scrolling option from the theme configuration.
	 *
     * @return string The rendered schedule, typically a string, or a default message if no schedule is available.
	 */
    public function displayMySchedule(): string {
        $current_user = wp_get_current_user();
        $user = $this->model->get($current_user->ID);
        $user = $this->model->getMycodes([$user])[0];

        $string = "";
        if (sizeof($user->getCodes()) > 1) {
            $informationVideo = new InformationController();
            if (get_theme_mod('ecran_connecte_schedule_scroll', 'vert') == 'vert') {
                $string .= '<div class="ticker1">
						<div class="innerWrap tv-schedule">';
                foreach ($user->getCodes() as $code) {
                    $path = $this->getFilePath($code->getCode());
                    if (file_exists($path)) {
                        if ($this->displaySchedule($code->getCode())) {
                            $string .= '<div class="list">';
                            $string .= $this->displaySchedule($code->getCode());
                            $string .= $this->view->displayEndDiv();
                            if ($user->getTypeDefilement() == 'defil')
                            {
                                $string .= $informationVideo->displayVideo();
                            }
                        }
                    }
                }
                $string .= $this->view->displayEndDiv() . $this->view->displayEndDiv();
            } else {
                $string .= $this->view->displayStartSlide();
                foreach ($user->getCodes() as $code) {
                    $path = $this->getFilePath($code->getCode());
                    if (file_exists($path)) {
                        if ($this->displaySchedule($code->getCode())) {
                            $string .= $this->view->displayMidSlide();
                            $string .= $this->displaySchedule($code->getCode());
                            $string .= $this->view->displayEndDiv();
                            if ($user->getTypeDefilement() == 'defil')
                            {
                                $string .= $informationVideo->displayVideo();
                            }
                        }
                    }
                }
                $string .= $this->view->displayEndDiv();
            }
        } else {
            if (!empty($user->getCodes()[0])) {
                $string .= $this->displaySchedule($user->getCodes()[0]->getCode());
            } else {
                $string .= '<p>Vous n\'avez pas cours !</p>';
            }
        }
        return $string;
    }
}
