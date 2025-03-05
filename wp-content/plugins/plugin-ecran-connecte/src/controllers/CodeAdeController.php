<?php

namespace Controllers;

use Models\CodeAde;
use Models\Department;
use Views\CodeAdeView;

/**
 * Class CodeAdeController
 *
 * Manage codes ade (create, update, delete, display)
 *
 * @package Controllers
 */
class CodeAdeController extends Controller
{

    /**
     * Model of CodeAdeController
     * @var CodeAde
     */
    private CodeAde $model;

    /**
     * View of CodeAdeController
     * @var CodeAdeView
     */
    private CodeAdeView $view;

    /**
     * Constructor of CodeAdeController.
     */
    public function __construct() {
        $this->model = new CodeAde();
        $this->view = new CodeAdeView();
    }

	/**
	 * Handles the creation and insertion of a new item into the system.
	 *
	 * Validates the input fields 'title', 'code', and 'type', ensures they meet
	 * the required criteria, and verifies the absence of duplicate codes before
	 * inserting the item into the database. Provides appropriate feedback to the
	 * user based on success or failure of the operation.
	 *
	 * @return string Returns the form view for creating an item, or a refreshed view on successful creation.
	 */
    public function insert(): string {
        $action = filter_input(INPUT_POST, 'submit');

        $currentUser = wp_get_current_user();

        $deptModel = new Department();
        $isAdmin = in_array('administrator', $currentUser->roles);
        $currentDept = $isAdmin ? null : $deptModel->getUserInDept($currentUser->ID)->getId();
        $departments = $deptModel->getAll();

        if (isset($action)) {

            $validType = ['year', 'group', 'halfGroup', 'room'];

            $title = filter_input(INPUT_POST, 'title');
            $code = filter_input(INPUT_POST, 'code');
            $type = filter_input(INPUT_POST, 'type');
            $deptId = filter_input(INPUT_POST, 'dept');

            if (is_string($title) && strlen($title) > 4 && strlen($title) < 30 &&
                is_numeric($code) && is_string($code) && strlen($code) < 20 &&
                in_array($type, $validType)) {

                $this->model->setTitle($title);
                $this->model->setCode($code);
                $this->model->setType($type);
                $this->model->setDeptId($deptId);

                if (!$this->checkDuplicateCode($this->model) && $this->model->insert()) {
                    $this->view->successCreation();
                    $this->addFile($code);
                    $this->view->refreshPage();
                } else {
                    $this->view->displayErrorDoubleCode();
                }
            } else {
                $this->view->errorCreation();
            }
        }
        return $this->view->createForm($departments, $isAdmin, $currentDept);
    }


	/**
	 * Handles the modification of a code entry. Validates input data such as title, code, and type
	 * against specific conditions. If the input is valid, updates the code entry in the model and
	 * performs additional checks like preventing duplicate codes. Handles both success and error
	 * scenarios during the modification process.
	 *
	 * @return string Returns a view response depending on the outcome of the modification process:
	 *               - Error view if the specified ID does not exist.
	 *               - Success or error views based on the input validation and update results.
	 */
    public function modify(): string {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id || !$this->model->get($id)) {
            return $this->view->errorNobody();
        }

        $codeAde = $this->model->get($id);
        $submit = filter_input(INPUT_POST, 'submit');

        if (isset($submit)) {
            $validType = ['year', 'group', 'halfGroup'];

            $title = filter_input(INPUT_POST, 'title');
            $code = filter_input(INPUT_POST, 'code');
            $type = filter_input(INPUT_POST, 'type');

            if (is_string($title) && strlen($title) > 4 && strlen($title) < 30 &&
                in_array($type, $validType)) {

                $codeAde->setTitle($title);
                $codeAde->setCode($code);
                $codeAde->setType($type);

                if (!$this->checkDuplicateCode($codeAde) && $codeAde->update()) {
                    if ($codeAde->getCode() != $code) {
                        $this->addFile($code);
                    }
                    $this->view->successModification();
                } else {
                    $this->view->displayErrorDoubleCode();
                }
            } else {
                $this->view->errorModification();
            }
        }
        return $this->view->displayModifyCode($codeAde->getTitle(), $codeAde->getType(), $codeAde->getCode());
    }

	/**
	 * Retrieves all stored codes categorized by their types ('year', 'group', 'halfGroup')
	 * from the model and passes them to the view for display.
	 *
	 * @return string Returns the rendered view displaying all codes grouped by their respective types.
	 */
    public function displayAllCodes() : string {
        $years = $this->model->getAllFromType('year');
        $groups = $this->model->getAllFromType('group');
        $halfGroups = $this->model->getAllFromType('halfGroup');
        $room = $this->model->getAllFromType('room');

        return $this->view->displayAllCode($years, $groups, $halfGroups, $room);
    }

	/**
	 * Handles the deletion of multiple code entries based on user selection. Processes the request
	 * to identify selected codes and deletes them from the model. Refreshes the view after handling
	 * each deletion to ensure the user interface reflects the changes.
	 *
	 * @return void This method does not return any value. It performs the deletion action and updates
	 *              the view accordingly.
	 */
    public function deleteCodes(): void {
        $actionDelete = filter_input(INPUT_POST, 'delete');
        if (isset($actionDelete)) {
            if (isset($_REQUEST['checkboxStatusCode'])) {
                $checked_values = $_REQUEST['checkboxStatusCode'];
                foreach ($checked_values as $id) {
                    $this->model = $this->model->get($id);
                    $this->model->delete();
                    $this->view->refreshPage();
                }
            }
        }
    }

	/**
	 * Checks for duplicate code entries by comparing the provided code entry
	 * against existing entries within the model. It ensures that the new or
	 * modified code does not conflict with other records, excluding itself
	 * from the comparison.
	 *
	 * @param CodeAde $newCodeAde The code entry to be checked for duplication.
	 *
	 * @return bool Returns true if a duplicate code entry exists, otherwise false.
	 */
    private function checkDuplicateCode(CodeAde $newCodeAde): bool {
        $codesAde = $this->model->checkCode($newCodeAde->getTitle(), $newCodeAde->getCode());

        $count = 0;
        foreach ($codesAde as $codeAde) {
            if ($newCodeAde->getId() === $codeAde->getId()) {
                unset($codesAde[$count]);
            }
            ++$count;
        }

        if (sizeof($codesAde) > 0) {
            return true;
        }

        return false;
    }
}