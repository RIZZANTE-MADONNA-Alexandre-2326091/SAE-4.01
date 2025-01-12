<?php

namespace Controllers;

use Models\Department;
use Views\DepartmentView;

/**
 * Class DepartmentController
 *
 *  Manage departments (create, update, delete, display)
 *
 * @package Controllers
 */
class DepartmentController extends Controller
{
	/**
	 * @var Department
	 */
	private Department $model;

	/**
	 * @var DepartmentView
	 */
	private DepartmentView $view;

	/**
	 * DepartmentController constructor.
	 */
	public function __construct() {
		$this->model = new Department();
		$this->view = new DepartmentView();
	}

	/**
	 * Handle the insertion of a department entity after validating input data.
	 *
	 * Processes the form submission to add a new department. It checks
	 * the input name for validity and ensures no duplicate names exist
	 * before the entity is inserted into the database.
	 *
	 * @return string The form view for department creation.
	 */
	public function insert(): string{
		$action = filter_input(INPUT_POST, 'submit');

		if(isset($action)){
			$name = filter_input(INPUT_POST, 'deptName');
			if(is_string($name) && strlen($name) >= 10){

				$this->model->setName($name);

				if(!$this->checkDuplicateDept($this->model)){
					$this->model->insert();
					$this->view->displayCreationSuccess();

				}else{
					$this->view->displayErrorDoubleName();
				}
			}else{
				$this->view->displayCreationError();
			}
		}

		return $this->view->displayFormDepartment();
	}

	/**
	 * Deletes one or more departments based on user input.
	 *
	 * @return void
	 */
	public function delete(): void{
		$action = filter_input(INPUT_GET, 'deleteDepartment');

		if(isset($action)) {
			if ( isset( $_REQUEST['checkboxStatusDept'] ) ) {
				$checked_values = $_REQUEST['checkboxStatusDept'];
				foreach ( $checked_values as $id ) {
					$this->model = $this->model->get($id);
					$this->model->delete();
				}
			}
		}

	}

	/**
	 * Modify an existing department by updating its name.
	 * Validates the input data, checks for duplicates, and performs an update if valid.
	 *
	 * @return string Returns the rendered view for the modification form or related view messages.
	 */
	public function modify(): string{

		$id = $_GET['id'];

		if(!is_numeric($id) || !$this->model->get($id)){
			return $this->view->noDepartment();
		}

		$action = filter_input(INPUT_POST, 'modifDept');

		if(isset($action)){
			$newName = filter_input(INPUT_POST, 'deptName');
			if(is_string($newName) && strlen($newName) > 10){

				$this->model->setId($id);
				$this->model->setName($newName);

				if(!$this->checkDuplicateDept($this->model)){
					$this->model->update();
					$this->view->displayModificationSucces();

				}else{
					$this->view->displayErrorDoubleName();
				}
			}else{
				$this->view->displayModificationError();
			}
		}

		return $this->view->modifyForm();
	}

	/**
	 * Retrieve and display all departments.
	 *
	 * @return string
	 */
	public function displayAll(): string{
		$departments = $this->model->getAll();

		return $this->view->displayAllDept($departments);
	}

	/**
	 * Checks if a department with the same name already exists.
	 *
	 * @param Department $department The department object containing the name to check for duplication.
	 *
	 * @return bool Returns true if a duplicate department name is found, otherwise false.
	 */
	public function checkDuplicateDept(Department $department): bool{
		$departments = $this->model->getDepartmentName($department->getName());

		foreach($departments as $dept){
			if( $department->getName() === $dept->getName()){
				return true;
			}
		}
		return false;
	}
}