<?php

namespace Controllers;

use Models\CodeAde;
use Models\User;
use Views\TechnicianView;

/**
 * Class TechnicianController
 *
 * Manage Technician (Create, update, delete, display, display schedule)
 *
 * @package Controllers
 */
class TechnicianController extends UserController implements Schedule
{

    /**
     * @var User
     */
    private $model;

    /**
     * @var TechnicianView
     */
    private $view;

    /**
     * Constructor of SecretaryController.
     */
    public function __construct() {
        parent::__construct();
        $this->model = new User();
        $this->view = new TechnicianView();
    }

    /**
     * Insert a technician in the database
     */
    public function insert() {
        $action = filter_input(INPUT_POST, 'createTech');

        if (isset($action)) {

            $login = filter_input(INPUT_POST, 'loginTech');
            $password = filter_input(INPUT_POST, 'pwdTech');
            $passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmTech');
            $email = filter_input(INPUT_POST, 'emailTech');

            if (is_string($login) && strlen($login) >= 4 && strlen($login) <= 25 &&
                is_string($password) && strlen($password) >= 8 && strlen($password) <= 25 &&
                $password === $passwordConfirm
                && is_email($email)) {

                $this->model->setLogin($login);
                $this->model->setPassword($password);
                $this->model->setEmail($email);
                $this->model->setRole('technicien');

                if ($this->model->insert()) {
                    $this->view->displayInsertValidate();
                } else {
                    $this->view->displayErrorInsertion();
                }
            } else {
                $this->view->displayErrorCreation();
            }
        }
        return $this->view->displayFormTechnician();
    }

    /**
     * Display all technicians in a table
     *
     * @return string
     */
    public function displayAllTechnician() {
        $users = $this->model->getUsersByRole('technicien');
        return $this->view->displayAllTechnicians($users);
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
	 * Display the schedule of all students
	 *
	 * @return mixed|string
	 */
	public function displayMySchedule() {
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
	 * Parse schedule data to extract time, room, and floor details.
	 *
	 * @param string $schedule The content of the schedule for a specific code (HTML or other structure).
	 *
	 * @return array|null Returns an array with parsed data or null if parsing fails.
	 */
	private
	function parseScheduleData( $schedule ) {
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
