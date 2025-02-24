<?php

namespace Controllers;

use Models\Alert;
use Models\CodeAde;
use Models\Information;
use Models\User;
use R34ICS;
use Views\UserView;

/**
 * Class UserController
 *
 * Manage all users (Create, update, delete)
 *
 * @package Controllers
 */
class UserController extends Controller
{

    /**
     * @var User
     */
    private User $model;

    /**
     * @var UserView
     */
    private UserView $view;

    /**
     * UserController constructor.
     */
    public function __construct() {
        $this->model = new User();
        $this->view = new UserView();
    }

	/**
	 * Deletes a user and associated data such as alerts and information.
	 *
	 * @param int $id The ID of the user
	 */
    public function delete(int $id): void {
        $user = $this->model->get($id);
        $userData = get_userdata($id);
        $user->delete();
        if (in_array("secretaire", $userData->roles) || in_array("administrator", $userData->roles)) {
            $modelAlert = new Alert();
            $alerts = $modelAlert->getAuthorListAlert($user->getLogin());
            foreach ($alerts as $alert) {
                $alert->delete();
            }

	        $modelInfo = new Information();
	        $infos = $modelInfo->getAuthorListInformation($user->getId());
	        foreach ($infos as $info) {
		        $goodType = ['img', 'pdf', 'event', 'LocCvideo', 'LocSvideo'];
		        if (in_array($info->getType(), $goodType)) {
			        $infoController = new InformationController();
			        $infoController->deleteFile($info->getId());
		        }
		        $modelInfo->delete();
	        }
        }
    }

	/**
	 * Handles the deletion of a user's account, providing functionality for requesting and validating deletion codes.
	 *
	 * @return string
	 */
    public function deleteAccount(): string {
    if (in_array('administrator', $current_user->roles)) {
        return '<p>La suppression de compte n’est pas autorisée pour les administrateurs.</p>';
    }

    $actionDeleteMyAccount = filter_input(INPUT_POST, 'deleteMyAccount');
    $actionConfirmDelete = filter_input(INPUT_POST, 'deleteAccount');

    $user = $this->model->get($current_user->ID);

    if (isset($actionDeleteMyAccount)) {
        $password = filter_input(INPUT_POST, 'verifPwd');
        if (wp_check_password($password, $current_user->user_pass)) {
            $code = wp_generate_password();
            if (!empty($user->getCodeDeleteAccount())) {
                $user->updateCode($code);
            } else {
                $user->createCode($code);
            }

            $to = $current_user->user_email;
            $subject = "Désinscription à la télé-connecté";
            $message = '
                <!DOCTYPE html>
                <html lang="fr">
                    <head>
                        <title>Désinscription à la télé-connecté</title>
                    </head>
                    <body>
                        <p>Bonjour, vous avez décidé de vous désinscrire sur le site de la Télé Connecté.</p>
                        <p>Votre code de désinscription est : ' . $code . '.</p>
                        <p>Pour vous désinscrire, rendez-vous sur le site :
                           <a href="' . home_url() . '/mon-compte/">Tv Connectée</a>.
                        </p>
                    </body>
                </html>';
            $headers = array('Content-Type: text/html; charset=UTF-8');

            wp_mail($to, $subject, $message, $headers);
            $this->view->displayMailSend();
        } else {
            $this->view->displayWrongPassword();
        }
    } elseif (isset($actionConfirmDelete)) {
        $code = filter_input(INPUT_POST, 'codeDelete');
        $userCode = $user->getCodeDeleteAccount();
        if ($code == $userCode) {
            $user->deleteCode();
            $user->delete();
            $this->view->displayModificationValidate();
        } else {
            $this->view->displayWrongPassword();
        }
    }

    return $this->view->displayDeleteAccount() . $this->view->displayEnterCode();
}


    /**
     * Modify his password, delete his account or modify his groups
     *
     * @return string
     */
    public function chooseModif() {
        $current_user = wp_get_current_user();
        $string = $this->view->displayStartMultiSelect();

		$string .= $this->view->displayTitleSelect('pass', 'Modifier mon mot de passe', true);

        $string .= $this->view->displayTitleSelect('delete', 'Supprimer mon compte') .
            $this->view->displayEndOfTitle();

        $string .= $this->view->displayContentSelect('pass', $this->modifyPwd(), true);

        $string .= $this->view->displayContentSelect('delete', $this->deleteAccount()) . $this->view->displayEndDiv();

        return $string;
    }

	/**
	 * Modifies the password for the current user.
	 *
	 * @return string
	 */
    public function modifyPwd(): string {
        $action = filter_input(INPUT_POST, 'modifyMyPwd');
        $current_user = wp_get_current_user();
        if (isset($action)) {
            $pwd = filter_input(INPUT_POST, 'verifPwd');
            if (wp_check_password($pwd, $current_user->user_pass)) {
                $newPwd = filter_input(INPUT_POST, 'newPwd');
                wp_set_password($newPwd, $current_user->ID);
                $this->view->displayModificationPassValidate();
            } else {
                $this->view->displayWrongPassword();
            }
        }
        return $this->view->displayModifyPassword();
    }

	/**
	 * Display a schedule based on a given code and parameters.
	 *
	 * @param string $code The unique code to identify the schedule file.
	 * @param bool $allDay Optional. Whether to display events as all-day events. Default is false.
	 *
	 * @return string Rendered HTML of the schedule.
	 */
    public function displaySchedule(string $code,bool $allDay = false): string {
        global $R34ICS;
        $R34ICS = new R34ICS();

        $url = $this->getFilePath($code);
        $args = array(
            'count' => 10,
            'description' => null,
            'eventdesc' => null,
            'format' => null,
            'hidetimes' => null,
            'showendtimes' => null,
            'title' => null,
            'view' => 'list',
        );
        return $R34ICS->display_calendar($url, $code, $allDay, $args);
    }

    /**
     * Display the schedule link to the code in the url
     *
     * @return string
     */
    function displayYearSchedule() {
        $id = $this->getMyIdUrl();

        $codeAde = new CodeAde();

        if (is_numeric($id)) {
            $codeAde = $codeAde->get($id);
            if (!is_null($codeAde->getTitle()) && $codeAde->getType() === 'year') {
                return $this->displaySchedule($codeAde->getCode(), true);
            }
        }

        return $this->view->displaySelectSchedule();
    }

	/**
	 * Checks for duplicate user based on login and email.
	 *
	 * @param User $newUser The user object containing login and email to check for duplicates.
	 *
	 * @return bool Returns true if a duplicate user is found, otherwise false.
	 */
    public function checkDuplicateUser(User $newUser): bool {
        $codesAde = $this->model->checkUser($newUser->getLogin(), $newUser->getEmail());

        if (sizeof($codesAde) > 0) {
            return true;
        }

        return false;
    }

	/**
	 * Processes and updates the modification of codes associated with the current user.
	 *
	 * The method retrieves user input for year, group, and half-group codes,
	 * validates them, and associates them with their corresponding types.
	 * If the codes are valid, they are updated in the model and stored
	 * for the current user. Feedback messages are displayed based on the
	 * success or failure of the update. The method also fetches all
	 * available codes for display.
	 *
	 * @return string Returns the generated view displaying the current codes
	 *               alongside available year, group, and half-group codes.
	 */
    public function modifyCodes(): string {
        $current_user = wp_get_current_user();
        $codeAde = new CodeAde();
        $this->model = $this->model->get($current_user->ID);

        $action = filter_input(INPUT_POST, 'modifvalider');

        if (isset($action)) {
            $year = filter_input(INPUT_POST, 'modifYear');
            $group = filter_input(INPUT_POST, 'modifGroup');
            $halfGroup = filter_input(INPUT_POST, 'modifHalfgroup');


            if (is_numeric($year) && is_numeric($group) && is_numeric($halfGroup)) {

                $codes = [$year, $group, $halfGroup];
                $codesAde = [];
                foreach ($codes as $code) {
                    if ($code !== 0) {
                        $code = $codeAde->getByCode($code);
                    }
                    $codesAde[] = $code;
                }

                if ($codesAde[0]->getType() !== 'year') {
                    $codesAde[0] = 0;
                }

                if ($codesAde[1]->getType() !== 'group') {
                    $codesAde[1] = 0;
                }

                if ($codesAde[2]->getType() !== 'halfGroup') {
                    $codesAde[2] = 0;
                }

                $this->model->setCodes($codesAde);

                if ($this->model->update()) {
                    $this->view->successMesageChangeCode();
                } else {
                    $this->view->errorMesageChangeCode();
                }
            }
        }

        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        return $this->view->displayModifyMyCodes($this->model->getCodes(), $years, $groups, $halfGroups);
    }
}
