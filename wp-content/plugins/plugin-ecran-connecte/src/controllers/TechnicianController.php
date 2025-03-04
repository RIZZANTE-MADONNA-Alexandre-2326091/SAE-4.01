<?php

namespace Controllers;

use Models\CodeAde;
use Models\User;
use Views\TechnicianView;

/**
 * Controller responsible for handling technician-related functionality and operations.
 * Extends the UserController and implements the Schedule interface.
 */
class TechnicianController extends UserController implements Schedule
{
    private User $model;
    private TechnicianView $view;

    public function __construct() {
        parent::__construct();
        $this->model = new User();
        $this->view = new TechnicianView();
    }

    /**
     * Handles the insertion of a new technician.
     *
     * @return string The rendered view content.
     */
    public function insert(): string {
        $action = filter_input(INPUT_POST, 'createTech');

        if (isset($action)) {
            $login = filter_input(INPUT_POST, 'loginTech');
            $password = filter_input(INPUT_POST, 'pwdTech');
            $passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmTech');
            $codes = $_POST['selectTech'] ?? []; // Récupère les codes ADE sélectionnés

            if ($this->validateInput($login, $password, $passwordConfirm, $codes)) {
                $codesAde = $this->getValidCodes($codes);

                if ($codesAde === null) {
                    return $this->view->displayErrorCreation();
                }

                $this->model->setLogin($login);
                $this->model->setEmail($login . '@' . $login . '.fr'); // Génère un email par défaut
                $this->model->setPassword($password);
                $this->model->setRole('technicien');
                $this->model->setCodes($codesAde);

                if (!$this->checkDuplicateUser($this->model) && $this->model->insert()) {
                    return $this->view->displayInsertValidate();
                } else {
                    return $this->view->displayErrorLogin();
                }
            } else {
                return $this->view->displayErrorCreation();
            }
        }

        // Affiche le formulaire de création avec les codes ADE disponibles
        $codeAde = new CodeAde();
        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        return $this->view->displayFormTechnician($years, $groups, $halfGroups);
    }

    /**
     * Validates input data for technician creation.
     *
     * @param string $login The technician's login.
     * @param string $password The technician's password.
     * @param string $passwordConfirm The password confirmation.
     * @param array $codes The selected codes.
     *
     * @return bool True if the input is valid, false otherwise.
     */
    private function validateInput(string $login, string $password, string $passwordConfirm, array $codes): bool {
        return strlen($login) >= 4 && strlen($login) <= 25 &&
            strlen($password) >= 8 && strlen($password) <= 25 &&
            $password === $passwordConfirm &&
            !empty($codes);
    }

    /**
     * Retrieves valid codes from the provided array.
     *
     * @param array $codes The selected codes.
     *
     * @return array|null An array of valid codes or null if any code is invalid.
     */
    private function getValidCodes(array $codes): ?array {
        $codeAde = new CodeAde();
        $codesAde = [];

        foreach ($codes as $code) {
            if (is_numeric($code) && $code > 0) {
                $codeEntity = $codeAde->getByCode($code);
                if (is_null($codeEntity->getId())) {
                    return null;
                }
                $codesAde[] = $codeEntity;
            }
        }

        return $codesAde;
    }

    /**
     * Modifies an existing technician.
     *
     * @param User $user The technician to modify.
     *
     * @return string The rendered view content.
     */
    public function modify(User $user): string {
        $action = filter_input(INPUT_POST, 'modifValidate');

        if (isset($action)) {
            $codes = $_POST['selectTech'] ?? [];
            $codesAde = $this->getValidCodes($codes);

            if ($codesAde === null) {
                return $this->view->displayErrorCreation();
            }

            $user->setCodes($codesAde);

            if ($user->update()) {
                $page = get_page_by_title_V2('Gestion des utilisateurs');
                $linkManageUser = get_permalink($page->ID);
                return $this->view->displayModificationValidate($linkManageUser);
            }
        }

        // Affiche le formulaire de modification avec les codes ADE disponibles
        $codeAde = new CodeAde();
        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        return $this->view->modifyForm($user, $years, $groups, $halfGroups);
    }

    /**
     * Displays all technicians.
     *
     * @return string The rendered view content.
     */
    public function displayAllTechnician(): string {
        $users = $this->model->getUsersByRole('technicien');
        return $this->view->displayAllTechnicians($users);
    }

    /**
     * Displays the schedule for the current technician, sorted by floor and time.
     *
     * @return string The rendered schedule content.
     **/
    public function displayMySchedule(): string {
        $current_user = wp_get_current_user();
        $user = $this->model->get($current_user->ID);
        $user = $this->model->getMycodes([$user])[0];

        if (empty($user->getCodes())) {
            return '<p>Aucun cours de prévu aujourd\'hui.</p>';
        }

        // Récupère tous les cours de la journée
        $courses = [];
        foreach ($user->getCodes() as $code) {
            $path = $this->getFilePath($code->getCode());
            if (file_exists($path)) {
                $schedule = $this->displaySchedule($code->getCode());
                if ($schedule) {
                    $parsedCourse = $this->parseCourseData($schedule);
                    if ($parsedCourse) {
                        $courses[] = $parsedCourse;
                    }
                }
            }
        }

        // Trie les cours selon les consignes
        usort($courses, function ($a, $b) {
            // Priorité des étages : rez-de-chaussée > premier étage > deuxième étage
            if ($a['floor'] === $b['floor']) {
                // Si même étage, trie par heure
                return $a['time'] <=> $b['time'];
            }
            return $a['floor'] <=> $b['floor'];
        });

        // Affiche les cours triés
        $string = "";
        if (get_theme_mod('ecran_connecte_schedule_scroll', 'vert') == 'vert') {
            $string .= '<div class="ticker1"><div class="innerWrap tv-schedule">';
            foreach ($courses as $course) {
                $string .= '<div class="list">' . $course['content'] . '</div>';
            }
            $string .= '</div></div>';
        } else {
            $string .= $this->view->displayStartSlide();
            foreach ($courses as $course) {
                $string .= $this->view->displayMidSlide() . $course['content'] . $this->view->displayEndDiv();
            }
            $string .= $this->view->displayEndDiv();
        }
        return $string;
    }

    /**
     * Parses course data to extract time, room, floor, and content.
     *
     * @param string $schedule The schedule data.
     *
     * @return array|null The parsed data or null if extraction fails.
     */
    private function parseCourseData(string $schedule): ?array {
        // Extraction de l'heure (time)
        preg_match('/<span class="time">([\d:]+)<\/span>/', $schedule, $timeMatch);
        // Extraction du numéro de la salle (room)
        preg_match('/<span class="room">(\d+)<\/span>/', $schedule, $roomMatch);

        if (!empty($timeMatch[1]) && !empty($roomMatch[1])) {
            $time  = strtotime($timeMatch[1]); // Convertit l'heure en timestamp
            $room  = (int) $roomMatch[1]; // Numéro de salle
            $floor = intval($room / 100); // Étages dérivés des centaines dans le numéro de salle (ex: 207 -> étage 2)

            return [
                'time'    => $time,         // Heure en format timestamp
                'room'    => $room,         // Numéro de la salle
                'floor'   => $floor,       // Étages calculés à partir de la salle
                'content' => $schedule   // Garde le contenu brut pour réutilisation après tri
            ];
        }

        return null; // Retourne null si l'extraction échoue.
    }
}