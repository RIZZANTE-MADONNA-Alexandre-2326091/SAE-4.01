<?php

namespace Controllers;

use Models\CodeAde;
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
    public function insert(): string {
        $action = filter_input(INPUT_POST, 'createTv');

        $codeAde = new CodeAde();

        if (isset($action)) {

            $login = filter_input(INPUT_POST, 'loginTv');
            $password = filter_input(INPUT_POST, 'pwdTv');
            $passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmTv');
            $codes = $_POST['selectTv'];

            if (is_string($login) && strlen($login) >= 4 && strlen($login) <= 25 &&
                is_string($password) && strlen($password) >= 8 && strlen($password) <= 25 &&
                $password === $passwordConfirm) {

                $codesAde = array();
                foreach ($codes as $code) {
                    if (is_numeric($code) && $code > 0) {
                        if (is_null($codeAde->getByCode($code)->getId())) {
                            return 'error';
                        } else {
                            $codesAde[] = $codeAde->getByCode($code);
                        }
                    }
                }

                $this->model->setLogin($login);
                $this->model->setEmail($login . '@' . $login . '.fr');
                $this->model->setPassword($password);
                $this->model->setRole('television');
                $this->model->setCodes($codesAde);

                if (!$this->checkDuplicateUser($this->model) && $this->model->insert()) {
                    $this->view->displayInsertValidate();
                } else {
                    $this->view->displayErrorLogin();
                }
            } else {
                $this->view->displayErrorCreation();
            }
        }

        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        return $this->view->displayFormTelevision($years, $groups, $halfGroups);
    }

	/**
	 * Modify user data and handle the modification process
	 *
	 * @param user $user The user object that will be modified
	 *
	 * @return string The HTML content for the modification form or an error message
	 */
    public function modify(user $user): string {
        $page = get_page_by_title_V2('Gestion des utilisateurs');
        $linkManageUser = get_permalink($page->ID);

        $codeAde = new CodeAde();

        $action = filter_input(INPUT_POST, 'modifValidate');

        if (isset($action)) {
            $codes = $_POST['selectTv'];

            $codesAde = array();
            foreach ($codes as $code) {
                if (is_null($codeAde->getByCode($code)->getId())) {
                    return 'error';
                } else {
                    $codesAde[] = $codeAde->getByCode($code);
                }
            }

            $user->setCodes($codesAde);

            if ($user->update()) {
                $this->view->displayModificationValidate($linkManageUser);
            }
        }

        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        return $this->view->modifyForm($user, $years, $groups, $halfGroups);
    }

	/**
	 * Retrieves and displays all users with the role of 'television'.
	 *
	 * @return string The rendered view displaying all television users.
	 */
    public function displayAllTv(): string {
        $users = $this->model->getUsersByRole('television');
        return $this->view->displayAllTv($users);
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
            if (get_theme_mod('ecran_connecte_schedule_scroll', 'vert') == 'vert') {
                $string .= '<div class="ticker1">
						<div class="innerWrap tv-schedule">';
                foreach ($user->getCodes() as $code) {
                    $path = $this->getFilePath($code->getCode());
                    if (file_exists($path)) {
                        if ($this->displaySchedule($code->getCode())) {
                            $string .= '<div class="list">';
                            $string .= $this->displaySchedule($code->getCode());
                            $string .= '</div>';
                        }
                    }
                }
                $string .= '</div></div>';
            } else {
                $string .= $this->view->displayStartSlide();
                foreach ($user->getCodes() as $code) {
                    $path = $this->getFilePath($code->getCode());
                    if (file_exists($path)) {
                        if ($this->displaySchedule($code->getCode())) {
                            $string .= $this->view->displayMidSlide();
                            $string .= $this->displaySchedule($code->getCode());
                            $string .= $this->view->displayEndDiv();
                        }
                    }
                }
                $string .= $this->view->displayEndDiv();
            }
        } else {
            if (!empty($user->getCodes()[0])) {
                $string .= $this->displaySchedule($user->getCodes()[0]->getCode());
            } else {
                $string .= '<div class="courstext">Vous n\'avez pas cours !</div>';
            }
        }
        return $string;
    }
}
