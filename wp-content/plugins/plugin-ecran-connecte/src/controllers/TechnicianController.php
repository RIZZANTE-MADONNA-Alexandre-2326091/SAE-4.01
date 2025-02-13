<?php

namespace Controllers;

use Models\CodeAde;
use Models\Department;
use Models\User;
use Views\TechnicianView;

/**
 * Controller responsible for handling technician-related functionality and operations.
 * Extends the UserController and implements the Schedule interface.
 */
class TechnicianController extends UserController implements Schedule
{

    /**
     * @var User
     */
    private User $model;

    /**
     * @var TechnicianView
     */
    private TechnicianView $view;

    /**
     * Constructor of SecretaryController.
     */
    public function __construct() {
        parent::__construct();
        $this->model = new User();
        $this->view = new TechnicianView();
    }

	/**
	 * Handles the insertion of a new technician entry based on user input from a POST request.
	 *
	 * Validates the input data such as login, password, password confirmation, and email.
	 * Ensures the login length is between 4 and 25 characters, password length is between 8 and 25 characters,
	 * and the passwords match. If the inputs are valid and the email format is correct,
	 * the technician details are saved into the model.
	 *
	 * On successful insertion, displays a confirmation message.
	 * Otherwise, displays an error message indicating the issue.
	 *
	 * If no technician creation action is detected in the POST request or if the
	 * provided inputs are invalid, the method returns the technician creation form view.
	 *
	 * @return string The rendered view content, either the form or feedback messages.
	 */
    public function insert(): string {
        $action = filter_input(INPUT_POST, 'createTech');

	    $currentUser = wp_get_current_user();

	    $deptModel = new Department();
	    $isAdmin = in_array('administrator', $currentUser->roles);
	    $currentDept = $isAdmin ? -1 : $deptModel->getUserInDept($currentUser->ID)->getId();

        if (isset($action)) {

            $login = filter_input(INPUT_POST, 'loginTech');
            $password = filter_input(INPUT_POST, 'pwdTech');
            $passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmTech');
            $email = filter_input(INPUT_POST, 'emailTech');
	        $deptId = $isAdmin ? filter_input(INPUT_POST, 'deptIdTech') : $currentUser;

            if (is_string($login) && strlen($login) >= 4 && strlen($login) <= 25 &&
                is_string($password) && strlen($password) >= 8 && strlen($password) <= 25 &&
                $password === $passwordConfirm && is_email($email)) {

                $this->model->setLogin($login);
                $this->model->setPassword($password);
                $this->model->setEmail($email);
                $this->model->setRole('technicien');
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

        return $this->view->displayFormTechnician($departments, $isAdmin, $currentDept);
    }

	/**
	 * Displays all technicians by retrieving users with the role of 'technicien'
	 * and passing them to the view for rendering.
	 *
	 * @return string The rendered view displaying all technicians.
	 */
    public function displayAllTechnician(): string {
        $users = $this->model->getUsersByRole('technicien');

	    $deptModel = new Department();
	    $userDeptList = array();
	    foreach ($users as $user) {
		    $userDeptList[] = $deptModel->getUserInDept($user->getId())->getName();
	    }

        return $this->view->displayAllTechnicians($users, $userDeptList);
    }

//    /**
//     * Display the schedule of all students
//     *
//     * @return mixed|string
//     */
//    public function displayMySchedule() {
//        $codeAde = new CodeAde();
//
//        $years = $codeAde->getAllFromType('year');
//        $string = "";
//        foreach ($years as $year) {
//            $string .= $this->displaySchedule($year->getCode());
//        }
//        return $string;
//    }


	/**
	 * Displays the schedule for the current user based on their assigned codes.
	 *
	 * This method retrieves the current user's information and fetches their associated codes.
	 * It processes the schedule data for multiple codes to display sorted and formatted schedules.
	 * If there are multiple courses, they are sorted by time, room number, and floor number before being displayed.
	 * If only one code is present, it simply displays the schedule for that code.
	 * If no courses are scheduled, an appropriate message is returned.
	 *
	 * @return string The HTML string containing the formatted and sorted schedule or a message indicating no scheduled courses.
	 */
	public function displayMySchedule(): string {
		$current_user = wp_get_current_user();
		$user = $this->model->get( $current_user->ID );
		$user = $this->model->getMycodes( [ $user ] )[0];

		$string = "";
		if ( sizeof( $user->getCodes() ) > 1 ) {
			// Récupération des informations pertinentes pour le tri
			$courses = [];
			foreach ( $user->getCodes() as $code ) {
				$path = $this->getFilePath( $code->getCode() );
				if ( file_exists( $path ) ) {
					$schedule = $this->displaySchedule( $code->getCode() );
					if ( $schedule ) {
						$parsedSchedule = $this->parseScheduleData( $schedule ); // Méthode pour extraire les infos heure/salle/étage
						if ( $parsedSchedule ) {
							$courses[] = $parsedSchedule;
						}
					}
				}
			}

			// Tri des cours : par heure, par numéro de salle, puis par étage
			usort( $courses, function ( $a, $b ) {
				// Trier par heure
				if ( $a['time'] === $b['time'] ) {
					// Trier par salle
					if ( $a['room'] === $b['room'] ) {
						// Trier par étage
						return $a['floor'] <=> $b['floor'];
					}

					return $a['room'] <=> $b['room'];
				}

				return $a['time'] <=> $b['time'];
			} );

			// Génération de l'affichage après tri
			if ( get_theme_mod( 'ecran_connecte_schedule_scroll', 'vert' ) == 'vert' ) {
				$string .= '<div class="ticker1">
                        <div class="innerWrap tv-schedule">';
				foreach ( $courses as $course ) {
					$string .= '<div class="list">';
					$string .= $course['content']; // Utiliser le contenu trié
					$string .= '</div>';
				}
				$string .= '</div></div>';
			} else {
				$string .= $this->view->displayStartSlide();
				foreach ( $courses as $course ) {
					$string .= $this->view->displayMidSlide();
					$string .= $course['content']; // Utiliser le contenu trié
					$string .= $this->view->displayEndDiv();
				}
				$string .= $this->view->displayEndDiv();
			}
		} else {
			if ( ! empty( $user->getCodes()[0] ) ) {
				$string .= $this->displaySchedule( $user->getCodes()[0]->getCode() );
			} else {
				$string .= '<p>Aucun cours de prévu aujourd\'hui </p>';
			}
		}
		return $string;
	}

	/**
	 * Parses the schedule data from an HTML formatted string and extracts specific details such as time, room number, and floor.
	 *
	 * @param string $schedule The HTML formatted string containing the schedule information.
	 *
	 * @return array|null Returns an associative array with the keys 'time' (timestamp), 'room' (int), 'floor' (int), and 'content' (string),
	 *                    or null if the extraction fails.
	 */
	private
	function parseScheduleData(string $schedule ): array|null {
		// Supposons que le planning `$schedule` contient un format HTML avec des balises spécifiques,
		// par exemple <span class="time">08:00</span>, <span class="room">207</span>, etc.

		// Extraction de l'heure (time)
		preg_match( '/<span class="time">([\d:]+)<\/span>/', $schedule, $timeMatch );
		// Extraction du numéro de la salle (room)
		preg_match( '/<span class="room">(\d+)<\/span>/', $schedule, $roomMatch );

		if ( ! empty( $timeMatch[1] ) && ! empty( $roomMatch[1] ) ) {
			$time  = strtotime( $timeMatch[1] ); // Convertir l'heure (08:00) en timestamp
			$room  = (int) $roomMatch[1]; // Numéro de salle
			$floor = intval( $room / 100 ); // Étages dérivés des centaines dans le numéro de salle (ex: 207 -> étage 2)

			return [
				'time'    => $time,         // Heure en format timestamp
				'room'    => $room,         // Numéro de la salle
				'floor'   => $floor,       // Étages calculés à partir de la salle
				'content' => $schedule   // Garder le contenu brut pour réutilisation après tri
			];
		}

		return null; // Retourner null si l'extraction échoue.
	}
}
