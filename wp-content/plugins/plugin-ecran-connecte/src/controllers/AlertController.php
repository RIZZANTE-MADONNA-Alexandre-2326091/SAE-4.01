<?php

namespace Controllers;

use Models\Alert;
use Models\CodeAde;
use Views\AlertView;

/**
 * Class AlertController
 *
 * Manage alerts (create, update, delete, display)
 *
 * @package Controllers
 */
class AlertController extends Controller
{

    /**
     * @var Alert
     */
    private Alert $model;

    /**
     * @var AlertView
     */
    private AlertView $view;

    /**
     * AlertController constructor
     */
    public function __construct() {
        $this->model = new Alert();
        $this->view = new AlertView();
    }

	/**
	 * Handles the insertion of a new alert.
	 *
	 * This method processes user input to create and insert a new alert. It validates the input
	 * and ensures the alert content and associated data meet the requirements. If successful,
	 * the alert is stored in the database, and a push notification is sent to the relevant audience.
	 *
	 * @return string Returns the result of the view's creationForm method, which generates the alert creation form with
	 *               necessary data such as available years, groups, and half-groups. Displays appropriate error
	 *               messages or success notifications based on the operation outcome.
	 */
    public function insert(): string {
        $codeAde = new CodeAde();
        $action = filter_input(INPUT_POST, 'submit');
        if (isset($action)) {
            $codes = $_POST['selectAlert'];
            $content = filter_input(INPUT_POST, 'content');
            $endDate = filter_input(INPUT_POST, 'expirationDate');

            $creationDate = date('Y-m-d');
            $endDateString = strtotime($endDate);
            $creationDateString = strtotime(date('Y-m-d', time()));

            $this->model->setForEveryone(0);

            $codesAde = array();
            foreach ($codes as $code) {
                if ($code != 'all' && $code != 0) {
                    if (is_null($codeAde->getByCode($code)->getId())) {
                        $this->view->errorMessageInvalidForm();
                    } else {
                        $codesAde[] = $codeAde->getByCode($code);
                    }
                } else if ($code == 'all') {
                    $this->model->setForEveryone(1);
                }
            }

            if (is_string($content) && strlen($content) >= 4 && strlen($content) <= 280 && $this->isRealDate($endDate) && $creationDateString < $endDateString) {
                $current_user = wp_get_current_user();

                // Set the alert
                $this->model->setAuthor($current_user->ID);
                $this->model->setContent($content);
                $this->model->setCreationDate($creationDate);
                $this->model->setExpirationDate($endDate);
                $this->model->setCodes($codesAde);

                // Insert
                if ($id = $this->model->insert()) {
                    $this->view->displayAddValidate();
                } else {
                    $this->view->errorMessageCantAdd();
                }
            } else {
                $this->view->errorMessageInvalidForm();
            }
        }

        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        return $this->view->creationForm($years, $groups, $halfGroups);
    }

	/**
	 * Handles the modification of an alert by processing request parameters, updating alert data, or deleting it.
	 *
	 * This method performs the following actions:
	 * - Validates the `id` parameter to ensure it is numeric and corresponds to an existing alert.
	 * - Checks user permissions to determine if the current user has the authority to modify the alert.
	 * - Processes the form submission to update alert details such as content, expiration date, and associated codes.
	 * - Deletes the alert if the delete action is submitted.
	 * - Retrieves and prepares additional data needed for rendering the modification form.
	 *
	 * @return string Depending on the process, this method can return:
	 * - A view with a "no alert" message if the `id` parameter is invalid or the alert does not exist.
	 * - A view with an "alert not allowed" message if the user lacks permission.
	 * - A success or error message upon form submission.
	 * - The modification form view, including alert details and additional metadata.
	 */
    public function modify(): string {
        $id = $_GET['id'];

        if (!is_numeric($id) || !$this->model->get($id)) {
            return $this->view->noAlert();
        }
        $current_user = wp_get_current_user();
        $alert = $this->model->get($id);
        if (!in_array('administrator', $current_user->roles) && !in_array('secretaire', $current_user->roles) && $alert->getAuthor()->getId() != $current_user->ID) {
            return $this->view->alertNotAllowed();
        }

        if ($alert->getAdminId()) {
            return $this->view->alertNotAllowed();
        }

        $codeAde = new CodeAde();

        $submit = filter_input(INPUT_POST, 'submit');
        if (isset($submit)) {
            // Get value
            $content = filter_input(INPUT_POST, 'content');
            $expirationDate = filter_input(INPUT_POST, 'expirationDate');
            $codes = $_POST['selectAlert'];

            $alert->setForEveryone(0);

            $codesAde = array();
            foreach ($codes as $code) {
                if ($code != 'all' && $code != 0) {
                    if (is_null($codeAde->getByCode($code)->getId())) {
                        $this->view->errorMessageInvalidForm();
                    } else {
                        $codesAde[] = $codeAde->getByCode($code);
                    }
                } else if ($code == 'all') {
                    $alert->setForEveryone(1);
                }
            }

            // Set the alert
            $alert->setContent($content);
            $alert->setExpirationDate($expirationDate);
            $alert->setCodes($codesAde);

            if ($alert->update()) {
                $this->view->displayModifyValidate();
            } else {
                $this->view->errorMessageCantAdd();
            }
        }

        $delete = filter_input(INPUT_POST, 'delete');
        if (isset($delete)) {
            $alert->delete();
            $this->view->displayModifyValidate();
        }

        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        return $this->view->modifyForm($alert, $years, $groups, $halfGroups);
    }


	/**
	 * Handles the display and management of alerts, including pagination, filtering, and authorization checks.
	 * Retrieves alerts based on the user's role and constructs a paginated view with options for modifying or deleting selected alerts.
	 *
	 * @return string The built HTML string containing the alerts table, controls, and pagination.
	 */
	public function displayAll(): string {
        $numberAllEntity = $this->model->countAll();
        $url = $this->getPartOfUrl();
        $number = filter_input(INPUT_GET, 'number');
        $pageNumber = 1;
        if (sizeof($url) >= 2 && is_numeric($url[1])) {
            $pageNumber = $url[1];
        }
        if (isset($number) && !is_numeric($number) || empty($number)) {
            $number = 25;
        }
        $begin = ($pageNumber - 1) * $number;
        $maxPage = ceil($numberAllEntity / $number);
        if ($maxPage <= $pageNumber && $maxPage >= 1) {
            $pageNumber = $maxPage;
        }
        $current_user = wp_get_current_user();
        if (in_array('administrator', $current_user->roles) || in_array('secretaire', $current_user->roles)) {
            $alertList = $this->model->getList($begin, $number);
        } else {
            $alertList = $this->model->getAuthorListAlert($current_user->ID, $begin, $number);
        }
        $name = 'Alert';
        $header = ['Contenu', 'Date de création', 'Date d\'expiration', 'Auteur', 'Modifier'];
        $dataList = [];
        $row = $begin;

        foreach ($alertList as $alert) {
            $dataList[] = [$row+1, $this->view->buildCheckbox($name, $alert->getId()), $alert->getContent(), $alert->getCreationDate(), $alert->getExpirationDate(), $alert->getAuthor()->getLogin(), $this->view->buildLinkForModify(esc_url(get_permalink(get_page_by_title_V2('Modifier une alerte'))) . '?id=' . $alert->getId())];
            ++$row;
        }

        $submit = filter_input(INPUT_POST, 'delete');
        if (isset($submit)) {
            if (isset($_REQUEST['checkboxStatusAlert'])) {
                $checked_values = $_REQUEST['checkboxStatusAlert'];
                foreach ($checked_values as $id) {
                    $entity = $this->model->get($id);
                    $entity->delete();
                }
                $this->view->refreshPage();
            }
        }
        if ($pageNumber == 1) {
            $returnString = $this->view->contextDisplayAll();
        }
        return $returnString . $this->view->displayAll($name, 'Alertes', $header, $dataList) . $this->view->pageNumber($maxPage, $pageNumber, esc_url(get_permalink(get_page_by_title_V2('Gestion des alertes'))), $number);
    }


	/**
	 * Displays the main alert content for the current user by aggregating user-specific and general alerts,
	 * performing expiration checks, and presenting the alerts through the view layer.
	 *
	 * This method retrieves alerts specifically assigned to the current user and combines them with alerts available
	 * to everyone. It checks each alert for expiration and prepares the content before delegating the display to the view.
	 *
	 * @return void
	 */
    public function alertMain(): void {
        // Get codes from current user
        $current_user = wp_get_current_user();
        $alertsUser = $this->model->getForUser($current_user->ID);
        foreach ($this->model->getForEveryone() as $alert) {
            $alertsUser[] = $alert;
        }

        $contentList = array();
        foreach ($alertsUser as $alert) {
            $endDate = date('Y-m-d', strtotime($alert->getExpirationDate()));
            $this->endDateCheckAlert($alert->getId(), $endDate); // Check alert

            $content       = $alert->getContent() . '&emsp;&emsp;&emsp;&emsp;';
            $contentList[] = $content;
        }

        if (isset($content)) {
            $this->view->displayAlertMain($contentList);
        }
    }

	/**
	 * Synchronizes alerts between the current system and the admin website.
	 * Compares alerts retrieved from the admin website with the local alerts and updates them to reflect changes or deletes them if they no longer exist on the admin website.
	 * Inserts new alerts from
	 * */
	public function registerNewAlert(): void {
        $alertList = $this->model->getFromAdminWebsite();
        $myAlertList = $this->model->getAdminWebsiteAlert();
        foreach ($myAlertList as $alert) {
            if ($adminInfo = $this->model->getAlertFromAdminSite($alert->getId())) {
                if ($alert->getContent() != $adminInfo->getContent()) {
                    $alert->setContent($adminInfo->getContent());
                }
                if ($alert->getExpirationDate() != $adminInfo->getExpirationDate()) {
                    $alert->setExpirationDate($adminInfo->getExpirationDate());
                }
                $alert->setCodes([]);
                $alert->setForEveryone(1);
                $alert->update();
            } else {
                $alert->delete();
            }
        }
        foreach ($alertList as $alert) {
            $exist = 0;
            foreach ($myAlertList as $myAlert) {
                if ($alert->getId() == $myAlert->getAdminId()) {
                    ++$exist;
                }
            }
            if ($exist == 0) {
                $alert->setAdminId($alert->getId());
                $alert->setCodes([]);
                $alert->insert();
            }
        }
    }

	/**
	 * Checks if the specified alert's end date has passed and deletes the alert if the condition is met.
	 *
	 * @param int|string $id The unique identifier of the alert to check.
	 * @param string $endDate The expiration date of the alert in
	 */
    public function endDateCheckAlert(int|string $id, int $endDate): void {
        if ($endDate <= date("Y-m-d")) {
            $alert = $this->model->get($id);
            $alert->delete();
        }
    }
}
